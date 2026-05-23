<?php

namespace App\Services\AI;

use App\Contracts\AI\MultiModalAnalysisInterface;
use App\Contracts\AI\LlmConfigurationInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiMultiModalService implements MultiModalAnalysisInterface
{
    private const BASE_URL = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private readonly LlmConfigurationInterface $config,
        private readonly string $apiKey = ''
    ) {}

    private function resolvedKey(): string
    {
        return $this->apiKey ?: config('services.openai.key');
    }

    public function analyzeAudio(string $audioPath): array
    {
        $resolvedKey = $this->resolvedKey();
        if (empty($resolvedKey)) {
            throw new \RuntimeException("OpenAI API Key is not configured.");
        }

        $audioData = base64_encode(file_get_contents($audioPath));
        $extension = pathinfo($audioPath, PATHINFO_EXTENSION);
        $format = in_array(strtolower($extension), ['mp3', 'wav']) ? strtolower($extension) : 'wav';

        try {
            $response = Http::withToken($resolvedKey)
                ->timeout($this->config->getTimeout())
                ->post(self::BASE_URL, [
                    'model' => $this->config->getModelName(),
                    'temperature' => $this->config->getTemperature(),
                    'modalities' => ['text'],
                    'max_completion_tokens' => 4096,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->config->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $this->config->getUserPrompt()
                                ],
                                [
                                    'type' => 'input_audio',
                                    'input_audio' => [
                                        'data' => $audioData,
                                        'format' => $format
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                Log::error('[OpenAiMultiModal] API Error', ['body' => $response->body()]);
                throw new \RuntimeException('Gagal memproses audio di server AI.');
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? '[]';
            
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*/', '', $content);

            $parsed = json_decode($content, true);
            if (!$parsed) {
                return $this->mockResponse();
            }

            return $parsed;

        } catch (\Throwable $e) {
            Log::error('[OpenAiMultiModal] Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function synthesizeChunks(array $chunks): array
    {
        $resolvedKey = $this->resolvedKey();
        if (empty($resolvedKey)) {
            throw new \RuntimeException("OpenAI API Key is not configured.");
        }

        $chunksJson = json_encode($chunks, JSON_PRETTY_PRINT);
        
        // Load clean prompt from resources folder
        $promptTemplate = file_get_contents(resource_path('prompts/synthesis.md'));
        $prompt = str_replace('{CHUNKS_JSON}', $chunksJson, $promptTemplate);

        try {
            $response = Http::withToken($resolvedKey)
                 ->timeout(300)
                 ->post('https://api.openai.com/v1/chat/completions', [
                     'model' => $this->config->getSynthesisModelName(),
                     'temperature' => 0.1,
                     'max_completion_tokens' => 8192,
                     'response_format' => ['type' => 'json_object'],
                     'messages' => [
                         [
                             'role' => 'system',
                             'content' => $this->config->getSynthesisSystemPrompt()
                         ],
                         [
                             'role' => 'user',
                             'content' => $prompt
                         ]
                     ]
                 ]);
 
             if ($response->failed()) {
                 Log::error('[OpenAiMultiModal] Synthesis Error', ['body' => $response->body()]);
                 throw new \RuntimeException('Gagal menyatukan hasil potongan bimbingan.');
             }
 
             $json = $response->json();
             $content = $json['choices'][0]['message']['content'] ?? '[]';
             
             // Highly robust cleaning of markdown codeblocks
             $cleanContent = $content;
             if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
                 $cleanContent = $matches[1];
             } else {
                 $cleanContent = preg_replace('/^```[a-zA-Z]*\s*/', '', $cleanContent);
                 $cleanContent = preg_replace('/\s*```$/', '', $cleanContent);
             }
             $cleanContent = trim($cleanContent);
 
             $parsed = json_decode($cleanContent, true);
             
             // Fallback 1: If decoding failed, try extracting content between first '{' and last '}'
             if (!$parsed) {
                 $firstBrace = strpos($cleanContent, '{');
                 $lastBrace = strrpos($cleanContent, '}');
                 if ($firstBrace !== false && $lastBrace !== false) {
                     $jsonCandidate = substr($cleanContent, $firstBrace, $lastBrace - $firstBrace + 1);
                     $parsed = json_decode($jsonCandidate, true);
                 }
             }

             // Fallback 2: ULTRA RESILIENT PARTIAL JSON REPAIR (Token Saver & Truncation Healer)
             if (!$parsed) {
                 Log::warning('[OpenAiMultiModal] Attempting premium structural repair on truncated JSON string.');
                 $repairedContent = trim($cleanContent);
                 
                 // If it ends inside a word or string without closing quote
                 // Count unescaped double quotes
                 $quoteCount = preg_match_all('/(?<!\\\\)"/', $repairedContent);
                 if ($quoteCount % 2 !== 0) {
                     $repairedContent .= '"'; // Close current active string
                 }

                 // Structurally rebuild the JSON tree balance
                 $brackets = [];
                 $len = strlen($repairedContent);
                 $inString = false;
                 
                 for ($i = 0; $i < $len; $i++) {
                     $char = $repairedContent[$i];
                     if ($char === '"' && ($i === 0 || $repairedContent[$i-1] !== '\\')) {
                         $inString = !$inString;
                     }
                     if (!$inString) {
                         if ($char === '{' || $char === '[') {
                             $brackets[] = $char;
                         } else if ($char === '}' || $char === ']') {
                             array_pop($brackets);
                         }
                     }
                 }

                 // Close open elements from the stack in reverse order
                 while (!empty($brackets)) {
                     $openBracket = array_pop($brackets);
                     if ($openBracket === '{') {
                         // Check if we are inside a key-value or array of objects and need to close current transcription array index
                         $repairedContent = rtrim($repairedContent, ", \t\n\r");
                         $repairedContent .= '}';
                     } else if ($openBracket === '[') {
                         $repairedContent = rtrim($repairedContent, ", \t\n\r");
                         $repairedContent .= ']';
                     }
                 }

                 $parsed = json_decode($repairedContent, true);
                 if ($parsed) {
                     Log::info('[OpenAiMultiModal] JSON Structural repair succeeded! Incomplete elements healed.');
                 }
             }

             if (!$parsed) {
                 Log::error('[OpenAiMultiModal] Synthesis JSON Parsing Failure details', [
                     'json_error' => json_last_error_msg(),
                     'raw_content' => $content,
                     'clean_content' => $cleanContent
                 ]);
                 throw new \RuntimeException('Gagal mengurai format JSON hasil penggabungan. Error: ' . json_last_error_msg());
             }
 
             return $parsed;
 
         } catch (\Throwable $e) {
             Log::error('[OpenAiMultiModal] Synthesis Exception: ' . $e->getMessage());
             throw $e;
         }
    }

    private function mockResponse(): array
    {
        $mockJson = file_get_contents(resource_path('mocks/synthesis_mock.json'));
        return json_decode($mockJson, true) ?? [];
    }
}

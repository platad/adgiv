<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenAiService
 *
 * Responsibility: Handle all analysis tasks using OpenAI's gpt-4o-mini.
 * Optimized for cost and stability.
 */
class OpenAiService
{
    private const BASE_URL = 'https://api.openai.com/v1';
    private const DEFAULT_MODEL = 'gpt-4o-mini';

    public function __construct(
        private readonly string $apiKey = '',
        private readonly string $model = self::DEFAULT_MODEL,
    ) {
    }

    /**
     * Resolve API key from config.
     */
    private function resolvedKey(): string
    {
        return $this->apiKey ?: config('services.openai.key');
    }

    /**
     * Resolve model - gpt-4o-mini is optimized for cost and speed.
     */
    private function resolvedModel(): string
    {
        return $this->model;
    }

    public function complete(string $systemPrompt, string $userMessage, array $extraContext = []): array
    {
        $resolvedKey = $this->resolvedKey();

        if (empty($resolvedKey)) {
            Log::warning('[OpenAiService] No API key configured – returning mock response.');
            return $this->mockResponse($userMessage);
        }

        // Token optimization: ensure instructions are concise
        $systemPrompt .= "\n\nRESPONSE RULES: Respond ONLY in valid JSON format as requested. Be concise to save tokens.";

        return $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userMessage],
        ]);
    }

    /**
     * Send messages to OpenAI with JSON Mode enabled.
     */
    public function chat(array $messages): array
    {
        $resolvedKey = $this->resolvedKey();

        if (empty($resolvedKey)) {
            return ['content' => 'API Key OpenAI belum dikonfigurasi.', 'usage' => []];
        }

        try {
            $response = Http::withToken($resolvedKey)
                ->baseUrl(self::BASE_URL)
                ->timeout(60)
                ->post('/chat/completions', [
                    'model' => $this->resolvedModel(),
                    'temperature' => 0.1, 
                    'messages' => $messages,
                    'response_format' => ['type' => 'json_object'], 
                ]);

            if ($response->failed()) {
                Log::error('[OpenAiService] API error.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException('OpenAI API returned HTTP ' . $response->status());
            }

            $json = $response->json();

            return [
                'content' => $json['choices'][0]['message']['content'] ?? '',
                'usage' => $json['usage'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('[OpenAiService] Request failed.', ['error' => $e->getMessage()]);
            return ['content' => '{"error": "Sistem sedang sibuk, silakan coba lagi."}', 'usage' => []];
        }
    }

    private function mockResponse(string $userMessage): array
    {
        return ['content' => '{"error": "API Key tidak diset"}', 'usage' => []];
    }
}

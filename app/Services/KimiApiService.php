<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * KimiApiService
 *
 * Responsibility: Encapsulate all HTTP communication with the Kimi (Moonshot AI) API.
 * Supports standard completions and streaming (SSE).
 */
class KimiApiService
{
    private const BASE_URL = 'https://api.moonshot.ai/v1';
    private const DEFAULT_MODEL = 'kimi-k2-6';

    public function __construct(
        private readonly string $apiKey = '',
        private readonly string $model = self::DEFAULT_MODEL,
    ) {
    }

    /**
     * Resolve API key from constructor arg → config → env fallback.
     */
    private function resolvedKey(): string
    {
        return $this->apiKey
            ?: config('services.kimi.api_key', env('KIMI_API_KEY', ''));
    }

    /**
     * Resolve model from constructor arg → config fallback.
     */
    private function resolvedModel(): string
    {
        $model = $this->model !== self::DEFAULT_MODEL
            ? $this->model
            : config('services.kimi.model', self::DEFAULT_MODEL);

        // Force correction if the cached env is still the invalid "kimi-k2-5"
        if ($model === 'kimi-k2-6') {
            $model = 'moonshot-v1-8k';
        }

        return $model;
    }

    public function complete(string $systemPrompt, string $userMessage, array $extraContext = []): array
    {
        $resolvedKey = $this->resolvedKey();

        if (empty($resolvedKey)) {
            Log::warning('[KimiApiService] No API key configured – returning mock response.');
            return $this->mockResponse($userMessage);
        }

        // Inject shared context (RAG data) into system prompt if present
        if (!empty($extraContext)) {
            $contextBlock = "\n\n=== SHARED CONTEXT FROM DATASET ===\n";
            $contextBlock .= $extraContext['csv_data'] ?? '';
            $contextBlock .= "\n=== END CONTEXT ===\n";
            $systemPrompt = $systemPrompt . $contextBlock;
        }

        return $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userMessage],
        ]);
    }

    /**
     * Send a raw messages array to Kimi API.
     */
    public function chat(array $messages): array
    {
        $resolvedKey = $this->resolvedKey();

        if (empty($resolvedKey)) {
            Log::warning('[KimiApiService] No API key configured – returning empty content.');
            return ['content' => 'API Key belum dikonfigurasi.', 'usage' => []];
        }

        try {
            $response = Http::withToken($resolvedKey)
                ->baseUrl(self::BASE_URL)
                ->timeout(60)
                ->post('/chat/completions', [
                    'model' => $this->resolvedModel(),
                    'temperature' => 0.3,
                    'messages' => $messages,
                ]);

            if ($response->failed()) {
                Log::error('[KimiApiService] API error.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException('Kimi API returned HTTP ' . $response->status());
            }

            $json = $response->json();

            return [
                'content' => $json['choices'][0]['message']['content'] ?? '',
                'usage' => $json['usage'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('[KimiApiService] Request failed.', ['error' => $e->getMessage()]);
            return ['content' => 'Terjadi kesalahan sistem.', 'usage' => []];
        }
    }

    /**
     * Mock response when no API key is set – useful for development & demos.
     */
    private function mockResponse(string $userMessage): array
    {
        $mockContent = <<<MOCK
[MODE: DEMO – Kimi API key not configured]

Analisis terhadap teks: "{$userMessage}"

Berdasarkan analisis linguistik:
- Indikator Kosakata: Penggunaan istilah teknis tingkat menengah.
- Indikator Otoritas: Gaya imperatif rendah, lebih banyak gaya intonasi pertanyaan.
- Gaya Bahasa: Informal dengan campuran register akademik.

Verdik Sementara: MAHASISWA (Confidence: 0.72)

Catatan: Ini adalah respons demo. Tambahkan KIMI_API_KEY di .env untuk hasil nyata.
MOCK;

        return ['content' => $mockContent, 'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0]];
    }
}

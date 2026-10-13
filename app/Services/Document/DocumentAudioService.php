<?php

namespace App\Services\Document;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentAudioService
{
    private const BASE_URL = 'https://api.openai.com/v1/audio/speech';
    private const MODEL = 'tts-1';

    public function __construct(
        private readonly string $apiKey = ''
    ) {}

    private function resolvedKey(): string
    {
        return $this->apiKey ?: (config('services.openai.key') ?? env('OPENAI_API_KEY', ''));
    }

    /**
     * Menghasilkan file audio MP3 dari narasi teks dan menyimpannya di disk public.
     *
     * @param string $audioScript
     * @param string $locale
     * @return string URL publik file audio atau string kosong jika gagal
     */
    public function generateBriefingAudio(string $audioScript, string $locale = 'id'): string
    {
        $trimmedScript = trim($audioScript);
        if (empty($trimmedScript)) {
            return '';
        }

        $apiKey = $this->resolvedKey();
        if (empty($apiKey)) {
            Log::warning('[DocumentAudioService] OpenAI API Key tidak disetel. Melewati sintesis audio TTS.');
            return '';
        }

        // Caching berbasis hash teks & locale
        $hash = md5($trimmedScript . '_' . $locale);
        $directory = 'document_audio';
        $relativeFilePath = "{$directory}/{$hash}.mp3";

        // Pastikan direktori ada pada storage public
        Storage::disk('public')->makeDirectory($directory);

        // Jika file sudah pernah dibuat, gunakan kembali cache
        if (Storage::disk('public')->exists($relativeFilePath)) {
            Log::info('[DocumentAudioService] Menggunakan audio cache yang sudah ada', ['path' => $relativeFilePath]);
            return Storage::disk('public')->url($relativeFilePath);
        }

        // Voice profile: alloy untuk bahasa Indonesia & umum, nova untuk Inggris
        $voice = 'alloy';
        if ($locale === 'en') {
            $voice = 'nova';
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(45)
                ->post(self::BASE_URL, [
                    'model'           => self::MODEL,
                    'input'           => $trimmedScript,
                    'voice'           => $voice,
                    'response_format' => 'mp3',
                    'speed'           => 1.0,
                ]);

            if ($response->failed()) {
                Log::error('[DocumentAudioService] Gagal memanggil OpenAI TTS API', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return '';
            }

            Storage::disk('public')->put($relativeFilePath, $response->body());
            Log::info('[DocumentAudioService] Audio briefing berhasil dibuat & disimpan', ['path' => $relativeFilePath]);

            return Storage::disk('public')->url($relativeFilePath);

        } catch (\Throwable $e) {
            Log::error("[DocumentAudioService] Exception saat membuat TTS: {$e->getMessage()}");
            return '';
        }
    }
}

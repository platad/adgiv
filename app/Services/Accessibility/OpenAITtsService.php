<?php

namespace App\Services\Accessibility;

use App\Contracts\Accessibility\TtsInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OpenAITtsService implements TtsInterface
{
    private const BASE_URL = 'https://api.openai.com/v1/audio/speech';
    
    public function __construct(
        private readonly string $apiKey = ''
    ) {}

    private function resolvedKey(): string
    {
        return $this->apiKey ?: config('services.openai.key');
    }

    public function generateAudioUrl(string $text, string $locale = 'id'): string
    {
        $resolvedKey = $this->resolvedKey();
        if (empty($resolvedKey)) {
            Log::warning('[TTS] OpenAI API Key is not configured. Falling back to empty string.');
            return '';
        }

        // Pastikan direktori ada
        Storage::disk('public')->makeDirectory('accessibility_audio');

        // Buat nama file unik berdasarkan hash teks dan locale (Caching)
        $hash = md5($text . $locale);
        $fileName = "accessibility_audio/{$hash}.mp3";

        // Jika file sudah ada di cache, langsung kembalikan URL-nya
        if (Storage::disk('public')->exists($fileName)) {
            return Storage::disk('public')->url($fileName);
        }

        // Pilih voice model berdasarkan locale (opsional, alloy cukup netral)
        $voice = 'alloy';
        if ($locale === 'en') $voice = 'nova';

        try {
            $response = Http::withToken($resolvedKey)
                ->timeout(30)
                ->post(self::BASE_URL, [
                    'model' => 'tts-1',
                    'input' => $text,
                    'voice' => $voice,
                    'response_format' => 'mp3',
                    'speed' => 1.0
                ]);

            if ($response->failed()) {
                Log::error('[TTS] OpenAI API Error', ['body' => $response->body()]);
                return '';
            }

            // Simpan audio byte stream ke storage public
            Storage::disk('public')->put($fileName, $response->body());

            return Storage::disk('public')->url($fileName);

        } catch (\Throwable $e) {
            Log::error('[TTS] Exception: ' . $e->getMessage());
            return '';
        }
    }
}
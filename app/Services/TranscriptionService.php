<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * TranscriptionService
 *
 * Responsibility: Convert audio input to text.
 * Currently provides a mock implementation. Replace `transcribeAudio()` with
 * a real Whisper API call when the key is available.
 */
class TranscriptionService
{
    /**
     * Transcribe an uploaded audio file to text.
     * 
     * @param  UploadedFile $audioFile  The recorded audio blob from the browser.
     * @return string                   Transcribed text.
     */
    public function transcribeAudio(UploadedFile $audioFile): string
    {
        Log::info('[TranscriptionService] Audio received for OpenAI Whisper.', [
            'filename' => $audioFile->getClientOriginalName(),
            'size'     => $audioFile->getSize(),
        ]);

        $apiKey = config('services.openai.key');
        
        if (!$apiKey) {
            return "[SISTEM: API Key OpenAI tidak ditemukan.]";
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.openai.com/v1/audio/transcriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($audioFile->getRealPath(), 'r'),
                        'filename' => $audioFile->getClientOriginalName(),
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'whisper-1',
                    ],
                    [
                        'name'     => 'language',
                        'contents' => 'id',
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['text'] ?? '';
        } catch (\Exception $e) {
            Log::error('[TranscriptionService] OpenAI Error: ' . $e->getMessage());
            return "[SISTEM: Gagal transkripsi via OpenAI. Error: " . $e->getMessage() . "]";
        }
    }

    /**
     * Accept text directly (from Web Speech API results sent by the browser).
     *
     * @param  string $text  Text recognised by the browser Speech API.
     * @return string        Sanitised transcription.
     */
    public function processWebSpeechText(string $text): string
    {
        return trim(strip_tags($text));
    }
}

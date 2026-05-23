<?php

namespace App\Services\AI;

use App\Contracts\AI\LlmConfigurationInterface;
use Illuminate\Support\Facades\File;

class BimaAnalysisConfiguration implements LlmConfigurationInterface
{
    public function getSystemPrompt(): string
    {
        $path = resource_path('prompts/advice_giving.md');
        if (File::exists($path)) {
            return File::get($path);
        }
        throw new \RuntimeException('Prompt file not found at: ' . $path);
    }

    public function getUserPrompt(): string
    {
        return "Tolong analisa percakapan dalam audio ini sesuai instruksi pada system prompt dan kembalikan murni dalam format JSON object.";
    }

    public function getModelName(): string
    {
        return 'gpt-4o-mini-audio-preview';
    }

    public function getTimeout(): int
    {
        return 300;
    }

    public function getTemperature(): float
    {
        return 0.2;
    }

    public function getSynthesisModelName(): string
    {
        return 'gpt-4o-mini';
    }

    public function getSynthesisSystemPrompt(): string
    {
        return "Anda adalah Agen Sintesis BIMA (C-CDA Synthesizer). Gabungkan analisis potongan bimbingan akademik secara sangat presisi, hapus tumpang-tindih (deduplicate) pada bagian bertumpukan secara semantik, hitung ulang durasi, dan pastikan format JSON valid.";
    }
}

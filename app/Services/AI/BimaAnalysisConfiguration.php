<?php

namespace App\Services\AI;

use App\Contracts\AI\LlmConfigurationInterface;
use Illuminate\Support\Facades\File;

class BimaAnalysisConfiguration implements LlmConfigurationInterface
{
    public function getSystemPrompt(string $locale = 'id'): string
    {
        $path = resource_path("prompts/{$locale}/advice_giving.md");
        if (!File::exists($path)) {
            $path = resource_path('prompts/id/advice_giving.md');
        }
        if (File::exists($path)) {
            return File::get($path);
        }
        throw new \RuntimeException('Prompt file not found at: ' . $path);
    }

    public function getUserPrompt(string $locale = 'id'): string
    {
        return match ($locale) {
            'en' => 'Please analyze the conversation in this audio according to the instructions and return strictly as a JSON object.',
            'zh' => '请根据指令分析此音频中的对话，并严格以JSON对象格式返回结果。',
            default => 'Tolong analisa percakapan dalam audio ini sesuai instruksi pada system prompt dan kembalikan murni dalam format JSON object.',
        };
    }

    public function getModelName(): string
    {
        return 'gpt-audio-1.5';
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

    public function getSynthesisSystemPrompt(string $locale = 'id'): string
    {
        $path = resource_path("prompts/{$locale}/synthesis.md");
        if (!File::exists($path)) {
            $path = resource_path('prompts/id/synthesis.md');
        }
        if (File::exists($path)) {
            return File::get($path);
        }
        throw new \RuntimeException('Synthesis prompt file not found for locale: ' . $locale);
    }
}

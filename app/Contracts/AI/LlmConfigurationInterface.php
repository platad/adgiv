<?php

namespace App\Contracts\AI;

interface LlmConfigurationInterface
{
    /**
     * Get the system prompt (instructions for the LLM).
     *
     * @param string $locale The language locale (e.g., 'id', 'en', 'zh').
     */
    public function getSystemPrompt(string $locale = 'id'): string;

    /**
     * Get the specific user prompt to attach along with the payload.
     *
     * @param string $locale The language locale (e.g., 'id', 'en', 'zh').
     */
    public function getUserPrompt(string $locale = 'id'): string;

    /**
     * Get the model identifier (e.g., gpt-4o-audio-preview, gemini-1.5-pro).
     */
    public function getModelName(): string;

    /**
     * Get the timeout in seconds for the API call.
     */
    public function getTimeout(): int;

    /**
     * Get the temperature setting for the LLM.
     */
    public function getTemperature(): float;

    /**
     * Get the model identifier for the synthesis/reduction phase.
     */
    public function getSynthesisModelName(): string;

    /**
     * Get the system prompt for the synthesis/reduction phase.
     *
     * @param string $locale The language locale (e.g., 'id', 'en', 'zh').
     */
    public function getSynthesisSystemPrompt(string $locale = 'id'): string;
}

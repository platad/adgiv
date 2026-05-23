<?php

namespace App\Contracts\AI;

interface LlmConfigurationInterface
{
    /**
     * Get the system prompt (instructions for the LLM).
     */
    public function getSystemPrompt(): string;

    /**
     * Get the specific user prompt to attach along with the payload.
     */
    public function getUserPrompt(): string;

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
     */
    public function getSynthesisSystemPrompt(): string;
}

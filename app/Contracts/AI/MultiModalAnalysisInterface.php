<?php

namespace App\Contracts\AI;

interface MultiModalAnalysisInterface
{
    /**
     * Analyze an audio file directly and return structured data.
     *
     * @param string $audioPath Absolute path to the audio file.
     * @param string $locale The language locale for prompt selection (e.g., 'id', 'en', 'zh').
     * @return array The structured analysis result (transcription + annotation).
     */
    public function analyzeAudio(string $audioPath, string $locale = 'id'): array;

    /**
     * Synthesize multiple analyzed audio chunks into a single unified report.
     *
     * @param array $chunks Array of raw chunk results.
     * @param string $locale The language locale for prompt selection (e.g., 'id', 'en', 'zh').
     * @return array The synthesized final report.
     */
    public function synthesizeChunks(array $chunks, string $locale = 'id'): array;
}

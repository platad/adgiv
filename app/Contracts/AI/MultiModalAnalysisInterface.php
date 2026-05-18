<?php

namespace App\Contracts\AI;

interface MultiModalAnalysisInterface
{
    /**
     * Analyze an audio file directly and return structured data.
     *
     * @param string $audioPath Absolute path to the audio file.
     * @return array The structured analysis result (transcription + annotation).
     */
    public function analyzeAudio(string $audioPath): array;
}

<?php

namespace App\Contracts\Accessibility;

interface TtsInterface
{
    /**
     * Generate TTS audio from text and return the public URL.
     *
     * @param string $text The text to synthesize.
     * @param string $locale The language code (default 'id').
     * @return string Public URL to the generated MP3 file.
     */
    public function generateAudioUrl(string $text, string $locale = 'id'): string;
}
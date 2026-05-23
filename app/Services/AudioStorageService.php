<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioStorageService
{
    /**
     * Store the uploaded audio file and return its absolute path.
     * Extremely lightweight and fully compatible with cPanel shared hosting 
     * by avoiding any shell/exec commands or external FFmpeg dependencies.
     */
    public function storeAudio(UploadedFile $file): string
    {
        $filename = 'audio_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('analyses', $filename, 'public');
        return Storage::disk('public')->path($path);
    }
}

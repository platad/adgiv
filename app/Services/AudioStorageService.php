<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioStorageService
{
    /**
     * Store the uploaded audio file and return its absolute path.
     * Ensures max limit handling implicitly via standard PHP/Laravel rules,
     * but we provide a clean interface here.
     */
    public function storeAudio(UploadedFile $file): string
    {
        // 100MB validation should be done in Request, here we just store.
        $filename = 'audio_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Store in local storage disk 'public/analyses'
        $path = $file->storeAs('analyses', $filename, 'public');

        return storage_path('app/public/' . $path);
    }
}

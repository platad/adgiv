<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'audio' => ['required', 'file', 'mimes:mp3,wav,m4a,webm,ogg,aac', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'audio.max' => 'Ukuran file audio maksimal adalah 50MB.',
            'audio.mimes' => 'Format file audio harus berformat MP3, WAV, M4A, WEBM, OGG, atau AAC.',
        ];
    }
}

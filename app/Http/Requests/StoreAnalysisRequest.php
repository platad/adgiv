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
            'audio' => ['required', 'file', 'mimes:mp3,wav', 'max:102400'], // 100MB limit
        ];
    }

    public function messages(): array
    {
        return [
            'audio.max' => 'Ukuran file audio maksimal adalah 100MB.',
            'audio.mimes' => 'Format file audio tidak didukung oleh AI saat ini. Harap gunakan format MP3 atau WAV.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalysisFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_accurate' => ['required', 'boolean'],
            'comments' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

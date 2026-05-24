<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages dynamically translated.
     */
    public function messages(): array
    {
        $locale = $this->route('locale') ?: \Illuminate\Support\Facades\App::getLocale();
        if ($locale === 'en') {
            return [
                'email.required'    => 'Email address is required.',
                'email.email'       => 'Invalid email address format.',
                'password.required' => 'Password is required.',
            ];
        } elseif ($locale === 'zh') {
            return [
                'email.required'    => '电子邮箱地址是必填项。',
                'email.email'       => '电子邮箱格式不正确。',
                'password.required' => '密码是必填项。',
            ];
        }

        return [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }
}

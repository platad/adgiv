<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
                'name.required'     => 'Full name is required.',
                'name.max'          => 'Full name may not exceed 255 characters.',
                'email.required'    => 'Email address is required.',
                'email.email'       => 'Invalid email address format.',
                'email.unique'      => 'This email address is already registered.',
                'password.required' => 'Password is required.',
                'password.min'      => 'Password must be at least 8 characters.',
                'password.confirmed'=> 'Password confirmation does not match.',
            ];
        } elseif ($locale === 'zh') {
            return [
                'name.required'     => '完整姓名是必填项。',
                'name.max'          => '完整姓名最多255个字符。',
                'email.required'    => '电子邮箱地址是必填项。',
                'email.email'       => '电子邮箱格式不正确。',
                'email.unique'      => '此电子邮箱地址已被注册。',
                'password.required' => '密码是必填项。',
                'password.min'      => '密码长度至少为8个字符。',
                'password.confirmed'=> '两次输入的密码不一致。',
            ];
        }

        return [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'name.max'          => 'Nama lengkap maksimal 255 karakter.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format alamat email tidak valid.',
            'email.unique'      => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed'=> 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}

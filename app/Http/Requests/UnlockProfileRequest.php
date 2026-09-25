<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnlockProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
            'date_of_birth' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Kata sandi wajib diisi untuk verifikasi identitas.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi sebagai faktor verifikasi tambahan.',
            'date_of_birth.date_format' => 'Format tanggal lahir harus YYYY-MM-DD (contoh: 1998-05-20).',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap atau nama panggilan wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi sebagai kunci verifikasi dekripsi profil.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Ketentuan Layanan dan Kebijakan Privasi.',
        ];
    }
}

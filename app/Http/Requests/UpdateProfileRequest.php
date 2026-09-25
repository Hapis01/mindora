<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:today'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date_format' => 'Format tanggal lahir harus YYYY-MM-DD.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\User::class) ?? false;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $role = $this->input('role');
            if (in_array($role, ['admin', 'super_admin'])) {
                $validator->errors()->add('role', 'Role tersebut tidak valid.');
            }
        });
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'            => ['required', 'string', 'min:8'],
            'role'                => ['required', Rule::in(['petugas', 'nasabah'])],
            'status'              => ['required', Rule::in(['pending', 'verified', 'rejected'])],
            'phone'               => ['nullable', 'string', 'max:255'],
            'address'             => ['nullable', 'string', 'max:65535'],
            'umur'                => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender'              => ['nullable', Rule::in(['L', 'P'])],
            'status_pekerjaan'    => ['nullable', Rule::in(['bekerja', 'tidak_bekerja', 'pelajar', 'mahasiswa', 'pensiun', 'lainnya'])],
            'universitas'         => ['nullable', 'string', 'max:255'],
            'fakultas'            => ['nullable', 'string', 'max:255'],
            'pendidikan_terakhir' => ['nullable', Rule::in(['sd', 'smp', 'sma', 's1', 's2', 's3'])],
        ];
    }
}

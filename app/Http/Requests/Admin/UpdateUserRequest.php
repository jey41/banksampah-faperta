<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $role = $this->input('role');
            // Allow super_admin only if the user being updated is ALREADY a super_admin
            $userBeingUpdated = $this->route('user');
            if ($role === 'super_admin' && $userBeingUpdated->role !== 'super_admin') {
                $validator->errors()->add('role', 'Tidak dapat mengubah role menjadi Super Admin.');
            }
            if ($role === 'admin') {
                $validator->errors()->add('role', 'Role admin sudah tidak berlaku.');
            }
        });
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['super_admin', 'petugas', 'nasabah'])],
            'status' => ['required', Rule::in(['pending', 'verified', 'rejected'])],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:65535'],
            'umur' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'status_pekerjaan' => ['nullable', Rule::in(['bekerja', 'tidak_bekerja', 'pelajar', 'mahasiswa', 'pensiun', 'lainnya'])],
            'universitas' => ['nullable', 'string', 'max:255'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'pendidikan_terakhir' => ['nullable', Rule::in(['sd', 'smp', 'sma', 's1', 's2', 's3'])],
        ];
    }
}

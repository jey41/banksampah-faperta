<?php

namespace App\Http\Requests\Nasabah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTargetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'nasabah';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'target_amount' => 'required|integer|min:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul target wajib diisi.',
            'title.max' => 'Judul target maksimal 255 karakter.',
            'target_amount.required' => 'Jumlah target wajib diisi.',
            'target_amount.integer' => 'Jumlah target harus berupa angka.',
            'target_amount.min' => 'Jumlah target minimal Rp 10.000.',
        ];
    }
}

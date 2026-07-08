<?php

namespace App\Http\Requests\Nasabah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StorePickupRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'nasabah';
    }

    public function rules(): array
    {
        return [
            'pickup_address' => 'required|string|max:1000',
            'pickup_phone' => 'required|string|max:20',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|string|in:08:00-10:00,10:00-12:00,13:00-15:00',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_distance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_address.required' => 'Alamat penjemputan wajib diisi.',
            'pickup_phone.required' => 'Nomor telepon wajib diisi.',
            'pickup_date.required' => 'Tanggal penjemputan wajib diisi.',
            'pickup_date.after_or_equal' => 'Tanggal penjemputan harus hari ini atau setelahnya.',
            'pickup_time.required' => 'Waktu penjemputan wajib dipilih.',
            'pickup_time.in' => 'Waktu penjemputan tidak valid.',
        ];
    }

    /**
     * Add custom validation for distance limit.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->estimated_distance !== null && $this->estimated_distance > 2.0) {
                $validator->errors()->add(
                    'latitude',
                    'Jarak lokasi Anda (' . $this->estimated_distance . ' km) melebihi batas maksimal penjemputan (2 km).'
                );
            }
        });
    }
}

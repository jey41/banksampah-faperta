<?php

namespace App\Http\Requests\Nasabah;

use App\Services\TransactionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StoreWithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'nasabah';
    }

    public function rules(): array
    {
        $user = Auth::user();

        return [
            'amount' => 'required|integer|min:10000|max:' . $user->saldo,
            'withdrawal_method' => 'required|in:tunai,transfer_bank',
            'bank_name' => 'required_if:withdrawal_method,transfer_bank|string|max:255',
            'bank_type' => 'nullable|in:btn,lainnya',
            'account_number' => 'required_if:withdrawal_method,transfer_bank|string|max:255',
            'account_name' => 'required_if:withdrawal_method,transfer_bank|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah penarikan wajib diisi.',
            'amount.integer' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Jumlah penarikan minimal Rp 10.000.',
            'amount.max' => 'Jumlah penarikan melebihi saldo Anda.',
            'withdrawal_method.required' => 'Metode penarikan wajib dipilih.',
            'withdrawal_method.in' => 'Metode penarikan tidak valid.',
            'bank_name.required_if' => 'Nama bank wajib diisi untuk transfer bank.',
            'account_number.required_if' => 'Nomor rekening wajib diisi untuk transfer bank.',
            'account_name.required_if' => 'Nama pemegang rekening wajib diisi untuk transfer bank.',
        ];
    }

    /**
     * Add custom validation for operational hours.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!TransactionService::isWithinOperationalHours()) {
                $validator->errors()->add(
                    'withdrawal_method',
                    'Pengajuan penarikan hanya dapat dilakukan pada jam operasional 08:00 - 16:00.'
                );
            }
        });
    }
}

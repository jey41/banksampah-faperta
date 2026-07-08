<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePickupRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('pickup_request')) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'assigned', 'completed', 'cancelled'])],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}

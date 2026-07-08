<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrashPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $price = $this->route('trash_price');
        return $price
            ? ($this->user()?->can('update', $price) ?? false)
            : ($this->user()?->can('create', \App\Models\TrashPrice::class) ?? false);
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'category'      => ['required', 'string', 'max:255'],
            'category_type' => ['required', Rule::in(['umum', 'donasi'])],
            'unit'          => ['required', 'string', 'max:50'],
            'price_buy'     => ['required', 'integer', 'min:0'],
            'price_sell'    => ['nullable', 'integer', 'min:0'],
            'carbon_factor' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price_sell'    => $this->price_sell ?: 0,
            'carbon_factor' => $this->carbon_factor ?: 0,
        ]);
    }
}

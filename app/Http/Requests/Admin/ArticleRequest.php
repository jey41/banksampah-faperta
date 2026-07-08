<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $article = $this->route('article');
        return $article
            ? ($this->user()?->can('update', $article) ?? false)
            : ($this->user()?->can('create', \App\Models\Article::class) ?? false);
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status'  => ['required', Rule::in(['draft', 'published'])],
            'image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}

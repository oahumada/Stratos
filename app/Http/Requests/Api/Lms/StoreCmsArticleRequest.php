<?php

namespace App\Http\Requests\Api\Lms;

use Illuminate\Foundation\Http\FormRequest;

class StoreCmsArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // further policy can be applied
    }

    public function rules(): array
    {
        return [
            'topic' => ['required', 'string', 'max:255'],
            'auto_publish' => ['sometimes', 'boolean'],
            'author_id' => ['sometimes', 'nullable', 'integer'],
            'options' => ['sometimes', 'array'],
        ];
    }
}

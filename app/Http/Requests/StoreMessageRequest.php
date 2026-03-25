<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization checked by policy in controller
        return auth()->check() && auth()->user()->people_id;
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|min:1|max:5000',
            'reply_to_message_id' => 'nullable|string|exists:messages,id',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Message body is required',
            'body.max' => 'Message cannot exceed 5000 characters',
            'reply_to_message_id.exists' => 'Reply-to message not found',
        ];
    }
}

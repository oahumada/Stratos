<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization checked by policy in controller
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'context_type' => 'sometimes|in:none,learning_assignment,performance_review,incident,survey,onboarding',
            'context_id' => 'sometimes|nullable|string|max:36',
        ];
    }

    public function messages(): array
    {
        return [
            'context_type.in' => 'Invalid context type',
        ];
    }
}

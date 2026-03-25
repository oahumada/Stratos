<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization will be checked by policy in controller
        return auth()->check() && auth()->user()->people_id;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'integer|exists:people,id',
            'context_type' => 'nullable|in:none,learning_assignment,performance_review,incident,survey,onboarding',
            'context_id' => 'nullable|string|max:36',
        ];
    }

    public function messages(): array
    {
        return [
            'participant_ids.required' => 'Must include at least one participant',
            'participant_ids.*.exists' => 'One or more participant IDs are invalid',
            'context_type.in' => 'Invalid context type',
        ];
    }
}

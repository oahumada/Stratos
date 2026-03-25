<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization checked by policy in controller
        return auth()->check() && auth()->user()->people;
    }

    public function rules(): array
    {
        return [
            'people_id' => 'required|integer|exists:people,id',
            'can_send' => 'sometimes|boolean',
            'can_read' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'people_id.required' => 'Participant ID is required',
            'people_id.exists' => 'Participant not found',
        ];
    }
}

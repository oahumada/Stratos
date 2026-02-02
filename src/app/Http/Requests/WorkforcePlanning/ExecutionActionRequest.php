<?php

namespace App\Http\Requests\WorkforcePlanning;

use Illuminate\Foundation\Http\FormRequest;

class ExecutionActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en Policy
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres',
        ];
    }
}

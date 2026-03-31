<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRiskMitigationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_type' => 'required|in:training,mentoring,promotion,retention_bonus,redeployment',
            'description' => 'required|string|max:1000',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'assigned_to' => 'sometimes|exists:users,id',
            'due_date' => 'sometimes|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'action_type.required' => 'El tipo de acción es requerido',
            'action_type.in' => 'El tipo de acción no es válido',
            'description.required' => 'La descripción es requerida',
        ];
    }
}

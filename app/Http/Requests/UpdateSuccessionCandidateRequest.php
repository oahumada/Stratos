<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuccessionCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'readiness_level' => 'sometimes|in:junior,intermediate,senior,expert',
            'skill_match_score' => 'sometimes|numeric|between:0,100',
            'estimated_months_to_ready' => 'sometimes|integer|min:0',
            'gaps' => 'sometimes|array',
            'status' => 'sometimes|in:potential,active,ready,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'readiness_level.in' => 'El nivel de preparación no es válido',
            'skill_match_score.between' => 'El puntaje de coincidencia debe estar entre 0 y 100',
            'status.in' => 'El estado no es válido',
        ];
    }
}

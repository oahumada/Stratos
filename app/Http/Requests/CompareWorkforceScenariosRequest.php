<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareWorkforceScenariosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'scenario_ids'   => ['required', 'array', 'min:2', 'max:4'],
            'scenario_ids.*' => ['required', 'integer', 'exists:scenarios,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'scenario_ids.min' => 'Se requieren al menos 2 escenarios para comparar.',
            'scenario_ids.max' => 'Máximo 4 escenarios por comparación.',
        ];
    }
}

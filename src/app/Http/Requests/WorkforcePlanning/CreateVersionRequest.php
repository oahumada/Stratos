<?php

namespace App\Http\Requests\WorkforcePlanning;

use Illuminate\Foundation\Http\FormRequest;

class CreateVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en Policy
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'copy_skills' => 'nullable|boolean',
            'copy_strategies' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la nueva versión es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'description.max' => 'La descripción no puede exceder 2000 caracteres',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres',
            'copy_skills.boolean' => 'copy_skills debe ser true o false',
            'copy_strategies.boolean' => 'copy_strategies debe ser true o false',
        ];
    }

    /**
     * Validar que el escenario esté aprobado (inmutabilidad)
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $scenario = $this->route('scenario');

            if (! $scenario || $scenario->decision_status !== 'approved') {
                $validator->errors()->add(
                    'scenario',
                    'Solo se pueden crear nuevas versiones de escenarios aprobados (inmutabilidad)'
                );
            }
        });
    }
}

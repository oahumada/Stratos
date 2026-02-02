<?php

namespace App\Http\Requests\WorkforcePlanning;

use Illuminate\Foundation\Http\FormRequest;

class SyncParentSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en Policy
    }

    public function rules(): array
    {
        return [
            'force' => 'nullable|boolean', // Si forzar actualización aunque ya existan
        ];
    }

    public function messages(): array
    {
        return [
            'force.boolean' => 'El parámetro force debe ser true o false',
        ];
    }

    /**
     * Validar que el escenario tenga padre
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $scenario = $this->route('scenario');

            if (!$scenario || !$scenario->parent_id) {
                $validator->errors()->add(
                    'scenario',
                    'Solo los escenarios hijos pueden sincronizar skills desde el padre'
                );
            }
        });
    }
}

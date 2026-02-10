<?php

namespace App\Http\Requests\WorkforcePlanning;

use Illuminate\Foundation\Http\FormRequest;

class TransitionDecisionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en Policy
    }

    public function rules(): array
    {
        return [
            'to_status' => [
                'required',
                'string',
                'in:draft,pending_approval,approved,rejected',
            ],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'to_status.required' => 'El estado de destino es obligatorio',
            'to_status.in' => 'El estado debe ser: draft, pending_approval, approved o rejected',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres',
        ];
    }

    /**
     * Validar que la transición sea válida según reglas de negocio
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $scenario = $this->route('scenario');
            $toStatus = $this->input('to_status');

            if (! $scenario || ! $scenario->canTransitionTo($toStatus)) {
                $validator->errors()->add(
                    'to_status',
                    "No se puede transicionar de '{$scenario->decision_status}' a '{$toStatus}'"
                );
            }
        });
    }
}

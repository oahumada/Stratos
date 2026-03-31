<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuccessionCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id' => 'required|exists:people,id',
            'target_role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'person_id.required' => 'El candidato es requerido',
            'person_id.exists' => 'La persona no existe',
            'target_role_id.required' => 'El rol objetivo es requerido',
            'target_role_id.exists' => 'El rol no existe',
        ];
    }
}

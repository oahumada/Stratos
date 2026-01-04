<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkforcePlanningScenarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'horizon_months' => 'required|integer|min:1|max:36',
            'fiscal_year' => 'required|integer|min:2020|max:2030',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del scenario es requerido',
            'horizon_months.required' => 'El horizonte de meses es requerido',
            'horizon_months.min' => 'El horizonte debe ser al menos 1 mes',
            'fiscal_year.required' => 'El año fiscal es requerido',
        ];
    }
}

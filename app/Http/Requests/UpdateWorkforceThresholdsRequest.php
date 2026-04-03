<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkforceThresholdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'coverage' => ['required', 'array'],
            'coverage.success_min' => ['required', 'numeric', 'min:1', 'max:300'],
            'coverage.warning_min' => ['required', 'numeric', 'min:1', 'max:300'],
            'gap' => ['required', 'array'],
            'gap.warning_max_pct' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'coverage.success_min.required' => 'Debe indicar el umbral de cobertura en estado OK.',
            'coverage.warning_min.required' => 'Debe indicar el umbral mínimo para estado Medio.',
            'gap.warning_max_pct.required' => 'Debe indicar el máximo % de gap para estado Medio.',
        ];
    }
}

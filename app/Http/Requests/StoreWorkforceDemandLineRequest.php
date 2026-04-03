<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkforceDemandLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'lines' => ['required', 'array', 'min:1', 'max:50'],
            'lines.*.unit' => ['required', 'string', 'max:120'],
            'lines.*.role_name' => ['required', 'string', 'max:120'],
            'lines.*.period' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'lines.*.volume_expected' => ['required', 'integer', 'min:0'],
            'lines.*.time_standard_minutes' => ['required', 'integer', 'min:1', 'max:10080'],
            'lines.*.productivity_factor' => ['nullable', 'numeric', 'min:0.1', 'max:2.0'],
            'lines.*.coverage_target_pct' => ['nullable', 'numeric', 'min:1', 'max:200'],
            'lines.*.attrition_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.ramp_factor' => ['nullable', 'numeric', 'min:0.1', 'max:2.0'],
            'lines.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'lines.required' => 'Debe enviar al menos una línea de demanda.',
            'lines.*.period.regex' => 'El período debe tener formato YYYY-MM (ej: 2026-09).',
            'lines.*.volume_expected.required' => 'El volumen esperado es obligatorio.',
            'lines.*.time_standard_minutes.required' => 'El tiempo estándar en minutos es obligatorio.',
        ];
    }
}

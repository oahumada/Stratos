<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkforceDemandLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'unit' => ['sometimes', 'required', 'string', 'max:120'],
            'role_name' => ['sometimes', 'required', 'string', 'max:120'],
            'period' => ['sometimes', 'required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'volume_expected' => ['sometimes', 'required', 'integer', 'min:0'],
            'time_standard_minutes' => ['sometimes', 'required', 'integer', 'min:1', 'max:10080'],
            'productivity_factor' => ['sometimes', 'required', 'numeric', 'min:0.1', 'max:2.0'],
            'coverage_target_pct' => ['sometimes', 'required', 'numeric', 'min:1', 'max:200'],
            'attrition_pct' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'ramp_factor' => ['sometimes', 'required', 'numeric', 'min:0.1', 'max:2.0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'period.regex' => 'El período debe tener formato YYYY-MM (ej: 2026-09).',
        ];
    }
}

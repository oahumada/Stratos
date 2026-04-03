<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeWorkforceOperationalSensitivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'adjustments' => ['required', 'array'],
            'adjustments.productivity_factor' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:2.0',
                'required_without_all:adjustments.coverage_target_pct,adjustments.ramp_factor',
            ],
            'adjustments.coverage_target_pct' => [
                'nullable',
                'numeric',
                'min:1',
                'max:200',
                'required_without_all:adjustments.productivity_factor,adjustments.ramp_factor',
            ],
            'adjustments.ramp_factor' => [
                'nullable',
                'numeric',
                'min:0.1',
                'max:2.0',
                'required_without_all:adjustments.productivity_factor,adjustments.coverage_target_pct',
            ],
            'adjustments.cost_per_gap_hh' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

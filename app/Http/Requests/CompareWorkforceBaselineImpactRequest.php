<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareWorkforceBaselineImpactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'impact_parameters' => ['sometimes', 'array'],
            'impact_parameters.cost_per_gap_hh' => ['sometimes', 'numeric', 'min:0'],
            'impact_parameters.cost_risk_multiplier' => ['sometimes', 'numeric', 'min:0'],
            'impact_parameters.risk_base_offset' => ['sometimes', 'numeric', 'min:-10', 'max:10'],
            'impact_parameters.risk_weight_gap_pct' => ['sometimes', 'numeric', 'min:0', 'max:2'],
            'impact_parameters.risk_weight_attrition_pct' => ['sometimes', 'numeric', 'min:0', 'max:2'],
            'impact_parameters.risk_weight_ramp_gap' => ['sometimes', 'numeric', 'min:0', 'max:2'],
        ];
    }
}

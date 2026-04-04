<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkforceSensitivitySweepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'variable'        => ['required', 'string', 'in:productivity_factor,coverage_target_pct,ramp_factor'],
            'min'             => ['required', 'numeric', 'min:0.01'],
            'max'             => ['required', 'numeric', 'gt:min'],
            'steps'           => ['nullable', 'integer', 'min:2', 'max:20'],
            'cost_per_gap_hh' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeHeadcountImpactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication handled by middleware
    }

    public function rules(): array
    {
        return [
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'headcount_delta' => 'required|integer',
            'turnover_rate' => 'required|numeric|min:0|max:1',
            'timeline_weeks' => 'nullable|integer|min:1|max:104',
        ];
    }

    public function messages(): array
    {
        return [
            'scenario_id.required' => 'Scenario ID is required.',
            'scenario_id.exists' => 'The selected scenario does not exist.',
            'headcount_delta.required' => 'Headcount change is required.',
            'headcount_delta.integer' => 'Headcount change must be a whole number.',
            'turnover_rate.required' => 'Turnover rate is required.',
            'turnover_rate.numeric' => 'Turnover rate must be a decimal between 0 and 1.',
            'turnover_rate.min' => 'Turnover rate cannot be negative.',
            'turnover_rate.max' => 'Turnover rate cannot exceed 100%.',
            'timeline_weeks.integer' => 'Timeline must be in weeks.',
            'timeline_weeks.min' => 'Timeline must be at least 1 week.',
            'timeline_weeks.max' => 'Timeline cannot exceed 2 years (104 weeks).',
        ];
    }
}

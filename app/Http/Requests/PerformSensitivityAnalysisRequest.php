<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerformSensitivityAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication handled by middleware
    }

    public function rules(): array
    {
        return [
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'budget_adjustments' => 'nullable|array|max:10',
            'budget_adjustments.*' => 'integer|min:-50|max:50',
            'headcount_adjustments' => 'nullable|array|max:10',
            'headcount_adjustments.*' => 'integer',
            'timeline_adjustments' => 'nullable|array|max:10',
            'timeline_adjustments.*' => 'integer|min:-50|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'scenario_id.required' => 'Scenario ID is required.',
            'scenario_id.exists' => 'The selected scenario does not exist.',
            'budget_adjustments.array' => 'Budget adjustments must be an array.',
            'budget_adjustments.*.integer' => 'Each budget adjustment must be a whole number (percentage).',
            'budget_adjustments.*.min' => 'Budget adjustment cannot be less than -50%.',
            'budget_adjustments.*.max' => 'Budget adjustment cannot exceed 50%.',
            'headcount_adjustments.array' => 'Headcount adjustments must be an array.',
            'headcount_adjustments.*.integer' => 'Each headcount adjustment must be a whole number.',
            'timeline_adjustments.array' => 'Timeline adjustments must be an array.',
            'timeline_adjustments.*.integer' => 'Each timeline adjustment must be a whole number (percentage).',
            'timeline_adjustments.*.min' => 'Timeline adjustment cannot be less than -50%.',
            'timeline_adjustments.*.max' => 'Timeline adjustment cannot exceed 50%.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default adjustment ranges if not provided
        $this->mergeIfMissing([
            'budget_adjustments' => [-20, -10, 0, 10, 20],
            'headcount_adjustments' => [-15, -10, 0, 10, 15],
            'timeline_adjustments' => [-25, -15, 0, 15, 25],
        ]);
    }
}

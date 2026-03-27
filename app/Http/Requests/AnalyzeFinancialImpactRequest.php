<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeFinancialImpactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication handled by middleware
    }

    public function rules(): array
    {
        return [
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'cost_per_hire' => 'nullable|integer|min:20000|max:500000',
            'cost_per_training_hour' => 'nullable|integer|min:50|max:500',
            'training_hours' => 'nullable|integer|min:0|max:200',
            'infrastructure_cost_per_week' => 'nullable|integer|min:0|max:50000',
            'headcount_delta' => 'nullable|integer',
            'training_changes' => 'nullable|integer',
            'timeline_weeks' => 'nullable|integer|min:1|max:104',
            'efficiency_improvement' => 'nullable|numeric|min:0|max:1000000',
        ];
    }

    public function messages(): array
    {
        return [
            'scenario_id.required' => 'Scenario ID is required.',
            'scenario_id.exists' => 'The selected scenario does not exist.',
            'cost_per_hire.integer' => 'Cost per hire must be a whole number.',
            'cost_per_hire.min' => 'Cost per hire must be at least $20,000.',
            'cost_per_hire.max' => 'Cost per hire cannot exceed $500,000.',
            'cost_per_training_hour.integer' => 'Training hour cost must be a whole number.',
            'cost_per_training_hour.min' => 'Training cost must be at least $50/hour.',
            'training_hours.integer' => 'Training hours must be a whole number.',
            'training_hours.max' => 'Training hours cannot exceed 200 per person.',
            'infrastructure_cost_per_week.integer' => 'Infrastructure cost must be a whole number.',
            'infrastructure_cost_per_week.max' => 'Infrastructure cost cannot exceed $50,000/week.',
            'timeline_weeks.integer' => 'Timeline must be in weeks.',
            'timeline_weeks.min' => 'Timeline must be at least 1 week.',
            'timeline_weeks.max' => 'Timeline cannot exceed 2 years (104 weeks).',
        ];
    }
}

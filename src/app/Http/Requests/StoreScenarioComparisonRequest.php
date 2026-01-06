<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScenarioComparisonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'scenario_ids' => 'required|array|min:2|max:5',
            'scenario_ids.*' => 'integer|exists:workforce_planning_scenarios,id',
            'comparison_criteria' => 'nullable|array',
            'comparison_criteria.*' => 'in:cost,time,risk,coverage',
        ];
    }
}

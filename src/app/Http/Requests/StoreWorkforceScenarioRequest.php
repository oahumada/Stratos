<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkforceScenarioRequest extends FormRequest
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
            'scenario_type' => 'nullable|in:growth,transformation,optimization,crisis,custom',
            'horizon_months' => 'required|integer|min:1|max:60',
            'time_horizon_weeks' => 'nullable|integer|min:1|max:260',
            'target_date' => 'nullable|date',
            'status' => 'nullable|in:draft,active,archived,completed',
            'assumptions' => 'nullable|array',
            'custom_config' => 'nullable|array',
            'estimated_budget' => 'nullable|numeric|min:0',
            'fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'owner' => 'nullable|string|max:255',
            'template_id' => 'nullable|exists:scenario_templates,id',
        ];
    }
}

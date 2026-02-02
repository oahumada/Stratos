<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstantiateScenarioFromTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'customizations' => 'nullable|array',
            'customizations.name' => 'nullable|string|max:255',
            'customizations.description' => 'nullable|string|max:2000',
            'customizations.scenario_type' => 'nullable|string|in:succession,growth,cost_optimization,restructuring,capacity_planning',
            'customizations.scope_type' => 'nullable|string|in:organization,department,role_family',
            'customizations.parent_id' => 'nullable|integer|exists:scenarios,id',
            'customizations.horizon_months' => 'nullable|integer|min:1|max:60',
            'customizations.time_horizon_weeks' => 'nullable|integer|min:1|max:260',
            'customizations.target_date' => 'nullable|date',
            'customizations.assumptions' => 'nullable|array',
            'customizations.custom_config' => 'nullable|array',
            'customizations.estimated_budget' => 'nullable|numeric|min:0',
            'customizations.fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'customizations.owner' => 'nullable|string|max:255',
            // Fallback para campos sin customizations (backward compatibility)
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'horizon_months' => 'nullable|integer|min:1|max:60',
            'time_horizon_weeks' => 'nullable|integer|min:1|max:260',
            'target_date' => 'nullable|date',
            'assumptions' => 'nullable|array',
            'custom_config' => 'nullable|array',
            'estimated_budget' => 'nullable|numeric|min:0',
            'fiscal_year' => 'nullable|integer|min:2020|max:2050',
            'owner' => 'nullable|string|max:255',
        ];
    }
}

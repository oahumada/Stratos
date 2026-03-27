<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScenarioTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:scenario_templates,name',
            'slug' => 'nullable|string|max:255|unique:scenario_templates,slug',
            'description' => 'required|string|max:1000',
            'scenario_type' => 'required|string|in:transformation,growth,optimization,crisis,general',
            'industry' => 'required|string|in:technology,finance,healthcare,manufacturing,retail,general',
            'icon' => 'nullable|string|max:100',
            'config' => 'nullable|array',
            'config.predefined_skills' => 'nullable|array',
            'config.suggested_strategies' => 'nullable|array',
            'config.kpis' => 'nullable|array',
            'config.assumptions' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Template name is required',
            'name.unique' => 'A template with this name already exists',
            'description.required' => 'Template description is required',
            'scenario_type.required' => 'Scenario type must be specified',
            'scenario_type.in' => 'Invalid scenario type',
            'industry.required' => 'Industry classification is required',
            'industry.in' => 'Invalid industry',
        ];
    }
}

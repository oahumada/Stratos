<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScenarioTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $templateId = $this->route('template')?->id;

        return [
            'name' => "nullable|string|max:255|unique:scenario_templates,name,{$templateId}",
            'description' => 'nullable|string|max:1000',
            'scenario_type' => 'nullable|string|in:transformation,growth,optimization,crisis,general',
            'industry' => 'nullable|string|in:technology,finance,healthcare,manufacturing,retail,general',
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
            'name.unique' => 'A template with this name already exists',
            'scenario_type.in' => 'Invalid scenario type',
            'industry.in' => 'Invalid industry',
        ];
    }
}

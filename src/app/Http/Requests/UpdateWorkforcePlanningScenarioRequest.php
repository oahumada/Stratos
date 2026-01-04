<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkforcePlanningScenarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'horizon_months' => 'sometimes|required|integer|min:1|max:36',
            'fiscal_year' => 'sometimes|required|integer|min:2020|max:2030',
            'status' => 'sometimes|required|in:draft,pending_approval,approved,archived',
        ];
    }
}

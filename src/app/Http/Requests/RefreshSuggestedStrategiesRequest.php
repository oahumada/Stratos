<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshSuggestedStrategiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'time_pressure' => 'nullable|in:low,medium,high',
            'budget_sensitivity' => 'nullable|in:low,medium,high',
            'automation_allowed' => 'nullable|boolean',
        ];
    }
}

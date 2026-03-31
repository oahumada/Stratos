<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevelopmentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goal_description' => 'required|string|max:1000',
            'target_completion_date' => 'required|date|after:today',
            'activities' => 'sometimes|array',
            'activities.*.activity' => 'required_with:activities|string|max:255',
            'activities.*.duration_hours' => 'required_with:activities|integer|min:1',
            'activities.*.status' => 'required_with:activities|in:planned,in_progress,completed',
        ];
    }

    public function messages(): array
    {
        return [
            'goal_description.required' => 'La descripción del objetivo es requerida',
            'target_completion_date.required' => 'La fecha de finalización es requerida',
            'target_completion_date.after' => 'La fecha debe ser en el futuro',
        ];
    }
}

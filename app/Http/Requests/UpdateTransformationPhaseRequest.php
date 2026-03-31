<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransformationPhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_name' => 'sometimes|string|max:255',
            'start_month' => 'sometimes|integer|min:0',
            'duration_months' => 'sometimes|integer|min:1',
            'objectives' => 'sometimes|array',
            'headcount_targets' => 'sometimes|array',
            'key_milestones' => 'sometimes|array',
        ];
    }
}

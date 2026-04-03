<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkforceActionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'action_title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'owner_user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'required', 'in:planned,in_progress,blocked,completed,cancelled'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,critical'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'progress_pct' => ['sometimes', 'required', 'integer', 'min:0', 'max:100'],
        ];
    }
}

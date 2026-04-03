<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkforceActionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'action_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:planned,in_progress,blocked,completed,cancelled'],
            'priority' => ['nullable', 'in:low,medium,high,critical'],
            'due_date' => ['nullable', 'date'],
            'progress_pct' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}

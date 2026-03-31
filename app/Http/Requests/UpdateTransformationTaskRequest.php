<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransformationTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'owner_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|in:not_started,in_progress,blocked,completed,cancelled',
            'start_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'blockers' => 'sometimes|array',
            'dependencies' => 'sometimes|array',
        ];
    }
}

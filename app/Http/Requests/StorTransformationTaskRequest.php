<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorTransformationTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id' => 'required|exists:transformation_phases,id',
            'task_name' => 'required|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'owner_id' => 'sometimes|exists:users,id',
            'start_date' => 'sometimes|date',
            'due_date' => 'sometimes|date|after:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'phase_id.required' => 'La fase es requerida',
            'task_name.required' => 'El nombre de la tarea es requerido',
            'due_date.after' => 'La fecha de vencimiento debe ser después de la fecha de inicio',
        ];
    }
}

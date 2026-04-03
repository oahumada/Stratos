<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLmsInterventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lms_enrollment_id' => ['required', 'integer', 'exists:lms_enrollments,id'],
            'lms_course_id' => ['nullable', 'integer', 'exists:lms_courses,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'source' => ['nullable', 'string', 'max:80'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}

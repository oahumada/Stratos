<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:practice,inquiry,professional,interest'],
            'domain_skills' => ['nullable', 'array'],
            'domain_skills.*' => ['string'],
            'learning_goals' => ['nullable', 'array'],
            'learning_goals.*' => ['string'],
            'max_members' => ['nullable', 'integer', 'min:2'],
            'course_id' => ['nullable', 'integer', 'exists:lms_courses,id'],
            'image_url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}

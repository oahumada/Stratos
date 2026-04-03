<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LmsCoursePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // authorisation handled in controller; allow here
        return true;
    }

    public function rules(): array
    {
        return [
            'cert_min_resource_completion_ratio' => ['nullable', 'numeric', 'between:0,1'],
            'cert_require_assessment_score' => ['nullable', 'boolean'],
            'cert_min_assessment_score' => ['nullable', 'numeric', 'between:0,100'],
            'cert_template_id' => ['nullable', 'integer', 'exists:lms_certificate_templates,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default) ?: [];

        // Ensure nulls are preserved for optional overrides
        return [
            'cert_min_resource_completion_ratio' => array_key_exists('cert_min_resource_completion_ratio', $data) ? $data['cert_min_resource_completion_ratio'] : null,
            'cert_require_assessment_score' => array_key_exists('cert_require_assessment_score', $data) ? $data['cert_require_assessment_score'] : null,
            'cert_min_assessment_score' => array_key_exists('cert_min_assessment_score', $data) ? $data['cert_min_assessment_score'] : null,
            'cert_template_id' => array_key_exists('cert_template_id', $data) ? $data['cert_template_id'] : null,
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlertThresholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Delegate authorization to controller (organization ownership checked there)
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $threshold = $this->route('threshold');

        return [
            'metric' => 'sometimes|string|max:100|unique:alert_thresholds,metric,'.$threshold->id.',id,organization_id,'.$this->user()->organization_id,
            'threshold' => 'sometimes|numeric|min:0|max:999999.99',
            'severity' => 'sometimes|in:critical,high,medium,low,info',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'metric.unique' => 'Ya existe un umbral para esta métrica en tu organización.',
            'threshold.numeric' => 'El umbral debe ser un número válido.',
            'severity.in' => 'La severidad debe ser: critical, high, medium, low o info.',
        ];
    }
}

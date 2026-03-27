<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlertThresholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\AlertThreshold::class);
    }

    public function rules(): array
    {
        return [
            'metric' => 'required|string|max:100|unique:alert_thresholds,metric,NULL,id,organization_id,' . $this->user()->organization_id,
            'threshold' => 'required|numeric|min:0|max:999999.99',
            'severity' => 'required|in:critical,high,medium,low,info',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'metric.required' => 'El nombre de la métrica es obligatorio.',
            'metric.unique' => 'Ya existe un umbral para esta métrica en tu organización.',
            'threshold.required' => 'El valor del umbral es obligatorio.',
            'threshold.numeric' => 'El umbral debe ser un número válido.',
            'severity.required' => 'El nivel de severidad es obligatorio.',
            'severity.in' => 'La severidad debe ser: critical, high, medium, low o info.',
        ];
    }
}

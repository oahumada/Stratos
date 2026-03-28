<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateExecutiveSummaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'baseline_scenario_id' => 'nullable|integer|exists:scenarios,id',
            'include_recommendations' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'baseline_scenario_id.exists' => 'The baseline scenario does not exist.',
        ];
    }
}

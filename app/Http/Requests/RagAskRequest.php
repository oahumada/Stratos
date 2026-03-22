<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RagAskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'question' => 'required|string|min:5|max:1000',
            'context_type' => 'nullable|in:evaluations,capabilities,competencies,all',
            'max_sources' => 'nullable|integer|min:1|max:10',
            'include_metadata' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Por favor ingresa una pregunta.',
            'question.min' => 'La pregunta debe tener al menos 5 caracteres.',
            'question.max' => 'La pregunta no puede exceder 1000 caracteres.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        if (is_array($validated)) {
            $validated['max_sources'] = $validated['max_sources'] ?? 5;
            $validated['context_type'] = $validated['context_type'] ?? 'evaluations';
            $validated['include_metadata'] = $validated['include_metadata'] ?? true;
        }

        return $validated;
    }
}

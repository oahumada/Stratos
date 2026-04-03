<?php

namespace App\Http\Requests;

use App\Models\Scenario;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkforceScenarioStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:'.implode(',', Scenario::WORKFORCE_ALLOWED_STATUSES)],
        ];
    }
}

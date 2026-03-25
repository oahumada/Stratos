<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessagingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->people?->organization_id === request('organization_id');
    }

    public function rules(): array
    {
        return [
            'retention_days' => 'integer|between:1,365',
            'max_participants' => 'integer|between:1,1000',
            'enable_read_receipts' => 'boolean',
            'enable_typing_indicators' => 'boolean',
            'allowed_context_types' => 'array',
        ];
    }
}

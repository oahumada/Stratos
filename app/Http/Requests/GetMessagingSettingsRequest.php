<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMessagingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->people?->organization_id === request('organization_id');
    }

    public function rules(): array
    {
        return [];
    }
}

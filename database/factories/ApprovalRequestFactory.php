<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApprovalRequest>
 */
class ApprovalRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'token' => (string) Str::uuid(),
            'approvable_type' => null,
            'approvable_id' => null,
            'approver_id' => null,
            'status' => 'pending',
            'data' => [],
            'signed_at' => null,
            'expires_at' => now()->addDays(7),
        ];
    }
}

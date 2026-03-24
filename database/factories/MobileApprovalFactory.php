<?php

namespace Database\Factories;

use App\Models\MobileApproval;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MobileApproval>
 */
class MobileApprovalFactory extends Factory
{
    protected $model = MobileApproval::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestType = $this->faker->randomElement([
            'escalated_action',
            'manual_approval',
            'policy_exception',
        ]);

        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(), // Approver
            'requester_id' => User::factory(), // Requester
            'request_type' => $requestType,
            'title' => "{$requestType} Approval Required",
            'description' => $this->faker->sentence(15),
            'context' => [
                'anomaly_type' => $this->faker->randomElement(['spike', 'trend', 'health_degradation']),
                'severity_level' => $this->faker->randomElement(['low', 'medium', 'high']),
                'metric_name' => $this->faker->word(),
                'threshold' => $this->faker->randomFloat(2, 0, 100),
                'current_value' => $this->faker->randomFloat(2, 0, 200),
            ],
            'severity' => $this->faker->randomElement(['info', 'warning', 'critical']),
            'status' => 'pending',
            'requested_at' => now(),
            'expires_at' => now()->addHours(24),
            'approved_at' => null,
            'rejected_at' => null,
            'approver_notes' => null,
            'rejection_reason' => null,
            'approval_data' => null,
            'archived_at' => null,
        ];
    }

    /**
     * Approval is approved
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now()->subHours(12),
            'approver_notes' => 'Approved after review',
        ]);
    }

    /**
     * Approval is rejected
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejected_at' => now()->subHours(6),
            'rejection_reason' => 'Does not meet criteria',
        ]);
    }

    /**
     * Approval is escalated
     */
    public function escalated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'escalated',
        ]);
    }

    /**
     * Approval expired
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Critical severity
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'critical',
        ]);
    }
}

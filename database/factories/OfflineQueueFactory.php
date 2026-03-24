<?php

namespace Database\Factories;

use App\Models\OfflineQueue;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfflineQueue>
 */
class OfflineQueueFactory extends Factory
{
    protected $model = OfflineQueue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestType = $this->faker->randomElement([
            'approval_response',
            'webhook_callback',
            'webhook_registration',
        ]);

        return [
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'request_type' => $requestType,
            'endpoint' => $this->generateEndpoint($requestType),
            'payload' => $this->generatePayload($requestType),
            'deduplication_key' => 'dedup_'.$this->faker->unique()->uuid(),
            'status' => $this->faker->randomElement(['pending', 'synced', 'duplicate']),
            'retry_count' => 0,
            'last_error' => null,
            'response_data' => null,
            'queued_at' => now()->subHours($this->faker->numberBetween(1, 24)),
            'synced_at' => null,
        ];
    }

    /**
     * Generate endpoint based on request type
     */
    protected function generateEndpoint(string $requestType): string
    {
        return match ($requestType) {
            'approval_response' => '/api/mobile/approvals/1/approve',
            'webhook_callback' => '/api/webhooks/inbound/callback',
            'webhook_registration' => '/api/webhooks/register',
            default => '/api/mobile/sync'
        };
    }

    /**
     * Generate payload based on request type
     */
    protected function generatePayload(string $requestType): array
    {
        return match ($requestType) {
            'approval_response' => [
                'approval_id' => $this->faker->randomNumber(),
                'decision' => 'approve',
                'reason' => 'Looks good',
            ],
            'webhook_callback' => [
                'event_type' => 'automation.trigger',
                'data' => [
                    'workflow_id' => $this->faker->uuid(),
                    'status' => 'completed',
                ],
            ],
            'webhook_registration' => [
                'webhook_url' => $this->faker->url(),
                'events' => ['automation.trigger', 'anomaly.detected'],
            ],
            default => []
        };
    }

    /**
     * Status: pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'retry_count' => $this->faker->numberBetween(0, 2),
        ]);
    }

    /**
     * Status: synced
     */
    public function synced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'synced',
            'synced_at' => now(),
            'response_data' => ['success' => true],
        ]);
    }

    /**
     * Status: failed
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'retry_count' => 3,
            'last_error' => 'Max retries exceeded: Connection timeout',
        ]);
    }

    /**
     * Status: duplicate
     */
    public function duplicate(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'duplicate',
            'synced_at' => now(),
            'last_error' => 'Duplicate request detected',
        ]);
    }

    /**
     * Request type: approval response
     */
    public function approvalResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'request_type' => 'approval_response',
            'endpoint' => '/api/mobile/approvals/1/approve',
        ]);
    }

    /**
     * Request type: webhook callback
     */
    public function webhookCallback(): static
    {
        return $this->state(fn (array $attributes) => [
            'request_type' => 'webhook_callback',
            'endpoint' => '/api/webhooks/inbound/callback',
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebhookRegistry>
 */
class WebhookRegistryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $secret = \Illuminate\Support\Str::random(64);

        return [
            'organization_id' => \App\Models\Organizations::factory(),
            'webhook_url' => $this->faker->url(),
            'event_filters' => ['*'],
            'signing_secret' => bcrypt($secret),
            'raw_secret' => $secret,
            'is_active' => true,
            'last_triggered_at' => now()->subHours($this->faker->numberBetween(1, 100)),
            'failure_count' => $this->faker->numberBetween(0, 5),
            'metadata' => [
                'created_user_agent' => 'Factory/Test',
                'created_ip' => $this->faker->ipv4(),
            ],
        ];
    }
}

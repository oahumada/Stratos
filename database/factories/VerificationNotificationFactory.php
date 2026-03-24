<?php

namespace Database\Factories;

use App\Models\VerificationNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerificationNotification>
 */
class VerificationNotificationFactory extends Factory
{
    protected $model = VerificationNotification::class;

    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'type' => $this->faker->randomElement(['phase_transition', 'alert_threshold', 'violation_detected']),
            'data' => [
                'organization_name' => $this->faker->word,
                'from_phase' => 'silent',
                'to_phase' => 'flagging',
                'reason' => $this->faker->sentence,
            ],
            'severity' => $this->faker->randomElement(['info', 'warning', 'critical']),
            'read_at' => null,
        ];
    }

    public function phaseTransition(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'phase_transition',
                'severity' => 'info',
                'data' => [
                    'organization_name' => $this->faker->word,
                    'from_phase' => 'silent',
                    'to_phase' => 'flagging',
                    'reason' => 'High confidence achieved',
                ],
            ];
        });
    }

    public function alertThreshold(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'alert_threshold',
                'severity' => 'warning',
                'data' => [
                    'organization_name' => $this->faker->word,
                    'metric_name' => 'error_rate',
                    'current_value' => 45,
                    'threshold' => 30,
                ],
            ];
        });
    }

    public function read(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => $this->faker->dateTimeBetween(),
            ];
        });
    }
}

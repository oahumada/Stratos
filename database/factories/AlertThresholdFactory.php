<?php

namespace Database\Factories;

use App\Models\AlertThreshold;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertThresholdFactory extends Factory
{
    protected $model = AlertThreshold::class;

    public function definition()
    {
        return [
            'organization_id' => Organization::factory(),
            // Ensure metrics are more likely to be unique in tests to avoid accidental unique constraint collisions
            'metric' => $this->faker->unique()->randomElement(['cpu_usage', 'memory_usage', 'response_time', 'queue_length', 'db_connections']),
            'threshold' => $this->faker->randomFloat(2, 10, 100),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'is_active' => true,
            'description' => $this->faker->sentence(),
        ];
    }
}

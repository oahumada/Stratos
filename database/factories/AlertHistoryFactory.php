<?php

namespace Database\Factories;

use App\Models\AlertHistory;
use App\Models\AlertThreshold;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertHistoryFactory extends Factory
{
    protected $model = AlertHistory::class;

    public function definition()
    {
        return [
            'organization_id' => Organization::factory(),
            'alert_threshold_id' => AlertThreshold::factory(),
            'triggered_at' => now()->subMinutes(rand(0, 120)),
            'acknowledged_at' => null,
            'acknowledged_by' => null,
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => 'triggered',
            'metric_value' => $this->faker->randomFloat(2, 0, 200),
            'notes' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\StrategicPlanningScenarios;
use Illuminate\Database\Eloquent\Factories\Factory;

class StrategicPlanningScenariosFactory extends Factory
{
    protected $model = StrategicPlanningScenarios::class;

    public function definition(): array
    {
        return [
            'organization_id' => 1, // Must be overridden
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'scenario_type' => $this->faker->randomElement(['growth', 'transformation', 'optimization', 'crisis', 'custom']),
            'horizon_months' => $this->faker->numberBetween(6, 24),
            'time_horizon_weeks' => $this->faker->numberBetween(26, 156),
            'status' => $this->faker->randomElement(['draft', 'active', 'archived', 'completed']),
            'fiscal_year' => now()->year,
            'created_by' => 1, // Must be overridden
            'assumptions' => [],
            'custom_config' => [],
            'estimated_budget' => $this->faker->numberBetween(50000, 500000),
        ];
    }

    public function draft(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
            ];
        });
    }

    public function approved(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function archived(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'archived',
            ];
        });
    }
}

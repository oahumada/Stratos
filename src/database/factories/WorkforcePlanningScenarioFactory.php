<?php

namespace Database\Factories;

use App\Models\WorkforcePlanningScenario;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkforcePlanningScenarioFactory extends Factory
{
    protected $model = WorkforcePlanningScenario::class;

    public function definition(): array
    {
        return [
            'organization_id' => 1,
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'horizon_months' => $this->faker->numberBetween(6, 24),
            'status' => $this->faker->randomElement(['draft', 'pending_approval', 'approved', 'archived']),
            'fiscal_year' => $this->faker->numberBetween(2025, 2027),
            'created_by' => 1,
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

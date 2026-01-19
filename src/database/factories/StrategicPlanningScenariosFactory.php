<?php

namespace Database\Factories;

use App\Models\StrategicPlanningScenarios;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StrategicPlanningScenariosFactory extends Factory
{
    protected $model = StrategicPlanningScenarios::class;

    public function definition(): array
    {
        return [
            'organization_id' => 1, // Must be overridden
            'name' => $this->faker->sentence(3),
            'code' => strtoupper($this->faker->unique()->lexify('SCN-????')),
            'description' => $this->faker->paragraph(),
            'scenario_type' => $this->faker->randomElement(['growth', 'transformation', 'optimization', 'crisis', 'custom']),
            'horizon_months' => $this->faker->numberBetween(6, 24),
            'time_horizon_weeks' => $this->faker->numberBetween(26, 156),
            'status' => $this->faker->randomElement(['draft', 'active', 'archived', 'completed']),
            'fiscal_year' => now()->year,
            'created_by' => function () {
                return User::first()?->id ?? User::factory()->create()->id;
            },
            'owner_user_id' => function () {
                return User::first()?->id ?? User::factory()->create()->id;
            },
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'scope_type' => $this->faker->randomElement(['organization_wide', 'business_unit', 'department', 'critical_roles_only']),
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

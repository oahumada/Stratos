<?php

namespace Database\Factories;

use App\Models\ScenarioClosureStrategy;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioClosureStrategyFactory extends Factory
{
    protected $model = ScenarioClosureStrategy::class;

    public function definition(): array
    {
        $strategy = $this->faker->randomElement(['build', 'buy', 'borrow', 'bot', 'bind', 'bridge']);

        return [
            'scenario_id' => \App\Models\StrategicPlanningScenarios::factory(),
            'skill_id' => \App\Models\Skill::factory(),
            'strategy' => $strategy,
            'strategy_name' => strtoupper($strategy).' Strategy',
            'description' => $this->faker->sentence(),
            'estimated_cost' => $this->faker->numberBetween(10000, 100000),
            'estimated_time_weeks' => $this->faker->numberBetween(4, 52),
            'success_probability' => $this->faker->randomFloat(2, 0.3, 0.9),
            'risk_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['proposed', 'approved', 'in_progress', 'completed', 'cancelled']),
            'action_items' => ['Step 1', 'Step 2', 'Step 3'],
            'assigned_to' => null,
            'target_completion_date' => $this->faker->dateTimeBetween('now', '+6 months'),
        ];
    }

    public function proposed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'proposed',
        ]);
    }
}

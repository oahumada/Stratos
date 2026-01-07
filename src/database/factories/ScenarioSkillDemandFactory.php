<?php

namespace Database\Factories;

use App\Models\ScenarioSkillDemand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioSkillDemandFactory extends Factory
{
    protected $model = ScenarioSkillDemand::class;

    public function definition(): array
    {
        return [
            'scenario_id' => \App\Models\WorkforcePlanningScenario::factory(),
            'skill_id' => \App\Models\Skill::factory(),
            'role_id' => $this->faker->optional()->randomElement([1, 2, 3]),
            'department' => $this->faker->optional()->randomElement(['Engineering', 'Product', 'Operations']),
            'required_headcount' => $this->faker->numberBetween(1, 20),
            'required_level' => $this->faker->randomFloat(1, 2.0, 5.0),
            'current_headcount' => $this->faker->numberBetween(0, 10),
            'current_avg_level' => $this->faker->randomFloat(1, 1.0, 4.0),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'rationale' => $this->faker->sentence(),
            'target_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }

    public function critical(): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'critical',
        ]);
    }
}

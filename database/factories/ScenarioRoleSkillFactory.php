<?php

namespace Database\Factories;

use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioRoleSkillFactory extends Factory
{
    protected $model = ScenarioRoleSkill::class;

    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'role_id' => ScenarioRole::factory(),
            'skill_id' => Skill::factory(),
            'current_level' => $this->faker->numberBetween(1, 3),
            'required_level' => $this->faker->numberBetween(3, 5),
            'change_type' => 'upskill',
            'is_critical' => false,
            'source' => 'manual',
        ];
    }
}

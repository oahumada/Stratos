<?php

namespace Database\Factories;

use App\Models\ScenarioRoleCompetency;
use App\Models\Scenario;
use App\Models\Roles;
use App\Models\Competency;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioRoleCompetencyFactory extends Factory
{
    protected $model = ScenarioRoleCompetency::class;

    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'role_id' => Roles::factory(),
            'competency_id' => Competency::factory(),
            'required_level' => $this->faker->numberBetween(1, 4),
            'is_core' => $this->faker->boolean(),
            'is_referent' => $this->faker->boolean(),
        ];
    }
}

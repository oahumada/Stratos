<?php

namespace Database\Factories;

use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioRoleFactory extends Factory
{
    protected $model = ScenarioRole::class;

    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'role_id' => Roles::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => 'draft',
            'time_horizon_weeks' => fake()->randomElement([12, 24, 36, 48]),
            'horizon_months' => 12,
            'fiscal_year' => date('Y'),
            'owner_user_id' => \App\Models\User::factory(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}

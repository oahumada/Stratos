<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+2 years');

        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->sentence(3),
            'code' => 'SCN-' . strtoupper(fake()->bothify('???###')) . '-' . time(),
            'description' => fake()->paragraph(),
            'status' => 'draft',
            'horizon_months' => fake()->randomElement([12, 24, 36, 48]),
            'fiscal_year' => (int)date('Y'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'scope_type' => fake()->randomElement(['organization_wide', 'business_unit', 'department', 'critical_roles_only']),
            'scope_notes' => fake()->paragraph(),
            'strategic_context' => fake()->paragraph(),
            'budget_constraints' => fake()->paragraph(),
            'legal_constraints' => fake()->paragraph(),
            'labor_relations_constraints' => fake()->paragraph(),
            'owner_user_id' => \App\Models\User::factory(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}

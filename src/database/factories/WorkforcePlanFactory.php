<?php

namespace Database\Factories;

use App\Models\WorkforcePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkforcePlanFactory extends Factory
{
    protected $model = WorkforcePlan::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+1 month');
        $end = (clone $start)->modify('+6 months');

        return [
            'organization_id' => 1,
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'planning_horizon_months' => 12,
            'scope_type' => 'organization_wide',
            'scope_notes' => null,
            'strategic_context' => null,
            'budget_constraints' => null,
            'legal_constraints' => null,
            'labor_relations_constraints' => null,
            'status' => 'draft',
            'owner_user_id' => 1,
            'sponsor_user_id' => 1,
            'created_by' => 1,
        ];
    }

    public function draft(): self
    {
        return $this->state(fn(array $attrs) => ['status' => 'draft']);
    }

    public function approved(): self
    {
        return $this->state(fn(array $attrs) => ['status' => 'approved', 'approved_by' => 1, 'approved_at' => now()]);
    }
}

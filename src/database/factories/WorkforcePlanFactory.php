<?php

namespace Database\Factories;

use App\Models\WorkforcePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkforcePlanFactory extends Factory
{
    protected $model = WorkforcePlan::class;

    public function definition(): array
    {
        $start = now();
        $end = (clone $start)->addMonths(6);

        return [
            'organization_id' => 1,
            'name' => 'Workforce Plan ' . uniqid(),
            'description' => 'Auto-generated workforce plan for tests',
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

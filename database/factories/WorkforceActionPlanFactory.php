<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkforceActionPlanFactory extends Factory
{
    protected $model = WorkforceActionPlan::class;

    public function definition(): array
    {
        $org = Organization::factory()->create();

        return [
            'scenario_id'         => Scenario::factory()->create(['organization_id' => $org->id])->id,
            'organization_id'     => $org->id,
            'action_title'        => $this->faker->sentence(5),
            'description'         => $this->faker->paragraph(),
            'owner_user_id'       => null,
            'status'              => $this->faker->randomElement(['planned', 'in_progress', 'blocked', 'completed']),
            'priority'            => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'due_date'            => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'progress_pct'        => $this->faker->numberBetween(0, 100),
            'budget'              => $this->faker->optional()->randomFloat(2, 1000, 200000),
            'actual_cost'         => $this->faker->optional()->randomFloat(2, 0, 150000),
            'unit_name'           => $this->faker->optional()->company(),
            'lever'               => $this->faker->optional()->randomElement([
                'HIRE', 'RESKILL', 'ROTATE', 'TRANSFER', 'CONTINGENT', 'AUTOMATE', 'HYBRID_TALENT',
            ]),
            'hybrid_coverage_pct' => $this->faker->numberBetween(0, 50),
        ];
    }
}

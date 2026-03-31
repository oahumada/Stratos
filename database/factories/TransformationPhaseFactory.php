<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransformationPhase>
 */
class TransformationPhaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phaseNumber = $this->faker->numberBetween(1, 3);
        $startMonth = ($phaseNumber - 1) * 6;
        $durationMonths = $this->faker->numberBetween(3, 6);

        $scenario = \App\Models\Scenario::factory()->create();

        return [
            'organization_id' => $scenario->organization_id,
            'scenario_id' => $scenario->id,
            'phase_name' => "Phase {$phaseNumber}",
            'phase_number' => $phaseNumber,
            'start_month' => $startMonth,
            'duration_months' => $durationMonths,
            'objectives' => [
                $this->faker->sentence(6),
                $this->faker->sentence(6),
            ],
            'headcount_targets' => [
                'total_headcount' => $this->faker->numberBetween(50, 200),
                'new_hires' => $this->faker->numberBetween(5, 30),
            ],
            'key_milestones' => [
                $this->faker->dateTime->format('Y-m-d'),
            ],
            'metadata' => [],
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DevelopmentPlan>
 */
class DevelopmentPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $candidate = \App\Models\SuccessionCandidate::factory()->create();

        return [
            'organization_id' => $candidate->organization_id,
            'succession_candidate_id' => $candidate->id,
            'goal_description' => $this->faker->sentence(10),
            'target_completion_date' => $this->faker->dateTimeBetween('now', '+12 months'),
            'activities' => [
                [
                    'activity' => $this->faker->word,
                    'duration_hours' => $this->faker->numberBetween(10, 100),
                    'status' => 'planned',
                ],
                [
                    'activity' => $this->faker->word,
                    'duration_hours' => $this->faker->numberBetween(10, 100),
                    'status' => 'in_progress',
                ],
            ],
            'status' => $this->faker->randomElement(['active', 'paused', 'completed']),
            'progress_pct' => $this->faker->numberBetween(0, 100),
        ];
    }
}

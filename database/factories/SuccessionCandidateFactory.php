<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuccessionCandidate>
 */
class SuccessionCandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scenario = \App\Models\Scenario::factory()->create();

        return [
            'organization_id' => $scenario->organization_id,
            'scenario_id' => $scenario->id,
            'person_id' => \App\Models\People::factory(),
            'target_role_id' => \App\Models\Roles::factory(),
            'skill_match_score' => $this->faker->numberBetween(30, 95),
            'readiness_level' => $this->faker->randomElement(['junior', 'intermediate', 'senior', 'expert']),
            'estimated_months_to_ready' => $this->faker->numberBetween(3, 24),
            'gaps' => [
                $this->faker->word => $this->faker->randomElement(['critical', 'high', 'medium']),
            ],
            'status' => $this->faker->randomElement(['potential', 'active', 'ready']),
            'metadata' => [],
        ];
    }
}

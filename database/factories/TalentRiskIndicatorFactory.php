<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentRiskIndicator>
 */
class TalentRiskIndicatorFactory extends Factory
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
            'risk_type' => $this->faker->randomElement(['volatility', 'retention', 'skill_gap', 'engagement']),
            'risk_score' => $this->faker->numberBetween(20, 95),
            'last_assessed_at' => now(),
            'factors' => [
                'primary_driver' => $this->faker->word,
                'confidence' => $this->faker->numberBetween(60, 100),
            ],
            'metadata' => [],
        ];
    }
}

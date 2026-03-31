<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiskMitigation>
 */
class RiskMitigationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $riskIndicator = \App\Models\TalentRiskIndicator::factory()->create();

        return [
            'organization_id' => $riskIndicator->organization_id,
            'risk_indicator_id' => $riskIndicator->id,
            'action_type' => $this->faker->randomElement(['training', 'mentoring', 'promotion', 'retention_bonus', 'redeployment']),
            'description' => $this->faker->sentence(),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed']),
            'assigned_to' => \App\Models\User::factory(),
            'due_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'completion_date' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\TalentBlueprint;
use Illuminate\Database\Eloquent\Factories\Factory;

class TalentBlueprintFactory extends Factory
{
    protected $model = TalentBlueprint::class;

    public function definition()
    {
        $human = $this->faker->numberBetween(0, 100);
        $synthetic = 100 - $human;

        return [
            'scenario_id' => null,
            'role_name' => $this->faker->jobTitle,
            'role_description' => $this->faker->sentence(12),
            'estimated_fte' => $this->faker->randomFloat(2, 0.5, 10),
            'human_percentage' => $human,
            'synthetic_percentage' => $synthetic,
            'recommended_strategy' => $this->faker->randomElement(['Buy', 'Synthetic', 'Hybrid']),
            //'logic_justification' => $this->faker->sentence(10), // Column doesn't exist
            'agent_specs' => ['type' => $this->faker->randomElement(['assistant', 'specialist', null])],
            'key_competencies' => $this->faker->randomElements(['data', 'engineering', 'product', 'ml', 'ops'], 3),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->catchPhrase(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'scenario_type' => $this->faker->randomElement(['growth', 'transformation', 'optimization', 'crisis', 'custom']),
            'industry' => $this->faker->randomElement(['tech', 'finance', 'retail', 'general']),
            'icon' => 'mdi-briefcase',
            'config' => [
                'predefined_skills' => [],
                'suggested_strategies' => ['build'],
                'kpis' => ['Coverage %', 'Time to close gaps'],
            ],
            'is_active' => true,
            'usage_count' => 0,
        ];
    }
}

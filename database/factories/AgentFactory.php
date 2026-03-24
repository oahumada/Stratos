<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'name' => $this->faker->word(),
            'role_description' => $this->faker->text(100),
            'persona' => $this->faker->text(50),
            'provider' => $this->faker->randomElement(['openai', 'deepseek']),
            'model' => $this->faker->randomElement(['gpt-4o', 'deepseek-chat']),
            'expertise_areas' => ['testing'],
            'capabilities_config' => [
                'temperature' => 0.7,
                'max_tokens' => 4096,
            ],
        ];
    }
}

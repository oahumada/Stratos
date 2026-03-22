<?php

namespace Database\Factories;

use App\Models\GuideFaq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuideFaq>
 */
class GuideFaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => null,
            'slug' => $this->faker->unique()->slug(),
            'category' => $this->faker->randomElement(['getting_started', 'scenario_planning', 'talent_360']),
            'title' => $this->faker->sentence(6),
            'question' => $this->faker->sentence(10).'?',
            'answer' => $this->faker->paragraphs(2, true),
            'tags' => [$this->faker->word(), $this->faker->word()],
            'is_active' => true,
        ];
    }
}

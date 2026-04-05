<?php

namespace Database\Factories;

use App\Models\ImprovementFeedback;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImprovementFeedback>
 */
class ImprovementFeedbackFactory extends Factory
{
    protected $model = ImprovementFeedback::class;

    public function definition(): array
    {
        $agents = ['Stratos Guide', 'Stratos Sentinel', 'Stratos Impact Cortex', 'Stratos Architect'];
        $allTags = ['hallucination', 'irrelevant', 'incomplete', 'excellent', 'biased', 'slow'];

        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'agent_id' => $this->faker->randomElement($agents),
            'evaluation_id' => null,
            'intelligence_metric_id' => null,
            'rating' => $this->faker->numberBetween(1, 5),
            'feedback_text' => $this->faker->optional()->sentence(),
            'tags' => $this->faker->randomElements($allTags, $this->faker->numberBetween(0, 3)),
            'context' => [
                'query' => $this->faker->sentence(),
                'response_snippet' => $this->faker->paragraph(),
            ],
            'status' => 'pending',
            'processed_at' => null,
        ];
    }

    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
            'tags' => $this->faker->randomElements(['hallucination', 'irrelevant', 'incomplete', 'biased'], $this->faker->numberBetween(1, 2)),
        ]);
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(4, 5),
            'tags' => ['excellent'],
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    public function applied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'applied',
            'processed_at' => now(),
        ]);
    }

    public function withHallucination(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
            'tags' => ['hallucination'],
        ]);
    }
}

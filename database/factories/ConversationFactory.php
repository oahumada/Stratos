<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'created_by' => \App\Models\People::factory(),
            'organization_id' => \App\Models\Organization::factory(),
            'context_type' => fake()->randomElement(['none', 'learning_assignment', 'performance_review', 'incident', 'survey', 'onboarding']),
            'context_id' => null,
            'is_active' => true,
            'participant_count' => 0,
            'last_message_at' => null,
        ];
    }
}

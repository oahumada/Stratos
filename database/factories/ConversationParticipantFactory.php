<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConversationParticipant>
 */
class ConversationParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => \App\Models\Conversation::factory(),
            'organization_id' => \App\Models\Organization::factory(),
            'people_id' => \App\Models\People::factory(),
            'can_send' => true,
            'can_read' => true,
            'joined_at' => now(),
            'left_at' => null,
            'last_read_at' => null,
            'unread_count' => 0,
        ];
    }
}

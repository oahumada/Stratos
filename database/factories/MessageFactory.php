<?php

namespace Database\Factories;

use App\Enums\MessageState;
use App\Models\Conversation;
use App\Models\Organization;
use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => $this->faker->text(100),
            'people_id' => People::factory(),
            'conversation_id' => Conversation::factory(),
            'organization_id' => Organization::factory(),
            'state' => MessageState::SENT,
        ];
    }
}

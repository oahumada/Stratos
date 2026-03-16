<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonMovement>
 */
class PersonMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => \App\Models\People::factory(),
            'organization_id' => \App\Models\Organizations::factory(),
            'type' => 'transfer',
            'movement_date' => now(),
        ];
    }
}

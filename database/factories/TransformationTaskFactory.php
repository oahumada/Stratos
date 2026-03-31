<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransformationTask>
 */
class TransformationTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phase = \App\Models\TransformationPhase::factory()->create();
        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $dueDate = $this->faker->dateTimeBetween($startDate, '+6 months');

        return [
            'organization_id' => $phase->organization_id,
            'phase_id' => $phase->id,
            'task_name' => $this->faker->sentence(4),
            'description' => $this->faker->sentence(10),
            'owner_id' => \App\Models\User::factory(),
            'status' => $this->faker->randomElement(['not_started', 'in_progress', 'completed']),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'completion_date' => null,
            'blockers' => [],
            'dependencies' => [],
        ];
    }
}

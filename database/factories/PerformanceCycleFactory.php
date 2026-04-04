<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceCycleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Performance Cycle ' . $this->faker->year(),
            'period' => $this->faker->year() . '-Q' . $this->faker->numberBetween(1, 4),
            'status' => 'draft',
            'starts_at' => now()->addDays(1)->toDateString(),
            'ends_at' => now()->addDays(90)->toDateString(),
            'created_by' => User::factory(),
        ];
    }
}

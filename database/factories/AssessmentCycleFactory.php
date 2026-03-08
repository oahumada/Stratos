<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentCycleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Annual Review ' . $this->faker->year(),
            'description' => $this->faker->paragraph(),
            'mode' => 'specific_date',
            'status' => 'scheduled',
            'starts_at' => now()->addDays(1),
            'ends_at' => now()->addDays(15),
            'scope' => ['type' => 'all'],
            'evaluators' => [
                'self' => true,
                'manager' => true,
                'peers' => 2,
                'reports' => false,
            ],
            'created_by' => User::factory(),
        ];
    }
}

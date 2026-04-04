<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\People;
use App\Models\PerformanceCycle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'cycle_id' => PerformanceCycle::factory(),
            'people_id' => People::factory(),
            'reviewer_id' => User::factory(),
            'self_score' => $this->faker->randomFloat(2, 0, 100),
            'manager_score' => $this->faker->randomFloat(2, 0, 100),
            'peer_score' => $this->faker->randomFloat(2, 0, 100),
            'final_score' => null,
            'calibration_score' => null,
            'strengths' => null,
            'development_areas' => null,
            'status' => 'pending',
        ];
    }
}

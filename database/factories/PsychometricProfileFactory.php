<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\PsychometricProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychometricProfileFactory extends Factory
{
    protected $model = PsychometricProfile::class;

    public function definition(): array
    {
        $traits = ['Dominance', 'Influence', 'Steadiness', 'Conscientiousness', 'Resilience', 'Adaptability'];

        return [
            'people_id' => People::factory(),
            'assessment_session_id' => null,
            'trait_name' => $this->faker->randomElement($traits),
            'score' => $this->faker->randomFloat(2, 0, 1),
            'rationale' => $this->faker->sentence(),
            'metadata' => null,
        ];
    }
}

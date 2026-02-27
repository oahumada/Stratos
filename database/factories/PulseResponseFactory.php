<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\PulseSurvey;
use App\Models\PulseResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class PulseResponseFactory extends Factory
{
    protected $model = PulseResponse::class;

    public function definition(): array
    {
        return [
            'pulse_survey_id' => PulseSurvey::factory(),
            'people_id' => People::factory(),
            'answers' => ['q1' => $this->faker->sentence(), 'q2' => $this->faker->sentence()],
            'sentiment_score' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}

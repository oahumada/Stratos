<?php

namespace Database\Factories;

use App\Models\PulseSurvey;
use Illuminate\Database\Eloquent\Factories\Factory;

class PulseSurveyFactory extends Factory
{
    protected $model = PulseSurvey::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'type' => $this->faker->randomElement(['engagement', 'satisfaction', 'culture']),
            'questions' => [
                ['text' => $this->faker->sentence(), 'type' => 'scale'],
                ['text' => $this->faker->sentence(), 'type' => 'text'],
            ],
            'is_active' => true,
            'department_id' => null,
            'ai_report' => null,
        ];
    }
}

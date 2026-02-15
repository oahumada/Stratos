<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompetencyFactory extends Factory
{
    protected $model = \App\Models\Competency::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}

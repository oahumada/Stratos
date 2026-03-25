<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizations>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'subdomain' => fake()->unique()->slug(),
            'industry' => fake()->randomElement(['tech', 'finance', 'healthcare', 'retail', 'manufacturing']),
            'size' => fake()->randomElement(['small', 'medium', 'large', 'enterprise']),
        ];
    }
}

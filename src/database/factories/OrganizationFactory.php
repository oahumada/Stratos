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
            'name' => $this->faker->company(),
            'subdomain' => $this->faker->unique()->slug(),
            'industry' => $this->faker->randomElement(['tech', 'finance', 'healthcare', 'retail', 'manufacturing']),
            'size' => $this->faker->randomElement(['small', 'medium', 'large', 'enterprise']),
        ];
    }
}

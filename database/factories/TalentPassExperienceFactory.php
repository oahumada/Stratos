<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentPassExperience>
 */
class TalentPassExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-15 years', '-1 years');
        $endDate = fake()->optional(0.3)->dateTimeBetween($startDate, 'now');

        return [
            'talent_pass_id' => \App\Models\TalentPass::factory(),
            'job_title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_current' => $endDate === null,
            'location' => fake()->city() . ', ' . fake()->countryCode(),
            'employment_type' => fake()->randomElement(['full-time', 'part-time', 'contract', 'freelance', 'internship']),
        ];
    }

    /**
     * Set as current employment (no end date)
     */
    public function current(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'end_date' => null,
                'is_current' => true,
            ];
        });
    }

    /**
     * Set as remote work location
     */
    public function remote(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => 'Remote',
            ];
        });
    }
}


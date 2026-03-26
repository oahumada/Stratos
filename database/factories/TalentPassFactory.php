<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentPass>
 */
class TalentPassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ulid' => \Illuminate\Support\Str::ulid(),
            'organization_id' => \App\Models\Organization::factory(),
            'people_id' => \App\Models\People::factory(),
            'title' => fake()->word() . ' - ' . fake()->jobTitle(),
            'summary' => fake()->paragraph(),
            'status' => 'draft',
            'visibility' => fake()->randomElement(['private', 'internal', 'public']),
            'is_featured' => false,
            'view_count' => fake()->numberBetween(0, 100),
        ];
    }

    public function published(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function public(): self
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'public',
        ]);
    }

    public function private(): self
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'private',
        ]);
    }
}

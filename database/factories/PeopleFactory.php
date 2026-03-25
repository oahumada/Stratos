<?php

namespace Database\Factories;

use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People>
 */
class PeopleFactory extends Factory
{
    protected $model = People::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'department_id' => \App\Models\Departments::inRandomOrder()->first()?->id,
            'role_id' => \App\Models\Roles::inRandomOrder()->first()?->id,
            'email' => fake()->unique()->safeEmail(),
            'hire_date' => fake()->date(),
            'photo_url' => null,
        ];
    }
}

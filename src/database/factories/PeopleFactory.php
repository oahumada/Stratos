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
            'organization_id' => 1,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'department_id' => \App\Models\Departments::inRandomOrder()->first()?->id,
            'role_id' => \App\Models\Roles::inRandomOrder()->first()?->id,
            'email' => $this->faker->unique()->safeEmail(),
            'hire_date' => $this->faker->date(),
            'photo_url' => null,
        ];
    }
}

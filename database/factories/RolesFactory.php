<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolesFactory extends Factory
{
    protected $model = \App\Models\Roles::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'status' => 'active',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\DevelopmentPath;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use Illuminate\Database\Eloquent\Factories\Factory;

class DevelopmentPathFactory extends Factory
{
    protected $model = DevelopmentPath::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'people_id' => People::factory(),
            'target_role_id' => Roles::factory(),
            'action_title' => $this->faker->sentence(3),
            'status' => 'active',
            'started_at' => now(),
        ];
    }
}

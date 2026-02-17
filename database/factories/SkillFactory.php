<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->words(2, true),
            'category' => $this->faker->randomElement(['soft', 'technical', 'leadership']),
            'complexity_level' => $this->faker->randomElement(['foundational', 'tactical', 'strategic']),
            'description' => $this->faker->sentence(),
            'is_critical' => $this->faker->boolean(20),
            'status' => 'active',
        ];
    }
}

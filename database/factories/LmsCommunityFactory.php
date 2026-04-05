<?php

namespace Database\Factories;

use App\Models\LmsCommunity;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class LmsCommunityFactory extends Factory
{
    protected $model = LmsCommunity::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['practice', 'inquiry', 'professional', 'interest']),
            'practice_domain' => fake()->word(),
            'domain_skills' => [fake()->word(), fake()->word()],
            'learning_goals' => [fake()->sentence()],
            'status' => 'active',
            'max_members' => null,
            'health_score' => 0,
            'social_presence' => 0,
            'cognitive_presence' => 0,
            'teaching_presence' => 0,
        ];
    }

    public function withMaxMembers(int $max): static
    {
        return $this->state(fn () => ['max_members' => $max]);
    }
}

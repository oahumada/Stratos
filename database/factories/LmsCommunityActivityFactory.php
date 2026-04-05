<?php

namespace Database\Factories;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LmsCommunityActivityFactory extends Factory
{
    protected $model = LmsCommunityActivity::class;

    public function definition(): array
    {
        return [
            'community_id' => LmsCommunity::factory(),
            'user_id' => User::factory(),
            'activity_type' => fake()->randomElement(['discussion', 'ugc', 'peer_review', 'mentorship', 'recognition']),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
            'metadata' => null,
            'presence_type' => fake()->randomElement(['social', 'cognitive', 'teaching']),
            'engagement_score' => fake()->numberBetween(1, 100),
        ];
    }
}

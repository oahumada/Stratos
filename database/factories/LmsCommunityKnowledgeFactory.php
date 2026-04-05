<?php

namespace Database\Factories;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityKnowledge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LmsCommunityKnowledgeFactory extends Factory
{
    protected $model = LmsCommunityKnowledge::class;

    public function definition(): array
    {
        return [
            'community_id' => LmsCommunity::factory(),
            'author_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['best_practice', 'tutorial', 'faq', 'resource', 'wiki']),
            'tags' => fake()->randomElements(['leadership', 'technical', 'soft-skills', 'process', 'tools'], 2),
            'views_count' => fake()->numberBetween(0, 100),
            'likes_count' => fake()->numberBetween(0, 20),
            'is_pinned' => false,
        ];
    }

    public function pinned(): static
    {
        return $this->state(['is_pinned' => true]);
    }
}

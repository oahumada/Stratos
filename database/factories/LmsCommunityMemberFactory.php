<?php

namespace Database\Factories;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LmsCommunityMemberFactory extends Factory
{
    protected $model = LmsCommunityMember::class;

    public function definition(): array
    {
        return [
            'community_id' => LmsCommunity::factory(),
            'user_id' => User::factory(),
            'role' => 'novice',
            'lpp_stage' => 'peripheral',
            'contribution_score' => 0,
            'discussions_count' => 0,
            'ugc_count' => 0,
            'peer_reviews_count' => 0,
            'mentorships_count' => 0,
            'joined_at' => now(),
            'last_active_at' => now(),
        ];
    }

    public function role(string $role): static
    {
        $lppMap = [
            'novice' => 'peripheral',
            'member' => 'peripheral',
            'contributor' => 'active',
            'mentor' => 'active',
            'expert' => 'core',
            'leader' => 'core',
        ];

        return $this->state(fn () => [
            'role' => $role,
            'lpp_stage' => $lppMap[$role] ?? 'peripheral',
        ]);
    }
}

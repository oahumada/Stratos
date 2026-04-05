<?php

namespace App\Services\Lms;

use App\Models\LmsCommunityActivity;
use App\Models\LmsCommunityMember;

class CommunityProgressionService
{
    /**
     * Role progression thresholds.
     * Each role requires meeting ALL thresholds to qualify.
     */
    const THRESHOLDS = [
        'novice' => [],
        'member' => ['discussions_count' => 3],
        'contributor' => ['discussions_count' => 10, 'ugc_count' => 2],
        'mentor' => ['discussions_count' => 20, 'ugc_count' => 5, 'peer_reviews_count' => 3],
        'expert' => ['discussions_count' => 50, 'ugc_count' => 10, 'peer_reviews_count' => 5, 'mentorships_count' => 2],
        'leader' => ['discussions_count' => 100, 'ugc_count' => 20, 'peer_reviews_count' => 10, 'mentorships_count' => 5],
    ];

    /**
     * LPP stage mapping: role → stage.
     */
    const LPP_MAP = [
        'novice' => 'peripheral',
        'member' => 'peripheral',
        'contributor' => 'active',
        'mentor' => 'active',
        'expert' => 'core',
        'leader' => 'core',
    ];

    /**
     * Evaluate whether a member qualifies for promotion.
     *
     * @param LmsCommunityMember $member
     * @return array{current_role: string, next_role: string, promoted: bool, metrics: array, thresholds: array}
     */
    public function evaluateProgression(LmsCommunityMember $member): array
    {
        $metrics = [
            'discussions_count' => $member->discussions_count,
            'ugc_count' => $member->ugc_count,
            'peer_reviews_count' => $member->peer_reviews_count,
            'mentorships_count' => $member->mentorships_count,
            'contribution_score' => $member->contribution_score,
        ];

        $currentRole = $member->role;
        $nextRole = $this->calculateNextRole($currentRole, $metrics);
        $promoted = $nextRole !== $currentRole;

        if ($promoted) {
            $this->promote($member, $nextRole);
        }

        return [
            'current_role' => $currentRole,
            'next_role' => $nextRole,
            'promoted' => $promoted,
            'metrics' => $metrics,
            'thresholds' => self::THRESHOLDS[$nextRole] ?? [],
        ];
    }

    /**
     * Promote a member to a new role and record a recognition activity.
     *
     * @param LmsCommunityMember $member
     * @param string             $newRole
     * @return LmsCommunityMember
     */
    public function promote(LmsCommunityMember $member, string $newRole): LmsCommunityMember
    {
        $previousRole = $member->role;

        $member->update([
            'role' => $newRole,
            'lpp_stage' => $this->getLppStage($newRole),
        ]);

        // Record recognition activity
        LmsCommunityActivity::create([
            'community_id' => $member->community_id,
            'user_id' => $member->user_id,
            'activity_type' => 'recognition',
            'title' => "Promoted from {$previousRole} to {$newRole}",
            'content' => "Member promoted based on contribution metrics.",
            'metadata' => [
                'previous_role' => $previousRole,
                'new_role' => $newRole,
            ],
            'presence_type' => 'social',
            'engagement_score' => 10,
        ]);

        return $member->fresh();
    }

    /**
     * Get the LPP stage for a given role.
     *
     * @param string $role
     * @return string peripheral|active|core
     */
    public function getLppStage(string $role): string
    {
        return self::LPP_MAP[$role] ?? 'peripheral';
    }

    /**
     * Determine the next role based on metrics.
     */
    private function calculateNextRole(string $currentRole, array $metrics): string
    {
        $progression = LmsCommunityMember::ROLE_PROGRESSION;
        $currentIndex = array_search($currentRole, $progression);

        if ($currentIndex === false || $currentIndex >= count($progression) - 1) {
            return $currentRole;
        }

        $candidateRole = $progression[$currentIndex + 1];
        $thresholds = self::THRESHOLDS[$candidateRole] ?? [];

        foreach ($thresholds as $metric => $threshold) {
            if (($metrics[$metric] ?? 0) < $threshold) {
                return $currentRole;
            }
        }

        return $candidateRole;
    }
}

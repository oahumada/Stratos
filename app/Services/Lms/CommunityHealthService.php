<?php

namespace App\Services\Lms;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityActivity;
use App\Models\LmsCommunityMember;

class CommunityHealthService
{
    /**
     * Assess community health based on the Community of Inquiry (CoI) framework.
     *
     * Returns social, cognitive, and teaching presence scores,
     * a composite health_score, and a status label.
     *
     * @param LmsCommunity $community
     * @return array{social_presence: float, cognitive_presence: float, teaching_presence: float, health_score: float, status: string, details: array}
     */
    public function assessHealth(LmsCommunity $community): array
    {
        $memberCount = $community->members()->count();

        $recentActivities = LmsCommunityActivity::where('community_id', $community->id)
            ->recent(30);

        // --- Social Presence (40% weight) ---
        $totalActivities30d = (clone $recentActivities)->count();
        $uniqueActiveMembers = (clone $recentActivities)->distinct('user_id')->count('user_id');
        $discussions30d = (clone $recentActivities)->byType('discussion')->count();
        $recognitions30d = (clone $recentActivities)->byType('recognition')->count();

        $activeRatio = $memberCount > 0 ? ($uniqueActiveMembers / $memberCount) : 0;
        $activityDensity = $memberCount > 0 ? min(1, $totalActivities30d / ($memberCount * 5)) : 0;
        $discussionDensity = $memberCount > 0 ? min(1, $discussions30d / ($memberCount * 3)) : 0;
        $recognitionBonus = min(1, $recognitions30d / max($memberCount, 1));

        $socialPresence = min(100, ($activeRatio * 40) + ($activityDensity * 25) + ($discussionDensity * 25) + ($recognitionBonus * 10));

        // --- Cognitive Presence (35% weight) ---
        $ugc30d = (clone $recentActivities)->byType('ugc')->count();
        $peerReviews30d = (clone $recentActivities)->byType('peer_review')->count();
        $knowledgeShares30d = (clone $recentActivities)->byType('knowledge_share')->count();
        $avgEngagement = (clone $recentActivities)->avg('engagement_score') ?? 0;

        $ugcDensity = $memberCount > 0 ? min(1, $ugc30d / ($memberCount * 2)) : 0;
        $reviewDensity = $memberCount > 0 ? min(1, $peerReviews30d / ($memberCount * 1)) : 0;
        $knowledgeDensity = $memberCount > 0 ? min(1, $knowledgeShares30d / ($memberCount * 1)) : 0;
        $engagementNorm = min(1, $avgEngagement / 100);

        $cognitivePresence = min(100, ($ugcDensity * 30) + ($reviewDensity * 25) + ($knowledgeDensity * 25) + ($engagementNorm * 20));

        // --- Teaching Presence (25% weight) ---
        $facilitatorActivities30d = 0;
        if ($community->facilitator_id) {
            $facilitatorActivities30d = (clone $recentActivities)
                ->where('user_id', $community->facilitator_id)
                ->count();
        }
        $mentorship30d = (clone $recentActivities)->byType('mentorship')->count();
        $events30d = (clone $recentActivities)->byType('event')->count();
        $mentorCount = $community->members()->byRole('mentor')->count()
            + $community->members()->byRole('expert')->count()
            + $community->members()->byRole('leader')->count();

        $facilitatorDensity = min(1, $facilitatorActivities30d / 10);
        $mentorshipDensity = $memberCount > 0 ? min(1, $mentorship30d / ($memberCount * 0.5)) : 0;
        $eventDensity = min(1, $events30d / 4);
        $mentorRatio = $memberCount > 0 ? min(1, $mentorCount / ($memberCount * 0.2)) : 0;

        $teachingPresence = min(100, ($facilitatorDensity * 30) + ($mentorshipDensity * 25) + ($eventDensity * 25) + ($mentorRatio * 20));

        // --- Composite score ---
        $healthScore = ($socialPresence * 0.4) + ($cognitivePresence * 0.35) + ($teachingPresence * 0.25);

        $status = match (true) {
            $healthScore >= 75 => 'thriving',
            $healthScore >= 50 => 'healthy',
            $healthScore >= 25 => 'at_risk',
            default => 'critical',
        };

        return [
            'social_presence' => round($socialPresence, 2),
            'cognitive_presence' => round($cognitivePresence, 2),
            'teaching_presence' => round($teachingPresence, 2),
            'health_score' => round($healthScore, 2),
            'status' => $status,
            'details' => [
                'member_count' => $memberCount,
                'active_members_30d' => $uniqueActiveMembers,
                'total_activities_30d' => $totalActivities30d,
                'discussions_30d' => $discussions30d,
                'ugc_30d' => $ugc30d,
                'peer_reviews_30d' => $peerReviews30d,
                'mentorships_30d' => $mentorship30d,
                'events_30d' => $events30d,
            ],
        ];
    }

    /**
     * Assess health and persist the scores to the community record.
     *
     * @param LmsCommunity $community
     * @return array The health assessment result
     */
    public function recalculateAndSave(LmsCommunity $community): array
    {
        $health = $this->assessHealth($community);

        $community->update([
            'health_score' => $health['health_score'],
            'social_presence' => $health['social_presence'],
            'cognitive_presence' => $health['cognitive_presence'],
            'teaching_presence' => $health['teaching_presence'],
        ]);

        return $health;
    }
}

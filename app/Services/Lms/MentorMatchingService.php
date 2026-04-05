<?php

namespace App\Services\Lms;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityMember;
use App\Models\User;

class MentorMatchingService
{
    /**
     * Find potential mentors for a mentee within a community.
     *
     * Returns members with role ≥ mentor whose strengths overlap
     * with the mentee's weaknesses (using domain_skills from the community).
     *
     * @param LmsCommunity $community
     * @param User         $mentee
     * @return array{mentors: array, mentee_id: int, community_id: int}
     */
    public function findMentors(LmsCommunity $community, User $mentee): array
    {
        $menteeMember = LmsCommunityMember::where('community_id', $community->id)
            ->where('user_id', $mentee->id)
            ->first();

        if (! $menteeMember) {
            return ['mentors' => [], 'mentee_id' => $mentee->id, 'community_id' => $community->id];
        }

        // Find members who can mentor (role >= mentor)
        $potentialMentors = LmsCommunityMember::where('community_id', $community->id)
            ->where('user_id', '!=', $mentee->id)
            ->whereIn('role', [
                LmsCommunityMember::ROLE_MENTOR,
                LmsCommunityMember::ROLE_EXPERT,
                LmsCommunityMember::ROLE_LEADER,
            ])
            ->with('user')
            ->get();

        $domainSkills = $community->domain_skills ?? [];
        $menteeScore = $menteeMember->contribution_score;

        $mentors = $potentialMentors->map(function (LmsCommunityMember $mentor) use ($menteeScore, $domainSkills) {
            // Score based on contribution gap and domain overlap
            $scoreGap = max(0, $mentor->contribution_score - $menteeScore);
            $skillOverlap = count($domainSkills); // All mentors share community domain

            return [
                'user_id' => $mentor->user_id,
                'user_name' => $mentor->user->name ?? null,
                'role' => $mentor->role,
                'contribution_score' => $mentor->contribution_score,
                'score_gap' => $scoreGap,
                'domain_skills' => $domainSkills,
                'match_confidence' => min(100, ($scoreGap > 0 ? 50 : 20) + ($skillOverlap * 10)),
            ];
        })
            ->sortByDesc('match_confidence')
            ->values()
            ->toArray();

        return [
            'mentors' => $mentors,
            'mentee_id' => $mentee->id,
            'community_id' => $community->id,
        ];
    }

    /**
     * Suggest all potential mentor-mentee pairings ranked by skill overlap.
     *
     * Returns novice/member/contributor members paired with mentor+ members,
     * using contribution scores and community domain_skills for ranking.
     *
     * @param LmsCommunity $community
     * @return array{pairings: array, count: int, unmatched_mentees: int}
     */
    public function suggestMatches(LmsCommunity $community): array
    {
        $members = LmsCommunityMember::where('community_id', $community->id)
            ->with('user')
            ->get();

        $mentors = $members->filter(fn (LmsCommunityMember $m) => $m->canMentor());
        $mentees = $members->filter(fn (LmsCommunityMember $m) => ! $m->canMentor());

        $domainSkills = $community->domain_skills ?? [];
        $pairings = [];
        $matchedMentees = [];

        foreach ($mentees as $mentee) {
            $bestMentor = null;
            $bestConfidence = 0;

            foreach ($mentors as $mentor) {
                // Score based on contribution gap
                $scoreGap = max(0, $mentor->contribution_score - $mentee->contribution_score);
                $skillOverlap = count($domainSkills);
                $confidence = min(100, ($scoreGap > 0 ? 50 : 20) + ($skillOverlap * 10));

                if ($confidence > $bestConfidence) {
                    $bestConfidence = $confidence;
                    $bestMentor = $mentor;
                }
            }

            if ($bestMentor) {
                $pairings[] = [
                    'mentor_id' => $bestMentor->user_id,
                    'mentor_name' => $bestMentor->user->name ?? null,
                    'mentor_role' => $bestMentor->role,
                    'mentee_id' => $mentee->user_id,
                    'mentee_name' => $mentee->user->name ?? null,
                    'mentee_role' => $mentee->role,
                    'matching_skills' => $domainSkills,
                    'confidence' => $bestConfidence,
                ];
                $matchedMentees[] = $mentee->user_id;
            }
        }

        // Sort by confidence descending
        usort($pairings, fn ($a, $b) => $b['confidence'] <=> $a['confidence']);

        return [
            'pairings' => $pairings,
            'count' => count($pairings),
            'unmatched_mentees' => $mentees->count() - count($matchedMentees),
        ];
    }
}

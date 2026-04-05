<?php

namespace App\Services\Lms;

use App\Models\LmsCommunity;
use App\Models\LmsCommunityActivity;
use App\Models\LmsCommunityMember;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CommunityService
{
    /**
     * List communities for an organization with optional filters.
     *
     * @param int   $orgId   Organization ID
     * @param array $filters Optional filters: type, status, search
     * @param int   $perPage Results per page
     * @return LengthAwarePaginator
     */
    public function list(int $orgId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LmsCommunity::query()
            ->forOrganization($orgId)
            ->with(['facilitator', 'course'])
            ->withCount('members');

        if (! empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new community. Optionally adds the facilitator as a leader member.
     *
     * @param int   $orgId Organization ID
     * @param array $data  Community data
     * @return LmsCommunity
     */
    public function create(int $orgId, array $data): LmsCommunity
    {
        return DB::transaction(function () use ($orgId, $data) {
            $community = LmsCommunity::create([
                'organization_id' => $orgId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? 'practice',
                'practice_domain' => $data['practice_domain'] ?? null,
                'domain_skills' => $data['domain_skills'] ?? null,
                'learning_goals' => $data['learning_goals'] ?? null,
                'status' => $data['status'] ?? 'active',
                'max_members' => $data['max_members'] ?? null,
                'facilitator_id' => $data['facilitator_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
                'image_url' => $data['image_url'] ?? null,
            ]);

            // Add facilitator as leader if specified
            if (! empty($data['facilitator_id'])) {
                $this->addMember($community, $data['facilitator_id'], LmsCommunityMember::ROLE_LEADER);
            }

            return $community;
        });
    }

    /**
     * Update community details.
     *
     * @param LmsCommunity $community
     * @param array        $data
     * @return LmsCommunity
     */
    public function update(LmsCommunity $community, array $data): LmsCommunity
    {
        $community->update($data);

        return $community->fresh();
    }

    /**
     * Soft-archive a community.
     *
     * @param LmsCommunity $community
     * @return LmsCommunity
     */
    public function archive(LmsCommunity $community): LmsCommunity
    {
        $community->update(['status' => 'archived']);

        return $community->fresh();
    }

    /**
     * Add a user to a community.
     *
     * @param LmsCommunity $community
     * @param User|int     $user      User instance or user ID
     * @param string       $role
     * @return LmsCommunityMember
     */
    public function join(LmsCommunity $community, User|int $user, string $role = 'novice'): LmsCommunityMember
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($community->isFull()) {
            abort(422, 'Community has reached maximum members.');
        }

        $existing = LmsCommunityMember::where('community_id', $community->id)
            ->where('user_id', $userId)
            ->exists();

        if ($existing) {
            abort(422, 'User is already a member of this community.');
        }

        $member = LmsCommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $userId,
            'role' => $role,
            'lpp_stage' => (new CommunityProgressionService)->getLppStage($role),
            'joined_at' => now(),
            'last_active_at' => now(),
        ]);

        $this->recordActivity($community, $userId, 'recognition', [
            'title' => 'Joined community',
            'presence_type' => 'social',
        ]);

        return $member;
    }

    /**
     * Remove a user from a community.
     *
     * @param LmsCommunity $community
     * @param User|int     $user
     * @return void
     */
    public function leave(LmsCommunity $community, User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        LmsCommunityMember::where('community_id', $community->id)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Get paginated community members with optional filters.
     *
     * @param LmsCommunity $community
     * @param array        $filters Optional filters: role, search
     * @param int          $perPage
     * @return LengthAwarePaginator
     */
    public function getMembers(LmsCommunity $community, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LmsCommunityMember::where('community_id', $community->id)
            ->with('user');

        if (! empty($filters['role'])) {
            $query->byRole($filters['role']);
        }

        if (! empty($filters['active_only'])) {
            $query->active();
        }

        return $query->latest('joined_at')->paginate($perPage);
    }

    /**
     * Record an activity in the community.
     *
     * @param LmsCommunity $community
     * @param User|int     $user
     * @param string       $type     Activity type
     * @param array        $data     Additional data (title, content, metadata, presence_type, engagement_score)
     * @return LmsCommunityActivity
     */
    public function recordActivity(LmsCommunity $community, User|int $user, string $type, array $data = []): LmsCommunityActivity
    {
        $userId = $user instanceof User ? $user->id : $user;

        $activity = LmsCommunityActivity::create([
            'community_id' => $community->id,
            'user_id' => $userId,
            'activity_type' => $type,
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'presence_type' => $data['presence_type'] ?? null,
            'engagement_score' => $data['engagement_score'] ?? 0,
        ]);

        // Update member last_active_at
        LmsCommunityMember::where('community_id', $community->id)
            ->where('user_id', $userId)
            ->update(['last_active_at' => now()]);

        return $activity;
    }

    /**
     * Recalculate member statistics from actual activity data.
     *
     * @param LmsCommunityMember $member
     * @return LmsCommunityMember
     */
    public function updateMemberStats(LmsCommunityMember $member): LmsCommunityMember
    {
        $activities = LmsCommunityActivity::where('community_id', $member->community_id)
            ->where('user_id', $member->user_id);

        $member->update([
            'discussions_count' => (clone $activities)->byType('discussion')->count(),
            'ugc_count' => (clone $activities)->byType('ugc')->count(),
            'peer_reviews_count' => (clone $activities)->byType('peer_review')->count(),
            'mentorships_count' => (clone $activities)->byType('mentorship')->count(),
            'contribution_score' => (clone $activities)->sum('engagement_score'),
        ]);

        return $member->fresh();
    }

    /**
     * Add a member directly by user ID (internal helper).
     */
    private function addMember(LmsCommunity $community, int $userId, string $role): LmsCommunityMember
    {
        return LmsCommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $userId,
            'role' => $role,
            'lpp_stage' => (new CommunityProgressionService)->getLppStage($role),
            'joined_at' => now(),
            'last_active_at' => now(),
        ]);
    }
}

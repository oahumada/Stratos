<?php

namespace App\Services\Lms;

use App\Models\LmsCohort;
use App\Models\LmsCohortMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CohortService
{
    public function create(int $orgId, array $data): LmsCohort
    {
        return LmsCohort::create([
            'organization_id' => $orgId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'course_id' => $data['course_id'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'max_members' => $data['max_members'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'facilitator_id' => $data['facilitator_id'] ?? null,
        ]);
    }

    public function addMember(int $cohortId, int $userId, string $role = 'member'): LmsCohortMember
    {
        $cohort = LmsCohort::findOrFail($cohortId);

        if ($cohort->max_members !== null) {
            $currentCount = LmsCohortMember::where('cohort_id', $cohortId)->count();
            if ($currentCount >= $cohort->max_members) {
                abort(422, 'Cohort has reached maximum members.');
            }
        }

        return LmsCohortMember::create([
            'cohort_id' => $cohortId,
            'user_id' => $userId,
            'role' => $role,
            'joined_at' => now(),
        ]);
    }

    public function removeMember(int $cohortId, int $userId): void
    {
        LmsCohortMember::where('cohort_id', $cohortId)
            ->where('user_id', $userId)
            ->delete();
    }

    public function bulkAddMembers(int $cohortId, array $userIds, string $role = 'member'): Collection
    {
        $members = collect();

        DB::transaction(function () use ($cohortId, $userIds, $role, &$members) {
            foreach ($userIds as $userId) {
                $existing = LmsCohortMember::where('cohort_id', $cohortId)
                    ->where('user_id', $userId)
                    ->exists();

                if (! $existing) {
                    $members->push($this->addMember($cohortId, $userId, $role));
                }
            }
        });

        return $members;
    }

    public function getCohortProgress(int $cohortId): array
    {
        $cohort = LmsCohort::with('members.user')->findOrFail($cohortId);
        $memberCount = $cohort->members->count();

        $completedCount = $cohort->members->whereNotNull('completed_at')->count();
        $completionRate = $memberCount > 0 ? round(($completedCount / $memberCount) * 100, 2) : 0;

        return [
            'cohort_id' => $cohortId,
            'total_members' => $memberCount,
            'completed' => $completedCount,
            'completion_rate' => $completionRate,
        ];
    }

    public function getForOrganization(int $orgId): Collection
    {
        return LmsCohort::where('organization_id', $orgId)
            ->with(['course', 'facilitator'])
            ->withCount('members')
            ->latest()
            ->get();
    }

    public function getMemberCohorts(int $userId, int $orgId): Collection
    {
        return LmsCohort::where('organization_id', $orgId)
            ->whereHas('members', fn ($q) => $q->where('user_id', $userId))
            ->with(['course', 'facilitator'])
            ->withCount('members')
            ->get();
    }

    public function getMembers(int $cohortId): Collection
    {
        return LmsCohortMember::where('cohort_id', $cohortId)
            ->with('user')
            ->get();
    }
}

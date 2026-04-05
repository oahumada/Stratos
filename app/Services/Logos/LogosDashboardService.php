<?php

namespace App\Services\Logos;

use App\Models\LmsCommunity;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\OrganizationSnapshot;
use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

/**
 * Stratos Logos — Cross-module analytics dashboard service.
 *
 * Consolidates data from all 11 operational modules into unified
 * executive metrics. No ETL needed: all data is native.
 *
 * Domain: Analytics & Business Intelligence (λόγος)
 */
class LogosDashboardService
{
    /**
     * Executive summary with cross-module KPIs.
     */
    public function executiveSummary(int $orgId): array
    {
        return [
            'core' => $this->coreMetrics($orgId),
            'praxis' => $this->praxisMetrics($orgId),
            'agora' => $this->agoraMetrics($orgId),
            'horizon' => $this->horizonMetrics($orgId),
            'stratos_iq' => $this->stratosIqMetrics($orgId),
        ];
    }

    /**
     * Core module metrics: people, roles, skills.
     */
    public function coreMetrics(int $orgId): array
    {
        return [
            'total_people' => People::where('organization_id', $orgId)->count(),
            'total_roles' => Roles::where('organization_id', $orgId)->count(),
            'total_skills' => Skill::where('organization_id', $orgId)->count(),
            'active_people' => People::where('organization_id', $orgId)
                ->where('status', 'active')
                ->count(),
        ];
    }

    /**
     * Praxis (LMS) module metrics: courses, enrollments, completion.
     */
    public function praxisMetrics(int $orgId): array
    {
        $courses = LmsCourse::where('organization_id', $orgId);
        $enrollments = LmsEnrollment::whereHas('course', fn ($q) => $q->where('organization_id', $orgId));

        $totalEnrollments = (clone $enrollments)->count();
        $completedEnrollments = (clone $enrollments)->where('status', 'completed')->count();

        return [
            'total_courses' => $courses->count(),
            'published_courses' => (clone $courses)->where('is_active', true)->count(),
            'total_enrollments' => $totalEnrollments,
            'completed_enrollments' => $completedEnrollments,
            'completion_rate' => $totalEnrollments > 0
                ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
                : 0,
        ];
    }

    /**
     * Ágora (Communities) module metrics.
     */
    public function agoraMetrics(int $orgId): array
    {
        $communities = LmsCommunity::where('organization_id', $orgId);

        return [
            'total_communities' => $communities->count(),
            'active_communities' => (clone $communities)->where('status', 'active')->count(),
            'avg_health_score' => round((clone $communities)
                ->where('status', 'active')
                ->avg('health_score') ?? 0, 1),
            'total_members' => DB::table('lms_community_members')
                ->whereIn('community_id', (clone $communities)->pluck('id'))
                ->count(),
        ];
    }

    /**
     * Horizon (WFP) module metrics: scenarios, coverage.
     */
    public function horizonMetrics(int $orgId): array
    {
        $scenarios = Scenario::where('organization_id', $orgId);

        return [
            'total_scenarios' => $scenarios->count(),
            'active_scenarios' => (clone $scenarios)->where('status', 'active')->count(),
            'approved_scenarios' => (clone $scenarios)->where('status', 'approved')->count(),
        ];
    }

    /**
     * Stratos IQ: organizational intelligence from snapshots.
     */
    public function stratosIqMetrics(int $orgId): array
    {
        $latest = OrganizationSnapshot::where('organizations_id', $orgId)
            ->orderBy('snapshot_date', 'desc')
            ->first();

        $previous = OrganizationSnapshot::where('organizations_id', $orgId)
            ->orderBy('snapshot_date', 'desc')
            ->skip(1)
            ->first();

        return [
            'current_iq' => $latest?->stratos_iq ?? 0,
            'previous_iq' => $previous?->stratos_iq ?? 0,
            'iq_trend' => $latest && $previous
                ? round($latest->stratos_iq - $previous->stratos_iq, 1)
                : 0,
            'average_proficiency_gap' => $latest?->average_gap ?? 0,
            'learning_velocity' => $latest?->learning_velocity ?? 0,
            'snapshot_date' => $latest?->snapshot_date?->toDateString(),
        ];
    }

    /**
     * Cross-module trend data for time-series charts.
     */
    public function trends(int $orgId, int $months = 6): array
    {
        $snapshots = OrganizationSnapshot::where('organizations_id', $orgId)
            ->orderBy('snapshot_date', 'desc')
            ->limit($months)
            ->get()
            ->reverse()
            ->values();

        return $snapshots->map(fn ($s) => [
            'date' => $s->snapshot_date->toDateString(),
            'stratos_iq' => $s->stratos_iq,
            'average_gap' => $s->average_gap,
            'learning_velocity' => $s->learning_velocity,
            'total_people' => $s->total_people,
        ])->toArray();
    }
}

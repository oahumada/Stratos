<?php

namespace App\Http\Controllers\Api;

use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\SuccessionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * OrgChartController — Organizational chart visualization & overlay
 *
 * Provides API endpoints for org chart visualization:
 * - Current org structure with real headcount
 * - Scenario-overlaid changes from scenario FTE projections
 * - Change indicators (new roles, reductions, growth)
 */
class OrgChartController
{
    /**
     * Get org chart for scenario with overlay
     *
     * GET /api/strategic-planning/scenarios/{scenarioId}/org-chart
     */
    public function __invoke(int $scenarioId): JsonResponse
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $cacheKey = "org-chart-{$scenarioId}";
        $orgChart = Cache::remember($cacheKey, 900, function () use ($scenarioId, $scenario) {
            return [
                'scenario_id' => $scenarioId,
                'scenario_name' => $scenario->name,
                'roles' => $this->buildRoleStructure($scenarioId, $scenario->organization_id),
                'summary' => $this->calculateSummary($scenarioId, $scenario->organization_id),
                'generated_at' => now()->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $orgChart,
        ]);
    }

    /**
     * Build role structure for org chart from real data
     *
     * @param  int  $scenarioId  Scenario identifier
     * @param  int  $organizationId  Organization identifier
     * @return array Role hierarchy with current vs planned headcount and successors
     */
    private function buildRoleStructure(int $scenarioId, int $organizationId): array
    {
        // Load all roles for organization with people count
        $roles = Roles::where('organization_id', $organizationId)
            ->with(['people', 'children', 'department'])
            ->withCount('people')
            ->get();

        // Load scenario role FTE projections
        $scenarioRoles = ScenarioRole::where('scenario_id', $scenarioId)
            ->whereIn('role_id', $roles->pluck('id'))
            ->get()
            ->keyBy('role_id');

        // Load succession plans for this org, keyed by target_role_id
        $successionPlans = SuccessionPlan::where('organization_id', $organizationId)
            ->with(['person:id,name,email'])
            ->whereIn('target_role_id', $roles->pluck('id'))
            ->orderByDesc('readiness_score')
            ->get()
            ->groupBy('target_role_id');

        // Build role entries with deltas and succession data
        $roleStructure = $roles->map(function ($role) use ($scenarioRoles, $successionPlans) {
            $currentCount = $role->people_count;
            $scenarioRole = $scenarioRoles->get($role->id);
            $plannedCount = $scenarioRole ? (int) ($scenarioRole->fte ?? 0) : $currentCount;
            $delta = $plannedCount - $currentCount;

            // Build successor list for this role
            $successors = ($successionPlans->get($role->id) ?? collect())->map(function ($plan) {
                return [
                    'person_id' => $plan->person_id,
                    'name' => $plan->person?->name ?? 'Unknown',
                    'email' => $plan->person?->email,
                    'readiness_level' => $plan->readiness_level,
                    'readiness_score' => $plan->readiness_score,
                    'estimated_months' => $plan->estimated_months,
                    'status' => $plan->status,
                ];
            })->values()->all();

            return [
                'id' => (string) $role->id,
                'name' => $role->name,
                'level' => $role->level,
                'department' => $role->department?->name,
                'current_count' => $currentCount,
                'planned_count' => $plannedCount,
                'delta' => $delta,
                'change_type' => $this->determineChangeType($plannedCount, $currentCount),
                'status' => $role->status,
                'role_change' => $scenarioRole?->role_change,
                'impact_level' => $scenarioRole?->impact_level,
                'subordinates' => $role->children->count(),
                'successors' => $successors,
                'succession_coverage' => count($successors) > 0 ? 'covered' : 'at_risk',
                'metadata' => [
                    'archetype' => $scenarioRole?->archetype,
                    'strategic_role' => $scenarioRole?->strategic_role,
                    'evolution_type' => $scenarioRole?->evolution_type,
                ],
            ];
        })->values()->all();

        return $roleStructure;
    }

    /**
     * Calculate summary statistics for org changes including succession coverage
     *
     * @param  int  $scenarioId  Scenario identifier
     * @param  int  $organizationId  Organization identifier
     * @return array Summary with totals, change counts and succession stats
     */
    private function calculateSummary(int $scenarioId, int $organizationId): array
    {
        $roles = Roles::where('organization_id', $organizationId)
            ->withCount('people')
            ->get();

        $scenarioRoles = ScenarioRole::where('scenario_id', $scenarioId)
            ->whereIn('role_id', $roles->pluck('id'))
            ->get()
            ->keyBy('role_id');

        // Succession coverage counts
        $successionCoveredRoleIds = SuccessionPlan::where('organization_id', $organizationId)
            ->whereIn('target_role_id', $roles->pluck('id'))
            ->distinct('target_role_id')
            ->pluck('target_role_id');

        $totalCurrent = 0;
        $totalPlanned = 0;
        $newPositions = 0;
        $reductions = 0;

        foreach ($roles as $role) {
            $current = $role->people_count;
            $scenarioRole = $scenarioRoles->get($role->id);
            $planned = $scenarioRole ? (int) ($scenarioRole->fte ?? 0) : $current;
            $delta = $planned - $current;

            $totalCurrent += $current;
            $totalPlanned += $planned;

            if ($delta > 0) {
                $newPositions += $delta;
            } elseif ($delta < 0) {
                $reductions += abs($delta);
            }
        }

        return [
            'total_roles' => $roles->count(),
            'total_current_headcount' => $totalCurrent,
            'total_planned_headcount' => $totalPlanned,
            'net_change' => $totalPlanned - $totalCurrent,
            'new_positions' => $newPositions,
            'reductions' => $reductions,
            'percentage_change' => $totalCurrent > 0
                ? round((($totalPlanned - $totalCurrent) / $totalCurrent) * 100, 2)
                : 0,
            'succession_covered' => $successionCoveredRoleIds->count(),
            'succession_at_risk' => $roles->count() - $successionCoveredRoleIds->count(),
            'succession_coverage_pct' => $roles->count() > 0
                ? round(($successionCoveredRoleIds->count() / $roles->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Determine change type based on delta
     *
     * @param  int  $planned  Planned headcount
     * @param  int  $current  Current headcount
     * @return string Change type: 'new', 'unchanged', 'grow', 'reduce'
     */
    private function determineChangeType(int $planned, int $current): string
    {
        if ($planned === $current) {
            return 'unchanged';
        }
        if ($current === 0 && $planned > 0) {
            return 'new';
        }
        if ($planned > $current) {
            return 'grow';
        }

        return 'reduce';
    }
}

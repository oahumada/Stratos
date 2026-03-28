<?php

namespace App\Http\Controllers\Api;

use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use Illuminate\Http\JsonResponse;

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

        // Build org chart data structure from real data
        $orgChart = [
            'scenario_id' => $scenarioId,
            'scenario_name' => $scenario->name,
            'roles' => $this->buildRoleStructure($scenarioId, $scenario->organization_id),
            'summary' => $this->calculateSummary($scenarioId, $scenario->organization_id),
            'generated_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'data' => $orgChart,
        ]);
    }

    /**
     * Build role structure for org chart from real data
     *
     * @param int $scenarioId Scenario identifier
     * @param int $organizationId Organization identifier
     * @return array Role hierarchy with current vs planned headcount
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

        // Build role entries with deltas
        $roleStructure = $roles->map(function ($role) use ($scenarioRoles) {
            $currentCount = $role->people_count;
            $scenarioRole = $scenarioRoles->get($role->id);
            $plannedCount = $scenarioRole ? (int)($scenarioRole->fte ?? 0) : $currentCount;
            $delta = $plannedCount - $currentCount;

            return [
                'id' => (string)$role->id,
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
     * Calculate summary statistics for org changes
     *
     * @param int $scenarioId Scenario identifier
     * @param int $organizationId Organization identifier
     * @return array Summary with totals and change counts
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

        $totalCurrent = 0;
        $totalPlanned = 0;
        $newPositions = 0;
        $reductions = 0;

        foreach ($roles as $role) {
            $current = $role->people_count;
            $scenarioRole = $scenarioRoles->get($role->id);
            $planned = $scenarioRole ? (int)($scenarioRole->fte ?? 0) : $current;
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
        ];
    }

    /**
     * Determine change type based on delta
     *
     * @param int $planned Planned headcount
     * @param int $current Current headcount
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


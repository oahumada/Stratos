<?php

namespace App\Http\Controllers\Api;

use App\Models\Scenario;
use Illuminate\Http\JsonResponse;

/**
 * OrgChartController — Organizational chart visualization & overlay
 *
 * Provides API endpoints for org chart visualization:
 * - Current org structure
 * - Scenario-overlaid org chart
 * - Change indicators
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

        // Build org chart data structure
        $orgChart = [
            'scenario_id' => $scenarioId,
            'roles' => $this->buildRoleStructure($scenario),
            'changes_summary' => [
                'new_hires' => 0,
                'reductions' => 0,
                'movers' => 0,
            ],
            'generated_at' => now()->toIso8601String(),
        ];

        // TODO: Fetch real org structure from roles/people data
        // For now, return stub data structure

        return response()->json([
            'success' => true,
            'data' => $orgChart,
        ]);
    }

    /**
     * Build role structure for org chart
     *
     * @param  Scenario  $scenario
     * @return array<int, array<string, mixed>>
     */
    private function buildRoleStructure(Scenario $scenario): array
    {
        // Stub: Return basic role structure
        return [
            [
                'id' => 1,
                'name' => 'Chief Talent Officer',
                'level' => 0, // Executive
                'count' => 1,
                'delta' => 0,
                'successors' => [],
            ],
            [
                'id' => 2,
                'name' => 'HR Manager',
                'level' => 1,
                'count' => 3,
                'delta' => 1,
                'successors' => ['Jane Doe', 'John Smith'],
            ],
            [
                'id' => 3,
                'name' => 'HR Coordinator',
                'level' => 2,
                'count' => 5,
                'delta' => 2,
                'successors' => [],
            ],
            [
                'id' => 4,
                'name' => 'Learning & Development',
                'level' => 1,
                'count' => 4,
                'delta' => -1,
                'successors' => [],
            ],
        ];
    }
}

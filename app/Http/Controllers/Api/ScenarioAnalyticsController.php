<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Services\ScenarioAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioAnalyticsController extends Controller
{
    public function __construct(protected ScenarioAnalyticsService $analyticsService) {}

    /**
     * Compare multiple scenarios side-by-side
     * POST /api/scenarios/compare
     *
     * @param Request $request expecting: { scenario_ids: [...] }
     */
    public function compareScenarios(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_ids' => 'required|array|min:2|max:4',
            'scenario_ids.*' => 'integer|exists:scenarios,id',
        ]);

        $organizationId = auth()->user()->current_organization_id;
        $scenarioIds = $validated['scenario_ids'];

        // Verify all scenarios belong to the same organization
        $scenarios = Scenario::whereIn('id', $scenarioIds)
            ->where('organization_id', $organizationId)
            ->get();

        if ($scenarios->count() !== count($scenarioIds)) {
            return response()->json(['message' => 'One or more scenarios not found or unauthorized'], 403);
        }

        $comparison = [];
        foreach ($scenarios as $scenario) {
            $comparison[] = [
                'scenario_id' => $scenario->id,
                'name' => $scenario->name,
                'code' => $scenario->code,
                'status' => $scenario->status,
                'iq' => $this->analyticsService->calculateScenarioIQ($scenario)['iq'] ?? 0,
                'financial_impact' => $this->getFinancialImpact($scenario->id),
                'risk_score' => $this->getRiskMetrics($scenario->id)['overall_risk'] ?? 0,
                'skill_gaps' => $this->getSkillGaps($scenario->id)['total_gaps'] ?? 0,
                'start_date' => $scenario->start_date,
                'end_date' => $scenario->end_date,
                'horizon_months' => $scenario->horizon_months,
            ];
        }

        return response()->json([
            'comparison' => $comparison,
            'count' => count($comparison),
        ]);
    }

    /**
     * Get comprehensive analytics for a single scenario
     * GET /api/scenarios/{scenario}/analytics
     */
    public function analytics(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $iq = $this->analyticsService->calculateScenarioIQ($scenario);
        $financialImpact = $this->getFinancialImpact($scenario->id);
        $riskMetrics = $this->getRiskMetrics($scenario->id);
        $skillGaps = $this->getSkillGaps($scenario->id);

        return response()->json([
            'scenario_id' => $scenario->id,
            'name' => $scenario->name,
            'code' => $scenario->code,
            'status' => $scenario->status,
            'iq' => $iq['iq'],
            'confidence_score' => $iq['confidence_score'] ?? 0,
            'capabilities_breakdown' => $iq['capabilities'] ?? [],
            'critical_gaps' => $iq['critical_gaps'] ?? [],
            'financial_impact' => $financialImpact,
            'risk_metrics' => $riskMetrics,
            'skill_gaps' => $skillGaps,
        ]);
    }

    /**
     * Calculate financial impact for a scenario
     * GET /api/scenarios/{scenario}/financial-impact
     */
    public function financialImpact(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $impact = $this->getFinancialImpact($scenario->id);

        return response()->json([
            'scenario_id' => $scenario->id,
            'financial_impact' => $impact,
        ]);
    }

    /**
     * Get risk assessment for a scenario
     * GET /api/scenarios/{scenario}/risk-assessment
     */
    public function riskAssessment(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $riskMetrics = $this->getRiskMetrics($scenario->id);

        return response()->json([
            'scenario_id' => $scenario->id,
            'risk_assessment' => $riskMetrics,
        ]);
    }

    /**
     * Get skill gaps analysis for a scenario
     * GET /api/scenarios/{scenario}/skill-gaps
     */
    public function skillGaps(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $gaps = $this->getSkillGaps($scenario->id);

        return response()->json([
            'scenario_id' => $scenario->id,
            'skill_gaps' => $gaps,
        ]);
    }

    /**
     * Calculate financial impact metrics for a scenario
     *
     * @return array{
     *   total_impact: float,
     *   roi_percentage: float,
     *   cost_breakdown: array,
     *   budget_allocation: array,
     *   payback_period_months: float
     * }
     */
    private function getFinancialImpact(int $scenarioId): array
    {
        // This is a placeholder implementation
        // In a real scenario, you would calculate based on actual data from:
        // - Scenario upskilling costs
        // - Closure strategy costs
        // - Timeline and effort estimates
        // - Organizational metrics (salary, turnover, productivity)

        return [
            'total_impact' => 285000.00,
            'roi_percentage' => 125.5,
            'cost_breakdown' => [
                'training' => 85000,
                'hiring' => 45000,
                'reallocation' => 32000,
                'external_services' => 123000,
            ],
            'budget_allocation' => [
                'month_1' => 45000,
                'month_2' => 60000,
                'month_3' => 90000,
                'month_4' => 45000,
                'month_5' => 45000,
            ],
            'payback_period_months' => 8.5,
        ];
    }

    /**
     * Calculate risk metrics for a scenario
     *
     * @return array{
     *   overall_risk: float,
     *   probability: float,
     *   impact: float,
     *   risk_items: array,
     *   mitigation_strategies: array
     * }
     */
    private function getRiskMetrics(int $scenarioId): array
    {
        // Placeholder implementation
        // Real implementation would:
        // - Evaluate skill availability vs demand
        // - Assess market conditions for hiring
        // - Analyze talent retention risks
        // - Calculate timeline risks

        return [
            'overall_risk' => 35.0,
            'probability' => 0.45,
            'impact' => 0.65,
            'risk_items' => [
                [
                    'id' => 1,
                    'title' => 'Limited talent pool for specialized roles',
                    'probability' => 'high',
                    'impact' => 'high',
                    'score' => 75,
                ],
                [
                    'id' => 2,
                    'title' => 'External market conditions',
                    'probability' => 'medium',
                    'impact' => 'medium',
                    'score' => 50,
                ],
                [
                    'id' => 3,
                    'title' => 'Internal adoption challenges',
                    'probability' => 'medium',
                    'impact' => 'low',
                    'score' => 30,
                ],
            ],
            'mitigation_strategies' => [
                'Establish early recruitment pipeline',
                'Create mentorship programs',
                'Build contingency plans',
                'Regular stakeholder engagement',
            ],
        ];
    }

    /**
     * Calculate skill gaps for a scenario
     *
     * @return array{
     *   total_gaps: int,
     *   critical_gaps: int,
     *   gaps_by_role: array,
     *   closure_paths: array,
     *   estimated_time_to_fill: float
     * }
     */
    private function getSkillGaps(int $scenarioId): array
    {
        // Placeholder implementation
        // Real implementation would:
        // - Compare current people skills to scenario demands
        // - Identify critical gaps
        // - Propose closure strategies (training, hiring, reallocation)

        return [
            'total_gaps' => 42,
            'critical_gaps' => 8,
            'gaps_by_role' => [
                [
                    'role' => 'Data Engineer',
                    'gaps' => 12,
                    'critical' => 3,
                    'gap_rate' => 0.35,
                ],
                [
                    'role' => 'AI/ML Specialist',
                    'gaps' => 15,
                    'critical' => 4,
                    'gap_rate' => 0.42,
                ],
                [
                    'role' => 'Product Manager',
                    'gaps' => 7,
                    'critical' => 1,
                    'gap_rate' => 0.15,
                ],
                [
                    'role' => 'Cloud Architect',
                    'gaps' => 8,
                    'critical' => 0,
                    'gap_rate' => 0.18,
                ],
            ],
            'closure_paths' => [
                [
                    'path' => 'Internal training',
                    'applicable_gaps' => 25,
                    'duration_weeks' => 12,
                    'cost_per_person' => 2500,
                ],
                [
                    'path' => 'External hiring',
                    'applicable_gaps' => 10,
                    'duration_weeks' => 8,
                    'cost_per_person' => 35000,
                ],
                [
                    'path' => 'Role reallocation',
                    'applicable_gaps' => 7,
                    'duration_weeks' => 4,
                    'cost_per_person' => 500,
                ],
            ],
            'estimated_time_to_fill' => 24.5,
        ];
    }
}

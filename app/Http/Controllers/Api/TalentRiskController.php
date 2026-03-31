<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreRiskMitigationRequest;
use App\Models\RiskMitigation;
use App\Models\Scenario;
use App\Models\TalentRiskIndicator;
use App\Services\ScenarioPlanning\TalentRiskAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TalentRiskController extends Controller
{
    public function __construct(private TalentRiskAnalyticsService $service) {}

    /**
     * GET /api/scenarios/{id}/risks/indicators
     */
    public function indexIndicators(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $indicators = TalentRiskIndicator::where('scenario_id', $scenario->id)
            ->with(['person', 'mitigations'])
            ->paginate(20);

        return response()->json([
            'data' => $indicators->items(),
            'pagination' => [
                'total' => $indicators->total(),
                'per_page' => $indicators->perPage(),
                'current_page' => $indicators->currentPage(),
                'last_page' => $indicators->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/scenarios/{id}/risks/analyze
     * Trigger risk scoring for all people in scenario
     */
    public function analyze(Scenario $scenario): JsonResponse
    {
        $this->authorize('update', $scenario);

        $count = 0;
        $people = \App\Models\People::where('organization_id', $scenario->organization_id)->get();

        foreach ($people as $person) {
            // Analyze volatility risk
            $volatilityScore = $this->service->analyzeVolatilityRisk($person, $scenario->organization);

            TalentRiskIndicator::updateOrCreate(
                [
                    'scenario_id' => $scenario->id,
                    'person_id' => $person->id,
                    'risk_type' => 'volatility',
                ],
                [
                    'organization_id' => $scenario->organization_id,
                    'risk_score' => $volatilityScore,
                    'last_assessed_at' => now(),
                ]
            );

            // Analyze retention risk
            $retentionScore = $this->service->predictRetentionRisk($person, $scenario);

            TalentRiskIndicator::updateOrCreate(
                [
                    'scenario_id' => $scenario->id,
                    'person_id' => $person->id,
                    'risk_type' => 'retention',
                ],
                [
                    'organization_id' => $scenario->organization_id,
                    'risk_score' => 100 - $retentionScore, // Convert from retention probability to risk
                    'last_assessed_at' => now(),
                ]
            );

            $count++;
        }

        return response()->json([
            'message' => "Analyzed $count people",
            'count' => $count,
        ]);
    }

    /**
     * GET /api/scenarios/{id}/risks/summary
     */
    public function getSummary(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $indicators = TalentRiskIndicator::where('scenario_id', $scenario->id)->get();

        $highRiskCount = $indicators->where('risk_score', '>=', 70)->count();
        $mediumRiskCount = $indicators->whereBetween('risk_score', [40, 69])->count();
        $lowRiskCount = $indicators->where('risk_score', '<', 40)->count();

        return response()->json([
            'data' => [
                'total_indicators' => $indicators->count(),
                'high_risk_count' => $highRiskCount,
                'medium_risk_count' => $mediumRiskCount,
                'low_risk_count' => $lowRiskCount,
                'average_risk_score' => round($indicators->avg('risk_score'), 2),
            ],
        ]);
    }

    /**
     * GET /api/scenarios/{id}/risks/{riskType}/details
     */
    public function getDetailsByType(Scenario $scenario, string $riskType): JsonResponse
    {
        $this->authorize('view', $scenario);

        $indicators = TalentRiskIndicator::where('scenario_id', $scenario->id)
            ->where('risk_type', $riskType)
            ->with(['person', 'mitigations'])
            ->get();

        return response()->json(['data' => $indicators]);
    }

    /**
     * POST /api/scenarios/{id}/risks/{indicatorId}/mitigations
     */
    public function recordMitigation(
        TalentRiskIndicator $indicator,
        StoreRiskMitigationRequest $request
    ): JsonResponse {
        $this->authorize('create', RiskMitigation::class);

        $mitigation = $indicator->mitigations()->create($request->validated());

        return response()->json(['data' => $mitigation], 201);
    }

    /**
     * GET /api/scenarios/{id}/risks/{indicatorId}/mitigations
     */
    public function listMitigations(TalentRiskIndicator $indicator): JsonResponse
    {
        $this->authorize('view', $indicator);

        $mitigations = $indicator->mitigations()
            ->with('assignee')
            ->get();

        return response()->json(['data' => $mitigations]);
    }

    /**
     * PATCH /api/risks/mitigations/{id}/status
     */
    public function updateMitigationStatus(RiskMitigation $mitigation, Request $request): JsonResponse
    {
        $this->authorize('update', $mitigation);

        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,completed,failed',
        ]);

        if ($validated['status'] === 'completed') {
            $mitigation->markCompleted();
        } else {
            $mitigation->update(['status' => $validated['status']]);
        }

        return response()->json(['data' => $mitigation]);
    }

    /**
     * GET /api/scenarios/{id}/risks/heatmap
     * Visualization data: risk overview by type and level
     */
    public function getRiskHeatmap(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $indicators = TalentRiskIndicator::where('scenario_id', $scenario->id)->get();

        $heatmap = [
            'volatility' => [
                'high' => $indicators->where('risk_type', 'volatility')->where('risk_score', '>=', 70)->count(),
                'medium' => $indicators->where('risk_type', 'volatility')->whereBetween('risk_score', [40, 69])->count(),
                'low' => $indicators->where('risk_type', 'volatility')->where('risk_score', '<', 40)->count(),
            ],
            'retention' => [
                'high' => $indicators->where('risk_type', 'retention')->where('risk_score', '>=', 70)->count(),
                'medium' => $indicators->where('risk_type', 'retention')->whereBetween('risk_score', [40, 69])->count(),
                'low' => $indicators->where('risk_type', 'retention')->where('risk_score', '<', 40)->count(),
            ],
            'skill_gap' => [
                'high' => $indicators->where('risk_type', 'skill_gap')->where('risk_score', '>=', 70)->count(),
                'medium' => $indicators->where('risk_type', 'skill_gap')->whereBetween('risk_score', [40, 69])->count(),
                'low' => $indicators->where('risk_type', 'skill_gap')->where('risk_score', '<', 40)->count(),
            ],
            'engagement' => [
                'high' => $indicators->where('risk_type', 'engagement')->where('risk_score', '>=', 70)->count(),
                'medium' => $indicators->where('risk_type', 'engagement')->whereBetween('risk_score', [40, 69])->count(),
                'low' => $indicators->where('risk_type', 'engagement')->where('risk_score', '<', 40)->count(),
            ],
        ];

        return response()->json(['data' => $heatmap]);
    }
}

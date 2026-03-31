<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AnalyzeFinancialImpactRequest;
use App\Http\Requests\AnalyzeHeadcountImpactRequest;
use App\Http\Requests\PerformSensitivityAnalysisRequest;
use App\Models\Scenario;
use App\Services\ScenarioPlanning\WhatIfAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * WhatIfAnalysisController — What-if scenario analysis endpoints
 *
 * Provides API endpoints for analyzing scenario impacts:
 * - Headcount impact analysis
 * - Financial impact calculation
 * - Risk assessment
 * - Baseline comparison
 * - Outcome prediction
 * - Sensitivity analysis
 */
class WhatIfAnalysisController
{
    public function __construct(private WhatIfAnalysisService $whatIfService) {}

    /**
     * Analyze headcount impact of scenario changes
     *
     * POST /api/strategic-planning/what-if/headcount-impact
     */
    public function analyzeHeadcountImpact(AnalyzeHeadcountImpactRequest $request): JsonResponse
    {
        Log::info('WhatIfAnalysisController::analyzeHeadcountImpact called', [
            'user_id' => $request->user()?->id,
            'scenario_id' => $request->integer('scenario_id'),
            'payload' => $request->all(),
        ]);

        try {
            $analysis = $this->whatIfService->analyzeHeadcountImpact(
                $request->integer('scenario_id'),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'data' => $analysis,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatIfAnalysisService error', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * Analyze financial impact of scenario changes
     *
     * POST /api/strategic-planning/what-if/financial-impact
     */
    public function analyzeFinancialImpact(AnalyzeFinancialImpactRequest $request): JsonResponse
    {
        $analysis = $this->whatIfService->analyzeFinancialImpact(
            $request->integer('scenario_id'),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Analyze risk impact of scenario changes
     *
     * POST /api/strategic-planning/what-if/risk-impact
     */
    public function analyzeRiskImpact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'timeline_weeks' => 'nullable|integer|min:1|max:104',
            'headcount_delta' => 'nullable|integer',
            'turnover_rate' => 'nullable|numeric|min:0|max:1',
            'complexity' => 'nullable|numeric|min:0|max:2',
            'technology_changes' => 'nullable|integer|min:0',
            'total_cost' => 'nullable|numeric|min:0',
        ]);

        $analysis = $this->whatIfService->analyzeRiskImpact(
            $validated['scenario_id'],
            $validated
        );

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Compare scenario with baseline
     *
     * GET /api/strategic-planning/what-if/compare
     */
    public function compareWithBaseline(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'baseline_scenario_id' => 'nullable|integer|exists:scenarios,id',
        ]);

        $analysis = $this->whatIfService->compareScenariosWithBaseline(
            $validated['scenario_id'],
            $validated['baseline_scenario_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Predict outcomes and success probability
     *
     * POST /api/strategic-planning/what-if/predict-outcomes
     */
    public function predictOutcomes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'team_readiness' => 'nullable|numeric|min:0|max:1',
            'budget_confidence' => 'nullable|numeric|min:0|max:1',
            'stakeholder_buy_in' => 'nullable|numeric|min:0|max:1',
            'external_dependencies' => 'nullable|numeric|min:0|max:1',
            'change_management_readiness' => 'nullable|numeric|min:0|max:1',
        ]);

        $scenarioId = $validated['scenario_id'];
        $params = collect($validated)->except(['scenario_id'])->toArray();

        $analysis = $this->whatIfService->predictOutcomes($scenarioId, $params);

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Perform sensitivity analysis on scenario variables
     *
     * POST /api/strategic-planning/what-if/sensitivity-analysis
     */
    public function performSensitivityAnalysis(PerformSensitivityAnalysisRequest $request): JsonResponse
    {
        $analysis = $this->whatIfService->performSensitivityAnalysis(
            $request->integer('scenario_id'),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * Run comprehensive what-if analysis (all components)
     *
     * POST /api/strategic-planning/what-if/comprehensive
     */
    public function comprehensiveAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'headcount_delta' => 'nullable|integer',
            'timeline_weeks' => 'nullable|integer|min:1|max:104',
            'cost_per_hire' => 'nullable|integer|min:20000',
            'turnover_rate' => 'nullable|numeric|min:0|max:1',
            'complexity' => 'nullable|numeric|min:0|max:2',
            'team_readiness' => 'nullable|numeric|min:0|max:1',
            'budget_confidence' => 'nullable|numeric|min:0|max:1',
        ]);

        $scenarioId = $validated['scenario_id'];

        // Run all analyses
        $headcountAnalysis = $this->whatIfService->analyzeHeadcountImpact($scenarioId, $validated);
        $financialAnalysis = $this->whatIfService->analyzeFinancialImpact($scenarioId, $validated);
        $riskAnalysis = $this->whatIfService->analyzeRiskImpact($scenarioId, $validated);
        $baselineComparison = $this->whatIfService->compareScenariosWithBaseline($scenarioId);
        $predictions = $this->whatIfService->predictOutcomes($scenarioId, $validated);
        $sensitivity = $this->whatIfService->performSensitivityAnalysis($scenarioId, $validated);

        // Synthesize into comprehensive report
        $report = [
            'scenario_id' => $scenarioId,
            'analysis_timestamp' => now()->toIso8601String(),
            'executive_summary' => $this->generateExecutiveSummary([
                'headcount' => $headcountAnalysis,
                'financial' => $financialAnalysis,
                'risk' => $riskAnalysis,
                'predictions' => $predictions,
            ]),
            'headcount_analysis' => $headcountAnalysis,
            'financial_analysis' => $financialAnalysis,
            'risk_analysis' => $riskAnalysis,
            'baseline_comparison' => $baselineComparison,
            'outcome_predictions' => $predictions,
            'sensitivity_analysis' => $sensitivity,
            'overall_recommendation' => $this->generateOverallRecommendation(
                $predictions['success_probability'],
                $riskAnalysis['overall_risk_score'],
                $financialAnalysis['roi']
            ),
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    // ================== HELPER METHODS ==================

    private function generateExecutiveSummary(array $analyses): array
    {
        $headcount = $analyses['headcount'];
        $financial = $analyses['financial'];
        $risk = $analyses['risk'];
        $predictions = $analyses['predictions'];

        $summary = 'Scenario requires ';
        if ($headcount['hiring_needs'] > 0) {
            $summary .= "hiring {$headcount['hiring_needs']} people. ";
        }
        $summary .= "Expected ROI: {$financial['roi']}%. ";
        $summary .= "Risk Level: {$risk['risk_level']}. ";
        $summary .= "Success Probability: {$predictions['success_probability']}%.";

        return [
            'headline' => $summary,
            'key_metrics' => [
                'headcount_change' => $headcount['headcount_delta'],
                'financial_net_impact' => $financial['net_impact'],
                'roi' => $financial['roi'],
                'risk_score' => $risk['overall_risk_score'],
                'success_probability' => $predictions['success_probability'],
            ],
            'critical_factors' => array_merge(
                $headcount['risks'] ?? [],
                array_slice($risk['individual_risks'] ?? [], 0, 2)
            ),
        ];
    }

    private function generateOverallRecommendation(float $successProbability, float $riskScore, float $roi): string
    {
        if ($successProbability < 0.5 || $riskScore > 7) {
            return 'RECOMMENDATION: DO NOT PROCEED - Significant risks outweigh benefits. Revise scenario parameters.';
        }

        if ($successProbability < 0.65 || $riskScore > 5.5) {
            return 'RECOMMENDATION: PROCEED WITH CAUTION - Strengthen mitigation strategies before launch.';
        }

        if ($roi < 0.5) {
            return 'RECOMMENDATION: RECONSIDER - ROI is marginal. Explore alternative scenarios for better returns.';
        }

        if ($successProbability > 0.8 && $riskScore < 3.5) {
            return 'RECOMMENDATION: PROCEED - Scenario is well-positioned for success with manageable risks.';
        }

        return 'RECOMMENDATION: PROCEED - Scenario is feasible. Execute with standard governance.';
    }
}

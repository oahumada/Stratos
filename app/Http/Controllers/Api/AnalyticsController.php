<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnomalyDetectionService;
use App\Services\Analytics\PredictiveInsightsService;
use App\Services\Analytics\AutomatedRecommendationsService;
use App\Services\Analytics\MetricsAggregationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * AnalyticsController
 *
 * Provides AI/ML analytics endpoints for Phase 9:
 * - Anomaly detection
 * - Predictive insights
 * - Automated recommendations
 * - Real-time metrics
 *
 * All endpoints are multi-tenant scoped by organization_id.
 */
class AnalyticsController extends Controller
{
    public function __construct(
        private AnomalyDetectionService $anomalyService,
        private PredictiveInsightsService $predictiveService,
        private AutomatedRecommendationsService $recommendationsService,
        private MetricsAggregationService $metricsService
    ) {
        $this->middleware('auth:sanctum');
        $this->middleware('verified_organization');
    }

    /**
     * GET /api/analytics/anomalies
     * Detect anomalies in verification metrics and talent trends
     */
    public function getAnomalies(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $verificationAnomalies = $this->anomalyService->analyzeVerificationMetrics($organizationId);
        $talentAnomalies = $this->anomalyService->analyzeTalentAnomalies($organizationId);

        return response()->json([
            'organization_id' => $organizationId,
            'timestamp' => now()->toIso8601String(),
            'verification_anomalies' => $verificationAnomalies,
            'talent_anomalies' => $talentAnomalies,
            'total_anomalies' => count($verificationAnomalies) + count($talentAnomalies),
        ]);
    }

    /**
     * GET /api/analytics/predictions/compliance
     * Forecast compliance score for next 30 days
     */
    public function forecastCompliance(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $forecast = $this->predictiveService->forecastCompliance($organizationId);

        return response()->json($forecast);
    }

    /**
     * GET /api/analytics/predictions/deployment-window
     * Find optimal time window for major deployments
     */
    public function predictDeploymentWindow(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;
        $daysAhead = $request->query('days', 14);

        $window = $this->predictiveService->predictOptimalDeploymentWindow(
            $organizationId,
            intval($daysAhead)
        );

        return response()->json($window);
    }

    /**
     * GET /api/analytics/predictions/resources
     * Predict resource needs and capacity requirements
     */
    public function predictResourceNeeds(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $predictions = $this->predictiveService->predictResourceNeeds($organizationId);

        return response()->json($predictions);
    }

    /**
     * POST /api/analytics/predictions/transition-risk
     * Assess risk for a specific role transition
     */
    public function assessTransitionRisk(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;
        $roleId = $request->validated('role_id');

        $risk = $this->predictiveService->assessTransitionRisk($organizationId, $roleId);

        return response()->json($risk);
    }

    /**
     * GET /api/analytics/recommendations
     * Generate comprehensive AI recommendations
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;
        $includeLLM = $request->query('include_llm', false);

        $recommendations = $this->recommendationsService->generateComprehensiveRecommendations($organizationId);

        if ($includeLLM) {
            // Optional: Enhance top recommendations with LLM explanations
            foreach ($recommendations['recommendations'] as &$rec) {
                if ($rec['priority'] === 'CRITICAL' || $rec['priority'] === 'HIGH') {
                    $rec = $this->recommendationsService->getDetailedExplanation(
                        $organizationId,
                        $rec,
                        useLLM: true
                    );
                }
            }
        }

        return response()->json($recommendations);
    }

    /**
     * GET /api/analytics/metrics/current
     * Get current system metrics snapshot
     */
    public function getCurrentMetrics(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        $metrics = $this->metricsService->getCurrentMetrics($organizationId);

        return response()->json([
            'organization_id' => $organizationId,
            'timestamp' => now()->toIso8601String(),
            'metrics' => $metrics,
        ]);
    }

    /**
     * GET /api/analytics/metrics/history
     * Get metrics history for analysis
     */
    public function getMetricsHistory(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;
        $days = $request->query('days', 30);
        $interval = $request->query('interval', 'daily');

        $history = $this->metricsService->getMetricsHistory(
            $organizationId,
            intval($days),
            $interval
        );

        return response()->json([
            'organization_id' => $organizationId,
            'period_days' => $days,
            'interval' => $interval,
            'total_data_points' => $history->count(),
            'data' => $history->values(),
        ]);
    }

    /**
     * GET /api/analytics/metrics/comparison
     * Compare current metrics with previous period
     */
    public function getMetricsComparison(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;
        $days = $request->query('days', 7);

        $comparison = $this->metricsService->getMetricsComparison($organizationId, intval($days));

        return response()->json([
            'organization_id' => $organizationId,
            'comparison_period_days' => $days,
            'data' => $comparison,
        ]);
    }

    /**
     * GET /api/analytics/metrics/latency-percentiles
     * Get latency percentiles (p50, p95, p99, max) over time
     */
    public function getLatencyPercentiles(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;
        $days = $request->query('days', 30);

        $percentiles = $this->metricsService->getLatencyPercentiles($organizationId, intval($days));

        return response()->json([
            'organization_id' => $organizationId,
            'period_days' => $days,
            'latency_percentiles_ms' => $percentiles,
        ]);
    }

    /**
     * GET /api/analytics/dashboard-summary
     * Unified dashboard summary with all analytics
     */
    public function getDashboardSummary(Request $request): JsonResponse
    {
        $organizationId = $request->user()->organization_id;

        return response()->json([
            'organization_id' => $organizationId,
            'timestamp' => now()->toIso8601String(),
            'current_metrics' => $this->metricsService->getCurrentMetrics($organizationId),
            'anomalies_count' => [
                'verification' => count($this->anomalyService->analyzeVerificationMetrics($organizationId)),
                'talent' => count($this->anomalyService->analyzeTalentAnomalies($organizationId)),
            ],
            'critical_recommendations' => $this->getCriticalRecommendations($organizationId),
            'deployment_feasibility' => $this->predictiveService->predictOptimalDeploymentWindow(
                $organizationId,
                7
            )['next_optimal_date'] ?? null,
        ]);
    }

    /**
     * Helper: Get only critical recommendations
     */
    private function getCriticalRecommendations(string $organizationId): array
    {
        $all = $this->recommendationsService->generateComprehensiveRecommendations($organizationId);
        
        return array_filter(
            $all['recommendations'],
            fn($rec) => $rec['priority'] === 'CRITICAL' || $rec['priority'] === 'HIGH'
        );
    }
}

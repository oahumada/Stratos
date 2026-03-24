<?php

namespace App\Services\Analytics;

use App\Models\VerificationAudit;
use App\Models\EventStore;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * PredictiveInsightsService
 *
 * Generates AI-driven insights and forecasts using real-time metrics data.
 *
 * Capabilities:
 * - Time-series forecasting (ARIMA-like simplified)
 * - Compliance score trends
 * - Transition time predictions
 * - System capacity planning
 * - Risk scoring for deployments
 */
class PredictiveInsightsService
{
    public function __construct(
        private MetricsAggregationService $metricsService,
        private AnomalyDetectionService $anomalyService
    ) {}

    /**
     * Generate compliance forecast for next 30 days
     */
    public function forecastCompliance(string $organizationId): array
    {
        $history = $this->metricsService->getMetricsHistory(
            organizationId: $organizationId,
            days: 60,
            interval: 'daily'
        );

        if ($history->count() < 7) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 7 days of data',
            ];
        }

        $scores = $history->pluck('compliance_score')->toArray();
        $forecast = $this->simpleLinearForecast($scores, 30);

        return [
            'status' => 'success',
            'current_score' => end($scores),
            'forecast_days' => 30,
            'expected_range' => [
                'min' => min($forecast),
                'max' => max($forecast),
                'avg' => array_sum($forecast) / count($forecast),
            ],
            'trend' => $forecast[count($forecast) - 1] > $scores[count($scores) - 1] ? 'IMPROVING' : 'DECLINING',
            'trend_confidence' => round(($forecast[count($forecast) - 1] - end($scores)) / end($scores) * 100, 1),
            'predicted_breach_date' => $this->predictBreachDate($scores, $forecast, threshold: 0.75),
        ];
    }

    /**
     * Predict optimal timing for major deployments
     */
    public function predictOptimalDeploymentWindow(string $organizationId, int $daysAhead = 14): array
    {
        $history = $this->metricsService->getMetricsHistory(
            organizationId: $organizationId,
            days: 30,
            interval: 'daily'
        );

        $latencies = $history->pluck('avg_latency');
        $systemStress = $history->pluck('throughput_capacity_percent');

        $predictions = [];
        for ($i = 0; $i < $daysAhead; $i++) {
            $date = now()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            // Reduced activity on weekends
            $stressMultiplier = in_array($dayOfWeek, [0, 6]) ? 0.7 : 1.0;

            $predictions[$date->toDateString()] = [
                'predicted_stress_level' => round(($systemStress->avg() * $stressMultiplier) * 100, 1),
                'predicted_latency_ms' => round($latencies->avg() * $stressMultiplier, 0),
                'risk_score' => $this->calculateDeploymentRisk($stressMultiplier),
                'recommendation' => $stressMultiplier < 0.8 ? 'OPTIMAL' : 'RISKY',
            ];
        }

        // Find optimal window
        $optimalDays = collect($predictions)
            ->filter(fn($p) => $p['recommendation'] === 'OPTIMAL')
            ->keys()
            ->toArray();

        return [
            'status' => 'success',
            'predictions' => $predictions,
            'optimal_window' => $optimalDays,
            'next_optimal_date' => $optimalDays[0] ?? null,
            'risk_analysis' => $this->analyzeDeploymentRisks($organizationId),
        ];
    }

    /**
     * Predict resource needs based on trend
     */
    public function predictResourceNeeds(string $organizationId): array
    {
        $history = $this->metricsService->getMetricsHistory(
            organizationId: $organizationId,
            days: 90,
            interval: 'daily'
        );

        if ($history->count() < 14) {
            return ['status' => 'insufficient_data'];
        }

        $throughput = $history->pluck('throughput_capacity_percent')->toArray();
        $avgTimeToTransition = $history->pluck('avg_transition_time')->toArray();

        $throughputTrend = $this->calculateTrend($throughput);
        $transitionTrend = $this->calculateTrend($avgTimeToTransition);

        return [
            'status' => 'success',
            'throughput_trend' => [
                'direction' => $throughputTrend > 0 ? 'INCREASING' : 'DECREASING',
                'percent_change_per_week' => round($throughputTrend * 7, 1),
                'utilization_at_capacity' => end($throughput) > 85,
            ],
            'processing_time_trend' => [
                'direction' => $transitionTrend > 0 ? 'SLOWING' : 'IMPROVING',
                'percent_change_per_week' => round($transitionTrend * 7, 1),
            ],
            'recommendations' => $this->generateResourceRecommendations(
                $throughputTrend,
                $transitionTrend,
                end($throughput)
            ),
            'capacity_saturation_date' => $this->predictCapacitySaturation($throughput),
        ];
    }

    /**
     * Risk scoring for verification transitions
     */
    public function assessTransitionRisk(string $organizationId, int $roleId): array
    {
        $anomalies = $this->anomalyService->analyzeVerificationMetrics($organizationId);
        $recentEvents = EventStore::where('organization_id', $organizationId)
            ->whereJsonContains('data->role_id', $roleId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $riskFactors = [
            'system_health' => isset($anomalies['system_health']) ? 0.3 : 0.0,
            'recent_failures' => $recentEvents->where('event_name', 'transition.failed')->count() * 0.05,
            'latency_spike' => isset($anomalies['avg_latency']) ? 0.2 : 0.0,
            'compliance_drift' => isset($anomalies['compliance_score']) ? 0.15 : 0.0,
        ];

        $totalRisk = min(1.0, array_sum($riskFactors));

        return [
            'overall_risk_score' => round($totalRisk * 100, 1),
            'risk_level' => $totalRisk > 0.7 ? 'CRITICAL' : ($totalRisk > 0.4 ? 'HIGH' : 'MEDIUM'),
            'risk_factors' => $riskFactors,
            'recommendation' => $this->generateTransitionRecommendation($totalRisk),
            'suggested_actions' => $this->getSuggestedActions($totalRisk, $anomalies),
        ];
    }

    /**
     * Simple linear forecast (moving average + trend)
     */
    private function simpleLinearForecast(array $values, int $days): array
    {
        $n = count($values);
        if ($n < 2) {
            return array_fill(0, $days, end($values) ?? 0);
        }

        // Calculate trend
        $x = range(0, $n - 1);
        $sumX = array_sum($x);
        $sumY = array_sum($values);
        $sumXY = array_sum(array_map(fn($xi, $yi) => $xi * $yi, $x, $values));
        $sumX2 = array_sum(array_map(fn($xi) => $xi * $xi, $x));

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Forecast
        $forecast = [];
        for ($i = 0; $i < $days; $i++) {
            $predicted = $intercept + $slope * ($n + $i);
            $forecast[] = max(0, min(1, $predicted));  // Clamp to [0, 1]
        }

        return $forecast;
    }

    /**
     * Predict date when compliance falls below threshold
     */
    private function predictBreachDate(?array $scores, array $forecast, float $threshold): ?string
    {
        if (!$scores) {
            return null;
        }

        foreach ($forecast as $day => $score) {
            if ($score < $threshold) {
                return now()->addDays($day)->toDateString();
            }
        }

        return null;
    }

    /**
     * Calculate deployment risk score
     */
    private function calculateDeploymentRisk(float $stressMultiplier): int
    {
        if ($stressMultiplier > 0.9) {
            return 95;
        } elseif ($stressMultiplier > 0.7) {
            return 60;
        } elseif ($stressMultiplier > 0.5) {
            return 30;
        } else {
            return 10;
        }
    }

    /**
     * Analyze deployment risks
     */
    private function analyzeDeploymentRisks(string $organizationId): array
    {
        // Placeholder for comprehensive risk analysis
        return [
            'critical_risks' => [],
            'warnings' => [],
            'overall_go_nogo' => 'CONDITIONAL',
        ];
    }

    /**
     * Calculate simple trend (slope)
     */
    private function calculateTrend(array $values): float
    {
        if (count($values) < 2) {
            return 0;
        }

        $n = count($values);
        $recent = array_slice($values, -5);
        $older = array_slice($values, -10, 5);

        return (array_sum($recent) / count($recent)) - (array_sum($older) / count($older));
    }

    /**
     * Generate resource recommendations
     */
    private function generateResourceRecommendations(float $throughputTrend, float $transitionTrend, float $currentUtilization): array
    {
        $recommendations = [];

        if ($throughputTrend > 0.05 && $currentUtilization > 75) {
            $recommendations[] = 'Consider scaling infrastructure to handle increasing load';
        }

        if ($transitionTrend > 0.02) {
            $recommendations[] = 'Investigate performance degradation in transition pipeline';
        }

        return $recommendations;
    }

    /**
     * Predict when capacity reaches saturation
     */
    private function predictCapacitySaturation(array $throughput): ?string
    {
        if (end($throughput) > 95) {
            return now()->toDateString();  // Already saturated
        }

        $trend = $this->calculateTrend($throughput);
        if ($trend <= 0) {
            return null;  // No increasing trend
        }

        $daysToSaturation = (100 - end($throughput)) / ($trend * 100);
        if ($daysToSaturation < 0) {
            return null;
        }

        return now()->addDays(intval($daysToSaturation))->toDateString();
    }

    /**
     * Generate transition recommendation
     */
    private function generateTransitionRecommendation(float $riskScore): string
    {
        if ($riskScore > 0.7) {
            return 'DEFER transition until system health improves';
        } elseif ($riskScore > 0.4) {
            return 'PROCEED with caution, implement monitoring';
        } else {
            return 'PROCEED, system health is good';
        }
    }

    /**
     * Get suggested actions based on risk
     */
    private function getSuggestedActions(float $riskScore, array $anomalies): array
    {
        $actions = [];

        if ($riskScore > 0.6) {
            $actions[] = 'Monitor system logs in real-time';
            $actions[] = 'Have rollback plan ready';
            $actions[] = 'Notify stakeholders of elevated risk';
        }

        if (isset($anomalies['system_health'])) {
            $actions[] = 'Check system resources (CPU, memory, disk)';
        }

        if (isset($anomalies['avg_latency'])) {
            $actions[] = 'Investigate latency spike (database, network)';
        }

        return $actions;
    }
}

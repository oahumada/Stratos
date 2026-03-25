<?php

namespace App\Services\Analytics;

use App\Models\VerificationAudit;
use Illuminate\Support\Collection;

/**
 * AnomalyDetectionService
 *
 * Detects anomalies in:
 * - Verification metrics (compliance scores, latency, transition times)
 * - Talent trends (skill distribution, role vacancy, attrition signals)
 * - Real-time performance (system health, throughput degradation)
 *
 * Uses statistical methods (Z-score, IQR, trend deviation) to flag anomalies.
 */
class AnomalyDetectionService
{
    public function __construct(
        private MetricsAggregationService $metricsService
    ) {}

    /**
     * Analyze verification metrics for anomalies
     */
    public function analyzeVerificationMetrics(string $organizationId): array
    {
        $history = $this->metricsService->getMetricsHistory(
            organizationId: $organizationId,
            days: 30,
            interval: 'daily'
        );

        $anomalies = [
            'compliance_score' => $this->detectTrendDeviation(
                $history->pluck('compliance_score'),
                'compliance_score',
                threshold: 0.15  // 15% deviation
            ),
            'avg_latency' => $this->detectSpikeAnomaly(
                $history->pluck('avg_latency'),
                'avg_latency',
                threshold: 2.5   // 2.5 sigma
            ),
            'transition_time' => $this->detectTrendDeviation(
                $history->pluck('avg_transition_time'),
                'avg_transition_time',
                threshold: 0.20
            ),
            'system_health' => $this->detectHealthDegradation($organizationId),
        ];

        return array_filter($anomalies);
    }

    /**
     * Analyze talent distribution for risks
     */
    public function analyzeTalentAnomalies(string $organizationId): array
    {
        $currentYear = now()->year;
        $hasData = VerificationAudit::where('organization_id', $organizationId)->exists();

        if (! $hasData) {
            return [];
        }

        return [
            'skill_gaps' => $this->detectCriticalGaps($organizationId),
            'role_vacancies' => $this->detectVacancyRisk($organizationId),
            'attrition_signals' => $this->detectAttritionRisk($organizationId),
            'competency_skew' => $this->detectCompetencySkew($organizationId),
        ];
    }

    /**
     * Z-score based spike detection
     */
    private function detectSpikeAnomaly(Collection $values, string $metric, float $threshold): array
    {
        if ($values->count() < 3) {
            return [];
        }

        $mean = $values->avg();
        $stdDev = $this->calculateStdDev($values);

        if ($stdDev === 0) {
            return [];
        }

        $anomalies = [];
        $values->each(function ($value, $index) use ($mean, $stdDev, $threshold, &$anomalies, $metric) {
            $zScore = abs(($value - $mean) / $stdDev);
            if ($zScore > $threshold) {
                $anomalies[] = [
                    'type' => 'SPIKE',
                    'metric' => $metric,
                    'value' => $value,
                    'expected_range' => [
                        'min' => $mean - ($stdDev * $threshold),
                        'max' => $mean + ($stdDev * $threshold),
                    ],
                    'severity' => $zScore > 4 ? 'CRITICAL' : ($zScore > 3 ? 'HIGH' : 'MEDIUM'),
                    'z_score' => round($zScore, 2),
                ];
            }
        });

        return $anomalies;
    }

    /**
     * Trend deviation detection (gradual changes)
     */
    private function detectTrendDeviation(Collection $values, string $metric, float $threshold): array
    {
        if ($values->count() < 7) {
            return [];
        }

        $recent = $values->slice(-7);
        $older = $values->slice(-14, 7);

        $recentAvg = $recent->avg();
        $olderAvg = $older->avg();

        $deviation = abs($recentAvg - $olderAvg) / $olderAvg;

        if ($deviation > $threshold) {
            return [[
                'type' => 'TREND_DEVIATION',
                'metric' => $metric,
                'old_average' => round($olderAvg, 2),
                'new_average' => round($recentAvg, 2),
                'deviation_percent' => round($deviation * 100, 1),
                'severity' => $deviation > 0.35 ? 'CRITICAL' : 'HIGH',
                'direction' => $recentAvg > $olderAvg ? 'INCREASING' : 'DECREASING',
            ]];
        }

        return [];
    }

    /**
     * System health degradation detection
     */
    private function detectHealthDegradation(string $organizationId): array
    {
        $recentAudits = VerificationAudit::where('organization_id', $organizationId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        if ($recentAudits->count() < 5) {
            return [];
        }

        $statusTrend = $recentAudits->pluck('status')->toArray();
        $failureRate = count(array_filter($statusTrend, fn ($s) => $s === 'failed')) / count($statusTrend);

        $anomalies = [];

        if ($failureRate > 0.3) {  // 30% failure rate
            $anomalies[] = [
                'type' => 'HEALTH_DEGRADATION',
                'metric' => 'verification_failure_rate',
                'failure_rate' => round($failureRate * 100, 1),
                'severity' => $failureRate > 0.5 ? 'CRITICAL' : 'HIGH',
                'recent_failures' => $recentAudits->whereIn('status', ['failed', 'error'])->count(),
                'recommendation' => 'Review recent deployment or check system resources',
            ];
        }

        return $anomalies;
    }

    /**
     * Detect critical skill gaps
     */
    private function detectCriticalGaps(string $organizationId): array
    {
        // Placeholder: integrate with Gap Analysis module
        return [];
    }

    /**
     * Detect role vacancy risks
     */
    private function detectVacancyRisk(string $organizationId): array
    {
        // Placeholder: analyze open roles vs filled
        return [];
    }

    /**
     * Detect attrition signals
     */
    private function detectAttritionRisk(string $organizationId): array
    {
        // Placeholder: analyze role transitions, completion rates
        return [];
    }

    /**
     * Detect competency concentration (single point of failure)
     */
    private function detectCompetencySkew(string $organizationId): array
    {
        // Placeholder: identify critical competencies held by few people
        return [];
    }

    /**
     * Helper: calculate standard deviation
     */
    private function calculateStdDev(Collection $values): float
    {
        if ($values->count() < 2) {
            return 0;
        }

        $mean = $values->avg();
        $variance = $values->map(fn ($v) => pow($v - $mean, 2))->avg();

        return sqrt($variance);
    }
}

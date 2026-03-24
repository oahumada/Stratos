<?php

namespace App\Services\Analytics;

use App\Models\VerificationAudit;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * MetricsAggregationService
 *
 * Central service for aggregating real-time metrics from VerificationAudit
 * and computing derived metrics for dashboards, alerts, and analytics.
 *
 * Caches aggregated metrics to avoid repeated calculations.
 */
class MetricsAggregationService
{
    /**
     * Get aggregated metrics for a time period
     */
    public function getMetricsHistory(
        string $organizationId,
        int $days = 30,
        string $interval = 'daily'
    ): Collection {
        $startDate = now()->subDays($days);

        $audits = VerificationAudit::where('organization_id', $organizationId)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();

        if ($audits->isEmpty()) {
            return collect();
        }

        return $this->aggregateByInterval($audits, $interval);
    }

    /**
     * Aggregate metrics by time interval
     */
    private function aggregateByInterval(Collection $audits, string $interval): Collection
    {
        $grouped = [];

        foreach ($audits as $audit) {
            $key = $this->getIntervalKey($audit->created_at, $interval);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }

            $grouped[$key][] = $audit;
        }

        return collect($grouped)->map(function ($auditGroup) {
            return $this->calculateMetricsForGroup(collect($auditGroup));
        })->values();
    }

    /**
     * Calculate metrics for a group of audits
     */
    private function calculateMetricsForGroup(Collection $audits): array
    {
        $completedAudits = $audits->where('status', 'completed');
        $totalCount = $audits->count();
        $successCount = $completedAudits->count();

        // Extract JSON data safely
        $latencies = $audits->map(function ($audit) {
            $data = is_array($audit->data) ? $audit->data : json_decode($audit->data, true) ?? [];
            return $data['latency_ms'] ?? 0;
        })->filter();

        $throughputData = $audits->map(function ($audit) {
            $data = is_array($audit->data) ? $audit->data : json_decode($audit->data, true) ?? [];
            return $data['throughput'] ?? 0;
        })->filter();

        $transitionTimes = $audits->map(function ($audit) {
            $data = is_array($audit->data) ? $audit->data : json_decode($audit->data, true) ?? [];
            return $data['transition_time_seconds'] ?? 0;
        })->filter();

        return [
            'timestamp' => $audits->first()?->created_at?->toDateString() ?? now()->toDateString(),
            'compliance_score' => $this->calculateComplianceScore($audits),
            'success_rate' => $totalCount > 0 ? $successCount / $totalCount : 0,
            'total_attempts' => $totalCount,
            'successful_transitions' => $successCount,
            'failed_transitions' => $totalCount - $successCount,
            'avg_latency' => $latencies->isNotEmpty() ? $latencies->avg() : 0,
            'min_latency' => $latencies->isNotEmpty() ? $latencies->min() : 0,
            'max_latency' => $latencies->isNotEmpty() ? $latencies->max() : 0,
            'avg_throughput' => $throughputData->isNotEmpty() ? $throughputData->avg() : 0,
            'throughput_capacity_percent' => $throughputData->isNotEmpty() ? min(100, $throughputData->avg()) : 0,
            'avg_transition_time' => $transitionTimes->isNotEmpty() ? $transitionTimes->avg() : 0,
            'p95_latency' => $this->calculatePercentile($latencies, 0.95),
            'p99_latency' => $this->calculatePercentile($latencies, 0.99),
        ];
    }

    /**
     * Calculate compliance score from audits
     */
    private function calculateComplianceScore(Collection $audits): float
    {
        $completedCount = $audits->where('status', 'completed')->count();
        $totalCount = $audits->count();

        if ($totalCount === 0) {
            return 0.0;
        }

        $baseScore = $completedCount / $totalCount;

        // Deduct for errors
        $errorCount = $audits->where('status', 'error')->count();
        $errorPenalty = ($errorCount * 0.05);

        // Deduct for warnings
        $warningCount = $audits->where('status', 'warning')->count();
        $warningPenalty = ($warningCount * 0.02);

        return max(0, min(1, $baseScore - $errorPenalty - $warningPenalty));
    }

    /**
     * Calculate percentile value
     */
    private function calculatePercentile(Collection $values, float $percentile): float
    {
        if ($values->isEmpty()) {
            return 0;
        }

        $sorted = $values->sort()->values()->toArray();
        $index = round(($percentile * count($sorted)) - 1);
        $index = max(0, min(count($sorted) - 1, $index));

        return $sorted[$index];
    }

    /**
     * Get interval key for grouping
     */
    private function getIntervalKey(Carbon $date, string $interval): string
    {
        return match ($interval) {
            'hourly' => $date->format('Y-m-d H:00'),
            'daily' => $date->format('Y-m-d'),
            'weekly' => $date->format('Y-W'),
            'monthly' => $date->format('Y-m'),
            default => $date->format('Y-m-d'),
        };
    }

    /**
     * Get current system metrics snapshot
     */
    public function getCurrentMetrics(string $organizationId): array
    {
        $recentAudits = VerificationAudit::where('organization_id', $organizationId)
            ->orderByDesc('created_at')
            ->limit(100)  // Last 100 audits
            ->get();

        if ($recentAudits->isEmpty()) {
            return [];
        }

        return $this->calculateMetricsForGroup($recentAudits);
    }

    /**
     * Get metrics comparison (current vs previous period)
     */
    public function getMetricsComparison(string $organizationId, int $days = 7): array
    {
        $currentPeriod = $this->getMetricsHistory(
            $organizationId,
            $days,
            'daily'
        );

        $previousPeriod = VerificationAudit::where('organization_id', $organizationId)
            ->where('created_at', '<', now()->subDays($days))
            ->where('created_at', '>=', now()->subDays($days * 2))
            ->get();

        $currentMetrics = $currentPeriod->isNotEmpty()
            ? $currentPeriod->last()
            : [];

        $previousMetrics = !$previousPeriod->isEmpty()
            ? $this->calculateMetricsForGroup($previousPeriod)
            : [];

        return [
            'current' => $currentMetrics,
            'previous' => $previousMetrics,
            'changes' => [
                'compliance_score_change' => ($currentMetrics['compliance_score'] ?? 0) - ($previousMetrics['compliance_score'] ?? 0),
                'success_rate_change' => ($currentMetrics['success_rate'] ?? 0) - ($previousMetrics['success_rate'] ?? 0),
                'avg_latency_change' => ($currentMetrics['avg_latency'] ?? 0) - ($previousMetrics['avg_latency'] ?? 0),
            ],
        ];
    }

    /**
     * Get percentile latencies over time
     */
    public function getLatencyPercentiles(string $organizationId, int $days = 30): array
    {
        $history = $this->getMetricsHistory($organizationId, $days, 'daily');

        return [
            'p50' => $history->map(fn($m) => $m['avg_latency'])->median() ?? 0,
            'p95' => $history->map(fn($m) => $m['p95_latency'])->max() ?? 0,
            'p99' => $history->map(fn($m) => $m['p99_latency'])->max() ?? 0,
            'max' => $history->map(fn($m) => $m['max_latency'])->max() ?? 0,
        ];
    }
}

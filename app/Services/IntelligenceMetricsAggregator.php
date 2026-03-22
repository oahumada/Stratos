<?php

namespace App\Services;

use App\Models\IntelligenceMetric;
use App\Models\IntelligenceMetricAggregate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IntelligenceMetricsAggregator
{
    /**
     * Aggregate metrics for a given date and organization.
     * Computes success_rate, percentiles (p50, p95, p99), averages.
     */
    public function aggregateMetricsForDate(
        ?int $organizationId = null,
        ?Carbon $date = null
    ): array {
        $date = $date ?? now()->subDay();
        $startOfDay = $date->clone()->startOfDay();
        $endOfDay = $date->clone()->endOfDay();

        $results = [];

        // Group by metric_type and source_type
        $query = IntelligenceMetric::whereBetween('created_at', [$startOfDay, $endOfDay]);

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $metrics = $query->get()
            ->groupBy(function ($metric) {
                return $metric->metric_type.'|'.$metric->source_type;
            });

        foreach ($metrics as $groupKey => $group) {
            [$metricType, $sourceType] = array_pad(
                explode('|', $groupKey),
                2,
                null
            );

            $aggregate = $this->computeAggregate($group, $metricType, $sourceType, $organizationId, $date);
            $results[] = $aggregate;
        }

        return $results;
    }

    /**
     * Compute aggregate statistics from a collection of metrics
     */
    protected function computeAggregate(
        \Illuminate\Support\Collection $metrics,
        string $metricType,
        ?string $sourceType,
        ?int $organizationId,
        Carbon $date
    ): array {
        $durations = $metrics->pluck('duration_ms')->sort()->values()->toArray();
        $confidences = $metrics->pluck('confidence')->toArray();
        $contextCounts = $metrics->pluck('context_count')->toArray();
        $successCount = $metrics->where('success', true)->count();
        $totalCount = $metrics->count();

        // Calculate percentiles
        $p50 = $this->calculatePercentile($durations, 50);
        $p95 = $this->calculatePercentile($durations, 95);
        $p99 = $this->calculatePercentile($durations, 99);

        // Calculate averages
        $avgDuration = (int) round(array_sum($durations) / max(count($durations), 1));
        $avgConfidence = count($confidences) > 0 ? round(array_sum($confidences) / count($confidences), 4) : 0;
        $avgContextCount = (int) round(array_sum($contextCounts) / max(count($contextCounts), 1));
        $successRate = $totalCount > 0 ? round($successCount / $totalCount, 4) : 0;

        return [
            'organization_id' => $organizationId,
            'metric_type' => $metricType,
            'source_type' => $sourceType,
            'date_key' => $date->toDateString(),
            'total_count' => $totalCount,
            'success_count' => $successCount,
            'success_rate' => $successRate,
            'avg_duration_ms' => $avgDuration,
            'p50_duration_ms' => $p50,
            'p95_duration_ms' => $p95,
            'p99_duration_ms' => $p99,
            'avg_confidence' => $avgConfidence,
            'avg_context_count' => $avgContextCount,
            'metadata' => null,
        ];
    }

    /**
     * Calculate a percentile from a sorted array of values
     */
    protected function calculatePercentile(array $values, int $percentile): int
    {
        if (empty($values)) {
            return 0;
        }

        $count = count($values);
        $position = ceil($count * $percentile / 100) - 1;
        $position = max(0, min($position, $count - 1));

        return (int) $values[$position];
    }

    /**
     * Store aggregates to database, upserting by unique constraint
     */
    public function storeAggregates(array $aggregates): void
    {
        foreach ($aggregates as $aggregate) {
            try {
                IntelligenceMetricAggregate::updateOrCreate(
                    [
                        'organization_id' => $aggregate['organization_id'],
                        'metric_type' => $aggregate['metric_type'],
                        'source_type' => $aggregate['source_type'],
                        'date_key' => $aggregate['date_key'],
                    ],
                    $aggregate
                );
            } catch (\Exception $e) {
                Log::error('Failed to store metric aggregate', [
                    'error' => $e->getMessage(),
                    'aggregate' => $aggregate,
                ]);
            }
        }
    }

    /**
     * Aggregate all metrics for a given date (across all orgs)
     * Useful for running daily aggregation job
     */
    public function aggregateAllMetricsForDate(?Carbon $date = null): void
    {
        $date = $date ?? now()->subDay();

        // Get all organizations that have metrics for this date
        $orgIds = IntelligenceMetric::whereBetween('created_at', [
            $date->clone()->startOfDay(),
            $date->clone()->endOfDay(),
        ])
            ->distinct('organization_id')
            ->pluck('organization_id')
            ->filter()
            ->toArray();

        // Add null for global metrics
        $orgIds[] = null;

        foreach ($orgIds as $orgId) {
            $aggregates = $this->aggregateMetricsForDate($orgId, $date);
            $this->storeAggregates($aggregates);
        }

        Log::info('Aggregated metrics for date', [
            'date' => $date->toDateString(),
            'organizations_processed' => count($orgIds),
        ]);
    }
}

<?php

namespace App\Services\Intelligence;

use App\Models\IntelligenceMetric;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SlaMonitorService
{
    /** P95 latency threshold in seconds */
    public const SLA_LATENCY_P95 = 2.0;

    /** Minimum success rate (95%) */
    public const SLA_SUCCESS_RATE = 0.95;

    /** Maximum hallucination rate (5%) */
    public const SLA_HALLUCINATION_RATE = 0.05;

    /**
     * Check SLA compliance for an organization over the given period.
     *
     * @return array{compliant: bool, metrics: array, violations: array}
     */
    public function checkSla(int $organizationId, int $days = 1): array
    {
        $since = Carbon::now()->subDays($days);

        $metrics = IntelligenceMetric::where('organization_id', $organizationId)
            ->where('created_at', '>=', $since)
            ->get();

        if ($metrics->isEmpty()) {
            return [
                'compliant' => true,
                'metrics' => $this->emptyMetrics(),
                'violations' => [],
            ];
        }

        $totalCalls = $metrics->count();
        $successCount = $metrics->where('success', true)->count();
        $successRate = $totalCalls > 0 ? $successCount / $totalCalls : 1.0;

        // P95 latency (duration_ms → seconds)
        $durations = $metrics->pluck('duration_ms')->sort()->values();
        $p95Index = (int) ceil(0.95 * $durations->count()) - 1;
        $p95Latency = ($durations[$p95Index] ?? 0) / 1000.0;

        // Hallucination rate from metadata
        $hallucinationCount = $metrics->filter(function ($m) {
            $meta = $m->metadata ?? [];

            return ! empty($meta['hallucination_detected']) || ($m->metric_type === 'incident' && ($meta['incident_type'] ?? '') === 'hallucination_detected');
        })->count();
        $hallucinationRate = $totalCalls > 0 ? $hallucinationCount / $totalCalls : 0.0;

        $violations = [];

        if ($p95Latency > self::SLA_LATENCY_P95) {
            $violations[] = [
                'metric' => 'latency_p95',
                'threshold' => self::SLA_LATENCY_P95,
                'actual' => round($p95Latency, 3),
            ];
        }

        if ($successRate < self::SLA_SUCCESS_RATE) {
            $violations[] = [
                'metric' => 'success_rate',
                'threshold' => self::SLA_SUCCESS_RATE,
                'actual' => round($successRate, 4),
            ];
        }

        if ($hallucinationRate > self::SLA_HALLUCINATION_RATE) {
            $violations[] = [
                'metric' => 'hallucination_rate',
                'threshold' => self::SLA_HALLUCINATION_RATE,
                'actual' => round($hallucinationRate, 4),
            ];
        }

        return [
            'compliant' => empty($violations),
            'metrics' => [
                'total_calls' => $totalCalls,
                'success_rate' => round($successRate, 4),
                'latency_p95_seconds' => round($p95Latency, 3),
                'hallucination_rate' => round($hallucinationRate, 4),
                'period_days' => $days,
            ],
            'violations' => $violations,
        ];
    }

    /**
     * Alert on SLA violations via the agents log channel.
     */
    public function alertOnViolation(array $slaResult, int $organizationId): void
    {
        if ($slaResult['compliant']) {
            Log::channel('agents')->info('SLA compliant', [
                'organization_id' => $organizationId,
                'metrics' => $slaResult['metrics'],
            ]);

            return;
        }

        Log::channel('agents')->critical('SLA violation detected', [
            'organization_id' => $organizationId,
            'violations' => $slaResult['violations'],
            'metrics' => $slaResult['metrics'],
        ]);
    }

    private function emptyMetrics(): array
    {
        return [
            'total_calls' => 0,
            'success_rate' => 1.0,
            'latency_p95_seconds' => 0.0,
            'hallucination_rate' => 0.0,
            'period_days' => 0,
        ];
    }
}

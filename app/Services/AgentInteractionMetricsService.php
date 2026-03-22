<?php

namespace App\Services;

use App\Models\AgentInteraction;
use Illuminate\Support\Facades\Cache;

/**
 * AgentInteractionMetricsService
 *
 * Provides analytics and insights on agent ↔ LLM interactions.
 * Tracks latency, token usage, error rates, and performance trends.
 */
class AgentInteractionMetricsService
{
    private const METRICS_CACHE_TTL = 3600; // 1 hour

    /**
     * Get comprehensive metrics for an organization.
     */
    public function getOrganizationMetrics(int $organizationId, ?\DateTimeInterface $since = null): array
    {
        $cacheKey = "agent_metrics:{$organizationId}";
        if ($since) {
            $cacheKey .= ':'.$since->format('Y-m-d');
        }

        return Cache::remember($cacheKey, self::METRICS_CACHE_TTL, function () use ($organizationId, $since) {
            $query = AgentInteraction::where('organization_id', $organizationId);

            if ($since) {
                $query->where('created_at', '>=', $since);
            } else {
                $query->where('created_at', '>=', now()->subDays(30));
            }

            $interactions = $query->get();

            if ($interactions->isEmpty()) {
                return [
                    'summary' => [
                        'total_interactions' => 0,
                        'total_succeeded' => 0,
                        'total_failed' => 0,
                        'success_rate' => 0.0,
                        'avg_latency_ms' => 0,
                        'total_tokens' => 0,
                        'avg_tokens' => 0,
                    ],
                    'by_agent' => [],
                    'by_provider' => [],
                    'by_status' => [],
                    'daily_trend' => [],
                    'error_distribution' => [],
                    'latency_percentiles' => [],
                ];
            }

            $succeeded = $interactions->where('status', 'success')->count();
            $failed = $interactions->where('status', 'error')->count();

            $latencies = $interactions->where('status', 'success')
                ->pluck('latency_ms')
                ->filter()
                ->values();

            $tokens = $interactions->pluck('token_count')->filter()->values();

            return [
                'summary' => [
                    'total_interactions' => $interactions->count(),
                    'total_succeeded' => $succeeded,
                    'total_failed' => $failed,
                    'success_rate' => $succeeded > 0 ? ($succeeded / $interactions->count()) : 0.0,
                    'avg_latency_ms' => $latencies->isNotEmpty() ? round($latencies->avg(), 2) : 0,
                    'total_tokens' => $tokens->sum(),
                    'avg_tokens' => $tokens->isNotEmpty() ? round($tokens->avg(), 0) : 0,
                ],
                'by_agent' => $this->getMetricsByAgent($interactions),
                'by_provider' => $this->getMetricsByProvider($interactions),
                'by_status' => $this->getMetricsByStatus($interactions),
                'daily_trend' => $this->getDailyTrend($organizationId, $since),
                'error_distribution' => $this->getErrorDistribution($interactions),
                'latency_percentiles' => $this->getLatencyPercentiles($latencies),
            ];
        });
    }

    /**
     * Get metrics breakdown by agent.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $interactions
     */
    private function getMetricsByAgent($interactions): array
    {
        $grouped = $interactions->groupBy('agent_name');

        return $grouped
            ->map(function ($group) {
                $succeeded = $group->where('status', 'success')->count();
                $total = $group->count();
                $latencies = $group->where('status', 'success')->pluck('latency_ms');

                return [
                    'agent_name' => $group->first()->agent_name,
                    'call_count' => $total,
                    'success_count' => $succeeded,
                    'error_count' => $total - $succeeded,
                    'success_rate' => $total > 0 ? ($succeeded / $total) : 0.0,
                    'avg_latency_ms' => $latencies->isNotEmpty() ? round($latencies->avg(), 2) : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get metrics breakdown by provider.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $interactions
     */
    private function getMetricsByProvider($interactions): array
    {
        $grouped = $interactions->groupBy('provider');

        return $grouped
            ->map(function ($group) {
                $succeeded = $group->where('status', 'success')->count();
                $total = $group->count();

                return [
                    'provider' => $group->first()->provider ?? 'unknown',
                    'call_count' => $total,
                    'success_count' => $succeeded,
                    'success_rate' => $total > 0 ? ($succeeded / $total) : 0.0,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get metrics breakdown by status.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $interactions
     */
    private function getMetricsByStatus($interactions): array
    {
        return [
            'success' => $interactions->where('status', 'success')->count(),
            'error' => $interactions->where('status', 'error')->count(),
        ];
    }

    /**
     * Get daily trend for specified period.
     */
    public function getDailyTrend(int $organizationId, ?\DateTimeInterface $since = null): array
    {
        $query = AgentInteraction::where('organization_id', $organizationId);

        if ($since) {
            $query->where('created_at', '>=', $since);
        } else {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $interactions = $query->get();
        $grouped = $interactions->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        $trend = [];
        $startDate = $since ? $since->format('Y-m-d') : now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $current = \DateTime::createFromFormat('Y-m-d', $startDate);
        while ($current->format('Y-m-d') <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $dayInteractions = $grouped->get($dateStr, collect());

            $trend[] = [
                'date' => $dateStr,
                'total_calls' => $dayInteractions->count(),
                'success' => $dayInteractions->where('status', 'success')->count(),
                'errors' => $dayInteractions->where('status', 'error')->count(),
            ];

            $current->modify('+1 day');
        }

        return $trend;
    }

    /**
     * Get error distribution.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $interactions
     */
    private function getErrorDistribution($interactions): array
    {
        $errors = $interactions
            ->where('status', 'error')
            ->groupBy('error_message')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(10);

        return $errors->map(function ($count, $message) {
            return [
                'error' => $message ?? 'Unknown',
                'count' => $count,
            ];
        })->values()->toArray();
    }

    /**
     * Get latency percentiles (P50, P95, P99).
     *
     * @param  \Illuminate\Support\Collection  $latencies
     */
    private function getLatencyPercentiles($latencies): array
    {
        if ($latencies->isEmpty()) {
            return ['p50' => 0, 'p95' => 0, 'p99' => 0];
        }

        $sorted = $latencies->sort()->values();

        return [
            'p50' => round($sorted->get(intval(count($sorted) * 0.5)) ?? 0, 2),
            'p95' => round($sorted->get(intval(count($sorted) * 0.95)) ?? 0, 2),
            'p99' => round($sorted->get(intval(count($sorted) * 0.99)) ?? 0, 2),
        ];
    }

    /**
     * Get top failing agents.
     */
    public function getTopFailingAgents(int $organizationId, ?\DateTimeInterface $since = null, int $limit = 10): array
    {
        $query = AgentInteraction::where('organization_id', $organizationId)
            ->where('status', 'error');

        if ($since) {
            $query->where('created_at', '>=', $since);
        } else {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $grouped = $query->get()->groupBy('agent_name');

        return $grouped
            ->map(fn ($group) => [
                'agent_name' => $group->first()->agent_name,
                'error_count' => $group->count(),
            ])
            ->sortByDesc(fn ($item) => $item['error_count'])
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get average latency by agent.
     */
    public function getAverageLatencyByAgent(int $organizationId, ?\DateTimeInterface $since = null): array
    {
        $query = AgentInteraction::where('organization_id', $organizationId)
            ->where('status', 'success');

        if ($since) {
            $query->where('created_at', '>=', $since);
        } else {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $grouped = $query->get()->groupBy('agent_name');

        return $grouped
            ->map(function ($group) {
                $latencies = $group->pluck('latency_ms')->filter();

                return [
                    'agent_name' => $group->first()->agent_name,
                    'avg_latency_ms' => $latencies->isNotEmpty() ? round($latencies->avg(), 2) : 0,
                    'median_latency_ms' => $this->getMedian($latencies),
                    'max_latency_ms' => $latencies->isNotEmpty() ? $latencies->max() : 0,
                ];
            })
            ->sortByDesc(fn ($item) => $item['avg_latency_ms'])
            ->values()
            ->toArray();
    }

    /**
     * Calculate median of a collection.
     *
     * @param  \Illuminate\Support\Collection  $values
     */
    private function getMedian($values): float
    {
        $sorted = $values->sort()->values();
        $count = count($sorted);

        if ($count === 0) {
            return 0;
        }

        if ($count % 2 === 0) {
            return round(($sorted[$count / 2 - 1] + $sorted[$count / 2]) / 2, 2);
        }

        return round($sorted[intval($count / 2)], 2);
    }

    /**
     * Invalidate metrics cache for organization.
     */
    public function invalidateMetricsCache(int $organizationId): void
    {
        Cache::forget("agent_metrics:{$organizationId}");
    }
}

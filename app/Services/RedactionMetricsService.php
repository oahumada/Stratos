<?php

namespace App\Services;

use App\Models\RedactionAuditTrail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RedactionMetricsService
{
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Get comprehensive redaction metrics for organization
     */
    public function getOrganizationMetrics(int $organizationId, ?Carbon $since = null): array
    {
        $since = $since ?? now()->subDays(30);

        $cacheKey = "redaction_metrics:{$organizationId}:{$since->format('Y-m-d')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($organizationId, $since) {
            $query = RedactionAuditTrail::where('organization_id', $organizationId)
                ->where('created_at', '>=', $since);

            $totalEvents = $query->count();
            $totalRedactions = $query->sum('count');
            $avgPerEvent = $totalEvents > 0 ? round($totalRedactions / $totalEvents, 2) : 0;

            return [
                'organization_id' => $organizationId,
                'period' => [
                    'since' => $since->toIso8601String(),
                    'until' => now()->toIso8601String(),
                ],
                'summary' => [
                    'total_events' => $totalEvents,
                    'total_redactions' => $totalRedactions,
                    'average_per_event' => $avgPerEvent,
                    'unique_hashes' => $query->pluck('original_hash')->unique()->count(),
                ],
                'by_type' => $this->getRedactionsByType($organizationId, $since),
                'by_context' => $this->getRedactionsByContext($organizationId, $since),
                'daily_trend' => $this->getDailyTrend($organizationId, $since),
                'top_users' => $this->getTopRedactingUsers($organizationId, $since),
            ];
        });
    }

    /**
     * Get breakdown by redaction type
     */
    public function getRedactionsByType(int $organizationId, ?Carbon $since = null): array
    {
        $since = $since ?? now()->subDays(30);

        $records = RedactionAuditTrail::where('organization_id', $organizationId)
            ->where('created_at', '>=', $since)
            ->select(['redaction_types', 'count'])
            ->get();

        $breakdown = [];

        foreach ($records as $record) {
            $types = is_array($record->redaction_types)
                ? $record->redaction_types
                : json_decode($record->redaction_types ?? '[]', true);

            foreach ($types as $type) {
                if (! isset($breakdown[$type])) {
                    $breakdown[$type] = ['events' => 0, 'redactions' => 0];
                }
                $breakdown[$type]['events']++;
                $breakdown[$type]['redactions'] += $record->count;
            }
        }

        // Sort by redactions descending
        uasort($breakdown, fn ($a, $b) => $b['redactions'] <=> $a['redactions']);

        return $breakdown;
    }

    /**
     * Get breakdown by context (endpoint/path)
     */
    public function getRedactionsByContext(int $organizationId, ?Carbon $since = null): array
    {
        $since = $since ?? now()->subDays(30);

        return RedactionAuditTrail::where('organization_id', $organizationId)
            ->where('created_at', '>=', $since)
            ->select(['context'])
            ->selectRaw('COUNT(*) as events')
            ->selectRaw('SUM(count) as redactions')
            ->groupBy('context')
            ->orderByRaw('redactions DESC')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'context' => $row->context,
                'events' => $row->events,
                'redactions' => $row->redactions,
            ])
            ->toArray();
    }

    /**
     * Get daily trend for visualization
     */
    public function getDailyTrend(int $organizationId, ?Carbon $since = null, int $days = 30): array
    {
        $since = $since ?? now()->subDays($days);

        $records = RedactionAuditTrail::where('organization_id', $organizationId)
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as events')
            ->selectRaw('SUM(count) as redactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with zeros
        $trend = [];
        $currentDate = $since->copy()->startOfDay();
        $endDate = now()->startOfDay();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $trend[$dateStr] = ['events' => 0, 'redactions' => 0];
            $currentDate->addDay();
        }

        // Merge actual data
        foreach ($records as $record) {
            $trend[$record->date] = [
                'events' => $record->events,
                'redactions' => $record->redactions,
            ];
        }

        return $trend;
    }

    /**
     * Get top users triggering redactions
     */
    public function getTopRedactingUsers(int $organizationId, ?Carbon $since = null, int $limit = 10): array
    {
        $since = $since ?? now()->subDays(30);

        return RedactionAuditTrail::where('organization_id', $organizationId)
            ->where('created_at', '>=', $since)
            ->whereNotNull('user_id')
            ->select(['user_id'])
            ->selectRaw('COUNT(*) as events')
            ->selectRaw('SUM(count) as redactions')
            ->groupBy('user_id')
            ->orderByRaw('redactions DESC')
            ->limit($limit)
            ->with('user:id,name,email')
            ->get()
            ->map(fn ($record) => [
                'user_id' => $record->user_id,
                'user_name' => $record->user?->name ?? 'Unknown',
                'user_email' => $record->user?->email ?? 'Unknown',
                'events' => $record->events,
                'redactions' => $record->redactions,
            ])
            ->toArray();
    }

    /**
     * Check if text contains unredacted PII (warning indicator)
     */
    public function checkTextForPii(string $text, ?array $types = null): array
    {
        $detected = RedactionService::detectPii($text, $types);

        return [
            'contains_pii' => count($detected) > 0,
            'types_found' => array_keys($detected),
            'count' => array_reduce($detected, fn ($carry, $matches) => $carry + count($matches), 0),
            'details' => $detected,
        ];
    }

    /**
     * Invalidate cache for organization
     */
    public function invalidateMetricsCache(int $organizationId): void
    {
        Cache::forget("redaction_metrics:{$organizationId}:*");
    }

    /**
     * Get health score for redaction coverage
     * Returns 0-1 score indicating how well redaction is being applied
     */
    public function getRedactionCoverageScore(int $organizationId, ?Carbon $since = null): float
    {
        $since = $since ?? now()->subDays(7);

        $stats = RedactionAuditTrail::where('organization_id', $organizationId)
            ->where('created_at', '>=', $since)
            ->selectRaw('COUNT(DISTINCT DATE(created_at)) as active_days')
            ->selectRaw('SUM(count) as total_redacted')
            ->first();

        // Simple heuristic: active coverage and consistent redaction
        $activeDayScore = min(1.0, ($stats?->active_days ?? 0) / 7); // 7 days in a week
        $redactionVolume = min(1.0, ($stats?->total_redacted ?? 0) / 100); // Assume 100+ is good

        return round(($activeDayScore * 0.6 + $redactionVolume * 0.4), 2);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IntelligenceMetricAggregate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IntelligenceAggregatesController extends Controller
{
    /**
     * Get aggregated intelligence metrics with filtering
     *
     * GET /api/intelligence/aggregates
     * Query params:
     *   - metric_type: rag|llm_call|agent|evaluation (optional)
     *   - source_type: guide_faq|evaluations|roles|all (optional)
     *   - date_from: YYYY-MM-DD (optional, default: 30 days ago)
     *   - date_to: YYYY-MM-DD (optional, default: today)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', IntelligenceMetricAggregate::class);

        $organizationId = auth()->user()->organization_id;

        // Validate query parameters
        $validated = $request->validate([
            'metric_type' => 'nullable|string|in:rag,llm_call,agent,evaluation',
            'source_type' => 'nullable|string',
            'date_from' => 'nullable|date_format:Y-m-d',
            'date_to' => 'nullable|date_format:Y-m-d',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Default date range: last 30 days
        $dateTo = isset($validated['date_to']) ? Carbon::parse($validated['date_to']) : now();
        $dateFrom = isset($validated['date_from']) ? Carbon::parse($validated['date_from']) : now()->subDays(30);

        // Cache key for this query (1 hour TTL since data doesn't update until 01:01 UTC)
        $metricType = $validated['metric_type'] ?? 'all';
        $cacheKey = "intelligence_aggregates:{$organizationId}:{$dateFrom->toDateString()}:{$dateTo->toDateString()}:{$metricType}";

        $aggregates = Cache::remember($cacheKey, 3600, function () use ($organizationId, $validated, $dateFrom, $dateTo) {
            $query = IntelligenceMetricAggregate::where('organization_id', $organizationId)
                ->whereBetween('date_key', [$dateFrom->toDateString(), $dateTo->toDateString()])
                ->orderBy('date_key', 'asc');

            if (isset($validated['metric_type'])) {
                $query->where('metric_type', $validated['metric_type']);
            }

            if (isset($validated['source_type'])) {
                $query->where('source_type', $validated['source_type']);
            }

            return $query->paginate($validated['per_page'] ?? 50);
        });

        return response()->json([
            'status' => 'success',
            'data' => $aggregates->items(),
            'pagination' => [
                'total' => $aggregates->total(),
                'per_page' => $aggregates->perPage(),
                'current_page' => $aggregates->currentPage(),
                'last_page' => $aggregates->lastPage(),
            ],
            'metadata' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'metric_type' => $validated['metric_type'] ?? 'all',
                'cached_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get summary statistics for a date range
     *
     * GET /api/intelligence/aggregates/summary
     * Query params:
     *   - metric_type: rag|llm_call|agent|evaluation (optional)
     *   - date_from: YYYY-MM-DD (optional)
     *   - date_to: YYYY-MM-DD (optional)
     */
    public function summary(Request $request): JsonResponse
    {
        $this->authorize('viewAny', IntelligenceMetricAggregate::class);

        $organizationId = auth()->user()->organization_id;

        $validated = $request->validate([
            'metric_type' => 'nullable|string|in:rag,llm_call,agent,evaluation',
            'date_from' => 'nullable|date_format:Y-m-d',
            'date_to' => 'nullable|date_format:Y-m-d',
        ]);

        $dateTo = isset($validated['date_to']) ? Carbon::parse($validated['date_to']) : now();
        $dateFrom = isset($validated['date_from']) ? Carbon::parse($validated['date_from']) : now()->subDays(30);

        $metricType = $validated['metric_type'] ?? 'all';
        $cacheKey = "intelligence_summary:{$organizationId}:{$dateFrom->toDateString()}:{$dateTo->toDateString()}:{$metricType}";

        $summary = Cache::remember($cacheKey, 3600, function () use ($organizationId, $validated, $dateFrom, $dateTo) {
            $query = IntelligenceMetricAggregate::where('organization_id', $organizationId)
                ->whereBetween('date_key', [$dateFrom->toDateString(), $dateTo->toDateString()]);

            if (isset($validated['metric_type'])) {
                $query->where('metric_type', $validated['metric_type']);
            }

            $aggregates = $query->get();

            return [
                'total_records' => $aggregates->count(),
                'total_calls' => $aggregates->sum('total_count'),
                'successful_calls' => $aggregates->sum('success_count'),
                'success_rate' => $aggregates->count() > 0
                    ? round($aggregates->avg('success_rate'), 3)
                    : 0.0,
                'avg_duration_ms' => $aggregates->count() > 0
                    ? round($aggregates->avg('avg_duration_ms'), 2)
                    : 0.0,
                'p50_duration_ms' => $aggregates->count() > 0
                    ? round($aggregates->avg('p50_duration_ms'), 2)
                    : 0.0,
                'p95_duration_ms' => $aggregates->count() > 0
                    ? round($aggregates->avg('p95_duration_ms'), 2)
                    : 0.0,
                'p99_duration_ms' => $aggregates->count() > 0
                    ? round($aggregates->avg('p99_duration_ms'), 2)
                    : 0.0,
                'avg_confidence' => $aggregates->count() > 0
                    ? round($aggregates->avg('avg_confidence'), 3)
                    : 0.0,
                'avg_context_count' => $aggregates->count() > 0
                    ? round($aggregates->avg('avg_context_count'), 1)
                    : 0.0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $summary,
            'metadata' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'metric_type' => $validated['metric_type'] ?? 'all',
                'cached_at' => now()->toIso8601String(),
            ],
        ]);
    }
}

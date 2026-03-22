<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgentInteractionMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentInteractionMetricsController extends Controller
{
    protected AgentInteractionMetricsService $metricsService;

    public function __construct(AgentInteractionMetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Get comprehensive metrics for an organization.
     */
    public function summary(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $since = $request->query('since') ? \Carbon\Carbon::parse($request->query('since')) : null;

        $metrics = $this->metricsService->getOrganizationMetrics($organizationId, $since);

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Get top failing agents.
     */
    public function failingAgents(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $limit = $request->query('limit', 10);
        $since = $request->query('since') ? \Carbon\Carbon::parse($request->query('since')) : null;

        $data = $this->metricsService->getTopFailingAgents($organizationId, $since, $limit);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get average latency by agent.
     */
    public function latencyByAgent(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $since = $request->query('since') ? \Carbon\Carbon::parse($request->query('since')) : null;

        $data = $this->metricsService->getAverageLatencyByAgent($organizationId, $since);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

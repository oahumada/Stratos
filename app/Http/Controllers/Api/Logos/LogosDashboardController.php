<?php

namespace App\Http\Controllers\Api\Logos;

use App\Http\Controllers\Controller;
use App\Services\Logos\LogosDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Stratos Logos (λόγος) — Analytics & Business Intelligence.
 *
 * Unified cross-module analytics controller that consolidates
 * data from Core, Areté, Pathos, Kairos, Eureka, Ethos, Themis,
 * Praxis, Ágora, and Horizon into executive dashboards.
 */
class LogosDashboardController extends Controller
{
    public function __construct(
        private LogosDashboardService $dashboardService
    ) {}

    /**
     * GET /api/logos/executive-summary
     * Cross-module executive dashboard.
     */
    public function executiveSummary(): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        return response()->json([
            'status' => 'success',
            'data' => $this->dashboardService->executiveSummary($orgId),
        ]);
    }

    /**
     * GET /api/logos/module/{module}
     * Metrics for a specific module.
     */
    public function moduleMetrics(string $module): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        $metrics = match ($module) {
            'core' => $this->dashboardService->coreMetrics($orgId),
            'praxis' => $this->dashboardService->praxisMetrics($orgId),
            'agora' => $this->dashboardService->agoraMetrics($orgId),
            'horizon' => $this->dashboardService->horizonMetrics($orgId),
            'stratos-iq' => $this->dashboardService->stratosIqMetrics($orgId),
            default => null,
        };

        if ($metrics === null) {
            return response()->json([
                'status' => 'error',
                'message' => "Unknown module: {$module}",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'module' => $module,
            'data' => $metrics,
        ]);
    }

    /**
     * GET /api/logos/trends
     * Cross-module trend data for time-series charts.
     */
    public function trends(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $months = $request->integer('months', 6);

        return response()->json([
            'status' => 'success',
            'data' => $this->dashboardService->trends($orgId, min($months, 24)),
        ]);
    }
}

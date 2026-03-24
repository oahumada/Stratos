<?php

namespace App\Http\Controllers\Deployment;

use App\Services\VerificationMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * VerificationMetricsDashboardController - Provides metrics data for frontend dashboard
 *
 * Endpoints:
 * - GET /api/deployment/verification-metrics - Get current metrics
 * - GET /api/deployment/verification-metrics/history - Get historical data for charts
 * - GET /api/deployment/verification-metrics/by-type - Get metrics breakdown by agent
 */
class VerificationMetricsDashboardController
{
    public function __construct(
        protected VerificationMetricsService $metricsService,
    ) {}

    /**
     * Get current metrics for organization
     */
    public function current(Request $request): JsonResponse
    {
        try {
            $organizationId = auth()->user()->organization_id;
            $windowHours = $request->query('hours', 24);

            $metrics = $this->metricsService->getOrganizationMetrics(
                organizationId: $organizationId,
                windowHours: $windowHours
            );

            if (empty($metrics)) {
                return response()->json([
                    'data' => null,
                    'message' => 'No metrics available yet',
                ], 204);
            }

            return response()->json([
                'data' => $metrics,
                'window_hours' => $windowHours,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching metrics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get metrics breakdown by agent type
     */
    public function byType(Request $request): JsonResponse
    {
        try {
            $organizationId = auth()->user()->organization_id;
            $windowHours = $request->query('hours', 24);

            $metrics = $this->metricsService->getDetailedMetricsByType(
                organizationId: $organizationId,
                windowHours: $windowHours
            );

            return response()->json([
                'data' => $metrics,
                'window_hours' => $windowHours,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching metrics by type',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export metrics as JSON for download
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $organizationId = auth()->user()->organization_id;
            $windowHours = $request->query('hours', 24);

            $json = $this->metricsService->exportMetricsJson(
                organizationId: $organizationId,
                windowHours: $windowHours
            );

            return response()->json([
                'export_data' => json_decode($json),
                'exported_at' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error exporting metrics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

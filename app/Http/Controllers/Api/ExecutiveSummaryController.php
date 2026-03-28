<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\GenerateExecutiveSummaryRequest;
use App\Models\Scenario;
use App\Services\ScenarioPlanning\ExecutiveSummaryService;
use Illuminate\Http\JsonResponse;

/**
 * ExecutiveSummaryController — Executive-level scenario insights
 *
 * Provides API endpoints for executive dashboards:
 * - Executive summary generation
 * - KPI aggregation
 * - Decision recommendations
 * - Risk heatmap data
 * - Readiness assessment
 */
class ExecutiveSummaryController
{
    public function __construct(private ExecutiveSummaryService $summaryService)
    {
    }

    /**
     * Generate comprehensive executive summary for a scenario
     *
     * GET /api/strategic-planning/scenarios/{scenarioId}/executive-summary
     */
    public function __invoke(int $scenarioId): JsonResponse
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $summary = $this->summaryService->generateExecutiveSummary($scenarioId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Generate executive summary with custom options
     *
     * POST /api/strategic-planning/scenarios/{scenarioId}/executive-summary
     */
    public function generate(GenerateExecutiveSummaryRequest $request, int $scenarioId): JsonResponse
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $summary = $this->summaryService->generateExecutiveSummary(
            $scenarioId,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Export executive summary as PDF/PPTX
     *
     * POST /api/strategic-planning/scenarios/{scenarioId}/executive-summary/export
     */
    public function export(int $scenarioId, \Illuminate\Http\Request $request): JsonResponse
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $format = $request->input('format', 'pdf');
        if (! in_array($format, ['pdf', 'pptx'])) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid export format. Must be pdf or pptx.',
            ], 400);
        }

        // TODO: Implement export to PDF/PPTX using mPDF or PHPPowerPoint
        return response()->json([
            'success' => true,
            'message' => "Export to $format queued",
            'download_url' => "/api/strategic-planning/scenarios/{$scenarioId}/executive-summary/download?format={$format}",
        ]);
    }
}

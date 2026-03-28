<?php

namespace App\Http\Controllers\Api;

use App\Models\Scenario;
use App\Services\ScenarioPlanning\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ExportController — Executive summary export endpoints
 *
 * Handles PDF/PPTX export generation and download:
 * - Export initiation and status tracking
 * - File download with expiration
 * - Async job management
 */
class ExportController
{
    public function __construct(private ExportService $exportService)
    {
    }

    /**
     * Initiate PDF export of executive summary
     *
     * POST /api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf
     */
    public function exportPdf(int $scenarioId, Request $request): JsonResponse
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $options = [
            'include_appendix' => $request->boolean('include_appendix', false),
            'style' => $request->input('style', 'professional'),
        ];

        $result = $this->exportService->exportToPdf($scenarioId, $options);

        return response()->json([
            'success' => $result['success'],
            'data' => $result,
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Initiate PPTX export of executive summary
     *
     * POST /api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx
     */
    public function exportPptx(int $scenarioId, Request $request): JsonResponse
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $options = [
            'template' => $request->input('template', 'corporate'),
            'include_speaker_notes' => $request->boolean('include_speaker_notes', true),
        ];

        $result = $this->exportService->exportToPptx($scenarioId, $options);

        return response()->json([
            'success' => $result['success'],
            'data' => $result,
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Download previously generated export file
     *
     * GET /api/strategic-planning/scenarios/{scenarioId}/executive-summary/download
     */
    public function download(int $scenarioId, Request $request): JsonResponse|\Illuminate\Http\Response
    {
        $scenario = Scenario::findOrFail($scenarioId);
        $this->authorize('view', $scenario);

        $format = $request->input('format', 'pdf');
        $filename = $request->input('file');

        if (! in_array($format, ['pdf', 'pptx'])) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid format.',
            ], 400);
        }

        if (! $filename) {
            return response()->json([
                'success' => false,
                'error' => 'Filename required.',
            ], 400);
        }

        $file = $this->exportService->getExportFile($filename, $format);

        if (! $file['success']) {
            return response()->json($file, 404);
        }

        // TODO: Implement file streaming
        // return response()->download($file['file_path']);

        return response()->json([
            'success' => true,
            'message' => 'Download prepared',
            'file_path' => $file['file_path'],
        ]);
    }

    /**
     * Get export job status
     *
     * GET /api/strategic-planning/exports/{format}/status
     */
    public function status(Request $request): JsonResponse
    {
        $jobId = $request->input('job_id');

        if (! $jobId) {
            return response()->json([
                'success' => false,
                'error' => 'Job ID required.',
            ], 400);
        }

        // TODO: Implement job status polling
        // Should check queue/database for job status

        return response()->json([
            'success' => true,
            'status' => 'processing',
            'progress_percent' => 50,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImpactReportService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImpactReportController extends Controller
{
    use ApiResponses;

    public function __construct(protected ImpactReportService $reportService) {}

    /**
     * GET /api/reports/scenario/{id}/impact
     */
    public function scenarioImpact(int $scenarioId): JsonResponse
    {
        try {
            $report = $this->reportService->generateScenarioImpactReport($scenarioId);
            return $this->successResponse($report, 'Reporte de impacto generado.');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar reporte: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/reports/roi
     */
    public function organizationalRoi(): JsonResponse
    {
        try {
            $report = $this->reportService->generateOrganizationalRoiReport();
            return $this->successResponse($report, 'Reporte de ROI organizacional generado.');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar reporte ROI: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/reports/consolidated
     */
    public function consolidated(): JsonResponse
    {
        try {
            $report = $this->reportService->generateConsolidatedReport();
            return $this->successResponse($report, 'Reporte consolidado generado.');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al generar reporte consolidado: ' . $e->getMessage(), 500);
        }
    }
}

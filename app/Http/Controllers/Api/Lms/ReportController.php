<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\LmsReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected LmsReportService $reportService,
    ) {}

    public function completion(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $filters = $request->only(['category']);
        $data = $this->reportService->completionReport($organizationId, $filters);

        return response()->json(['data' => $data]);
    }

    public function compliance(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->reportService->complianceStatusReport($organizationId);

        return response()->json(['data' => $data]);
    }

    public function timeToComplete(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->reportService->timeToCompleteReport($organizationId);

        return response()->json(['data' => $data]);
    }

    public function engagement(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->reportService->engagementTrendsReport($organizationId);

        return response()->json(['data' => $data]);
    }

    public function export(Request $request, string $type): StreamedResponse|JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $validTypes = ['completion', 'compliance', 'time-to-complete', 'engagement'];
        if (! in_array($type, $validTypes)) {
            return response()->json(['message' => 'Invalid report type.'], 422);
        }

        $reportData = match ($type) {
            'completion' => $this->reportService->completionReport($organizationId),
            'compliance' => $this->reportService->complianceStatusReport($organizationId),
            'time-to-complete' => $this->reportService->timeToCompleteReport($organizationId),
            'engagement' => $this->reportService->engagementTrendsReport($organizationId),
        };

        $csv = $this->reportService->exportToCsv($reportData, $type);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, "lms-report-{$type}-".date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}

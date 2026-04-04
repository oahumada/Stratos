<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\ScheduledReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduledReportController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected ScheduledReportService $scheduledReportService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $reports = $this->scheduledReportService->getForOrganization($organizationId);

        return response()->json(['data' => $reports]);
    }

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $validated = $request->validate([
            'report_type' => 'required|in:completion,compliance,engagement,time_to_complete',
            'filters' => 'nullable|array',
            'frequency' => 'required|in:daily,weekly,monthly',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
            'is_active' => 'nullable|boolean',
        ]);

        $report = $this->scheduledReportService->create(
            $organizationId,
            $request->user()->id,
            $validated,
        );

        return response()->json(['data' => $report], 201);
    }

    public function update(Request $request, int $scheduledReport): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'sometimes|in:completion,compliance,engagement,time_to_complete',
            'filters' => 'nullable|array',
            'frequency' => 'sometimes|in:daily,weekly,monthly',
            'recipients' => 'sometimes|array|min:1',
            'recipients.*' => 'required|email',
            'is_active' => 'nullable|boolean',
        ]);

        $report = $this->scheduledReportService->update($scheduledReport, $validated);

        return response()->json(['data' => $report]);
    }

    public function destroy(int $scheduledReport): JsonResponse
    {
        \App\Models\LmsScheduledReport::findOrFail($scheduledReport)->delete();

        return response()->json(['message' => 'Scheduled report deleted.']);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}

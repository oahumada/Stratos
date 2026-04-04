<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\ComplianceTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplianceController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected ComplianceTrackingService $complianceService,
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->complianceService->getDashboardData($organizationId);

        return response()->json(['data' => $data]);
    }

    public function records(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $query = \App\Models\LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->with('user:id,name,email', 'course:id,title,compliance_category');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->whereHas('course', fn ($q) => $q->where('compliance_category', $request->input('category')));
        }

        $records = $query->orderByDesc('due_date')
            ->paginate($request->input('per_page', 25));

        return response()->json($records);
    }

    public function check(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $overdue = $this->complianceService->checkOverdueRecords($organizationId);
        $escalations = $this->complianceService->processEscalations($organizationId);
        $recertifications = $this->complianceService->processRecertifications($organizationId);

        return response()->json([
            'success' => true,
            'overdue_updated' => $overdue,
            'escalations' => $escalations,
            'recertifications_created' => $recertifications,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        $filters = $request->only(['status', 'category']);
        $csv = $this->complianceService->exportCsv($organizationId, $filters);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'compliance-export-'.date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\Request;

/**
 * AuditController - API endpoints for audit log querying and reporting
 */
class AuditController extends Controller
{
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Get paginated audit logs for organization
     */
    public function index(Request $request)
    {
        $organizationId = auth()->user()->organization_id;

        $filters = [
            'action' => $request->input('action'),
            'entity_type' => $request->input('entity_type'),
            'user_id' => $request->input('user_id'),
            'days' => $request->integer('days', 7),
        ];

        $perPage = $request->integer('per_page', 20);
        $page = $request->integer('page', 1);

        $logs = $this->auditService->paginateLogs(
            $organizationId,
            array_filter($filters),
            $perPage
        );

        $stats = $this->auditService->getStatistics($organizationId, $filters['days']);

        return response()->json([
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Get activity heatmap data
     */
    public function heatmap()
    {
        $organizationId = auth()->user()->organization_id;

        return response()->json([
            'hourly' => $this->auditService->getActivityHeatmap($organizationId, 7),
            'daily' => $this->auditService->getActivityByDay($organizationId, 30),
        ]);
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $organizationId = auth()->user()->organization_id;

        $filters = [
            'action' => $request->input('action'),
            'entity_type' => $request->input('entity_type'),
            'user_id' => $request->input('user_id'),
            'days' => $request->integer('days', 30),
        ];

        $csv = $this->auditService->exportToCSV($organizationId, array_filter($filters));

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-logs-'.now()->format('Y-m-d').'.csv"',
        ]);
    }

    /**
     * Get entity change history timeline
     */
    public function entityTimeline($entityType, $entityId)
    {
        $history = $this->auditService->getEntityChangeHistory($entityType, $entityId);

        return response()->json($history);
    }

    /**
     * Get user activity summary
     */
    public function userActivity($userId)
    {
        $organizationId = auth()->user()->organization_id;
        $activity = $this->auditService->getUserActivity($organizationId, $userId, 30);

        return response()->json($activity);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminOperationAudit;
use App\Services\AdminOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOperationsController extends Controller
{
    public function __construct(
        private AdminOperationsService $operationsService
    ) {}

    /**
     * GET /api/admin/operations
     * List recent operations for the organization
     */
    public function index(Request $request): JsonResponse
    {
        $organization = $request->user()->people?->organization;
        
        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 422);
        }

        $this->authorize('viewAny', [AdminOperationAudit::class, $organization]);

        $history = $this->operationsService->getHistory($organization, 50);
        $stats = $this->operationsService->getSummaryStats($organization);

        return response()->json([
            'data' => $history,
            'stats' => $stats,
        ]);
    }

    /**
     * POST /api/admin/operations/{operation}/preview
     * Preview what would happen with dry-run
     */
    public function preview(Request $request, AdminOperationAudit $operation): JsonResponse
    {
        $this->authorize('update', $operation);

        if ($operation->status !== 'pending') {
            return response()->json([
                'message' => 'Can only preview pending operations',
            ], 422);
        }

        try {
            // Get the preview data based on operation type
            $previewData = $this->generatePreview($operation);
            
            $operation = $this->operationsService->previewOperation(
                $operation,
                fn() => $previewData
            );

            return response()->json([
                'data' => $operation,
                'message' => 'Preview completed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/admin/operations/{operation}/execute
     * Execute the operation (after approval)
     */
    public function execute(Request $request, AdminOperationAudit $operation): JsonResponse
    {
        $this->authorize('update', $operation);

        $validated = $request->validate([
            'confirmed' => 'required|boolean|accepted',
        ]);

        if (!$validated['confirmed']) {
            return response()->json([
                'message' => 'Operation must be confirmed',
            ], 422);
        }

        try {
            $operation = $this->operationsService->executeOperation(
                $operation,
                fn() => $this->performOperation($operation)
            );

            return response()->json([
                'data' => $operation,
                'message' => 'Operation executed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /api/admin/operations/{operation}/cancel
     * Cancel a pending operation
     */
    public function cancel(Request $request, AdminOperationAudit $operation): JsonResponse
    {
        $this->authorize('delete', $operation);

        if ($operation->status !== 'pending') {
            return response()->json([
                'message' => 'Can only cancel pending operations',
            ], 422);
        }

        $operation->markAsCancelled();

        return response()->json([
            'data' => $operation,
            'message' => 'Operation cancelled',
        ]);
    }

    /**
     * Generate preview data based on operation type
     */
    private function generatePreview(AdminOperationAudit $operation): array
    {
        return [
            'operation_type' => $operation->operation_type,
            'estimated_records' => 0,
            'affected_areas' => [],
            'warnings' => [],
        ];
    }

    /**
     * Perform the actual operation
     */
    private function performOperation(AdminOperationAudit $operation): array
    {
        return [
            'result' => [
                'status' => 'completed',
            ],
            'records_processed' => 0,
            'records_affected' => 0,
        ];
    }
}

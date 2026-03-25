<?php

namespace App\Services;

use App\Models\AdminOperationAudit;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminOperationsService
{
    public function __construct()
    {
    }

    /**
     * Create and log an admin operation audit record
     */
    public function createAudit(
        User $user,
        Organization $organization,
        string $operationType,
        string $operationName,
        array $parameters = []
    ): AdminOperationAudit {
        return AdminOperationAudit::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'operation_type' => $operationType,
            'operation_name' => $operationName,
            'parameters' => $parameters,
            'status' => 'pending',
        ]);
    }

    /**
     * Execute a dry-run preview of an operation
     */
    public function previewOperation(
        AdminOperationAudit $audit,
        callable $previewCallback
    ): AdminOperationAudit {
        try {
            $preview = $previewCallback();
            $audit->markAsDryRun($preview);
            return $audit;
        } catch (\Exception $e) {
            $audit->markAsFailed("Preview failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Execute an operation (after approval)
     */
    public function executeOperation(
        AdminOperationAudit $audit,
        callable $executionCallback
    ): AdminOperationAudit {
        if (!in_array($audit->status, ['pending', 'dry_run'])) {
            throw new \Exception("Cannot execute operation in {$audit->status} status");
        }

        $audit->markAsRunning();

        try {
            DB::beginTransaction();

            $result = $executionCallback();

            $audit->markAsSuccess(
                $result['result'] ?? [],
                $result['records_processed'] ?? 0,
                $result['records_affected'] ?? 0
            );

            DB::commit();
            return $audit;
        } catch (\Exception $e) {
            DB::rollBack();
            $audit->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Get operation history for organization
     */
    public function getHistory(Organization $organization, int $limit = 50)
    {
        return AdminOperationAudit::forOrganization($organization->id)
            ->latest('created_at')
            ->limit($limit)
            ->with(['user:id,name,email'])
            ->get();
    }

    /**
     * Get summary stats for dashboard
     */
    public function getSummaryStats(Organization $organization): array
    {
        $recentOps = AdminOperationAudit::forOrganization($organization->id)
            ->recent(30);

        return [
            'total_operations' => $recentOps->count(),
            'successful' => $recentOps->byStatus('success')->count(),
            'failed' => $recentOps->byStatus('failed')->count(),
            'pending' => $recentOps->byStatus('pending')->count(),
            'total_records_affected' => $recentOps->byStatus('success')->sum('records_affected'),
            'average_duration' => $recentOps->byStatus('success')->avg('duration_seconds'),
        ];
    }
}

<?php

namespace App\Jobs;

use App\Events\AdminOperationCompleted;
use App\Events\AdminOperationFailed;
use App\Events\AdminOperationRolledBack;
use App\Events\AdminOperationStarted;
use App\Models\AdminOperationAudit;
use App\Services\AdminOperationLockService;
use App\Services\AdminOperationRollbackService;
use App\Services\AdminOperationsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AdminOperationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Admin Operation Audit ID to execute
     */
    public int $auditId;

    /**
     * Organization ID for multi-tenant scoping
     */
    public int $organizationId;

    /**
     * Number of attempts before failing
     */
    public int $tries = 3;

    /**
     * Backoff strategy for retries (exponential: 30s, 120s, 300s)
     */
    public array $backoff = [30, 120, 300];

    /**
     * Timeout for job execution (seconds) - operations can take time
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(int $auditId, int $organizationId)
    {
        $this->auditId = $auditId;
        $this->organizationId = $organizationId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        AdminOperationsService $service,
        AdminOperationLockService $lockService,
        AdminOperationRollbackService $rollbackService
    ): void {
        try {
            // Load audit record with organization scoping
            $audit = AdminOperationAudit::where('id', $this->auditId)
                ->where('organization_id', $this->organizationId)
                ->first();

            if (! $audit) {
                Log::error('AdminOperationJob: Audit record not found', [
                    'audit_id' => $this->auditId,
                    'organization_id' => $this->organizationId,
                ]);
                throw new \RuntimeException('Audit record not found');
            }

            // Try to acquire concurrency lock for this operation type
            $lockAcquired = $lockService->acquire(
                $this->organizationId,
                $audit->operation_type
            );

            if (! $lockAcquired) {
                Log::warning('AdminOperationJob: Could not acquire lock (operation already running)', [
                    'audit_id' => $this->auditId,
                    'operation_type' => $audit->operation_type,
                    'attempt' => $this->attempts(),
                ]);

                // Release for retry
                throw new \RuntimeException('Could not acquire operation lock - another instance is running');
            }

            // Create snapshot before executing (for rollback capability)
            $snapshot = $rollbackService->createSnapshot($audit);
            $audit->update(['dry_run_preview' => $snapshot]);

            // Mark as running
            $audit->markAsRunning();

            // Broadcast operation started event
            AdminOperationStarted::dispatch($audit);

            Log::info('AdminOperationJob: Starting operation', [
                'audit_id' => $this->auditId,
                'operation_type' => $audit->operation_type,
                'organization_id' => $this->organizationId,
            ]);

            // Execute the operation via service
            $result = $service->executeOperation($audit);

            // Mark as success
            $audit->markAsSuccess($result);

            // Broadcast operation completed event
            AdminOperationCompleted::dispatch($audit);

            Log::info('AdminOperationJob: Operation completed successfully', [
                'audit_id' => $this->auditId,
                'operation_type' => $audit->operation_type,
            ]);
        } catch (\Exception $e) {
            Log::error('AdminOperationJob: Operation failed', [
                'audit_id' => $this->auditId,
                'operation_type' => $audit->operation_type ?? 'unknown',
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Mark as failed if all retries exhausted
            if ($this->attempts() >= $this->tries) {
                $audit->markAsFailed($e->getMessage());
            }

            throw $e;
        } finally {
            // Always release lock when done
            if (isset($audit)) {
                $lockService->release($this->organizationId, $audit->operation_type);
            }
        }
    }

    /**
     * Handle job failure after all retries exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        $audit = AdminOperationAudit::find($this->auditId);
        if ($audit) {
            $audit->markAsFailed($exception->getMessage());

            // Broadcast operation failed event
            AdminOperationFailed::dispatch($audit);

            // Attempt automatic rollback
            $rollbackService = app(AdminOperationRollbackService::class);
            if ($rollbackService->canRollback($audit)) {
                Log::info('AdminOperationJob: Attempting automatic rollback', [
                    'audit_id' => $this->auditId,
                ]);

                if ($rollbackService->performRollback($audit)) {
                    AdminOperationRolledBack::dispatch($audit);
                }
            }

            Log::error('AdminOperationJob: Failed permanently after retries', [
                'audit_id' => $this->auditId,
                'error' => $exception->getMessage(),
                'rolled_back' => $rollbackService->canRollback($audit) ? true : false,
            ]);
        }
    }
}

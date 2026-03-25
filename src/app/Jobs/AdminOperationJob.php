<?php

namespace App\Jobs;

use App\Models\AdminOperationAudit;
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
     * Queue name for this job
     */
    public string $queue = 'default';

    /**
     * Create a new job instance.
     */
    public function __construct(int $auditId, int $organizationId)
    {
        $this->auditId = $auditId;
        $this->organizationId = $organizationId;
        $this->queue = config('queue.default', 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(AdminOperationsService $service): void
    {
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

            // Mark as running
            $audit->markAsRunning();

            Log::info('AdminOperationJob: Starting operation', [
                'audit_id' => $this->auditId,
                'operation_type' => $audit->operation_type,
                'organization_id' => $this->organizationId,
            ]);

            // Execute the operation via service
            $result = $service->executeOperation($audit);

            // Mark as success
            $audit->markAsSuccess($result);

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
            Log::error('AdminOperationJob: Failed permanently after retries', [
                'audit_id' => $this->auditId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}

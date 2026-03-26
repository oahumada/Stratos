<?php

namespace App\Services;

use App\Events\AdminOperationRolledBack;
use App\Models\AdminOperationAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminOperationRollbackService
{
    /**
     * Perform automatic rollback for a failed operation
     */
    public function performRollback(AdminOperationAudit $operation): bool
    {
        try {
            // Check if rollback is possible
            if (! $this->canRollback($operation)) {
                Log::warning('AdminOperationRollback: Operation cannot be rolled back', [
                    'audit_id' => $operation->id,
                    'operation_type' => $operation->operation_type,
                    'reason' => 'No snapshot available or status not allowed',
                ]);

                return false;
            }

            Log::info('AdminOperationRollback: Starting rollback', [
                'audit_id' => $operation->id,
                'operation_type' => $operation->operation_type,
            ]);

            DB::beginTransaction();

            // Perform operation-specific rollback
            $result = match ($operation->operation_type) {
                'backfill' => $this->rollbackBackfill($operation),
                'generate' => $this->rollbackGenerate($operation),
                'import' => $this->rollbackImport($operation),
                'cleanup' => $this->rollbackCleanup($operation),
                'rebuild' => $this->rollbackRebuild($operation),
                default => throw new \RuntimeException("Unknown operation type: {$operation->operation_type}"),
            };

            // Mark as rolled back within transaction
            $operation->update([
                'status' => 'rolled_back',
                'error_message' => 'Automatically rolled back after failed execution',
            ]);

            DB::commit();

            // Dispatch rolled back event after successful rollback
            AdminOperationRolledBack::dispatch($operation->fresh());

            Log::info('AdminOperationRollback: Rollback completed successfully', [
                'audit_id' => $operation->id,
                'records_affected' => $result['records_affected'] ?? 0,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('AdminOperationRollback: Rollback failed', [
                'audit_id' => $operation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Check if an operation can be rolled back
     */
    public function canRollback(AdminOperationAudit $operation): bool
    {
        // Must have snapshot data (array must exist, even if empty)
        if ($operation->dry_run_preview === null) {
            return false;
        }

        // Only rollback failed operations
        if ($operation->status !== 'failed') {
            return false;
        }

        // Cleanup operations cannot be rolled back
        if ($operation->operation_type === 'cleanup') {
            return false;
        }

        // Allow rollback for certain operation types
        return in_array($operation->operation_type, [
            'backfill',
            'generate',
            'import',
            'rebuild',
        ]);
    }

    /**
     * Rollback backfill operation
     */
    private function rollbackBackfill(AdminOperationAudit $operation): array
    {
        $snapshot = $operation->dry_run_preview;

        // Delete backfilled records based on IDs stored in snapshot
        if (isset($snapshot['created_ids']) && ! empty($snapshot['created_ids'])) {
            $deleted = DB::table($snapshot['table'] ?? 'records')
                ->whereIn('id', $snapshot['created_ids'])
                ->delete();

            return ['records_affected' => $deleted];
        }

        return ['records_affected' => 0];
    }

    /**
     * Rollback generate operation
     */
    private function rollbackGenerate(AdminOperationAudit $operation): array
    {
        $snapshot = $operation->dry_run_preview;

        // Delete generated records
        if (isset($snapshot['generated_ids']) && ! empty($snapshot['generated_ids'])) {
            $deleted = DB::table($snapshot['table'] ?? 'generated_items')
                ->whereIn('id', $snapshot['generated_ids'])
                ->delete();

            return ['records_affected' => $deleted];
        }

        return ['records_affected' => 0];
    }

    /**
     * Rollback import operation
     */
    private function rollbackImport(AdminOperationAudit $operation): array
    {
        $snapshot = $operation->dry_run_preview;

        // Delete imported records
        if (isset($snapshot['imported_ids']) && ! empty($snapshot['imported_ids'])) {
            $deleted = DB::table($snapshot['table'] ?? 'imports')
                ->whereIn('id', $snapshot['imported_ids'])
                ->delete();

            return ['records_affected' => $deleted];
        }

        return ['records_affected' => 0];
    }

    /**
     * Rollback cleanup operation
     */
    private function rollbackCleanup(AdminOperationAudit $operation): array
    {
        // Cleanup operations are destructive and typically cannot be rolled back
        // Some cleanup data might be stored in snapshot for reference
        Log::warning('AdminOperationRollback: Cleanup operations cannot be rolled back', [
            'audit_id' => $operation->id,
        ]);

        return ['records_affected' => 0];
    }

    /**
     * Rollback rebuild operation
     */
    private function rollbackRebuild(AdminOperationAudit $operation): array
    {
        // Rebuild operations typically restore from snapshot or previous state
        // Implementation depends on what was rebuilt (indexes, caches, etc.)
        Log::info('AdminOperationRollback: Rebuild rollback (restoration not fully reversible)', [
            'audit_id' => $operation->id,
        ]);

        return ['records_affected' => 0];
    }

    /**
     * Create snapshot before operation execution
     */
    public function createSnapshot(AdminOperationAudit $operation): array
    {
        return match ($operation->operation_type) {
            'backfill' => $this->snapshotBackfill($operation),
            'generate' => $this->snapshotGenerate($operation),
            'import' => $this->snapshotImport($operation),
            'cleanup' => $this->snapshotCleanup($operation),
            'rebuild' => $this->snapshotRebuild($operation),
            default => [],
        };
    }

    /**
     * Snapshot backfill operation
     */
    private function snapshotBackfill(AdminOperationAudit $operation): array
    {
        return [
            'table' => 'records',
            'timestamp' => now()->toIso8601String(),
            'created_ids' => [],
        ];
    }

    /**
     * Snapshot generate operation
     */
    private function snapshotGenerate(AdminOperationAudit $operation): array
    {
        return [
            'table' => 'generated_items',
            'timestamp' => now()->toIso8601String(),
            'generated_ids' => [],
        ];
    }

    /**
     * Snapshot import operation
     */
    private function snapshotImport(AdminOperationAudit $operation): array
    {
        return [
            'table' => 'imports',
            'timestamp' => now()->toIso8601String(),
            'imported_ids' => [],
        ];
    }

    /**
     * Snapshot cleanup operation
     */
    private function snapshotCleanup(AdminOperationAudit $operation): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'note' => 'Cleanup operations are not reversible',
        ];
    }

    /**
     * Snapshot rebuild operation
     */
    private function snapshotRebuild(AdminOperationAudit $operation): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'rebuild_type' => 'generic',
        ];
    }
}

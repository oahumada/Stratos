<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AdminOperationLockService - Manages concurrency locks for admin operations
 *
 * Responsibilities:
 * 1. Prevent concurrent execution of the same operation type per organization
 * 2. Provide mutual exclusion (mutex) for sensitive operations
 * 3. Auto-release locks after timeout or manual release
 *
 * Strategy:
 * - Lock key: "admin_op_lock:{organization_id}:{operation_type}"
 * - TTL: 10 minutes (default) - should be adjusted per operation complexity
 * - Blocking: acquireLock() waits up to 10 seconds for lock release
 * - Release: manual releaseLock() or auto-expiry
 */
class AdminOperationLockService
{
    /**
     * Default lock timeout in seconds (10 minutes)
     * Operations should not run longer than this
     */
    protected int $defaultLockTimeout = 600;

    /**
     * Default wait timeout in seconds (how long to wait for lock)
     * If operation is still running after this, fail fast
     */
    protected int $defaultWaitTimeout = 10;

    /**
     * Generate lock key from organization and operation type
     */
    protected function getLockKey(int $organizationId, string $operationType): string
    {
        return "admin_op_lock:{$organizationId}:{$operationType}";
    }

    /**
     * Acquire a lock for an operation
     * Returns true if lock acquired, false if another operation is running
     *
     * @param  int  $organizationId  Organization ID for multi-tenant scoping
     * @param  string  $operationType  Operation type (backfill, generate, import, etc)
     * @param  int  $waitSeconds  How long to wait for lock to be available
     */
    public function acquire(
        int $organizationId,
        string $operationType,
        ?int $waitSeconds = null
    ): bool {
        $waitSeconds = $waitSeconds ?? $this->defaultWaitTimeout;
        $lockKey = $this->getLockKey($organizationId, $operationType);

        try {
            $lock = Cache::lock($lockKey, $this->defaultLockTimeout);

            // Try to acquire, waiting up to $waitSeconds
            if ($lock->block($waitSeconds)) {
                Log::info('AdminOperationLockService: Lock acquired', [
                    'organization_id' => $organizationId,
                    'operation_type' => $operationType,
                    'lock_key' => $lockKey,
                ]);

                return true;
            }

            Log::warning('AdminOperationLockService: Lock timeout (operation already running)', [
                'organization_id' => $organizationId,
                'operation_type' => $operationType,
                'wait_seconds' => $waitSeconds,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('AdminOperationLockService: Error acquiring lock', [
                'organization_id' => $organizationId,
                'operation_type' => $operationType,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if a lock is currently held
     * Uses a direct cache check instead of trying to acquire
     */
    public function isLocked(int $organizationId, string $operationType): bool
    {
        $lockKey = $this->getLockKey($organizationId, $operationType);

        try {
            // Check if the lock key exists in cache
            // Laravel puts lock owners in cache keys with a specific format
            $lockOwner = Cache::get($lockKey);

            return $lockOwner !== null;
        } catch (\Exception $e) {
            Log::error('AdminOperationLockService: Error checking lock status', [
                'organization_id' => $organizationId,
                'operation_type' => $operationType,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Manually release a lock
     * Called after operation completes (success or failure)
     */
    public function release(int $organizationId, string $operationType): bool
    {
        $lockKey = $this->getLockKey($organizationId, $operationType);

        try {
            $lock = Cache::lock($lockKey, 1);

            // If we can acquire it, it means it was held by another process
            // But we can safely clear the cache key
            Cache::forget($lockKey);

            Log::info('AdminOperationLockService: Lock released', [
                'organization_id' => $organizationId,
                'operation_type' => $operationType,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('AdminOperationLockService: Error releasing lock', [
                'organization_id' => $organizationId,
                'operation_type' => $operationType,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Execute a callback with lock protection
     * Automatically acquires lock, runs callback, releases lock
     *
     * Usage:
     * ```
     * $service->withLock($orgId, 'backfill', function() {
     *     // Do work here
     * });
     * ```
     *
     * @return mixed Returns result from callback, null if lock not acquired
     */
    public function withLock(
        int $organizationId,
        string $operationType,
        callable $callback,
        ?int $waitSeconds = null
    ): mixed {
        $waitSeconds = $waitSeconds ?? $this->defaultWaitTimeout;

        if (! $this->acquire($organizationId, $operationType, $waitSeconds)) {
            Log::warning('AdminOperationLockService: Could not acquire lock for execution', [
                'organization_id' => $organizationId,
                'operation_type' => $operationType,
            ]);

            return null;
        }

        try {
            $result = $callback();

            return $result;
        } finally {
            $this->release($organizationId, $operationType);
        }
    }
}

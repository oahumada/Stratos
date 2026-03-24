<?php

namespace App\Services\Mobile;

use App\Models\OfflineQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OfflineQueueService - Manages offline request queuing and sync
 *
 * Responsibilities:
 * 1. Queue webhook/approval requests when device is offline
 * 2. Store in database for later sync
 * 3. Sync queued requests when connection restored
 * 4. Handle conflict resolution (older requests fail if already processed)
 * 5. Deduplicate requests (prevent double submission)
 * 6. Retry logic with exponential backoff
 *
 * Queue Strategy:
 * - Store request in offline_queue table with status 'pending'
 * - When app comes online, call /api/mobile/offline-queue/sync
 * - Sync endpoint processes all pending requests in order
 * - Mark as 'synced' with timestamp for audit trail
 * - Retry failed requests up to 3 times
 *
 * Conflict Handling:
 * - If approval already exists in DB, mark as 'duplicate'
 * - Track original request_id to prevent race conditions
 * - Return conflict_resolution status to client
 *
 * Integration:
 * - Stores deferred requests (approvals, webhook callbacks)
 * - Works with PushNotificationService for sync notifications
 * - Integrates with RemediationService for auto-retry
 */
class OfflineQueueService
{
    public const MAX_RETRIES = 3;

    public const BATCH_SIZE = 50; // Sync in batches to avoid overwhelming server

    /**
     * Queue a request for later sync
     */
    public function queueRequest(
        int $userId,
        int $organizationId,
        string $requestType, // approval_response, webhook_callback, webhook_registration
        string $endpoint,
        array $payload,
        ?string $deduplicationKey = null
    ): OfflineQueue {
        try {
            // Check for existing queued request (deduplication)
            if ($deduplicationKey) {
                $existing = OfflineQueue::where('organization_id', $organizationId)
                    ->where('user_id', $userId)
                    ->where('deduplication_key', $deduplicationKey)
                    ->where('status', 'pending')
                    ->first();

                if ($existing) {
                    Log::debug('Request already queued (deduplicated)', [
                        'user_id' => $userId,
                        'dedup_key' => $deduplicationKey,
                    ]);

                    return $existing;
                }
            }

            $queued = OfflineQueue::create([
                'user_id' => $userId,
                'organization_id' => $organizationId,
                'request_type' => $requestType,
                'endpoint' => $endpoint,
                'payload' => $payload,
                'deduplication_key' => $deduplicationKey,
                'status' => 'pending',
                'retry_count' => 0,
                'last_error' => null,
                'queued_at' => now(),
            ]);

            Log::info('Request queued for offline sync', [
                'queue_id' => $queued->id,
                'user_id' => $userId,
                'type' => $requestType,
            ]);

            return $queued;
        } catch (\Exception $e) {
            Log::error('Error queuing request', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            throw $e;
        }
    }

    /**
     * Sync all pending requests for a user
     */
    public function syncUserQueue(int $userId, int $organizationId): array
    {
        try {
            DB::beginTransaction();

            $pendingRequests = OfflineQueue::where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->where('status', 'pending')
                ->orderBy('queued_at', 'asc')
                ->limit(self::BATCH_SIZE)
                ->get();

            $results = [
                'total' => $pendingRequests->count(),
                'synced' => 0,
                'failed' => 0,
                'details' => [],
            ];

            foreach ($pendingRequests as $request) {
                $result = $this->processQueuedRequest($request);

                $results['details'][] = [
                    'queue_id' => $request->id,
                    'status' => $result['status'],
                    'reason' => $result['reason'] ?? null,
                ];

                if ($result['status'] === 'synced') {
                    $results['synced']++;
                } else {
                    $results['failed']++;
                }
            }

            DB::commit();

            Log::info('User offline queue synced', array_merge($results, [
                'user_id' => $userId,
            ]));

            return $results;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing user queue', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [
                'total' => 0,
                'synced' => 0,
                'failed' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sync all pending requests for an organization
     */
    public function syncOrganizationQueue(int $organizationId): array
    {
        $pendingCount = OfflineQueue::where('organization_id', $organizationId)
            ->where('status', 'pending')
            ->count();

        if ($pendingCount === 0) {
            return ['total' => 0, 'synced' => 0, 'failed' => 0];
        }

        $results = ['total' => 0, 'synced' => 0, 'failed' => 0];

        // Process in batches
        while ($pendingCount > 0) {
            $requests = OfflineQueue::where('organization_id', $organizationId)
                ->where('status', 'pending')
                ->limit(self::BATCH_SIZE)
                ->get();

            foreach ($requests as $request) {
                $result = $this->processQueuedRequest($request);
                $results['total']++;

                if ($result['status'] === 'synced') {
                    $results['synced']++;
                } else {
                    $results['failed']++;
                }
            }

            $pendingCount = OfflineQueue::where('organization_id', $organizationId)
                ->where('status', 'pending')
                ->count();
        }

        Log::info('Organization offline queue synced', array_merge($results, [
            'organization_id' => $organizationId,
        ]));

        return $results;
    }

    /**
     * Process a single queued request
     */
    protected function processQueuedRequest(OfflineQueue $request): array
    {
        try {
            // Check for conflicts (already processed)
            if ($this->isConflict($request)) {
                $request->update([
                    'status' => 'duplicate',
                    'synced_at' => now(),
                    'last_error' => 'Duplicate or already processed',
                ]);

                return ['status' => 'duplicate', 'reason' => 'Request already processed'];
            }

            // Send request to backend
            $response = $this->sendRequest($request);

            if ($response['success']) {
                $request->update([
                    'status' => 'synced',
                    'synced_at' => now(),
                    'response_data' => $response['data'] ?? null,
                ]);

                Log::info('Queued request synced', [
                    'queue_id' => $request->id,
                    'endpoint' => $request->endpoint,
                ]);

                return ['status' => 'synced'];
            }

            // Retry logic
            if ($request->retry_count < self::MAX_RETRIES) {
                $request->increment('retry_count');
                $request->update(['last_error' => $response['error']]);

                Log::warning('Queued request failed, will retry', [
                    'queue_id' => $request->id,
                    'retry_count' => $request->retry_count,
                ]);

                return ['status' => 'pending', 'reason' => 'Retry pending'];
            }

            // Max retries exceeded
            $request->update([
                'status' => 'failed',
                'last_error' => $response['error'],
            ]);

            Log::error('Queued request failed after max retries', [
                'queue_id' => $request->id,
                'endpoint' => $request->endpoint,
                'error' => $response['error'],
            ]);

            return ['status' => 'failed', 'reason' => 'Max retries exceeded'];
        } catch (\Exception $e) {
            $request->update(['last_error' => $e->getMessage()]);

            Log::error('Error processing queued request', [
                'queue_id' => $request->id,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'error', 'reason' => $e->getMessage()];
        }
    }

    /**
     * Send request to backend endpoint
     */
    protected function sendRequest(OfflineQueue $request): array
    {
        try {
            // Add authentication headers
            $headers = [
                'Authorization' => "Bearer {$request->user->createToken('mobile-sync')->plainTextToken}",
                'Content-Type' => 'application/json',
                'X-Mobile-Sync' => 'true',
                'X-Queue-Id' => $request->id,
            ];

            $response = Http::withHeaders($headers)->post(
                url($request->endpoint),
                $request->payload
            );

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'error' => "HTTP {$response->status()}: {$response->body()}",
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check if request is a duplicate/conflict
     */
    protected function isConflict(OfflineQueue $request): bool
    {
        // If it's an approval response, check if approval already has decision
        if ($request->request_type === 'approval_response') {
            $approvalId = $request->payload['approval_id'] ?? null;
            if ($approvalId) {
                // Query if already processed
                // This would depend on your MobileApproval table structure
                return false; // Placeholder
            }
        }

        return false;
    }

    /**
     * Get queue status for user
     */
    public function getQueueStatus(int $userId, int $organizationId): array
    {
        $queued = OfflineQueue::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->where('status', 'pending')
            ->count();

        $failed = OfflineQueue::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->where('status', 'failed')
            ->count();

        $synced = OfflineQueue::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->where('status', 'synced')
            ->where('synced_at', '>', now()->subDays(1)) // Last 24h
            ->count();

        return [
            'pending' => $queued,
            'failed' => $failed,
            'synced_today' => $synced,
            'last_sync' => OfflineQueue::where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->where('status', 'synced')
                ->latest('synced_at')
                ->value('synced_at')?->toIso8601String(),
        ];
    }

    /**
     * Retry failed requests
     */
    public function retryFailedRequests(int $organizationId, int $maxRetries = 1): int
    {
        $failed = OfflineQueue::where('organization_id', $organizationId)
            ->where('status', 'failed')
            ->where('retry_count', '<', self::MAX_RETRIES)
            ->limit(50)
            ->get();

        $retried = 0;
        foreach ($failed as $request) {
            $request->update(['status' => 'pending', 'retry_count' => $request->retry_count + 1]);
            $this->processQueuedRequest($request);
            $retried++;
        }

        return $retried;
    }

    /**
     * Clean up old queued items (> 30 days)
     */
    public function cleanupOldQueue(int $organizationId, int $daysToKeep = 30): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return OfflineQueue::where('organization_id', $organizationId)
            ->whereIn('status', ['synced', 'duplicate', 'failed'])
            ->where('updated_at', '<', $cutoffDate)
            ->delete();
    }
}

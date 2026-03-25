<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mobile\DeviceTokenService;
use App\Services\Mobile\MobileApprovalService;
use App\Services\Mobile\OfflineQueueService;
use App\Services\Mobile\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MobileController - Mobile-first API endpoints
 *
 * Handles:
 * 1. Device token registration (FCM, APNs)
 * 2. Mobile approval workflows
 * 3. Offline queue syncing
 * 4. Push notification subscriptions
 *
 * All endpoints require auth:sanctum + multi-tenant organization_id scoping
 * All endpoints paginate responses where applicable
 */
class MobileController extends Controller
{
    public function __construct(
        protected DeviceTokenService $deviceService,
        protected MobileApprovalService $approvalService,
        protected OfflineQueueService $queueService,
        protected PushNotificationService $pushService,
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Register or update device token
     *
     * POST /api/mobile/register-device
     * {
     *   "token": "fcm_token_xyz...",
     *   "platform": "android|ios",
     *   "metadata": {
     *     "app_version": "1.0.0",
     *     "os_version": "14.0",
     *     "device_model": "iPhone 13"
     *   }
     * }
     */
    public function registerDevice(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'platform' => 'required|in:ios,android',
                'metadata' => 'nullable|array',
            ]);

            $user = $request->user();
            $organizationId = $user->organization_id;

            // Validate token format
            if (! $this->deviceService->validateToken($validated['token'], $validated['platform'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token format for platform',
                ], 422);
            }

            $device = $this->deviceService->register(
                $user->id,
                $organizationId,
                $validated['token'],
                $validated['platform'],
                $validated['metadata'] ?? []
            );

            Log::info('Device registered', [
                'user_id' => $user->id,
                'platform' => $validated['platform'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully',
                'data' => [
                    'device_id' => $device->id,
                    'platform' => $device->platform,
                    'registered_at' => $device->created_at->toIso8601String(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Error registering device', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register device',
            ], 500);
        }
    }

    /**
     * Get user's active devices
     *
     * GET /api/mobile/devices
     */
    public function getDevices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $devices = $this->deviceService->getActiveDevices(
                $user->id,
                $user->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $devices,
                'count' => count($devices),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching devices', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch devices',
            ], 500);
        }
    }

    /**
     * Deactivate device token (logout)
     *
     * DELETE /api/mobile/devices/{deviceId}
     */
    public function deactivateDevice(Request $request, int $deviceId): JsonResponse
    {
        try {
            $result = $this->deviceService->deactivate($deviceId);

            if (! $result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found',
                ], 404);
            }

            Log::info('Device deactivated', ['device_id' => $deviceId]);

            return response()->json([
                'success' => true,
                'message' => 'Device deactivated successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deactivating device', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate device',
            ], 500);
        }
    }

    /**
     * Get pending approvals for current user
     *
     * GET /api/mobile/approvals
     */
    public function getPendingApprovals(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $approvals = $this->approvalService->getPendingApprovals(
                $user->organization_id,
                $user->id
            );

            return response()->json([
                'success' => true,
                'data' => $approvals,
                'count' => count($approvals),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching pending approvals', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch approvals',
            ], 500);
        }
    }

    /**
     * Approve a request
     *
     * POST /api/mobile/approvals/{approvalId}/approve
     * {
     *   "reason": "Looks good to proceed",
     *   "additional_data": {}
     * }
     */
    public function approveRequest(Request $request, int $approvalId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
                'additional_data' => 'nullable|array',
            ]);

            $user = $request->user();

            // Get approval and verify authorization
            $approval = \App\Models\MobileApproval::where('id', $approvalId)
                ->where('organization_id', $user->organization_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (! $approval->isActionable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval is no longer actionable',
                ], 422);
            }

            $this->approvalService->approve(
                $approval,
                $validated['reason'] ?? null,
                $validated['additional_data'] ?? null
            );

            Log::info('Approval approved', [
                'approval_id' => $approvalId,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully',
                'data' => [
                    'approval_id' => $approval->id,
                    'status' => 'approved',
                    'approved_at' => $approval->fresh()->approved_at?->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval not found',
                ], 404);
            }

            Log::error('Error approving request', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request',
            ], 500);
        }
    }

    /**
     * Reject a request
     *
     * POST /api/mobile/approvals/{approvalId}/reject
     * {
     *   "reason": "Not sufficient information",
     *   "additional_data": {}
     * }
     */
    public function rejectRequest(Request $request, int $approvalId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
                'additional_data' => 'nullable|array',
            ]);

            $user = $request->user();

            // Get approval and verify authorization
            $approval = \App\Models\MobileApproval::where('id', $approvalId)
                ->where('organization_id', $user->organization_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (! $approval->isActionable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval is no longer actionable',
                ], 422);
            }

            $this->approvalService->reject(
                $approval,
                $validated['reason'],
                $validated['additional_data'] ?? null
            );

            Log::info('Approval rejected', [
                'approval_id' => $approvalId,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully',
                'data' => [
                    'approval_id' => $approval->id,
                    'status' => 'rejected',
                    'rejected_at' => $approval->fresh()->rejected_at?->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval not found',
                ], 404);
            }

            Log::error('Error rejecting request', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request',
            ], 500);
        }
    }

    /**
     * Get approval history (paginated)
     *
     * GET /api/mobile/approvals/history?page=1&status=approved
     */
    public function getApprovalHistory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|in:pending,approved,rejected,escalated',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $user = $request->user();
            $perPage = $validated['per_page'] ?? 20;
            $status = $validated['status'] ?? null;

            $history = $this->approvalService->getApprovalHistory(
                $user->organization_id,
                $status,
                $perPage
            );

            return response()->json([
                'success' => true,
                'data' => $history['data'],
                'pagination' => $history['pagination'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching approval history', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch approval history',
            ], 500);
        }
    }

    /**
     * Sync offline queue
     *
     * POST /api/mobile/offline-queue/sync
     */
    public function syncQueue(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $result = $this->queueService->syncUserQueue(
                $user->id,
                $user->organization_id
            );

            Log::info('Offline queue synced', [
                'user_id' => $user->id,
                'synced' => $result['synced'],
                'failed' => $result['failed'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Queue synced',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error syncing queue', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync queue',
            ], 500);
        }
    }

    /**
     * Get offline queue status
     *
     * GET /api/mobile/offline-queue/status
     */
    public function getQueueStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $status = $this->queueService->getQueueStatus(
                $user->id,
                $user->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching queue status', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch queue status',
            ], 500);
        }
    }

    /**
     * Get device statistics for organization (admin only)
     *
     * GET /api/mobile/stats/devices
     */
    public function getDeviceStats(Request $request): JsonResponse
    {
        try {
            // Verify user has admin role
            if ($request->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = $this->deviceService->getOrganizationStats(
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching device stats', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch device statistics',
            ], 500);
        }
    }
}

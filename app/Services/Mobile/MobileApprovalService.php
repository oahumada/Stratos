<?php

namespace App\Services\Mobile;

use App\Models\MobileApproval;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MobileApprovalService - Manages mobile approval workflows
 *
 * Responsibilities:
 * 1. Create approval requests for escalated auto-remediation actions
 * 2. Route approvals to appropriate users (on-call, shift lead, manager)
 * 3. Track approval responses (approve/reject with reason)
 * 4. Execute remediation actions on approval
 * 5. Generate audit trail for compliance
 * 6. Support timeout and escalation (notify manager if no response)
 *
 * Integration Points:
 * - RemediationService: Routes approval requests
 * - PushNotificationService: Sends approval notifications
 * - EventStore/EventDispatcher: Records approval events
 *
 * Approval Levels:
 * - immediate: Auto-executed, approval for record
 * - manual: Requires approval before action
 * - escalation: Manager/CHRO approval required
 *
 * Timeout: 24 hours (configurable via config)
 */
class MobileApprovalService
{
    public function __construct(
        protected PushNotificationService $pushService,
    ) {}

    /**
     * Create approval request for manual remediation
     */
    public function createApprovalRequest(
        int $organizationId,
        int $requesterId,
        int $approverId,
        string $requestType, // escalated_action, manual_approval, policy_exception
        string $title,
        string $description,
        array $context, // details about what requires approval
        string $severity = 'warning' // info, warning, critical
    ): MobileApproval {
        try {
            $approval = MobileApproval::create([
                'organization_id' => $organizationId,
                'user_id' => $approverId,
                'requester_id' => $requesterId,
                'request_type' => $requestType,
                'title' => $title,
                'description' => $description,
                'context' => $context,
                'severity' => $severity,
                'status' => 'pending',
                'requested_at' => now(),
                'expires_at' => now()->addHours(config('mobile.approval_timeout_hours', 24)),
            ]);

            Log::info('Mobile approval request created', [
                'approval_id' => $approval->id,
                'requester_id' => $requesterId,
                'approver_id' => $approverId,
                'type' => $requestType,
            ]);

            // Send push notification to approver
            $this->notifyApprover($approval);

            return $approval;
        } catch (\Exception $e) {
            Log::error('Error creating approval request', [
                'error' => $e->getMessage(),
                'organization_id' => $organizationId,
            ]);

            throw $e;
        }
    }

    /**
     * Approve the request
     */
    public function approve(
        MobileApproval $approval,
        ?string $reason = null,
        ?array $additionalData = null
    ): bool {
        try {
            DB::beginTransaction();

            $approval->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approver_notes' => $reason,
                'approval_data' => $additionalData,
            ]);

            DB::commit();

            Log::info('Mobile approval approved', [
                'approval_id' => $approval->id,
                'approver_id' => $approval->user_id,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving request', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Reject the request
     */
    public function reject(
        MobileApproval $approval,
        string $reason,
        ?array $additionalData = null
    ): bool {
        try {
            DB::beginTransaction();

            $approval->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'approval_data' => $additionalData,
            ]);

            DB::commit();

            Log::info('Mobile approval rejected', [
                'approval_id' => $approval->id,
                'approver_id' => $approval->user_id,
                'reason' => $reason,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting request', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get pending approvals for a user
     */
    public function getPendingApprovals(int $organizationId, int $userId): array
    {
        return MobileApproval::where('organization_id', $organizationId)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderBy('requested_at', 'desc')
            ->get()
            ->map(fn ($approval) => [
                'id' => $approval->id,
                'request_type' => $approval->request_type,
                'title' => $approval->title,
                'description' => $approval->description,
                'severity' => $approval->severity,
                'requested_at' => $approval->requested_at->toIso8601String(),
                'expires_at' => $approval->expires_at->toIso8601String(),
                'context' => $approval->context,
            ])
            ->toArray();
    }

    /**
     * Get approval history for organization
     */
    public function getApprovalHistory(
        int $organizationId,
        ?string $status = null,
        int $perPage = 20
    ): array {
        $query = MobileApproval::where('organization_id', $organizationId);

        if ($status) {
            $query->where('status', $status);
        }

        $paginated = $query
            ->orderBy('requested_at', 'desc')
            ->paginate($perPage);

        return [
            'data' => $paginated->getCollection()->map(fn ($approval) => [
                'id' => $approval->id,
                'request_type' => $approval->request_type,
                'title' => $approval->title,
                'status' => $approval->status,
                'severity' => $approval->severity,
                'requester_name' => optional($approval->requester)->full_name,
                'approver_name' => optional($approval->user)->full_name,
                'requested_at' => $approval->requested_at->toIso8601String(),
                'responded_at' => optional($approval->approved_at ?? $approval->rejected_at)?->toIso8601String(),
            ])->toArray(),
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ];
    }

    /**
     * Check for expired approvals and escalate
     */
    public function escalateExpiredApprovals(int $organizationId): int
    {
        $expired = MobileApproval::where('organization_id', $organizationId)
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $approval) {
            // Mark as escalated
            $approval->update(['status' => 'escalated']);

            // Find manager to escalate to
            $manager = $this->findManagerForEscalation($approval->user_id);
            if ($manager) {
                $this->createApprovalRequest(
                    $organizationId,
                    $approval->requester_id,
                    $manager->id,
                    'escalation',
                    "ESCALATED: {$approval->title}",
                    'Previous approval expired. This requires manager review.',
                    $approval->context,
                    'critical'
                );

                Log::info('Approval escalated', [
                    'original_approval_id' => $approval->id,
                    'escalated_to' => $manager->id,
                ]);
            }
        }

        return count($expired);
    }

    /**
     * Notify approver of pending approval
     */
    protected function notifyApprover(MobileApproval $approval): void
    {
        try {
            $this->pushService->sendApprovalNotification(
                $approval,
                "Approval Required: {$approval->title}",
                $approval->description
            );
        } catch (\Exception $e) {
            Log::warning('Failed to send approval notification', [
                'approval_id' => $approval->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Find manager for escalation (can be customized per organization)
     */
    protected function findManagerForEscalation(int $userId): ?User
    {
        $user = User::find($userId);

        if (! $user) {
            return null;
        }

        // Try to find manager in same department
        // This is a placeholder - customize based on org structure
        return User::where('organization_id', $user->organization_id)
            ->where('role', 'manager')
            ->first();
    }

    /**
     * Archive old approvals (configurable retention)
     */
    public function archiveOldApprovals(int $organizationId, int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return MobileApproval::where('organization_id', $organizationId)
            ->whereIn('status', ['approved', 'rejected', 'escalated'])
            ->where('updated_at', '<', $cutoffDate)
            ->update(['archived_at' => now()]);
    }
}

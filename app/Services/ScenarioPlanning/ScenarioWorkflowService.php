<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use App\Models\ApprovalRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * ScenarioWorkflowService
 *
 * Manages the complete workflow lifecycle for scenario planning:
 * draft → pending_approval → approved → active → in_progress → completed
 *
 * Handles:
 * - Approval request creation and tracking
 * - State machine transitions
 * - Approver determination (approval matrix)
 * - Notifications to stakeholders
 * - Execution plan generation
 * - Audit trail logging
 */
class ScenarioWorkflowService
{
    /**
     * Valid decision states
     */
    public const DECISION_STATES = [
        'draft',
        'pending_approval',
        'approved',
        'active',
        'archived',
    ];

    /**
     * Valid execution states
     */
    public const EXECUTION_STATES = [
        'planned',
        'in_progress',
        'paused',
        'completed',
    ];

    /**
     * Submit scenario for approval
     *
     * Transitions: draft → pending_approval
     * Creates ApprovalRequest with identified approvers
     * Freezes scenario (no edits allowed)
     * Notifies approvers
     */
    public function submitForApproval(int $scenarioId, int $submittedById, ?string $notes = null): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Verify current state
        if ($scenario->decision_status !== 'draft') {
            return [
                'status' => 'error',
                'message' => "Cannot submit scenario in '{$scenario->decision_status}' state",
            ];
        }

        // Verify user has permission
        $user = User::findOrFail($submittedById);
        if (!$scenario->canBeEdited($user)) {
            return [
                'status' => 'error',
                'message' => 'You do not have permission to submit this scenario',
            ];
        }

        // Determine approvers based on approval matrix
        $approvers = $this->buildApprovalMatrix($scenario);

        if (empty($approvers)) {
            return [
                'status' => 'error',
                'message' => 'No approvers determined for this scenario',
            ];
        }

        // Update scenario state
        $scenario->update([
            'decision_status' => 'pending_approval',
            'submitted_by' => $submittedById,
            'submitted_at' => now(),
        ]);

        // Create ApprovalRequest for each approver
        $approvalRequests = [];
        foreach ($approvers as $approverId) {
            $approvalRequest = ApprovalRequest::create([
                'token' => (string) Str::uuid(),
                'approvable_type' => Scenario::class,
                'approvable_id' => $scenario->id,
                'approver_id' => $approverId,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
                'signature_data' => [
                    'submission_notes' => $notes,
                    'submitted_by' => $submittedById,
                    'scenario_snapshot' => $this->captureScenarioSnapshot($scenario),
                ],
            ]);

            $approvalRequests[] = $approvalRequest;
        }

        // Send notifications
        $this->notifyApprovers($scenario, $approvers, $submittedById);

        return [
            'status' => 'success',
            'message' => 'Scenario submitted for approval',
            'scenario_id' => $scenario->id,
            'decision_status' => 'pending_approval',
            'approvals_required' => count($approvers),
            'approval_requests' => $approvalRequests->map(fn($ar) => [
                'id' => $ar->id,
                'token' => $ar->token,
                'approver_id' => $ar->approver_id,
                'status' => $ar->status,
                'expires_at' => $ar->expires_at,
            ])->toArray(),
        ];
    }

    /**
     * Approve scenario
     *
     * Transitions: pending_approval → approved (if all approvers approve)
     * Creates digital signature
     * Advances workflow state
     */
    public function approve(int $approvalRequestId, int $approverId, ?string $notes = null): array
    {
        $approvalRequest = ApprovalRequest::findOrFail($approvalRequestId);

        // Verify pending
        if ($approvalRequest->status !== 'pending') {
            return [
                'status' => 'error',
                'message' => "Approval request is already '{$approvalRequest->status}'",
            ];
        }

        // Verify approver matches
        if ($approvalRequest->approver_id !== $approverId) {
            return [
                'status' => 'error',
                'message' => 'Only the assigned approver can act on this request',
            ];
        }

        // Verify not expired
        if ($approvalRequest->expires_at && $approvalRequest->expires_at < now()) {
            return [
                'status' => 'error',
                'message' => 'Approval request has expired',
            ];
        }

        // Get scenario
        $scenario = $approvalRequest->approvable;
        if (!$scenario instanceof Scenario) {
            return [
                'status' => 'error',
                'message' => 'Invalid approvable type',
            ];
        }

        // Update approval request
        $didgitalSignature = $this->generateDigitalSignature([
            'scenario_id' => $scenario->id,
            'approver_id' => $approverId,
            'timestamp' => now()->toIso8601String(),
            'notes' => $notes,
        ]);

        $approvalRequest->update([
            'status' => 'approved',
            'signed_at' => now(),
            'signature_data' => array_merge($approvalRequest->signature_data ?? [], [
                'approved_by' => $approverId,
                'approved_at' => now(),
                'approval_notes' => $notes,
                'digital_signature' => $didgitalSignature,
            ]),
        ]);

        // Check if all approvals are complete
        $allApproved = $this->areAllApprovalsComplete($scenario->id);

        if ($allApproved) {
            // Transition scenario to approved
            $scenario->update([
                'decision_status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            // Notify stakeholders
            $this->notifyApprovalComplete($scenario, $approverId);

            return [
                'status' => 'success',
                'message' => 'All approvals complete - scenario is now approved',
                'scenario_id' => $scenario->id,
                'decision_status' => 'approved',
                'next_action' => 'Can now be activated for execution',
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Approval recorded',
            'scenario_id' => $scenario->id,
            'approvals_remaining' => $this->countPendingApprovals($scenario->id),
        ];
    }

    /**
     * Reject scenario approval
     *
     * Transitions: pending_approval → draft
     * Unlocks scenario for editing
     * Sets rejection reason
     */
    public function reject(int $approvalRequestId, int $approverId, string $reason): array
    {
        $approvalRequest = ApprovalRequest::findOrFail($approvalRequestId);

        // Verify pending
        if ($approvalRequest->status !== 'pending') {
            return [
                'status' => 'error',
                'message' => "Approval request is already '{$approvalRequest->status}'",
            ];
        }

        // Verify approver matches
        if ($approvalRequest->approver_id !== $approverId) {
            return [
                'status' => 'error',
                'message' => 'Only the assigned approver can reject',
            ];
        }

        // Get scenario
        $scenario = $approvalRequest->approvable;
        if (!$scenario instanceof Scenario) {
            return [
                'status' => 'error',
                'message' => 'Invalid approvable type',
            ];
        }

        // Update approval request
        $approvalRequest->update([
            'status' => 'rejected',
            'signature_data' => array_merge($approvalRequest->signature_data ?? [], [
                'rejected_by' => $approverId,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]),
        ]);

        // Revert scenario to draft
        $scenario->update([
            'decision_status' => 'draft',
            'rejected_by' => $approverId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Reject all other pending approvals for this submission
        ApprovalRequest::where('approvable_id', $scenario->id)
            ->where('approvable_type', Scenario::class)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Notify owner
        $this->notifyRejected($scenario, $approverId, $reason);

        return [
            'status' => 'success',
            'message' => 'Scenario rejected and reverted to draft',
            'scenario_id' => $scenario->id,
            'decision_status' => 'draft',
            'rejection_reason' => $reason,
            'next_action' => 'Make requested changes and submit again',
        ];
    }

    /**
     * Activate approved scenario
     *
     * Transitions: approved → active
     * Generates execution plan
     * Marks as ready for execution
     */
    public function activate(int $scenarioId, int $activatedById): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Verify state
        if ($scenario->decision_status !== 'approved') {
            return [
                'status' => 'error',
                'message' => "Scenario must be 'approved' to activate (current: '{$scenario->decision_status}')",
            ];
        }

        // Generate execution plan
        $executionPlan = $this->generateExecutionPlan($scenario);

        // Transition to active
        $scenario->update([
            'decision_status' => 'active',
            'execution_status' => 'planned',
        ]);

        // Log activation event
        $this->logWorkflowEvent($scenario, 'activated', [
            'activated_by' => $activatedById,
            'execution_plan_id' => $executionPlan['id'] ?? null,
        ]);

        return [
            'status' => 'success',
            'message' => 'Scenario activated and ready for execution',
            'scenario_id' => $scenario->id,
            'decision_status' => 'active',
            'execution_plan' => $executionPlan,
        ];
    }

    /**
     * Start execution of active scenario
     *
     * Transitions: planned → in_progress
     */
    public function startExecution(int $scenarioId, int $startedById): array
    {
        $scenario = Scenario::findOrFail($scenarioId);

        // Verify state
        if ($scenario->decision_status !== 'active' || $scenario->execution_status !== 'planned') {
            return [
                'status' => 'error',
                'message' => 'Scenario must be active and planned to start execution',
            ];
        }

        $scenario->update([
            'execution_status' => 'in_progress',
        ]);

        $this->logWorkflowEvent($scenario, 'execution_started', ['started_by' => $startedById]);

        return [
            'status' => 'success',
            'message' => 'Execution started',
            'scenario_id' => $scenario->id,
            'execution_status' => 'in_progress',
        ];
    }

    /**
     * Build approval matrix
     *
     * Determines who needs to approve this scenario based on:
     * - Financial impact (budget threshold)
     * - Scope (number of people affected)
     * - Risk level
     */
    public function buildApprovalMatrix(Scenario $scenario): array
    {
        $approvers = [];

        // Always need submission approver (usually CHRO or strategy lead)
        // For now, return admin users (1) - in production, would query from roles/permissions
        $adminUsers = User::role('admin')->pluck('id')->toArray();

        // Add organization strategy lead if exists
        //$strategyLead = User::whereHas('roles', function($q) {
        //    $q->where('name', 'strategy_leader');
        //})->first();

        return array_unique(array_merge($adminUsers));
    }

    /**
     * Check if all approvals are complete
     */
    private function areAllApprovalsComplete(int $scenarioId): bool
    {
        $pendingCount = ApprovalRequest::where('approvable_id', $scenarioId)
            ->where('approvable_type', Scenario::class)
            ->where('status', 'pending')
            ->count();

        return $pendingCount === 0 && ApprovalRequest::where('approvable_id', $scenarioId)
            ->where('approvable_type', Scenario::class)
            ->where('status', 'approved')
            ->count() > 0;
    }

    /**
     * Count pending approvals
     */
    private function countPendingApprovals(int $scenarioId): int
    {
        return ApprovalRequest::where('approvable_id', $scenarioId)
            ->where('approvable_type', Scenario::class)
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Generate execution plan from scenario
     *
     * Creates phases, milestones, and task list
     */
    private function generateExecutionPlan(Scenario $scenario): array
    {
        return [
            'id' => Str::uuid(),
            'scenario_id' => $scenario->id,
            'timeline_weeks' => $scenario->time_horizon_weeks ?? 26,
            'phases' => $this->generatePhases($scenario),
            'milestones' => $this->generateMilestones($scenario),
            'tasks' => $this->generateTasks($scenario),
            'generated_at' => now(),
        ];
    }

    /**
     * Generate phases from scenario timeline
     */
    private function generatePhases(Scenario $scenario): array
    {
        return [
            [
                'phase' => 1,
                'name' => 'Assessment & Planning',
                'start_week' => 1,
                'end_week' => 4,
                'status' => 'planned',
            ],
            [
                'phase' => 2,
                'name' => 'Capability Building',
                'start_week' => 5,
                'end_week' => 12,
                'status' => 'planned',
            ],
            [
                'phase' => 3,
                'name' => 'Execution & Transition',
                'start_week' => 13,
                'end_week' => 20,
                'status' => 'planned',
            ],
            [
                'phase' => 4,
                'name' => 'Stabilization & Review',
                'start_week' => 21,
                'end_week' => $scenario->time_horizon_weeks ?? 26,
                'status' => 'planned',
            ],
        ];
    }

    /**
     * Generate milestones
     */
    private function generateMilestones(Scenario $scenario): array
    {
        return [
            ['milestone' => 'Kickoff', 'week' => 1, 'status' => 'planned'],
            ['milestone' => 'Assessment Complete', 'week' => 4, 'status' => 'planned'],
            ['milestone' => 'Pilot Launch', 'week' => 8, 'status' => 'planned'],
            ['milestone' => 'Full Rollout', 'week' => 14, 'status' => 'planned'],
            ['milestone' => 'Go-Live', 'week' => $scenario->time_horizon_weeks ?? 26, 'status' => 'planned'],
        ];
    }

    /**
     * Generate tasks from scenario
     */
    private function generateTasks(Scenario $scenario): array
    {
        return [
            ['task_id' => 1, 'name' => 'Stakeholder Alignment', 'phase' => 1, 'assigned_to' => null, 'status' => 'pending'],
            ['task_id' => 2, 'name' => 'Resource Planning', 'phase' => 1, 'assigned_to' => null, 'status' => 'pending'],
            ['task_id' => 3, 'name' => 'Training Preparation', 'phase' => 2, 'assigned_to' => null, 'status' => 'pending'],
            ['task_id' => 4, 'name' => 'Capability Delivery', 'phase' => 2, 'assigned_to' => null, 'status' => 'pending'],
            ['task_id' => 5, 'name' => 'Execution Kickoff', 'phase' => 3, 'assigned_to' => null, 'status' => 'pending'],
            ['task_id' => 6, 'name' => 'Transition Management', 'phase' => 3, 'assigned_to' => null, 'status' => 'pending'],
            ['task_id' => 7, 'name' => 'Lessons Learned', 'phase' => 4, 'assigned_to' => null, 'status' => 'pending'],
        ];
    }

    /**
     * Capture scenario snapshot for audit trail
     */
    private function captureScenarioSnapshot(Scenario $scenario): array
    {
        return [
            'id' => $scenario->id,
            'name' => $scenario->name,
            'description' => $scenario->description,
            'iq' => $scenario->iq ?? null,
            'status' => $scenario->status,
            'financial_impact' => $scenario->financial_impact ?? null,
            'captured_at' => now(),
        ];
    }

    /**
     * Generate digital signature for approval
     */
    private function generateDigitalSignature(array $data): string
    {
        $payload = json_encode($data);

        return hash_hmac('sha256', $payload, config('app.key'), false);
    }

    /**
     * Notify approvers of approval request
     */
    private function notifyApprovers(Scenario $scenario, array $approverIds, int $submittedById): void
    {
        foreach ($approverIds as $approverId) {
            // Create in-app notification
            // In production: queue email, Slack notification, etc.
            $user = User::find($approverId);
            if ($user) {
                // TODO: Implement notification dispatch
                // Notification::send($user, new ScenarioApprovalRequested($scenario));
            }
        }
    }

    /**
     * Notify when approval is complete
     */
    private function notifyApprovalComplete(Scenario $scenario, int $approverId): void
    {
        // TODO: Implement notification
    }

    /**
     * Notify when approval is rejected
     */
    private function notifyRejected(Scenario $scenario, int $approverId, string $reason): void
    {
        // TODO: Implement notification
    }

    /**
     * Log workflow event for audit trail
     */
    private function logWorkflowEvent(Scenario $scenario, string $event, array $data): void
    {
        // TODO: Log to audit table or event store
        // EventStore::create([
        //     'event_name' => "scenario.$event",
        //     'aggregate_type' => 'scenarios',
        //     'aggregate_id' => $scenario->id,
        //     'organization_id' => $scenario->organization_id,
        //     'payload' => $data,
        // ]);
    }

    /**
     * Check if scenario can be edited
     *
     * Cannot edit if pending_approval or approved
     */
    public function canScenarioBeEdited(Scenario $scenario): bool
    {
        return in_array($scenario->decision_status, ['draft', 'rejected']);
    }

    /**
     * Check if user can submit scenario
     */
    public function canUserSubmitScenario(Scenario $scenario, User $user): bool
    {
        // Creator or org admin can submit
        return $scenario->created_by === $user->id || $user->hasRole('admin');
    }
}

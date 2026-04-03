<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Models\ApprovalRequest;
use App\Models\LmsEnrollment;
use App\Models\DevelopmentAction;

class SystemNotificationService
{
    public function __construct(protected NotificationDispatcher $dispatcher) {}

    /**
     * Notify approver of pending approval
     */
    public function notifyApprovalPending(ApprovalRequest $approval, User $approver): void
    {
        $this->dispatcher->dispatchToUser(
            $approver,
            "New Approval Required: {$approval->title}",
            "You have a new approval request from {$approval->requester?->name}: {$approval->description}",
            [
                'approval_id' => $approval->id,
                'approver_id' => $approver->id,
                'type' => 'approval_pending',
            ]
        );
    }

    /**
     * Notify requester of approval decision
     */
    public function notifyApprovalDecision(ApprovalRequest $approval, bool $approved): void
    {
        $requester = $approval->requester;
        if (! $requester) {
            return;
        }

        $status = $approved ? '✅ Approved' : '❌ Rejected';
        $this->dispatcher->dispatchToUser(
            $requester,
            "Approval {$status}: {$approval->title}",
            "Your approval request has been {($approved ? 'approved' : 'rejected')}.\n\nReason: {$approval->decision_reason}",
            [
                'approval_id' => $approval->id,
                'type' => 'approval_decision',
                'approved' => $approved,
            ]
        );
    }

    /**
     * Notify user of new course enrollment
     */
    public function notifyCourseEnrolled(LmsEnrollment $enrollment): void
    {
        $user = $enrollment->user;
        $course = $enrollment->course;

        if (! $user || ! $course) {
            return;
        }

        $this->dispatcher->dispatchToUser(
            $user,
            "New Course Assigned: {$course->name}",
            "You have been enrolled in the course **{$course->name}**.\n\nDue date: {$enrollment->due_date?->format('M d, Y') ?? 'No deadline'}",
            [
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'type' => 'course_enrolled',
            ]
        );
    }

    /**
     * Notify of development action assignment
     */
    public function notifyDevelopmentActionAssigned(DevelopmentAction $action): void
    {
        $user = $action->user;
        if (! $user) {
            return;
        }

        $this->dispatcher->dispatchToUser(
            $user,
            "Development Action Assigned: {$action->name}",
            "A new development action has been assigned to you:\n\n**{$action->name}**\n\nTarget completion: {$action->target_completion_date?->format('M d, Y') ?? 'TBD'}",
            [
                'action_id' => $action->id,
                'type' => 'dev_action_assigned',
            ]
        );
    }

    /**
     * Notify manager of overdue development action
     */
    public function notifyDevelopmentActionOverdue(DevelopmentAction $action): void
    {
        $manager = $action->user?->manager;
        if (! $manager) {
            return;
        }

        $this->dispatcher->dispatchToUser(
            $manager,
            "Overdue Development Action: {$action->name}",
            "Development action for {$action->user?->name} is now overdue:\n\n**{$action->name}**\n\nDue: {$action->target_completion_date?->format('M d, Y')}",
            [
                'action_id' => $action->id,
                'user_id' => $action->user_id,
                'type' => 'dev_action_overdue',
            ]
        );
    }

    /**
     * Notify on role change/promotion
     */
    public function notifyRoleChange(User $user, string $oldRole, string $newRole): void
    {
        $this->dispatcher->dispatchToUser(
            $user,
            "Role Change: {$oldRole} → {$newRole}",
            "Your role has been updated from **{$oldRole}** to **{$newRole}**.\n\nPlease review your new responsibilities and permissions.",
            [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'type' => 'role_changed',
            ]
        );
    }

    /**
     * Broadcast system announcement to organization
     */
    public function broadcastSystemAnnouncement(int $organizationId, string $title, string $message): void
    {
        $this->dispatcher->dispatchToOrganization(
            $organizationId,
            $title,
            $message,
            ['type' => 'system_announcement']
        );
    }
}

<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use App\Models\ApprovalRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * ScenarioNotificationService - Manages notifications for scenario approvals
 *
 * Responsibilities:
 * 1. Send approval request notifications to stakeholders
 * 2. Send approval grant/rejection notifications
 * 3. Send scenario activation notifications
 * 4. Support multiple channels (Email, Slack, In-App)
 * 5. Track notification delivery and engagement
 */
class ScenarioNotificationService
{
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SLACK = 'slack';
    public const CHANNEL_IN_APP = 'in_app';

    /**
     * Notify approvers about pending approval request
     */
    public function notifyApprovalRequest(ApprovalRequest $approvalRequest, array $approverIds): array
    {
        $scenario = $approvalRequest->approvable; // Polymorphic relationship
        $submittedBy = $approvalRequest->submitted_by; // User who submitted

        if (!$submittedBy || !$scenario) {
            Log::warning('Cannot notify: missing submitted_by or scenario', [
                'approval_request_id' => $approvalRequest->id,
            ]);
            return [];
        }

        $results = [];

        foreach ($approverIds as $approverId) {
            $approver = User::find($approverId);
            if (!$approver) {
                continue;
            }

            $channels = $this->buildNotificationChannels($approver);

            if (in_array(self::CHANNEL_EMAIL, $channels)) {
                $results[] = $this->sendApprovalEmail($approver, $scenario, $approvalRequest, $submittedBy);
            }

            if (in_array(self::CHANNEL_SLACK, $channels)) {
                $results[] = $this->sendApprovalSlack($approver, $scenario, $approvalRequest, $submittedBy);
            }

            if (in_array(self::CHANNEL_IN_APP, $channels)) {
                $this->createInAppNotification($approver, $scenario, 'approval_requested');
            }

            // Track notification sent
            $scenario->update(['notifications_sent_at' => now()]);
        }

        return $results;
    }

    /**
     * Notify approvers and scenario creator about approval granted
     */
    public function notifyApprovalGranted(ApprovalRequest $approvalRequest, User $approver): void
    {
        $scenario = $approvalRequest->approvable;
        $creator = $scenario->created_by_user; // Assuming relationship exists

        if (!$creator) {
            return;
        }

        $message = "{$approver->name} has approved your scenario '{$scenario->name}'";

        // Notify creator
        $channels = $this->buildNotificationChannels($creator);
        if (in_array(self::CHANNEL_EMAIL, $channels)) {
            $this->sendApprovalGrantedEmail($creator, $scenario, $approver);
        }

        if (in_array(self::CHANNEL_SLACK, $channels)) {
            $this->sendSlackNotification(
                $creator,
                "✅ Approval Granted: {$message}",
                'approval_granted'
            );
        }

        if (in_array(self::CHANNEL_IN_APP, $channels)) {
            $this->createInAppNotification($creator, $scenario, 'approval_granted');
        }

        Log::info("Approval granted notification sent for scenario {$scenario->id}");
    }

    /**
     * Notify approvers and scenario creator about approval rejection
     */
    public function notifyApprovalRejected(ApprovalRequest $approvalRequest, User $rejector, string $reason): void
    {
        $scenario = $approvalRequest->approvable;
        $creator = $scenario->created_by_user;

        if (!$creator) {
            return;
        }

        $message = "{$rejector->name} rejected your scenario approval: {$reason}";

        // Notify creator
        $channels = $this->buildNotificationChannels($creator);
        if (in_array(self::CHANNEL_EMAIL, $channels)) {
            $this->sendApprovalRejectedEmail($creator, $scenario, $rejector, $reason);
        }

        if (in_array(self::CHANNEL_SLACK, $channels)) {
            $this->sendSlackNotification(
                $creator,
                "❌ Approval Rejected: {$message}",
                'approval_rejected'
            );
        }

        if (in_array(self::CHANNEL_IN_APP, $channels)) {
            $this->createInAppNotification($creator, $scenario, 'approval_rejected');
        }

        Log::info("Approval rejection notification sent for scenario {$scenario->id}");
    }

    /**
     * Notify stakeholders about scenario activation
     */
    public function notifyScenarioActivated(Scenario $scenario, array $stakeholderIds): void
    {
        $executionPlan = $scenario->execution_plan ?? []; // JSON data

        foreach ($stakeholderIds as $stakeholderId) {
            $stakeholder = User::find($stakeholderId);
            if (!$stakeholder) {
                continue;
            }

            $channels = $this->buildNotificationChannels($stakeholder);

            if (in_array(self::CHANNEL_EMAIL, $channels)) {
                $this->sendScenarioActivatedEmail($stakeholder, $scenario, $executionPlan);
            }

            if (in_array(self::CHANNEL_SLACK, $channels)) {
                $this->sendSlackNotification(
                    $stakeholder,
                    "🚀 Scenario Activated: '{$scenario->name}' is now in execution phase",
                    'scenario_activated'
                );
            }

            if (in_array(self::CHANNEL_IN_APP, $channels)) {
                $this->createInAppNotification($stakeholder, $scenario, 'scenario_activated');
            }
        }

        Log::info("Scenario activation notification sent for scenario {$scenario->id}");
    }

    /**
     * Send approval request email
     */
    private function sendApprovalEmail(
        User $approver,
        Scenario $scenario,
        ApprovalRequest $approvalRequest,
        User $submittedBy
    ): array {
        try {
            $approvalLink = route('approve-scenario', [
                'token' => $approvalRequest->token ?? base64_encode($approvalRequest->id),
            ]);

            Mail::send('emails.approvals.approval-request', [
                'approver_name' => $approver->name,
                'scenario_name' => $scenario->name,
                'submitted_by' => $submittedBy->name,
                'approval_link' => $approvalLink,
                'rejection_link' => route('reject-scenario', [
                    'token' => $approvalRequest->token ?? base64_encode($approvalRequest->id),
                ]),
                'organization_name' => $approver->organization->name ?? 'Stratos',
            ], function ($message) use ($approver, $scenario) {
                $message->to($approver->email)
                    ->subject("Action Required: Approval Request for '{$scenario->name}'");
            });

            return [
                'success' => true,
                'channel' => self::CHANNEL_EMAIL,
                'recipient' => $approver->email,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send approval email', [
                'approver_id' => $approver->id,
                'scenario_id' => $scenario->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'channel' => self::CHANNEL_EMAIL,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send approval granted email
     */
    private function sendApprovalGrantedEmail(User $creator, Scenario $scenario, User $approver): void
    {
        try {
            Mail::send('emails.approvals.approval-granted', [
                'creator_name' => $creator->name,
                'scenario_name' => $scenario->name,
                'approver_name' => $approver->name,
                'next_steps' => 'Your scenario will soon proceed to execution.',
            ], function ($message) use ($creator, $scenario) {
                $message->to($creator->email)
                    ->subject("✅ Approval Granted for '{$scenario->name}'");
            });
        } catch (\Exception $e) {
            Log::error('Failed to send approval granted email', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send approval rejected email
     */
    private function sendApprovalRejectedEmail(
        User $creator,
        Scenario $scenario,
        User $rejector,
        string $reason
    ): void {
        try {
            Mail::send('emails.approvals.approval-rejected', [
                'creator_name' => $creator->name,
                'scenario_name' => $scenario->name,
                'rejector_name' => $rejector->name,
                'rejection_reason' => $reason,
                'next_steps' => 'You can view the feedback and revise your scenario.',
                'revise_link' => route('scenario-edit', ['scenario' => $scenario->id]),
            ], function ($message) use ($creator, $scenario) {
                $message->to($creator->email)
                    ->subject("❌ Approval Rejected for '{$scenario->name}'");
            });
        } catch (\Exception $e) {
            Log::error('Failed to send approval rejected email', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send scenario activated email
     */
    private function sendScenarioActivatedEmail(User $stakeholder, Scenario $scenario, array $executionPlan): void
    {
        try {
            Mail::send('emails.approvals.scenario-activated', [
                'stakeholder_name' => $stakeholder->name,
                'scenario_name' => $scenario->name,
                'phases_count' => count($executionPlan['phases'] ?? []),
                'timeline_weeks' => $executionPlan['total_weeks'] ?? 26,
                'tracking_link' => route('scenario-show', ['scenario' => $scenario->id]),
            ], function ($message) use ($stakeholder, $scenario) {
                $message->to($stakeholder->email)
                    ->subject("🚀 Scenario Activated: '{$scenario->name}'");
            });
        } catch (\Exception $e) {
            Log::error('Failed to send scenario activated email', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send Slack notification
     */
    public function sendSlackNotification(User $user, string $message, string $event_type): bool
    {
        try {
            $webhookUrl = config('services.slack.approval_webhook');
            if (!$webhookUrl) {
                Log::debug('Slack webhook not configured');
                return false;
            }

            $payload = [
                'text' => $message,
                'attachments' => [
                    [
                        'color' => $this->getSlackColor($event_type),
                        'fields' => [
                            [
                                'title' => 'User',
                                'value' => $user->name,
                                'short' => true,
                            ],
                            [
                                'title' => 'Organization',
                                'value' => $user->organization->name ?? 'N/A',
                                'short' => true,
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::post($webhookUrl, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send Slack notification', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Create in-app notification
     */
    private function createInAppNotification(User $user, Scenario $scenario, string $type): void
    {
        try {
            // Create or use Notification model if available
            // For now, just log it
            Log::info("In-app notification created", [
                'user_id' => $user->id,
                'scenario_id' => $scenario->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create in-app notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Build notification channels for a user
     * (Can be extended to check user preferences)
     */
    private function buildNotificationChannels(User $user): array
    {
        // Default: Email + In-App (Slack optional if webhook configured)
        $channels = [self::CHANNEL_EMAIL, self::CHANNEL_IN_APP];

        if (config('services.slack.approval_webhook')) {
            $channels[] = self::CHANNEL_SLACK;
        }

        return $channels;
    }

    /**
     * Get Slack message color by event type
     */
    private function getSlackColor(string $event_type): string
    {
        return match($event_type) {
            'approval_requested' => '#0099ff', // Blue
            'approval_granted' => '#36a64f',   // Green
            'approval_rejected' => '#ff3333',  // Red
            'scenario_activated' => '#ffa500', // Orange
            default => '#cccccc',              // Gray
        };
    }

    /**
     * Resend notification for approval request
     */
    public function resendNotification(ApprovalRequest $approvalRequest, array $channels = []): array
    {
        $scenario = $approvalRequest->approvable;
        $approverIds = json_decode($approvalRequest->approver_ids ?? '[]', true) ?? [];

        if (empty($channels)) {
            $channels = [self::CHANNEL_EMAIL, self::CHANNEL_IN_APP];
        }

        $results = [];
        foreach ($approverIds as $approverId) {
            $approver = User::find($approverId);
            if (!$approver) {
                continue;
            }

            if (in_array(self::CHANNEL_EMAIL, $channels)) {
                $results[] = $this->sendApprovalEmail(
                    $approver,
                    $scenario,
                    $approvalRequest,
                    $scenario->created_by_user
                );
            }
        }

        // Track resend
        $scenario->update(['last_notification_resent_at' => now()]);

        Log::info("Notification resent for approval request {$approvalRequest->id}");

        return $results;
    }
}

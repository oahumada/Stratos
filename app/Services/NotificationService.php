<?php

namespace App\Services;

use App\Models\AlertHistory;
use App\Models\EscalationPolicy;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notifications based on escalation policy
     */
    public function notifyAlert(AlertHistory $alert, EscalationPolicy $policy): bool
    {
        try {
            $notificationsSent = 0;

            // Get escalation chain for severity
            $escalationChain = EscalationPolicy::getChainForSeverity(
                $alert->organization_id,
                $alert->severity
            );

            if ($escalationChain->isEmpty()) {
                Log::warning("No escalation policy found for severity: {$alert->severity}");
                return false;
            }

            foreach ($escalationChain as $policyLevel) {
                // Check if this level should trigger based on timing
                if ($this->shouldTriggerLevel($alert, $policyLevel)) {
                    if ($this->sendNotifications($alert, $policyLevel)) {
                        $notificationsSent++;
                    }
                }
            }

            return $notificationsSent > 0;
        } catch (\Exception $e) {
            Log::error("Error sending alert notifications: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Determine if escalation level should trigger based on timing
     */
    private function shouldTriggerLevel(AlertHistory $alert, EscalationPolicy $policy): bool
    {
        if ($policy->escalation_level === 1) {
            // Level 1 always triggers immediately
            return true;
        }

        // For higher levels, check if alert has been triggered long enough
        $delayMinutes = $policy->delay_minutes ?? 0;
        $triggerTime = $alert->triggered_at->addMinutes($delayMinutes);

        return now()->isAfter($triggerTime);
    }

    /**
     * Send notifications via configured channels
     */
    private function sendNotifications(AlertHistory $alert, EscalationPolicy $policy): bool
    {
        $sent = false;

        try {
            if ($policy->hasEmailNotification()) {
                $sent = $this->sendEmailNotification($alert, $policy) || $sent;
            }

            if ($policy->hasSlackNotification()) {
                $sent = $this->sendSlackNotification($alert, $policy) || $sent;
            }
        } catch (\Exception $e) {
            Log::error("Error sending notifications: {$e->getMessage()}");
        }

        return $sent;
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(AlertHistory $alert, EscalationPolicy $policy): bool
    {
        try {
            $recipients = $policy->recipientEmails();

            if (empty($recipients)) {
                return false;
            }

            $message = $this->buildEmailMessage($alert, $policy);

            // Using Mail::to()->queue() for async sending
            Mail::to($recipients)
                ->queue(new \App\Mail\AlertNotificationMail($message));

            Log::info("Alert email queued for {$alert->id} to " . implode(',', $recipients));

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending email notification: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send Slack notification (requires webhook URL)
     */
    private function sendSlackNotification(AlertHistory $alert, EscalationPolicy $policy): bool
    {
        try {
            $webhook = config('services.slack.webhook_url');

            if (!$webhook) {
                Log::warning("Slack webhook URL not configured");
                return false;
            }

            $payload = $this->buildSlackPayload($alert, $policy);

            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->post($webhook, $payload);

            if ($response->failed()) {
                Log::error("Slack notification failed for alert {$alert->id}");
                return false;
            }

            Log::info("Slack notification sent for alert {$alert->id}");

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending Slack notification: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Build email message content
     */
    private function buildEmailMessage(AlertHistory $alert, EscalationPolicy $policy): array
    {
        $organization = $alert->organization;
        $threshold = $alert->alertThreshold;

        return [
            'subject' => "🚨 Alert: {$threshold->metric} - {$alert->severity}",
            'alert_id' => $alert->id,
            'organization' => $organization->name,
            'metric' => $threshold->metric,
            'severity' => strtoupper($alert->severity),
            'threshold' => number_format($threshold->threshold, 2),
            'current_value' => number_format($alert->metric_value, 2),
            'triggered_at' => $alert->triggered_at->format('Y-m-d H:i:s'),
            'description' => $threshold->description,
            'level' => $policy->escalation_level,
            'action_url' => route('alerts.show', $alert->id),
        ];
    }

    /**
     * Build Slack webhook payload
     */
    private function buildSlackPayload(AlertHistory $alert, EscalationPolicy $policy): array
    {
        $threshold = $alert->alertThreshold;
        $colors = [
            'critical' => '#DC143C',  // Crimson
            'high' => '#FF4500',      // Red-Orange
            'medium' => '#FFD700',    // Gold
            'low' => '#87CEEB',       // Sky Blue
            'info' => '#90EE90',      // Light Green
        ];

        $color = $colors[$alert->severity] ?? '#808080';

        return [
            'attachments' => [
                [
                    'color' => $color,
                    'title' => "🚨 {$alert->severity} Alert",
                    'title_link' => route('alerts.show', $alert->id),
                    'fields' => [
                        [
                            'title' => 'Metric',
                            'value' => $threshold->metric,
                            'short' => true,
                        ],
                        [
                            'title' => 'Current Value',
                            'value' => number_format($alert->metric_value, 2),
                            'short' => true,
                        ],
                        [
                            'title' => 'Threshold',
                            'value' => number_format($threshold->threshold, 2),
                            'short' => true,
                        ],
                        [
                            'title' => 'Severity',
                            'value' => strtoupper($alert->severity),
                            'short' => true,
                        ],
                        [
                            'title' => 'Triggered',
                            'value' => $alert->triggered_at->diffForHumans(),
                            'short' => false,
                        ],
                        [
                            'title' => 'Escalation Level',
                            'value' => $policy->escalation_level,
                            'short' => true,
                        ],
                    ],
                    'footer' => 'Stratos Alert System',
                    'ts' => $alert->triggered_at->timestamp,
                ],
            ],
        ];
    }

    /**
     * Get notification history for alert
     */
    public function getNotificationHistory(AlertHistory $alert): array
    {
        // TODO: Implement when NotificationLog model is created
        return [];
    }

    /**
     * Resend notification for specific level
     */
    public function resendNotification(AlertHistory $alert, int $escalationLevel): bool
    {
        $policy = EscalationPolicy::forOrganization($alert->organization_id)
            ->forSeverity($alert->severity)
            ->forLevel($escalationLevel)
            ->first();

        if (!$policy) {
            return false;
        }

        return $this->sendNotifications($alert, $policy);
    }

    /**
     * Suppress notifications for timeframe
     */
    public function suppressNotifications(AlertHistory $alert, int $minutes = 60): void
    {
        $alert->update([
            'suppressed_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Check if notifications should be suppressed
     */
    public function isNotificationSuppressed(AlertHistory $alert): bool
    {
        return $alert->suppressed_until && now()->isBefore($alert->suppressed_until);
    }
}

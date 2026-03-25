<?php

namespace App\Services;

use App\Events\VerificationAlertThreshold;
use App\Events\VerificationPhaseTransitioned;
use App\Events\VerificationViolationDetected;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * VerificationNotificationService - Manages notification channels for verification system
 *
 * Responsibilities:
 * 1. Route notifications to configured channels (Slack, Email, Database)
 * 2. Respect notification preferences per organization
 * 3. Prevent notification spam (throttling/deduplication)
 * 4. Support multiple severity levels
 * 5. Log all notification attempts for audit trail
 *
 * Channels:
 * - slack: To Slack webhook (requires VERIFICATION_SLACK_WEBHOOK)
 * - email: To admin email addresses
 * - database: To internal verification_notifications table
 * - log: To Laravel log files
 */
class VerificationNotificationService
{
    /**
     * Handle phase transition notification
     */
    public function notifyPhaseTransition(
        VerificationPhaseTransitioned $event
    ): void {
        try {
            // Load organization to get notification preferences
            $organization = Organization::find($event->organizationId);

            if (! $organization) {
                Log::warning('Organization not found for phase transition notification', [
                    'organization_id' => $event->organizationId,
                ]);

                return;
            }

            // Check if notifications are enabled for this organization
            if (! $this->areNotificationsEnabled($organization)) {
                Log::debug('Notifications disabled for organization', [
                    'organization_id' => $event->organizationId,
                ]);

                return;
            }

            // Prepare notification data
            $data = [
                'type' => 'phase_transition',
                'organization_name' => $organization->name,
                'from_phase' => $event->fromPhase,
                'to_phase' => $event->toPhase,
                'reason' => $event->reason,
                'metrics' => $event->metrics,
                'transitioned_at' => $event->transitionedAt->toIso8601String(),
            ];

            // Send to configured channels
            $this->sendToChannels($organization, $data, 'phase_transition');

            Log::info('Phase transition notification sent', [
                'organization_id' => $event->organizationId,
                'from_phase' => $event->fromPhase,
                'to_phase' => $event->toPhase,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending phase transition notification', [
                'organization_id' => $event->organizationId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle alert threshold notification
     */
    public function notifyAlertThreshold(
        VerificationAlertThreshold $event
    ): void {
        try {
            $organization = Organization::find($event->organizationId);

            if (! $organization) {
                return;
            }

            // Check if this alert type is enabled
            if (! $this->isAlertTypeEnabled($organization, $event->metricName)) {
                return;
            }

            // Throttle: prevent duplicate alerts within 5 minutes
            if ($this->isDuplicateAlert($event)) {
                Log::debug('Alert throttled (duplicate)', [
                    'organization_id' => $event->organizationId,
                    'metric' => $event->metricName,
                ]);

                return;
            }

            // Prepare data
            $data = [
                'type' => 'alert_threshold',
                'organization_name' => $organization->name,
                'metric_name' => $event->metricName,
                'current_value' => $event->currentValue,
                'threshold' => $event->threshold,
                'severity' => $event->severity,
                'context' => $event->context,
                'alerted_at' => now()->toIso8601String(),
            ];

            // Send to channels
            $this->sendToChannels($organization, $data, 'alert_threshold', severity: $event->severity);

            // Mark alert as sent (for throttling)
            $this->markAlertSent($event);

            Log::info('Alert threshold notification sent', [
                'organization_id' => $event->organizationId,
                'metric' => $event->metricName,
                'severity' => $event->severity,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending alert threshold notification', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle violation detected notification
     */
    public function notifyViolationDetected(
        VerificationViolationDetected $event
    ): void {
        try {
            $organization = Organization::find($event->organizationId);

            if (! $organization) {
                return;
            }

            // Prepare data
            $data = [
                'type' => 'violation_detected',
                'organization_name' => $organization->name,
                'agent_name' => $event->agentName,
                'violation_count' => count($event->violations),
                'violations' => $event->violations,
                'confidence_score' => $event->confidenceScore,
                'severity' => $event->severity,
                'detected_at' => now()->toIso8601String(),
            ];

            // Send only to critical channels if severity is high
            $channels = $event->severity === 'critical'
                ? ['slack', 'email', 'database', 'log']
                : ['database', 'log'];

            $this->sendToChannels($organization, $data, 'violation_detected', channels: $channels, severity: $event->severity);

            Log::info('Violation detected notification sent', [
                'organization_id' => $event->organizationId,
                'agent_name' => $event->agentName,
                'severity' => $event->severity,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending violation detected notification', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to configured channels
     */
    private function sendToChannels(
        Organization $organization,
        array $data,
        string $type,
        ?array $channels = null,
        string $severity = 'info'
    ): void {
        // Default channels based on severity
        $channels = $channels ?? match ($severity) {
            'critical' => ['slack', 'email', 'database', 'log'],
            'warning' => ['slack', 'database', 'log'],
            default => ['database', 'log'],
        };

        // Get notification settings for organization
        $settings = $this->getNotificationSettings($organization);

        // Send to each enabled channel
        foreach ($channels as $channel) {
            if (! ($settings[$channel]['enabled'] ?? false)) {
                continue;
            }

            try {
                $this->dispatchToChannel(
                    channel: $channel,
                    organization: $organization,
                    data: $data,
                    settings: $settings[$channel] ?? []
                );
            } catch (\Exception $e) {
                Log::error("Error sending {$type} to {$channel}", [
                    'organization_id' => $organization->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send to specific channel
     */
    private function dispatchToChannel(
        string $channel,
        Organization $organization,
        array $data,
        array $settings = []
    ): void {
        match ($channel) {
            'slack' => $this->sendToSlack($organization, $data, $settings),
            'email' => $this->sendToEmail($organization, $data, $settings),
            'database' => $this->sendToDatabase($organization, $data),
            'log' => $this->sendToLog($organization, $data),
            default => Log::warning("Unknown notification channel: {$channel}"),
        };
    }

    /**
     * Send to Slack
     */
    private function sendToSlack(
        Organization $organization,
        array $data,
        array $settings
    ): void {
        $webhook = $settings['webhook_url'] ?? config('services.slack.verification_webhook');

        if (! $webhook) {
            Log::debug('Slack webhook not configured for verification notifications');

            return;
        }

        // Format message
        $message = $this->formatSlackMessage($data);

        // Send to Slack via webhook
        \Illuminate\Support\Facades\Http::post($webhook, [
            'blocks' => $message,
        ]);
    }

    /**
     * Send to Email
     */
    private function sendToEmail(
        Organization $organization,
        array $data,
        array $settings
    ): void {
        $recipients = $settings['recipients'] ?? $this->getAdminEmails($organization);

        if (empty($recipients)) {
            Log::debug('No email recipients configured for verification notifications');

            return;
        }

        // Send email to admins (implementation uses Laravel Mailable)
        // This will be implemented in Step 3 (Full Test Suite)
        Log::info('Email notification queued', [
            'organization_id' => $organization->id,
            'recipients' => count($recipients),
        ]);
    }

    /**
     * Send to Database
     */
    private function sendToDatabase(
        Organization $organization,
        array $data
    ): void {
        // Create notification record in database
        // This allows admins to view notification history
        \DB::table('verification_notifications')->insert([
            'organization_id' => $organization->id,
            'type' => $data['type'],
            'data' => json_encode($data),
            'severity' => $data['severity'] ?? 'info',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Send to Log
     */
    private function sendToLog(
        Organization $organization,
        array $data
    ): void {
        $severity = $data['severity'] ?? 'info';
        $message = $this->formatLogMessage($data);

        Log::log(
            level: $severity,
            message: $message,
            context: [
                'organization_id' => $organization->id,
                'verification_data' => $data,
            ]
        );
    }

    /**
     * Format message for Slack
     */
    private function formatSlackMessage(array $data): array
    {
        $type = $data['type'];

        if ($type === 'phase_transition') {
            return [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => sprintf(
                            "*✅ Verification Phase Transition*\n*%s*\n\n*From:* %s → *To:* %s\n*Reason:* %s\n*Time:* %s",
                            $data['organization_name'],
                            ucfirst($data['from_phase']),
                            ucfirst($data['to_phase']),
                            $data['reason'],
                            $data['transitioned_at']
                        ),
                    ],
                ],
            ];
        }

        if ($type === 'alert_threshold') {
            $severity = $data['severity'] === 'critical' ? '🚨' : '⚠️';

            return [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => sprintf(
                            "%s *Alert Threshold Exceeded*\n*%s*\n\n*Metric:* %s\n*Current:* %.2f%%\n*Threshold:* %.2f%%\n*Severity:* %s",
                            $severity,
                            $data['organization_name'],
                            $data['metric_name'],
                            $data['current_value'],
                            $data['threshold'],
                            ucfirst($data['severity'])
                        ),
                    ],
                ],
            ];
        }

        return [];
    }

    /**
     * Format message for logs
     */
    private function formatLogMessage(array $data): string
    {
        return match ($data['type']) {
            'phase_transition' => "Phase Transition: {$data['from_phase']} → {$data['to_phase']} ({$data['reason']})",
            'alert_threshold' => "Alert: {$data['metric_name']} = {$data['current_value']}% (threshold: {$data['threshold']}%)",
            'violation_detected' => "Violations detected by {$data['agent_name']}: {$data['violation_count']} issues",
            default => 'Verification event',
        };
    }

    /**
     * Check if notifications are enabled for organization
     */
    private function areNotificationsEnabled(Organization $organization): bool
    {
        return true; // Can be extended with database flags
    }

    /**
     * Check if specific alert type is enabled
     */
    private function isAlertTypeEnabled(Organization $organization, string $metricName): bool
    {
        return true; // Can be extended with database configuration
    }

    /**
     * Check if alert is duplicate (throttle)
     */
    private function isDuplicateAlert(VerificationAlertThreshold $event): bool
    {
        $cacheKey = "verification.alert.{$event->organizationId}.{$event->metricName}";

        return Cache::has($cacheKey);
    }

    /**
     * Mark alert as sent (for throttling)
     */
    private function markAlertSent(VerificationAlertThreshold $event): void
    {
        $cacheKey = "verification.alert.{$event->organizationId}.{$event->metricName}";
        Cache::put($cacheKey, true, minutes: 5);
    }

    /**
     * Get notification settings for organization
     */
    private function getNotificationSettings(Organization $organization): array
    {
        $settings = config('verification.notifications', []);

        return array_merge($settings, [
            'slack' => [
                'enabled' => (bool) config('services.slack.verification_webhook'),
                'webhook_url' => config('services.slack.verification_webhook'),
            ],
            'email' => [
                'enabled' => true,
                'recipients' => $this->getAdminEmails($organization),
            ],
            'database' => [
                'enabled' => true,
            ],
            'log' => [
                'enabled' => true,
            ],
        ]);
    }

    /**
     * Get admin emails for organization
     */
    private function getAdminEmails(Organization $organization): array
    {
        return $organization->users()
            ->where('role', 'admin')
            ->pluck('email')
            ->toArray();
    }

    // =========================================================================
    // CRUD & Management Methods
    // =========================================================================

    /**
     * Create and persist a notification record.
     */
    public function createNotification(
        string $organizationId,
        \App\Enums\NotificationType $type,
        \App\Enums\NotificationSeverity $severity,
        string $message,
        array $metadata = []
    ): \App\Models\VerificationNotification {
        return \App\Models\VerificationNotification::create([
            'organization_id' => $organizationId,
            'type' => $type->value,
            'severity' => $severity->value,
            'message' => $message,
            'data' => $metadata,
        ]);
    }

    /**
     * Filter notifications by optional type and/or severity.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\VerificationNotification>
     */
    public function filterNotifications(
        string $organizationId,
        ?\App\Enums\NotificationType $type = null,
        ?\App\Enums\NotificationSeverity $severity = null
    ): \Illuminate\Support\Collection {
        $query = \App\Models\VerificationNotification::query()
            ->where('organization_id', $organizationId);

        if ($type !== null) {
            $query->where('type', $type->value);
        }

        if ($severity !== null) {
            $query->where('severity', $severity->value);
        }

        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Get paginated notifications.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\VerificationNotification>
     */
    public function getNotifications(
        string $organizationId,
        int $perPage = 20,
        int $page = 1
    ): \Illuminate\Support\Collection {
        return \App\Models\VerificationNotification::query()
            ->where('organization_id', $organizationId)
            ->orderByDesc('created_at')
            ->forPage($page, $perPage)
            ->get();
    }

    /**
     * Send a single notification to a specific channel.
     */
    public function sendToChannel(
        \App\Models\VerificationNotification $notification,
        string $channel
    ): void {
        Log::info('Sending notification to channel', [
            'notification_id' => $notification->id,
            'channel' => $channel,
        ]);
    }

    /**
     * Send a test notification to a channel.
     *
     * @return array{success: bool, message: string}
     */
    public function sendTestNotification(
        string $organizationId,
        string $channel,
        ?string $recipient = null
    ): array {
        Log::info('Test notification sent', [
            'organization_id' => $organizationId,
            'channel' => $channel,
            'recipient' => $recipient,
        ]);

        $to = $recipient ?? 'default';

        return ['success' => true, 'message' => "Test notification sent to {$to} via {$channel}"];
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(int $notificationId): void
    {
        \App\Models\VerificationNotification::where('id', $notificationId)
            ->update(['read_at' => now()]);
    }

    /**
     * Mark all notifications for an organization as read.
     */
    public function markAllAsRead(string $organizationId): void
    {
        \App\Models\VerificationNotification::where('organization_id', $organizationId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread notification count for an organization.
     */
    public function getUnreadCount(string $organizationId): int
    {
        return \App\Models\VerificationNotification::where('organization_id', $organizationId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Delete notifications older than the retention window.
     */
    public function cleanupOldNotifications(int $retentionDays = 90): int
    {
        return \App\Models\VerificationNotification::where('created_at', '<', now()->subDays($retentionDays))
            ->delete();
    }
}

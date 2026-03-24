<?php

namespace App\Http\Controllers\Deployment;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VerificationNotification;
use App\Services\VerificationMetricsService;
use App\Services\VerificationNotificationService;
use Cache;
use Log;

class VerificationHubController extends Controller
{
    public function __construct(
        private VerificationMetricsService $metricsService,
        private VerificationNotificationService $notificationService,
    ) {}

    /**
     * Get scheduler status and next execution
     */
    public function schedulerStatus(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        $lastExecution = Cache::get("verification_scheduler_last_run_{$orgId}");
        $nextExecution = now()->addHour(); // Scheduler runs hourly

        $recentExecutions = Cache::get("verification_scheduler_executions_{$orgId}", []);

        return response()->json([
            'enabled' => config('verification-deployment.auto_transitions_enabled', false),
            'mode' => config('verification-deployment.mode', 'monitoring_only'),
            'last_run' => $lastExecution ? $lastExecution['timestamp'] : null,
            'last_run_status' => $lastExecution ? $lastExecution['status'] : null,
            'next_run' => $nextExecution->toIso8601String(),
            'seconds_until_next' => $nextExecution->diffInSeconds(now()),
            'recent_executions' => collect($recentExecutions)
                ->take(5)
                ->map(fn ($exec) => [
                    'timestamp' => $exec['timestamp'],
                    'status' => $exec['status'],
                    'transitions_evaluated' => $exec['transitions_evaluated'] ?? 0,
                    'transitions_executed' => $exec['transitions_executed'] ?? 0,
                    'message' => $exec['message'] ?? null,
                ])
                ->values(),
        ]);
    }

    /**
     * Get recent phase transitions
     */
    public function recentTransitions(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $limit = min($request->integer('limit', 10), 50);
        $days = $request->integer('days', 30);

        $transitions = Cache::get("verification_transitions_{$orgId}", []);

        $recent = collect($transitions)
            ->filter(fn ($t) => now()->diffInDays($t['timestamp']) <= $days)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->map(fn ($t) => [
                'timestamp' => $t['timestamp'],
                'from_phase' => $t['from_phase'],
                'to_phase' => $t['to_phase'],
                'triggered_by' => $t['triggered_by'],
                'metrics' => $t['metrics'] ?? [
                    'confidence_score' => $t['confidence'] ?? 0,
                    'error_rate' => $t['error_rate'] ?? 0,
                    'retry_rate' => $t['retry_rate'] ?? 0,
                ],
            ])
            ->values();

        return response()->json([
            'data' => $recent,
            'count' => count($recent),
        ]);
    }

    /**
     * Get notification center data
     */
    public function notifications(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $limit = min($request->integer('limit', 20), 100);
        $type = $request->string('type'); // phase_transition, alert_threshold, violation_detected
        $severity = $request->string('severity'); // info, warning, critical
        $read = $request->string('read'); // all, unread, read

        $query = VerificationNotification::where('organization_id', $orgId);

        if ($type) {
            $query->where('type', $type);
        }

        if ($severity) {
            $query->where('severity', $severity);
        }

        if ($read === 'unread') {
            $query->whereNull('read_at');
        } elseif ($read === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query
            ->latest('created_at')
            ->paginate($limit);

        return response()->json([
            'data' => $notifications->items(),
            'pagination' => [
                'total' => $notifications->total(),
                'count' => count($notifications->items()),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
            ],
        ]);
    }

    /**
     * Send test notification to configured channels
     */
    public function testNotification(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $channel = $request->string('channel'); // slack, email, database, log

        if (!in_array($channel, ['slack', 'email', 'database', 'log'])) {
            return response()->json(['error' => 'Invalid channel'], 422);
        }

        try {
            $testMessage = "🧪 Verification System Test Notification from {$orgId}";

            match ($channel) {
                'slack' => $this->notificationService->sendToSlack([
                    'type' => 'test',
                    'title' => 'Test Notification',
                    'message' => $testMessage,
                    'severity' => 'info',
                ], $orgId),
                'email' => $this->notificationService->sendToEmail([
                    'type' => 'test',
                    'title' => 'Test Notification',
                    'message' => $testMessage,
                    'severity' => 'info',
                ], $orgId),
                'database' => VerificationNotification::create([
                    'organization_id' => $orgId,
                    'type' => 'test',
                    'severity' => 'info',
                    'data' => [
                        'title' => 'Test Notification',
                        'message' => $testMessage,
                    ],
                ]),
                'log' => Log::info($testMessage),
            };

            return response()->json([
                'success' => true,
                'message' => "Test notification sent to {$channel}",
                'channel' => $channel,
            ]);
        } catch (\Exception $e) {
            Log::error("Test notification failed for {$channel}", ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => "Failed to send test notification: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Get current configuration
     */
    public function configuration(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        return response()->json([
            'mode' => config('verification-deployment.mode', 'monitoring_only'),
            'auto_transitions_enabled' => config('verification-deployment.auto_transitions_enabled', false),
            'notification_channels' => [
                'slack' => [
                    'enabled' => (bool) config('verification-notifications.slack.webhook'),
                    'connected' => (bool) config('verification-notifications.slack.webhook'),
                ],
                'email' => [
                    'enabled' => config('verification-notifications.email.enabled', true),
                    'from' => config('verification-notifications.email.from'),
                ],
                'database' => [
                    'enabled' => config('verification-notifications.database.enabled', true),
                    'retention_days' => config('verification-notifications.database.retention_days', 90),
                ],
                'log' => [
                    'enabled' => config('verification-notifications.log.enabled', true),
                    'level' => config('verification-notifications.log.level', 'info'),
                ],
            ],
            'alert_thresholds' => [
                'error_rate_threshold' => config('verification-notifications.alert_thresholds.error_rate', 40),
                'confidence_score_threshold' => config('verification-notifications.alert_thresholds.confidence_score', 85),
                'retry_rate_threshold' => config('verification-notifications.alert_thresholds.retry_rate', 20),
            ],
            'throttle_settings' => [
                'enabled' => config('verification-notifications.throttle.enabled', true),
                'window_minutes' => config('verification-notifications.throttle.window_minutes', 5),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Deployment;

use App\Http\Controllers\Controller;
use App\Models\VerificationNotification;
use App\Services\VerificationMetricsService;
use App\Services\VerificationNotificationService;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $request->validate([
            'channel' => ['required', 'in:slack,email,database,log'],
            'recipient' => ['nullable', 'string'],
        ]);

        $orgId = auth()->user()->organization_id;
        $channel = $request->input('channel');
        $recipient = $request->input('recipient');

        try {
            $result = $this->notificationService->sendTestNotification($orgId, $channel, $recipient);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'channel' => $channel,
            ], $result['success'] ? 200 : 500);
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

    /**
     * Get audit logs with filtering
     */
    public function auditLogs(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $limit = min($request->integer('limit', 50), 100);
        $action = $request->string('action');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to', now());
        $userId = $request->integer('user_id');

        $query = \App\Models\VerificationAuditLog::where('organization_id', $orgId);

        if ($action) {
            $query->where('action', $action);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        $logs = $query
            ->with('user')
            ->latest('created_at')
            ->paginate($limit);

        return response()->json([
            'data' => $logs->items(),
            'pagination' => [
                'total' => $logs->total(),
                'count' => count($logs->items()),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
            ],
        ]);
    }

    /**
     * Run dry-run simulation for phase transitions
     */
    public function dryRunSimulation(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        // Get current metrics
        $hours = $request->integer('hours', 24);
        $metrics = $this->metricsService->getOrganizationMetrics($orgId, $hours);

        // Get current thresholds
        $thresholds = [
            'error_rate' => $request->integer('error_rate_threshold', config('verification-deployment.error_rate_threshold', 40)),
            'confidence' => $request->integer('confidence_threshold', config('verification-deployment.confidence_threshold', 90)),
            'retry_rate' => $request->integer('retry_rate_threshold', config('verification-deployment.retry_rate_threshold', 20)),
        ];

        // Evaluate transition readiness
        $wouldTransition = $metrics['avg_confidence_score'] >= $thresholds['confidence'] &&
                          $metrics['error_rate'] <= $thresholds['error_rate'] &&
                          $metrics['retry_rate'] <= $thresholds['retry_rate'] &&
                          $metrics['sample_size_count'] >= 100;

        // Determine next phase
        $currentPhase = Cache::get("verification_current_phase_{$orgId}", 'silent');
        $nextPhase = match ($currentPhase) {
            'silent' => $wouldTransition ? 'flagging' : 'silent',
            'flagging' => $wouldTransition ? 'reject' : 'flagging',
            'reject' => $wouldTransition ? 'tuning' : 'reject',
            'tuning' => 'tuning',
            default => 'silent'
        };

        // Build gap analysis
        $gaps = [];
        if ($metrics['avg_confidence_score'] < $thresholds['confidence']) {
            $gaps[] = [
                'metric' => 'confidence',
                'current' => round($metrics['avg_confidence_score'], 1),
                'required' => $thresholds['confidence'],
                'gap' => $thresholds['confidence'] - round($metrics['avg_confidence_score'], 1),
                'days_to_meet' => ceil(($thresholds['confidence'] - $metrics['avg_confidence_score']) / 0.5),
            ];
        }
        if ($metrics['error_rate'] > $thresholds['error_rate']) {
            $gaps[] = [
                'metric' => 'error_rate',
                'current' => round($metrics['error_rate'], 1),
                'required' => $thresholds['error_rate'],
                'gap' => $metrics['error_rate'] - $thresholds['error_rate'],
                'needs_improvement' => true,
            ];
        }
        if ($metrics['retry_rate'] > $thresholds['retry_rate']) {
            $gaps[] = [
                'metric' => 'retry_rate',
                'current' => round($metrics['retry_rate'], 1),
                'required' => $thresholds['retry_rate'],
                'gap' => $metrics['retry_rate'] - $thresholds['retry_rate'],
                'needs_improvement' => true,
            ];
        }

        return response()->json([
            'current_phase' => $currentPhase,
            'would_transition' => $wouldTransition,
            'next_phase' => $nextPhase,
            'reason' => $wouldTransition
                ? "All metrics meet transition criteria for phase: $nextPhase"
                : 'Some metrics do not meet criteria. See gaps below.',
            'metrics' => [
                'confidence_score' => round($metrics['avg_confidence_score'], 1),
                'error_rate' => round($metrics['error_rate'], 1),
                'retry_rate' => round($metrics['retry_rate'], 1),
                'sample_size' => $metrics['sample_size_count'],
            ],
            'thresholds' => $thresholds,
            'gaps' => $gaps,
            'summary' => [
                'checks_passed' => count(array_filter([
                    $metrics['avg_confidence_score'] >= $thresholds['confidence'],
                    $metrics['error_rate'] <= $thresholds['error_rate'],
                    $metrics['retry_rate'] <= $thresholds['retry_rate'],
                    $metrics['sample_size_count'] >= 100,
                ])),
                'total_checks' => 4,
            ],
        ]);
    }

    /**
     * Generate compliance report
     */
    public function complianceReport(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $dateFrom = $request->date('date_from', now()->subDays(30));
        $dateTo = $request->date('date_to', now());
        $format = $request->string('format', 'json'); // json, csv, pdf

        $auditLogs = \App\Models\VerificationAuditLog::where('organization_id', $orgId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('user')
            ->latest('created_at')
            ->get();

        $summary = [
            'period_start' => $dateFrom->toDateString(),
            'period_end' => $dateTo->toDateString(),
            'total_events' => $auditLogs->count(),
            'phase_transitions' => $auditLogs->where('action', 'phase_transition')->count(),
            'config_changes' => $auditLogs->where('action', 'config_change')->count(),
            'manual_overrides' => $auditLogs->where('action', 'manual_override')->count(),
            'triggered_manual' => $auditLogs->where('triggered_by', 'manual')->count(),
            'triggered_automatic' => $auditLogs->where('triggered_by', 'automatic')->count(),
            'unique_users' => $auditLogs->pluck('user_id')->unique()->count(),
        ];

        // Format report
        $report = [
            'summary' => $summary,
            'events' => $auditLogs->map(fn ($log) => [
                'timestamp' => $log->created_at->toIso8601String(),
                'action' => $log->action,
                'user' => $log->user?->name ?? 'System',
                'triggered_by' => $log->triggered_by,
                'phase_from' => $log->phase_from,
                'phase_to' => $log->phase_to,
                'reason' => $log->reason,
                'summary' => $this->getChangeSummary($log),
            ])->values(),
        ];

        return response()->json([
            'data' => $report,
            'format' => $format,
            'filename' => "compliance-report-{$dateFrom->format('Y-m-d')}-to-{$dateTo->format('Y-m-d')}.json",
        ]);
    }

    /**
     * Private helper to summarize changes
     */
    private function getChangeSummary(\App\Models\VerificationAuditLog $log): string
    {
        return match ($log->action) {
            'phase_transition' => "Phase transitioned from {$log->phase_from} to {$log->phase_to}",
            'config_change' => 'Configuration updated',
            'manual_override' => "Manual override: {$log->reason}",
            default => 'System action recorded'
        };
    }
}

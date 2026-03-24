<?php

namespace App\Http\Controllers\Deployment;

use App\Http\Controllers\Controller;
use App\Models\VerificationAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VerificationDashboardController extends Controller
{
    /**
     * Get current verification metrics
     */
    public function metrics(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        // Fetch latest verification metrics
        $latestMetrics = VerificationAudit::where('organization_id', $organizationId)
            ->latest('created_at')
            ->first();

        return response()->json([
            'currentPhase' => $latestMetrics?->current_phase ?? 'silent',
            'confidenceScore' => $latestMetrics?->confidence_score ?? 0,
            'errorRate' => $latestMetrics?->error_rate ?? 0,
            'retryRate' => $latestMetrics?->retry_rate ?? 0,
            'sampleSize' => $latestMetrics?->sample_size ?? 0,
            'transitionReadiness' => $latestMetrics?->transition_readiness ?? 0,
            'lastUpdated' => $latestMetrics?->updated_at ?? now(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Get compliance metrics
     */
    public function complianceMetrics(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $audits = VerificationAudit::where('organization_id', $organizationId)
            ->select('created_at', 'confidence_score', 'error_rate', 'retry_rate', 'sample_size')
            ->latest('created_at')
            ->take(20)
            ->get();

        $passedTests = $audits->filter(fn ($a) => $a->confidence_score >= 90)->count();
        $totalTests = $audits->count();
        $complianceScore = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;

        return response()->json([
            'complianceScore' => round($complianceScore),
            'passedTests' => $passedTests,
            'totalTests' => $totalTests,
            'trend' => $this->calculateTrend($audits),
            'recentAudits' => $audits->map(fn ($a) => [
                'date' => $a->created_at->format('M d, H:i'),
                'status' => $a->confidence_score >= 90 ? 'passed' : 'failed',
                'score' => $a->confidence_score,
                'errorRate' => $a->error_rate,
            ]),
        ]);
    }

    /**
     * Get metrics history for trend analysis
     */
    public function metricsHistory(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $hours = $request->query('window', 24);

        $startTime = now()->subHours($hours);

        $history = VerificationAudit::where('organization_id', $organizationId)
            ->where('created_at', '>=', $startTime)
            ->select('created_at', 'confidence_score', 'error_rate', 'retry_rate', 'throughput', 'latency')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(
            $history->map(fn ($m) => [
                'timestamp' => $m->created_at->toIso8601String(),
                'confidenceScore' => $m->confidence_score ?? 0,
                'errorRate' => $m->error_rate ?? 0,
                'retryRate' => $m->retry_rate ?? 0,
                'throughput' => $m->throughput ?? 0,
                'latency' => $m->latency ?? 0,
            ])->reverse()
        );
    }

    /**
     * Real-time events stream (Server-Sent Events)
     */
    public function realtimeEventsStream(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $organizationId = auth()->user()->organization_id;

        return response()->stream(function () use ($organizationId, $request) {
            // Set headers for SSE
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no');
            header('Connection: keep-alive');

            $lastEventId = $request->query('since', now()->subMinutes(5)->toIso8601String());

            while (true) {
                // Send heartbeat every 30 seconds
                echo ": heartbeat\n\n";
                flush();

                // Fetch new events
                $events = VerificationAudit::where('organization_id', $organizationId)
                    ->where('created_at', '>', Carbon::parse($lastEventId))
                    ->select('id', 'created_at', 'current_phase', 'status', 'confidence_score', 'error_rate')
                    ->latest('created_at')
                    ->take(10)
                    ->get();

                foreach ($events as $event) {
                    $data = [
                        'type' => 'alert',
                        'payload' => [
                            'id' => $event->id,
                            'timestamp' => $event->created_at->toIso8601String(),
                            'type' => $this->getEventType($event->current_phase),
                            'message' => $this->getEventMessage($event->current_phase),
                            'severity' => $event->confidence_score < 80 ? 'warning' : 'info',
                        ],
                    ];

                    echo "event: verification-update\n";
                    echo 'data: ' . json_encode($data) . "\n\n";
                    $lastEventId = $event->created_at->toIso8601String();
                }

                // Wait 5 seconds before next check
                sleep(5);

                // Connection limit (5 minutes)
                if (time() - $_SERVER['REQUEST_TIME'] > 300) {
                    break;
                }
            }
        }, 200, [
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Export metrics in various formats
     */
    public function exportMetrics(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $format = $request->query('format', 'json');
        $hours = $request->query('hours', 24);

        $startTime = now()->subHours($hours);

        $data = VerificationAudit::where('organization_id', $organizationId)
            ->where('created_at', '>=', $startTime)
            ->select('created_at', 'current_phase', 'confidence_score', 'error_rate', 'retry_rate', 'sample_size')
            ->orderBy('created_at')
            ->get();

        if ($format === 'csv') {
            $csv = "Timestamp,Phase,Confidence,ErrorRate,RetryRate,SampleSize\n";
            foreach ($data as $d) {
                $csv .= "{$d->created_at},{$d->current_phase},{$d->confidence_score},{$d->error_rate},{$d->retry_rate},{$d->sample_size}\n";
            }

            return response()->json([
                'format' => 'csv',
                'data' => $csv,
                'filename' => 'verification-metrics-' . now()->format('Y-m-d-H-i-s') . '.csv',
            ]);
        }

        return response()->json([
            'format' => 'json',
            'data' => $data->map(fn ($d) => [
                'timestamp' => $d->created_at->toIso8601String(),
                'phase' => $d->current_phase,
                'confidenceScore' => $d->confidence_score,
                'errorRate' => $d->error_rate,
                'retryRate' => $d->retry_rate,
                'sampleSize' => $d->sample_size,
            ]),
            'filename' => 'verification-metrics-' . now()->format('Y-m-d-H-i-s') . '.json',
        ]);
    }

    /**
     * Calculate trend from metrics
     */
    private function calculateTrend($audits): int
    {
        if ($audits->count() < 2) {
            return 0;
        }

        $latest = $audits->first()->confidence_score;
        $previous = $audits->get(1)->confidence_score ?? 0;

        return (int) (($latest - $previous) / max($previous, 1) * 100);
    }

    /**
     * Get event type from phase
     */
    private function getEventType(string $phase): string
    {
        return match ($phase) {
            'silent', 'flagging', 'reject', 'tuning' => 'transition',
            default => 'notification',
        };
    }

    /**
     * Get event message
     */
    private function getEventMessage(string $phase, string $status): string
    {
        return match ($phase) {
            'silent' => 'Silent monitoring phase active',
            'flagging' => 'Issues being flagged for review',
            'reject' => 'Rejecting invalid transitions',
            'tuning' => 'Fine-tuning detection parameters',
            default => 'System status update',
        };
    }

    /**
     * Get event severity from status
     */
    private function getEventSeverity(string $status): string
    {
        return match ($status) {
            'error' => 'error',
            'warning' => 'warning',
            'success' => 'info',
            default => 'info',
        };
    }
}

<?php

namespace App\Services;

use App\Models\VerificationEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * VerificationMetricsService - Analiza métricas de verificación para evaluar transiciones de fase
 *
 * Responsibilities:
 * 1. Collect verification events from event_store
 * 2. Calculate metrics (confidence, error rate, retry rate)
 * 3. Support windowed analysis (últimas N horas)
 * 4. Multi-tenant isolation (organization_id scoping)
 * 5. Cache results para performance
 */
class VerificationMetricsService
{
    /**
     * Get metrics for an organization in a time window
     *
     * @param int $organizationId Organization to analyze
     * @param int $windowHours Time window in hours (default: 24)
     * @return array Metrics with statistics
     */
    public function getOrganizationMetrics(
        int $organizationId,
        int $windowHours = 24,
    ): array {
        try {
            $startTime = Carbon::now()->subHours($windowHours);

            // Collect verification events in time window
            $events = DB::table('event_store')
                ->where('aggregate_type', 'verification')
                ->where('metadata->organization_id', $organizationId)
                ->where('occurred_at', '>=', $startTime)
                ->orderBy('occurred_at', 'desc')
                ->get();

            if ($events->isEmpty()) {
                Log::info('No verification events found in window', [
                    'organization_id' => $organizationId,
                    'window_hours' => $windowHours,
                ]);

                return [];
            }

            // Parse and aggregate events
            return $this->aggregateEventMetrics(
                events: $events,
                organizationId: $organizationId,
                windowHours: $windowHours
            );
        } catch (\Exception $e) {
            Log::error('Error collecting verification metrics', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Aggregate event statistics
     *
     * @param \Illuminate\Database\Eloquent\Collection $events
     * @param int $organizationId
     * @param int $windowHours
     * @return array Aggregated metrics
     */
    private function aggregateEventMetrics(
        $events,
        int $organizationId,
        int $windowHours,
    ): array {
        $totalEvents = $events->count();
        $confidenceScores = [];
        $violationCounts = [];
        $rejectedCount = 0;
        $reviewedCount = 0;
        $acceptedCount = 0;
        $retriedCount = 0;

        foreach ($events as $event) {
            $payload = json_decode($event->payload ?? '{}', true);
            $metadata = json_decode($event->metadata ?? '{}', true);

            // Track by event type
            match ($event->event_name) {
                'verification.completed' => $this->processCompletedEvent(
                    $payload,
                    $confidenceScores,
                    $violationCounts
                ),
                'verification.recommendation_accept' => $acceptedCount++,
                'verification.recommendation_review' => $reviewedCount++,
                'verification.recommendation_reject' => $rejectedCount++,
                'verification.retried' => $retriedCount++,
                default => null,
            };
        }

        // Calculate aggregate statistics
        $avgConfidence = ! empty($confidenceScores)
            ? array_sum($confidenceScores) / count($confidenceScores)
            : 0;

        $totalViolations = array_sum($violationCounts);
        $errorRate = $totalEvents > 0
            ? ($totalViolations / $totalEvents) * 100
            : 0;

        $retryRate = $totalEvents > 0
            ? ($retriedCount / $totalEvents) * 100
            : 0;

        return [
            'organization_id' => $organizationId,
            'window_hours' => $windowHours,
            'analysis_period' => [
                'start' => Carbon::now()->subHours($windowHours)->toIso8601String(),
                'end' => now()->toIso8601String(),
            ],
            'total_verifications' => $totalEvents,
            'avg_confidence_score' => round($avgConfidence, 2),
            'error_rate' => round($errorRate, 2),
            'retry_rate' => round($retryRate, 2),
            'recommendation_breakdown' => [
                'accepted' => $acceptedCount,
                'review_needed' => $reviewedCount,
                'rejected' => $rejectedCount,
            ],
            'total_violations' => $totalViolations,
            'current_phase' => $this->getCurrentPhase(),
            'last_updated' => now()->toIso8601String(),
        ];
    }

    /**
     * Process verification.completed events
     */
    private function processCompletedEvent(
        array $payload,
        array &$confidenceScores,
        array &$violationCounts
    ): void {
        // Extract confidence score
        if (isset($payload['confidence_score'])) {
            $confidenceScores[] = $payload['confidence_score'];
        }

        // Count violations
        $violations = $payload['violations'] ?? [];
        $violationCounts[] = is_array($violations) ? count($violations) : 0;
    }

    /**
     * Get current phase from configuration
     *
     * @return string current phase (silent|flagging|reject|tuning)
     */
    private function getCurrentPhase(): string
    {
        return config('verification-deployment.current_phase', 'silent');
    }

    /**
     * Get detailed metrics by verification type
     *
     * @param int $organizationId
     * @param int $windowHours
     * @return array Metrics broken down by agent/type
     */
    public function getDetailedMetricsByType(
        int $organizationId,
        int $windowHours = 24,
    ): array {
        try {
            $startTime = Carbon::now()->subHours($windowHours);

            // Group events by agent name
            $events = DB::table('event_store')
                ->where('aggregate_type', 'verification')
                ->where('metadata->organization_id', $organizationId)
                ->where('occurred_at', '>=', $startTime)
                ->get();

            $metricsByType = [];

            foreach ($events as $event) {
                $payload = json_decode($event->payload ?? '{}', true);
                $agentName = $payload['agent_name'] ?? 'unknown';

                if (! isset($metricsByType[$agentName])) {
                    $metricsByType[$agentName] = [
                        'agent_name' => $agentName,
                        'event_count' => 0,
                        'avg_confidence' => 0,
                        'total_violations' => 0,
                    ];
                }

                $metricsByType[$agentName]['event_count']++;

                if (isset($payload['confidence_score'])) {
                    $metricsByType[$agentName]['avg_confidence'] = (
                        ($metricsByType[$agentName]['avg_confidence'] *
                            ($metricsByType[$agentName]['event_count'] - 1)) +
                        $payload['confidence_score']
                    ) / $metricsByType[$agentName]['event_count'];
                }

                $violations = $payload['violations'] ?? [];
                $metricsByType[$agentName]['total_violations'] += is_array($violations)
                    ? count($violations)
                    : 0;
            }

            return $metricsByType;
        } catch (\Exception $e) {
            Log::error('Error getting detailed metrics', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Export metrics as JSON for reporting
     */
    public function exportMetricsJson(
        int $organizationId,
        int $windowHours = 24,
    ): string {
        $metrics = $this->getOrganizationMetrics($organizationId, $windowHours);
        $detailedMetrics = $this->getDetailedMetricsByType($organizationId, $windowHours);

        return json_encode([
            'summary' => $metrics,
            'by_type' => $detailedMetrics,
            'exported_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

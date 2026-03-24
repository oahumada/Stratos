<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Services\VerificationIntegrationService;
use App\Services\VerificationMetricsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * ProcessVerificationPhaseTransition - Job automático para evaluar transiciones de fase
 *
 * Triggered: Periodically by scheduler (configurable interval from config/verification-deployment.php)
 * Purpose: Automatically transition verification phases based on metrics thresholds
 * Scope: Per-organization (runs independently for each tenant)
 *
 * Modes Soportados:
 * - auto_transitions: Transiciona automáticamente (requiere supervisión)
 * - hybrid: Propone cambios (requiere aprobación manual)
 * - monitoring_only: Solo registra métricas (sin cambios automáticos)
 */
class ProcessVerificationPhaseTransition implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Organization ID to process (multi-tenant isolation)
     */
    public int $organizationId;

    /**
     * Number of attempts before failing
     */
    public int $tries = 2;

    /**
     * Backoff strategy for retries (exponential)
     */
    public array $backoff = [30, 120];

    /**
     * Timeout for job execution (seconds)
     */
    public int $timeout = 300;

    /**
     * Create a new job instance
     */
    public function __construct(int $organizationId)
    {
        $this->organizationId = $organizationId;
        $this->onQueue(config('queue.default', 'default'));
    }

    /**
     * Execute the job
     */
    public function handle(
        VerificationIntegrationService $verificationService,
        VerificationMetricsService $metricsService
    ): void {
        try {
            // 1. Load deployment configuration
            $deploymentConfig = config('verification-deployment');
            $deploymentMode = $deploymentConfig['deployment_mode'] ?? 'monitoring_only';

            // 2. Skip if not in auto_transitions mode
            if ($deploymentMode !== 'auto_transitions') {
                Log::info('Phase transition check skipped - not in auto_transitions mode', [
                    'organization_id' => $this->organizationId,
                    'mode' => $deploymentMode,
                ]);

                return;
            }

            // 3. Get auto_transitions configuration
            $autoConfig = $deploymentConfig['auto_transitions'] ?? [];
            $errorRateThreshold = $autoConfig['error_rate_threshold'] ?? 80;
            $retryRateThreshold = $autoConfig['retry_rate_threshold'] ?? 50;
            $checkIntervalMinutes = $autoConfig['check_interval_minutes'] ?? 30;
            $dataWindowHours = $autoConfig['data_window_hours'] ?? 24;

            Log::info('Starting phase transition evaluation', [
                'organization_id' => $this->organizationId,
                'error_rate_threshold' => $errorRateThreshold,
                'retry_rate_threshold' => $retryRateThreshold,
                'data_window_hours' => $dataWindowHours,
            ]);

            // 4. Collect metrics for organization
            $metrics = $metricsService->getOrganizationMetrics(
                organizationId: $this->organizationId,
                windowHours: $dataWindowHours
            );

            if (empty($metrics)) {
                Log::info('No metrics available for phase transition', [
                    'organization_id' => $this->organizationId,
                ]);

                return;
            }

            // 5. Evaluate transition conditions
            $shouldTransition = $this->evaluateTransitionConditions(
                metrics: $metrics,
                errorRateThreshold: $errorRateThreshold,
                retryRateThreshold: $retryRateThreshold
            );

            // 6. Execute transition if conditions met
            if ($shouldTransition['should_transition']) {
                $this->executePhaseTransition(
                    $verificationService,
                    $shouldTransition['next_phase'],
                    $shouldTransition['reason']
                );
            } else {
                Log::info('Phase transition conditions not met', [
                    'organization_id' => $this->organizationId,
                    'current_metrics' => $metrics,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error during phase transition evaluation', [
                'organization_id' => $this->organizationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Evaluate if transition conditions are met
     *
     * @param array $metrics Organization metrics
     * @param float $errorRateThreshold Maximum allowed error rate
     * @param float $retryRateThreshold Maximum allowed retry rate
     * @return array ['should_transition' => bool, 'next_phase' => string, 'reason' => string]
     */
    private function evaluateTransitionConditions(
        array $metrics,
        float $errorRateThreshold,
        float $retryRateThreshold
    ): array {
        $currentPhase = $metrics['current_phase'] ?? 'silent';
        $confidenceScore = $metrics['avg_confidence_score'] ?? 0;
        $errorRate = $metrics['error_rate'] ?? 0;
        $retryRate = $metrics['retry_rate'] ?? 0;
        $totalVerifications = $metrics['total_verifications'] ?? 0;

        // Require minimum sample size before transitioning
        $minimumSampleSize = 100;
        if ($totalVerifications < $minimumSampleSize) {
            return [
                'should_transition' => false,
                'reason' => "Insufficient data: {$totalVerifications} < {$minimumSampleSize}",
            ];
        }

        // Transition logic based on current phase and metrics
        return match ($currentPhase) {
            'silent' => $this->evaluateSilentPhaseTransition(
                confidenceScore,
                errorRate,
                errorRateThreshold
            ),
            'flagging' => $this->evaluateFlaggingPhaseTransition(
                confidenceScore,
                errorRate,
                errorRateThreshold
            ),
            'reject' => $this->evaluateRejectPhaseTransition(
                confidenceScore,
                errorRate,
                retryRate,
                errorRateThreshold,
                retryRateThreshold
            ),
            'tuning' => [
                'should_transition' => false,
                'reason' => 'Already in final tuning phase',
            ],
            default => ['should_transition' => false, 'reason' => 'Unknown phase'],
        };
    }

    /**
     * Evaluate transition from silent phase
     * Criteria: High confidence (>90%) and low error rate
     */
    private function evaluateSilentPhaseTransition(
        float $confidenceScore,
        float $errorRate,
        float $errorRateThreshold
    ): array {
        if ($confidenceScore > 90 && $errorRate < $errorRateThreshold * 0.5) {
            return [
                'should_transition' => true,
                'next_phase' => 'flagging',
                'reason' => "Ready: confidence={$confidenceScore}% errors={$errorRate}%",
            ];
        }

        return [
            'should_transition' => false,
            'reason' => "Not ready: confidence={$confidenceScore}% errors={$errorRate}%",
        ];
    }

    /**
     * Evaluate transition from flagging phase
     * Criteria: Very high confidence (>95%) and very low error rate
     */
    private function evaluateFlaggingPhaseTransition(
        float $confidenceScore,
        float $errorRate,
        float $errorRateThreshold
    ): array {
        if ($confidenceScore > 95 && $errorRate < $errorRateThreshold * 0.3) {
            return [
                'should_transition' => true,
                'next_phase' => 'reject',
                'reason' => "Ready: confidence={$confidenceScore}% errors={$errorRate}%",
            ];
        }

        return [
            'should_transition' => false,
            'reason' => "Not ready: confidence={$confidenceScore}% errors={$errorRate}%",
        ];
    }

    /**
     * Evaluate transition from reject phase to tuning
     * Criteria: Extremely high confidence (>98%), minimal errors, low retry rate
     */
    private function evaluateRejectPhaseTransition(
        float $confidenceScore,
        float $errorRate,
        float $retryRate,
        float $errorRateThreshold,
        float $retryRateThreshold
    ): array {
        if (
            $confidenceScore > 98 &&
            $errorRate < $errorRateThreshold * 0.2 &&
            $retryRate < $retryRateThreshold
        ) {
            return [
                'should_transition' => true,
                'next_phase' => 'tuning',
                'reason' => "Ready: confidence={$confidenceScore}% errors={$errorRate}% retries={$retryRate}%",
            ];
        }

        return [
            'should_transition' => false,
            'reason' => "Not ready: confidence={$confidenceScore}% errors={$errorRate}% retries={$retryRate}%",
        ];
    }

    /**
     * Execute phase transition with notification
     */
    private function executePhaseTransition(
        VerificationIntegrationService $verificationService,
        string $nextPhase,
        string $reason
    ): void {
        try {
            // Get current phase before transition
            $currentPhase = $verificationService->getCurrentPhase();

            // Update phase configuration
            $this->updatePhaseConfiguration($nextPhase);

            Log::info('Phase transition executed', [
                'organization_id' => $this->organizationId,
                'from_phase' => $currentPhase,
                'to_phase' => $nextPhase,
                'reason' => $reason,
                'timestamp' => now(),
            ]);

            // Dispatch notification event (implemented in Step 2)
            event('verification.phase_transitioned', [
                'organization_id' => $this->organizationId,
                'from_phase' => $currentPhase,
                'to_phase' => $nextPhase,
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            Log::error('Error executing phase transition', [
                'organization_id' => $this->organizationId,
                'next_phase' => $nextPhase,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Update phase configuration file
     * Note: In production, consider using database-backed configuration
     */
    private function updatePhaseConfiguration(string $nextPhase): void {
        $deploymentConfig = config('verification-deployment');

        // Mark transition timestamp
        $deploymentConfig['last_transition_at'] = now()->toIso8601String();
        $deploymentConfig['last_transition_from'] = $deploymentConfig['current_phase'] ?? 'silent';
        $deploymentConfig['current_phase'] = $nextPhase;

        // In a production system, save to database or use Cache
        // For now, update config in memory and log the change
        Config::set('verification-deployment', $deploymentConfig);
    }
}

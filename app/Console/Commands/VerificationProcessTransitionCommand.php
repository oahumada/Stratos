<?php

namespace App\Console\Commands;

use App\Jobs\ProcessVerificationPhaseTransition;
use App\Models\Organization;
use App\Services\VerificationMetricsService;
use Illuminate\Console\Command;

/**
 * VerificationProcessTransitionCommand - Manual trigger for phase transition evaluation
 *
 * Useful for:
 * - Testing phase transition logic in development
 * - Manual trigger when automatic scheduler is disabled
 * - Forced phase transitions for testing
 *
 * Usage:
 * php artisan verification:process-transition --org=1 --show-metrics
 * php artisan verification:process-transition --all
 */
class VerificationProcessTransitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:process-transition {--org=} {--all} {--show-metrics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually trigger verification phase transition evaluation';

    /**
     * Execute the console command.
     */
    public function handle(VerificationMetricsService $metricsService): int
    {
        try {
            if ($this->option('all')) {
                return $this->processAllOrganizations($metricsService);
            }

            if ($this->option('org')) {
                return $this->processOrganization(
                    (int) $this->option('org'),
                    $metricsService
                );
            }

            $this->error('Either --org=ID or --all must be specified');

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Process a single organization
     */
    private function processOrganization(
        int $organizationId,
        VerificationMetricsService $metricsService
    ): int {
        $org = Organization::find($organizationId);

        if (! $org) {
            $this->error("Organization {$organizationId} not found");

            return Command::FAILURE;
        }

        $this->info("Processing organization: {$org->name} (ID: {$organizationId})");

        // Show metrics if requested
        if ($this->option('show-metrics')) {
            $this->displayMetrics($organizationId, $metricsService);
        }

        // Dispatch job
        ProcessVerificationPhaseTransition::dispatch($organizationId);

        $this->info('✅ Phase transition job dispatched successfully');
        $this->info('Monitor queue with: php artisan queue:work');

        return Command::SUCCESS;
    }

    /**
     * Process all organizations
     */
    private function processAllOrganizations(
        VerificationMetricsService $metricsService
    ): int {
        $organizations = Organization::where('is_active', true)->get();

        if ($organizations->isEmpty()) {
            $this->warn('No active organizations found');

            return Command::SUCCESS;
        }

        $this->info("Found {$organizations->count()} active organizations");

        foreach ($organizations as $org) {
            $this->line('');
            $this->info("Processing: {$org->name}");

            // Show metrics if requested
            if ($this->option('show-metrics')) {
                $this->displayMetrics($org->id, $metricsService);
            }

            // Dispatch job
            ProcessVerificationPhaseTransition::dispatch($org->id);
            $this->comment('  ✓ Job dispatched');
        }

        $this->info('');
        $this->info('✅ All phase transition jobs dispatched');
        $this->info('Monitor queue with: php artisan queue:work');

        return Command::SUCCESS;
    }

    /**
     * Display metrics table for organization
     */
    private function displayMetrics(
        int $organizationId,
        VerificationMetricsService $metricsService
    ): void {
        $this->line('');

        try {
            $metrics = $metricsService->getOrganizationMetrics(
                organizationId: $organizationId,
                windowHours: 24
            );

            if (empty($metrics)) {
                $this->warn('  No metrics available');

                return;
            }

            // Display summary
            $this->info('  📊 Metrics Summary (Last 24h):');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Verifications', $metrics['total_verifications'] ?? 'N/A'],
                    ['Avg Confidence', ($metrics['avg_confidence_score'] ?? 0).'%'],
                    ['Error Rate', ($metrics['error_rate'] ?? 0).'%'],
                    ['Retry Rate', ($metrics['retry_rate'] ?? 0).'%'],
                    ['Current Phase', $metrics['current_phase'] ?? 'unknown'],
                    ['Total Violations', $metrics['total_violations'] ?? 0],
                ]
            );

            // Display recommendation breakdown
            if (isset($metrics['recommendation_breakdown'])) {
                $breakdown = $metrics['recommendation_breakdown'];
                $this->table(
                    ['Recommendation', 'Count'],
                    [
                        ['Accepted', $breakdown['accepted'] ?? 0],
                        ['Review Needed', $breakdown['review_needed'] ?? 0],
                        ['Rejected', $breakdown['rejected'] ?? 0],
                    ]
                );
            }

            $this->line('');
        } catch (\Exception $e) {
            $this->error('  Error loading metrics: '.$e->getMessage());
        }
    }
}

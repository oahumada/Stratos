<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\VerificationMetricsService;
use Illuminate\Console\Command;

/**
 * VerificationDryRunCommand - Simulate phase transitions without applying changes
 *
 * Useful for:
 * - Testing transition logic before enabling automation
 * - Validating metrics collection
 * - Planning infrastructure upgrades
 */
class VerificationDryRunCommand extends Command
{
    protected $signature = 'verification:dry-run {--org=} {--phase=} {--verbose}';

    protected $description = 'Simulate verification phase transitions without applying changes';

    public function handle(VerificationMetricsService $metricsService): int
    {
        try {
            if ($this->option('org')) {
                return $this->runForOrganization((int) $this->option('org'), $metricsService);
            }

            $this->runForAllOrganizations($metricsService);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    private function runForOrganization(int $orgId, VerificationMetricsService $metricsService): int
    {
        $org = Organization::find($orgId);

        if (! $org) {
            $this->error("Organization {$orgId} not found");

            return Command::FAILURE;
        }

        $this->info("📋 DRY-RUN for {$org->name}");
        $this->line('');

        // Get current metrics
        $metrics = $metricsService->getOrganizationMetrics($orgId, 24);

        if (empty($metrics)) {
            $this->warn('No metrics available');

            return Command::SUCCESS;
        }

        // Display current state
        $this->displayCurrentState($metrics);

        // Analyze transitions
        $this->analyzeTransitions($metrics);

        $this->info('✅ Dry-run complete - No changes applied');

        return Command::SUCCESS;
    }

    private function runForAllOrganizations(VerificationMetricsService $metricsService): void
    {
        $organizations = Organization::where('is_active', true)->get();

        $this->info("📋 DRY-RUN for {$organizations->count()} organizations");
        $this->line('');

        foreach ($organizations as $org) {
            $metrics = $metricsService->getOrganizationMetrics($org->id, 24);

            if (! empty($metrics)) {
                $this->displayCurrentState($metrics, $org->name);
                $this->analyzeTransitions($metrics);
                $this->line('');
            }
        }
    }

    private function displayCurrentState(array $metrics, string $name = ''): void
    {
        if ($name) {
            $this->line("<info>{$name}</info>");
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Verifications', $metrics['total_verifications'] ?? 0],
                ['Avg Confidence', ($metrics['avg_confidence_score'] ?? 0).'%'],
                ['Error Rate', ($metrics['error_rate'] ?? 0).'%'],
                ['Retry Rate', ($metrics['retry_rate'] ?? 0).'%'],
                ['Current Phase', $metrics['current_phase'] ?? 'unknown'],
            ]
        );

        $this->line('');
    }

    private function analyzeTransitions(array $metrics): void
    {
        $phase = $metrics['current_phase'] ?? 'silent';
        $confidence = $metrics['avg_confidence_score'] ?? 0;
        $errorRate = $metrics['error_rate'] ?? 0;

        $this->info("Analyzing transitions from phase: {$phase}");

        switch ($phase) {
            case 'silent':
                if ($confidence > 90 && $errorRate < 40) {
                    $this->line('✅ <fg=green>Ready to transition → flagging</>');
                    $this->line("Reason: Confidence at {$confidence}%, errors at {$errorRate}%");
                } else {
                    $this->line('❌ <fg=yellow>Not ready for transition</>');
                    $this->line("Needs: Confidence > 90% (current: {$confidence}%), Errors < 40% (current: {$errorRate}%)");
                }
                break;

            case 'flagging':
                if ($confidence > 95 && $errorRate < 15) {
                    $this->line('✅ <fg=green>Ready to transition → reject</>');
                    $this->line("Reason: Confidence at {$confidence}%, errors at {$errorRate}%");
                } else {
                    $this->line('❌ <fg=yellow>Not ready for transition</>');
                    $this->line("Needs: Confidence > 95% (current: {$confidence}%), Errors < 15% (current: {$errorRate}%)");
                }
                break;

            case 'reject':
                if ($confidence > 98 && $errorRate < 5) {
                    $this->line('✅ <fg=green>Ready to transition → tuning</>');
                } else {
                    $this->line('❌ <fg=yellow>Not ready for transition</>');
                    $this->line("Needs: Confidence > 98% (current: {$confidence}%), Errors < 5% (current: {$errorRate}%)");
                }
                break;

            default:
                $this->line('✅ Already in final phase');
        }

        $this->line('');
    }
}

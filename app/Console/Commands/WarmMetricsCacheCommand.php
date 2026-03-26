<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\Cache\MetricsCacheService;
use Illuminate\Console\Command;

class WarmMetricsCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:warm-cache {--org-id=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-warm metrics cache for organizations to prevent cold starts';

    public function __construct(
        protected MetricsCacheService $metricsCache
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $orgId = $this->option('org-id');

        $this->info('🔥 Metrics Cache Warming...');

        if ($orgId) {
            // Warm cache for specific organization
            return $this->warmOrgCache((int) $orgId, $dryRun);
        }

        // Warm cache for all organizations
        $orgs = Organization::pluck('id');
        $total = $orgs->count();

        $this->info("Warming cache for {$total} organization(s)...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($orgs as $id) {
            try {
                if (! $dryRun) {
                    $this->metricsCache->fetchMetricsAndBenchmarks($id);
                }
                $success++;
                $bar->advance();
            } catch (\Exception $e) {
                $failed++;
                $bar->advance();
                $this->warn("\nFailed warming org {$id}: {$e->getMessage()}");
            }
        }

        $bar->finish();

        $this->newLine();
        $this->info('✅ Cache warming complete!');
        $this->info("  Warmed: {$success}/{$total}");
        if ($failed > 0) {
            $this->warn("  Failed: {$failed}/{$total}");
        }

        if ($dryRun) {
            $this->comment('(dry-run mode - no cache written)');
        }

        return self::SUCCESS;
    }

    /**
     * Warm cache for specific organization.
     */
    private function warmOrgCache(int $orgId, bool $dryRun): int
    {
        try {
            if (! $dryRun) {
                $this->metricsCache->fetchMetricsAndBenchmarks($orgId);
            }
            $this->info("✅ Cache warmed for organization {$orgId}");
            if ($dryRun) {
                $this->comment('(dry-run mode - no cache written)');
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed warming cache: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}

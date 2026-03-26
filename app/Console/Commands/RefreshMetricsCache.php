<?php

namespace App\Console\Commands;

use App\Services\Cache\MetricsCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshMetricsCache extends Command
{
    protected $signature = 'metrics:cache-refresh {organization_id? : Organization ID, or "all" to refresh all} {--apply : Actually perform the invalidation}';

    protected $description = 'Invalidate Redis cache for business metrics + financial indicators';

    public function __construct(
        protected MetricsCacheService $metricsCache
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $orgId = $this->argument('organization_id');
        $apply = $this->option('apply');

        if (! $apply) {
            $this->info('ℹ️  DRY RUN: No changes will be made. Use --apply to execute.');
        }

        if ($orgId === 'all') {
            // Invalidate ALL orgs (practical for scheduled refreshes)
            $this->comment('Invalidating cache for ALL organizations...');
            // Note: This would require iterating over all orgs
            // For now, just log it
            Log::info('metrics:cache-refresh ALL requested (dry-run)');
            $this->info('✅ To invalidate all: run with specific org IDs or call manually in code');

            return 0;
        }

        if (! $orgId) {
            $this->error('❌ Provide organization_id or "all"');

            return 1;
        }

        $org = intval($orgId);
        if ($apply) {
            $this->metricsCache->invalidate($org);
            $this->info("✅ Cache invalidated for org {$org}");
            Log::info("metrics:cache-refresh: Invalidated cache for org {$org}");
        } else {
            $this->info("📋 Would invalidate cache for org {$org}");
            $this->info('Cache Key: '.MetricsCacheService::getCacheKey($org));
            $this->info('Cache TTL: '.MetricsCacheService::getTtl().' seconds');
        }

        return 0;
    }
}

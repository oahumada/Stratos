<?php

namespace App\Observers;

use App\Models\FinancialIndicator;
use App\Services\Cache\MetricsCacheService;
use Illuminate\Support\Facades\Log;

class FinancialIndicatorObserver
{
    public function __construct(
        protected MetricsCacheService $metricsCache
    ) {}

    /**
     * Invalidate cache when a financial indicator is created
     */
    public function created(FinancialIndicator $indicator): void
    {
        $this->invalidateCache($indicator, 'created');
    }

    /**
     * Invalidate cache when a financial indicator is updated
     */
    public function updated(FinancialIndicator $indicator): void
    {
        $this->invalidateCache($indicator, 'updated');
    }

    /**
     * Invalidate cache when a financial indicator is deleted
     */
    public function deleted(FinancialIndicator $indicator): void
    {
        $this->invalidateCache($indicator, 'deleted');
    }

    /**
     * Common invalidation logic
     */
    protected function invalidateCache(FinancialIndicator $indicator, string $event): void
    {
        $organizationId = $indicator->organization_id;

        if (! $organizationId) {
            Log::warning('FinancialIndicatorObserver: No organization_id on model');

            return;
        }

        $this->metricsCache->invalidate($organizationId);

        Log::info(
            "MetricsCache: Invalidated for org {$organizationId} (FinancialIndicator {$event})",
            ['indicator_id' => $indicator->id, 'indicator_type' => $indicator->indicator_type]
        );
    }
}

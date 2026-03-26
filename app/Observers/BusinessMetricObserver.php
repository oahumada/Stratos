<?php

namespace App\Observers;

use App\Models\BusinessMetric;
use App\Services\Cache\MetricsCacheService;
use Illuminate\Support\Facades\Log;

class BusinessMetricObserver
{
    public function __construct(
        protected MetricsCacheService $metricsCache
    ) {}

    /**
     * Invalidate cache when a business metric is created
     */
    public function created(BusinessMetric $metric): void
    {
        $this->invalidateCache($metric, 'created');
    }

    /**
     * Invalidate cache when a business metric is updated
     */
    public function updated(BusinessMetric $metric): void
    {
        $this->invalidateCache($metric, 'updated');
    }

    /**
     * Invalidate cache when a business metric is deleted
     */
    public function deleted(BusinessMetric $metric): void
    {
        $this->invalidateCache($metric, 'deleted');
    }

    /**
     * Common invalidation logic
     */
    protected function invalidateCache(BusinessMetric $metric, string $event): void
    {
        $organizationId = $metric->organization_id;

        if (! $organizationId) {
            Log::warning('BusinessMetricObserver: No organization_id on model');

            return;
        }

        $this->metricsCache->invalidate($organizationId);

        Log::info(
            "MetricsCache: Invalidated for org {$organizationId} (BusinessMetric {$event})",
            ['metric_id' => $metric->id, 'metric_name' => $metric->metric_name]
        );
    }
}

<?php

namespace App\Listeners;

use App\Models\BusinessMetric;
use App\Services\Cache\MetricsCacheService;
use Illuminate\Database\Events\ModelEvent;
use Illuminate\Support\Facades\Log;

class InvalidateMetricsCacheOnWrite
{
    public function __construct(
        protected MetricsCacheService $metricsCache
    ) {}

    /**
     * Handle business metric writes (create/update/delete)
     * Automatically invalidate cross-request cache for affected organization
     */
    public function handle(ModelEvent $event): void
    {
        // Only process BusinessMetric and FinancialIndicator models
        if (! ($event->model instanceof BusinessMetric)) {
            return;
        }

        $organizationId = $event->model->organization_id;

        if (! $organizationId) {
            Log::warning('InvalidateMetricsCacheOnWrite: No organization_id on model');

            return;
        }

        $this->metricsCache->invalidate($organizationId);

        Log::info(
            "MetricsCache: Invalidated for org {$organizationId} on {$event->event}",
            ['model' => class_basename($event->model)]
        );
    }
}

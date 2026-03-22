<?php

namespace App\Jobs;

use App\Services\IntelligenceMetricsAggregator;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AggregateIntelligenceMetricsDaily implements ShouldQueue
{
    use Queueable;

    protected ?Carbon $date;

    /**
     * Create a new job instance.
     */
    public function __construct(?Carbon $date = null)
    {
        $this->date = $date ?? now()->subDay();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $aggregator = resolve(IntelligenceMetricsAggregator::class);
        $aggregator->aggregateAllMetricsForDate($this->date);
    }
}

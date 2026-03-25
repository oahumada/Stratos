<?php

use App\Models\IntelligenceMetric;
use App\Models\IntelligenceMetricAggregate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('runs in dry-run mode without persisting aggregates', function () {
    IntelligenceMetric::factory()->create([
        'organization_id' => 10,
        'metric_type' => 'rag',
        'source_type' => 'evaluations',
        'duration_ms' => 100,
        'confidence' => 0.9,
        'context_count' => 2,
        'success' => true,
        'created_at' => now()->subDays(2)->setTime(10, 0),
    ]);

    $this->artisan('backfill:intelligence-metric-aggregates', [
        '--from' => now()->subDays(2)->toDateString(),
        '--to' => now()->subDays(2)->toDateString(),
    ])->assertExitCode(0);

    expect(IntelligenceMetricAggregate::count())->toBe(0);
});

it('backfills aggregates and remains idempotent', function () {
    $targetDate = now()->subDays(3)->toDateString();

    IntelligenceMetric::factory()->createMany([
        [
            'organization_id' => 99,
            'metric_type' => 'rag',
            'source_type' => 'evaluations',
            'duration_ms' => 100,
            'confidence' => 0.8,
            'context_count' => 2,
            'success' => true,
            'created_at' => now()->subDays(3)->setTime(9, 0),
        ],
        [
            'organization_id' => 99,
            'metric_type' => 'rag',
            'source_type' => 'evaluations',
            'duration_ms' => 300,
            'confidence' => 0.6,
            'context_count' => 4,
            'success' => false,
            'created_at' => now()->subDays(3)->setTime(11, 0),
        ],
    ]);

    $this->artisan('backfill:intelligence-metric-aggregates', [
        '--from' => $targetDate,
        '--to' => $targetDate,
        '--organization_id' => 99,
        '--apply' => true,
    ])->assertExitCode(0);

    $aggregate = IntelligenceMetricAggregate::query()
        ->where('organization_id', 99)
        ->where('metric_type', 'rag')
        ->where('source_type', 'evaluations')
        ->whereDate('date_key', $targetDate)
        ->first();

    expect($aggregate)->not->toBeNull();
    expect($aggregate->total_count)->toBe(2)
        ->and($aggregate->success_count)->toBe(1)
        ->and((float) $aggregate->success_rate)->toBe(0.5);

    $this->artisan('backfill:intelligence-metric-aggregates', [
        '--from' => $targetDate,
        '--to' => $targetDate,
        '--organization_id' => 99,
        '--apply' => true,
    ])->assertExitCode(0);

    $countAfterSecondRun = IntelligenceMetricAggregate::query()
        ->where('organization_id', 99)
        ->where('metric_type', 'rag')
        ->where('source_type', 'evaluations')
        ->whereDate('date_key', $targetDate)
        ->count();

    expect($countAfterSecondRun)->toBe(1);
});

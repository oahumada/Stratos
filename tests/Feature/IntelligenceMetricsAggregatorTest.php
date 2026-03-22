<?php

use App\Models\IntelligenceMetric;
use App\Models\IntelligenceMetricAggregate;
use App\Services\IntelligenceMetricsAggregator;

beforeEach(function () {
    // Clear existing data
    IntelligenceMetric::truncate();
    IntelligenceMetricAggregate::truncate();
});

it('aggregates metrics and calculates success rate', function () {
    $org = 1;
    $date = now()->subDay();

    // Create 10 metrics: 8 success, 2 failed
    IntelligenceMetric::factory(8)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'success' => true,
        'duration_ms' => 500,
        'created_at' => $date,
    ]);

    IntelligenceMetric::factory(2)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'success' => false,
        'duration_ms' => 1000,
        'created_at' => $date,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate($org, $date);

    expect($aggregates)->toHaveCount(1);
    expect($aggregates[0]['total_count'])->toBe(10);
    expect($aggregates[0]['success_count'])->toBe(8);
    expect($aggregates[0]['success_rate'])->toBe(0.8);
});

it('calculates percentiles correctly', function () {
    $org = 2;
    $date = now()->subDay();

    // Create metrics with specific durations: 100, 200, 300, ..., 1000
    for ($i = 1; $i <= 10; $i++) {
        IntelligenceMetric::factory()->create([
            'organization_id' => $org,
            'metric_type' => 'rag',
            'source_type' => 'evaluations',
            'success' => true,
            'duration_ms' => $i * 100,
            'created_at' => $date,
        ]);
    }

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate($org, $date);

    expect($aggregates)->toHaveCount(1);
    expect($aggregates[0]['p50_duration_ms'])->toBe(500);
    expect($aggregates[0]['p95_duration_ms'])->toBeGreaterThanOrEqual(900);
    expect($aggregates[0]['p99_duration_ms'])->toBeGreaterThanOrEqual(990);
});

it('calculates averages for confidence and context count', function () {
    $org = 3;
    $date = now()->subDay()->startOfDay();

    IntelligenceMetric::factory()->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'success' => true,
        'confidence' => 0.8,
        'context_count' => 5,
        'created_at' => $date,
    ]);

    IntelligenceMetric::factory()->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'success' => true,
        'confidence' => 0.9,
        'context_count' => 3,
        'created_at' => $date,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate($org, $date);

    expect($aggregates)->toHaveCount(1);
    expect($aggregates[0]['avg_confidence'])->toBeGreaterThanOrEqual(0.84);
    expect($aggregates[0]['avg_confidence'])->toBeLessThanOrEqual(0.86);
    expect($aggregates[0]['avg_context_count'])->toBe(4);
});

it('stores aggregates using upsert', function () {
    $org = 4;
    $date = now()->subDay();

    IntelligenceMetric::factory(5)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'success' => true,
        'created_at' => $date,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate($org, $date);
    $aggregator->storeAggregates($aggregates);

    $stored = IntelligenceMetricAggregate::where('organization_id', $org)
        ->where('metric_type', 'rag')
        ->where('date_key', $date->toDateString())
        ->first();

    expect($stored)->not->toBeNull();
    expect($stored->total_count)->toBe(5);
});

it('handles multiple metric types per day', function () {
    $org = 5;
    $date = now()->subDay();

    IntelligenceMetric::factory(3)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'created_at' => $date,
    ]);

    IntelligenceMetric::factory(2)->create([
        'organization_id' => $org,
        'metric_type' => 'llm_call',
        'source_type' => 'evaluations',
        'created_at' => $date,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate($org, $date);

    expect($aggregates)->toHaveCount(2);
    $ragMetric = collect($aggregates)->firstWhere('metric_type', 'rag');
    $llmMetric = collect($aggregates)->firstWhere('metric_type', 'llm_call');

    expect($ragMetric['total_count'])->toBe(3);
    expect($llmMetric['total_count'])->toBe(2);
});

it('aggregates all organizations in aggregateAllMetricsForDate', function () {
    $date = now()->subDay()->startOfDay();

    // Create metrics for org 6
    IntelligenceMetric::factory(5)->create([
        'organization_id' => 6,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'created_at' => $date,
    ]);

    // Create metrics for org 7
    IntelligenceMetric::factory(3)->create([
        'organization_id' => 7,
        'metric_type' => 'rag',
        'source_type' => 'evaluation',
        'created_at' => $date,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregator->aggregateAllMetricsForDate($date);

    $org6Aggregate = IntelligenceMetricAggregate::where('organization_id', 6)
        ->where('date_key', $date->toDateString())
        ->first();

    $org7Aggregate = IntelligenceMetricAggregate::where('organization_id', 7)
        ->where('date_key', $date->toDateString())
        ->first();

    expect($org6Aggregate)->not->toBeNull();
    expect($org7Aggregate)->not->toBeNull();
    expect($org6Aggregate->total_count)->toBe(5);
    expect($org7Aggregate->total_count)->toBe(3);
});

it('uses date from yesterday if no date provided', function () {
    $yesterday = now()->subDay()->startOfDay();

    IntelligenceMetric::factory(2)->create([
        'organization_id' => 8,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'created_at' => $yesterday,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate(8); // No date provided

    expect($aggregates)->toHaveCount(1);
    expect($aggregates[0]['date_key'])->toBe($yesterday->toDateString());
});

it('respects organization_id null scoping', function () {
    $date = now()->subDay()->startOfDay();

    IntelligenceMetric::factory(2)->create([
        'organization_id' => null,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'created_at' => $date,
    ]);

    $aggregator = resolve(IntelligenceMetricsAggregator::class);
    $aggregates = $aggregator->aggregateMetricsForDate(null, $date);

    expect($aggregates)->toHaveCount(1);
    expect($aggregates[0]['organization_id'])->toBeNull();
});

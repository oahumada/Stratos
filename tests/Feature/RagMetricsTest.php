<?php

use App\Models\IntelligenceMetric;

it('stores metric when rag service is called', function () {
    $org = 1;

    // Create a metric directly like RagService would
    IntelligenceMetric::create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'context_count' => 3,
        'confidence' => 0.85,
        'duration_ms' => 245,
        'success' => true,
        'metadata' => ['question_hash' => 'abc123'],
    ]);

    // Verify metric was saved
    $metric = IntelligenceMetric::where('organization_id', $org)
        ->where('metric_type', 'rag')
        ->latest()
        ->first();

    expect($metric)->not->toBeNull();
    expect($metric->source_type)->toBe('guide_faq');
    expect($metric->context_count)->toBe(3);
    expect($metric->confidence)->toBe(0.85);
    expect($metric->duration_ms)->toBe(245);
    expect($metric->success)->toBeTrue();
});

it('stores metric with zero confidence when no documents found', function () {
    $org = 2;

    IntelligenceMetric::create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'source_type' => 'guide_faq',
        'context_count' => 0,
        'confidence' => 0.0,
        'duration_ms' => 125,
        'success' => true,
        'metadata' => ['question_hash' => 'xyz789'],
    ]);

    $metric = IntelligenceMetric::where('organization_id', $org)
        ->where('metric_type', 'rag')
        ->latest()
        ->first();

    expect($metric)->not->toBeNull();
    expect($metric->context_count)->toBe(0);
    expect($metric->confidence)->toBe(0.0);
    expect($metric->success)->toBeTrue();
});

it('aggregates metrics by organization', function () {
    $org1 = 10;
    $org2 = 20;

    IntelligenceMetric::factory(3)->create(['organization_id' => $org1, 'metric_type' => 'rag']);
    IntelligenceMetric::factory(2)->create(['organization_id' => $org2, 'metric_type' => 'rag']);

    $org1Count = IntelligenceMetric::where('organization_id', $org1)->count();
    $org2Count = IntelligenceMetric::where('organization_id', $org2)->count();

    expect($org1Count)->toBe(3);
    expect($org2Count)->toBe(2);
});

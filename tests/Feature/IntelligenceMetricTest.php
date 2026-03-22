<?php

use App\Models\IntelligenceMetric;

it('creates an intelligence metric with factory', function () {
    $metric = IntelligenceMetric::factory()->create();

    expect($metric)->toBeInstanceOf(IntelligenceMetric::class);
    expect($metric->metric_type)->toBeIn(['rag', 'llm_call', 'agent']);
    expect($metric->duration_ms)->toBeInt();
    expect($metric->confidence)->toBeFloat();
    expect($metric->success)->toBeBool();
});

it('casts metadata as array', function () {
    $metric = IntelligenceMetric::factory()->create([
        'metadata' => ['question_hash' => 'abc123', 'provider' => 'openai'],
    ]);

    expect($metric->metadata)->toBeArray();
    expect($metric->metadata['question_hash'])->toBe('abc123');
});

it('scopes by organization and metric type', function () {
    IntelligenceMetric::factory()->create(['organization_id' => 1, 'metric_type' => 'rag']);
    IntelligenceMetric::factory()->create(['organization_id' => 1, 'metric_type' => 'llm_call']);
    IntelligenceMetric::factory()->create(['organization_id' => 2, 'metric_type' => 'rag']);

    $orgOneRag = IntelligenceMetric::where('organization_id', 1)->where('metric_type', 'rag')->count();
    $orgTwoRag = IntelligenceMetric::where('organization_id', 2)->where('metric_type', 'rag')->count();

    expect($orgOneRag)->toBe(1);
    expect($orgTwoRag)->toBe(1);
});

it('stores and retrieves float confidence correctly', function () {
    $metric = IntelligenceMetric::factory()->create(['confidence' => 0.8542]);

    expect($metric->confidence)->toBeGreaterThanOrEqual(0.85);
    expect($metric->confidence)->toBeLessThanOrEqual(0.86);
});

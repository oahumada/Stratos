<?php

use App\Models\IntelligenceMetricAggregate;
use App\Models\User;

beforeEach(function () {
    IntelligenceMetricAggregate::truncate();
});

it('requires authentication for GET /api/intelligence/aggregates', function () {
    $response = $this->getJson('/api/intelligence/aggregates');
    $response->assertUnauthorized();
});

it('returns aggregates for authenticated user', function () {
    $user = User::factory()->create();
    $org = $user->organization_id;

    IntelligenceMetricAggregate::factory(5)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => now()->subDays(5)->toDateString(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates');

    $response->assertOk();
    $response->assertJsonStructure([
        'status',
        'data' => [],
        'pagination' => [
            'total',
            'per_page',
            'current_page',
            'last_page',
        ],
        'metadata',
    ]);
    expect($response->json('data'))->toHaveCount(5);
});

it('filters aggregates by metric_type', function () {
    $user = User::factory()->create();
    $org = $user->organization_id;
    $date = now()->subDays(5)->toDateString();

    IntelligenceMetricAggregate::factory(3)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => $date,
    ]);

    IntelligenceMetricAggregate::factory(2)->create([
        'organization_id' => $org,
        'metric_type' => 'llm_call',
        'date_key' => $date,
    ]);

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates?metric_type=rag');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
    expect($response->json('metadata.metric_type'))->toBe('rag');
});

it('filters aggregates by date range', function () {
    $user = User::factory()->create();
    $org = $user->organization_id;

    IntelligenceMetricAggregate::factory(2)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => now()->subDays(35)->toDateString(),
    ]);

    IntelligenceMetricAggregate::factory(3)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => now()->subDays(5)->toDateString(),
    ]);

    $dateFrom = now()->subDays(10)->toDateString();
    $dateTo = now()->toDateString();

    $response = $this->actingAs($user)->getJson("/api/intelligence/aggregates?date_from={$dateFrom}&date_to={$dateTo}");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

it('uses default date range of 30 days when not specified', function () {
    $user = User::factory()->create();
    $org = $user->organization_id;

    // Create old data (beyond 30 days)
    IntelligenceMetricAggregate::factory(2)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => now()->subDays(35)->toDateString(),
    ]);

    // Create recent data (within 30 days)
    IntelligenceMetricAggregate::factory(3)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => now()->subDays(5)->toDateString(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

it('scopes aggregates by organization_id', function () {
    $user = User::factory()->create();
    $orgId = $user->organization_id;

    $otherOrg = rand(999, 9999); // Different org
    $date = now()->subDays(5)->toDateString();

    // Create aggregates for user's org with different source_types
    for ($i = 0; $i < 5; $i++) {
        IntelligenceMetricAggregate::factory()->create([
            'organization_id' => $orgId,
            'metric_type' => 'rag',
            'source_type' => ['guide_faq', 'evaluations', 'roles', 'scenarios', null][$i],
            'date_key' => $date,
        ]);
    }

    // Create aggregates for other org with different source_types
    for ($i = 0; $i < 3; $i++) {
        IntelligenceMetricAggregate::factory()->create([
            'organization_id' => $otherOrg,
            'metric_type' => 'rag',
            'source_type' => ['guide_faq', 'evaluations', 'roles'][$i],
            'date_key' => $date,
        ]);
    }

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});

it('returns summary statistics', function () {
    $user = User::factory()->create();
    $org = $user->organization_id;
    $date = now()->subDays(5)->toDateString();

    IntelligenceMetricAggregate::factory(3)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => $date,
        'total_count' => 100,
        'success_count' => 90,
        'success_rate' => 0.9,
        'avg_duration_ms' => 500,
        'p50_duration_ms' => 450,
        'p95_duration_ms' => 900,
        'p99_duration_ms' => 950,
        'avg_confidence' => 0.85,
        'avg_context_count' => 3,
    ]);

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates/summary');

    $response->assertOk();
    $response->assertJsonStructure([
        'status',
        'data' => [
            'total_records',
            'total_calls',
            'successful_calls',
            'success_rate',
            'avg_duration_ms',
            'p50_duration_ms',
            'p95_duration_ms',
            'p99_duration_ms',
            'avg_confidence',
            'avg_context_count',
        ],
        'metadata',
    ]);

    expect($response->json('data.total_records'))->toBe(3);
    expect($response->json('data.total_calls'))->toBe(300);
    expect($response->json('data.successful_calls'))->toBe(270);
});

it('handles empty results gracefully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates');

    $response->assertOk();
    expect($response->json('data'))->toBeEmpty();
    expect($response->json('pagination.total'))->toBe(0);
});

it('respects per_page parameter', function () {
    $user = User::factory()->create();
    $org = $user->organization_id;

    IntelligenceMetricAggregate::factory(25)->create([
        'organization_id' => $org,
        'metric_type' => 'rag',
        'date_key' => now()->subDays(5)->toDateString(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/intelligence/aggregates?per_page=10');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(10);
    expect($response->json('pagination.per_page'))->toBe(10);
    expect($response->json('pagination.total'))->toBe(25);
});

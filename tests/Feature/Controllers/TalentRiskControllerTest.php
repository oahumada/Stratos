<?php

use App\Models\Scenario;
use App\Models\TalentRiskIndicator;
use App\Models\User;

test('can list risk indicators', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    TalentRiskIndicator::factory(3)->create(['scenario_id' => $scenario->id, 'organization_id' => $scenario->organization_id]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/risks/indicators");

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
})->group('risks');

test('can trigger risk analysis', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);

    $response = $this
        ->actingAs($user)
        ->postJson("/api/scenarios/{$scenario->id}/risks/analyze");

    $response->assertSuccessful();
    expect($response->json('count'))->toBeGreaterThanOrEqual(0);
})->group('risks');

test('can get risk summary', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    TalentRiskIndicator::factory(5)->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $scenario->organization_id,
        'risk_score' => 75,
    ]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/risks/summary");

    $response->assertSuccessful();
    $data = $response->json('data');
    expect($data)->toHaveKeys(['total_indicators', 'high_risk_count', 'average_risk_score']);
})->group('risks');

test('can get risk heatmap', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    TalentRiskIndicator::factory(5)->create(['scenario_id' => $scenario->id, 'organization_id' => $scenario->organization_id]);
})->group('risks');

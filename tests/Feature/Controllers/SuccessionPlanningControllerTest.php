<?php

use App\Models\Scenario;
use App\Models\SuccessionCandidate;
use App\Models\User;

test('can list succession candidates', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    SuccessionCandidate::factory(3)->create(['scenario_id' => $scenario->id, 'organization_id' => $scenario->organization_id]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/succession/candidates");

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
})->group('succession');

test('can create succession candidate', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    $person = \App\Models\People::factory()->create(['organization_id' => $scenario->organization_id]);
    $role = \App\Models\Roles::factory()->create(['organization_id' => $scenario->organization_id]);

    $response = $this
        ->actingAs($user)
        ->postJson("/api/scenarios/{$scenario->id}/succession/candidates", [
            'person_id' => $person->id,
            'target_role_id' => $role->id,
        ]);

    $response->assertCreated();
    expect($response->json('data.person_id'))->toBe($person->id);
})->group('succession');

test('can get succession coverage', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    SuccessionCandidate::factory(2)->create(['scenario_id' => $scenario->id]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/succession/coverage");

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
})->group('succession');

test('can analyze successors', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    SuccessionCandidate::factory(3)->create(['scenario_id' => $scenario->id]);

    $response = $this
        ->actingAs($user)
        ->postJson("/api/scenarios/{$scenario->id}/succession/analyze");

    $response->assertSuccessful();
    expect($response->json('count'))->toBeGreaterThanOrEqual(0);
})->group('succession');

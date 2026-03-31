<?php

use App\Models\Scenario;
use App\Models\TransformationPhase;
use App\Models\User;

test('can get transformation roadmap', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/transformation/roadmap");

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
})->group('roadmap');

test('can generate transformation plan', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);

    $response = $this
        ->actingAs($user)
        ->postJson("/api/scenarios/{$scenario->id}/transformation/generate");

    $response->assertSuccessful();
    expect($response->json('message'))->toContain('generated');

    $phases = TransformationPhase::where('scenario_id', $scenario->id)->count();
    expect($phases)->toBeGreaterThan(0);
})->group('roadmap');

test('can list transformation phases', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    TransformationPhase::factory(5)->create(['scenario_id' => $scenario->id, 'organization_id' => $scenario->organization_id]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/transformation/phases");

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
    expect($response->json('pagination.total'))->toBeGreaterThan(0);
})->group('roadmap');

test('can create transformation task', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    $phase = TransformationPhase::factory()->create(['scenario_id' => $scenario->id, 'organization_id' => $scenario->organization_id]);
    $owner = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->postJson("/api/scenarios/{$scenario->id}/transformation/tasks", [
            'phase_id' => $phase->id,
            'task_name' => 'Test Task',
            'owner_id' => $owner->id,
        ]);

    $response->assertCreated();
    expect($response->json('data.phase_id'))->toBe($phase->id);
})->group('roadmap');

test('can update task status', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);
    $phase = TransformationPhase::factory()->create(['scenario_id' => $scenario->id, 'organization_id' => $scenario->organization_id]);
    $task = \App\Models\TransformationTask::factory()->create(['phase_id' => $phase->id, 'organization_id' => $phase->organization_id]);

    $response = $this
        ->actingAs($user)
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

    $response->assertSuccessful();
    expect($response->json('data.status'))->toBe('completed');
})->group('roadmap');

test('can get blockers', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $user->update(['current_organization_id' => $user->organization_id]);
    $scenario = Scenario::factory()->create(['organization_id' => $user->organization_id]);

    $response = $this
        ->actingAs($user)
        ->getJson("/api/scenarios/{$scenario->id}/transformation/blockers");

    $response->assertSuccessful();
    expect($response->json('data'))->toBeArray();
})->group('roadmap');

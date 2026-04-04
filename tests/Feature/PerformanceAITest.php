<?php

use App\Models\Organization;
use App\Models\People;
use App\Models\PerformanceCycle;
use App\Models\PerformanceReview;
use App\Models\Roles;
use App\Services\PerformanceAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'perf_test');
    Sanctum::actingAs($this->user, ['*']);

    // Create a role and person in this org
    $role = Roles::factory()->create(['organization_id' => $this->org->id]);
    $this->person = People::factory()->create([
        'organization_id' => $this->org->id,
        'role_id' => $role->id,
    ]);
});

it('can list cycles (empty)', function () {
    $response = $this->getJson('/api/performance/cycles');

    $response->assertOk()
        ->assertJsonStructure(['data'])
        ->assertJsonCount(0, 'data');
});

it('can create a performance cycle', function () {
    $response = $this->postJson('/api/performance/cycles', [
        'name' => 'Q1 2026 Review',
        'period' => '2026-Q1',
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-03-31',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Q1 2026 Review')
        ->assertJsonPath('data.status', 'draft')
        ->assertJsonPath('data.period', '2026-Q1');

    $this->assertDatabaseHas('performance_cycles', [
        'name' => 'Q1 2026 Review',
        'organization_id' => $this->org->id,
    ]);
});

it('cycle has correct status after activate', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/performance/cycles/{$cycle->id}/activate");

    $response->assertOk()
        ->assertJsonPath('data.status', 'active');

    $this->assertDatabaseHas('performance_cycles', [
        'id' => $cycle->id,
        'status' => 'active',
    ]);
});

it('can close a cycle and sets status to closed', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
        'status' => 'active',
    ]);

    $response = $this->postJson("/api/performance/cycles/{$cycle->id}/close");

    $response->assertOk()
        ->assertJsonPath('data.status', 'closed');

    $this->assertDatabaseHas('performance_cycles', [
        'id' => $cycle->id,
        'status' => 'closed',
    ]);
});

it('can create a review with auto-computed final_score', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
    ]);

    $response = $this->postJson("/api/performance/cycles/{$cycle->id}/reviews", [
        'people_id' => $this->person->id,
        'self_score' => 70,
        'manager_score' => 80,
        'peer_score' => 75,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.people_id', $this->person->id);

    $finalScore = $response->json('data.final_score');
    expect($finalScore)->not->toBeNull();
});

it('final_score weighted average is correct', function () {
    // self=60 (20%), manager=80 (50%), peer=70 (30%)
    // Expected: 60*0.2 + 80*0.5 + 70*0.3 = 12 + 40 + 21 = 73.0
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
    ]);

    $response = $this->postJson("/api/performance/cycles/{$cycle->id}/reviews", [
        'people_id' => $this->person->id,
        'self_score' => 60,
        'manager_score' => 80,
        'peer_score' => 70,
    ]);

    $response->assertCreated();
    $finalScore = (float) $response->json('data.final_score');
    expect($finalScore)->toBe(73.0);
});

it('can update review scores and final_score recomputes', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
    ]);

    $review = PerformanceReview::factory()->create([
        'organization_id' => $this->org->id,
        'cycle_id' => $cycle->id,
        'people_id' => $this->person->id,
        'self_score' => 50,
        'manager_score' => 50,
        'peer_score' => 50,
        'final_score' => 50,
    ]);

    $response = $this->patchJson("/api/performance/cycles/{$cycle->id}/reviews/{$review->id}", [
        'self_score' => 100,
        'manager_score' => 100,
        'peer_score' => 100,
    ]);

    $response->assertOk();
    $finalScore = (float) $response->json('data.final_score');
    expect($finalScore)->toBe(100.0);
});

it('can calibrate a cycle with 3+ reviews', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
    ]);

    $role = Roles::factory()->create(['organization_id' => $this->org->id]);

    foreach ([60, 80, 95] as $score) {
        $person = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
        ]);
        PerformanceReview::factory()->create([
            'organization_id' => $this->org->id,
            'cycle_id' => $cycle->id,
            'people_id' => $person->id,
            'final_score' => $score,
            'status' => 'completed',
        ]);
    }

    $response = $this->postJson("/api/performance/cycles/{$cycle->id}/calibrate");

    $response->assertOk()
        ->assertJsonStructure(['data' => ['adjusted', 'mean', 'std_dev']]);
});

it('calibrate returns adjusted count, mean, and std_dev', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
    ]);

    $role = Roles::factory()->create(['organization_id' => $this->org->id]);
    $scores = [50, 55, 60, 90];

    foreach ($scores as $score) {
        $person = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
        ]);
        PerformanceReview::factory()->create([
            'organization_id' => $this->org->id,
            'cycle_id' => $cycle->id,
            'people_id' => $person->id,
            'final_score' => $score,
            'status' => 'completed',
        ]);
    }

    $response = $this->postJson("/api/performance/cycles/{$cycle->id}/calibrate");
    $data = $response->json('data');

    expect($data['adjusted'])->toBe(4);
    expect($data['mean'])->toBe(63.75);
    expect($data['std_dev'])->toBeGreaterThan(0);
});

it('insights returns top_performers, needs_attention, and distribution', function () {
    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
    ]);

    $role = Roles::factory()->create(['organization_id' => $this->org->id]);
    $scores = [90, 85, 60, 40, 30];

    foreach ($scores as $score) {
        $person = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
        ]);
        PerformanceReview::factory()->create([
            'organization_id' => $this->org->id,
            'cycle_id' => $cycle->id,
            'people_id' => $person->id,
            'final_score' => $score,
            'status' => 'completed',
        ]);
    }

    $response = $this->getJson("/api/performance/cycles/{$cycle->id}/insights");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'top_performers',
                'needs_attention',
                'avg_score',
                'distribution' => ['high', 'mid', 'low'],
            ],
        ]);

    $data = $response->json('data');
    expect(count($data['top_performers']))->toBe(2);
    expect(count($data['needs_attention']))->toBe(2);
    expect($data['distribution']['high'])->toBe(2);
    expect($data['distribution']['mid'])->toBe(1);
    expect($data['distribution']['low'])->toBe(2);
});

it('cannot access another org cycles', function () {
    $otherOrg = Organization::factory()->create();
    $otherUser = createUserForOrganizationWithRole($otherOrg, 'perf_test');

    $cycle = PerformanceCycle::factory()->create([
        'organization_id' => $otherOrg->id,
        'created_by' => $otherUser->id,
    ]);

    // Our user (different org) tries to access
    $response = $this->getJson("/api/performance/cycles/{$cycle->id}");
    $response->assertNotFound();
});

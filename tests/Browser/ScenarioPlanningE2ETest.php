<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->organization->id,
        'created_by' => $this->user->id,
    ]);
});

test('executive summary api returns kpi cards', function () {
    $response = $this->actingAs($this->user)->getJson(
        "/api/strategic-planning/scenarios/{$this->scenario->id}/executive-summary"
    );
    $response->assertStatus(200)->assertJsonHas('scenario_id');
});

test('org chart api returns organizational data', function () {
    $response = $this->actingAs($this->user)->getJson(
        "/api/strategic-planning/scenarios/{$this->scenario->id}/org-chart"
    );
    $response->assertStatus(200)->assertJsonHas('scenario_id');
});

test('export pdf endpoint works', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/strategic-planning/scenarios/{$this->scenario->id}/executive-summary/export/pdf"
    );
    $response->assertStatus(200)->assertJsonHas('status');
});

test('export pptx endpoint works', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/strategic-planning/scenarios/{$this->scenario->id}/executive-summary/export/pptx"
    );
    $response->assertStatus(200)->assertJsonHas('status');
});

test('what if analysis endpoint works', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/strategic-planning/scenarios/{$this->scenario->id}/what-if/comprehensive",
        ['headcount_delta' => 25]
    );
    $response->assertStatus(200);
});

test('analytics dashboard loads', function () {
    $response = $this->actingAs($this->user)->get(
        "/scenario-planning/analytics?scenario_id={$this->scenario->id}"
    );
    $response->assertStatus(200);
});

test('unauthorized user cannot access', function () {
    $otherUser = User::factory()->create(['organization_id' => Organization::factory()->create()->id]);
    $response = $this->actingAs($otherUser)->getJson(
        "/api/strategic-planning/scenarios/{$this->scenario->id}/executive-summary"
    );
    $response->assertForbidden();
});

test('complete workflow integration', function () {
    $this->actingAs($this->user)
        ->getJson("/api/strategic-planning/scenarios/{$this->scenario->id}/executive-summary")
        ->assertStatus(200);
    
    $this->actingAs($this->user)
        ->getJson("/api/strategic-planning/scenarios/{$this->scenario->id}/org-chart")
        ->assertStatus(200);
    
    $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/what-if/comprehensive", ['headcount_delta' => 25])
        ->assertStatus(200);
    
    $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/executive-summary/export/pdf")
        ->assertStatus(200);
});

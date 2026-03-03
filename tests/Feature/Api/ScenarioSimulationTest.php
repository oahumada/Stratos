<?php

use App\Models\Organizations;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Growth Test Scenario',
    ]);
});

it('simulates talent growth for a scenario', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/simulate-growth", [
            'growth_percentage' => 15,
            'horizon_months' => 24,
            'target_departments' => ['Engineering', 'Product'],
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'simulation' => [
                'current_talent_pool',
                'projected_talent_requirement',
                'net_capacity_gap',
                'by_capability_area',
                'strategic_skills_needed',
                'critical_talent_risks',
            ],
        ],
    ]);

    expect($response->json('success'))->toBeTrue();
});

it('gets critical talents at risk', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/api/strategic-planning/critical-talents?scenario_id={$this->scenario->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            '*' => [
                'capability',
                'role_archetype',
                'criticality_score',
                'impact_analysis',
            ],
        ],
        'summary' => [
            'total_critical_nodes',
            'nodes_secured',
            'nodes_at_risk',
            'high_risk_exposure_pct',
        ],
    ]);

    expect($response->json('success'))->toBeTrue();
});

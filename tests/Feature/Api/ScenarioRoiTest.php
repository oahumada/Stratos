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
        'name' => 'ROI Test Scenario',
    ]);
});

it('calculates strategic ROI for different talent acquisition modes', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/roi-calculator/calculate', [
            'scenario_id' => $this->scenario->id,
            'talent_nodes_needed' => 10,
            'acquisition_cost_per_node' => 15000,
            'reskilling_cost_per_node' => 5000,
            'reskilling_duration_months' => 6,
            'internal_talent_pipeline' => 15,
            'external_market_salary' => 80000,
            'internal_maintenance_salary' => 60000,
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'roi_comparison' => [
                'build',
                'buy',
                'borrow',
                'bot',
            ],
            'recommendation' => [
                'strategy',
                'reasoning',
            ],
        ],
    ]);

    expect($response->json('success'))->toBeTrue();
});

it('validates required fields for ROI calculation', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/roi-calculator/calculate', [
            'scenario_id' => $this->scenario->id,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'talent_nodes_needed',
        'acquisition_cost_per_node',
        'reskilling_cost_per_node',
    ]);
});

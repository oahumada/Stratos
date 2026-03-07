<?php

use App\Models\Organizations;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->actingAs($this->user);
    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Report Scenario Q3',
        'status' => 'active',
    ]);

    // Create a capability and attach it to the scenario to avoid arithmetic/missing data errors
    $capability = \App\Models\Capability::create([
        'organization_id' => $this->org->id,
        'name' => 'Strategic AI Capability',
        'code' => 'CAP-'.uniqid(),
    ]);

    \DB::table('scenario_capabilities')->insert([
        'scenario_id' => $this->scenario->id,
        'capability_id' => $capability->id,
        'strategic_weight' => 10,
        'strategic_role' => 'core',
    ]);
});

it('generates a scenario impact report', function () {
    $response = $this->getJson("/api/reports/scenario/{$this->scenario->id}/impact");

    if ($response->status() !== 200) {
        dump($response->json());
    }

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'meta',
            'executive_summary',
        ],
    ]);
});

it('generates an organizational ROI report', function () {
    $response = $this->getJson('/api/reports/roi');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'meta',
            'kpis',
            'roi_metrics',
            'active_scenarios',
            'learning_progress',
            'distributions',
            'talent_pipeline',
        ],
    ]);
});

it('generates a consolidated impact report', function () {
    $response = $this->getJson('/api/reports/consolidated');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'meta',
            'scenario_impact',
            'organizational_roi',
            'strategic_recommendations',
        ],
    ]);
});

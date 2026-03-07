<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\Roles;
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
        'status' => 'active',
    ]);
});

it('simulates attrition crisis', function () {
    $response = $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/crisis/attrition", [
        'attrition_rate' => 20,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'crisis_type',
            'impact' => [
                'people_at_risk',
                'replacement_cost_usd',
            ],
        ],
    ]);
});

it('simulates skill obsolescence crisis', function () {
    $response = $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/crisis/obsolescence", [
        'obsolete_skill_ids' => [1, 2, 3],
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'crisis_type',
            'impact' => [
                'obsolete_skills_count',
                'people_affected',
            ],
        ],
    ]);
});

it('calculates career paths for a person', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    $response = $this->getJson("/api/career-paths/{$person->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'person',
            'career_paths',
        ],
    ]);
});

it('calculates optimal route between roles', function () {
    $roleA = Roles::factory()->create(['organization_id' => $this->org->id]);
    $roleB = Roles::factory()->create(['organization_id' => $this->org->id]);

    $response = $this->getJson("/api/career-paths/route/{$roleA->id}/{$roleB->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'from_role',
            'to_role',
            'transferability_score',
            'route',
        ],
    ]);
});

<?php

use App\Models\Organization;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const WORKFORCE_STATUS_ENDPOINT = '/api/strategic-planning/workforce-planning/scenarios/';

const SCENARIO_STATUS_PATH = '/status';

it('transitions scenario status through allowed workforce workflow', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'hr_leader');
    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'in_review',
    ])->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.previous_status', 'draft')
        ->assertJsonPath('data.status', 'in_review');

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'approved',
    ])->assertOk()
        ->assertJsonPath('data.previous_status', 'in_review')
        ->assertJsonPath('data.status', 'approved');

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'active',
    ])->assertOk()
        ->assertJsonPath('data.previous_status', 'approved')
        ->assertJsonPath('data.status', 'active');

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'completed',
    ])->assertOk()
        ->assertJsonPath('data.previous_status', 'active')
        ->assertJsonPath('data.status', 'completed');

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'archived',
    ])->assertOk()
        ->assertJsonPath('data.previous_status', 'completed')
        ->assertJsonPath('data.status', 'archived');
});

it('rejects invalid workforce status transition', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'hr_leader');
    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'active',
    ])
        ->assertStatus(409)
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.current_status', 'draft')
        ->assertJsonPath('errors.target_status', 'active');
});

it('returns 404 for status transition when scenario belongs to another organization', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($orgA, 'hr_leader');
    $scenario = Scenario::factory()->create([
        'organization_id' => $orgB->id,
        'status' => 'draft',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'in_review',
    ])->assertNotFound();
});

it('forbids scenario status transition for non-governance roles', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_STATUS_ENDPOINT.$scenario->id.SCENARIO_STATUS_PATH, [
        'status' => 'in_review',
    ])->assertForbidden();
});

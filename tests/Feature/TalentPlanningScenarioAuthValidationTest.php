<?php

use App\Models\Organization;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const SCENARIO_LIST_ENDPOINT = '/api/strategic-planning/scenarios';
const SCENARIO_SHOW_PATTERN = '/api/strategic-planning/scenarios/%d';

it('requires authentication for scenario list endpoint', function () {
    $this->json('GET', SCENARIO_LIST_ENDPOINT)->assertUnauthorized();
});

it('forbids scenario list endpoint when user organization cannot be resolved', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(SCENARIO_LIST_ENDPOINT)
        ->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para Talent Planning');
});

it('allows scenario list for authenticated users within their organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    Scenario::factory()->create(['organization_id' => $organization->id]);
    Scenario::factory()->create(['organization_id' => $organization->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(SCENARIO_LIST_ENDPOINT)
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'items' => [
                    '*' => ['id', 'name', 'code', 'status'],
                ],
                'pagination' => [
                    'current_page',
                    'total',
                    'per_page',
                    'last_page',
                ],
            ],
        ]);
});

it('requires authentication for scenario show endpoint', function () {
    $this->json('GET', sprintf(SCENARIO_SHOW_PATTERN, 1))->assertUnauthorized();
});

it('returns 422 when organization cannot be resolved for scenario show', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf(SCENARIO_SHOW_PATTERN, 1))
        ->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para Talent Planning');
});

it('allows scenario show for resources within user organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'name' => 'Strategic Plan 2025',
        'status' => 'approved',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf(SCENARIO_SHOW_PATTERN, $scenario->id))
        ->assertOk()
        ->assertJsonPath('data.id', $scenario->id)
        ->assertJsonPath('data.name', 'Strategic Plan 2025');
});

it('returns 404 when accessing scenarios from other organizations', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($org1, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org2->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf(SCENARIO_SHOW_PATTERN, $scenario->id))
        ->assertNotFound();
});

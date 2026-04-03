<?php

use App\Models\ChangeSet;
use App\Models\Organization;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const CHANGESET_CREATE_PATTERN = '/api/strategic-planning/scenarios/%d/change-sets';
const CHANGESET_PREVIEW_PATTERN = '/api/strategic-planning/change-sets/%d/preview';
const CHANGESET_APPLY_PATTERN = '/api/strategic-planning/change-sets/%d/apply';

it('requires authentication for changeset creation', function () {
    $this->json('POST', sprintf(CHANGESET_CREATE_PATTERN, 1))->assertUnauthorized();
});

it('requires authentication for changeset preview', function () {
    $this->json('GET', sprintf(CHANGESET_PREVIEW_PATTERN, 1))->assertUnauthorized();
});

it('returns 422 when organization cannot be resolved for changeset operations', function (string $method, string $endpoint) {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    if ($method === 'POST' && strpos($endpoint, 'change-sets') === false) {
        // Create scenario scenario_id first for the creation test
        $this->json($method, sprintf(CHANGESET_CREATE_PATTERN, 1))
            ->assertStatus(422)
            ->assertJsonPath('message', 'No se pudo resolver organization_id para ChangeSet');
    } else {
        $this->json($method, $endpoint)
            ->assertStatus(422)
            ->assertJsonPath('message', 'No se pudo resolver organization_id para ChangeSet');
    }
})->with([
    'POST create changeset' => ['POST', sprintf(CHANGESET_CREATE_PATTERN, 1)],
    'GET preview changeset' => ['GET', sprintf(CHANGESET_PREVIEW_PATTERN, 1)],
    'POST apply changeset' => ['POST', sprintf(CHANGESET_APPLY_PATTERN, 1)],
]);

it('allows changeset creation for scenarios within user organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $scenario = Scenario::factory()->create(['organization_id' => $organization->id]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(CHANGESET_CREATE_PATTERN, $scenario->id), [
        'title' => 'Q1 Transformation Changes',
        'description' => 'Pending approval',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.scenario_id', (string) $scenario->id)
        ->assertJsonPath('data.organization_id', $organization->id);
});

it('prevents changeset access from other organizations', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($org1, 'talent_planner');
    $changeset = ChangeSet::factory()->create(['organization_id' => $org2->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf(CHANGESET_PREVIEW_PATTERN, $changeset->id))
        ->assertNotFound();
});

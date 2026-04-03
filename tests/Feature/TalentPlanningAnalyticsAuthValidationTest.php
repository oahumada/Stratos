<?php

use App\Models\Organization;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const SCENARIO_ANALYTICS_PATTERN = '/api/scenarios/%d/analytics';
const SCENARIO_FINANCIAL_PATTERN = '/api/scenarios/%d/financial-impact';
const SCENARIO_RISK_PATTERN = '/api/scenarios/%d/risk-assessment';
const SCENARIO_SKILLGAPS_PATTERN = '/api/scenarios/%d/skill-gaps';

it('requires authentication for scenario analytics endpoints', function (string $method, string $endpoint) {
    $this->json($method, sprintf($endpoint, 1))->assertUnauthorized();
})->with([
    'GET analytics' => ['GET', SCENARIO_ANALYTICS_PATTERN],
    'GET financial-impact' => ['GET', SCENARIO_FINANCIAL_PATTERN],
    'GET risk-assessment' => ['GET', SCENARIO_RISK_PATTERN],
    'GET skill-gaps' => ['GET', SCENARIO_SKILLGAPS_PATTERN],
]);

it('returns 422 when organization cannot be resolved for analytics endpoints', function (string $method, string $endpoint) {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    $this->json($method, sprintf($endpoint, 1))
        ->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para Talent Planning Analytics');
})->with([
    'GET analytics 422' => ['GET', SCENARIO_ANALYTICS_PATTERN],
    'GET financial-impact 422' => ['GET', SCENARIO_FINANCIAL_PATTERN],
    'GET risk-assessment 422' => ['GET', SCENARIO_RISK_PATTERN],
    'GET skill-gaps 422' => ['GET', SCENARIO_SKILLGAPS_PATTERN],
]);

it('allows analytics endpoints for scenarios within user organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    grantPermissionToRole('talent_planner', 'scenarios.view', 'talent_planning', 'view');

    $scenario = Scenario::factory()->create(['organization_id' => $organization->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf(SCENARIO_ANALYTICS_PATTERN, $scenario->id))
        ->assertOk()
        ->assertJsonStructure(['scenario_id', 'name', 'iq']);

    $this->getJson(sprintf(SCENARIO_FINANCIAL_PATTERN, $scenario->id))
        ->assertOk()
        ->assertJsonStructure(['scenario_id', 'financial_impact']);
});

it('returns 404 when accessing scenarios from other organizations via analytics', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($org1, 'talent_planner');
    grantPermissionToRole('talent_planner', 'scenarios.view', 'talent_planning', 'view');

    $scenario = Scenario::factory()->create(['organization_id' => $org2->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf(SCENARIO_ANALYTICS_PATTERN, $scenario->id))
        ->assertNotFound();

    $this->getJson(sprintf(SCENARIO_FINANCIAL_PATTERN, $scenario->id))
        ->assertNotFound();
});

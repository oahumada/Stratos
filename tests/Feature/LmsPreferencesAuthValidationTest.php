<?php

use App\Http\Middleware\CheckPermission;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const LMS_PREFERENCES_AUTH_ENDPOINT = '/api/lms/preferences';

it('requires authentication for preferences endpoints', function (string $method, array $payload = []) {
    $this->json($method, LMS_PREFERENCES_AUTH_ENDPOINT, $payload)
        ->assertUnauthorized();
})->with([
    'GET preferences' => ['GET'],
    'PATCH preferences' => ['PATCH', ['show_completed_interventions' => true]],
]);

it('validates payload when updating lms preferences', function () {
    $this->withoutMiddleware(CheckPermission::class);

    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'collaborator');

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(LMS_PREFERENCES_AUTH_ENDPOINT, [
        'show_completed_interventions' => 'invalid',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['show_completed_interventions']);
});

it('forbids preferences endpoints when user lacks permission', function (string $method, array $payload = []) {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_pref_no_access_generic');

    Sanctum::actingAs($user, ['*']);

    $this->json($method, LMS_PREFERENCES_AUTH_ENDPOINT, $payload)
        ->assertForbidden();
})->with([
    'GET forbidden' => ['GET'],
    'PATCH forbidden' => ['PATCH', ['show_completed_interventions' => true]],
]);

it('allows reading and updating lms preferences when permission is granted', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_pref_access');

    grantPermissionToRole(
        'qa_lms_pref_access',
        'lms.courses.view',
        'lms',
        'view',
        'Permite consultar preferencias LMS',
    );

    Sanctum::actingAs($user, ['*']);

    $this->getJson(LMS_PREFERENCES_AUTH_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('data.show_completed_interventions', false);

    $this->patchJson(LMS_PREFERENCES_AUTH_ENDPOINT, [
        'show_completed_interventions' => true,
    ])->assertOk()
        ->assertJsonPath('data.show_completed_interventions', true);
});

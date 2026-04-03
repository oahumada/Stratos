<?php

use App\Http\Middleware\CheckPermission;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const LMS_INTERVENTIONS_AUTH_ENDPOINT = '/api/lms/interventions';
const LMS_INTERVENTIONS_AUTH_RESET_ENDPOINT = '/api/lms/interventions/reset';
const LMS_INTERVENTIONS_AUTH_COMPLETE_PATTERN = '/api/lms/interventions/%d/complete';

it('requires authentication for intervention endpoints', function () {
    $organization = Organization::factory()->create();
    $course = LmsCourse::query()->create([
        'title' => 'Auth Matrix Course',
        'organization_id' => $organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 30,
        'status' => 'in_progress',
    ]);

    $requests = [
        ['GET', LMS_INTERVENTIONS_AUTH_ENDPOINT, []],
        ['POST', LMS_INTERVENTIONS_AUTH_ENDPOINT, ['lms_enrollment_id' => $enrollment->id]],
        ['POST', LMS_INTERVENTIONS_AUTH_RESET_ENDPOINT, []],
        ['POST', sprintf(LMS_INTERVENTIONS_AUTH_COMPLETE_PATTERN, $enrollment->id), []],
    ];

    foreach ($requests as [$method, $endpoint, $payload]) {
        $this->json($method, $endpoint, $payload)->assertUnauthorized();
    }
});

it('forbids intervention endpoints when user lacks lms view permission', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_intervention_no_access');

    $course = LmsCourse::query()->create([
        'title' => 'Forbidden Matrix Course',
        'organization_id' => $organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 40,
        'status' => 'in_progress',
    ]);

    Sanctum::actingAs($user, ['*']);

    $requests = [
        ['GET', LMS_INTERVENTIONS_AUTH_ENDPOINT, []],
        ['POST', LMS_INTERVENTIONS_AUTH_ENDPOINT, ['lms_enrollment_id' => $enrollment->id]],
        ['POST', LMS_INTERVENTIONS_AUTH_RESET_ENDPOINT, []],
        ['POST', sprintf(LMS_INTERVENTIONS_AUTH_COMPLETE_PATTERN, $enrollment->id), []],
    ];

    foreach ($requests as [$method, $endpoint, $payload]) {
        $this->json($method, $endpoint, $payload)->assertForbidden();
    }
});

it('validates payload when storing interventions', function () {
    $this->withoutMiddleware(CheckPermission::class);

    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'collaborator');

    Sanctum::actingAs($user, ['*']);

    $this->postJson(LMS_INTERVENTIONS_AUTH_ENDPOINT, [])->assertStatus(422)
        ->assertJsonValidationErrors(['lms_enrollment_id']);
});

it('allows intervention endpoints when lms view permission is granted', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_intervention_access');

    grantPermissionToRole('qa_lms_intervention_access', 'lms.courses.view', 'lms', 'view');

    $course = LmsCourse::query()->create([
        'title' => 'Allowed Matrix Course',
        'organization_id' => $organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 60,
        'status' => 'in_progress',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(LMS_INTERVENTIONS_AUTH_ENDPOINT)->assertOk();

    $this->postJson(LMS_INTERVENTIONS_AUTH_ENDPOINT, [
        'lms_enrollment_id' => $enrollment->id,
    ])->assertCreated();

    $this->postJson(LMS_INTERVENTIONS_AUTH_RESET_ENDPOINT)->assertOk();

    $this->postJson(sprintf(LMS_INTERVENTIONS_AUTH_COMPLETE_PATTERN, $enrollment->id))
        ->assertNotFound();
});

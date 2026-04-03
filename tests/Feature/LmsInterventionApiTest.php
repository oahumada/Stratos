<?php

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsIntervention;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

const LMS_INTERVENTIONS_ENDPOINT = '/api/lms/interventions';
const LMS_INTERVENTIONS_RESET_ENDPOINT = '/api/lms/interventions/reset';
const LMS_INTERVENTIONS_COMPLETE_PATTERN = '/api/lms/interventions/%d/complete';
const LMS_PREFERENCES_ENDPOINT = '/api/lms/preferences';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware();

    $this->organization = Organization::factory()->create();
    $this->otherOrganization = Organization::factory()->create();

    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'current_organization_id' => $this->organization->id,
    ]);

    Sanctum::actingAs($this->user, ['*']);
});

it('stores and lists started lms interventions for current organization', function () {
    $course = LmsCourse::query()->create([
        'title' => 'Curso Riesgo',
        'organization_id' => $this->organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $this->organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 20,
        'status' => 'in_progress',
    ]);

    $store = $this->postJson(LMS_INTERVENTIONS_ENDPOINT, [
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'source' => 'lms_risk',
    ]);

    $store->assertCreated()
        ->assertJsonPath('data.lms_enrollment_id', $enrollment->id)
        ->assertJsonPath('data.status', 'started');

    $this->assertDatabaseHas('lms_interventions', [
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'status' => 'started',
    ]);

    $index = $this->getJson(LMS_INTERVENTIONS_ENDPOINT);

    $index->assertOk()
        ->assertJsonPath('data.started_enrollment_ids.0', $enrollment->id)
        ->assertJsonPath("data.statuses_by_enrollment.{$enrollment->id}", 'started');
});

it('blocks storing interventions for enrollments outside current organization', function () {
    $otherCourse = LmsCourse::query()->create([
        'title' => 'Curso otra org',
        'organization_id' => $this->otherOrganization->id,
    ]);

    $otherUser = User::factory()->create(['organization_id' => $this->otherOrganization->id]);
    $otherEnrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $otherCourse->id,
        'user_id' => $otherUser->id,
        'progress_percentage' => 30,
        'status' => 'in_progress',
    ]);

    $response = $this->postJson(LMS_INTERVENTIONS_ENDPOINT, [
        'lms_enrollment_id' => $otherEnrollment->id,
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('lms_interventions', [
        'lms_enrollment_id' => $otherEnrollment->id,
    ]);
});

it('resets started interventions to cleared for current organization', function () {
    $course = LmsCourse::query()->create([
        'title' => 'Curso reset',
        'organization_id' => $this->organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $this->organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 25,
        'status' => 'in_progress',
    ]);

    LmsIntervention::query()->create([
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'status' => 'started',
        'source' => 'test',
        'started_at' => now(),
    ]);

    $response = $this->postJson(LMS_INTERVENTIONS_RESET_ENDPOINT);

    $response->assertOk()
        ->assertJsonPath('data.cleared', 1);

    $this->assertDatabaseHas('lms_interventions', [
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'status' => 'cleared',
    ]);

    $index = $this->getJson(LMS_INTERVENTIONS_ENDPOINT);
    $index->assertOk()->assertJsonPath('data.started_enrollment_ids', []);
});

it('resets completed interventions to cleared for current organization', function () {
    $course = LmsCourse::query()->create([
        'title' => 'Curso reset completed',
        'organization_id' => $this->organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $this->organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 90,
        'status' => 'in_progress',
    ]);

    LmsIntervention::query()->create([
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'status' => 'completed',
        'source' => 'test',
        'started_at' => now(),
    ]);

    $response = $this->postJson(LMS_INTERVENTIONS_RESET_ENDPOINT);

    $response->assertOk()->assertJsonPath('data.cleared', 1);

    $this->assertDatabaseHas('lms_interventions', [
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'status' => 'cleared',
    ]);
});

it('completes a started intervention for the current organization', function () {
    $course = LmsCourse::query()->create([
        'title' => 'Curso completion',
        'organization_id' => $this->organization->id,
    ]);

    $enrollmentUser = User::factory()->create(['organization_id' => $this->organization->id]);
    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'progress_percentage' => 55,
        'status' => 'in_progress',
    ]);

    LmsIntervention::query()->create([
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $enrollmentUser->id,
        'status' => 'started',
        'source' => 'test',
        'started_at' => now(),
    ]);

    $response = $this->postJson(sprintf(LMS_INTERVENTIONS_COMPLETE_PATTERN, $enrollment->id));

    $response->assertOk()
        ->assertJsonPath('data.lms_enrollment_id', $enrollment->id)
        ->assertJsonPath('data.status', 'completed');

    $this->assertDatabaseHas('lms_interventions', [
        'organization_id' => $this->organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'status' => 'completed',
    ]);
});

it('returns default lms preferences when not configured', function () {
    $response = $this->getJson(LMS_PREFERENCES_ENDPOINT);

    $response->assertOk()
        ->assertJsonPath('data.show_completed_interventions', false);
});

it('updates lms preferences for authenticated user only', function () {
    $otherUser = User::factory()->create([
        'organization_id' => $this->organization->id,
        'current_organization_id' => $this->organization->id,
        'ui_preferences' => [
            'lms' => [
                'show_completed_interventions' => false,
            ],
        ],
    ]);

    $update = $this->patchJson(LMS_PREFERENCES_ENDPOINT, [
        'show_completed_interventions' => true,
    ]);

    $update->assertOk()
        ->assertJsonPath('data.show_completed_interventions', true);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
    ]);

    $this->assertTrue((bool) data_get($this->user->fresh()->ui_preferences, 'lms.show_completed_interventions'));
    $this->assertFalse((bool) data_get($otherUser->fresh()->ui_preferences, 'lms.show_completed_interventions'));
});

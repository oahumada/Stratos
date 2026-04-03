<?php

use App\Models\LmsCourse;
use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

const LMS_COURSE_POLICY_SHOW_PATTERN = '/api/lms/courses/%d';
const LMS_COURSE_POLICY_TEST_TITLE = 'Test Course';

it('requires authentication for course policy endpoints', function (
    string $method,
    array $payload = [],
) {
    $org = Organization::factory()->create();
    $course = LmsCourse::create([
        'title' => 'Auth Required Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $this->json($method, sprintf(LMS_COURSE_POLICY_SHOW_PATTERN, $course->id), $payload)
        ->assertUnauthorized();
})->with([
    'GET policy' => ['GET'],
    'PATCH policy' => ['PATCH', ['cert_min_resource_completion_ratio' => 0.5]],
]);

it('allows admin to view course policy', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => LMS_COURSE_POLICY_TEST_TITLE,
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/lms/courses/{$course->id}");

    $response->assertSuccessful();
    $response->assertJsonStructure(['id', 'title', 'cert_min_resource_completion_ratio', 'cert_require_assessment_score', 'cert_min_assessment_score']);
});

it('forbids non-admin from viewing course policy', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => LMS_COURSE_POLICY_TEST_TITLE,
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/lms/courses/{$course->id}");

    $response->assertForbidden();
});

it('allows admin to update course policy with valid data', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => LMS_COURSE_POLICY_TEST_TITLE,
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $payload = [
        'cert_min_resource_completion_ratio' => 0.8,
        'cert_require_assessment_score' => true,
        'cert_min_assessment_score' => 70,
    ];

    $response = $this->patchJson("/api/lms/courses/{$course->id}", $payload);

    $response->assertSuccessful();
    $data = $response->json('course');
    expect($data['cert_min_resource_completion_ratio'])->toBe(0.8);
    expect($data['cert_require_assessment_score'])->toBeTrue();
    expect($data['cert_min_assessment_score'])->toBe(70);
});

it('validates update payload and returns 422 for invalid values', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => LMS_COURSE_POLICY_TEST_TITLE,
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $payload = [
        'cert_min_resource_completion_ratio' => 1.5, // invalid > 1
    ];

    $response = $this->patchJson("/api/lms/courses/{$course->id}", $payload);

    $response->assertStatus(422);
});

it('forbids updating course policy when user lacks manage permission', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => 'Forbidden Update Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $this->patchJson(sprintf(LMS_COURSE_POLICY_SHOW_PATTERN, $course->id), [
        'cert_min_resource_completion_ratio' => 0.8,
        'cert_require_assessment_score' => true,
        'cert_min_assessment_score' => 70,
    ])->assertForbidden();
});

it('allows non-admin role with explicit permissions to view and update policy', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'qa_lms_course_policy_access');

    grantPermissionToRole('qa_lms_course_policy_access', 'lms.courses.view', 'lms', 'view');
    grantPermissionToRole('qa_lms_course_policy_access', 'lms.courses.manage', 'lms', 'manage');

    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => 'Permission Based Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $this->getJson(sprintf(LMS_COURSE_POLICY_SHOW_PATTERN, $course->id))
        ->assertOk();

    $this->patchJson(sprintf(LMS_COURSE_POLICY_SHOW_PATTERN, $course->id), [
        'cert_min_resource_completion_ratio' => 0.75,
        'cert_require_assessment_score' => true,
        'cert_min_assessment_score' => 85,
    ])->assertOk()
        ->assertJsonPath('course.cert_min_resource_completion_ratio', 0.75)
        ->assertJsonPath('course.cert_require_assessment_score', true)
        ->assertJsonPath('course.cert_min_assessment_score', 85);
});

it('returns 404 for course policy endpoints when the course belongs to another organization', function () {
    $org = Organization::factory()->create();
    $otherOrg = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($org, 'qa_lms_course_policy_tenant_scope');
    grantPermissionToRole('qa_lms_course_policy_tenant_scope', 'lms.courses.view', 'lms', 'view');
    grantPermissionToRole('qa_lms_course_policy_tenant_scope', 'lms.courses.manage', 'lms', 'manage');

    Sanctum::actingAs($user, ['*']);

    $otherCourse = LmsCourse::create([
        'title' => 'Other Org Course',
        'organization_id' => $otherOrg->id,
        'is_active' => true,
    ]);

    $this->getJson(sprintf(LMS_COURSE_POLICY_SHOW_PATTERN, $otherCourse->id))
        ->assertNotFound();

    $this->patchJson(sprintf(LMS_COURSE_POLICY_SHOW_PATTERN, $otherCourse->id), [
        'cert_min_resource_completion_ratio' => 0.65,
        'cert_require_assessment_score' => true,
        'cert_min_assessment_score' => 75,
    ])->assertNotFound();
});

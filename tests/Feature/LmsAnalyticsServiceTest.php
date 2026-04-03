<?php

use App\Http\Middleware\CheckPermission;
use App\Models\LmsCertificate;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\User;
use App\Services\Talent\Lms\LmsAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const LMS_ANALYTICS_OVERVIEW_ENDPOINT = '/api/lms/analytics/overview';

it('requires authentication to access lms analytics overview', function () {
    $this->getJson(LMS_ANALYTICS_OVERVIEW_ENDPOINT)
        ->assertUnauthorized();
});

it('forbids lms analytics overview when user lacks lms view permission', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_analytics_no_access');

    Sanctum::actingAs($user, ['*']);

    $this->getJson(LMS_ANALYTICS_OVERVIEW_ENDPOINT)
        ->assertForbidden();
});

it('returns 422 when organization cannot be resolved for analytics overview', function () {
    $this->withoutMiddleware(CheckPermission::class);

    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_analytics_org_missing');

    $user->update([
        'organization_id' => null,
        'current_organization_id' => null,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(LMS_ANALYTICS_OVERVIEW_ENDPOINT)
        ->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para analytics LMS.');
});

it('allows lms analytics overview when lms view permission is granted', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_analytics_access');

    grantPermissionToRole('qa_lms_analytics_access', 'lms.courses.view', 'lms', 'view');

    Sanctum::actingAs($user, ['*']);

    $this->getJson(LMS_ANALYTICS_OVERVIEW_ENDPOINT)
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'summary',
                'per_course',
                'at_risk_learners',
            ],
        ]);
});

it('calculates lms certification kpis including per-course rate', function () {
    $organization = Organization::factory()->create();
    $organizationId = $organization->id;
    $user = User::factory()->create();

    $courseA = LmsCourse::query()->create([
        'title' => 'Curso A',
        'organization_id' => $organizationId,
    ]);

    $courseB = LmsCourse::query()->create([
        'title' => 'Curso B',
        'organization_id' => $organizationId,
    ]);

    $enrollmentA1 = LmsEnrollment::query()->create([
        'lms_course_id' => $courseA->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'status' => 'completed',
    ]);

    LmsEnrollment::query()->create([
        'lms_course_id' => $courseA->id,
        'user_id' => $user->id,
        'progress_percentage' => 20,
        'resources_completed' => 1,
        'resources_total' => 10,
        'assessment_score' => 55,
        'status' => 'in_progress',
    ]);

    LmsEnrollment::query()->create([
        'lms_course_id' => $courseB->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'status' => 'completed',
    ]);

    LmsCertificate::query()->create([
        'organization_id' => $organizationId,
        'lms_enrollment_id' => $enrollmentA1->id,
        'certificate_number' => 'CERT-A-1',
        'certificate_url' => 'https://example.com/cert-a-1.pdf',
        'certificate_hash' => 'hash-a-1',
        'issued_at' => now(),
        'is_revoked' => false,
    ]);

    $service = app(LmsAnalyticsService::class);
    $result = $service->getKpisForOrganization($organizationId);

    expect($result['summary']['total_courses'])->toBe(2)
        ->and($result['summary']['total_enrollments'])->toBe(3)
        ->and($result['summary']['completed_enrollments'])->toBe(2)
        ->and((float) $result['summary']['completion_rate_pct'])->toBe(66.67)
        ->and($result['summary']['issued_certificates'])->toBe(1)
        ->and((float) $result['summary']['certification_rate_pct'])->toBe(33.33)
        ->and($result['summary']['at_risk_enrollments'])->toBe(1)
        ->and((float) $result['summary']['at_risk_rate_pct'])->toBe(33.33);

    $courseAStats = collect($result['per_course'])->firstWhere('course_id', $courseA->id);

    expect($courseAStats)->not->toBeNull()
        ->and($courseAStats['total_enrollments'])->toBe(2)
        ->and($courseAStats['issued_certificates'])->toBe(1)
        ->and((float) $courseAStats['certification_rate_pct'])->toBe(50.0);

    expect($result['at_risk_learners'])->toHaveCount(1)
        ->and($result['at_risk_learners'][0]['course_id'])->toBe($courseA->id)
        ->and((float) $result['at_risk_learners'][0]['progress_percentage'])->toBe(20.0);
});

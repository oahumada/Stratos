<?php

use App\Events\CertificateIssued;
use App\Models\DevelopmentAction;
use App\Models\DevelopmentPath;
use App\Models\LmsCertificate;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\User;
use App\Services\Talent\Lms\LmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('auto-issues a certificate when lms sync marks an action as completed', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');
    Event::fake();

    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $person = People::factory()->create(['organization_id' => $organization->id, 'user_id' => $user->id]);
    $role = Roles::factory()->create(['organization_id' => $organization->id]);
    $path = DevelopmentPath::create([
        'action_title' => 'Ruta LMS',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);
    $course = LmsCourse::create([
        'title' => 'Leadership 101',
        'organization_id' => $organization->id,
        'xp_points' => 25,
        'is_active' => true,
    ]);
    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'resources_completed' => 10,
        'resources_total' => 10,
        'assessment_score' => 90,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $action = DevelopmentAction::create([
        'development_path_id' => $path->id,
        'title' => 'Completar curso',
        'type' => 'training',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'in_progress',
        'lms_course_id' => $course->id,
        'lms_enrollment_id' => (string) $enrollment->id,
        'lms_provider' => 'internal',
    ]);

    $updated = app(LmsService::class)->syncProgress($action);

    expect($updated)->toBeTrue();
    expect($action->fresh()->status)->toBe('completed');

    $certificate = LmsCertificate::query()->where('lms_enrollment_id', (string) $enrollment->id)->first();

    expect($certificate)->not->toBeNull();
    expect($certificate?->person_id)->toBe($person->id);

    Event::assertDispatched(CertificateIssued::class);
});

it('syncs pending lms actions from the console command', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');
    Event::fake();

    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $person = People::factory()->create(['organization_id' => $organization->id, 'user_id' => $user->id]);
    $role = Roles::factory()->create(['organization_id' => $organization->id]);
    $path = DevelopmentPath::create([
        'action_title' => 'Ruta batch',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);
    $course = LmsCourse::create([
        'title' => 'Data Basics',
        'organization_id' => $organization->id,
        'xp_points' => 10,
        'is_active' => true,
    ]);
    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'resources_completed' => 8,
        'resources_total' => 10,
        'assessment_score' => 85,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $action = DevelopmentAction::create([
        'development_path_id' => $path->id,
        'title' => 'Sincronizar progreso',
        'type' => 'training',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'in_progress',
        'lms_course_id' => $course->id,
        'lms_enrollment_id' => (string) $enrollment->id,
        'lms_provider' => 'internal',
    ]);

    $this->artisan('lms:sync-progress', [
        '--organization_id' => $organization->id,
    ])->assertExitCode(0);

    expect($action->fresh()->status)->toBe('completed');
    expect(LmsCertificate::query()->where('lms_enrollment_id', (string) $enrollment->id)->exists())->toBeTrue();
});

it('does not auto-issue certificate when completion criteria are not met', function () {
    config(['filesystems.default' => 'local']);
    config([
        'stratos.lms.certificate_issuance.min_resource_completion_ratio' => 0.70,
        'stratos.lms.certificate_issuance.require_assessment_score' => true,
        'stratos.lms.certificate_issuance.min_assessment_score' => 80,
    ]);

    Storage::fake('local');
    Event::fake();

    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $person = People::factory()->create(['organization_id' => $organization->id, 'user_id' => $user->id]);
    $role = Roles::factory()->create(['organization_id' => $organization->id]);
    $path = DevelopmentPath::create([
        'action_title' => 'Ruta no elegible',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);
    $course = LmsCourse::create([
        'title' => 'Course with gate',
        'organization_id' => $organization->id,
        'xp_points' => 20,
        'is_active' => true,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'resources_completed' => 7,
        'resources_total' => 10,
        'assessment_score' => 65,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $action = DevelopmentAction::create([
        'development_path_id' => $path->id,
        'title' => 'Completar curso con score bajo',
        'type' => 'training',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'in_progress',
        'lms_course_id' => $course->id,
        'lms_enrollment_id' => (string) $enrollment->id,
        'lms_provider' => 'internal',
    ]);

    $updated = app(LmsService::class)->syncProgress($action);

    expect($updated)->toBeTrue();
    expect($action->fresh()->status)->toBe('completed');
    expect(LmsCertificate::query()->where('lms_enrollment_id', (string) $enrollment->id)->exists())->toBeFalse();
    Event::assertNotDispatched(CertificateIssued::class);
});

it('applies per-course policy overrides instead of global defaults', function () {
    config(['filesystems.default' => 'local']);
    config([
        'stratos.lms.certificate_issuance.min_resource_completion_ratio' => 0.90,
        'stratos.lms.certificate_issuance.require_assessment_score' => true,
        'stratos.lms.certificate_issuance.min_assessment_score' => 95,
    ]);

    Storage::fake('local');
    Event::fake();

    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $person = People::factory()->create(['organization_id' => $organization->id, 'user_id' => $user->id]);
    $role = Roles::factory()->create(['organization_id' => $organization->id]);
    $path = DevelopmentPath::create([
        'action_title' => 'Ruta con override',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);

    $course = LmsCourse::create([
        'title' => 'Course override policy',
        'organization_id' => $organization->id,
        'is_active' => true,
        'cert_min_resource_completion_ratio' => 0.70,
        'cert_require_assessment_score' => true,
        'cert_min_assessment_score' => 80,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'resources_completed' => 7,
        'resources_total' => 10,
        'assessment_score' => 80,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $action = DevelopmentAction::create([
        'development_path_id' => $path->id,
        'title' => 'Completar curso con override',
        'type' => 'training',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'in_progress',
        'lms_course_id' => $course->id,
        'lms_enrollment_id' => (string) $enrollment->id,
        'lms_provider' => 'internal',
    ]);

    $updated = app(LmsService::class)->syncProgress($action);

    expect($updated)->toBeTrue();
    expect($action->fresh()->status)->toBe('completed');
    expect(LmsCertificate::query()->where('lms_enrollment_id', (string) $enrollment->id)->exists())->toBeTrue();
    Event::assertDispatched(CertificateIssued::class);
});

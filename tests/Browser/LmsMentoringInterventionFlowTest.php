<?php

use App\Models\DevelopmentAction;
use App\Models\DevelopmentPath;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsIntervention;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('completes lms intervention from mentoring flow with lms risk context', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->admin()->create([
        'organization_id' => $organization->id,
        'current_organization_id' => $organization->id,
        'email_verified_at' => now(),
    ]);

    $person = People::factory()->create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $role = Roles::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $path = DevelopmentPath::query()->create([
        'action_title' => 'Ruta mentoring LMS',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);

    $course = LmsCourse::query()->create([
        'title' => 'Curso Riesgo Mentoring',
        'organization_id' => $organization->id,
        'is_active' => true,
    ]);

    $enrollment = LmsEnrollment::query()->create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 25,
        'resources_completed' => 2,
        'resources_total' => 10,
        'assessment_score' => 55,
        'status' => 'in_progress',
    ]);

    $action = DevelopmentAction::query()->create([
        'development_path_id' => $path->id,
        'title' => 'Intervención mentoring',
        'type' => 'mentoring',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'in_progress',
        'lms_course_id' => $course->id,
        'lms_enrollment_id' => (string) $enrollment->id,
        'lms_provider' => 'internal',
    ]);

    LmsIntervention::query()->create([
        'organization_id' => $organization->id,
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'started',
        'source' => 'lms_risk',
        'started_at' => now(),
    ]);

    $url = sprintf(
        '/mentoring?source=lms_risk&people_id=%d&course_id=%d&enrollment_id=%d&development_action_id=%d&progress=25&assessment=55&resource_completion=20',
        $person->id,
        $course->id,
        $enrollment->id,
        $action->id,
    );

    config([
        'stratos.qa.e2e_bypass' => true,
        'stratos.qa.e2e_admin_id' => $user->id,
    ]);

    $this->withoutVite();

    visit('/__e2e_login')->assertNoJavascriptErrors();

    visit($url)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

    $this->actingAs($user)
        ->postJson("/api/lms/interventions/{$enrollment->id}/complete")
        ->assertOk();

    expect(
        LmsIntervention::query()
            ->where('organization_id', $organization->id)
            ->where('lms_enrollment_id', $enrollment->id)
            ->value('status')
    )->toBe('completed');
});

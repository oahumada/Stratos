<?php

use App\Models\DevelopmentAction;
use App\Models\DevelopmentPath;
use App\Models\LmsCourse;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\User;
use App\Notifications\CertificateIssuedNotification;
use App\Notifications\LmsCourseCompletedNotification;
use App\Services\Talent\Lms\CertificateService;
use App\Services\Talent\Lms\LmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('uses mail database and broadcast channels for lms notifications', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $person = People::factory()->create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);
    $role = Roles::factory()->create(['organization_id' => $organization->id]);

    $path = DevelopmentPath::create([
        'action_title' => 'Ruta test',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);

    $course = LmsCourse::create([
        'title' => 'Curso test',
        'organization_id' => $organization->id,
        'xp_points' => 40,
        'is_active' => true,
    ]);

    $action = DevelopmentAction::create([
        'development_path_id' => $path->id,
        'title' => 'Completar curso',
        'type' => 'training',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'completed',
        'completed_at' => now(),
        'lms_course_id' => $course->id,
        'lms_provider' => 'internal',
    ]);

    $completedNotification = new LmsCourseCompletedNotification($action, $course);

    expect($completedNotification->via($user))->toBe(['mail', 'database', 'broadcast']);

    Storage::fake('local');
    config(['filesystems.default' => 'local']);

    $certificate = app(CertificateService::class)->issue([
        'organization_id' => $organization->id,
        'person_id' => $person->id,
        'issued_at' => now(),
    ]);

    $certificateNotification = new CertificateIssuedNotification($certificate->fresh());

    expect($certificateNotification->via($user))->toBe(['mail', 'database', 'broadcast']);
});

it('sends slack webhook when lms certificate webhook is configured', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');
    config(['services.lms.slack_webhook_url' => 'https://hooks.slack.test/lms']);

    Http::fake([
        'https://hooks.slack.test/*' => Http::response(['ok' => true], 200),
    ]);

    $organization = Organization::factory()->create();

    app(CertificateService::class)->issue([
        'organization_id' => $organization->id,
        'issued_at' => now(),
    ]);

    Http::assertSentCount(1);
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'hooks.slack.test/lms')
            && str_contains((string) $request->body(), 'Certificado emitido');
    });
});

it('sends slack webhook when lms course is completed', function () {
    config(['services.lms.slack_webhook_url' => 'https://hooks.slack.test/lms']);

    Http::fake([
        'https://hooks.slack.test/*' => Http::response(['ok' => true], 200),
    ]);

    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $person = People::factory()->create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);
    $role = Roles::factory()->create(['organization_id' => $organization->id]);

    $course = LmsCourse::create([
        'title' => 'Curso Slack Test',
        'organization_id' => $organization->id,
        'xp_points' => 75,
        'is_active' => true,
    ]);

    $path = DevelopmentPath::create([
        'action_title' => 'Ruta test',
        'organization_id' => $organization->id,
        'people_id' => $person->id,
        'target_role_id' => $role->id,
        'status' => 'active',
    ]);

    $action = DevelopmentAction::create([
        'development_path_id' => $path->id,
        'title' => 'Completar curso Slack',
        'type' => 'training',
        'strategy' => 'build',
        'order' => 1,
        'status' => 'completed',
        'completed_at' => now(),
        'lms_course_id' => $course->id,
        'lms_provider' => 'mock',
        'lms_enrollment_id' => 'enrollment-slack-test',
    ]);

    $service = app(LmsService::class);
    $service->sendCourseCompletedSlackNotification($action, $course);

    Http::assertSentCount(1);
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'hooks.slack.test/lms')
            && str_contains((string) $request->body(), 'Curso completado')
            && str_contains((string) $request->body(), 'Curso Slack Test');
    });
});

<?php

use App\Models\LmsCmi5Session;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsLesson;
use App\Models\LmsLessonAuditLog;
use App\Models\LmsModule;
use App\Models\LmsScormPackage;
use App\Models\LmsScormTracking;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lms\Cmi5Service;
use App\Services\Lms\LessonAuditService;
use App\Services\Lms\ScormPlayerService;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

function createStandardsTestUser(?Organization $org = null): array
{
    $org = $org ?? Organization::factory()->create();
    $user = User::factory()->admin()->create([
        'organization_id' => $org->id,
        'current_organization_id' => $org->id,
    ]);
    Sanctum::actingAs($user, ['*']);

    return [$org, $user];
}

beforeEach(function () {
    Storage::fake('local');
});

// ─── SCORM 2004: CMI Element Handling ─────────────────────────────────

it('handles SCORM 2004 completion_status CMI element', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Package',
        'filename' => 'test2004.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $response = $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.completion_status' => 'completed',
        ],
    ]);

    $response->assertOk();
    $tracking = LmsScormTracking::first();
    expect($tracking->lesson_status)->toBe('completed');
});

it('handles SCORM 2004 success_status CMI element', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Package',
        'filename' => 'test2004.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $response = $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.success_status' => 'passed',
        ],
    ]);

    $response->assertOk();
    $tracking = LmsScormTracking::first();
    expect($tracking->success_status)->toBe('passed');
});

it('handles SCORM 2004 score.scaled CMI element', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Package',
        'filename' => 'test2004.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $response = $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.score.scaled' => '0.85',
        ],
    ]);

    $response->assertOk();
    $tracking = LmsScormTracking::first();
    expect($tracking->scaled_score)->toBe(0.85);
});

it('handles SCORM 2004 progress_measure CMI element', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Package',
        'filename' => 'test2004.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $response = $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.progress_measure' => '0.6500',
        ],
    ]);

    $response->assertOk();
    $tracking = LmsScormTracking::first();
    expect($tracking->progress_measure)->toBe(0.65);
});

// ─── SCORM 2004: ISO 8601 Duration ───────────────────────────────────

it('parses SCORM 2004 ISO 8601 session_time duration', function () {
    $seconds = LmsScormTracking::iso8601ToSeconds('PT1H30M15S');
    expect($seconds)->toBe(5415);
});

it('parses ISO 8601 duration with only minutes', function () {
    $seconds = LmsScormTracking::iso8601ToSeconds('PT45M');
    expect($seconds)->toBe(2700);
});

it('parses ISO 8601 duration with only seconds', function () {
    $seconds = LmsScormTracking::iso8601ToSeconds('PT30S');
    expect($seconds)->toBe(30);
});

it('handles SCORM 2004 session_time via CMI data', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Package',
        'filename' => 'test2004.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.session_time' => 'PT1H30M15S',
        ],
    ]);

    $tracking = LmsScormTracking::first();
    expect($tracking->total_time)->toBe('0001:30:15');
    expect($tracking->session_count)->toBe(1);
});

it('handles SCORM 2004 SetValue service method', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Test',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $service = app(ScormPlayerService::class);
    $tracking = $service->getOrCreateTracking($package->id, $user->id, $org->id);

    $result = $service->handleScorm2004SetValue($tracking->id, 'cmi.completion_status', 'completed', $org->id);
    expect($result->lesson_status)->toBe('completed');

    $result = $service->handleScorm2004SetValue($tracking->id, 'cmi.success_status', 'passed', $org->id);
    expect($result->success_status)->toBe('passed');

    $result = $service->handleScorm2004SetValue($tracking->id, 'cmi.score.scaled', '0.92', $org->id);
    expect($result->scaled_score)->toBe(0.92);
});

it('handles SCORM 2004 GetValue service method', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'SCORM 2004 Test',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => '2004',
    ]);

    $service = app(ScormPlayerService::class);
    $tracking = $service->getOrCreateTracking($package->id, $user->id, $org->id);

    $tracking->update(['lesson_status' => 'completed', 'success_status' => 'passed', 'scaled_score' => 0.95]);

    $completion = $service->handleScorm2004GetValue($tracking->id, 'cmi.completion_status', $org->id);
    expect($completion)->toBe('completed');

    $success = $service->handleScorm2004GetValue($tracking->id, 'cmi.success_status', $org->id);
    expect($success)->toBe('passed');

    $score = $service->handleScorm2004GetValue($tracking->id, 'cmi.score.scaled', $org->id);
    expect($score)->toBe('0.95');
});

// ─── cmi5: Session Management ────────────────────────────────────────

it('can create a cmi5 session via launch endpoint', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'identifier' => 'cmi5-course-1',
        'version' => 'cmi5',
    ]);

    $response = $this->postJson("/api/lms/cmi5/{$package->id}/launch", [
        'launch_mode' => 'Normal',
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonStructure([
        'data' => [
            'session' => ['id', 'registration_id', 'session_id', 'status', 'launch_mode'],
            'launch_url',
        ],
    ]);

    expect($response->json('data.session.status'))->toBe('launched');
    expect($response->json('data.session.launch_mode'))->toBe('Normal');
    expect(LmsCmi5Session::count())->toBe(1);
});

it('can fetch cmi5 launch data', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'identifier' => 'cmi5-course-1',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $session = $service->createSession($package->id, $user->id, $org->id);

    $response = $this->getJson("/api/lms/cmi5/sessions/{$session->id}/fetch");

    $response->assertOk();
    $response->assertJsonStructure([
        'auth-token',
        'endpoint',
        'actor',
        'registration',
        'activityId',
        'moveOn',
        'launchMode',
    ]);
    expect($response->json('registration'))->toBe($session->registration_id);
    expect($response->json('launchMode'))->toBe('Normal');
});

it('can handle cmi5 xAPI statements', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $session = $service->createSession($package->id, $user->id, $org->id);

    // Send initialized statement
    $response = $this->postJson("/api/lms/cmi5/sessions/{$session->id}/statement", [
        'verb' => ['id' => 'http://adlnet.gov/expapi/verbs/initialized'],
        'actor' => $session->actor_json,
        'object' => ['id' => 'activity-1'],
    ]);

    $response->assertOk();
    $session->refresh();
    expect($session->status)->toBe('initialized');

    // Send completed statement
    $response = $this->postJson("/api/lms/cmi5/sessions/{$session->id}/statement", [
        'verb' => ['id' => 'http://adlnet.gov/expapi/verbs/completed'],
        'actor' => $session->actor_json,
        'object' => ['id' => 'activity-1'],
    ]);

    $response->assertOk();
    $session->refresh();
    expect($session->status)->toBe('completed');
});

it('handles cmi5 statement with score', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $session = $service->createSession($package->id, $user->id, $org->id);

    $this->postJson("/api/lms/cmi5/sessions/{$session->id}/statement", [
        'verb' => ['id' => 'http://adlnet.gov/expapi/verbs/passed'],
        'actor' => $session->actor_json,
        'object' => ['id' => 'activity-1'],
        'result' => [
            'score' => ['scaled' => 0.92],
            'duration' => 'PT1H15M',
        ],
    ]);

    $session->refresh();
    expect($session->status)->toBe('passed');
    expect($session->score_scaled)->toBe(0.92);
    expect($session->duration)->toBe('PT1H15M');
});

it('evaluates cmi5 moveOn criteria', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $session = $service->createSession($package->id, $user->id, $org->id);
    $session->update(['move_on' => 'Passed']);

    // Before passing — not satisfied
    $satisfied = $service->evaluateMoveOn($session->id, $org->id);
    expect($satisfied)->toBeFalse();

    // After passing — satisfied
    $session->update(['status' => 'passed']);
    $satisfied = $service->evaluateMoveOn($session->id, $org->id);
    expect($satisfied)->toBeTrue();

    $session->refresh();
    expect($session->satisfied)->toBeTrue();
});

it('can get auth token for cmi5 session', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $session = $service->createSession($package->id, $user->id, $org->id);

    $response = $this->getJson("/api/lms/cmi5/sessions/{$session->id}/auth-token");

    $response->assertOk();
    $response->assertJsonStructure(['auth-token']);
    expect($response->json('auth-token'))->not->toBeEmpty();
});

it('can list cmi5 sessions for a package', function () {
    [$org, $user] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $service->createSession($package->id, $user->id, $org->id);
    $service->createSession($package->id, $user->id, $org->id, 'Browse');

    $response = $this->getJson("/api/lms/cmi5/{$package->id}/sessions");

    $response->assertOk();
    expect(count($response->json('data')))->toBe(2);
});

// ─── Lesson Audit Trail ──────────────────────────────────────────────

it('can log a lesson audit event', function () {
    [$org, $user] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'enrolled',
    ]);

    $response = $this->postJson('/api/lms/audit/log', [
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'module_id' => $module->id,
        'action' => 'started',
        'metadata' => ['source' => 'web'],
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    expect(LmsLessonAuditLog::count())->toBe(1);

    $log = LmsLessonAuditLog::first();
    expect($log->action)->toBe('started');
    expect($log->organization_id)->toBe($org->id);
    expect($log->metadata)->toBe(['source' => 'web']);
});

it('can get lesson history for an enrollment', function () {
    [$org, $user] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'enrolled',
    ]);

    $service = app(LessonAuditService::class);
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'started');
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'viewed');
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'completed');

    $response = $this->getJson("/api/lms/audit/enrollments/{$enrollment->id}/lessons/{$lesson->id}");

    $response->assertOk();
    expect(count($response->json('data')))->toBe(3);
});

it('can get user timeline for an enrollment', function () {
    [$org, $user] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson1 = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $lesson2 = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 2',
        'content_type' => 'text',
        'order' => 2,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'enrolled',
    ]);

    $service = app(LessonAuditService::class);
    $service->log($enrollment->id, $lesson1->id, $user->id, $org->id, 'started');
    $service->log($enrollment->id, $lesson1->id, $user->id, $org->id, 'completed');
    $service->log($enrollment->id, $lesson2->id, $user->id, $org->id, 'started');

    $response = $this->getJson("/api/lms/audit/enrollments/{$enrollment->id}/timeline");

    $response->assertOk();
    expect(count($response->json('data')))->toBe(3);
});

it('calculates time spent per lesson', function () {
    [$org, $user] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'enrolled',
    ]);

    // Create started/completed pair with known timestamps
    $started = LmsLessonAuditLog::create([
        'organization_id' => $org->id,
        'user_id' => $user->id,
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'action' => 'started',
    ]);
    // Force the created_at to 10 minutes ago via DB update
    \Illuminate\Support\Facades\DB::table('lms_lesson_audit_logs')
        ->where('id', $started->id)
        ->update(['created_at' => now()->subMinutes(10)]);

    LmsLessonAuditLog::create([
        'organization_id' => $org->id,
        'user_id' => $user->id,
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'action' => 'completed',
    ]);

    $service = app(LessonAuditService::class);
    $timePerLesson = $service->getTimeSpentPerLesson($enrollment->id);

    expect($timePerLesson)->toHaveKey($lesson->id);
    expect($timePerLesson[$lesson->id])->toBeGreaterThanOrEqual(590);
    expect($timePerLesson[$lesson->id])->toBeLessThanOrEqual(610);
});

it('can export audit trail as CSV', function () {
    [$org, $user] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'enrolled',
    ]);

    $service = app(LessonAuditService::class);
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'started');
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'completed');

    $response = $this->get("/api/lms/audit/enrollments/{$enrollment->id}/export");

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('Date,User,Lesson,Action');
    expect($response->streamedContent())->toContain('started');
    expect($response->streamedContent())->toContain('completed');
});

it('can get course audit summary', function () {
    [$org, $user] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'enrolled',
    ]);

    $service = app(LessonAuditService::class);
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'started');
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'viewed');
    $service->log($enrollment->id, $lesson->id, $user->id, $org->id, 'completed');

    $response = $this->getJson("/api/lms/audit/courses/{$course->id}/summary");

    $response->assertOk();
    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['views'])->toBe(2); // started + viewed
    expect($data[0]['completions'])->toBe(1);
    expect($data[0]['unique_users'])->toBe(1);
});

// ─── Multi-tenant Isolation ──────────────────────────────────────────

it('cmi5 sessions are tenant-isolated', function () {
    [$org1, $user1] = createStandardsTestUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org1->id,
        'title' => 'cmi5 Package',
        'filename' => 'cmi5.zip',
        'storage_path' => "scorm/{$org1->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'version' => 'cmi5',
    ]);

    $service = app(Cmi5Service::class);
    $session = $service->createSession($package->id, $user1->id, $org1->id);

    // Create user in different org
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->admin()->create([
        'organization_id' => $org2->id,
        'current_organization_id' => $org2->id,
    ]);
    Sanctum::actingAs($user2, ['*']);

    // Should not see sessions from org1
    $response = $this->getJson("/api/lms/cmi5/{$package->id}/sessions");
    $response->assertOk();
    expect(count($response->json('data')))->toBe(0);
});

it('audit logs are scoped by organization', function () {
    [$org1, $user1] = createStandardsTestUser();

    $course = LmsCourse::create([
        'organization_id' => $org1->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user1->id,
        'status' => 'enrolled',
    ]);

    $service = app(LessonAuditService::class);
    $log = $service->log($enrollment->id, $lesson->id, $user1->id, $org1->id, 'started');

    expect($log->organization_id)->toBe($org1->id);

    // Verify scoped query
    $org2 = Organization::factory()->create();
    $scopedLogs = LmsLessonAuditLog::forOrganization($org2->id)->get();
    expect($scopedLogs)->toHaveCount(0);
});

it('LmsLessonAuditLog model has no updated_at', function () {
    expect(LmsLessonAuditLog::UPDATED_AT)->toBeNull();
});

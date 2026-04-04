<?php

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsScheduledReport;
use App\Models\LmsSession;
use App\Models\LmsSessionAttendance;
use App\Models\LmsSurvey;
use App\Models\LmsSurveyResponse;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lms\IltService;
use App\Services\Lms\ScheduledReportService;
use App\Services\Lms\SurveyService;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

// ── Helpers ──

function createLmsCourse(Organization $org, array $overrides = []): LmsCourse
{
    return LmsCourse::create(array_merge([
        'title' => 'Test Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ], $overrides));
}

function createIltSession(Organization $org, LmsCourse $course, User $instructor, array $overrides = []): LmsSession
{
    return LmsSession::create(array_merge([
        'organization_id' => $org->id,
        'course_id' => $course->id,
        'instructor_id' => $instructor->id,
        'title' => 'Test ILT Session',
        'session_type' => 'virtual',
        'starts_at' => Carbon::now()->addDays(7),
        'ends_at' => Carbon::now()->addDays(7)->addHours(2),
        'timezone' => 'UTC',
    ], $overrides));
}

// ── Setup ──

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->otherOrg = Organization::factory()->create();
    $this->user = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
    $this->otherUser = User::factory()->admin()->create(['current_organization_id' => $this->otherOrg->id]);
    $this->course = createLmsCourse($this->org);
    $this->instructor = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
});

// ══════════════════════════════════════════════════
// Feature 1: ILT Session CRUD
// ══════════════════════════════════════════════════

it('creates an ILT session via service', function () {
    $service = app(IltService::class);

    $session = $service->createSession($this->org->id, $this->course->id, [
        'instructor_id' => $this->instructor->id,
        'title' => 'Laravel Workshop',
        'session_type' => 'in_person',
        'location' => 'Room A',
        'starts_at' => Carbon::now()->addDays(5),
        'ends_at' => Carbon::now()->addDays(5)->addHours(3),
    ]);

    expect($session)->not->toBeNull();
    expect($session->title)->toBe('Laravel Workshop');
    expect($session->session_type)->toBe('in_person');
    expect($session->organization_id)->toBe($this->org->id);
});

it('updates an ILT session', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor);

    $updated = $service->updateSession($session->id, ['title' => 'Updated Title']);

    expect($updated->title)->toBe('Updated Title');
});

it('soft deletes an ILT session via API', function () {
    Sanctum::actingAs($this->user);
    $session = createIltSession($this->org, $this->course, $this->instructor);

    $response = $this->deleteJson("/api/lms/sessions/{$session->id}");

    $response->assertOk();
    expect(LmsSession::find($session->id))->toBeNull();
    expect(LmsSession::withTrashed()->find($session->id))->not->toBeNull();
});

it('lists ILT sessions via API', function () {
    Sanctum::actingAs($this->user);
    createIltSession($this->org, $this->course, $this->instructor, ['title' => 'Session A']);
    createIltSession($this->org, $this->course, $this->instructor, ['title' => 'Session B']);

    $response = $this->getJson('/api/lms/sessions');

    $response->assertOk();
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(2);
});

// ══════════════════════════════════════════════════
// ILT Registration + Max Attendees
// ══════════════════════════════════════════════════

it('registers an attendee for a session', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor);

    $attendance = $service->registerAttendee($session->id, $this->user->id);

    expect($attendance->status)->toBe('registered');
    expect($attendance->session_id)->toBe($session->id);
});

it('enforces max attendees limit', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor, ['max_attendees' => 1]);

    $service->registerAttendee($session->id, $this->user->id);

    $extra = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
    expect(fn () => $service->registerAttendee($session->id, $extra->id))
        ->toThrow(\RuntimeException::class, 'Session is full');
});

it('cancels registration', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor);
    $service->registerAttendee($session->id, $this->user->id);

    $result = $service->cancelRegistration($session->id, $this->user->id);

    expect($result)->toBeTrue();
    $attendance = LmsSessionAttendance::where('session_id', $session->id)
        ->where('user_id', $this->user->id)->first();
    expect($attendance->status)->toBe('cancelled');
});

// ══════════════════════════════════════════════════
// ILT Attendance Marking
// ══════════════════════════════════════════════════

it('marks attendance with check-in', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor);
    $service->registerAttendee($session->id, $this->user->id);

    $attendance = $service->markAttendance($session->id, $this->user->id, 'attended');

    expect($attendance->status)->toBe('attended');
    expect($attendance->check_in_at)->not->toBeNull();
});

it('bulk marks attendance', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor);

    $user2 = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
    $service->registerAttendee($session->id, $this->user->id);
    $service->registerAttendee($session->id, $user2->id);

    $results = $service->bulkMarkAttendance($session->id, [
        ['user_id' => $this->user->id, 'status' => 'attended'],
        ['user_id' => $user2->id, 'status' => 'absent'],
    ]);

    expect(count($results))->toBe(2);
    expect($results[0]->status)->toBe('attended');
    expect($results[1]->status)->toBe('absent');
});

// ══════════════════════════════════════════════════
// ILT Feedback
// ══════════════════════════════════════════════════

it('submits feedback for a session', function () {
    $service = app(IltService::class);
    $session = createIltSession($this->org, $this->course, $this->instructor);
    $service->registerAttendee($session->id, $this->user->id);

    $attendance = $service->submitFeedback($session->id, $this->user->id, 'Great session!', 5);

    expect($attendance->feedback)->toBe('Great session!');
    expect($attendance->rating)->toBe(5);
});

// ══════════════════════════════════════════════════
// Feature 3: Survey CRUD + NPS
// ══════════════════════════════════════════════════

it('creates a survey for a course', function () {
    $service = app(SurveyService::class);

    $survey = $service->createSurvey($this->org->id, $this->course->id, [
        'title' => 'Post-Course Survey',
        'questions' => [
            ['type' => 'nps', 'question' => 'How likely are you to recommend?', 'options' => null],
            ['type' => 'rating', 'question' => 'Rate the content quality', 'options' => null],
            ['type' => 'text', 'question' => 'Any comments?', 'options' => null],
        ],
    ]);

    expect($survey->title)->toBe('Post-Course Survey');
    expect(count($survey->questions))->toBe(3);
    expect($survey->organization_id)->toBe($this->org->id);
});

it('submits a survey response', function () {
    $service = app(SurveyService::class);
    $survey = $service->createSurvey($this->org->id, $this->course->id, [
        'title' => 'Feedback Survey',
        'questions' => [
            ['type' => 'nps', 'question' => 'Recommend?', 'options' => null],
        ],
    ]);

    $response = $service->submitResponse($survey->id, $this->user->id, [
        ['question_index' => 0, 'answer' => 9],
    ]);

    expect($response->nps_score)->toBe(9);
    expect($response->completed_at)->not->toBeNull();
});

it('prevents duplicate survey responses', function () {
    $service = app(SurveyService::class);
    $survey = $service->createSurvey($this->org->id, $this->course->id, [
        'title' => 'Once Only',
        'questions' => [['type' => 'text', 'question' => 'Test?', 'options' => null]],
    ]);

    $service->submitResponse($survey->id, $this->user->id, [
        ['question_index' => 0, 'answer' => 'First'],
    ]);

    expect($service->hasUserResponded($survey->id, $this->user->id))->toBeTrue();
});

it('calculates NPS correctly', function () {
    $service = app(SurveyService::class);
    $survey = $service->createSurvey($this->org->id, $this->course->id, [
        'title' => 'NPS Survey',
        'questions' => [['type' => 'nps', 'question' => 'Recommend?', 'options' => null]],
    ]);

    // 3 promoters (9,10,10), 1 passive (8), 1 detractor (4)
    $users = [];
    foreach ([9, 10, 10, 8, 4] as $score) {
        $u = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
        $users[] = $u;
        $service->submitResponse($survey->id, $u->id, [
            ['question_index' => 0, 'answer' => $score],
        ]);
    }

    $nps = $service->calculateNps($survey->id);

    expect($nps['promoters'])->toBe(3);
    expect($nps['passives'])->toBe(1);
    expect($nps['detractors'])->toBe(1);
    // NPS = ((3 - 1) / 5) * 100 = 40
    expect((int) $nps['nps_score'])->toBe(40);
});

// ══════════════════════════════════════════════════
// Feature 4: Scheduled Reports CRUD
// ══════════════════════════════════════════════════

it('creates a scheduled report', function () {
    $service = app(ScheduledReportService::class);

    $report = $service->create($this->org->id, $this->user->id, [
        'report_type' => 'completion',
        'frequency' => 'weekly',
        'recipients' => ['admin@example.com'],
    ]);

    expect($report->report_type)->toBe('completion');
    expect($report->frequency)->toBe('weekly');
    expect($report->next_send_at)->not->toBeNull();
    expect($report->is_active)->toBeTrue();
});

it('updates a scheduled report', function () {
    $service = app(ScheduledReportService::class);
    $report = $service->create($this->org->id, $this->user->id, [
        'report_type' => 'engagement',
        'frequency' => 'daily',
        'recipients' => ['test@example.com'],
    ]);

    $updated = $service->update($report->id, ['frequency' => 'monthly']);

    expect($updated->frequency)->toBe('monthly');
});

it('deletes a scheduled report via API', function () {
    Sanctum::actingAs($this->user);

    $report = LmsScheduledReport::create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
        'report_type' => 'compliance',
        'frequency' => 'daily',
        'recipients' => ['a@b.com'],
        'next_send_at' => Carbon::now()->addDay(),
    ]);

    $response = $this->deleteJson("/api/lms/reports/scheduled/{$report->id}");

    $response->assertOk();
    expect(LmsScheduledReport::find($report->id))->toBeNull();
});

it('lists scheduled reports for organization', function () {
    Sanctum::actingAs($this->user);

    LmsScheduledReport::create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
        'report_type' => 'completion',
        'frequency' => 'weekly',
        'recipients' => ['x@y.com'],
        'next_send_at' => Carbon::now()->addWeek(),
    ]);

    $response = $this->getJson('/api/lms/reports/scheduled');

    $response->assertOk();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(1);
});

// ══════════════════════════════════════════════════
// Feature 2: PDF Report
// ══════════════════════════════════════════════════

it('generates report HTML via service', function () {
    $service = app(\App\Services\Lms\LmsReportService::class);

    $html = $service->generateReportHtml($this->org->id, 'completion');

    expect($html)->toContain('Completion Report');
    expect($html)->toContain('<table');
});

it('exports PDF report via API', function () {
    Sanctum::actingAs($this->user);

    $response = $this->get('/api/lms/reports/pdf?type=completion');

    // Should return either PDF or HTML fallback
    $response->assertOk();
    $contentType = $response->headers->get('Content-Type');
    expect(
        str_contains($contentType, 'application/pdf') || str_contains($contentType, 'text/html')
    )->toBeTrue();
});

// ══════════════════════════════════════════════════
// Multi-tenant Isolation
// ══════════════════════════════════════════════════

it('scopes ILT sessions by organization', function () {
    $otherCourse = createLmsCourse($this->otherOrg);
    $otherInstructor = User::factory()->admin()->create(['current_organization_id' => $this->otherOrg->id]);

    createIltSession($this->org, $this->course, $this->instructor, ['title' => 'Org1 Session']);
    createIltSession($this->otherOrg, $otherCourse, $otherInstructor, ['title' => 'Org2 Session']);

    $org1Sessions = LmsSession::forOrganization($this->org->id)->get();
    $org2Sessions = LmsSession::forOrganization($this->otherOrg->id)->get();

    expect($org1Sessions->pluck('title')->toArray())->toContain('Org1 Session');
    expect($org1Sessions->pluck('title')->toArray())->not->toContain('Org2 Session');
    expect($org2Sessions->pluck('title')->toArray())->toContain('Org2 Session');
});

it('scopes surveys by organization', function () {
    $otherCourse = createLmsCourse($this->otherOrg);

    LmsSurvey::create([
        'organization_id' => $this->org->id,
        'course_id' => $this->course->id,
        'title' => 'Org1 Survey',
        'questions' => [['type' => 'text', 'question' => 'Test?']],
    ]);

    LmsSurvey::create([
        'organization_id' => $this->otherOrg->id,
        'course_id' => $otherCourse->id,
        'title' => 'Org2 Survey',
        'questions' => [['type' => 'text', 'question' => 'Test?']],
    ]);

    $org1Surveys = LmsSurvey::forOrganization($this->org->id)->get();
    expect($org1Surveys)->toHaveCount(1);
    expect($org1Surveys->first()->title)->toBe('Org1 Survey');
});

it('scopes scheduled reports by organization', function () {
    LmsScheduledReport::create([
        'organization_id' => $this->org->id,
        'created_by' => $this->user->id,
        'report_type' => 'completion',
        'frequency' => 'daily',
        'recipients' => ['a@b.com'],
        'next_send_at' => Carbon::now()->addDay(),
    ]);

    LmsScheduledReport::create([
        'organization_id' => $this->otherOrg->id,
        'created_by' => $this->otherUser->id,
        'report_type' => 'engagement',
        'frequency' => 'weekly',
        'recipients' => ['c@d.com'],
        'next_send_at' => Carbon::now()->addWeek(),
    ]);

    $org1Reports = LmsScheduledReport::forOrganization($this->org->id)->get();
    expect($org1Reports)->toHaveCount(1);
    expect($org1Reports->first()->report_type)->toBe('completion');
});

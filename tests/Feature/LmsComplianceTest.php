<?php

use App\Models\LmsComplianceRecord;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lms\ComplianceTrackingService;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

function createComplianceCourse(Organization $org, array $overrides = []): LmsCourse
{
    $course = LmsCourse::create([
        'title' => $overrides['title'] ?? 'Compliance Course',
        'organization_id' => $org->id,
        'is_active' => true,
        'category' => $overrides['category'] ?? null,
    ]);

    $course->forceFill(array_intersect_key($overrides, array_flip([
        'is_compliance', 'compliance_deadline_days',
        'recertification_interval_months', 'compliance_category',
    ])))->save();

    return $course->fresh();
}

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
});

it('creates compliance record when enrolling in compliance course', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'Safety Training',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
        'compliance_category' => 'safety',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now(),
    ]);

    $service = app(ComplianceTrackingService::class);
    $record = $service->createRecord($enrollment);

    expect($record)->not->toBeNull();
    expect($record->status)->toBe('pending');
    expect($record->organization_id)->toBe($this->org->id);
    expect($record->due_date->toDateString())->toBe(Carbon::now()->addDays(30)->toDateString());
});

it('does not create compliance record for non-compliance course', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'Regular Course',
        'is_compliance' => false,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now(),
    ]);

    $service = app(ComplianceTrackingService::class);
    $record = $service->createRecord($enrollment);

    expect($record)->toBeNull();
});

it('detects overdue records correctly', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'Overdue Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 10,
        'compliance_category' => 'security',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now()->subDays(20),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->subDays(5),
        'status' => 'pending',
    ]);

    $service = app(ComplianceTrackingService::class);
    $count = $service->checkOverdueRecords($this->org->id);

    expect($count)->toBe(1);

    $record = LmsComplianceRecord::first();
    expect($record->status)->toBe('overdue');
});

it('marks compliance record as completed and calculates recertification date', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'Cert Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
        'recertification_interval_months' => 12,
        'compliance_category' => 'ethics',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'completed',
        'started_at' => Carbon::now()->subDays(10),
        'completed_at' => Carbon::now(),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->addDays(20),
        'status' => 'pending',
    ]);

    $service = app(ComplianceTrackingService::class);
    $record = $service->handleCompletion($enrollment);

    expect($record)->not->toBeNull();
    expect($record->status)->toBe('completed');
    expect($record->completed_date->toDateString())->toBe(Carbon::today()->toDateString());
    expect($record->recertification_due_date->toDateString())
        ->toBe(Carbon::today()->addMonths(12)->toDateString());
});

it('processes escalation levels correctly', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'Escalation Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
        'compliance_category' => 'regulatory',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now()->subDays(45),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->subDays(10),
        'status' => 'overdue',
        'escalation_level' => 0,
    ]);

    $service = app(ComplianceTrackingService::class);
    $results = $service->processEscalations($this->org->id);

    $record = LmsComplianceRecord::first();
    expect($record->escalation_level)->toBe(3);
    expect($results['escalated'])->toBe(1);
});

it('returns correct dashboard data', function () {
    Sanctum::actingAs($this->user, ['*']);

    $course = createComplianceCourse($this->org, [
        'title' => 'Dashboard Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
        'compliance_category' => 'safety',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now(),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->addDays(15),
        'status' => 'pending',
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->subDays(5),
        'status' => 'completed',
        'completed_date' => Carbon::now()->subDays(2),
    ]);

    $service = app(ComplianceTrackingService::class);
    $data = $service->getDashboardData($this->org->id);

    expect($data['total_records'])->toBe(2);
    expect($data['completed'])->toBe(1);
    expect($data['pending'])->toBe(1);
    expect($data['compliance_rate_pct'])->toBe(50.0);
});

it('exports CSV with expected data', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'CSV Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
        'compliance_category' => 'safety',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now(),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->addDays(15),
        'status' => 'pending',
    ]);

    $service = app(ComplianceTrackingService::class);
    $csv = $service->exportCsv($this->org->id);

    expect($csv)->toContain('ID,User,Email,Course,Category,Due Date');
    expect($csv)->toContain('CSV Course');
    expect($csv)->toContain('safety');
});

it('creates recertification records for expired certifications', function () {
    $course = createComplianceCourse($this->org, [
        'title' => 'Recert Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
        'recertification_interval_months' => 6,
        'compliance_category' => 'safety',
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'completed',
        'started_at' => Carbon::now()->subMonths(7),
        'completed_at' => Carbon::now()->subMonths(6),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->subMonths(6)->subDays(5),
        'status' => 'completed',
        'completed_date' => Carbon::now()->subMonths(6),
        'recertification_due_date' => Carbon::now()->subDays(1),
    ]);

    $service = app(ComplianceTrackingService::class);
    $count = $service->processRecertifications($this->org->id);

    expect($count)->toBe(1);
    expect(LmsComplianceRecord::where('status', 'pending')->count())->toBe(1);
    expect(LmsComplianceRecord::where('status', 'expired')->count())->toBe(1);
});

it('enforces multi-tenant isolation on compliance records', function () {
    $otherOrg = Organization::factory()->create();

    $course = createComplianceCourse($this->org, [
        'title' => 'Org1 Course',
        'is_compliance' => true,
        'compliance_deadline_days' => 30,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'enrolled',
        'started_at' => Carbon::now(),
    ]);

    LmsComplianceRecord::create([
        'lms_enrollment_id' => $enrollment->id,
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'due_date' => Carbon::now()->addDays(15),
        'status' => 'pending',
    ]);

    $service = app(ComplianceTrackingService::class);

    $thisOrgData = $service->getDashboardData($this->org->id);
    $otherOrgData = $service->getDashboardData($otherOrg->id);

    expect($thisOrgData['total_records'])->toBe(1);
    expect($otherOrgData['total_records'])->toBe(0);
});

it('returns valid data from report endpoints', function () {
    Sanctum::actingAs($this->user, ['*']);

    $course = LmsCourse::create([
        'title' => 'Report Course',
        'organization_id' => $this->org->id,
        'is_active' => true,
        'category' => 'leadership',
    ]);

    LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'completed',
        'started_at' => Carbon::now()->subDays(5),
        'completed_at' => Carbon::now(),
    ]);

    $this->getJson('/api/lms/reports/completion')
        ->assertOk()
        ->assertJsonStructure(['data' => ['by_category', 'totals']]);

    $this->getJson('/api/lms/reports/compliance')
        ->assertOk()
        ->assertJsonStructure(['data' => ['courses', 'overall_compliance_rate']]);

    $this->getJson('/api/lms/reports/time-to-complete')
        ->assertOk()
        ->assertJsonStructure(['data' => ['courses']]);

    $this->getJson('/api/lms/reports/engagement')
        ->assertOk()
        ->assertJsonStructure(['data' => ['months']]);
});

it('returns 401 for unauthenticated access', function () {
    $this->getJson('/api/lms/compliance/dashboard')->assertUnauthorized();
    $this->getJson('/api/lms/compliance/records')->assertUnauthorized();
    $this->postJson('/api/lms/compliance/check')->assertUnauthorized();
    $this->getJson('/api/lms/reports/completion')->assertUnauthorized();
    $this->getJson('/api/lms/reports/compliance')->assertUnauthorized();
});

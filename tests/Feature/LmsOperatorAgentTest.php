<?php

use App\Events\CertificateIssued;
use App\Models\LmsEnrollment;
use App\Models\LmsCourse;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('operator issues certificate and dispatches event', function () {
    Event::fake();

    $org = Organization::factory()->create();
    $user = User::factory()->create();
    $person = People::factory()->create(['user_id' => $user->id, 'organization_id' => $org->id]);

    $course = LmsCourse::create(['title' => 'Test Course', 'organization_id' => $org->id]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $agent = app(App\Services\Agents\LmsOperatorAgent::class);
    $result = $agent->issueCertificate($enrollment->id);

    Event::assertDispatched(CertificateIssued::class);
    expect($result['lms_certificate_id'])->not->toBeNull();
});

<?php

use App\Events\CertificateRevoked;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\People;
use App\Models\TalentPass;
use App\Models\TalentPassCredential;
use App\Models\User;
use App\Services\Talent\Lms\CertificateService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

it('issues certificate and stores artifact', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');

    $org = Organization::factory()->create();
    $person = People::factory()->create(['organization_id' => $org->id]);

    $service = new CertificateService;
    $cert = $service->issue([
        'organization_id' => $org->id,
        'person_id' => $person->id,
    ]);

    expect($cert)->toBeInstanceOf(App\Models\LmsCertificate::class);

    $expectedPath = 'certificates/'.$cert->id.'-'.$cert->certificate_number.'.pdf';
    Storage::disk('local')->assertExists($expectedPath);

    // Verify using service (which normalizes Storage::url() paths)
    expect($service->verify($cert))->toBeTrue();
});

it('revokes certificate and dispatches revoked event', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');
    Event::fake();

    $org = Organization::factory()->create();
    $person = People::factory()->create(['organization_id' => $org->id]);

    $service = new CertificateService;
    $cert = $service->issue([
        'organization_id' => $org->id,
        'person_id' => $person->id,
    ]);

    $revoked = $service->revoke($cert, 'compliance check');

    expect($revoked->is_revoked)->toBeTrue();
    expect($revoked->meta['revoked_reason'] ?? null)->toBe('compliance check');

    Event::assertDispatched(CertificateRevoked::class);
});

it('syncs issued lms certificate to talent pass credentials automatically', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');

    $org = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $person = People::factory()->create(['organization_id' => $org->id, 'user_id' => $user->id]);

    $course = LmsCourse::create([
        'title' => 'Data Literacy',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'progress_percentage' => 100,
        'status' => 'completed',
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    $service = new CertificateService;

    $cert = $service->issue([
        'organization_id' => $org->id,
        'person_id' => $person->id,
        'lms_enrollment_id' => $enrollment->id,
        'certificate_number' => 'CERT-TP-001',
    ]);

    $talentPass = TalentPass::query()
        ->where('organization_id', $org->id)
        ->where('people_id', $person->id)
        ->first();

    expect($talentPass)->not->toBeNull();

    $credential = TalentPassCredential::query()
        ->where('talent_pass_id', $talentPass->id)
        ->where('credential_id', 'CERT-TP-001')
        ->first();

    expect($credential)->not->toBeNull();
    expect($credential?->credential_name)->toBe('Data Literacy');
    expect($credential?->issuer)->toBe('Stratos LMS');
    expect($credential?->credential_url)->toBe($cert->certificate_url);
});

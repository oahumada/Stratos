<?php

use App\Events\CertificateRevoked;
use App\Models\Organization;
use App\Models\People;
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

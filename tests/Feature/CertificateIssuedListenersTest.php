<?php

use App\Events\CertificateIssued;
use App\Services\Talent\Lms\CertificateService;
use App\Models\Organization;
use App\Models\People;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

it('listeners handle CertificateIssued and perform side-effects', function () {
    config(['filesystems.default' => 'local']);
    Log::shouldReceive('info')->times(3)->andReturnNull();

    $org = Organization::factory()->create();
    $person = People::factory()->create(['organization_id' => $org->id]);

    $service = new CertificateService();
    $cert = $service->issue(['organization_id' => $org->id, 'person_id' => $person->id]);

    Event::dispatch(new CertificateIssued($cert));

    // Expectations are validated by the Log mock
    expect(true)->toBeTrue();
});

<?php

use function Pest\Faker\fake;
use App\Services\Talent\Lms\CertificateService;
use App\Models\Organization;
use App\Models\People;
use Illuminate\Support\Facades\Storage;

it('issues certificate and stores artifact', function () {
    config(['filesystems.default' => 'local']);
    Storage::fake('local');

    $org = Organization::factory()->create();
    $person = People::factory()->create(['organization_id' => $org->id]);

    $service = new CertificateService();
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

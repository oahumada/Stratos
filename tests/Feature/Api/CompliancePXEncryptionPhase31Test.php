<?php

use App\Models\AssessmentSession;
use App\Models\Organization;
use App\Models\People;
use App\Models\PsychometricProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
});

it('encrypts psychometric profile rationale and evidence at rest', function () {
    $personId = DB::table('people')->insertGetId([
        'organization_id' => $this->organization->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
        'hire_date' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $sessionId = DB::table('assessment_sessions')->insertGetId([
        'organization_id' => $this->organization->id,
        'people_id' => $personId,
        'type' => 'psychometric',
        'status' => 'completed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $rationale = 'Análisis psicológico confidencial del perfil';
    $evidence = 'Evidencia basada en entrevista estructurada y tests';

    $profile = PsychometricProfile::query()->create([
        'people_id' => $personId,
        'assessment_session_id' => $sessionId,
        'trait_name' => 'Leadership Potential',
        'score' => 8.5,
        'rationale' => $rationale,
        'evidence' => $evidence,
    ]);

    $raw = DB::table('psychometric_profiles')
        ->where('id', $profile->id)
        ->first(['rationale', 'evidence']);

    expect($raw)
        ->not->toBeNull()
        ->and($raw->rationale)->not->toBe($rationale)
        ->and($raw->evidence)->not->toBe($evidence)
        ->and(Crypt::decryptString($raw->rationale))->toBe($rationale)
        ->and(Crypt::decryptString($raw->evidence))->toBe($evidence);

    $freshProfile = PsychometricProfile::query()->findOrFail($profile->id);

    expect($freshProfile->rationale)->toBe($rationale)
        ->and($freshProfile->evidence)->toBe($evidence);
});

it('supports legacy plaintext psychometric data without breaking reads', function () {
    $personId = DB::table('people')->insertGetId([
        'organization_id' => $this->organization->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'hire_date' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $sessionId = DB::table('assessment_sessions')->insertGetId([
        'organization_id' => $this->organization->id,
        'people_id' => $personId,
        'type' => 'psychometric',
        'status' => 'completed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $legacyRationale = 'Legacy rationale plain text';
    $legacyEvidence = 'Legacy evidence in plain text';

    $profileId = DB::table('psychometric_profiles')->insertGetId([
        'people_id' => $personId,
        'assessment_session_id' => $sessionId,
        'trait_name' => 'Legacy Trait',
        'score' => 0.700,
        'rationale' => $legacyRationale,
        'evidence' => $legacyEvidence,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $profile = PsychometricProfile::query()->findOrFail($profileId);

    expect($profile->rationale)->toBe($legacyRationale)
        ->and($profile->evidence)->toBe($legacyEvidence);
});

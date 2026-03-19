<?php

use App\Models\Organization;
use App\Models\Roles;
use App\Models\RoleSkill;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organizationA = Organization::factory()->create();
    $this->organizationB = Organization::factory()->create();

    $this->userA = User::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);
});

it('requires authentication for phase 4 compliance endpoints', function () {
    $this->getJson('/api/compliance/internal-audit-wizard')->assertUnauthorized();
    $this->getJson('/api/compliance/credentials/roles/1')->assertUnauthorized();
    $this->postJson('/api/compliance/credentials/roles/1/verify')->assertUnauthorized();
});

it('exports vc json-ld for a role in authenticated organization only', function () {
    config([
        'stratos.compliance.issuer_did' => 'did:web:compliance.stratos.test',
    ]);

    $role = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Chief Transformation Officer',
        'digital_signature' => 'sig-abc-123',
        'signed_at' => now()->subDays(10),
        'signature_version' => 'v1.0',
    ]);

    $foreignRole = Roles::factory()->create([
        'organization_id' => $this->organizationB->id,
        'name' => 'Foreign Role',
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/credentials/roles/'.$role->id);

    $response->assertSuccessful();
    $response->assertJsonPath('data.type.0', 'VerifiableCredential');
    $response->assertJsonPath('data.type.1', 'RoleComplianceCredential');
    $response->assertJsonPath('data.issuer.id', 'did:web:compliance.stratos.test');
    $response->assertJsonPath('data.credentialSubject.role.id', $role->id);
    $response->assertJsonPath('data.credentialSubject.role.name', 'Chief Transformation Officer');
    $response->assertJsonPath('data.credentialSubject.compliance.digital_signature_present', true);

    $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/credentials/roles/'.$foreignRole->id)
        ->assertNotFound();
});

it('verifies role credential cryptographically and detects tampering', function () {
    config([
        'stratos.compliance.issuer_did' => 'did:web:compliance.stratos.test',
    ]);

    $role = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Verified Role',
        'description' => 'Role used for VC verification test',
        'status' => 'active',
    ]);

    $role->seal();
    $role->refresh();

    $credentialResponse = $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/credentials/roles/'.$role->id);

    $credentialResponse->assertSuccessful();
    $credential = $credentialResponse->json('data');

    $verificationResponse = $this->actingAs($this->userA, 'sanctum')
        ->postJson('/api/compliance/credentials/roles/'.$role->id.'/verify', [
            'credential' => $credential,
        ]);

    $verificationResponse->assertSuccessful();
    $verificationResponse->assertJsonPath('data.is_valid', true);
    $verificationResponse->assertJsonPath('data.checks.proof_matches_role_signature', true);
    $verificationResponse->assertJsonPath('data.checks.issuer_matches_expected', true);
    $verificationResponse->assertJsonPath('data.checks.credential_subject_role_matches', true);

    $tamperedCredential = $credential;
    $tamperedCredential['proof']['jws'] = 'tampered-signature';

    $tamperedResponse = $this->actingAs($this->userA, 'sanctum')
        ->postJson('/api/compliance/credentials/roles/'.$role->id.'/verify', [
            'credential' => $tamperedCredential,
        ]);

    $tamperedResponse->assertSuccessful();
    $tamperedResponse->assertJsonPath('data.is_valid', false);
    $tamperedResponse->assertJsonPath('data.checks.proof_matches_role_signature', false);
});

it('returns internal audit wizard summary for critical roles with signature status', function () {
    $skillA = Skill::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Critical Skill A',
    ]);

    $skillB = Skill::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Critical Skill B',
    ]);

    $signedRole = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Signed Critical Role',
        'digital_signature' => 'sig-signed',
        'signed_at' => now()->subDays(15),
        'signature_version' => 'v1.0',
    ]);

    $missingRole = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Missing Signature Role',
        'digital_signature' => null,
        'signed_at' => null,
    ]);

    $expiredRole = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Expired Signature Role',
        'digital_signature' => 'sig-expired',
        'signed_at' => now()->subDays(400),
        'signature_version' => 'v1.0',
    ]);

    $foreignRole = Roles::factory()->create([
        'organization_id' => $this->organizationB->id,
        'name' => 'Foreign Critical Role',
        'digital_signature' => 'sig-foreign',
        'signed_at' => now(),
    ]);

    RoleSkill::create([
        'role_id' => $signedRole->id,
        'skill_id' => $skillA->id,
        'required_level' => 4,
        'is_critical' => true,
    ]);

    RoleSkill::create([
        'role_id' => $missingRole->id,
        'skill_id' => $skillA->id,
        'required_level' => 4,
        'is_critical' => true,
    ]);

    RoleSkill::create([
        'role_id' => $expiredRole->id,
        'skill_id' => $skillB->id,
        'required_level' => 5,
        'is_critical' => true,
    ]);

    RoleSkill::create([
        'role_id' => $foreignRole->id,
        'skill_id' => $skillB->id,
        'required_level' => 5,
        'is_critical' => true,
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/internal-audit-wizard?signature_valid_days=365');

    $response->assertSuccessful();
    $response->assertJsonPath('data.summary.total_critical_roles', 3);
    $response->assertJsonPath('data.summary.compliant_roles', 1);
    $response->assertJsonPath('data.summary.non_compliant_roles', 2);

    $roles = collect($response->json('data.roles'));
    expect($roles->pluck('role_name')->all())
        ->toContain('Signed Critical Role')
        ->toContain('Missing Signature Role')
        ->toContain('Expired Signature Role')
        ->not->toContain('Foreign Critical Role');

    $missing = $roles->firstWhere('role_name', 'Missing Signature Role');
    $expired = $roles->firstWhere('role_name', 'Expired Signature Role');

    expect($missing['signature_status'])->toBe('missing');
    expect($expired['signature_status'])->toBe('expired');
});

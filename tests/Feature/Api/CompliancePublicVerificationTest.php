<?php

use App\Models\Organization;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves did document publicly with expected issuer and verification service endpoint', function () {
    config([
        'app.url' => 'https://stratos.example',
        'stratos.compliance.issuer_did' => 'did:web:compliance.stratos.example',
        'stratos.compliance.verification_method_fragment' => 'stratos-digital-seal',
    ]);

    $response = $this->get('/.well-known/did.json');

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'application/did+json');
    $response->assertJsonPath('id', 'did:web:compliance.stratos.example');
    $response->assertJsonPath('verificationMethod.0.id', 'did:web:compliance.stratos.example#stratos-digital-seal');
    $response->assertJsonPath('service.0.serviceEndpoint', 'https://stratos.example/api/compliance/public/credentials/verify');
    $response->assertJsonPath('service.1.serviceEndpoint', 'https://stratos.example/api/compliance/public/verifier-metadata');
});

it('serves public verifier metadata for external auditors', function () {
    config([
        'app.url' => 'https://stratos.example',
        'stratos.compliance.issuer_did' => 'did:web:compliance.stratos.example',
        'stratos.compliance.verifier_version' => '2026.03',
        'stratos.compliance.policy_version' => 'v2',
    ]);

    $response = $this->getJson('/api/compliance/public/verifier-metadata');

    $response->assertSuccessful();
    $response->assertJsonPath('data.version', '2026.03');
    $response->assertJsonPath('data.policy_version', 'v2');
    $response->assertJsonPath('data.issuer_did', 'did:web:compliance.stratos.example');
    $response->assertJsonPath('data.verification.public_verify_endpoint', 'https://stratos.example/api/compliance/public/credentials/verify');
    $response->assertJsonPath('data.did_document', 'https://stratos.example/.well-known/did.json');
    $response->assertJsonPath('data.supported_credentials.0', 'RoleComplianceCredential');
});

it('verifies credential publicly and detects tampering without authentication', function () {
    config([
        'stratos.compliance.issuer_did' => 'did:web:compliance.stratos.test',
    ]);

    $organization = Organization::factory()->create();

    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $role = Roles::factory()->create([
        'organization_id' => $organization->id,
        'name' => 'Externally Verifiable Role',
        'description' => 'Role for public verification test',
        'status' => 'active',
    ]);

    $role->seal();
    $role->refresh();

    $credentialResponse = $this->actingAs($user, 'sanctum')
        ->getJson('/api/compliance/credentials/roles/'.$role->id);

    $credentialResponse->assertSuccessful();
    $credential = $credentialResponse->json('data');

    $publicVerification = $this->postJson('/api/compliance/public/credentials/verify', [
        'credential' => $credential,
    ]);

    $publicVerification->assertSuccessful();
    $publicVerification->assertJsonPath('data.is_valid', true);
    $publicVerification->assertJsonPath('data.role_exists', true);
    $publicVerification->assertJsonPath('data.checks.proof_matches_role_signature', true);
    $publicVerification->assertJsonPath('data.checks.issuer_matches_expected', true);
    $publicVerification->assertJsonPath('data.checks.credential_subject_role_matches', true);
    $publicVerification->assertJsonPath('data.checks.credential_subject_organization_matches', true);

    $tamperedCredential = $credential;
    $tamperedCredential['proof']['jws'] = 'tampered-proof-value';

    $tamperedResponse = $this->postJson('/api/compliance/public/credentials/verify', [
        'credential' => $tamperedCredential,
    ]);

    $tamperedResponse->assertSuccessful();
    $tamperedResponse->assertJsonPath('data.is_valid', false);
    $tamperedResponse->assertJsonPath('data.role_exists', true);
    $tamperedResponse->assertJsonPath('data.checks.proof_matches_role_signature', false);
});

it('returns invalid for public verification when credential subject does not map to an existing role', function () {
    $response = $this->postJson('/api/compliance/public/credentials/verify', [
        'credential' => [
            'issuer' => ['id' => 'did:web:compliance.stratos.test'],
            'credentialSubject' => [
                'organization_id' => 999999,
                'role' => ['id' => 888888],
            ],
            'proof' => ['jws' => 'unknown-proof'],
        ],
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.is_valid', false);
    $response->assertJsonPath('data.role_exists', false);
});

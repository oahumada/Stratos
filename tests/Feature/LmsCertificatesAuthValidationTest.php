<?php

use App\Http\Middleware\CheckPermission;
use App\Models\LmsCertificate;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const LMS_CERTIFICATES_ENDPOINT = '/api/lms/certificates';
const LMS_CERTIFICATES_SHOW_PATTERN = '/api/lms/certificates/%d';
const LMS_CERTIFICATES_DOWNLOAD_PATTERN = '/api/lms/certificates/%d/download';
const LMS_CERTIFICATES_REVOKE_PATTERN = '/api/lms/certificates/%d/revoke';

it('requires authentication for protected certificates endpoints', function (string $method, string $endpoint) {
    $this->json($method, $endpoint)->assertUnauthorized();
})->with([
    'GET index' => ['GET', LMS_CERTIFICATES_ENDPOINT],
    'GET show' => ['GET', '/api/lms/certificates/1'],
    'GET download' => ['GET', '/api/lms/certificates/1/download'],
    'POST revoke' => ['POST', '/api/lms/certificates/1/revoke'],
]);

it('forbids protected certificates endpoints when permission is missing', function (string $method, string $endpoint) {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cert_no_access');

    Sanctum::actingAs($user, ['*']);

    $this->json($method, $endpoint)->assertForbidden();
})->with([
    'GET index forbidden' => ['GET', LMS_CERTIFICATES_ENDPOINT],
    'GET show forbidden' => ['GET', '/api/lms/certificates/1'],
    'GET download forbidden' => ['GET', '/api/lms/certificates/1/download'],
    'POST revoke forbidden' => ['POST', '/api/lms/certificates/1/revoke'],
]);

it('returns 422 when organization cannot be resolved for protected certificates endpoints', function (string $method, string $endpoint) {
    $this->withoutMiddleware(CheckPermission::class);

    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cert_org_missing');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    $this->json($method, $endpoint)
        ->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para certificados LMS.');
})->with([
    'GET index 422' => ['GET', LMS_CERTIFICATES_ENDPOINT],
    'GET show 422' => ['GET', '/api/lms/certificates/1'],
    'GET download 422' => ['GET', '/api/lms/certificates/1/download'],
    'POST revoke 422' => ['POST', '/api/lms/certificates/1/revoke'],
]);

it('allows protected certificates endpoints with lms certify permission and enforces tenant isolation', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cert_access');
    grantPermissionToRole('qa_lms_cert_access', 'lms.certify', 'lms', 'certify');

    $sameOrgCertificate = LmsCertificate::query()->create([
        'organization_id' => $organization->id,
        'certificate_number' => 'CERT-ALLOW-01',
        'certificate_url' => 'https://example.test/cert-allow-01.pdf',
        'certificate_hash' => 'hash-allow-01',
        'issued_at' => now(),
        'is_revoked' => false,
    ]);

    $otherOrgCertificate = LmsCertificate::query()->create([
        'organization_id' => $otherOrganization->id,
        'certificate_number' => 'CERT-OTHER-01',
        'certificate_url' => 'https://example.test/cert-other-01.pdf',
        'certificate_hash' => 'hash-other-01',
        'issued_at' => now(),
        'is_revoked' => false,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(LMS_CERTIFICATES_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('data.0.id', $sameOrgCertificate->id);

    $this->getJson(sprintf(LMS_CERTIFICATES_SHOW_PATTERN, $sameOrgCertificate->id))
        ->assertOk()
        ->assertJsonPath('data.id', $sameOrgCertificate->id);

    $this->getJson(sprintf(LMS_CERTIFICATES_DOWNLOAD_PATTERN, $sameOrgCertificate->id))
        ->assertOk()
        ->assertJsonPath('url', $sameOrgCertificate->certificate_url);

    $this->postJson(sprintf(LMS_CERTIFICATES_REVOKE_PATTERN, $sameOrgCertificate->id))
        ->assertOk()
        ->assertJsonPath('success', true);

    expect((bool) $sameOrgCertificate->fresh()->is_revoked)->toBeTrue();

    $this->getJson(sprintf(LMS_CERTIFICATES_SHOW_PATTERN, $otherOrgCertificate->id))
        ->assertNotFound();

    $this->getJson(sprintf(LMS_CERTIFICATES_DOWNLOAD_PATTERN, $otherOrgCertificate->id))
        ->assertNotFound();

    $this->postJson(sprintf(LMS_CERTIFICATES_REVOKE_PATTERN, $otherOrgCertificate->id))
        ->assertNotFound();
});

it('allows verify endpoints for authenticated users without requiring lms certify permission', function () {
    $organization = Organization::factory()->create();
    $certificate = LmsCertificate::query()->create([
        'organization_id' => $organization->id,
        'certificate_number' => 'CERT-VERIFY-01',
        'certificate_url' => 'https://example.test/cert-verify-01.pdf',
        'certificate_hash' => 'hash-verify-01',
        'issued_at' => now(),
        'is_revoked' => false,
    ]);

    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cert_verify_only');
    Sanctum::actingAs($user, ['*']);

    $this->getJson(sprintf('/api/lms/certificates/%d/verify', $certificate->id))
        ->assertOk()
        ->assertJsonStructure(['verified']);

    $this->getJson(sprintf('/api/lms/certificates/%d/verification', $certificate->id))
        ->assertOk()
        ->assertJsonStructure(['verified']);
});

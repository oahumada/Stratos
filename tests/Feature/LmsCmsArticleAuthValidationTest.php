<?php

use App\Http\Middleware\CheckPermission;
use App\Jobs\GenerateLmsArticle;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const LMS_CMS_ARTICLES_ENDPOINT = '/api/lms/cms/articles';
const LMS_CMS_TOPIC = 'Ruta de liderazgo';

it('requires authentication for lms cms article creation', function () {
    $this->postJson(LMS_CMS_ARTICLES_ENDPOINT, [
        'topic' => LMS_CMS_TOPIC,
    ])->assertUnauthorized();
});

it('forbids lms cms article creation when permission is missing', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cms_no_access');

    Sanctum::actingAs($user, ['*']);

    $this->postJson(LMS_CMS_ARTICLES_ENDPOINT, [
        'topic' => LMS_CMS_TOPIC,
    ])->assertForbidden();
});

it('validates payload when creating lms cms article', function () {
    $this->withoutMiddleware(CheckPermission::class);

    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cms_validation');

    Sanctum::actingAs($user, ['*']);

    $this->postJson(LMS_CMS_ARTICLES_ENDPOINT, [
        'topic' => '',
        'options' => 'invalid',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['topic', 'options']);
});

it('returns 422 when organization cannot be resolved for lms cms article creation', function () {
    $this->withoutMiddleware(CheckPermission::class);

    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cms_org_missing');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(LMS_CMS_ARTICLES_ENDPOINT, [
        'topic' => LMS_CMS_TOPIC,
    ])->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para CMS LMS.');
});

it('queues article generation with the authenticated user organization when permission is granted', function () {
    Bus::fake();

    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'qa_lms_cms_access');

    grantPermissionToRole('qa_lms_cms_access', 'lms.cms.manage', 'lms', 'manage');

    Sanctum::actingAs($user, ['*']);

    $this->postJson(LMS_CMS_ARTICLES_ENDPOINT, [
        'topic' => LMS_CMS_TOPIC,
        'auto_publish' => true,
        'author_id' => 999,
        'organization_id' => $otherOrganization->id,
        'options' => [
            'tone' => 'executive',
        ],
    ])->assertStatus(202)
        ->assertJsonPath('success', true);

    Bus::assertDispatched(GenerateLmsArticle::class, function (GenerateLmsArticle $job) use ($organization) {
        return $job->organizationId === $organization->id
            && $job->topic === LMS_CMS_TOPIC
            && ($job->options['auto_publish'] ?? null) === true
            && ($job->options['author_id'] ?? null) === 999
            && ($job->options['tone'] ?? null) === 'executive';
    });
});

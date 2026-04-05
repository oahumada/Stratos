<?php

use App\Models\LmsCommunity;
use App\Models\LmsCommunityActivity;
use App\Models\LmsCommunityMember;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lms\CommunityHealthService;
use App\Services\Lms\CommunityProgressionService;
use App\Services\Lms\CommunityService;
use Laravel\Sanctum\Sanctum;

// =============================================================================
// CRUD Tests
// =============================================================================

it('can list communities', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    LmsCommunity::factory()->count(3)->create(['organization_id' => $org->id]);

    $response = $this->getJson('/api/lms/communities');

    $response->assertStatus(200);
    $response->assertJsonStructure(['data', 'current_page', 'per_page']);
    expect(count($response->json('data')))->toBe(3);
});

it('can create a community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.manage', 'lms', 'manage');

    $response = $this->postJson('/api/lms/communities', [
        'name' => 'DevOps Practice Group',
        'description' => 'A community for DevOps practitioners',
        'type' => 'practice',
        'domain_skills' => ['kubernetes', 'ci-cd'],
        'learning_goals' => ['Master CI/CD pipelines'],
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment(['name' => 'DevOps Practice Group']);
    $this->assertDatabaseHas('lms_communities', [
        'name' => 'DevOps Practice Group',
        'organization_id' => $org->id,
    ]);
});

it('validates required fields when creating community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.manage', 'lms', 'manage');

    $response = $this->postJson('/api/lms/communities', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

it('can show a community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    $response = $this->getJson("/api/lms/communities/{$community->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => $community->name]);
    $response->assertJsonStructure(['community', 'health']);
});

it('can update a community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.manage', 'lms', 'manage');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id, 'name' => 'Old Name']);

    $response = $this->putJson("/api/lms/communities/{$community->id}", [
        'name' => 'New Name',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('lms_communities', ['id' => $community->id, 'name' => 'New Name']);
});

it('can archive a community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.manage', 'lms', 'manage');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    $response = $this->deleteJson("/api/lms/communities/{$community->id}");

    $response->assertStatus(200);
    $this->assertDatabaseHas('lms_communities', ['id' => $community->id, 'status' => 'archived']);
});

// =============================================================================
// Membership Tests
// =============================================================================

it('allows a user to join a community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    $response = $this->postJson("/api/lms/communities/{$community->id}/join");

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_community_members', [
        'community_id' => $community->id,
        'user_id' => $user->id,
        'role' => 'novice',
    ]);
});

it('allows a user to leave a community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
    ]);

    $response = $this->postJson("/api/lms/communities/{$community->id}/leave");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('lms_community_members', [
        'community_id' => $community->id,
        'user_id' => $user->id,
    ]);
});

it('cannot join a full community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create([
        'organization_id' => $org->id,
        'max_members' => 2,
    ]);

    // Fill the community
    LmsCommunityMember::factory()->count(2)->create(['community_id' => $community->id]);

    $response = $this->postJson("/api/lms/communities/{$community->id}/join");

    $response->assertStatus(422);
});

it('cannot join a community twice', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    // First join
    $this->postJson("/api/lms/communities/{$community->id}/join")->assertStatus(201);

    // Second join should fail (unique constraint)
    $response = $this->postJson("/api/lms/communities/{$community->id}/join");
    $response->assertStatus(422);
});

it('can list community members', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    LmsCommunityMember::factory()->count(3)->create(['community_id' => $community->id]);

    $response = $this->getJson("/api/lms/communities/{$community->id}/members");

    $response->assertStatus(200);
    $response->assertJsonStructure(['data', 'current_page']);
    expect(count($response->json('data')))->toBe(3);
});

// =============================================================================
// Progression Tests (service-level)
// =============================================================================

it('promotes novice to member after 3 discussions', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $member = LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
        'role' => 'novice',
        'discussions_count' => 3,
    ]);

    $service = app(CommunityProgressionService::class);
    $result = $service->evaluateProgression($member);

    expect($result['promoted'])->toBeTrue();
    expect($result['next_role'])->toBe('member');
});

it('promotes member to contributor with 10 discussions and 2 ugc', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $member = LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
        'role' => 'member',
        'discussions_count' => 10,
        'ugc_count' => 2,
    ]);

    $service = app(CommunityProgressionService::class);
    $result = $service->evaluateProgression($member);

    expect($result['promoted'])->toBeTrue();
    expect($result['next_role'])->toBe('contributor');
});

it('promotes contributor to mentor with 20 discussions, 5 ugc, and 3 peer reviews', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $member = LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
        'role' => 'contributor',
        'discussions_count' => 20,
        'ugc_count' => 5,
        'peer_reviews_count' => 3,
    ]);

    $service = app(CommunityProgressionService::class);
    $result = $service->evaluateProgression($member);

    expect($result['promoted'])->toBeTrue();
    expect($result['next_role'])->toBe('mentor');
});

it('does not promote novice without meeting thresholds', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $member = LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
        'role' => 'novice',
        'discussions_count' => 1,
        'ugc_count' => 0,
    ]);

    $service = app(CommunityProgressionService::class);
    $result = $service->evaluateProgression($member);

    expect($result['promoted'])->toBeFalse();
    expect($result['current_role'])->toBe('novice');
    expect($result['next_role'])->toBe('novice');
});

it('records a recognition activity on promotion', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $member = LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
        'role' => 'novice',
        'discussions_count' => 5,
    ]);

    $service = app(CommunityProgressionService::class);
    $service->evaluateProgression($member);

    $this->assertDatabaseHas('lms_community_activities', [
        'community_id' => $community->id,
        'user_id' => $user->id,
        'activity_type' => 'recognition',
    ]);
});

// =============================================================================
// Health Score Tests (service-level)
// =============================================================================

it('calculates health score with CoI formula weights', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    $service = app(CommunityHealthService::class);
    $result = $service->assessHealth($community);

    // With no members/activities, scores should be 0
    // Formula: (social * 0.4) + (cognitive * 0.35) + (teaching * 0.25)
    expect($result)->toHaveKeys(['social_presence', 'cognitive_presence', 'teaching_presence', 'health_score', 'status']);
    expect($result['health_score'])->toEqual(0);
});

it('assigns correct health status thresholds', function () {
    $org = Organization::factory()->create();

    $service = app(CommunityHealthService::class);

    // Critical: < 25
    $community1 = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $result1 = $service->assessHealth($community1);
    expect($result1['status'])->toBe('critical');

    // Create a community with some activity to push score into at_risk (25-49)
    $community2 = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);
    LmsCommunityMember::factory()->create([
        'community_id' => $community2->id,
        'user_id' => $user->id,
    ]);
    // Create enough activity to push social presence up
    for ($i = 0; $i < 15; $i++) {
        LmsCommunityActivity::factory()->create([
            'community_id' => $community2->id,
            'user_id' => $user->id,
            'activity_type' => 'discussion',
            'presence_type' => 'social',
            'engagement_score' => 50,
        ]);
    }
    $result2 = $service->assessHealth($community2);
    expect(in_array($result2['status'], ['at_risk', 'healthy', 'thriving']))->toBeTrue();
    expect($result2['health_score'])->toBeGreaterThan(0);
});

it('returns zero health for empty community', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    $service = app(CommunityHealthService::class);
    $result = $service->assessHealth($community);

    expect($result['health_score'])->toEqual(0);
    expect($result['social_presence'])->toEqual(0);
    expect($result['cognitive_presence'])->toEqual(0);
    expect($result['teaching_presence'])->toEqual(0);
});

it('persists health scores via recalculateAndSave', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create([
        'organization_id' => $org->id,
        'health_score' => 0,
        'social_presence' => 0,
        'cognitive_presence' => 0,
        'teaching_presence' => 0,
    ]);

    // Add activity so scores are non-trivially 0
    $user = User::factory()->create(['organization_id' => $org->id]);
    LmsCommunityMember::factory()->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
    ]);
    LmsCommunityActivity::factory()->count(5)->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
        'activity_type' => 'discussion',
    ]);

    $service = app(CommunityHealthService::class);
    $result = $service->recalculateAndSave($community);

    $community->refresh();
    expect((float) $community->health_score)->toEqual($result['health_score']);
    expect((float) $community->social_presence)->toEqual($result['social_presence']);
    expect((float) $community->cognitive_presence)->toEqual($result['cognitive_presence']);
    expect((float) $community->teaching_presence)->toEqual($result['teaching_presence']);
});

// =============================================================================
// Multi-tenant Isolation Tests
// =============================================================================

it('cannot see other org communities', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    LmsCommunity::factory()->create(['organization_id' => $org1->id, 'name' => 'Org1 Community']);
    LmsCommunity::factory()->create(['organization_id' => $org2->id, 'name' => 'Org2 Community']);

    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson('/api/lms/communities');
    $response->assertStatus(200);

    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->not->toContain('Org1 Community');
    expect($names)->toContain('Org2 Community');
});

it('cannot join another org community', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org1->id]);

    Sanctum::actingAs($user2, ['*']);
    $response = $this->postJson("/api/lms/communities/{$community->id}/join");

    $response->assertStatus(403);
});

it('scopes activities to community', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community1 = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $community2 = LmsCommunity::factory()->create(['organization_id' => $org->id]);

    LmsCommunityActivity::factory()->count(2)->create([
        'community_id' => $community1->id,
        'user_id' => $user->id,
    ]);
    LmsCommunityActivity::factory()->count(3)->create([
        'community_id' => $community2->id,
        'user_id' => $user->id,
    ]);

    $response = $this->getJson("/api/lms/communities/{$community1->id}/activities");

    $response->assertStatus(200);
    // Only community1 activities should be returned
    $activityIds = collect($response->json('data'))->pluck('community_id')->unique()->all();
    expect($activityIds)->toEqual([$community1->id]);
});

// =============================================================================
// Activity Feed Tests
// =============================================================================

it('records an activity when joining a community', function () {
    $org = Organization::factory()->create();
    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $service = app(CommunityService::class);
    $service->join($community, $user);

    $this->assertDatabaseHas('lms_community_activities', [
        'community_id' => $community->id,
        'user_id' => $user->id,
        'activity_type' => 'recognition',
        'title' => 'Joined community',
    ]);
});

it('returns paginated activities from endpoint', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);
    grantPermissionToRole('admin', 'lms.courses.view', 'lms', 'view');

    $community = LmsCommunity::factory()->create(['organization_id' => $org->id]);
    LmsCommunityActivity::factory()->count(5)->create([
        'community_id' => $community->id,
        'user_id' => $user->id,
    ]);

    $response = $this->getJson("/api/lms/communities/{$community->id}/activities");

    $response->assertStatus(200);
    $response->assertJsonStructure(['data', 'current_page', 'per_page']);
    expect(count($response->json('data')))->toBe(5);
});

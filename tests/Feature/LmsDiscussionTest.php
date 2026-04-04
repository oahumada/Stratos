<?php

use App\Models\LmsCourse;
use App\Models\LmsDiscussion;
use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

const DISCUSSIONS_ENDPOINT = '/api/lms/discussions';
const FIRST_DISCUSSION_TITLE = 'First Discussion';

it('can create a discussion post', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => 'Discussion Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $response = $this->postJson(DISCUSSIONS_ENDPOINT, [
        'title' => FIRST_DISCUSSION_TITLE,
        'body' => 'This is the body of the discussion.',
        'course_id' => $course->id,
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment(['title' => FIRST_DISCUSSION_TITLE]);
    $this->assertDatabaseHas('lms_discussions', [
        'title' => FIRST_DISCUSSION_TITLE,
        'organization_id' => $org->id,
        'user_id' => $user->id,
    ]);
});

it('can reply to a post (threaded)', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $parent = LmsDiscussion::create([
        'title' => 'Parent Post',
        'body' => 'Parent body',
        'user_id' => $user->id,
        'organization_id' => $org->id,
    ]);

    $response = $this->postJson("/api/lms/discussions/{$parent->id}/reply", [
        'body' => 'This is a reply.',
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment(['parent_id' => $parent->id]);
    $this->assertDatabaseHas('lms_discussions', [
        'body' => 'This is a reply.',
        'parent_id' => $parent->id,
    ]);
});

it('can toggle like (like then unlike)', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $discussion = LmsDiscussion::create([
        'title' => 'Likeable Post',
        'body' => 'Like me!',
        'user_id' => $user->id,
        'organization_id' => $org->id,
    ]);

    // Like
    $response = $this->postJson(DISCUSSIONS_ENDPOINT . "/{$discussion->id}/like");
    $response->assertOk();
    $response->assertJsonFragment(['liked' => true, 'likes_count' => 1]);

    // Unlike
    $response = $this->postJson(DISCUSSIONS_ENDPOINT . "/{$discussion->id}/like");
    $response->assertOk();
    $response->assertJsonFragment(['liked' => false, 'likes_count' => 0]);
});

it('likes_count updates correctly', function () {
    $org = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org->id]);

    $discussion = LmsDiscussion::create([
        'title' => 'Popular Post',
        'body' => 'Many likes!',
        'user_id' => $user1->id,
        'organization_id' => $org->id,
    ]);

    Sanctum::actingAs($user1, ['*']);
    $this->postJson("/api/lms/discussions/{$discussion->id}/like");

    Sanctum::actingAs($user2, ['*']);
    $response = $this->postJson("/api/lms/discussions/{$discussion->id}/like");

    $response->assertOk();
    $response->assertJsonFragment(['likes_count' => 2]);

    expect($discussion->fresh()->likes_count)->toBe(2);
});

it('can pin a post (requires manage permission)', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $discussion = LmsDiscussion::create([
        'title' => 'Pin me',
        'body' => 'Important post',
        'user_id' => $user->id,
        'organization_id' => $org->id,
    ]);

    $response = $this->postJson("/api/lms/discussions/{$discussion->id}/pin");
    $response->assertOk();
    $response->assertJsonFragment(['is_pinned' => true]);

    // Toggle unpin
    $response = $this->postJson("/api/lms/discussions/{$discussion->id}/pin");
    $response->assertOk();
    $response->assertJsonFragment(['is_pinned' => false]);
});

it('can delete own post', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $discussion = LmsDiscussion::create([
        'title' => 'To Delete',
        'body' => 'Will be deleted',
        'user_id' => $user->id,
        'organization_id' => $org->id,
    ]);

    $response = $this->deleteJson("/api/lms/discussions/{$discussion->id}");
    $response->assertOk();

    $this->assertDatabaseMissing('lms_discussions', ['id' => $discussion->id]);
});

it('enforces multi-tenant isolation', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    $discussion = LmsDiscussion::create([
        'title' => 'Org1 Post',
        'body' => 'Org1 body',
        'user_id' => $user1->id,
        'organization_id' => $org1->id,
    ]);

    // User from org2 should not see org1 discussions
    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson(DISCUSSIONS_ENDPOINT);
    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();
    expect($ids)->not->toContain($discussion->id);

    // User from org2 cannot delete org1 post
    $response = $this->deleteJson("/api/lms/discussions/{$discussion->id}");
    $response->assertStatus(404);
});

it('unauthenticated returns 401', function () {
    $this->getJson(DISCUSSIONS_ENDPOINT)->assertUnauthorized();
    $this->postJson(DISCUSSIONS_ENDPOINT, ['body' => 'test'])->assertUnauthorized();
});

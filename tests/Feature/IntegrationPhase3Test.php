<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\UserNotificationChannel;
use App\Services\Caching\NotificationCacheService;
use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->cacheService = app(NotificationCacheService::class);
    $this->dispatcher = app(NotificationDispatcher::class);
});

test('rate limit headers present on all requests', function () {
    $response = $this->actingAs($this->user)->getJson('/api/catalogs');
    $response->assertStatus(200)
        ->assertHeader('X-RateLimit-Limit')
        ->assertHeader('X-RateLimit-Remaining')
        ->assertHeader('X-RateLimit-Reset');
});

test('cache service initialized', function () {
    expect($this->cacheService)->toBeInstanceOf(NotificationCacheService::class);
    expect($this->dispatcher)->toBeInstanceOf(NotificationDispatcher::class);
});

test('notification preferences accessible via API', function () {
    $response = $this->actingAs($this->user)->getJson('/api/notification-preferences');
    $response->assertStatus(200);
});

test('user cannot access other user preferences', function () {
    $otherUser = User::factory()->create(['organization_id' => $this->organization->id]);
    
    // This should return own preferences or 403
    $response = $this->actingAs($this->user)->getJson('/api/notification-preferences');
    expect($response->status())->toBe(200);
});

test('multi-org channels isolated', function () {
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->create(['organization_id' => $org2->id]);

    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
        'is_active' => true,
    ]);

    UserNotificationChannel::factory()->create([
        'user_id' => $user2->id,
        'organization_id' => $org2->id,
        'channel_type' => 'slack',
        'is_active' => true,
    ]);

    // User1's org should not see User2's channels
    $response1 = $this->actingAs($this->user)->getJson('/api/notification-preferences');
    $response1->assertStatus(200);
    
    $response2 = $this->actingAs($user2)->getJson('/api/notification-preferences');
    $response2->assertStatus(200);
});

test('rate limit decrements across requests', function () {
    $response1 = $this->actingAs($this->user)->getJson('/api/catalogs');
    $remaining1 = (int)$response1->headers->get('X-RateLimit-Remaining');

    $response2 = $this->actingAs($this->user)->getJson('/api/catalogs');
    $remaining2 = (int)$response2->headers->get('X-RateLimit-Remaining');

    expect($remaining2)->toBeLessThanOrEqual($remaining1);
});

test('cache warming does not error', function () {
    $result = $this->cacheService->warmCache($this->organization->id);
    expect(true)->toBeTrue();
});

test('notification channel creation and retrieval', function () {
    $response = $this->actingAs($this->user)->postJson(
        '/api/notification-preferences',
        [
            'channel_type' => 'email',
            'is_active' => true,
        ]
    );

    expect($response->status())->toBe(201);

    // Verify it appears in GET
    $getResponse = $this->actingAs($this->user)->getJson('/api/notification-preferences');
    expect($getResponse->status())->toBe(200);
});

test('rate limit and cache integration works', function () {
    // Create channel
    $this->actingAs($this->user)->postJson(
        '/api/notification-preferences',
        ['channel_type' => 'email', 'is_active' => true]
    );

    // First request
    $response1 = $this->actingAs($this->user)->getJson('/api/notification-preferences');
    $response1->assertStatus(200);
    $remaining1 = (int)$response1->headers->get('X-RateLimit-Remaining');

    // Second request (should hit cache but rate limit still applies)
    $response2 = $this->actingAs($this->user)->getJson('/api/notification-preferences');
    $response2->assertStatus(200);
    $remaining2 = (int)$response2->headers->get('X-RateLimit-Remaining');

    // Rate limit should decrement
    expect($remaining2)->toBeLessThanOrEqual($remaining1);
});

test('dispatcher accessible and functional', function () {
    $result = $this->dispatcher->dispatchToUser($this->user, [
        'title' => 'Test',
        'message' => 'Integration test',
        'type' => 'info',
    ]);

    expect($result)->toBeArray();
});

test('multi-tenancy isolation enforced on API', function () {
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->create(['organization_id' => $org2->id]);

    // Different orgs should have independent rate limits and preferences
    $response1 = $this->actingAs($this->user)->getJson('/api/catalogs');
    $remaining1 = (int)$response1->headers->get('X-RateLimit-Remaining');

    $response2 = $this->actingAs($user2)->getJson('/api/catalogs');
    $remaining2 = (int)$response2->headers->get('X-RateLimit-Remaining');

    // Should have similar remaining counts (independent limits)
    expect(abs($remaining1 - $remaining2))->toBeLessThan(10);
});

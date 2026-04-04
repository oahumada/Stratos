<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\UserNotificationChannel;
use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->dispatcher = app(NotificationDispatcher::class);
});

test('notification api endpoint accessible', function () {
    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-preferences"
    );

    $response->assertStatus(200);
});

test('user can create notification channel', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/notification-preferences",
        [
            'channel_type' => 'email',
            'is_active' => true,
        ]
    );

    expect($response->status())->toBe(201);
});

test('notification channel settings accessible', function () {
    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-channel-settings"
    );

    $response->assertStatus(200);
});

test('user notification channels scoped by organization', function () {
    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-preferences"
    );

    $response->assertStatus(200);
    expect(count($response->json('data')))->toBeGreaterThan(0);
});

test('notification dispatch service accessible', function () {
    expect($this->dispatcher)->toBeInstanceOf(NotificationDispatcher::class);
});

test('notification preferences validate channel type', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/notification-preferences",
        [
            'channel_type' => 'invalid_channel',
            'is_active' => true,
        ]
    );

    expect($response->status())->toBe(422);
});

test('toggle notification channel works', function () {
    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->user)->postJson(
        "/api/notification-preferences/email/toggle"
    );

    expect($response->status())->toBe(200);
});

test('delete notification channel works', function () {
    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
    ]);

    $response = $this->actingAs($this->user)->deleteJson(
        "/api/notification-preferences/email"
    );

    expect($response->status())->toBe(204);
});

test('multi-org notification channels isolated', function () {
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->create(['organization_id' => $org2->id]);

    UserNotificationChannel::factory()->create([
        'user_id' => $user2->id,
        'organization_id' => $org2->id,
        'channel_type' => 'email',
    ]);

    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-preferences"
    );

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

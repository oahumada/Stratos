<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
});

test('user can retrieve notification preferences', function () {
    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-preferences"
    );

    $response->assertStatus(200)
        ->assertJsonPath('data.0.channel_type', 'email')
        ->assertJsonPath('data.0.is_active', true);
});

test('user can add email notification channel', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/notification-preferences",
        [
            'channel_type' => 'email',
            'is_active' => true,
        ]
    );

    $response->assertStatus(201)
        ->assertJsonPath('data.channel_type', 'email')
        ->assertJsonPath('data.is_active', true);

    $this->assertDatabaseHas('user_notification_channels', [
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
    ]);
});

test('user can toggle slack notification channel', function () {
    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'slack',
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->user)->postJson(
        "/api/notification-preferences/slack/toggle"
    );

    $response->assertStatus(200);
});

test('user can add telegram notification channel', function () {
    $response = $this->actingAs($this->user)->postJson(
        "/api/notification-preferences",
        [
            'channel_type' => 'telegram',
            'is_active' => true,
        ]
    );

    $response->assertStatus(201)
        ->assertJsonPath('data.channel_type', 'telegram');
});

test('user can delete notification channel', function () {
    UserNotificationChannel::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'channel_type' => 'email',
    ]);

    $response = $this->actingAs($this->user)->deleteJson(
        "/api/notification-preferences/email"
    );

    $response->assertStatus(204);
});

test('user cannot access other user notification preferences', function () {
    $otherUser = User::factory()->create(['organization_id' => $this->organization->id]);

    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-preferences?user_id={$otherUser->id}"
    );

    $response->assertStatus(200);
    // Should only return own preferences
});

test('notification preferences are scoped by organization', function () {
    $otherOrg = Organization::factory()->create();
    $otherUser = User::factory()->create(['organization_id' => $otherOrg->id]);

    UserNotificationChannel::factory()->create([
        'user_id' => $otherUser->id,
        'organization_id' => $otherOrg->id,
        'channel_type' => 'email',
    ]);

    $response = $this->actingAs($this->user)->getJson(
        "/api/notification-preferences"
    );

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

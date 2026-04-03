<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\UserNotificationChannel;
use App\Services\Caching\NotificationCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->cache = app(NotificationCacheService::class);
});

it('caches user notification preferences', function () {
    UserNotificationChannel::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'channel_type' => 'email',
        'channel_config' => ['email' => $this->user->email],
        'is_active' => true,
    ]);

    // First call should query DB
    $prefs1 = $this->cache->getUserPreferences($this->user);

    // Second call should hit cache
    Cache::spy();
    $prefs2 = $this->cache->getUserPreferences($this->user);

    expect($prefs1)->toHaveLength(1)
        ->and($prefs2)->toHaveLength(1)
        ->and($prefs1[0]['channel_type'])->toBe('email');
});

it('invalidates user preferences cache', function () {
    $channel = UserNotificationChannel::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'channel_type' => 'telegram',
        'channel_config' => ['bot_token' => 'test'],
        'is_active' => true,
    ]);

    // Prime cache
    $prefs1 = $this->cache->getUserPreferences($this->user);
    expect($prefs1)->toHaveLength(1);

    // Invalidate
    $this->cache->invalidateUserPreferences($this->user->id);

    // Add new channel
    UserNotificationChannel::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'channel_type' => 'slack',
        'channel_config' => [],
        'is_active' => true,
    ]);

    // Fresh query should see new channel
    $prefs2 = $this->cache->getUserPreferences($this->user);
    expect($prefs2)->toHaveLength(2);
});

it('caches organization channels', function () {
    \App\Models\NotificationChannelSetting::create([
        'organization_id' => $this->org->id,
        'channel_type' => 'slack',
        'is_enabled' => true,
        'global_config' => [],
    ]);

    $channels1 = $this->cache->getOrgChannels($this->org->id);
    expect($channels1)->toHaveLength(1);

    // Add another
    \App\Models\NotificationChannelSetting::create([
        'organization_id' => $this->org->id,
        'channel_type' => 'telegram',
        'is_enabled' => true,
        'global_config' => [],
    ]);

    // Cache should still show 1
    $channels2 = $this->cache->getOrgChannels($this->org->id);
    expect($channels2)->toHaveLength(1);

    // Invalidate and check
    $this->cache->invalidateOrgChannels($this->org->id);
    $channels3 = $this->cache->getOrgChannels($this->org->id);
    expect($channels3)->toHaveLength(2);
});

it('returns null for non-existent dispatch result', function () {
    $result = $this->cache->getDispatchResult($this->user, 'test');
    expect($result)->toBeNull();
});


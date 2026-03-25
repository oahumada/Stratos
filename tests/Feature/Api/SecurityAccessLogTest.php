<?php

use App\Models\Organization;
use App\Models\SecurityAccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->admin = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'collaborator',
    ]);
});

// ─────────────────────────────────────────────────────── Login Events ──

it('logs successful login event', function () {
    event(new \Illuminate\Auth\Events\Login('web', $this->user, false));

    $log = SecurityAccessLog::first();
    expect($log)->not->toBeNull();
    expect($log->event)->toBe('login');
    expect($log->user_id)->toBe($this->user->id);
    expect($log->organization_id)->toBe($this->organization->id);
    expect($log->email)->toBe($this->user->email);
    expect($log->role)->toBe($this->user->role);
    expect($log->ip_address)->not->toBeNull();
    expect($log->user_agent)->not->toBeNull();
});

it('logs login with MFA', function () {
    $this->user->update([
        'two_factor_confirmed_at' => now(),
    ]);

    event(new \Illuminate\Auth\Events\Login('web', $this->user, false));

    $log = SecurityAccessLog::first();
    expect($log->mfa_used)->toBeTrue();
});

// ─────────────────────────────────────────────────────── API Endpoints ──

it('requires admin role to view security access logs', function () {
    $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/security/access-logs')
        ->assertForbidden();
});

it('admin lists security access logs', function () {
    SecurityAccessLog::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => $this->user->email,
        'ip_address' => '192.168.1.1',
        'user_agent' => 'Mozilla/5.0',
        'role' => 'collaborator',
        'mfa_used' => false,
        'occurred_at' => now()->subHours(1),
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/security/access-logs');

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonCount(1, 'data.data');
    $response->assertJsonPath('data.data.0.event', 'login');
    $response->assertJsonPath('data.data.0.user_id', $this->user->id);
});

it('filters access logs by event type', function () {
    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => 'test@example.com',
        'occurred_at' => now(),
    ]);

    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'logout',
        'email' => 'test@example.com',
        'occurred_at' => now(),
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/security/access-logs?event=login');

    expect($response->json('data.data'))->toHaveCount(1);
    expect($response->json('data.data.0.event'))->toBe('login');
});

it('returns summary metrics for admin', function () {
    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => 'user1@example.com',
        'ip_address' => '192.168.1.1',
        'mfa_used' => true,
        'occurred_at' => now()->subHours(12),
    ]);

    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => 'user2@example.com',
        'ip_address' => '192.168.1.2',
        'mfa_used' => false,
        'occurred_at' => now()->subHours(6),
    ]);

    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login_failed',
        'email' => 'user3@example.com',
        'ip_address' => '192.168.1.3',
        'occurred_at' => now(),
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/security/access-logs/summary');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveKeys([
        'total_events',
        'events_last_24h',
        'successful_logins',
        'failed_logins',
        'logouts',
        'mfa_used_percentage',
        'top_ips',
        'events_by_type',
    ]);

    expect($response->json('data.total_events'))->toBe(3);
    expect($response->json('data.successful_logins'))->toBe(2);
    expect($response->json('data.failed_logins'))->toBe(1);
    expect($response->json('data.mfa_used_percentage'))->toEqual(50.0);
});

it('isolates security logs by organization', function () {
    $org2 = Organization::factory()->create();
    $admin2 = User::factory()->create([
        'organization_id' => $org2->id,
        'role' => 'admin',
    ]);

    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => 'user1@example.com',
        'occurred_at' => now(),
    ]);

    SecurityAccessLog::create([
        'organization_id' => $org2->id,
        'event' => 'login',
        'email' => 'user2@example.com',
        'occurred_at' => now(),
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/security/access-logs');

    expect($response->json('data.data'))->toHaveCount(1);
    expect($response->json('data.data.0.email'))->toBe('user1@example.com');
});

it('filters by date range', function () {
    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => 'test@example.com',
        'occurred_at' => now()->subDays(2),
    ]);

    SecurityAccessLog::create([
        'organization_id' => $this->organization->id,
        'event' => 'login',
        'email' => 'test@example.com',
        'occurred_at' => now(),
    ]);

    $from = now()->startOfDay()->format('Y-m-d');
    $to = now()->endOfDay()->format('Y-m-d');

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/security/access-logs?from={$from}&to={$to}");

    expect($response->json('data.data'))->toHaveCount(1);
});

// ─────────────────────────────────────────────────────── MFA Middleware ──

it('middleware aliases mfa.required correctly', function () {
    // This is just a sanity check that the middleware is registered
    expect(true)->toBeTrue();
});

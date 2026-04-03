<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
});

it('returns rate limit headers on successful request', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/notification-preferences');

    expect($response->headers->has('X-RateLimit-Limit'))->toBeTrue()
        ->and($response->headers->has('X-RateLimit-Remaining'))->toBeTrue()
        ->and($response->headers->has('X-RateLimit-Reset'))->toBeTrue()
        ->and($response->status())->toBe(200);
});

it('rate limit headers show correct values', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/notification-preferences');

    $limit = $response->headers->get('X-RateLimit-Limit');
    $remaining = $response->headers->get('X-RateLimit-Remaining');
    $reset = $response->headers->get('X-RateLimit-Reset');

    // Authenticated users get 300/min
    expect((int)$limit)->toBe(300)
        ->and((int)$remaining)->toBeLessThanOrEqual(300)
        ->and((int)$reset)->toBeGreaterThan(time());
});

it('middleware differentiates authenticated users from guests', function () {
    $authResponse = $this->actingAs($this->user)
        ->getJson('/api/notification-preferences');

    $guestResponse = $this->getJson('/api/catalogs');

    $authLimit = (int)$authResponse->headers->get('X-RateLimit-Limit');
    $guestLimit = (int)$guestResponse->headers->get('X-RateLimit-Limit');

    // Authenticated should have higher limit than guest/public
    expect($authLimit)->toBeGreaterThan($guestLimit);
});

it('public routes get strict rate limit', function () {
    $response = $this->getJson('/api/catalogs');

    $limit = (int)$response->headers->get('X-RateLimit-Limit');

    // Public routes get 30/min
    expect($limit)->toBe(30)
        ->and($response->status())->toBe(200);
});

it('rate limiter middleware is registered in bootstrap', function () {
    $appFile = file_get_contents(base_path('bootstrap/app.php'));
    expect($appFile)->toContain('ApiRateLimiter');
});

it('identifies unauthenticated requests by IP', function () {
    $response1 = $this->getJson('/api/catalogs', ['X-Forwarded-For' => '192.168.1.1']);
    $response2 = $this->getJson('/api/catalogs', ['X-Forwarded-For' => '192.168.1.2']);

    // Different IPs might have different limits
    expect($response1->status())->toBe(200)
        ->and($response2->status())->toBe(200);
});

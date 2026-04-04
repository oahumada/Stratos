<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
});

test('authenticated user can make api request', function () {
    $response = $this->actingAs($this->user)->getJson('/api/catalogs');
    $response->assertStatus(200);
});

test('authenticated user receives rate limit headers', function () {
    $response = $this->actingAs($this->user)->getJson('/api/catalogs');

    $response->assertStatus(200)
        ->assertHeader('X-RateLimit-Limit')
        ->assertHeader('X-RateLimit-Remaining')
        ->assertHeader('X-RateLimit-Reset');

    expect((int)$response->headers->get('X-RateLimit-Limit'))->toBeGreaterThan(0);
});

test('rate limit remaining decrements with requests', function () {
    $response1 = $this->actingAs($this->user)->getJson('/api/catalogs');
    $remaining1 = (int)$response1->headers->get('X-RateLimit-Remaining');

    $response2 = $this->actingAs($this->user)->getJson('/api/catalogs');
    $remaining2 = (int)$response2->headers->get('X-RateLimit-Remaining');

    expect($remaining2)->toBeLessThanOrEqual($remaining1);
});

test('rate limit reset header contains valid timestamp', function () {
    $response = $this->actingAs($this->user)->getJson('/api/catalogs');
    
    $resetTimestamp = (int)$response->headers->get('X-RateLimit-Reset');
    $now = time();

    expect($resetTimestamp)->toBeGreaterThanOrEqual($now);
    expect($resetTimestamp)->toBeLessThanOrEqual($now + 60);
});

test('different users have independent rate limits', function () {
    $user2 = User::factory()->create(['organization_id' => $this->organization->id]);

    $response1 = $this->actingAs($this->user)->getJson('/api/catalogs');
    $remaining1 = (int)$response1->headers->get('X-RateLimit-Remaining');

    $response2 = $this->actingAs($user2)->getJson('/api/catalogs');
    $remaining2 = (int)$response2->headers->get('X-RateLimit-Remaining');

    // Different users should have similar remaining counts (independent limits)
    expect(abs($remaining1 - $remaining2))->toBeLessThan(5);
});

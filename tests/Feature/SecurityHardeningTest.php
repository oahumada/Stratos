<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->org->id,
        'role' => 'admin',
    ]);
});

test('ai generation endpoint is throttled to 5 requests per minute', function () {
    RateLimiter::clear('ai_generation:'.$this->user->id);

    // First 5 requests should pass (not 429)
    for ($i = 0; $i < 5; $i++) {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/strategic-planning/scenarios/generate');

        expect($response->status())->not->toBe(429);
    }

    // 6th request should be throttled
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/strategic-planning/scenarios/generate');

    expect($response->status())->toBe(429);
});

test('ai analysis endpoint is throttled to 10 requests per minute', function () {
    // Note: The path is prefixed by 'strategic-planning' in api.php
    RateLimiter::clear('ai_analysis:'.$this->user->id);

    for ($i = 0; $i < 10; $i++) {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/strategic-planning/assessments/sessions/1/analyze');

        expect($response->status())->not->toBe(429);
    }

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/strategic-planning/assessments/sessions/1/analyze');

    expect($response->status())->toBe(429);
});

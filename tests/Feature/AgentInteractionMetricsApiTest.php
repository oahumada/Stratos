<?php

use App\Models\AgentInteraction;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->for($this->organization)->create();
});

it('requires authentication for metrics endpoint', function () {
    $this->getJson('/api/agent-interactions/metrics/summary')
        ->assertUnauthorized();
});

it('returns metrics summary', function () {
    for ($i = 0; $i < 5; $i++) {
        AgentInteraction::create([
            'agent_name' => 'Test Agent',
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
            'prompt_hash' => hash('sha256', "prompt$i"),
            'latency_ms' => 1000 + $i * 100,
            'status' => 'success',
        ]);
    }

    $response = $this->actingAs($this->user)
        ->getJson('/api/agent-interactions/metrics/summary');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'summary' => [
                'total_interactions',
                'total_succeeded',
                'success_rate',
            ],
        ],
    ]);

    expect($response->json('data.summary.total_interactions'))->toBe(5);
});

it('returns failing agents', function () {
    for ($i = 0; $i < 3; $i++) {
        AgentInteraction::create([
            'agent_name' => 'Failing Agent',
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'prompt_hash' => hash('sha256', "fail$i"),
            'latency_ms' => 5000,
            'status' => 'error',
            'error_message' => 'Error',
        ]);
    }

    $response = $this->actingAs($this->user)
        ->getJson('/api/agent-interactions/metrics/failing-agents');

    $response->assertSuccessful();
    expect($response->json('data.0.error_count'))->toBe(3);
});

it('returns latency by agent', function () {
    AgentInteraction::create([
        'agent_name' => 'Fast Agent',
        'organization_id' => $this->organization->id,
        'user_id' => $this->user->id,
        'prompt_hash' => hash('sha256', 'fast1'),
        'latency_ms' => 100,
        'status' => 'success',
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/agent-interactions/metrics/latency-by-agent');

    $response->assertSuccessful();
    expect($response->json('data.0.agent_name'))->toBe('Fast Agent');
});

it('isolates metrics by organization', function () {
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->for($org2)->create();

    AgentInteraction::create([
        'agent_name' => 'Agent',
        'organization_id' => $this->organization->id,
        'user_id' => $this->user->id,
        'prompt_hash' => hash('sha256', 'org1'),
        'latency_ms' => 1000,
        'status' => 'success',
    ]);

    AgentInteraction::create([
        'agent_name' => 'Agent',
        'organization_id' => $org2->id,
        'user_id' => $user2->id,
        'prompt_hash' => hash('sha256', 'org2'),
        'latency_ms' => 800,
        'status' => 'success',
    ]);

    $response1 = $this->actingAs($this->user)
        ->getJson('/api/agent-interactions/metrics/summary');

    $response2 = $this->actingAs($user2)
        ->getJson('/api/agent-interactions/metrics/summary');

    expect($response1->json('data.summary.total_interactions'))->toBe(1);
    expect($response2->json('data.summary.total_interactions'))->toBe(1);
});

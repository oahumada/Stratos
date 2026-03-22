<?php

use App\Models\AgentInteraction;
use App\Models\Organization;
use App\Models\User;
use App\Services\AgentInteractionMetricsService;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->for($this->organization)->create();
    $this->metricsService = new AgentInteractionMetricsService;
});

it('records successful agent interaction', function () {
    $interaction = AgentInteraction::create([
        'agent_name' => 'Stratos Impact Cortex',
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'prompt_hash' => hash('sha256', 'test prompt'),
        'latency_ms' => 1200,
        'token_count' => 350,
        'status' => 'success',
        'input_length' => 500,
        'output_length' => 800,
        'provider' => 'deepseek',
        'model' => 'deepseek-v2.5',
        'context' => '/api/scenarios',
    ]);

    expect($interaction->wasRecentlyCreated)->toBeTrue();
});

it('calculates organization metrics', function () {
    for ($i = 0; $i < 10; $i++) {
        AgentInteraction::create([
            'agent_name' => 'Test Agent',
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
            'prompt_hash' => hash('sha256', "prompt $i"),
            'latency_ms' => 1000 + $i * 100,
            'token_count' => 300 + $i * 10,
            'status' => 'success',
        ]);
    }

    $metrics = $this->metricsService->getOrganizationMetrics($this->organization->id);

    expect($metrics['summary']['total_interactions'])->toBe(10);
    expect($metrics['summary']['total_succeeded'])->toBe(10);
});

it('breaks down metrics by agent', function () {
    AgentInteraction::create([
        'agent_name' => 'Agent A',
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'prompt_hash' => hash('sha256', 'p1'),
        'latency_ms' => 1200,
        'status' => 'success',
    ]);

    AgentInteraction::create([
        'agent_name' => 'Agent B',
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
        'prompt_hash' => hash('sha256', 'p2'),
        'latency_ms' => 800,
        'status' => 'success',
    ]);

    $metrics = $this->metricsService->getOrganizationMetrics($this->organization->id);

    expect($metrics['by_agent'])->toHaveCount(2);
});

it('returns top failing agents', function () {
    for ($i = 0; $i < 5; $i++) {
        AgentInteraction::create([
            'agent_name' => 'Failing Agent',
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
            'prompt_hash' => hash('sha256', "fail$i"),
            'latency_ms' => 5000,
            'status' => 'error',
            'error_message' => 'API Error',
        ]);
    }

    $failing = $this->metricsService->getTopFailingAgents($this->organization->id);

    expect($failing)->toHaveCount(1);
    expect($failing[0]['error_count'])->toBe(5);
});

it('calculates latency percentiles', function () {
    for ($i = 1; $i <= 100; $i++) {
        AgentInteraction::create([
            'agent_name' => 'Agent',
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
            'prompt_hash' => hash('sha256', "latency$i"),
            'latency_ms' => $i * 10,
            'status' => 'success',
        ]);
    }

    $metrics = $this->metricsService->getOrganizationMetrics($this->organization->id);
    $percentiles = $metrics['latency_percentiles'];

    expect($percentiles['p50'])->toBeGreaterThan(0);
    expect($percentiles['p95'])->toBeGreaterThanOrEqual($percentiles['p50']);
    expect($percentiles['p99'])->toBeGreaterThanOrEqual($percentiles['p95']);
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

    $metrics1 = $this->metricsService->getOrganizationMetrics($this->organization->id);
    $metrics2 = $this->metricsService->getOrganizationMetrics($org2->id);

    expect($metrics1['summary']['total_interactions'])->toBe(1);
    expect($metrics2['summary']['total_interactions'])->toBe(1);
});

it('returns empty metrics when no interactions', function () {
    $metrics = $this->metricsService->getOrganizationMetrics($this->organization->id);

    expect($metrics['summary']['total_interactions'])->toBe(0);
    expect($metrics['summary']['success_rate'])->toBe(0.0);
});

<?php

use App\Models\Organizations;
use App\Models\Scenario;
use App\Models\User;
use App\Services\AiOrchestratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Reestructuración Q2',
    ]);

    // Mock the AI Orchestrator
    $mockOrchestrator = Mockery::mock(AiOrchestratorService::class);
    $mockOrchestrator->shouldReceive('agentThink')
        ->andReturn([
            'response' => [
                'actions' => [
                    'Implementar sesiones de team building cross-funcional',
                    'Rotar líderes de squad durante 2 semanas',
                    'Crear canal de comunicación directa entre equipos afectados',
                ],
                'training' => 'Workshop de Inteligencia Emocional para el Squad de Ingeniería',
                'security_insight' => 'El plan no genera sesgo de género ni edad. Validación ética aprobada.',
            ],
        ]);
    $this->app->instance(AiOrchestratorService::class, $mockOrchestrator);
});

it('generates a mitigation plan via Stratos Sentinel', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/mitigate", [
            'metrics' => [
                'cultural_friction' => 35,
                'success_probability' => 62,
                'synergy_score' => 5.2,
            ],
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'plan',
    ]);
    $response->assertJson(['success' => true]);
});

it('returns a plan with correct structure when metrics are provided', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/mitigate", [
            'metrics' => [
                'cultural_friction' => 20,
                'success_probability' => 80,
                'synergy_score' => 7.5,
            ],
        ]);

    $response->assertStatus(200);
    $plan = $response->json('plan');
    expect($plan)->toHaveKeys(['actions', 'training', 'security_insight']);
    expect($plan['actions'])->toBeArray()->toHaveCount(3);
});

it('uses default metrics when none are provided', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/mitigate");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

it('returns error for non-existent scenario', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenarios/99999/mitigate', [
            'metrics' => [
                'cultural_friction' => 10,
                'success_probability' => 90,
                'synergy_score' => 9,
            ],
        ]);

    $response->assertStatus(500);
});

it('returns plan with actions array', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/mitigate", [
            'metrics' => [
                'cultural_friction' => 25,
                'success_probability' => 70,
                'synergy_score' => 6,
            ],
        ]);

    $response->assertStatus(200);
    $actions = $response->json('plan.actions');
    expect($actions)->toBeArray();
    expect(count($actions))->toBeGreaterThan(0);
});

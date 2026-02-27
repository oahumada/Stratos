<?php

use App\Models\Organizations;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Reestructuración Q2',
    ]);
});

it('generates a mitigation plan via Stratos Sentinel', function () {
    // Mock the AI Orchestrator response (DeepSeek/OpenAI)
    Http::fake([
        '*' => Http::response([
            'response' => [
                'actions' => [
                    'Implementar sesiones de team building cross-funcional',
                    'Rotar líderes de squad durante 2 semanas',
                    'Crear canal de comunicación directa entre equipos afectados',
                ],
                'training' => 'Workshop de Inteligencia Emocional para el Squad de Ingeniería',
                'security_insight' => 'El plan no genera sesgo de género ni edad. Validación ética aprobada.',
            ],
        ], 200),
    ]);

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
    Http::fake([
        '*' => Http::response([
            'response' => [
                'actions' => ['Acción 1', 'Acción 2', 'Acción 3'],
                'training' => 'Capacitación recomendada',
                'security_insight' => 'Sin riesgos éticos detectados',
            ],
        ], 200),
    ]);

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
    Http::fake([
        '*' => Http::response([
            'response' => [
                'actions' => ['Default action'],
                'training' => 'Default training',
                'security_insight' => 'OK',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/mitigate");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

it('returns 404 for non-existent scenario', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenarios/99999/mitigate', [
            'metrics' => [
                'cultural_friction' => 10,
                'success_probability' => 90,
                'synergy_score' => 9,
            ],
        ]);

    $response->assertStatus(500); // findOrFail throws ModelNotFoundException → caught as 500
});

it('logs the mitigation plan in the audit trail', function () {
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('channel')->with('ai_audit')->atLeast()->once()->andReturnSelf();

    Http::fake([
        '*' => Http::response([
            'response' => [
                'actions' => ['Acción auditada'],
                'training' => 'Training auditado',
                'security_insight' => 'Insight auditado',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/mitigate", [
            'metrics' => [
                'cultural_friction' => 25,
                'success_probability' => 70,
                'synergy_score' => 6,
            ],
        ]);

    $response->assertStatus(200);
});

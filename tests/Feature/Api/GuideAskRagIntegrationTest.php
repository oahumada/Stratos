<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\User;
use App\Services\AiOrchestratorService;
use App\Services\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->person = People::factory()->create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
    ]);

    // Mock RagService to evitar llamadas reales al LLM
    $ragMock = Mockery::mock(RagService::class);
    $ragMock->shouldReceive('ask')
        ->once()
        ->andReturn([
            'success' => true,
            'question' => '¿Cómo funciona Stratos Guide?',
            'answer' => 'Respuesta desde RAG',
            'sources' => [],
            'confidence' => 0.9,
            'context_count' => 1,
        ]);
    $this->app->instance(RagService::class, $ragMock);

    // Aseguramos que el orquestador legacy no se use cuando RAG tiene contexto
    $orchestratorMock = Mockery::mock(AiOrchestratorService::class);
    $orchestratorMock->shouldReceive('agentThink')->never();
    $this->app->instance(AiOrchestratorService::class, $orchestratorMock);
});

it('uses RagService for guide questions when RAG context is available', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/guide/ask', [
            'question' => '¿Cómo funciona Stratos Guide?',
            'module' => 'scenario_planning',
        ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.answer', 'Respuesta desde RAG');

    $response->assertJsonStructure([
        'success',
        'data' => [
            'answer',
            'next_action',
            'related_module',
            'rag' => [
                'confidence',
                'sources',
            ],
        ],
    ]);
});

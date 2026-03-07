<?php

use App\Models\LLMEvaluation;
use App\Models\Organization;
use App\Models\User;
use App\Services\RAGASEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->evaluator = app(RAGASEvaluator::class);
});

describe('RAGAS LLM Evaluation API', function () {
    it('creates a new evaluation and queues it', function () {
        Queue::fake();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/qa/llm-evaluations', [
                'input_prompt' => 'Generate workforce planning scenarios',
                'output_content' => 'This is a generated scenario...',
                'context' => 'Company wants to adopt AI in 12 months',
                'llm_provider' => 'deepseek',
            ]);

        $response->assertStatus(202);
        $response->assertJsonStructure([
            'success',
            'data' => ['id', 'status', 'created_at'],
            'message',
        ]);

        // Verify evaluation was created in database
        $this->assertDatabaseHas('llm_evaluations', [
            'llm_provider' => 'deepseek',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'pending',
        ]);

        // Verify job was dispatched
        Queue::assertPushed(\App\Jobs\EvaluateLLMGeneration::class);
    });

    it('retrieves evaluation status with metrics when completed', function () {
        // Mock completed evaluation
        $evaluation = LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'composite_score' => 0.87,
            'normalized_score' => 0.85,
            'quality_level' => 'good',
            'faithfulness_score' => 0.88,
            'relevance_score' => 0.85,
            'context_alignment_score' => 0.89,
            'coherence_score' => 0.82,
            'hallucination_rate' => 0.08,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/qa/llm-evaluations/{$evaluation->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', 'completed');
        $response->assertJsonPath('data.metrics.composite_score', 0.87);
        $response->assertJsonPath('data.metrics.quality_level', 'good');
        $response->assertJsonStructure([
            'data' => [
                'metrics' => [
                    'individual_scores' => [
                        'faithfulness',
                        'relevance',
                        'context_alignment',
                        'coherence',
                        'hallucination_rate',
                    ],
                ],
            ],
        ]);
    });

    it('returns 404 for non-existent evaluation', function () {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations/999');

        $response->assertStatus(404);
    });

    it('enforces multi-tenant isolation', function () {
        $otherOrg = Organization::factory()->create();
        $otherUser = User::factory()->create(['organization_id' => $otherOrg->id]);

        $evaluation = LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Try to view evaluation from different organization
        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/qa/llm-evaluations/{$evaluation->id}");

        $response->assertStatus(403); // Forbidden
    });

    it('lists evaluations with pagination', function () {
        LLMEvaluation::factory()->count(25)->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations?page=1&per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.total', 25);
        $response->assertJsonPath('meta.current_page', 1);
    });

    it('filters evaluations by provider', function () {
        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'llm_provider' => 'deepseek',
            'status' => 'completed',
        ]);

        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'llm_provider' => 'openai',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations?provider=deepseek');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.llm_provider', 'deepseek');
    });

    it('filters evaluations by status', function () {
        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
        ]);

        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations?status=completed');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.status', 'completed');
    });

    it('filters evaluations by quality level', function () {
        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'quality_level' => 'excellent',
        ]);

        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'quality_level' => 'poor',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations?quality_level=excellent');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.quality_level', 'excellent');
    });

    it('returns organization metrics', function () {
        LLMEvaluation::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'composite_score' => 0.85,
            'quality_level' => 'good',
            'llm_provider' => 'deepseek',
        ]);

        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'composite_score' => 0.92,
            'quality_level' => 'excellent',
            'llm_provider' => 'openai',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations/metrics/summary');

        $response->assertStatus(200);
        $response->assertJsonPath('data.total_evaluations', 4);
        $response->assertJsonPath('data.quality_distribution.good', 3);
        $response->assertJsonPath('data.quality_distribution.excellent', 1);
        $response->assertJsonStructure([
            'data' => [
                'total_evaluations',
                'avg_composite_score',
                'max_composite_score',
                'min_composite_score',
                'quality_distribution',
                'provider_distribution',
            ],
        ]);
    });

    it('returns metrics filtered by provider', function () {
        LLMEvaluation::factory()->count(2)->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'llm_provider' => 'deepseek',
        ]);

        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'llm_provider' => 'openai',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/qa/llm-evaluations/metrics/summary?provider=deepseek');

        $response->assertStatus(200);
        $response->assertJsonPath('data.total_evaluations', 2);
    });

    it('requires authentication', function () {
        $response = $this->postJson('/api/qa/llm-evaluations', [
            'input_prompt' => 'test',
            'output_content' => 'test',
        ]);

        $response->assertStatus(401);
    });

    it('validates required fields on creation', function () {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/qa/llm-evaluations', [
                'input_prompt' => 'only prompt',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['output_content']);
    });

    it('validates provider enum', function () {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/qa/llm-evaluations', [
                'input_prompt' => 'test',
                'output_content' => 'test',
                'llm_provider' => 'invalid-provider',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['llm_provider']);
    });
});

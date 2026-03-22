<?php

use App\Models\LLMEvaluation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
});

describe('RAG Ask Endpoint', function () {
    it('requires authentication', function () {
        $response = $this->postJson('/api/rag/ask', [
            'question' => 'What is the quality of LLM outputs?',
        ]);

        $response->assertStatus(401);
    });

    it('validates question is required', function () {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question']);
    });

    it('validates question minimum length', function () {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'ab',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question']);
    });

    it('returns answer with no relevant documents', function () {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'What is the meaning of life?',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('question', 'What is the meaning of life?');
        $response->assertJsonPath('sources', []);
        $confidence = $response->json('confidence');
        expect($confidence == 0)->toBeTrue();
    });

    it('retrieves and returns relevant evaluations', function () {
        // Create evaluations with context
        $eval1 = LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'The evaluation shows high quality metrics with excellent faithfulness scores.',
            'context_content' => 'Context about quality assessment and metrics.',
            'quality_level' => 'excellent',
            'llm_provider' => 'deepseek',
        ]);

        $eval2 = LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'Performance analysis indicates hallucination rate below threshold.',
            'context_content' => 'Context about hallucination detection.',
            'quality_level' => 'good',
            'llm_provider' => 'openai',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'What about quality evaluation metrics?',
                'context_type' => 'evaluations',
                'max_sources' => 5,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('question', 'What about quality evaluation metrics?');
        $response->assertJsonStructure([
            'success',
            'question',
            'answer',
            'sources',
            'confidence',
            'context_count',
        ]);

        // Verify sources are returned
        $response->assertJsonPath('context_count', 2);
        $sources = $response->json('sources');
        expect($sources)->toHaveCount(2);
        expect($sources[0])->toHaveKeys(['id', 'type', 'relevance_score', 'provider']);
    });

    it('respects organization isolation', function () {
        $otherOrg = Organization::factory()->create();
        $otherUser = User::factory()->create(['organization_id' => $otherOrg->id]);

        // Create evaluation in original organization
        $eval = LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'Secret quality metrics data.',
            'context_content' => 'Secret context.',
        ]);

        // Other user should not see this data
        $response = $this->actingAs($otherUser, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'Secret quality metrics',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('context_count', 0);
    });

    it('filters by context_type', function () {
        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'Evaluation data.',
            'context_content' => 'For filtering test.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'Test filtering by context',
                'context_type' => 'evaluations',
                'max_sources' => 10,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    });

    it('respects max_sources limit', function () {
        LLMEvaluation::factory()->count(10)->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'Multiple evaluation data.',
            'context_content' => 'Test data.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'Get limited sources',
                'max_sources' => 3,
            ]);

        $response->assertStatus(200);
        expect($response->json('context_count'))->toBeLessThanOrEqual(3);
    });

    it('includes metadata when requested', function () {
        $eval = LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'Sample output.',
            'quality_level' => 'good',
            'llm_provider' => 'deepseek',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'Sample question for metadata',
                'include_metadata' => true,
            ]);

        $response->assertStatus(200);
        $sources = $response->json('sources');
        if (count($sources) > 0) {
            expect($sources[0])->toHaveKeys(['quality_level', 'provider']);
        }
    });

    it('returns confidence score', function () {
        LLMEvaluation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'completed',
            'output_content' => 'High quality output with excellent metrics.',
            'quality_level' => 'excellent',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/rag/ask', [
                'question' => 'High quality excellent metrics',
            ]);

        $response->assertStatus(200);
        $confidence = $response->json('confidence');
        expect($confidence)->toBeGreaterThanOrEqual(0.0);
        expect($confidence)->toBeLessThanOrEqual(1.0);
    });
});

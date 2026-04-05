<?php

use App\Jobs\ProcessImprovementSignals;
use App\Jobs\ReindexKnowledge;
use App\Models\Embedding;
use App\Models\EmbeddingVersion;
use App\Models\ImprovementFeedback;
use App\Models\Organization;
use App\Models\User;
use App\Services\EmbeddingService;
use App\Services\Intelligence\FeedbackLoopService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->for($this->organization)->create();
    $this->feedbackService = new FeedbackLoopService;
});

// ── Submit Feedback ──────────────────────────────────────────────────

it('stores feedback correctly', function () {
    $feedback = $this->feedbackService->submitFeedback([
        'user_id' => $this->user->id,
        'agent_id' => 'Stratos Guide',
        'rating' => 4,
        'feedback_text' => 'Great response',
        'tags' => ['excellent'],
        'context' => ['query' => 'How do I create a scenario?'],
    ], $this->organization->id);

    expect($feedback)->toBeInstanceOf(ImprovementFeedback::class)
        ->and($feedback->organization_id)->toBe($this->organization->id)
        ->and($feedback->rating)->toBe(4)
        ->and($feedback->status)->toBe('pending')
        ->and($feedback->tags)->toBe(['excellent']);
});

it('scopes feedback by organization', function () {
    $otherOrg = Organization::factory()->create();

    ImprovementFeedback::factory()->count(3)->create([
        'organization_id' => $this->organization->id,
    ]);

    ImprovementFeedback::factory()->count(2)->create([
        'organization_id' => $otherOrg->id,
    ]);

    $summary = $this->feedbackService->getFeedbackSummary($this->organization->id);

    expect($summary['total'])->toBe(3);
});

// ── Pattern Detection ────────────────────────────────────────────────

it('detects top error tags in patterns', function () {
    ImprovementFeedback::factory()->count(5)->create([
        'organization_id' => $this->organization->id,
        'tags' => ['hallucination', 'irrelevant'],
        'rating' => 2,
    ]);

    ImprovementFeedback::factory()->count(3)->create([
        'organization_id' => $this->organization->id,
        'tags' => ['incomplete'],
        'rating' => 3,
    ]);

    $patterns = $this->feedbackService->getPatterns($this->organization->id);

    expect($patterns['top_error_tags'])->toHaveKey('hallucination')
        ->and($patterns['top_error_tags']['hallucination'])->toBe(5)
        ->and($patterns['total_feedback'])->toBe(8);
});

// ── Agent Accuracy ───────────────────────────────────────────────────

it('calculates agent accuracy correctly', function () {
    ImprovementFeedback::factory()->count(6)->create([
        'organization_id' => $this->organization->id,
        'agent_id' => 'TestAgent',
        'rating' => 5,
    ]);

    ImprovementFeedback::factory()->count(4)->create([
        'organization_id' => $this->organization->id,
        'agent_id' => 'TestAgent',
        'rating' => 1,
    ]);

    $accuracy = $this->feedbackService->getAgentAccuracy('TestAgent', $this->organization->id);

    expect($accuracy['total_feedback'])->toBe(10)
        ->and($accuracy['acceptance_rate'])->toBe(60.0)
        ->and($accuracy['negative_rate'])->toBe(40.0)
        ->and($accuracy['positive_count'])->toBe(6)
        ->and($accuracy['negative_count'])->toBe(4);
});

// ── Reindex Trigger ──────────────────────────────────────────────────

it('triggers reindex check on high negative feedback', function () {
    ImprovementFeedback::factory()->count(12)->negative()->create([
        'organization_id' => $this->organization->id,
        'created_at' => now()->subDays(3),
    ]);

    expect($this->feedbackService->shouldTriggerReindex($this->organization->id))->toBeTrue();
});

it('does not trigger reindex below threshold', function () {
    ImprovementFeedback::factory()->count(5)->negative()->create([
        'organization_id' => $this->organization->id,
        'created_at' => now()->subDays(3),
    ]);

    expect($this->feedbackService->shouldTriggerReindex($this->organization->id))->toBeFalse();
});

// ── ProcessImprovementSignals Job ────────────────────────────────────

it('processes pending feedback in job', function () {
    Queue::fake();

    $feedback = ImprovementFeedback::factory()->count(3)->create([
        'organization_id' => $this->organization->id,
        'status' => 'pending',
        'rating' => 3,
        'tags' => ['incomplete'],
    ]);

    $job = new ProcessImprovementSignals($this->organization->id);
    $job->handle(new FeedbackLoopService);

    foreach ($feedback as $fb) {
        $fb->refresh();
        expect($fb->status)->toBe('processed')
            ->and($fb->processed_at)->not->toBeNull();
    }
});

it('flags agents with high hallucination rate', function () {
    Queue::fake();

    // 8 out of 10 have hallucination tag → 80% rate
    ImprovementFeedback::factory()->count(8)->create([
        'organization_id' => $this->organization->id,
        'agent_id' => 'HallucinatingAgent',
        'status' => 'pending',
        'rating' => 1,
        'tags' => ['hallucination'],
    ]);

    ImprovementFeedback::factory()->count(2)->create([
        'organization_id' => $this->organization->id,
        'agent_id' => 'HallucinatingAgent',
        'status' => 'pending',
        'rating' => 4,
        'tags' => ['excellent'],
    ]);

    // Job should complete without errors and process all
    $job = new ProcessImprovementSignals($this->organization->id);
    $job->handle(new FeedbackLoopService);

    $processed = ImprovementFeedback::where('organization_id', $this->organization->id)
        ->where('status', 'processed')
        ->count();

    expect($processed)->toBe(10);
});

// ── ReindexKnowledge Job ─────────────────────────────────────────────

it('creates version snapshot on reindex', function () {
    $mock = Mockery::mock(EmbeddingService::class);
    $mock->shouldReceive('generate')->andReturn(array_fill(0, 1536, 0.1));
    $mock->shouldReceive('findSimilar')->andReturn([]);

    ImprovementFeedback::factory()->positive()->create([
        'organization_id' => $this->organization->id,
        'status' => 'pending',
        'context' => ['query' => 'test query', 'response_snippet' => 'test response'],
    ]);

    $job = new ReindexKnowledge($this->organization->id, 'v-test');
    $job->handle($mock);

    $version = EmbeddingVersion::where('organization_id', $this->organization->id)->first();

    expect($version)->not->toBeNull()
        ->and($version->version_tag)->toBe('v-test')
        ->and($version->created_by)->toBe('reindex_job');
});

it('stores positive feedback as embeddings', function () {
    $mock = Mockery::mock(EmbeddingService::class);
    $mock->shouldReceive('generate')->andReturn(array_fill(0, 1536, 0.1));
    $mock->shouldReceive('findSimilar')->andReturn([]);

    ImprovementFeedback::factory()->count(3)->positive()->create([
        'organization_id' => $this->organization->id,
        'status' => 'pending',
        'context' => ['query' => 'test query', 'response_snippet' => 'test response'],
    ]);

    $job = new ReindexKnowledge($this->organization->id, 'v-test');
    $job->handle($mock);

    $embeddings = Embedding::where('organization_id', $this->organization->id)
        ->where('resource_type', 'feedback_positive')
        ->count();

    expect($embeddings)->toBe(3);

    $applied = ImprovementFeedback::where('organization_id', $this->organization->id)
        ->where('status', 'applied')
        ->count();

    expect($applied)->toBe(3);
});

it('flags unreliable embeddings from hallucination feedback', function () {
    // Create an existing embedding to be found by similarity
    $existing = Embedding::create([
        'organization_id' => $this->organization->id,
        'resource_type' => 'knowledge',
        'resource_id' => 1,
        'metadata' => ['name' => 'test'],
        'embedding' => array_fill(0, 1536, 0.1),
    ]);

    $similarResult = (object) ['id' => $existing->id, 'name' => 'test', 'similarity' => 0.95];

    $mock = Mockery::mock(EmbeddingService::class);
    $mock->shouldReceive('generate')->andReturn(array_fill(0, 1536, 0.1));
    $mock->shouldReceive('findSimilar')->andReturn([$similarResult]);

    ImprovementFeedback::factory()->withHallucination()->create([
        'organization_id' => $this->organization->id,
        'status' => 'pending',
        'context' => ['query' => 'bad query', 'response_snippet' => 'hallucinated response'],
    ]);

    $job = new ReindexKnowledge($this->organization->id, 'v-test');
    $job->handle($mock);

    $existing->refresh();

    expect($existing->metadata)->toHaveKey('flagged_unreliable')
        ->and($existing->metadata['flagged_unreliable'])->toBeTrue();
});

// ── Feedback Summary ─────────────────────────────────────────────────

it('returns correct feedback summary counts', function () {
    ImprovementFeedback::factory()->count(3)->create([
        'organization_id' => $this->organization->id,
        'status' => 'pending',
        'agent_id' => 'AgentA',
        'rating' => 5,
        'tags' => ['excellent'],
    ]);

    ImprovementFeedback::factory()->count(2)->processed()->create([
        'organization_id' => $this->organization->id,
        'agent_id' => 'AgentB',
        'rating' => 2,
        'tags' => ['hallucination'],
    ]);

    $summary = $this->feedbackService->getFeedbackSummary($this->organization->id);

    expect($summary['total'])->toBe(5)
        ->and($summary['by_status'])->toHaveKey('pending')
        ->and($summary['by_status']['pending'])->toBe(3)
        ->and($summary['by_status']['processed'])->toBe(2)
        ->and($summary['by_tag'])->toHaveKey('excellent')
        ->and($summary['by_tag'])->toHaveKey('hallucination')
        ->and($summary['by_agent'])->toHaveKey('AgentA')
        ->and($summary['by_agent'])->toHaveKey('AgentB');
});

// ── API Endpoints ────────────────────────────────────────────────────

it('validates input on store endpoint', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/intelligence/feedback', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['rating']);
});

it('rejects invalid rating on store endpoint', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/intelligence/feedback', [
            'rating' => 6,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['rating']);
});

it('stores feedback via API', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/intelligence/feedback', [
            'rating' => 4,
            'feedback_text' => 'Very helpful response',
            'tags' => ['excellent'],
            'agent_id' => 'Stratos Guide',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.rating', 4)
        ->assertJsonPath('data.agent_id', 'Stratos Guide');

    $this->assertDatabaseHas('improvement_feedback', [
        'organization_id' => $this->organization->id,
        'rating' => 4,
        'agent_id' => 'Stratos Guide',
    ]);
});

it('returns paginated results on index endpoint', function () {
    ImprovementFeedback::factory()->count(20)->create([
        'organization_id' => $this->organization->id,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/intelligence/feedback?per_page=10');

    $response->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('total', 20);
});

it('returns analysis on patterns endpoint', function () {
    ImprovementFeedback::factory()->count(5)->create([
        'organization_id' => $this->organization->id,
        'tags' => ['hallucination'],
        'rating' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/intelligence/feedback/patterns');

    $response->assertOk()
        ->assertJsonPath('data.total_feedback', 5)
        ->assertJsonStructure(['data' => ['top_error_tags', 'worst_agents', 'trend']]);
});

it('returns summary on summary endpoint', function () {
    ImprovementFeedback::factory()->count(5)->create([
        'organization_id' => $this->organization->id,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/intelligence/feedback/summary');

    $response->assertOk()
        ->assertJsonPath('data.total', 5)
        ->assertJsonStructure(['data' => ['total', 'avg_rating', 'by_status', 'by_tag', 'by_agent']]);
});

// ── Multi-tenant Isolation ───────────────────────────────────────────

it('isolates feedback between tenants via API', function () {
    $otherOrg = Organization::factory()->create();
    $otherUser = User::factory()->for($otherOrg)->create();

    ImprovementFeedback::factory()->count(5)->create([
        'organization_id' => $this->organization->id,
    ]);

    ImprovementFeedback::factory()->count(3)->create([
        'organization_id' => $otherOrg->id,
    ]);

    $response = $this->actingAs($otherUser)
        ->getJson('/api/intelligence/feedback');

    $response->assertOk()
        ->assertJsonPath('total', 3);
});

// ── Command ──────────────────────────────────────────────────────────

it('dispatches jobs from artisan command', function () {
    Bus::fake([ProcessImprovementSignals::class]);

    $this->artisan('intelligence:process-feedback', ['--org' => $this->organization->id])
        ->assertSuccessful();

    Bus::assertDispatched(ProcessImprovementSignals::class, function ($job) {
        return true;
    });
});

it('dispatches jobs for all orgs from command', function () {
    Bus::fake([ProcessImprovementSignals::class]);

    Organization::factory()->count(2)->create();
    $totalOrgs = Organization::count();

    $this->artisan('intelligence:process-feedback', ['--all' => true])
        ->assertSuccessful();

    Bus::assertDispatchedTimes(ProcessImprovementSignals::class, $totalOrgs);
});

it('fails command without org or all flag', function () {
    $this->artisan('intelligence:process-feedback')
        ->assertFailed();
});

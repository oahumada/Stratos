<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\PsychometricProfile;
use App\Models\PulseResponse;
use App\Models\User;
use App\Services\AiOrchestratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);

    // Mock the AI Orchestrator to avoid needing real Agent records and LLM calls
    $mockOrchestrator = Mockery::mock(AiOrchestratorService::class);
    $mockOrchestrator->shouldReceive('agentThink')
        ->andReturn([
            'response' => [
                'diagnosis' => 'La organización muestra señales saludables.',
                'ceo_actions' => [
                    'Mantener programas de bienestar',
                    'Reforzar comunicación interna',
                    'Celebrar logros del trimestre',
                ],
                'critical_node' => 'Ninguno identificado',
            ],
        ]);
    $this->app->instance(AiOrchestratorService::class, $mockOrchestrator);
});

it('returns health scan results with correct structure', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    PulseResponse::factory()->count(10)->create([
        'people_id' => $person->id,
        'sentiment_score' => 72,
        'created_at' => now()->subDays(5),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'organization_id',
            'scan_timestamp',
            'signals',
            'anomalies',
            'ai_analysis',
            'health_score',
        ],
    ]);
});

it('detects low sentiment anomaly when average is below 50', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    PulseResponse::factory()->count(10)->create([
        'people_id' => $person->id,
        'sentiment_score' => 30,
        'created_at' => now()->subDays(5),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $response->assertStatus(200);

    $anomalies = $response->json('data.anomalies');
    $types = collect($anomalies)->pluck('type')->toArray();
    expect($types)->toContain('low_sentiment');
});

it('detects low participation anomaly when pulse count is below 5', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    PulseResponse::factory()->count(2)->create([
        'people_id' => $person->id,
        'sentiment_score' => 80,
        'created_at' => now()->subDays(3),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $response->assertStatus(200);

    $anomalies = $response->json('data.anomalies');
    $types = collect($anomalies)->pluck('type')->toArray();
    expect($types)->toContain('low_participation');
});

it('calculates health score within valid range 0-100', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    PulseResponse::factory()->count(25)->create([
        'people_id' => $person->id,
        'sentiment_score' => 85,
        'created_at' => now()->subDays(7),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $healthScore = $response->json('data.health_score');
    expect($healthScore)->toBeGreaterThanOrEqual(0);
    expect($healthScore)->toBeLessThanOrEqual(100);
});

it('includes psychometric profiles in signals', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    PsychometricProfile::factory()->create([
        'people_id' => $person->id,
        'trait_name' => 'Dominance',
        'score' => 0.75,
        'created_at' => now()->subDays(10),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $response->assertStatus(200);
    $signals = $response->json('data.signals');
    expect($signals['profiles_analyzed'])->toBeGreaterThanOrEqual(1);
});

it('returns correct organization_id in result', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $response->assertStatus(200);
    expect($response->json('data.organization_id'))->toBe($this->org->id);
});

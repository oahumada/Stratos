<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\PsychometricProfile;
use App\Models\PulseResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
});

it('returns health scan results with correct structure', function () {
    // Create people with pulse responses for the org
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    // Create pulse responses with sentiment
    PulseResponse::factory()->count(10)->create([
        'people_id' => $person->id,
        'sentiment_score' => 72,
        'created_at' => now()->subDays(5),
    ]);

    // Mock the AI Sentinel response
    Http::fake([
        '*' => Http::response([
            'response' => [
                'diagnosis' => 'La organización muestra señales saludables.',
                'ceo_actions' => [
                    'Mantener programas de bienestar',
                    'Reforzar comunicación interna',
                    'Celebrar logros del trimestre',
                ],
                'critical_node' => 'Ninguno identificado',
            ],
        ], 200),
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
        'sentiment_score' => 30, // Low sentiment
        'created_at' => now()->subDays(5),
    ]);

    Http::fake([
        '*' => Http::response([
            'response' => [
                'diagnosis' => 'Alerta: sentimiento bajo.',
                'ceo_actions' => ['Investigar causas'],
                'critical_node' => 'Área de Ingeniería',
            ],
        ], 200),
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

    // Only 2 responses (below threshold of 5)
    PulseResponse::factory()->count(2)->create([
        'people_id' => $person->id,
        'sentiment_score' => 80,
        'created_at' => now()->subDays(3),
    ]);

    Http::fake([
        '*' => Http::response([
            'response' => [
                'diagnosis' => 'Participación insuficiente.',
                'ceo_actions' => ['Enviar recordatorios'],
                'critical_node' => 'N/A',
            ],
        ], 200),
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

    Http::fake([
        '*' => Http::response([
            'response' => [
                'diagnosis' => 'Excelente.',
                'ceo_actions' => ['Mantener el rumbo'],
                'critical_node' => 'Ninguno',
            ],
        ], 200),
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

    Http::fake([
        '*' => Http::response([
            'response' => [
                'diagnosis' => 'OK',
                'ceo_actions' => ['Nada urgente'],
                'critical_node' => 'N/A',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $signals = $response->json('data.signals');
    expect($signals['profiles_analyzed'])->toBeGreaterThanOrEqual(1);
});

it('logs the health scan via audit trail service', function () {
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('channel')->with('ai_audit')->atLeast()->once()->andReturnSelf();

    Http::fake([
        '*' => Http::response([
            'response' => [
                'diagnosis' => 'Auditado.',
                'ceo_actions' => ['Acción auditada'],
                'critical_node' => 'Auditado',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/pulse/health-scan');

    $response->assertStatus(200);
});

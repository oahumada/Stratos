<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\ScenarioGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();

    $this->admin = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);

    $this->generation = ScenarioGeneration::create([
        'organization_id' => $this->organization->id,
        'created_by' => $this->admin->id,
        'status' => 'complete',
        'prompt' => 'Prompt de prueba',
        'llm_response' => [
            'scenario_metadata' => [
                'name' => 'Escenario AutoAccept Test',
                'description' => 'Escenario para probar feature flag de auto-accept',
                'scenario_type' => 'transformation',
                'horizon_months' => 12,
            ],
            'capabilities' => [],
        ],
        'metadata' => [],
        'generated_at' => now(),
        'redacted' => true,
    ]);
});

it('blocks auto-accept when feature flag is disabled', function () {
    config()->set('features.auto_accept_import_generation', false);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson("/api/strategic-planning/scenarios/generate/{$this->generation->id}/accept", [
            'auto_accept' => true,
            'import' => false,
        ]);

    $response->assertForbidden();
    $response->assertJsonPath('message', 'Auto-accept feature disabled');

    expect(Scenario::query()->where('source_generation_id', $this->generation->id)->exists())->toBeFalse();

    $this->generation->refresh();
    $audit = $this->generation->metadata['auto_accept_audit'] ?? [];

    expect($audit)->not->toBeEmpty();
    expect($audit[0]['result'] ?? null)->toBe('skipped_feature_flag');
});

it('allows auto-accept when feature flag is enabled', function () {
    config()->set('features.auto_accept_import_generation', true);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson("/api/strategic-planning/scenarios/generate/{$this->generation->id}/accept", [
            'auto_accept' => true,
            'import' => false,
        ]);

    $response->assertCreated();
    $response->assertJsonPath('success', true);

    expect(Scenario::query()->where('source_generation_id', $this->generation->id)->exists())->toBeTrue();

    $this->generation->refresh();
    $audit = $this->generation->metadata['auto_accept_audit'] ?? [];

    expect($audit)->not->toBeEmpty();
    expect($audit[0]['result'] ?? null)->toBe('accepted');
});

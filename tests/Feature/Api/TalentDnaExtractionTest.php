<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\PsychometricProfile;
use App\Models\Skill;
use App\Models\User;
use App\Services\AiOrchestratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->person = People::factory()->create([
        'organization_id' => $this->org->id,
        'first_name' => 'Carlos',
        'last_name' => 'Mendoza',
    ]);

    // Mock the AI Orchestrator
    $mockOrchestrator = Mockery::mock(AiOrchestratorService::class);
    $mockOrchestrator->shouldReceive('agentThink')
        ->andReturn([
            'response' => [
                'success_persona' => 'Líder técnico con alta dominancia y expertise en ML',
                'dominant_gene' => 'Combinación de liderazgo técnico con alta capacidad de influencia',
                'search_profile' => 'Buscar candidatos con D alto (>0.7), skills en ML/AI, 5+ años',
            ],
        ]);
    $this->app->instance(AiOrchestratorService::class, $mockOrchestrator);
});

it('extracts DNA from a high performer with skills and traits', function () {
    $skill = Skill::factory()->create(['organization_id' => $this->org->id, 'name' => 'Machine Learning']);
    $this->person->activeSkills()->create([
        'skill_id' => $skill->id,
        'current_level' => 5,
        'evidence_source' => 'Talent360',
    ]);

    PsychometricProfile::factory()->create([
        'people_id' => $this->person->id,
        'trait_name' => 'Dominance',
        'score' => 0.85,
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/dna-extract/{$this->person->id}");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'success_persona',
            'dominant_gene',
            'search_profile',
        ],
    ]);
});

it('returns meaningful persona description', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/dna-extract/{$this->person->id}");

    $response->assertStatus(200);
    $data = $response->json('data');
    expect($data['success_persona'])->toBeString()->not->toBeEmpty();
    expect($data['dominant_gene'])->toBeString()->not->toBeEmpty();
    expect($data['search_profile'])->toBeString()->not->toBeEmpty();
});

it('returns 500 for non-existent person', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/talent/dna-extract/99999');

    $response->assertStatus(500);
    $response->assertJson(['success' => false]);
});

it('works with a person that has no skills or traits', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/dna-extract/{$this->person->id}");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

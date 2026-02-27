<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\PsychometricProfile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->person = People::factory()->create([
        'organization_id' => $this->org->id,
        'first_name' => 'Carlos',
        'last_name' => 'Mendoza',
    ]);
});

it('extracts DNA from a high performer with skills and traits', function () {
    // Setup: give the person skills
    $skill = Skill::factory()->create(['organization_id' => $this->org->id, 'name' => 'Machine Learning']);
    $this->person->activeSkills()->create([
        'skill_id' => $skill->id,
        'current_level' => 5,
        'evidence_source' => 'Talent360',
    ]);

    // Setup: give the person psychometric profiles
    PsychometricProfile::factory()->create([
        'people_id' => $this->person->id,
        'trait_name' => 'Dominance',
        'score' => 0.85,
    ]);

    // Mock AI response
    Http::fake([
        '*' => Http::response([
            'response' => [
                'success_persona' => 'Líder técnico con alta dominancia y expertise en ML',
                'dominant_gene' => 'Combinación de liderazgo técnico con alta capacidad de influencia',
                'search_profile' => 'Buscar candidatos con D alto (>0.7), skills en ML/AI, 5+ años',
            ],
        ], 200),
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
    Http::fake([
        '*' => Http::response([
            'response' => [
                'success_persona' => 'Ingeniero senior orientado a resultados',
                'dominant_gene' => 'Resiliencia bajo presión',
                'search_profile' => 'Candidatos con 3+ años en startups de alto crecimiento',
            ],
        ], 200),
    ]);

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

it('handles AI service failure gracefully', function () {
    Http::fake([
        '*' => Http::response(['error' => 'Service unavailable'], 503),
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/dna-extract/{$this->person->id}");

    // The service catches exceptions and returns error key
    $response->assertStatus(200);
});

it('works with a person that has no skills or traits', function () {
    Http::fake([
        '*' => Http::response([
            'response' => [
                'success_persona' => 'Perfil en desarrollo',
                'dominant_gene' => 'Datos insuficientes para determinar',
                'search_profile' => 'Se requiere más información del colaborador',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/talent/dna-extract/{$this->person->id}");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

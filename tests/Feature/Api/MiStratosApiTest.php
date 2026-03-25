<?php

use App\Models\AssessmentSession;
use App\Models\Competency;
use App\Models\DevelopmentPath;
use App\Models\Organizations;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create([
        'name' => 'Stratos HQ',
        'subdomain' => 'hq',
    ]);

    $this->user = User::factory()->create([
        'organization_id' => $this->org->id,
        'role' => 'collaborator',
    ]);

    $this->role = Roles::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Software Engineer',
        'cube_dimensions' => [
            'x_archetype' => 'Tactical',
            'y_mastery_level' => 3,
            'z_business_process' => 'Product Development',
        ],
    ]);

    $this->person = People::factory()->create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'first_name' => 'Omar',
        'last_name' => 'Ahumada',
        'role_id' => $this->role->id,
    ]);
});

it('returns dashboard data for authenticated collaborator', function () {
    // 1. Setup specific data
    $comp = Competency::factory()->create(['organization_id' => $this->org->id, 'name' => 'Critical Thinking']);
    $skill = Skill::factory()->create(['organization_id' => $this->org->id, 'name' => 'Problem Solving']);

    $this->role->skills()->attach($skill->id, ['required_level' => 4]);
    $skill->competencies()->attach($comp->id);

    // Attach skill to person with current level
    $this->person->skills()->attach($skill->id, ['current_level' => 3]);

    // Create a development path
    DevelopmentPath::factory()->create([
        'people_id' => $this->person->id,
        'action_title' => 'Mastering React',
        'status' => 'active',
    ]);

    // Create an assessment session
    AssessmentSession::factory()->create([
        'people_id' => $this->person->id,
        'type' => 'interview',
        'status' => 'active',
    ]);

    // 2. Call the API
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/mi-stratos/dashboard');

    // 3. Assertions
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.person.full_name', 'Omar Ahumada')
        ->assertJsonPath('data.person.role.name', 'Software Engineer')
        ->assertJsonStructure([
            'data' => [
                'person',
                'kpis',
                'gap_analysis',
                'competencies',
                'learning_paths',
                'conversations',
                'quests',
                'evaluations',
            ],
        ]);

    // Verify KPIs
    expect($response->json('data.kpis.potential'))->toBeGreaterThanOrEqual(0);
    expect($response->json('data.kpis.readiness'))->toBeGreaterThanOrEqual(0);
});

it('returns empty state if user has no linked person', function () {
    $userWithoutPerson = User::factory()->create([
        'organization_id' => $this->org->id,
        'role' => 'collaborator',
    ]);

    $response = $this->actingAs($userWithoutPerson, 'sanctum')
        ->getJson('/api/mi-stratos/dashboard');

    $response->assertStatus(200)
        ->assertJsonPath('data.person', null)
        ->assertJsonFragment(['message' => 'No hay un perfil de colaborador asociado a este usuario.']);
});

it('requires authentication', function () {
    $response = $this->getJson('/api/mi-stratos/dashboard');
    $response->assertStatus(401);
});

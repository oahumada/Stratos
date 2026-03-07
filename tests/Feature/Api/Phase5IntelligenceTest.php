<?php

use App\Models\Organizations;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->actingAs($this->user);
    $this->person = People::factory()->create(['organization_id' => $this->org->id, 'user_id' => $this->user->id]);
    $this->role = Roles::factory()->create(['organization_id' => $this->org->id]);
    $this->person->update(['role_id' => $this->role->id]);

    // Seed agents needed for intelligence services
    $this->seed(\Database\Seeders\SystemAgentsSeeder::class);

    // Create some gaps
    $skill = Skill::factory()->create();
    PeopleRoleSkills::create([
        'people_id' => $this->person->id,
        'skill_id' => $skill->id,
        'role_id' => $this->role->id,
        'current_level' => 1,
        'required_level' => 4,
        'verified' => true,
    ]);
});

it('generates a learning blueprint for a person', function () {
    $response = $this->postJson("/api/learning-blueprints/{$this->person->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'person',
            'success_probability',
            'current_gaps',
            'learning_paths',
            'roi_projection',
        ],
    ]);
});

it('materializes a blueprint into a development path', function () {
    $blueprint = [
        'learning_paths' => [
            [
                'phase' => 1,
                'name' => 'Fase Test',
                'actions' => [
                    [
                        'title' => 'Acción Test',
                        'type' => 'training',
                        'strategy' => 'build',
                        'hours' => 10,
                    ],
                ],
            ],
        ],
        'estimated_completion_months' => 3,
    ];

    $response = $this->postJson("/api/learning-blueprints/{$this->person->id}/materialize", [
        'blueprint' => $blueprint,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'id',
            'people_id',
            'actions',
        ],
    ]);
});

it('runs a sentinel system scan', function () {
    $response = $this->getJson('/api/sentinel/scan');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'scan_timestamp',
            'overall_health',
            'modules',
            'alerts',
        ],
    ]);
});

it('gets sentinel health score', function () {
    $response = $this->getJson('/api/sentinel/health');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'health_score',
        ],
    ]);
});

it('gets contextual guide suggestions', function () {
    $response = $this->getJson('/api/guide/suggestions?module=scenario_planning');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'module',
            'suggestions',
            'proactive_tips',
            'quick_actions',
        ],
    ]);
});

it('completes an onboarding step in guide', function () {
    $response = $this->postJson('/api/guide/onboarding/complete', [
        'module' => 'scenario_planning',
        'step' => 'create_first',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

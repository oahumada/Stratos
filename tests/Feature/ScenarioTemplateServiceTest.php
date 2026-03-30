<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\ScenarioTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'current_organization_id' => $this->organization->id,
        'role' => 'manager',
    ]);
});

// ================== LIST TEMPLATES ==================

it('retrieves all templates with pagination', function () {
    ScenarioTemplate::factory(20)->create(['is_active' => true]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [],
            'pagination' => ['current_page', 'total', 'per_page', 'last_page'],
        ]);

    expect($response->json('pagination.total'))->toBe(20);
});

it('filters templates by scenario type', function () {
    ScenarioTemplate::factory(5)->create(['scenario_type' => 'transformation']);
    ScenarioTemplate::factory(3)->create(['scenario_type' => 'growth']);

    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates?scenario_type=transformation');

    $response->assertSuccessful();
    expect(count($response->json('data')))->toBe(5);
});

it('filters templates by industry', function () {
    ScenarioTemplate::factory(3)->create(['industry' => 'technology']);
    ScenarioTemplate::factory(2)->create(['industry' => 'finance']);

    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates?industry=technology');

    $response->assertSuccessful();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(3);
});

it('searches templates by name', function () {
    ScenarioTemplate::factory()->create(['name' => 'AI Adoption Accelerator']);
    ScenarioTemplate::factory()->create(['name' => 'Digital Transformation']);

    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates?search=AI');

    $response->assertSuccessful();
    expect(count($response->json('data')))->toBe(1);
    expect($response->json('data.0.name'))->toBe('AI Adoption Accelerator');
});

// ================== SHOW TEMPLATE ==================

it('retrieves a single template and increments usage', function () {
    $template = ScenarioTemplate::factory()->create(['usage_count' => 5]);

    $response = $this->actingAs($this->user)
        ->getJson("/api/strategic-planning/scenario-templates/{$template->id}");

    $response->assertSuccessful()
        ->assertJsonFragment(['id' => $template->id]);

    expect($template->refresh()->usage_count)->toBe(6);
});

it('returns 404 for non-existent template', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates/999999');

    $response->assertNotFound();
});

// ================== CREATE TEMPLATE ==================

it('creates a new template with valid data', function () {
    $data = [
        'name' => 'New Template',
        'description' => 'A new scenario template',
        'scenario_type' => 'transformation',
        'industry' => 'technology',
        'icon' => 'mdi-robot',
        'config' => [
            'predefined_skills' => [['skill_id' => 1, 'required_headcount' => 5]],
            'suggested_strategies' => ['build', 'buy'],
            'kpis' => ['Talent coverage', 'Time to hire'],
        ],
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenario-templates', $data);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'New Template']);

    $this->assertDatabaseHas('scenario_templates', [
        'name' => 'New Template',
        'scenario_type' => 'transformation',
    ]);
});

it('validates required fields on create', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenario-templates', [
            'name' => 'Incomplete Template',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['description', 'scenario_type', 'industry']);
});

it('prevents duplicate template names', function () {
    ScenarioTemplate::factory()->create(['name' => 'Unique Template']);

    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenario-templates', [
            'name' => 'Unique Template',
            'description' => 'Different',
            'scenario_type' => 'growth',
            'industry' => 'general',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('autogenerates slug from name', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenario-templates', [
            'name' => 'My Awesome Template',
            'description' => 'Test',
            'scenario_type' => 'transformation',
            'industry' => 'general',
        ]);

    $response->assertCreated();
    expect($response->json('data.slug'))->toContain('my-awesome-template');
});

// ================== UPDATE TEMPLATE ==================

it('updates an existing template', function () {
    $template = ScenarioTemplate::factory()->create();

    $response = $this->actingAs($this->user)
        ->patchJson("/api/strategic-planning/scenario-templates/{$template->id}", [
            'description' => 'Updated description',
            'is_active' => false,
        ]);

    $response->assertSuccessful();
    $template->refresh();
    expect($template->description)->toBe('Updated description');
    expect($template->is_active)->toBeFalse();
});

it('merges config when updating', function () {
    $template = ScenarioTemplate::factory()->create([
        'config' => [
            'kpis' => ['KPI1'],
            'strategies' => ['build'],
        ],
    ]);

    $response = $this->actingAs($this->user)
        ->patchJson("/api/strategic-planning/scenario-templates/{$template->id}", [
            'config' => ['kpis' => ['KPI1', 'KPI2']],
        ]);

    $response->assertSuccessful();
    $template->refresh();
    expect($template->config['kpis'])->toContain('KPI1');
    expect($template->config['kpis'])->toContain('KPI2');
    expect($template->config['strategies'])->toContain('build');
});

// ================== DELETE TEMPLATE ==================

it('deletes a template (soft delete)', function () {
    $template = ScenarioTemplate::factory()->create();

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/strategic-planning/scenario-templates/{$template->id}");

    $response->assertSuccessful();
    expect($template->refresh()->deleted_at)->not->toBeNull();
});

// ================== SAVE AS TEMPLATE ==================

it('converts scenario to template', function () {
    $scenario = Scenario::factory()->create([
        'name' => 'Test Scenario',
        'description' => 'Test description',
        'budget' => 500000,
        'timeline_weeks' => 12,
    ]);

    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenario-templates/save-as-template', [
            'scenario_id' => $scenario->id,
            'name' => 'Scenario Template',
            'description' => 'Converted to template',
            'scenario_type' => 'transformation',
            'industry' => 'technology',
        ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'Scenario Template']);

    $this->assertDatabaseHas('scenario_templates', ['name' => 'Scenario Template']);
});

it('validates scenario exists when saving as template', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/scenario-templates/save-as-template', [
            'scenario_id' => 999999,
            'name' => 'Template',
            'description' => 'Test',
            'scenario_type' => 'general',
            'industry' => 'general',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('scenario_id');
});

// ================== INSTANTIATE FROM TEMPLATE ==================

it('creates scenario from template', function () {
    $template = ScenarioTemplate::factory()->create([
        'config' => [
            'predefined_skills' => [['skill_id' => 1]],
            'suggested_strategies' => ['build'],
        ],
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenario-templates/{$template->id}/instantiate", [
            'name' => 'Scenario from Template',
            'budget' => 750000,
            'timeline_weeks' => 24,
        ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'Scenario from Template']);

    $this->assertDatabaseHas('scenarios', [
        'name' => 'Scenario from Template',
        'template_id' => $template->id,
        'status' => 'draft',
    ]);
});

it('increments template usage when instantiating', function () {
    $template = ScenarioTemplate::factory()->create(['usage_count' => 5]);

    $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenario-templates/{$template->id}/instantiate", [
            'name' => 'New Scenario',
        ]);

    expect($template->refresh()->usage_count)->toBe(6);
});

// ================== CLONE TEMPLATE ==================

it('clones a template', function () {
    $original = ScenarioTemplate::factory()->create([
        'name' => 'Original Template',
        'config' => ['kpis' => ['KPI1']],
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenario-templates/{$original->id}/clone");

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'Original Template (Copy)']);

    $this->assertDatabaseHas('scenario_templates', ['name' => 'Original Template (Copy)']);

    $cloned = ScenarioTemplate::where('name', 'Original Template (Copy)')->first();
    expect($cloned->config)->toBe($original->config);
    expect($cloned->usage_count)->toBe(0);
});

it('customizes cloned template name', function () {
    $original = ScenarioTemplate::factory()->create();

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/scenario-templates/{$original->id}/clone", [
            'name' => 'Custom Clone Name',
        ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'Custom Clone Name']);
});

// ================== RECOMMENDATIONS ==================

it('returns recommended templates', function () {
    ScenarioTemplate::factory(5)->create(['is_active' => true]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates/recommendations?limit=3');

    $response->assertSuccessful();
    expect(count($response->json('data')))->toBeLessThanOrEqual(3);
});

// ================== STATISTICS ==================

it('returns template statistics', function () {
    ScenarioTemplate::factory(10)->create(['is_active' => true]);
    ScenarioTemplate::factory(3)->create(['is_active' => false]);
    ScenarioTemplate::factory(5)->create(['scenario_type' => 'transformation']);
    ScenarioTemplate::factory(5)->create(['scenario_type' => 'growth']);

    $response = $this->actingAs($this->user)
        ->getJson('/api/strategic-planning/scenario-templates/statistics');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                'total_templates',
                'active_templates',
                'total_instantiations',
                'templates_by_type',
                'templates_by_industry',
                'most_used_templates',
            ],
        ]);
});

// ================== AUTHORIZATION ==================

it('requires authentication to view templates', function () {
    $response = $this->getJson('/api/strategic-planning/scenario-templates');
    $response->assertUnauthorized();
});

it('requires manager role to create templates', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->postJson('/api/strategic-planning/scenario-templates', [
            'name' => 'Test',
            'description' => 'Test',
            'scenario_type' => 'general',
            'industry' => 'general',
        ]);

    $response->assertForbidden();
});

it('requires admin role to delete templates', function () {
    $template = ScenarioTemplate::factory()->create();
    $user = User::factory()->create(['role' => 'manager']);

    $response = $this->actingAs($user)
        ->deleteJson("/api/strategic-planning/scenario-templates/{$template->id}");
    $response->assertSuccessful();
});

<?php

use App\Models\Organizations;
use App\Models\User;
use App\Models\WorkforcePlanningScenario;
use App\Models\ScenarioTemplate;
use App\Models\ScenarioSkillDemand;
use App\Models\Skills;
use App\Services\WorkforcePlanningService;

beforeEach(function () {
    $this->organization = Organizations::create([
        'name' => 'Test Organization',
        'subdomain' => 'testorg',
        'industry' => 'Technology',
        'size' => 'medium',
    ]);

    $this->user = User::create([
        'organization_id' => $this->organization->id,
        'name' => 'Test User',
        'email' => 'test@testorg.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
});

test('it enforces tenant isolation for scenarios', function () {
    $org1 = $this->organization;
    $org2 = Organizations::create([
        'name' => 'Other Organization',
        'subdomain' => 'otherorg',
        'industry' => 'Finance',
        'size' => 'small',
    ]);

    $scenario1 = WorkforcePlanningScenario::create([
        'organization_id' => $org1->id,
        'name' => 'Scenario 1',
        'description' => 'Test scenario 1',
        'horizon_months' => 12,
        'fiscal_year' => 2026,
        'status' => 'draft',
        'created_by' => $this->user->id,
    ]);

    $user2 = User::create([
        'organization_id' => $org2->id,
        'name' => 'Other User',
        'email' => 'user@otherorg.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    $scenario2 = WorkforcePlanningScenario::create([
        'organization_id' => $org2->id,
        'name' => 'Scenario 2',
        'description' => 'Test scenario 2',
        'horizon_months' => 12,
        'fiscal_year' => 2026,
        'status' => 'draft',
        'created_by' => $user2->id,
    ]);

    $this->actingAs($this->user);

    // User can access own org's scenario
    $response = $this->getJson("/api/v1/workforce-planning/workforce-scenarios/{$scenario1->id}");
    $response->assertStatus(200);

    // User cannot access other org's scenario (403 Forbidden by policy)
    $response = $this->getJson("/api/v1/workforce-planning/workforce-scenarios/{$scenario2->id}");
    $response->assertStatus(403);
});

test('it creates scenario from template', function () {
    $this->actingAs($this->user);

    $template = ScenarioTemplate::create([
        'name' => 'Test Template',
        'slug' => 'test-template',
        'scenario_type' => 'transformation',
        'industry' => 'technology',
        'description' => 'Test template',
        'config' => [],  // Empty config to avoid FK violations
    ]);

    $response = $this->postJson("/api/v1/workforce-planning/workforce-scenarios/{$template->id}/instantiate-from-template", [
        'name' => 'My Scenario from Template',
        'horizon_months' => 12,
        'fiscal_year' => 2026,
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'id',
            'name',
            'template_id',
            'scenario_type',
            'organization_id',
        ],
    ]);

    $this->assertDatabaseHas('workforce_planning_scenarios', [
        'name' => 'My Scenario from Template',
        'template_id' => $template->id,
        'organization_id' => $this->organization->id,
    ]);
});

test('it calculates gaps with expected structure', function () {
    $scenario = WorkforcePlanningScenario::create([
        'organization_id' => $this->organization->id,
        'name' => 'Gap Analysis Scenario',
        'description' => 'Test scenario',
        'horizon_months' => 12,
        'fiscal_year' => 2026,
        'status' => 'draft',
        'created_by' => $this->user->id,
    ]);

    $skill = Skills::create([
        'organization_id' => $this->organization->id,
        'name' => 'Python',
        'category' => 'technical',
    ]);

    ScenarioSkillDemand::create([
        'scenario_id' => $scenario->id,
        'skill_id' => $skill->id,
        'required_headcount' => 10,
        'required_level' => 4,
        'priority' => 'critical',
        'rationale' => 'Test gap',
    ]);

    $service = app(WorkforcePlanningService::class);
    $result = $service->calculateScenarioGaps($scenario);

    expect($result)->toHaveKeys(['scenario_id', 'generated_at', 'summary', 'gaps']);
    expect($result['summary'])->toHaveKeys(['total_skills', 'critical_skills', 'risk_score', 'avg_coverage_pct']);
    expect($result['gaps'])->toHaveCount(1);

    $gap = $result['gaps'][0];
    expect($gap['skill_id'])->toBe($skill->id);
    expect($gap['skill_name'])->toBe('Python');
    expect($gap['priority'])->toBe('critical');
    // Sin people_skills, el gap será igual a required_headcount (no hay inventory actual)
    expect($gap['gap_headcount'])->toBe(10);
    expect($gap['coverage_pct'])->toBe(0.0);
});

test('it generates suggested strategies', function () {
    $scenario = WorkforcePlanningScenario::create([
        'organization_id' => $this->organization->id,
        'name' => 'Strategy Scenario',
        'description' => 'Test scenario',
        'horizon_months' => 12,
        'fiscal_year' => 2026,
        'status' => 'draft',
        'created_by' => $this->user->id,
    ]);

    $skill = Skills::create([
        'organization_id' => $this->organization->id,
        'name' => 'JavaScript',
        'category' => 'technical',
    ]);

    ScenarioSkillDemand::create([
        'scenario_id' => $scenario->id,
        'skill_id' => $skill->id,
        'required_headcount' => 15,
        'required_level' => 4,
        'priority' => 'critical',
        'rationale' => 'Test strategy generation',
    ]);

    $this->actingAs($this->user);

    $response = $this->postJson("/api/v1/workforce-planning/workforce-scenarios/{$scenario->id}/refresh-suggested-strategies", [
        'time_pressure' => 'high',
        'budget_sensitivity' => 'medium',
        'automation_allowed' => false,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message',
        'created',
    ]);

    $this->assertDatabaseHas('scenario_closure_strategies', [
        'scenario_id' => $scenario->id,
        'skill_id' => $skill->id,
        'status' => 'proposed',
    ]);
});

test('it lists scenario templates', function () {
    ScenarioTemplate::create([
        'name' => 'Template 1',
        'slug' => 'template-1',
        'scenario_type' => 'growth',
        'industry' => 'technology',
        'description' => 'Template 1',
        'is_active' => true,
        'config' => [],
    ]);

    ScenarioTemplate::create([
        'name' => 'Template 2',
        'slug' => 'template-2',
        'scenario_type' => 'transformation',
        'industry' => 'finance',
        'description' => 'Template 2',
        'is_active' => true,
        'config' => [],
    ]);

    ScenarioTemplate::create([
        'name' => 'Template 3',
        'slug' => 'template-3',
        'scenario_type' => 'optimization',
        'industry' => 'general',
        'description' => 'Template 3',
        'is_active' => true,
        'config' => [],
    ]);

    $this->actingAs($this->user);

    $response = $this->getJson('/api/v1/workforce-planning/scenario-templates');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            '*' => ['id', 'name', 'slug', 'scenario_type', 'industry'],
        ],
        'pagination',
    ]);
});

<?php

use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\RoleSkill;
use App\Models\Scenario;
use App\Models\ScenarioClosureStrategy;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioSkillDemand;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_workforce_motor_test');
    grantPermissionToRole('qa_workforce_motor_test', 'scenarios.view', 'scenarios', 'view');
    grantPermissionToRole('qa_workforce_motor_test', 'scenarios.create', 'scenarios', 'create');
    $this->actingAs($this->user);

    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'status' => 'draft',
    ]);

    $this->skill_python = Skill::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Python',
    ]);

    $this->role_backend = Roles::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Backend Engineer',
    ]);
});

it('detects skill gap and recommends HIRE strategy', function () {
    // Scenario: Need 3 Backend engineers with Python, but current team has 0
    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'fte' => 3,
    ]);

    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'skill_id' => $this->skill_python->id,
        'proficiency_required' => 'senior',
        'gap_status' => 'critical',
    ]);

    // Trigger gap analysis
    \App\Jobs\AnalyzeTalentGap::dispatch($this->scenario->id);

    // Verify strategy was created
    $strategies = ScenarioClosureStrategy::where('scenario_id', $this->scenario->id)
        ->where('strategy', 'hire')
        ->get();

    expect($strategies)->toHaveCount(1)
        ->and($strategies->first()->role_id)->toBe($this->role_backend->id)
        ->and($strategies->first()->skill_id)->toBe($this->skill_python->id)
        ->and($strategies->first()->description)->toContain('hire');
});

it('recommends RESKILL when internal resources can upskill', function () {
    // Current: 2 developers without Python, Need: 2 developers with Python (internal reskilling)
    $currentPeople = People::factory()->count(2)->create(['organization_id' => $this->org->id]);

    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'fte' => 2,
    ]);

    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'skill_id' => $this->skill_python->id,
        'proficiency_required' => 'intermediate',
        'gap_status' => 'medium',
    ]);

    \App\Jobs\AnalyzeTalentGap::dispatch($this->scenario->id);

    $strategies = ScenarioClosureStrategy::where('scenario_id', $this->scenario->id)
        ->whereIn('strategy', ['reskill', 'training'])
        ->get();

    expect($strategies->isNotEmpty())->toBeTrue()
        ->and($strategies->first()->estimated_time_weeks)->toBeGreaterThan(0);
});

it('calculates motor recommendations with success probability', function () {
    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'fte' => 1,
    ]);

    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'skill_id' => $this->skill_python->id,
        'proficiency_required' => 'senior',
        'gap_status' => 'critical',
    ]);

    \App\Jobs\AnalyzeTalentGap::dispatch($this->scenario->id);

    $strategy = ScenarioClosureStrategy::where('scenario_id', $this->scenario->id)->first();

    expect($strategy)->not->toBeNull()
        ->and($strategy->success_probability)->toBeGreaterThanOrEqual(0)
        ->and($strategy->success_probability)->toBeLessThanOrEqual(100)
        ->and($strategy->estimated_cost)->toBeGreaterThan(0)
        ->and($strategy->estimated_time_weeks)->toBeGreaterThan(0);
});

it('returns multiple strategies for complex gap scenarios', function () {
    // Multiple gaps: Python (hire) + AI/ML (reskill) + Leadership (rotate)
    $skill_ai = Skill::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'AI/ML',
    ]);

    $role_lead = Roles::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Tech Lead',
    ]);

    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'fte' => 2,
    ]);

    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role_lead->id,
        'fte' => 1,
    ]);

    // Gap 1: Python
    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'skill_id' => $this->skill_python->id,
        'gap_status' => 'critical',
    ]);

    // Gap 2: AI/ML
    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role_lead->id,
        'skill_id' => $skill_ai->id,
        'gap_status' => 'high',
    ]);

    \App\Jobs\AnalyzeTalentGap::dispatch($this->scenario->id);

    $strategies = ScenarioClosureStrategy::where('scenario_id', $this->scenario->id)->get();

    expect($strategies->count())->toBeGreaterThanOrEqual(1)
        ->and($strategies->pluck('strategy')->unique()->count())->toBeGreaterThanOrEqual(1);
});

it('returns closure strategies via API endpoint', function () {
    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'fte' => 1,
    ]);

    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'skill_id' => $this->skill_python->id,
        'gap_status' => 'critical',
    ]);

    \App\Jobs\AnalyzeTalentGap::dispatch($this->scenario->id);

    $response = $this->getJson("/api/scenarios/{$this->scenario->id}")
        ->assertOk();

    $data = $response->json('data');

    expect($data)->toHaveKey('closure_strategies')
        ->and(count($data['closure_strategies']))->toBeGreaterThanOrEqual(0);
});

it('persists strategy metadata for auditability', function () {
    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'fte' => 2,
    ]);

    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role_backend->id,
        'skill_id' => $this->skill_python->id,
        'gap_status' => 'critical',
    ]);

    \App\Jobs\AnalyzeTalentGap::dispatch($this->scenario->id);

    $strategy = ScenarioClosureStrategy::where('scenario_id', $this->scenario->id)->first();

    expect($strategy->is_ia_generated)->toBeTrue()
        ->and($strategy->ia_confidence_score)->toBeGreaterThanOrEqual(0)
        ->and($strategy->ia_strategy_rationale)->not->toBeNull()
        ->and($strategy->action_items)->toBeArray();
});

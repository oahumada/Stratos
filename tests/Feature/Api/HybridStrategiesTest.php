<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use App\Models\Skill;
use App\Models\User;
use App\Models\Roles;
use App\Models\TalentBlueprint;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $org->id]);
    $this->actingAs($this->user);

    $this->scenario = Scenario::factory()->create([
        'organization_id' => $org->id,
        'owner_user_id' => $this->user->id,
        'horizon_months' => 6,
        'fiscal_year' => 2026,
        'created_by' => $this->user->id,
    ]);
});

test('it generates bot strategy when blueprint has high synthetic leverage', function () {
    // 1. Create a role and blueprint
    $role = Roles::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Synthetic Analyst',
    ]);
    
    TalentBlueprint::create([
        'scenario_id' => $this->scenario->id,
        'role_name' => 'Synthetic Analyst',
        'total_fte_required' => 2,
        'human_leverage' => 20,
        'synthetic_leverage' => 80, // > 50% -> BOT
        'recommended_strategy' => 'Synthetic',
        'agent_specs' => ['logic_justification' => 'IA can handle most tasks.'],
    ]);

    $sRole = ScenarioRole::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'quantity' => 2,
    ]);

    $skill = Skill::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Data Processing',
    ]);

    // Gap
    ScenarioRoleSkill::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $sRole->id,
        'skill_id' => $skill->id,
        'required_level' => 4,
        'current_level' => 1,
    ]);

    // 2. Refresh strategies
    $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/refresh-suggested-strategies")
        ->assertStatus(200);

    // 3. Assert bot strategy
    $this->assertDatabaseHas('scenario_closure_strategies', [
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'strategy' => 'bot',
    ]);
});

test('it prefers blueprint recommendation for low synthetic leverage', function () {
     // 1. Create a role and blueprint
     $role = Roles::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Hybrid Designer',
    ]);
    
    TalentBlueprint::create([
        'scenario_id' => $this->scenario->id,
        'role_name' => 'Hybrid Designer',
        'total_fte_required' => 1,
        'human_leverage' => 70,
        'synthetic_leverage' => 30, // < 50% -> Uses recommended_strategy
        'recommended_strategy' => 'Buy',
        'agent_specs' => [],
    ]);

    $sRole = ScenarioRole::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'quantity' => 1,
    ]);

    $skill = Skill::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'UX Design',
    ]);

    // Gap
    ScenarioRoleSkill::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $sRole->id,
        'skill_id' => $skill->id,
        'required_level' => 4,
        'current_level' => 3,
    ]);

    // 2. Refresh strategies
    $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/refresh-suggested-strategies")
        ->assertStatus(200);

    // 3. Assert buy strategy (from blueprint recommendation)
    $this->assertDatabaseHas('scenario_closure_strategies', [
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'strategy' => 'buy',
    ]);
});

test('it returns strategies with blueprint data in list endpoint', function () {
    // Setup
    $role = Roles::create(['organization_id' => $this->user->organization_id, 'name' => 'Analyst']);
    TalentBlueprint::create([
        'scenario_id' => $this->scenario->id,
        'role_name' => 'Analyst',
        'total_fte_required' => 1,
        'human_leverage' => 60,
        'synthetic_leverage' => 40,
        'recommended_strategy' => 'Build',
        'agent_specs' => [],
    ]);
    
    \DB::table('scenario_closure_strategies')->insert([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'strategy' => 'build',
        'strategy_name' => 'Test Build',
        'status' => 'proposed',
    ]);

    $this->getJson("/api/strategic-planning/scenarios/{$this->scenario->id}/strategies")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'strategy',
                    'role_name',
                    'blueprint' => [
                        'human_leverage',
                        'synthetic_leverage'
                    ]
                ]
            ]
        ])
        ->assertJsonPath('data.0.role_name', 'Analyst')
        ->assertJsonPath('data.0.blueprint.human_leverage', 60);
});

<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use App\Models\Skill;
use App\Models\User;
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

test('it generates strategies based on skill gaps', function () {
    // 1. Create a skill gap
    $role = \App\Models\Roles::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Test Role Higher Gap',
    ]);
    $sRole = ScenarioRole::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'quantity' => 1,
    ]);

    $skill = Skill::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Test Skill 1',
    ]);

    // Gap: Required 5, Current 1 = Gap of 4 (Critical -> Buy)
    ScenarioRoleSkill::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $sRole->id,
        'skill_id' => $skill->id,
        'required_level' => 5,
        'current_level' => 1,
        'is_critical' => true,
    ]);

    // 2. Call the endpoint
    $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/refresh-suggested-strategies")
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    // 3. Assertions
    $this->assertDatabaseHas('scenario_closure_strategies', [
        'scenario_id' => $this->scenario->id,
        'skill_id' => $skill->id,
        'strategy' => 'buy', // Should be buy due to large gap
    ]);
});

test('it generates build strategy for small gaps', function () {
    // 1. Create a small skill gap
    $role = \App\Models\Roles::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Test Role',
    ]);
    $sRole = ScenarioRole::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'quantity' => 1,
    ]);

    $skill = Skill::create([
        'organization_id' => $this->user->organization_id,
        'name' => 'Test Skill 1',
    ]);

    // Gap: Required 3, Current 2 = Gap of 1 (Small -> Build)
    ScenarioRoleSkill::create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $sRole->id,
        'skill_id' => $skill->id,
        'required_level' => 3,
        'current_level' => 2,
        'is_critical' => false,
    ]);

    // 2. Call the endpoint
    $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/refresh-suggested-strategies")
        ->assertStatus(200);

    // 3. Assertions
    $this->assertDatabaseHas('scenario_closure_strategies', [
        'scenario_id' => $this->scenario->id,
        'skill_id' => $skill->id,
        'strategy' => 'build',
    ]);
});

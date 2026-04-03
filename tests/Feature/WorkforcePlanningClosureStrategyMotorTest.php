<?php

use App\Models\Organization;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_workforce_motor_test');
    grantPermissionToRole('qa_workforce_motor_test', 'scenarios.view', 'scenarios', 'view');
    $this->actingAs($this->user);

    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'status' => 'draft',
    ]);

    $this->role = Roles::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Backend Engineer',
    ]);
});

it('creates scenario role successfully', function () {
    $scenarioRole = ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role->id,
        'fte' => 3,
    ]);

    expect($scenarioRole)->toExist()
        ->and($scenarioRole->fte)->toBe(3);
});

it('creates scenario role competency', function () {
    $scenarioRole = ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role->id,
    ]);

    $competency = Skill::factory()->create(['organization_id' => $this->org->id]);

    $roleCompetency = ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $scenarioRole->id,
        'competency_id' => $competency->id,
        'required_level' => 4,
        'is_core' => true,
    ]);

    expect($roleCompetency)->toExist()
        ->and($roleCompetency->required_level)->toBe(4)
        ->and($roleCompetency->is_core)->toBeTrue();
});

it('retrieves scenario with roles and competencies', function () {
    $role1 = ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role->id,
    ]);

    $competency = Skill::factory()->create(['organization_id' => $this->org->id]);
    ScenarioRoleCompetency::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role1->id,
        'competency_id' => $competency->id,
    ]);

    $scenario = Scenario::with(['roles', 'roles.competencies'])->find($this->scenario->id);

    expect($scenario->roles)->toHaveCount(1)
        ->and($scenario->roles->first()->competencies)->toHaveCount(1);
});

it('enforces organization scoping', function () {
    $otherOrg = Organization::factory()->create();
    $otherScenario = Scenario::factory()->create(['organization_id' => $otherOrg->id]);

    $role1 = ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role->id,
    ]);

    $crossOrgScenarioRoles = ScenarioRole::where('scenario_id', $otherScenario->id)->get();

    expect($crossOrgScenarioRoles)->toHaveCount(0)
        ->and($role1->scenario->organization_id)->toBe($this->org->id);
});

it('supports multi-role scenarios', function () {
    $role2 = Roles::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Frontend Engineer',
    ]);

    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $this->role->id,
        'fte' => 2,
    ]);

    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role2->id,
        'fte' => 3,
    ]);

    $roles = ScenarioRole::where('scenario_id', $this->scenario->id)->get();

    expect($roles)->toHaveCount(2)
        ->and($roles->sum('fte'))->toBe(5);
});

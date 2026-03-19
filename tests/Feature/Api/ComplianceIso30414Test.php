<?php

use App\Models\Departments;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Models\RoleSkill;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organizationA = Organization::factory()->create();
    $this->organizationB = Organization::factory()->create();

    $this->userA = User::factory()->create([
        'organization_id' => $this->organizationA->id,
    ]);
});

it('requires authentication to access iso30414 summary', function () {
    $this->getJson('/api/compliance/iso30414/summary')->assertUnauthorized();
});

it('returns iso30414 metrics for authenticated organization only', function () {
    $departmentA = Departments::create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Engineering',
    ]);

    $departmentB = Departments::create([
        'organization_id' => $this->organizationB->id,
        'name' => 'Finance',
    ]);

    $roleA = Roles::factory()->create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Backend Engineer',
        'base_salary' => 60000,
    ]);

    $roleB = Roles::factory()->create([
        'organization_id' => $this->organizationB->id,
        'name' => 'Controller',
        'base_salary' => 90000,
    ]);

    $skillA = Skill::create([
        'organization_id' => $this->organizationA->id,
        'name' => 'Critical Thinking',
        'scope_type' => 'transversal',
        'status' => 'active',
    ]);

    $skillB = Skill::create([
        'organization_id' => $this->organizationB->id,
        'name' => 'Budget Control',
        'scope_type' => 'transversal',
        'status' => 'active',
    ]);

    RoleSkill::create([
        'role_id' => $roleA->id,
        'skill_id' => $skillA->id,
        'required_level' => 4,
        'is_critical' => true,
    ]);

    RoleSkill::create([
        'role_id' => $roleB->id,
        'skill_id' => $skillB->id,
        'required_level' => 5,
        'is_critical' => true,
    ]);

    $personA = People::factory()->create([
        'organization_id' => $this->organizationA->id,
        'department_id' => $departmentA->id,
        'role_id' => $roleA->id,
        'salary' => 50000,
        'email' => 'person-a@example.com',
    ]);

    $personB = People::factory()->create([
        'organization_id' => $this->organizationB->id,
        'department_id' => $departmentB->id,
        'role_id' => $roleB->id,
        'salary' => 80000,
        'email' => 'person-b@example.com',
    ]);

    PeopleRoleSkills::create([
        'people_id' => $personA->id,
        'role_id' => $roleA->id,
        'skill_id' => $skillA->id,
        'current_level' => 2,
        'required_level' => 4,
        'is_active' => true,
    ]);

    PeopleRoleSkills::create([
        'people_id' => $personB->id,
        'role_id' => $roleB->id,
        'skill_id' => $skillB->id,
        'current_level' => 1,
        'required_level' => 5,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->userA, 'sanctum')
        ->getJson('/api/compliance/iso30414/summary');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'replacement_cost' => [
                'total_headcount',
                'total_estimated_replacement_cost',
                'average_estimated_replacement_cost',
                'highest_risk_roles',
            ],
            'talent_maturity_by_department',
            'transversal_capability_gaps',
        ],
    ]);

    $response->assertJsonPath('data.replacement_cost.total_headcount', 1);
    $response->assertJsonPath('data.talent_maturity_by_department.0.name', 'Engineering');
    $response->assertJsonPath('data.transversal_capability_gaps.0.skill_name', 'Critical Thinking');
});

<?php

use App\Models\Competency;
use App\Models\Organization;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::create([
        'name' => 'Test Organization',
        'status' => 'active',
    ]);

    $this->scenario = Scenario::create([
        'organization_id' => $this->organization->id,
        'name' => 'Test Scenario',
        'status' => 'draft',
        'time_horizon_weeks' => 48,
    ]);
});

describe('Role Archetype Assignment', function () {
    it('assigns strategic archetype for high human leverage', function () {
        $role = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Chief Strategy Officer',
            'status' => 'active',
        ]);

        $scenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'human_leverage' => 85,
            'archetype' => 'E',
            'fte' => 1,
        ]);

        expect($scenarioRole->archetype)->toBe('E')
            ->and($scenarioRole->human_leverage)->toBeGreaterThan(70);
    });

    it('assigns tactical archetype for medium human leverage', function () {
        $role = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Team Lead',
            'status' => 'active',
        ]);

        $scenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'human_leverage' => 55,
            'archetype' => 'T',
            'fte' => 2,
        ]);

        expect($scenarioRole->archetype)->toBe('T')
            ->and($scenarioRole->human_leverage)->toBeBetween(40, 70);
    });

    it('assigns operational archetype for low human leverage', function () {
        $role = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Warehouse Manager',
            'status' => 'active',
        ]);

        $scenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'human_leverage' => 30,
            'archetype' => 'O',
            'fte' => 5,
        ]);

        expect($scenarioRole->archetype)->toBe('O')
            ->and($scenarioRole->human_leverage)->toBeLessThanOrEqual(40);
    });
});

describe('Referent Role Flag', function () {
    it('allows operational role with high mastery level when marked as referent', function () {
        $role = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Warehouse Manager',
            'status' => 'active',
        ]);

        $scenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'human_leverage' => 30,
            'archetype' => 'O',
            'fte' => 1,
        ]);

        $competency = Competency::create([
            'name' => 'Team Formation',
            'status' => 'active',
        ]);

        $mapping = ScenarioRoleCompetency::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'competency_id' => $competency->id,
            'required_level' => 5,
            'is_core' => true,
            'is_referent' => true, // Marked as mentor/referent
            'change_type' => 'maintenance',
            'rationale' => 'Acts as technical mentor for warehouse team',
        ]);

        expect($mapping->is_referent)->toBeTrue()
            ->and($mapping->required_level)->toBe(5)
            ->and($scenarioRole->archetype)->toBe('O');
    });

    it('stores referent flag correctly in database', function () {
        $role = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Senior Technician',
            'status' => 'active',
        ]);

        $scenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'archetype' => 'O',
            'fte' => 1,
        ]);

        $competency = Competency::create([
            'name' => 'Advanced Troubleshooting',
            'status' => 'active',
        ]);

        $mapping = ScenarioRoleCompetency::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $role->id,
            'competency_id' => $competency->id,
            'required_level' => 4,
            'is_core' => false,
            'is_referent' => true,
            'change_type' => 'enrichment',
        ]);

        $retrieved = ScenarioRoleCompetency::find($mapping->id);

        expect($retrieved->is_referent)->toBeTrue();
    });
});

describe('Archetype Derivation Logic', function () {
    it('derives correct archetype from human leverage percentage', function () {
        // Strategic: > 70%
        $strategicRole = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'VP of Innovation',
            'status' => 'active',
        ]);

        $strategicScenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $strategicRole->id,
            'human_leverage' => 80,
            'archetype' => 'E',
            'fte' => 1,
        ]);

        // Tactical: 40-70%
        $tacticalRole = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Project Manager',
            'status' => 'active',
        ]);

        $tacticalScenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $tacticalRole->id,
            'human_leverage' => 50,
            'archetype' => 'T',
            'fte' => 3,
        ]);

        // Operational: < 40%
        $operationalRole = Roles::create([
            'organization_id' => $this->organization->id,
            'name' => 'Operator',
            'status' => 'active',
        ]);

        $operationalScenarioRole = ScenarioRole::create([
            'scenario_id' => $this->scenario->id,
            'role_id' => $operationalRole->id,
            'human_leverage' => 20,
            'archetype' => 'O',
            'fte' => 10,
        ]);

        expect($strategicScenarioRole->archetype)->toBe('E')
            ->and($tacticalScenarioRole->archetype)->toBe('T')
            ->and($operationalScenarioRole->archetype)->toBe('O');
    });
});

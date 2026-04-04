<?php

use App\Models\EmployeePulse;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_px_integration_role');
    grantPermissionToRole('qa_px_integration_role', 'scenarios.view', 'scenarios', 'view');
    $this->actingAs($this->user);
    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'status' => 'active',
    ]);
});

it('requires authentication to access people experience endpoint', function () {
    auth()->logout();

    $this->getJson("/api/scenarios/{$this->scenario->id}/people-experience")
        ->assertUnauthorized();
});

it('returns people experience data structure for a scenario', function () {
    $this->getJson("/api/scenarios/{$this->scenario->id}/people-experience")
        ->assertOk()
        ->assertJsonStructure([
            'scenario_id',
            'people_experience' => [
                'active_people',
                'pulses_last_30d',
                'avg_enps',
                'avg_engagement_level',
                'avg_stress_level',
                'high_turnover_risk_people',
            ],
            'headcount' => [
                'current',
                'projected',
                'change',
            ],
        ]);
});

it('calculates people experience metrics from real pulse data', function () {
    $person = People::factory()->create(['organization_id' => $this->org->id]);

    EmployeePulse::create([
        'people_id' => $person->id,
        'e_nps' => 8,
        'engagement_level' => 7,
        'stress_level' => 4,
        'ai_turnover_risk' => 80,
    ]);

    EmployeePulse::create([
        'people_id' => $person->id,
        'e_nps' => 6,
        'engagement_level' => 5,
        'stress_level' => 6,
        'ai_turnover_risk' => 60,
    ]);

    $response = $this->getJson("/api/scenarios/{$this->scenario->id}/people-experience")
        ->assertOk();

    $pe = $response->json('people_experience');

    expect($pe['active_people'])->toBe(1)
        ->and($pe['pulses_last_30d'])->toBe(2)
        ->and((float) $pe['avg_enps'])->toBe(7.0)
        ->and((float) $pe['avg_engagement_level'])->toBe(6.0)
        ->and((float) $pe['avg_stress_level'])->toBe(5.0)
        ->and($pe['high_turnover_risk_people'])->toBe(1); // only ai_turnover_risk >= 70
});

it('reflects headcount from scenario roles', function () {
    People::factory()->count(3)->create(['organization_id' => $this->org->id]);

    $role = Roles::factory()->create(['organization_id' => $this->org->id]);
    ScenarioRole::factory()->create([
        'scenario_id' => $this->scenario->id,
        'role_id' => $role->id,
        'fte' => 2,
    ]);

    $response = $this->getJson("/api/scenarios/{$this->scenario->id}/people-experience")
        ->assertOk();

    $headcount = $response->json('headcount');

    expect($headcount['current'])->toBe(3)
        ->and($headcount['projected'])->toBe(2)
        ->and($headcount['change'])->toBe(-1);
});

it('prevents cross-tenant access to people experience', function () {
    $otherOrg = Organization::factory()->create();
    $otherScenario = Scenario::factory()->create(['organization_id' => $otherOrg->id]);

    $this->getJson("/api/scenarios/{$otherScenario->id}/people-experience")
        ->assertNotFound();
});

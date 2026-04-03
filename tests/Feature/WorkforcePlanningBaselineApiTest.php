<?php

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceDemandLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const BASELINE_SUMMARY_ENDPOINT = '/api/strategic-planning/workforce-planning/baseline/summary';
const WORKFORCE_THRESHOLDS_ENDPOINT = '/api/strategic-planning/workforce-planning/thresholds';
const COMPARE_BASELINE_PATTERN = '/api/strategic-planning/workforce-planning/scenarios/%d/compare-baseline';
const ANALYZE_SCENARIO_PATTERN = '/api/strategic-planning/workforce-planning/scenarios/%d/analyze';
const COMPARE_BASELINE_IMPACT_PATTERN = '/api/strategic-planning/workforce-planning/scenarios/%d/compare-baseline-impact';
const OPERATIONAL_SENSITIVITY_PATTERN = '/api/strategic-planning/workforce-planning/scenarios/%d/operational-sensitivity';

it('requires authentication for workforce thresholds endpoint', function () {
    $this->getJson(WORKFORCE_THRESHOLDS_ENDPOINT)->assertUnauthorized();
});

it('returns configured workforce thresholds for authenticated user', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_THRESHOLDS_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.coverage.success_min', 100)
        ->assertJsonPath('data.coverage.warning_min', 90)
        ->assertJsonPath('data.gap.warning_max_pct', 10);
});

it('updates workforce thresholds for authenticated user organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'hr_leader');

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_THRESHOLDS_ENDPOINT, [
        'coverage' => [
            'success_min' => 98,
            'warning_min' => 88,
        ],
        'gap' => [
            'warning_max_pct' => 12,
        ],
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.coverage.success_min', 98)
        ->assertJsonPath('data.coverage.warning_min', 88)
        ->assertJsonPath('data.gap.warning_max_pct', 12);

    $organization->refresh();

    expect($organization->workforce_thresholds)
        ->toBeArray()
        ->and($organization->workforce_thresholds['coverage']['success_min'])
        ->toBe(98)
        ->and($organization->workforce_thresholds['coverage']['warning_min'])
        ->toBe(88)
        ->and($organization->workforce_thresholds['gap']['warning_max_pct'])
        ->toBe(12);

    $audit = AuditLog::query()
        ->where('organization_id', $organization->id)
        ->where('entity_type', 'WorkforceThresholds')
        ->where('entity_id', (string) $organization->id)
        ->latest('id')
        ->first();

    expect($audit)->not->toBeNull()
        ->and($audit?->user_id)->toBe($user->id)
        ->and($audit?->action)->toBe('updated')
        ->and($audit?->triggered_by)->toBe('api')
        ->and($audit?->changes['workforce_thresholds'][0]['coverage']['success_min'] ?? null)->toBe(100)
        ->and($audit?->changes['workforce_thresholds'][0]['coverage']['warning_min'] ?? null)->toBe(90)
        ->and($audit?->changes['workforce_thresholds'][0]['gap']['warning_max_pct'] ?? null)->toBe(10)
        ->and($audit?->changes['workforce_thresholds'][1]['coverage']['success_min'] ?? null)->toBe(98)
        ->and($audit?->changes['workforce_thresholds'][1]['coverage']['warning_min'] ?? null)->toBe(88)
        ->and($audit?->changes['workforce_thresholds'][1]['gap']['warning_max_pct'] ?? null)->toBe(12);
});

it('forbids updating workforce thresholds for non-admin non-hr-leader roles', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_THRESHOLDS_ENDPOINT, [
        'coverage' => [
            'success_min' => 98,
            'warning_min' => 88,
        ],
        'gap' => [
            'warning_max_pct' => 12,
        ],
    ])->assertForbidden();
});

it('requires authentication for workforce baseline summary endpoint', function () {
    $this->getJson(BASELINE_SUMMARY_ENDPOINT)->assertUnauthorized();
});

it('returns 422 when organization cannot be resolved for workforce baseline summary', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $user->update(['organization_id' => null, 'current_organization_id' => null]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(BASELINE_SUMMARY_ENDPOINT)
        ->assertStatus(422)
        ->assertJsonPath('message', 'No se pudo resolver organization_id para Workforce Planning');
});

it('returns baseline summary for authenticated user organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
        'kpis' => [
            'required_hh' => 1200,
            'effective_hh' => 900,
        ],
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(BASELINE_SUMMARY_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.planning_context', 'baseline')
        ->assertJsonPath('data.required_hh', 1200)
        ->assertJsonPath('data.effective_hh', 900)
        ->assertJsonPath('data.coverage_pct', 75)
        ->assertJsonPath('data.gap_hh', 300);
});

it('requires authentication for compare baseline endpoint', function () {
    $this->postJson(sprintf(COMPARE_BASELINE_PATTERN, 1))->assertUnauthorized();
});

it('returns 404 when comparing scenario from another organization', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($orgA, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $orgB->id]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(COMPARE_BASELINE_PATTERN, $scenario->id))
        ->assertNotFound();
});

it('returns baseline comparison deltas for scenario in same organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $baseline = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
        'kpis' => [
            'required_hh' => 1000,
            'effective_hh' => 850,
        ],
    ]);

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
        'kpis' => [
            'required_hh' => 1300,
            'effective_hh' => 910,
        ],
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(COMPARE_BASELINE_PATTERN, $scenario->id))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.scenario.scenario_id', $scenario->id)
        ->assertJsonPath('data.baseline.scenario_id', $baseline->id)
        ->assertJsonPath('data.delta.delta_gap_hh', 240)
        ->assertJsonPath('data.delta.delta_coverage_pct', -15);
});

it('supports planning_context baseline in analyze endpoint', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $baseline = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
        'kpis' => [
            'required_hh' => 1000,
            'effective_hh' => 900,
        ],
    ]);

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
        'kpis' => [
            'required_hh' => 1300,
            'effective_hh' => 910,
        ],
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(ANALYZE_SCENARIO_PATTERN, $scenario->id), [
        'planning_context' => 'baseline',
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.planning_context', 'baseline')
        ->assertJsonPath('data.requested_scenario_id', $scenario->id)
        ->assertJsonPath('data.analyzed_scenario_id', $baseline->id)
        ->assertJsonPath('data.summary.required_hh', 1000)
        ->assertJsonPath('data.summary.effective_hh', 900);
});

it('uses demand lines metrics in analyze endpoint when available', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
        'kpis' => [
            'required_hh' => 9999,
            'effective_hh' => 1111,
        ],
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'volume_expected' => 600,
        'time_standard_minutes' => 30,
        'coverage_target_pct' => 100,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'volume_expected' => 300,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 100,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(ANALYZE_SCENARIO_PATTERN, $scenario->id), [
        'planning_context' => 'scenario',
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.analyzed_scenario_id', $scenario->id)
        ->assertJsonPath('data.summary.required_hh', 600)
        ->assertJsonPath('data.summary.effective_hh', 600)
        ->assertJsonPath('data.summary.coverage_pct', 100)
        ->assertJsonPath('data.summary.gap_hh', 0)
        ->assertJsonPath('data.summary.gap_fte', 0);
});

it('uses demand lines metrics in baseline summary when baseline has lines', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $baseline = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
        'kpis' => [
            'required_hh' => 5000,
            'effective_hh' => 1000,
        ],
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $baseline->id,
        'volume_expected' => 300,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 90,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(BASELINE_SUMMARY_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.scenario_id', $baseline->id)
        ->assertJsonPath('data.required_hh', 300)
        ->assertJsonPath('data.effective_hh', 270)
        ->assertJsonPath('data.coverage_pct', 90)
        ->assertJsonPath('data.gap_hh', 30);
});

it('returns impact deltas for compare baseline impact endpoint', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $baseline = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
        'estimated_budget' => 100000,
        'kpis' => [
            'risk_score' => 3,
        ],
    ]);

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
        'estimated_budget' => 135000,
        'kpis' => [
            'risk_score' => 6,
        ],
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(COMPARE_BASELINE_IMPACT_PATTERN, $scenario->id))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.baseline_scenario_id', $baseline->id)
        ->assertJsonPath('data.delta.delta_cost_estimate', 35000)
        ->assertJsonPath('data.delta.delta_risk_score', 3)
        ->assertJsonPath('data.delta.delta_risk_level', 'higher');
});

it('applies custom impact parameters for compare baseline impact endpoint', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $baseline = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
        'estimated_budget' => 100000,
        'kpis' => [
            'risk_score' => 3,
        ],
    ]);

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
        'estimated_budget' => 120000,
        'kpis' => [
            'risk_score' => 4,
        ],
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $baseline->id,
        'volume_expected' => 300,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 95,
        'productivity_factor' => 1,
        'attrition_pct' => 5,
        'ramp_factor' => 1,
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'volume_expected' => 300,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 80,
        'productivity_factor' => 1,
        'attrition_pct' => 15,
        'ramp_factor' => 0.85,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(COMPARE_BASELINE_IMPACT_PATTERN, $scenario->id), [
        'impact_parameters' => [
            'cost_per_gap_hh' => 100,
            'cost_risk_multiplier' => 1000,
            'risk_base_offset' => 1,
            'risk_weight_gap_pct' => 1,
            'risk_weight_attrition_pct' => 1,
            'risk_weight_ramp_gap' => 1,
        ],
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.impact_parameters.cost_per_gap_hh', 100)
        ->assertJsonPath('data.scenario.risk_score', 8.59)
        ->assertJsonPath('data.scenario.risk_level', 'critical')
        ->assertJsonPath('data.baseline.risk_score', 5)
        ->assertJsonPath('data.delta.delta_risk_score', 3.59)
        ->assertJsonPath('data.scenario.cost_estimate', 130355)
        ->assertJsonPath('data.baseline.cost_estimate', 106500)
        ->assertJsonPath('data.delta.delta_cost_estimate', 23855)
        ->assertJsonPath('data.delta.delta_risk_level', 'higher');
});

it('requires authentication for operational sensitivity endpoint', function () {
    $this->postJson(sprintf(OPERATIONAL_SENSITIVITY_PATTERN, 1), [
        'adjustments' => [
            'productivity_factor' => 1.1,
        ],
    ])->assertUnauthorized();
});

it('returns operational what-if impact for productivity coverage and ramp adjustments', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'volume_expected' => 600,
        'time_standard_minutes' => 30,
        'coverage_target_pct' => 100,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'volume_expected' => 300,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 100,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(sprintf(OPERATIONAL_SENSITIVITY_PATTERN, $scenario->id), [
        'adjustments' => [
            'productivity_factor' => 1.25,
            'coverage_target_pct' => 90,
            'ramp_factor' => 1.1,
            'cost_per_gap_hh' => 100,
        ],
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.scenario_id', $scenario->id)
        ->assertJsonPath('data.baseline.required_hh', 600)
        ->assertJsonPath('data.baseline.effective_hh', 600)
        ->assertJsonPath('data.baseline.gap_hh', 0)
        ->assertJsonPath('data.baseline.gap_cost_estimate', 0)
        ->assertJsonPath('data.simulated.required_hh', 600)
        ->assertJsonPath('data.simulated.effective_hh', 392.73)
        ->assertJsonPath('data.simulated.coverage_pct', 65.46)
        ->assertJsonPath('data.simulated.gap_hh', 207.27)
        ->assertJsonPath('data.simulated.gap_fte', 1.26)
        ->assertJsonPath('data.simulated.gap_cost_estimate', 20727)
        ->assertJsonPath('data.delta.delta_gap_hh', 207.27)
        ->assertJsonPath('data.delta.delta_gap_cost_estimate', 20727)
        ->assertJsonPath('data.delta.delta_coverage_pct', -34.54);
});

<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceDemandLine;
use App\Services\ScenarioComparatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const FASE4_WFP_BASE    = '/api/strategic-planning/workforce-planning/scenarios/';
const FASE4_COMPARE_URL = '/api/strategic-planning/workforce-planning/scenarios/compare-multi';

beforeEach(function () {
    $this->org  = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_fase4_test');
    Sanctum::actingAs($this->user, ['*']);

    $this->scenarioA = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name'            => 'Escenario Optimista',
        'status'          => 'draft',
    ]);

    $this->scenarioB = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name'            => 'Escenario Conservador',
        'status'          => 'draft',
    ]);
});

// ── ScenarioComparatorService unit tests ─────────────────────────────────────

it('compareMulti returns snapshot for each scenario', function () {
    $service  = app(ScenarioComparatorService::class);
    $scenarios = collect([$this->scenarioA, $this->scenarioB]);

    $result = $service->compareMulti($scenarios);

    expect($result['scenarios_count'])->toBe(2);
    expect($result['scenarios'])->toHaveCount(2);
    expect($result['ranking'])->toHaveCount(2);
});

it('ranking assigns rank 1 to the scenario with highest composite score', function () {
    // Create demand lines so coverage differs
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 90,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioB->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 40,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    $service = app(ScenarioComparatorService::class);
    $result  = $service->compareMulti(collect([$this->scenarioA, $this->scenarioB]));

    $rank1 = collect($result['ranking'])->firstWhere('rank', 1);
    expect($rank1['scenario_id'])->toBe($this->scenarioA->id);
});

it('snapshot includes lever_distribution and action_summary fields', function () {
    $service  = app(ScenarioComparatorService::class);
    $result   = $service->compareMulti(collect([$this->scenarioA, $this->scenarioB]));

    $snapshot = $result['scenarios'][0];
    expect($snapshot)->toHaveKey('coverage_pct')
        ->toHaveKey('gap_fte')
        ->toHaveKey('budget')
        ->toHaveKey('risk_score')
        ->toHaveKey('lever_distribution')
        ->toHaveKey('action_summary');
});

it('dimensions matrix contains coverage_pct, gap_fte, budget, risk_score', function () {
    $service = app(ScenarioComparatorService::class);
    $result  = $service->compareMulti(collect([$this->scenarioA, $this->scenarioB]));

    expect($result['dimensions'])->toHaveKey('coverage_pct')
        ->toHaveKey('gap_fte')
        ->toHaveKey('budget')
        ->toHaveKey('risk_score');
});

// ── Sensitivity sweep unit tests ─────────────────────────────────────────────

it('sensitivitySweep returns correct number of steps', function () {
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 60,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    $service = app(ScenarioComparatorService::class);
    $result  = $service->sensitivitySweep($this->scenarioA, [
        'variable'        => 'productivity_factor',
        'min'             => 0.5,
        'max'             => 2.0,
        'steps'           => 6,
        'cost_per_gap_hh' => 50,
    ]);

    expect($result['sweep'])->toHaveCount(6);
    expect($result)->toHaveKey('optimal_step')
        ->toHaveKey('interpretation');
});

it('sensitivitySweep kpis include coverage_pct, gap_hh, gap_fte, gap_cost_estimate', function () {
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 8,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 60,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    $service = app(ScenarioComparatorService::class);
    $result  = $service->sensitivitySweep($this->scenarioA, [
        'variable' => 'coverage_target_pct',
        'min'      => 50,
        'max'      => 100,
        'steps'    => 5,
    ]);

    $step = $result['sweep'][0];
    expect($step['kpis'])->toHaveKey('coverage_pct')
        ->toHaveKey('gap_hh')
        ->toHaveKey('gap_fte')
        ->toHaveKey('gap_cost_estimate');
});

it('coverage increases as coverage_target_pct increases in sweep', function () {
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 50,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    $service = app(ScenarioComparatorService::class);
    $result  = $service->sensitivitySweep($this->scenarioA, [
        'variable' => 'coverage_target_pct',
        'min'      => 50,
        'max'      => 100,
        'steps'    => 5,
    ]);

    $coverages = array_column(array_column($result['sweep'], 'kpis'), 'coverage_pct');
    // Ascending trend
    expect($coverages[4])->toBeGreaterThan($coverages[0]);
});

it('optimal step has highest coverage at lowest cost', function () {
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 70,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    $service = app(ScenarioComparatorService::class);
    $result  = $service->sensitivitySweep($this->scenarioA, [
        'variable' => 'productivity_factor',
        'min'      => 0.5,
        'max'      => 2.0,
        'steps'    => 8,
    ]);

    $optimal = $result['optimal_step'];
    expect($optimal)->toHaveKey('value')
        ->toHaveKey('kpis');

    $maxCoverage = max(array_column(array_column($result['sweep'], 'kpis'), 'coverage_pct'));
    expect($optimal['kpis']['coverage_pct'])->toBeGreaterThanOrEqual($maxCoverage - 1.5);
});

// ── API tests ────────────────────────────────────────────────────────────────

it('POST compare-multi returns 200 with comparison result', function () {
    $response = $this->postJson(FASE4_COMPARE_URL, [
        'scenario_ids' => [$this->scenarioA->id, $this->scenarioB->id],
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'scenarios_count',
                'scenarios',
                'ranking',
                'dimensions',
            ],
        ]);

    expect($response->json('data.scenarios_count'))->toBe(2);
});

it('POST compare-multi requires at least 2 scenario_ids', function () {
    $response = $this->postJson(FASE4_COMPARE_URL, [
        'scenario_ids' => [$this->scenarioA->id],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['scenario_ids']);
});

it('POST compare-multi rejects scenarios from another organization', function () {
    $otherOrg = Organization::factory()->create();
    $foreignScenario = Scenario::factory()->create(['organization_id' => $otherOrg->id]);

    $response = $this->postJson(FASE4_COMPARE_URL, [
        'scenario_ids' => [$this->scenarioA->id, $foreignScenario->id],
    ]);

    // Foreign scenario filtered out → only 1 valid → 422 from controller
    $response->assertStatus(422);
});

it('POST sensitivity-sweep returns 200 with sweep results', function () {
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 60,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    $response = $this->postJson(FASE4_WFP_BASE.$this->scenarioA->id.'/sensitivity-sweep', [
        'variable'        => 'productivity_factor',
        'min'             => 0.5,
        'max'             => 2.0,
        'steps'           => 5,
        'cost_per_gap_hh' => 40,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'scenario_id',
                'variable',
                'range',
                'sweep',
                'optimal_step',
                'interpretation',
            ],
        ]);

    expect($response->json('data.scenario_id'))->toBe($this->scenarioA->id);
    expect($response->json('data.sweep'))->toHaveCount(5);
});

it('POST sensitivity-sweep rejects invalid variable', function () {
    $response = $this->postJson(FASE4_WFP_BASE.$this->scenarioA->id.'/sensitivity-sweep', [
        'variable' => 'invalid_var',
        'min'      => 0.5,
        'max'      => 2.0,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['variable']);
});

it('POST sensitivity-sweep returns 404 for scenario from another org', function () {
    $otherOrg = Organization::factory()->create();
    $foreign  = Scenario::factory()->create(['organization_id' => $otherOrg->id]);

    $response = $this->postJson(FASE4_WFP_BASE.$foreign->id.'/sensitivity-sweep', [
        'variable' => 'productivity_factor',
        'min'      => 0.5,
        'max'      => 2.0,
    ]);

    $response->assertStatus(404);
});

// ── Full Fase 4 E2E ──────────────────────────────────────────────────────────

it('full E2E: compare 3 scenarios + sweep to find optimal productivity', function () {
    $scenarioC = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'name'            => 'Escenario Agresivo',
        'status'          => 'draft',
    ]);

    // Create demand lines for ScenarioA with higher coverage
    WorkforceDemandLine::factory()->create([
        'scenario_id'           => $this->scenarioA->id,
        'organization_id'       => $this->org->id,
        'volume_expected'       => 10,
        'time_standard_minutes' => 60,
        'coverage_target_pct'   => 85,
        'productivity_factor'   => 1.0,
        'ramp_factor'           => 1.0,
    ]);

    // 1. Compare 3 scenarios
    $compareResponse = $this->postJson(FASE4_COMPARE_URL, [
        'scenario_ids' => [$this->scenarioA->id, $this->scenarioB->id, $scenarioC->id],
    ]);

    $compareResponse->assertOk();
    expect($compareResponse->json('data.scenarios_count'))->toBe(3);
    expect($compareResponse->json('data.ranking.0.scenario_id'))->toBe($this->scenarioA->id);

    // 2. Sweep on best scenario
    $sweepResponse = $this->postJson(FASE4_WFP_BASE.$this->scenarioA->id.'/sensitivity-sweep', [
        'variable' => 'productivity_factor',
        'min'      => 0.8,
        'max'      => 1.5,
        'steps'    => 4,
    ]);

    $sweepResponse->assertOk();
    expect($sweepResponse->json('data.optimal_step.value'))->toBeFloat();
    expect($sweepResponse->json('data.interpretation'))->toBeString()->not->toBeEmpty();
});

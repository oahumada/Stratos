<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const FASE3_SCENARIOS_BASE = '/api/strategic-planning/workforce-planning/scenarios/';

beforeEach(function () {
    $this->org  = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_fase3_test');
    Sanctum::actingAs($this->user, ['*']);

    $this->scenario = Scenario::factory()->create([
        'organization_id' => $this->org->id,
        'status'          => 'draft',
    ]);
});

// ── Budget tracking ──────────────────────────────────────────────────────────

it('stores action plan with budget and actual_cost fields', function () {
    $response = $this->postJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/action-plan', [
        'action_title' => 'Contratar 3 backend engineers',
        'lever'        => 'HIRE',
        'unit_name'    => 'Producto Digital',
        'budget'       => 150000,
        'actual_cost'  => 0,
        'due_date'     => now()->addMonths(3)->toDateString(),
        'priority'     => 'high',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.action.lever', 'HIRE')
        ->assertJsonPath('data.action.unit_name', 'Producto Digital');

    $this->assertDatabaseHas('workforce_action_plans', [
        'scenario_id' => $this->scenario->id,
        'lever'       => 'HIRE',
        'unit_name'   => 'Producto Digital',
    ]);
});

it('stores action plan with hybrid_coverage_pct', function () {
    $response = $this->postJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/action-plan', [
        'action_title'        => 'Automatizar análisis de reportes con IA',
        'lever'               => 'HYBRID_TALENT',
        'hybrid_coverage_pct' => 60,
        'budget'              => 12000,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.action.hybrid_coverage_pct', 60)
        ->assertJsonPath('data.action.lever', 'HYBRID_TALENT');
});

it('updates action plan actual_cost and lever', function () {
    $action = WorkforceActionPlan::factory()->create([
        'scenario_id'     => $this->scenario->id,
        'organization_id' => $this->org->id,
        'budget'          => 50000,
        'actual_cost'     => 0,
        'lever'           => 'RESKILL',
    ]);

    $response = $this->patchJson(
        FASE3_SCENARIOS_BASE.$this->scenario->id.'/action-plan/'.$action->id,
        ['actual_cost' => 38000, 'status' => 'in_progress']
    );

    $response->assertOk()
        ->assertJsonPath('data.action.lever', 'RESKILL');

    $this->assertDatabaseHas('workforce_action_plans', [
        'id' => $action->id,
        'status' => 'in_progress',
    ]);
});

it('rejects invalid lever value', function () {
    $response = $this->postJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/action-plan', [
        'action_title' => 'Test action',
        'lever'        => 'INVALID_LEVER',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['lever']);
});

// ── Execution dashboard — semáforo + alertas + budget + hybrid ───────────────

it('execution dashboard returns risk_semaphore field', function () {
    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'risk_semaphore',
                'alerts',
                'budget',
                'hybrid_coverage_pct',
                'by_lever',
                'by_unit',
            ],
        ]);
});

it('risk semaphore is verde when no actions exist', function () {
    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');

    $response->assertOk()
        ->assertJsonPath('data.risk_semaphore', 'verde');
});

it('risk semaphore is rojo when many actions are overdue', function () {
    // Crear 5 acciones, 3 vencidas (>30% overdue → rojo)
    foreach (range(1, 5) as $i) {
        WorkforceActionPlan::factory()->create([
            'scenario_id'     => $this->scenario->id,
            'organization_id' => $this->org->id,
            'status'          => 'in_progress',
            'due_date'        => $i <= 3
                ? now()->subDays($i * 5)->toDateString()   // 3 vencidas
                : now()->addMonths(1)->toDateString(),
            'progress_pct'    => 15, // bajo progreso también
        ]);
    }

    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');

    $response->assertOk()
        ->assertJsonPath('data.risk_semaphore', 'rojo');
});

it('risk semaphore is verde when all actions are on track', function () {
    WorkforceActionPlan::factory()->count(3)->create([
        'scenario_id'     => $this->scenario->id,
        'organization_id' => $this->org->id,
        'status'          => 'in_progress',
        'due_date'        => now()->addMonths(2)->toDateString(),
        'progress_pct'    => 70,
    ]);

    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');

    $response->assertOk()
        ->assertJsonPath('data.risk_semaphore', 'verde');
});

it('execution dashboard includes budget variance when actions have budget', function () {
    WorkforceActionPlan::factory()->create([
        'scenario_id'     => $this->scenario->id,
        'organization_id' => $this->org->id,
        'budget'          => 100000,
        'actual_cost'     => 130000, // 30% over budget
        'status'          => 'in_progress',
    ]);

    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');

    $response->assertOk();
    expect($response->json('data.budget.over_budget'))->toBeTrue();
    expect((float) $response->json('data.budget.variance_pct'))->toBe(30.0);
});

it('execution dashboard shows hybrid coverage percentage', function () {
    WorkforceActionPlan::factory()->create([
        'scenario_id'         => $this->scenario->id,
        'organization_id'     => $this->org->id,
        'lever'               => 'HYBRID_TALENT',
        'hybrid_coverage_pct' => 50,
        'status'              => 'in_progress',
    ]);

    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');

    $response->assertOk();
    expect((float) $response->json('data.hybrid_coverage_pct'))->toBe(50.0);
});

it('execution dashboard shows alert when actions are blocked', function () {
    WorkforceActionPlan::factory()->create([
        'scenario_id'     => $this->scenario->id,
        'organization_id' => $this->org->id,
        'status'          => 'blocked',
    ]);

    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');
    $response->assertOk();

    $alerts = $response->json('data.alerts');
    $types  = array_column($alerts, 'type');
    expect($types)->toContain('blocked');
});

it('execution dashboard shows by_lever breakdown', function () {
    WorkforceActionPlan::factory()->create([
        'scenario_id'     => $this->scenario->id,
        'organization_id' => $this->org->id,
        'lever'           => 'RESKILL',
        'status'          => 'in_progress',
        'progress_pct'    => 40,
    ]);

    WorkforceActionPlan::factory()->create([
        'scenario_id'     => $this->scenario->id,
        'organization_id' => $this->org->id,
        'lever'           => 'HIRE',
        'status'          => 'planned',
        'progress_pct'    => 0,
    ]);

    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');
    $response->assertOk();

    $byLever = $response->json('data.by_lever');
    expect($byLever)->toHaveKey('RESKILL');
    expect($byLever)->toHaveKey('HIRE');
});

// ── Full Fase 3 E2E flow ─────────────────────────────────────────────────────

it('full Fase 3 flow: create action with budget + lever → dashboard semáforo + alerts', function () {
    // 1. Crear acción con presupuesto excedido y vencida
    $this->postJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/action-plan', [
        'action_title' => 'Reskilling equipo data',
        'lever'        => 'RESKILL',
        'unit_name'    => 'Analytics',
        'budget'       => 20000,
        'actual_cost'  => 28000, // 40% over budget
        'due_date'     => now()->subWeek()->toDateString(), // vencida
        'status'       => 'in_progress',
        'progress_pct' => 10,
    ])->assertStatus(201);

    // 2. Crear segunda acción bloqueada (sin budget para no afectar la varianza)
    $this->postJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/action-plan', [
        'action_title' => 'Contratar PM senior',
        'lever'        => 'HIRE',
        'unit_name'    => 'Producto',
        'due_date'     => now()->addMonths(2)->toDateString(),
        'status'       => 'blocked',
        'progress_pct' => 0,
    ])->assertStatus(201);

    // 3. Verificar dashboard
    $response = $this->getJson(FASE3_SCENARIOS_BASE.$this->scenario->id.'/execution-dashboard');
    $response->assertOk();

    // Semáforo debe ser rojo (overdue + blocked)
    expect($response->json('data.risk_semaphore'))->toBe('rojo');

    // Debe haber alertas de overdue y blocked
    $alertTypes = array_column($response->json('data.alerts'), 'type');
    expect($alertTypes)->toContain('overdue');
    expect($alertTypes)->toContain('blocked');

    // Budget over
    expect($response->json('data.budget.over_budget'))->toBeTrue();

    // Breakdown por unidad
    $units = array_column($response->json('data.by_unit'), 'unit_name');
    expect($units)->toContain('Analytics');
    expect($units)->toContain('Producto');
});

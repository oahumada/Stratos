<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use App\Models\WorkforceDemandLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const WORKFORCE_E2E_SCENARIO_CREATE_ENDPOINT = '/api/strategic-planning/scenarios';
const WORKFORCE_E2E_THRESHOLDS_ENDPOINT = '/api/strategic-planning/workforce-planning/thresholds';
const WORKFORCE_E2E_MONITORING_ENDPOINT = '/api/strategic-planning/workforce-planning/monitoring/summary';

it('executes the full workforce integration flow end to end', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'hr_leader');

    Sanctum::actingAs($user, ['*']);

    $createScenarioResponse = $this->postJson(WORKFORCE_E2E_SCENARIO_CREATE_ENDPOINT, [
        'name' => 'Workforce P2 Integration Flow',
        'description' => 'Escenario completo para pruebas integradas',
        'planning_horizon_months' => 6,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(6)->toDateString(),
    ]);

    $createScenarioResponse
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Workforce P2 Integration Flow');

    $scenarioId = (int) $createScenarioResponse->json('data.id');
    $scenario = Scenario::query()->findOrFail($scenarioId);

    $this->postJson("/api/strategic-planning/workforce-planning/scenarios/{$scenarioId}/demand-lines", [
        'lines' => [
            [
                'unit' => 'Sales',
                'role_name' => 'Account Executive',
                'period' => '2026-09',
                'volume_expected' => 500,
                'time_standard_minutes' => 60,
                'coverage_target_pct' => 95,
            ],
        ],
    ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.created', 1);

    $this->postJson("/api/strategic-planning/workforce-planning/scenarios/{$scenarioId}/analyze", [
        'planning_context' => 'scenario',
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.analyzed_scenario_id', $scenarioId)
        ->assertJsonPath('data.summary.required_hh', 500)
        ->assertJsonPath('data.summary.effective_hh', 475);

    $this->patchJson(WORKFORCE_E2E_THRESHOLDS_ENDPOINT, [
        'coverage' => [
            'success_min' => 97,
            'warning_min' => 85,
        ],
        'gap' => [
            'warning_max_pct' => 14,
        ],
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.coverage.success_min', 97)
        ->assertJsonPath('data.gap.warning_max_pct', 14);

    $this->postJson("/api/strategic-planning/workforce-planning/scenarios/{$scenarioId}/action-plan", [
        'action_title' => 'Capacitar célula comercial',
        'description' => 'Plan intensivo para cobertura Q3',
        'owner_user_id' => $user->id,
        'priority' => 'high',
        'status' => 'planned',
        'progress_pct' => 0,
        'due_date' => now()->addDays(10)->toDateString(),
    ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.action.action_title', 'Capacitar célula comercial');

    $action = WorkforceActionPlan::query()
        ->where('scenario_id', $scenarioId)
        ->latest('id')
        ->firstOrFail();

    $this->patchJson("/api/strategic-planning/workforce-planning/scenarios/{$scenarioId}/action-plan/{$action->id}", [
        'status' => 'in_progress',
        'progress_pct' => 40,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.action.status', 'in_progress')
        ->assertJsonPath('data.action.progress_pct', 40);

    $this->getJson("/api/strategic-planning/workforce-planning/scenarios/{$scenarioId}/execution-dashboard")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.summary.total_actions', 1)
        ->assertJsonPath('data.summary.in_progress_actions', 1)
        ->assertJsonPath('data.summary.avg_progress_pct', 40);

    $this->patchJson("/api/strategic-planning/workforce-planning/scenarios/{$scenarioId}/status", [
        'status' => 'in_review',
    ])
        ->assertOk()
        ->assertJsonPath('data.previous_status', 'draft')
        ->assertJsonPath('data.status', 'in_review');

    $this->getJson(WORKFORCE_E2E_MONITORING_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.usage_by_tenant.organization_id', $organization->id)
        ->assertJsonPath('data.usage_by_tenant.scenarios_total', 1)
        ->assertJsonPath('data.usage_by_tenant.demand_lines_total', 1)
        ->assertJsonPath('data.usage_by_tenant.action_plans_total', 1);

    expect($scenario->fresh()->status)->toBe('in_review')
        ->and(WorkforceDemandLine::query()->where('scenario_id', $scenarioId)->count())->toBe(1)
        ->and(WorkforceActionPlan::query()->where('scenario_id', $scenarioId)->count())->toBe(1);
});

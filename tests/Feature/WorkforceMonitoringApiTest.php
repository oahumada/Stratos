<?php

use App\Models\AuditLog;
use App\Models\ChangeSet;
use App\Models\IntelligenceMetricAggregate;
use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use App\Models\WorkforceDemandLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const WORKFORCE_MONITORING_SUMMARY_ENDPOINT = '/api/strategic-planning/workforce-planning/monitoring/summary';
const WORKFORCE_ENTERPRISE_SUMMARY_ENDPOINT = '/api/strategic-planning/workforce-planning/enterprise/summary';

it('requires authentication for workforce monitoring summary endpoint', function () {
    $this->getJson(WORKFORCE_MONITORING_SUMMARY_ENDPOINT)->assertUnauthorized();
});

it('returns workforce monitoring summary with tenant usage and operational health metrics', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'hr_leader');

    $scenarioDraft = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'draft',
    ]);

    Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
    ]);

    WorkforceDemandLine::factory()->count(2)->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenarioDraft->id,
    ]);

    WorkforceActionPlan::query()->create([
        'scenario_id' => $scenarioDraft->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción en progreso',
        'status' => 'in_progress',
        'priority' => 'high',
        'progress_pct' => 35,
    ]);

    WorkforceActionPlan::query()->create([
        'scenario_id' => $scenarioDraft->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción completada',
        'status' => 'completed',
        'priority' => 'medium',
        'progress_pct' => 100,
    ]);

    AuditLog::query()->create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
        'action' => 'updated',
        'entity_type' => 'WorkforceThresholds',
        'entity_id' => (string) $organization->id,
        'changes' => [
            'workforce_thresholds' => [
                [
                    'coverage' => ['success_min' => 100, 'warning_min' => 90],
                    'gap' => ['warning_max_pct' => 10],
                ],
                [
                    'coverage' => ['success_min' => 98, 'warning_min' => 88],
                    'gap' => ['warning_max_pct' => 12],
                ],
            ],
        ],
        'metadata' => ['context' => 'workforce_planning.thresholds'],
        'triggered_by' => 'api',
    ]);

    IntelligenceMetricAggregate::query()->create([
        'organization_id' => $organization->id,
        'metric_type' => 'agent',
        'source_type' => 'workforce_planning',
        'date_key' => now()->toDateString(),
        'total_count' => 120,
        'success_count' => 114,
        'success_rate' => 0.95,
        'avg_duration_ms' => 180,
        'p50_duration_ms' => 120,
        'p95_duration_ms' => 420,
        'p99_duration_ms' => 600,
        'avg_confidence' => 0.8,
        'avg_context_count' => 5,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_MONITORING_SUMMARY_ENDPOINT.'?period_days=7')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.usage_by_tenant.organization_id', $organization->id)
        ->assertJsonPath('data.usage_by_tenant.scenarios_total', 2)
        ->assertJsonPath('data.usage_by_tenant.demand_lines_total', 2)
        ->assertJsonPath('data.usage_by_tenant.action_plans_total', 2)
        ->assertJsonPath('data.usage_by_tenant.action_plans_in_progress', 1)
        ->assertJsonPath('data.usage_by_tenant.action_plans_completed', 1)
        ->assertJsonPath('data.usage_by_tenant.threshold_updates_in_window', 1)
        ->assertJsonPath('data.operational_health.latency_ms.available', true)
        ->assertJsonPath('data.operational_health.latency_ms.avg', 180)
        ->assertJsonPath('data.operational_health.latency_ms.p95', 420)
        ->assertJsonPath('data.operational_health.error_rate_pct.available', true)
        ->assertJsonPath('data.operational_health.error_rate_pct.value', 5)
        ->assertJsonPath('data.operational_health.api_status_code_breakdown.available', false);
});

it('returns telemetry unavailable flags when no workforce aggregate metrics exist', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_MONITORING_SUMMARY_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.operational_health.latency_ms.available', false)
        ->assertJsonPath('data.operational_health.error_rate_pct.available', false)
        ->assertJsonPath('data.operational_health.api_status_code_breakdown.available', false);
});

it('requires authentication for workforce enterprise summary endpoint', function () {
    $this->getJson(WORKFORCE_ENTERPRISE_SUMMARY_ENDPOINT)->assertUnauthorized();
});

it('returns enterprise planning summary with transversal portfolio execution and governance signals', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'hr_leader');

    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'active',
    ]);

    Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'in_review',
    ]);

    WorkforceDemandLine::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'volume_expected' => 300,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 90,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    WorkforceActionPlan::query()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción completada',
        'status' => 'completed',
        'priority' => 'high',
        'progress_pct' => 100,
    ]);

    WorkforceActionPlan::query()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción en curso',
        'status' => 'in_progress',
        'priority' => 'medium',
        'progress_pct' => 40,
    ]);

    ChangeSet::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'status' => 'draft',
    ]);

    ChangeSet::factory()->create([
        'organization_id' => $organization->id,
        'scenario_id' => $scenario->id,
        'status' => 'applied',
    ]);

    IntelligenceMetricAggregate::query()->create([
        'organization_id' => $organization->id,
        'metric_type' => 'agent',
        'source_type' => 'workforce_planning',
        'date_key' => now()->toDateString(),
        'total_count' => 100,
        'success_count' => 96,
        'success_rate' => 0.96,
        'avg_duration_ms' => 150,
        'p50_duration_ms' => 100,
        'p95_duration_ms' => 300,
        'p99_duration_ms' => 400,
        'avg_confidence' => 0.8,
        'avg_context_count' => 3,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_ENTERPRISE_SUMMARY_ENDPOINT.'?period_days=30')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.portfolio.scenarios_total', 2)
        ->assertJsonPath('data.portfolio.scenarios_active_or_approved', 1)
        ->assertJsonPath('data.portfolio.scenarios_in_governance_flow', 1)
        ->assertJsonPath('data.workforce_execution.demand_lines_total', 1)
        ->assertJsonPath('data.workforce_execution.required_hh_total', 300)
        ->assertJsonPath('data.workforce_execution.effective_hh_total', 270)
        ->assertJsonPath('data.workforce_execution.coverage_pct', 90)
        ->assertJsonPath('data.workforce_execution.action_plans_total', 2)
        ->assertJsonPath('data.workforce_execution.action_plans_completed', 1)
        ->assertJsonPath('data.workforce_execution.action_completion_pct', 50)
        ->assertJsonPath('data.governance.changesets_total', 2)
        ->assertJsonPath('data.governance.changesets_pending', 1)
        ->assertJsonPath('data.governance.changesets_applied', 1)
        ->assertJsonPath('data.operational_health.success_rate_pct', 96)
        ->assertJsonPath('data.operational_health.error_rate_pct', 4)
        ->assertJsonPath('data.operational_health.avg_latency_ms', 150);
});

<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceActionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const WORKFORCE_SCENARIOS_ENDPOINT = '/api/strategic-planning/workforce-planning/scenarios/';

const ACTION_PLAN_ENDPOINT = '/action-plan';

const EXECUTION_DASHBOARD_ENDPOINT = '/execution-dashboard';

const ACTION_TITLE_HIRING_WAVE = 'Plan de contratación por ola';

it('requires authentication for workforce action plan endpoints', function () {
    $this->getJson(WORKFORCE_SCENARIOS_ENDPOINT.'1'.ACTION_PLAN_ENDPOINT)
        ->assertUnauthorized();

    $this->postJson(WORKFORCE_SCENARIOS_ENDPOINT.'1'.ACTION_PLAN_ENDPOINT, [])
        ->assertUnauthorized();

    $this->patchJson(WORKFORCE_SCENARIOS_ENDPOINT.'1'.ACTION_PLAN_ENDPOINT.'/1', [])
        ->assertUnauthorized();

    $this->getJson(WORKFORCE_SCENARIOS_ENDPOINT.'1'.EXECUTION_DASHBOARD_ENDPOINT)
        ->assertUnauthorized();
});

it('returns 404 for action plan access when scenario belongs to another organization', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();

    $user = createUserForOrganizationWithRole($orgA, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $orgB->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT)
        ->assertNotFound();

    $this->postJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT, [
        'action_title' => 'Habilitar células de cobertura',
    ])->assertNotFound();
});

it('creates and lists workforce action plan items for scenario in same organization', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $organization->id]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT, [
        'action_title' => ACTION_TITLE_HIRING_WAVE,
        'description' => 'Foco en unidades críticas',
        'owner_user_id' => $user->id,
        'status' => 'planned',
        'priority' => 'high',
        'due_date' => now()->addDays(15)->toDateString(),
        'progress_pct' => 10,
    ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.action.action_title', ACTION_TITLE_HIRING_WAVE)
        ->assertJsonPath('data.action.owner_user_id', $user->id);

    $this->getJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.actions.0.action_title', ACTION_TITLE_HIRING_WAVE);
});

it('updates workforce action plan progress and returns execution dashboard summary', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $organization->id]);

    $completed = WorkforceActionPlan::query()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción completada',
        'status' => 'completed',
        'priority' => 'medium',
        'progress_pct' => 100,
    ]);

    $editable = WorkforceActionPlan::query()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción en seguimiento',
        'status' => 'planned',
        'priority' => 'high',
        'progress_pct' => 0,
        'due_date' => now()->subDay()->toDateString(),
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT.'/'.$editable->id, [
        'status' => 'in_progress',
        'progress_pct' => 45,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.action.status', 'in_progress')
        ->assertJsonPath('data.action.progress_pct', 45);

    $this->getJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.EXECUTION_DASHBOARD_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.summary.total_actions', 2)
        ->assertJsonPath('data.summary.completed_actions', 1)
        ->assertJsonPath('data.summary.in_progress_actions', 1)
        ->assertJsonPath('data.summary.blocked_actions', 0)
        ->assertJsonPath('data.summary.overdue_actions', 1)
        ->assertJsonPath('data.summary.avg_progress_pct', 72.5);

    expect($completed->fresh()->status)->toBe('completed');
});

it('returns 409 when creating action plan on archived scenario', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'archived',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT, [
        'action_title' => 'Acción bloqueada por estado',
        'priority' => 'high',
    ])
        ->assertStatus(409)
        ->assertJsonPath('success', false);
});

it('returns 409 when updating action plan on completed scenario', function () {
    $organization = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($organization, 'talent_planner');
    $scenario = Scenario::factory()->create([
        'organization_id' => $organization->id,
        'status' => 'completed',
    ]);

    $action = WorkforceActionPlan::query()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $organization->id,
        'action_title' => 'Acción cerrada',
        'status' => 'planned',
        'priority' => 'medium',
        'progress_pct' => 10,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_SCENARIOS_ENDPOINT.$scenario->id.ACTION_PLAN_ENDPOINT.'/'.$action->id, [
        'status' => 'in_progress',
        'progress_pct' => 30,
    ])
        ->assertStatus(409)
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.scenario_status', 'completed');
});

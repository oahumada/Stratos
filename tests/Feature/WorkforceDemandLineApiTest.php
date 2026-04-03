<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\WorkforceDemandLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const WORKFORCE_DEMAND_SCENARIOS_ENDPOINT = '/api/strategic-planning/workforce-planning/scenarios/';

const WORKFORCE_DEMAND_LINES_ENDPOINT = '/demand-lines';

const WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT = '/demand-lines/';

const DEMAND_PERIOD_2026_07 = '2026-07';

const PERFORMANCE_BATCH_MAX_LINES = 50;

const PERFORMANCE_STORE_MAX_MS = 8000;

const PERFORMANCE_LIST_MAX_MS = 5000;

// ──── Auth guards ────────────────────────────────────────────────────────────

it('requires authentication to list demand lines', function () {
    $this->getJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.'1'.WORKFORCE_DEMAND_LINES_ENDPOINT)
        ->assertUnauthorized();
});

it('requires authentication to store demand lines', function () {
    $this->postJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.'1'.WORKFORCE_DEMAND_LINES_ENDPOINT, [])
        ->assertUnauthorized();
});

it('requires authentication to update demand lines', function () {
    $this->patchJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.'1'.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.'1', [])
        ->assertUnauthorized();
});

it('requires authentication to delete demand lines', function () {
    $this->deleteJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.'1'.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.'1')
        ->assertUnauthorized();
});

// ──── Tenant isolation ───────────────────────────────────────────────────────

it('returns 404 when listing demand lines for scenario from another org', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($orgA, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $orgB->id]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT)
        ->assertNotFound();
});

it('returns 404 when storing demand lines for scenario from another org', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($orgA, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $orgB->id]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT, [
        'lines' => [
            [
                'unit' => 'Sales',
                'role_name' => 'Account Executive',
                'period' => '2026-09',
                'volume_expected' => 500,
                'time_standard_minutes' => 60,
            ],
        ],
    ])->assertNotFound();
});

it('returns 404 when updating demand line from another org scenario', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($orgA, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $orgB->id]);

    $line = WorkforceDemandLine::factory()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $orgB->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.$line->id, [
        'volume_expected' => 777,
    ])->assertNotFound();
});

it('returns 404 when deleting demand line from another org scenario', function () {
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($orgA, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $orgB->id]);

    $line = WorkforceDemandLine::factory()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $orgB->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->deleteJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.$line->id)
        ->assertNotFound();
});

// ──── Validation ─────────────────────────────────────────────────────────────

it('validates that lines array cannot be empty', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT, [
        'lines' => [],
    ])->assertUnprocessable();
});

it('validates period format when storing demand lines', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT, [
        'lines' => [
            [
                'unit' => 'Sales',
                'role_name' => 'AE',
                'period' => '09-2026',
                'volume_expected' => 100,
                'time_standard_minutes' => 60,
            ],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrorFor('lines.0.period');
});

it('validates period format when updating demand lines', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);
    $line = WorkforceDemandLine::factory()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $org->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.$line->id, [
        'period' => '09-2026',
    ])->assertUnprocessable()
        ->assertJsonValidationErrorFor('period');
});

// ──── Happy paths ─────────────────────────────────────────────────────────────

it('stores demand lines and returns computed hh values', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT, [
        'lines' => [
            [
                'unit' => 'Operations',
                'role_name' => 'Analyst',
                'period' => DEMAND_PERIOD_2026_07,
                'volume_expected' => 600,
                'time_standard_minutes' => 30,
                'productivity_factor' => 1.0,
                'coverage_target_pct' => 100.0,
            ],
        ],
    ]);

    // 600 vol × 30 min / 60 = 300 HH brutas
    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.created', 1)
        ->assertJsonPath('data.scenario_id', $scenario->id)
        ->assertJsonPath('data.lines.0.required_hh', 300);

    $this->assertDatabaseHas('workforce_demand_lines', [
        'scenario_id' => $scenario->id,
        'unit' => 'Operations',
        'role_name' => 'Analyst',
        'period' => '2026-07',
    ]);
});

it('returns 409 when storing demand lines for archived scenario', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create([
        'organization_id' => $org->id,
        'status' => 'archived',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->postJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT, [
        'lines' => [
            [
                'unit' => 'Operations',
                'role_name' => 'Analyst',
                'period' => DEMAND_PERIOD_2026_07,
                'volume_expected' => 600,
                'time_standard_minutes' => 30,
            ],
        ],
    ])->assertStatus(409)
        ->assertJsonPath('success', false);
});

it('returns 409 when updating demand line for completed scenario', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create([
        'organization_id' => $org->id,
        'status' => 'completed',
    ]);

    $line = WorkforceDemandLine::factory()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $org->id,
        'period' => DEMAND_PERIOD_2026_07,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(
        WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.$line->id,
        [
            'volume_expected' => 999,
        ]
    )
        ->assertStatus(409)
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.scenario_status', 'completed');
});

it('lists all demand lines for a scenario with computed hh', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    WorkforceDemandLine::factory()->count(3)->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $org->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->getJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.total', 3)
        ->assertJsonPath('data.scenario_id', $scenario->id);
});

it('updates demand line for same organization and returns computed hh', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);
    $line = WorkforceDemandLine::factory()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $org->id,
        'volume_expected' => 100,
        'time_standard_minutes' => 60,
        'coverage_target_pct' => 100,
        'productivity_factor' => 1,
        'ramp_factor' => 1,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->patchJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.$line->id, [
        'volume_expected' => 240,
        'time_standard_minutes' => 30,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.line.id', $line->id)
        ->assertJsonPath('data.line.required_hh', 120);

    $this->assertDatabaseHas('workforce_demand_lines', [
        'id' => $line->id,
        'volume_expected' => 240,
        'time_standard_minutes' => 30,
    ]);
});

it('deletes demand line for same organization', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);
    $line = WorkforceDemandLine::factory()->create([
        'scenario_id' => $scenario->id,
        'organization_id' => $org->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->deleteJson(WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ITEM_ENDPOINT.$line->id)
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.deleted_line_id', $line->id);

    $this->assertDatabaseMissing('workforce_demand_lines', [
        'id' => $line->id,
    ]);
});

it('stores high-volume demand lines batch within acceptable time', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    Sanctum::actingAs($user, ['*']);

    $lines = [];
    for ($index = 0; $index < PERFORMANCE_BATCH_MAX_LINES; $index++) {
        $month = str_pad((string) (($index % 12) + 1), 2, '0', STR_PAD_LEFT);
        $lines[] = [
            'unit' => 'Ops-'.$index,
            'role_name' => 'Role-'.$index,
            'period' => '2026-'.$month,
            'volume_expected' => 200 + $index,
            'time_standard_minutes' => 30,
            'coverage_target_pct' => 95,
        ];
    }

    $startedAt = hrtime(true);

    $response = $this->postJson(
        WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT,
        ['lines' => $lines]
    );

    $elapsedMs = (int) round((hrtime(true) - $startedAt) / 1_000_000);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.created', PERFORMANCE_BATCH_MAX_LINES)
        ->assertJsonPath('data.total', null);

    expect($elapsedMs)->toBeLessThan(PERFORMANCE_STORE_MAX_MS)
        ->and(WorkforceDemandLine::query()->where('scenario_id', $scenario->id)->count())
        ->toBe(PERFORMANCE_BATCH_MAX_LINES);
});

it('lists high-volume demand lines within acceptable time', function () {
    $org = Organization::factory()->create();
    $user = createUserForOrganizationWithRole($org, 'talent_planner');
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    WorkforceDemandLine::factory()
        ->count(PERFORMANCE_BATCH_MAX_LINES)
        ->create([
            'organization_id' => $org->id,
            'scenario_id' => $scenario->id,
        ]);

    Sanctum::actingAs($user, ['*']);

    $startedAt = hrtime(true);

    $response = $this->getJson(
        WORKFORCE_DEMAND_SCENARIOS_ENDPOINT.$scenario->id.WORKFORCE_DEMAND_LINES_ENDPOINT
    );

    $elapsedMs = (int) round((hrtime(true) - $startedAt) / 1_000_000);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.total', PERFORMANCE_BATCH_MAX_LINES);

    expect($elapsedMs)->toBeLessThan(PERFORMANCE_LIST_MAX_MS);
});

<?php

use App\Models\ApprovalRequest;
use App\Models\Organization;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleSkill;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use App\Models\Skill;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Query Performance Tests — N+1 Fixes Validation
 *
 * Validates eager loading reduces query counts on hot endpoints.
 * Targets identified in docs/N1_AUDIT_REPORT_2026_04_03.md
 */

// ── ApprovalRequest eager loading ─────────────────────────────────────────

test('approval requests with eager load does not trigger N+1 for approver', function () {
    $org = Organization::factory()->create();
    $approver = User::factory()->create(['organization_id' => $org->id, 'role' => 'admin']);
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    ApprovalRequest::factory()->count(5)->create([
        'approvable_type' => Scenario::class,
        'approvable_id'   => $scenario->id,
        'approver_id'     => $approver->id,
    ]);

    $queries = [];
    DB::listen(function ($q) use (&$queries) { $queries[] = $q->sql; });

    // With fix: ->with('approver')
    $requests = ApprovalRequest::where('approvable_id', $scenario->id)
        ->where('approvable_type', Scenario::class)
        ->with('approver')
        ->get();

    $requests->each(fn ($ar) => $ar->approver?->name);

    // ≤3 queries: main select + eager approvers select (+ possible org join)
    expect(count($queries))->toBeLessThanOrEqual(3);
});

test('approval requests without eager load triggers N+1 for approver', function () {
    $org = Organization::factory()->create();
    $approver = User::factory()->create(['organization_id' => $org->id, 'role' => 'admin']);
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    ApprovalRequest::factory()->count(5)->create([
        'approvable_type' => Scenario::class,
        'approvable_id'   => $scenario->id,
        'approver_id'     => $approver->id,
    ]);

    $queries = [];
    DB::listen(function ($q) use (&$queries) { $queries[] = $q->sql; });

    // Old behavior: no eager load
    $requests = ApprovalRequest::where('approvable_id', $scenario->id)
        ->where('approvable_type', Scenario::class)
        ->get();

    $requests->each(fn ($ar) => $ar->approver?->name);

    // N+1: 1 main + 5 individual approver queries = 6
    expect(count($queries))->toBeGreaterThan(3);
});

// ── ScenarioRoleSkill eager loading ───────────────────────────────────────

test('scenario role skills with eager load avoids N+1 on role chain', function () {
    $org = Organization::factory()->create();
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    foreach (Roles::factory()->count(3)->create() as $role) {
        $sr = ScenarioRole::factory()->create(['scenario_id' => $scenario->id, 'role_id' => $role->id]);
        ScenarioRoleSkill::factory()->count(2)->create([
            'scenario_id'    => $scenario->id,
            'role_id'        => $sr->id,
            'required_level' => 4,
            'current_level'  => 2,
        ]);
    }

    $queries = [];
    DB::listen(function ($q) use (&$queries) { $queries[] = $q->sql; });

    // With fix: ->with(['scenarioRole.role'])
    $gaps = ScenarioRoleSkill::where('scenario_id', $scenario->id)
        ->whereRaw('required_level > current_level')
        ->with(['scenarioRole.role'])
        ->get();

    $gaps->each(fn ($gap) => $gap->scenarioRole?->role?->name);

    // ≤4 queries: gaps + scenarioRoles + roles (nested eager = 2 queries)
    expect(count($queries))->toBeLessThanOrEqual(4);
});

test('scenario role skills without eager load triggers N+1 on role chain', function () {
    $org = Organization::factory()->create();
    $scenario = Scenario::factory()->create(['organization_id' => $org->id]);

    foreach (Roles::factory()->count(3)->create() as $role) {
        $sr = ScenarioRole::factory()->create(['scenario_id' => $scenario->id, 'role_id' => $role->id]);
        ScenarioRoleSkill::factory()->count(2)->create([
            'scenario_id'    => $scenario->id,
            'role_id'        => $sr->id,
            'required_level' => 4,
            'current_level'  => 2,
        ]);
    }

    $queries = [];
    DB::listen(function ($q) use (&$queries) { $queries[] = $q->sql; });

    // Old behavior: lazy load in loop
    $gaps = ScenarioRoleSkill::where('scenario_id', $scenario->id)
        ->whereRaw('required_level > current_level')
        ->get();

    $gaps->each(function ($gap) {
        $sr = ScenarioRole::find($gap->role_id);
        if ($sr) {
            Roles::find($sr->role_id);
        }
    });

    // N+1: 1 + (6 gaps × 2) = 13 queries
    expect(count($queries))->toBeGreaterThan(4);
});

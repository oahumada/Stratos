<?php

use App\Models\Competency;
use App\Models\Organization;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\User;

// ─── Setup ────────────────────────────────────────────────────────────────────

beforeEach(function () {
    $org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $org->id]);
    $this->actingAs($this->user);
    $this->org = $org;

    $this->scenario = Scenario::factory()->create([
        'organization_id' => $org->id,
        'owner_user_id'   => $this->user->id,
        'created_by'      => $this->user->id,
        'horizon_months'  => 12,
        'status'          => 'draft',
    ]);
});

// ═════════════════════════════════════════════════════════════════════════════
// POST /step2/agent-proposals/apply
// ═════════════════════════════════════════════════════════════════════════════

describe('applyAgentProposals', function () {

    it('requires authentication', function () {
        auth()->logout();
        $this->postJson("/api/scenarios/{$this->scenario->id}/step2/agent-proposals/apply", [
            'approved_role_proposals'    => [],
            'approved_catalog_proposals' => [],
        ])->assertStatus(401);
    });

    it('returns 403 if scenario belongs to another org', function () {
        $otherOrg      = Organization::factory()->create();
        $otherScenario = Scenario::factory()->create([
            'organization_id' => $otherOrg->id,
            'owner_user_id'   => $this->user->id,
            'created_by'      => $this->user->id,
        ]);

        $this->postJson("/api/scenarios/{$otherScenario->id}/step2/agent-proposals/apply", [
            'approved_role_proposals'    => [],
            'approved_catalog_proposals' => [],
        ])->assertStatus(403);
    });

    it('validates required fields', function () {
        $this->postJson("/api/scenarios/{$this->scenario->id}/step2/agent-proposals/apply", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['approved_role_proposals']);
    });

    it('creates a new role and competency mapping with source=agent when type is NEW', function () {
        $competency = Competency::factory()->create([
            'organization_id' => $this->org->id,
            'name'            => 'MLOps Engineering',
            'status'          => 'active',
        ]);

        $payload = [
            'approved_role_proposals' => [
                [
                    'type'          => 'NEW',
                    'proposed_name' => 'AI Talent Engineer',
                    'archetype'     => 'T',
                    'fte_suggested' => 1.0,
                    'competency_mappings' => [
                        [
                            'competency_name' => 'MLOps Engineering',
                            'competency_id'   => $competency->id,
                            'change_type'     => 'enrichment',
                            'required_level'  => 4,
                            'is_core'         => true,
                        ],
                    ],
                ],
            ],
            'approved_catalog_proposals' => [],
        ];

        $res = $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/agent-proposals/apply",
            $payload
        );

        $res->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.roles_created', 1)
            ->assertJsonPath('data.mappings_created', 1);

        // Debe existir un ScenarioRole creado
        $this->assertDatabaseHas('scenario_roles', [
            'scenario_id' => $this->scenario->id,
            'archetype'   => 'T',
        ]);

        // El mapping debe tener source='agent'
        $scenarioRole = ScenarioRole::where('scenario_id', $this->scenario->id)->first();
        $this->assertDatabaseHas('scenario_role_competencies', [
            'scenario_id'   => $this->scenario->id,
            'role_id'       => $scenarioRole->id,
            'competency_id' => $competency->id,
            'source'        => 'agent',
            'change_type'   => 'enrichment',
            'required_level' => 4,
            'is_core'        => true,
        ]);
    });

    it('creates a catalog competency when type is ADD in catalog_proposals', function () {
        $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/agent-proposals/apply",
            [
                'approved_role_proposals' => [],
                'approved_catalog_proposals' => [
                    [
                        'type'             => 'ADD',
                        'proposed_name'    => 'Quantum Computing Fundamentals',
                        'action_rationale' => 'Necesaria para la capacidad de cómputo avanzado',
                    ],
                ],
            ]
        )->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competencies', [
            'organization_id' => $this->org->id,
            'name'            => 'Quantum Computing Fundamentals',
        ]);
    });


    it('resolves competency by name when competency_id is null', function () {
        $competency = Competency::factory()->create([
            'organization_id' => $this->org->id,
            'name'            => 'Python & Data Stack',
            'status'          => 'active',
        ]);

        $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/agent-proposals/apply",
            [
                'approved_role_proposals' => [
                    [
                        'type'          => 'NEW',
                        'proposed_name' => 'Data Engineer',
                        'archetype'     => 'T',
                        'fte_suggested' => 2.0,
                        'competency_mappings' => [
                            [
                                'competency_name' => 'Python & Data Stack',
                                'competency_id'   => null,
                                'change_type'     => 'enrichment',
                                'required_level'  => 3,
                                'is_core'         => true,
                            ],
                        ],
                    ],
                ],
                'approved_catalog_proposals' => [],
            ]
        )->assertStatus(200)->assertJson(['success' => true]);

        $scenarioRole = ScenarioRole::where('scenario_id', $this->scenario->id)->first();
        $this->assertDatabaseHas('scenario_role_competencies', [
            'role_id'       => $scenarioRole->id,
            'competency_id' => $competency->id,
            'source'        => 'agent',
        ]);
    });

    it('returns success with empty proposals', function () {
        $this->postJson(
            "/api/scenarios/{$this->scenario->id}/step2/agent-proposals/apply",
            [
                'approved_role_proposals'    => [],
                'approved_catalog_proposals' => [],
            ]
        )->assertStatus(200)->assertJson(['success' => true]);
    });
});

// ═════════════════════════════════════════════════════════════════════════════
// POST /step2/finalize
// ═════════════════════════════════════════════════════════════════════════════

describe('finalizeStep2', function () {

    it('requires authentication', function () {
        auth()->logout();
        $this->postJson("/api/scenarios/{$this->scenario->id}/step2/finalize")
            ->assertStatus(401);
    });

    it('returns 403 if scenario belongs to another org', function () {
        $otherOrg      = Organization::factory()->create();
        $otherScenario = Scenario::factory()->create([
            'organization_id' => $otherOrg->id,
            'owner_user_id'   => $this->user->id,
            'created_by'      => $this->user->id,
        ]);
        $this->postJson("/api/scenarios/{$otherScenario->id}/step2/finalize")
            ->assertStatus(403);
    });

    it('returns 422 when scenario has no roles', function () {
        $this->postJson("/api/scenarios/{$this->scenario->id}/step2/finalize")
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    });

    it('returns 422 when a role has no archetype', function () {
        $role = Roles::factory()->create(['organization_id' => $this->org->id]);
        ScenarioRole::factory()->create([
            'scenario_id' => $this->scenario->id,
            'role_id'     => $role->id,
            'archetype'   => null,
        ]);

        $this->postJson("/api/scenarios/{$this->scenario->id}/step2/finalize")
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    });

    it('finalizes step2 and sets scenario status to incubating', function () {
        $role = Roles::factory()->create([
            'organization_id' => $this->org->id,
            'status'          => 'draft',
        ]);
        ScenarioRole::factory()->create([
            'scenario_id' => $this->scenario->id,
            'role_id'     => $role->id,
            'archetype'   => 'T',
            'role_change' => 'create',
        ]);

        $this->postJson("/api/scenarios/{$this->scenario->id}/step2/finalize")
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Escenario debe pasar a incubating
        $this->assertDatabaseHas('scenarios', [
            'id'     => $this->scenario->id,
            'status' => 'incubating',
        ]);
    });

    it('moves new roles to in_incubation on finalize', function () {
        $role = Roles::factory()->create([
            'organization_id' => $this->org->id,
            'status'          => 'draft',
        ]);
        ScenarioRole::factory()->create([
            'scenario_id' => $this->scenario->id,
            'role_id'     => $role->id,
            'archetype'   => 'E',
            'role_change' => 'create',
        ]);

        $res = $this->postJson("/api/scenarios/{$this->scenario->id}/step2/finalize");
        $res->assertStatus(200)->assertJson(['success' => true]);

        // El rol debe haber pasado a in_incubation
        $role->refresh();
        expect($role->status)->toBe('in_incubation');
    });
});

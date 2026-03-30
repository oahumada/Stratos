<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('ScenarioAnalyticsController', function () {
    beforeEach(function () {
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create(['current_organization_id' => $this->organization->id]);
        Sanctum::actingAs($this->user, ['*']);
    });

    describe('compareScenarios', function () {
        it('compares two scenarios successfully', function () {
            $scenario1 = Scenario::factory()
                ->for($this->organization)
                ->create(['name' => 'Growth Strategy', 'code' => 'GS-001']);

            $scenario2 = Scenario::factory()
                ->for($this->organization)
                ->create(['name' => 'Cost Optimization', 'code' => 'CO-001']);

            \Log::debug('Test created scenarios', [
                'scenario1_id' => $scenario1->id,
                'scenario2_id' => $scenario2->id,
                'scenario1_org' => $scenario1->organization_id ?? null,
                'scenario2_org' => $scenario2->organization_id ?? null,
                'user_org' => $this->user->current_organization_id ?? null,
            ]);

            $response = $this->postJson('/api/scenarios/compare', [
                'scenario_ids' => [$scenario1->id, $scenario2->id],
            ]);

            $response->assertSuccessful()
                ->assertJsonStructure([
                    'comparison' => [
                        '*' => [
                            'scenario_id',
                            'name',
                            'code',
                            'status',
                            'iq',
                            'financial_impact',
                            'risk_score',
                            'skill_gaps',
                        ],
                    ],
                    'count',
                ]);

            expect($response->json('count'))->toBe(2);
        });

        it('validates maximum of 4 scenarios', function () {
            $scenarios = Scenario::factory()
                ->count(5)
                ->for($this->organization)
                ->create();

            $response = $this->postJson('/api/scenarios/compare', [
                'scenario_ids' => $scenarios->pluck('id')->toArray(),
            ]);

            $response->assertUnprocessable();
        });

        it('requires minimum of 2 scenarios', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->postJson('/api/scenarios/compare', [
                'scenario_ids' => [$scenario->id],
            ]);

            $response->assertUnprocessable();
        });

        it('prevents cross-organization access', function () {
            $otherOrg = Organization::factory()->create();
            $scenario1 = Scenario::factory()
                ->for($this->organization)
                ->create();

            $scenario2 = Scenario::factory()
                ->for($otherOrg)
                ->create();

            $response = $this->postJson('/api/scenarios/compare', [
                'scenario_ids' => [$scenario1->id, $scenario2->id],
            ]);

            $response->assertForbidden();
        });
    });

    describe('analytics', function () {
        it('returns comprehensive analytics for a scenario', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/analytics");

            $response->assertSuccessful()
                ->assertJsonStructure([
                    'scenario_id',
                    'name',
                    'code',
                    'status',
                    'iq',
                    'confidence_score',
                    'capabilities_breakdown',
                    'critical_gaps',
                    'financial_impact',
                    'risk_metrics',
                    'skill_gaps',
                ]);
        });

        it('enforces authorization', function () {
            $otherOrg = Organization::factory()->create();
            $otherUser = User::factory()->create(['current_organization_id' => $otherOrg->id]);

            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            Sanctum::actingAs($otherUser, ['*']);

            $response = $this->getJson("/api/scenarios/{$scenario->id}/analytics");

            $response->assertForbidden();
        });
    });

    describe('financialImpact', function () {
        it('returns financial impact analysis', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/financial-impact");

            $response->assertSuccessful()
                ->assertJsonStructure([
                    'scenario_id',
                    'financial_impact' => [
                        'total_impact',
                        'roi_percentage',
                        'cost_breakdown',
                        'budget_allocation',
                        'payback_period_months',
                    ],
                ]);
        });

        it('includes all cost categories', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/financial-impact");

            $response->assertSuccessful();

            $costBreakdown = $response->json('financial_impact.cost_breakdown');
            expect($costBreakdown)->toHaveKeys([
                'training',
                'hiring',
                'reallocation',
                'external_services',
            ]);
        });
    });

    describe('riskAssessment', function () {
        it('returns risk assessment data', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/risk-assessment");

            $response->assertSuccessful()
                ->assertJsonStructure([
                    'scenario_id',
                    'risk_assessment' => [
                        'overall_risk',
                        'probability',
                        'impact',
                        'risk_items',
                        'mitigation_strategies',
                    ],
                ]);
        });

        it('identifies risk items with scores', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/risk-assessment");

            $response->assertSuccessful();

            $riskItems = $response->json('risk_assessment.risk_items');
            expect($riskItems)->not->toBeEmpty();
            expect($riskItems[0])->toHaveKeys([
                'id',
                'title',
                'probability',
                'impact',
                'score',
            ]);
        });
    });

    describe('skillGaps', function () {
        it('returns skill gap analysis', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/skill-gaps");

            $response->assertSuccessful()
                ->assertJsonStructure([
                    'scenario_id',
                    'skill_gaps' => [
                        'total_gaps',
                        'critical_gaps',
                        'gaps_by_role',
                        'closure_paths',
                        'estimated_time_to_fill',
                    ],
                ]);
        });

        it('includes closure paths for gap remediation', function () {
            $scenario = Scenario::factory()
                ->for($this->organization)
                ->create();

            $response = $this->getJson("/api/scenarios/{$scenario->id}/skill-gaps");

            $response->assertSuccessful();

            $closurePaths = $response->json('skill_gaps.closure_paths');
            expect($closurePaths)->not->toBeEmpty();
            expect($closurePaths[0])->toHaveKeys([
                'path',
                'applicable_gaps',
                'duration_weeks',
                'cost_per_person',
            ]);
        });
    });

    describe('authorization', function () {
        it('requires authentication', function () {
            test()->sanctumActingAs(null);

            $response = $this->getJson('/api/scenarios/1/analytics');

            $response->assertUnauthorized();
        });

        it('respects organization boundaries', function () {
            $org1 = Organization::factory()->create();
            $org2 = Organization::factory()->create();

            $user1 = User::factory()->create(['current_organization_id' => $org1->id]);
            $user2 = User::factory()->create(['current_organization_id' => $org2->id]);

            $scenario1 = Scenario::factory()->for($org1)->create();
            $scenario2 = Scenario::factory()->for($org2)->create();

            Sanctum::actingAs($user1, ['*']);

            // User1 can access their org's scenario
            $this->getJson("/api/scenarios/{$scenario1->id}/analytics")
                ->assertSuccessful();

            // User1 cannot access other org's scenario
            $this->getJson("/api/scenarios/{$scenario2->id}/analytics")
                ->assertForbidden();
        });
    });

    describe('multi-tenant isolation', function () {
        it('isolates comparison results per organization', function () {
            $org1 = Organization::factory()->create();
            $org2 = Organization::factory()->create();

            $user1 = User::factory()->create(['current_organization_id' => $org1->id]);
            $user2 = User::factory()->create(['current_organization_id' => $org2->id]);

            $scenario1a = Scenario::factory()->for($org1)->create();
            $scenario1b = Scenario::factory()->for($org1)->create();

            $scenario2a = Scenario::factory()->for($org2)->create();
            $scenario2b = Scenario::factory()->for($org2)->create();

            Sanctum::actingAs($user1, ['*']);

            // Org1 can compare their scenarios
            $this->postJson('/api/scenarios/compare', [
                'scenario_ids' => [$scenario1a->id, $scenario1b->id],
            ])->assertSuccessful();

            // Org1 cannot mix with Org2's scenarios
            $this->postJson('/api/scenarios/compare', [
                'scenario_ids' => [$scenario1a->id, $scenario2a->id],
            ])->assertForbidden();
        });
    });
});

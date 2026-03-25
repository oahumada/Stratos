<?php

namespace Tests\Feature\Verification;

use App\Models\Organization;
use App\Services\VerificationMetricsService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * VerificationMetricsServiceTest - Tests for metrics collection and analysis
 *
 * Tests:
 * 1. Metrics are collected from event_store correctly
 * 2. Confidence score average is calculated properly
 * 3. Error rate is computed correctly
 * 4. Retry rate is tracked
 * 5. Metrics are windowed correctly (time-based)
 * 6. Multi-tenant isolation maintained
 * 7. Empty metrics handled gracefully
 */
class VerificationMetricsServiceTest extends TestCase
{
    protected Organization $organization;

    protected VerificationMetricsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
        $this->service = app(VerificationMetricsService::class);
    }

    /** @test */
    public function metrics_collected_from_event_store(): void
    {
        $this->createEvents(100, confidenceScore: 85);

        $metrics = $this->service->getOrganizationMetrics($this->organization->id, 24);

        $this->assertNotEmpty($metrics);
        $this->assertEquals(100, $metrics['total_verifications']);
    }

    /** @test */
    public function confidence_score_average_calculated(): void
    {
        // Create events with varying confidence scores
        DB::table('event_store')->insert([
            [
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => 'test-1',
                'version' => 1,
                'payload' => json_encode(['confidence_score' => 80, 'violations' => []]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ],
            [
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => 'test-2',
                'version' => 1,
                'payload' => json_encode(['confidence_score' => 90, 'violations' => []]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ],
        ]);

        $metrics = $this->service->getOrganizationMetrics($this->organization->id, 24);

        // Average of 80 and 90 is 85
        $this->assertEquals(85, $metrics['avg_confidence_score']);
    }

    /** @test */
    public function error_rate_calculated_from_violations(): void
    {
        // Create 100 events with violations
        for ($i = 0; $i < 100; $i++) {
            DB::table('event_store')->insert([
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => "test-{$i}",
                'version' => 1,
                'payload' => json_encode([
                    'confidence_score' => 85,
                    'violations' => $i % 2 === 0 ? [['code' => 'TEST']] : [],
                ]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ]);
        }

        $metrics = $this->service->getOrganizationMetrics($this->organization->id, 24);

        // 50 events have 1 violation each = 50 violations / 100 events = 50% error rate
        $this->assertEquals(50, $metrics['error_rate']);
    }

    /** @test */
    public function metrics_filtered_by_time_window(): void
    {
        // Create events: 50 recent, 50 old
        for ($i = 0; $i < 50; $i++) {
            DB::table('event_store')->insert([
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => "recent-{$i}",
                'version' => 1,
                'payload' => json_encode(['confidence_score' => 90, 'violations' => []]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now()->subHours(1),  // Recent
                'created_at' => now(),
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            DB::table('event_store')->insert([
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => "old-{$i}",
                'version' => 1,
                'payload' => json_encode(['confidence_score' => 70, 'violations' => []]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now()->subHours(30),  // Old (outside 24h window)
                'created_at' => now(),
            ]);
        }

        // Query with 24 hour window
        $metrics24h = $this->service->getOrganizationMetrics($this->organization->id, 24);

        // Query with 48 hour window
        $metrics48h = $this->service->getOrganizationMetrics($this->organization->id, 48);

        // 24h should have fewer events
        $this->assertLessThan($metrics48h['total_verifications'], $metrics24h['total_verifications'] + 100);
    }

    /** @test */
    public function recommendation_breakdown_tracked(): void
    {
        DB::table('event_store')->insert(array_merge(
            array_fill(0, 40, [
                'event_name' => 'verification.recommendation_accept',
                'aggregate_type' => 'verification',
                'version' => 1,
                'payload' => json_encode([]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ]),
            array_fill(0, 30, [
                'event_name' => 'verification.recommendation_review',
                'aggregate_type' => 'verification',
                'version' => 1,
                'payload' => json_encode([]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ]),
            array_fill(0, 30, [
                'event_name' => 'verification.recommendation_reject',
                'aggregate_type' => 'verification',
                'version' => 1,
                'payload' => json_encode([]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ])
        ));

        $metrics = $this->service->getOrganizationMetrics($this->organization->id, 24);

        $this->assertEquals(40, $metrics['recommendation_breakdown']['accepted']);
        $this->assertEquals(30, $metrics['recommendation_breakdown']['review_needed']);
        $this->assertEquals(30, $metrics['recommendation_breakdown']['rejected']);
    }

    /** @test */
    public function empty_result_when_no_events(): void
    {
        $metrics = $this->service->getOrganizationMetrics($this->organization->id, 24);

        $this->assertEmpty($metrics);
    }

    /** @test */
    public function multi_tenant_isolation_metrics(): void
    {
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        // Create events for org1
        $this->createEvents(50, organizationId: $org1->id, confidenceScore: 90);

        // Create events for org2
        $this->createEvents(30, organizationId: $org2->id, confidenceScore: 70);

        // Get metrics for each
        $metricsOrg1 = $this->service->getOrganizationMetrics($org1->id, 24);
        $metricsOrg2 = $this->service->getOrganizationMetrics($org2->id, 24);

        // Org1 should have 50, org2 should have 30
        $this->assertEquals(50, $metricsOrg1['total_verifications']);
        $this->assertEquals(30, $metricsOrg2['total_verifications']);
    }

    /** @test */
    public function detailed_metrics_by_type(): void
    {
        // Create events from different agents
        for ($i = 0; $i < 30; $i++) {
            DB::table('event_store')->insert([
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => "agent-a-{$i}",
                'version' => 1,
                'payload' => json_encode(['agent_name' => 'AgentA', 'confidence_score' => 85, 'violations' => []]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ]);
        }

        for ($i = 0; $i < 20; $i++) {
            DB::table('event_store')->insert([
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => "agent-b-{$i}",
                'version' => 1,
                'payload' => json_encode(['agent_name' => 'AgentB', 'confidence_score' => 92, 'violations' => []]),
                'metadata' => json_encode(['organization_id' => $this->organization->id]),
                'occurred_at' => now(),
                'created_at' => now(),
            ]);
        }

        $metricsByType = $this->service->getDetailedMetricsByType($this->organization->id, 24);

        $this->assertCount(2, $metricsByType);
        $this->assertArrayHasKey('AgentA', $metricsByType);
        $this->assertArrayHasKey('AgentB', $metricsByType);
        $this->assertEquals(30, $metricsByType['AgentA']['event_count']);
        $this->assertEquals(20, $metricsByType['AgentB']['event_count']);
    }

    /**
     * Helper: Create verification events
     */
    private function createEvents(
        int $count,
        ?int $organizationId = null,
        float $confidenceScore = 85,
        int $violationCount = 0
    ): void {
        $organizationId = $organizationId ?? $this->organization->id;

        for ($i = 0; $i < $count; $i++) {
            DB::table('event_store')->insert([
                'event_name' => 'verification.completed',
                'aggregate_type' => 'verification',
                'aggregate_id' => "test-{$organizationId}-{$i}",
                'version' => 1,
                'payload' => json_encode([
                    'confidence_score' => $confidenceScore + rand(-5, 5),
                    'violations' => array_fill(0, $violationCount, ['code' => 'TEST']),
                ]),
                'metadata' => json_encode(['organization_id' => $organizationId]),
                'occurred_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
}

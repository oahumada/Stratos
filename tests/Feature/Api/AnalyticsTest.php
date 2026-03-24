<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\TalentVerification;
use App\Models\VerificationAudit;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $organizationId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->organizationId = $this->user->organization_id;

        // Seed verification data
        $this->seedVerificationData();
    }

    /**
     * Seed sample verification audit data
     */
    protected function seedVerificationData(): void
    {
        for ($i = 0; $i < 30; $i++) {
            VerificationAudit::create([
                'organization_id' => $this->organizationId,
                'audit_type' => 'verification',
                'status' => $i % 5 === 4 ? 'error' : ($i % 3 === 2 ? 'warning' : 'completed'),
                'data' => json_encode([
                    'latency_ms' => rand(100, 500),
                    'throughput' => rand(50, 95),
                    'transition_time_seconds' => rand(30, 300),
                ]),
                'created_at' => now()->subDays(30 - $i),
            ]);
        }
    }

    /**
     * Test: Get anomalies detection
     */
    public function test_get_anomalies(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/anomalies');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'timestamp',
            'verification_anomalies',
            'talent_anomalies',
            'total_anomalies',
        ]);
        $this->assertEquals($this->organizationId, $response->json('organization_id'));
    }

    /**
     * Test: Forecast compliance score
     */
    public function test_forecast_compliance(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/predictions/compliance');

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'current_score',
            'forecast_days',
            'expected_range',
            'trend',
        ]);
    }

    /**
     * Test: Predict optimal deployment window
     */
    public function test_predict_deployment_window(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/predictions/deployment-window?days=7');

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'predictions',
            'optimal_window',
            'next_optimal_date',
        ]);
    }

    /**
     * Test: Predict resource needs
     */
    public function test_predict_resource_needs(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/predictions/resources');

        $response->assertOk();
        $this->assertIsArray($response->json('throughput_trend'));
        $this->assertIsArray($response->json('processing_time_trend'));
    }

    /**
     * Test: Assess transition risk
     */
    public function test_assess_transition_risk(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/analytics/predictions/transition-risk', [
            'role_id' => 1,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'overall_risk_score',
            'risk_level',
            'risk_factors',
            'recommendation',
            'suggested_actions',
        ]);
    }

    /**
     * Test: Get recommendations
     */
    public function test_get_recommendations(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/recommendations');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'generated_at',
            'total_recommendations',
            'by_priority',
            'recommendations',
        ]);
    }

    /**
     * Test: Get current metrics
     */
    public function test_get_current_metrics(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/metrics/current');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'timestamp',
            'metrics',
        ]);
    }

    /**
     * Test: Get metrics history
     */
    public function test_get_metrics_history(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/metrics/history?days=30&interval=daily');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'period_days',
            'interval',
            'total_data_points',
            'data',
        ]);
    }

    /**
     * Test: Get metrics comparison
     */
    public function test_get_metrics_comparison(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/metrics/comparison?days=7');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'comparison_period_days',
            'data',
        ]);
    }

    /**
     * Test: Get latency percentiles
     */
    public function test_get_latency_percentiles(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/metrics/latency-percentiles?days=30');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'period_days',
            'latency_percentiles_ms',
        ]);
    }

    /**
     * Test: Get dashboard summary
     */
    public function test_get_dashboard_summary(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/dashboard-summary');

        $response->assertOk();
        $response->assertJsonStructure([
            'organization_id',
            'timestamp',
            'current_metrics',
            'anomalies_count',
            'critical_recommendations',
            'deployment_feasibility',
        ]);
    }

    /**
     * Test: Unauthenticated requests are rejected
     */
    public function test_unauthenticated_request_fails(): void
    {
        $response = $this->getJson('/api/analytics/anomalies');

        $response->assertUnauthorized();
    }

    /**
     * Test: Multi-tenant isolation
     */
    public function test_multi_tenant_isolation(): void
    {
        $userOtherOrg = User::factory()->create();
        Sanctum::actingAs($userOtherOrg);

        $response = $this->getJson('/api/analytics/anomalies');

        $response->assertOk();
        $this->assertEquals(
            $userOtherOrg->organization_id,
            $response->json('organization_id')
        );
        $this->assertNotEquals(
            $this->organizationId,
            $response->json('organization_id')
        );
    }

    /**
     * Test: Recommendations include priority levels
     */
    public function test_recommendations_have_priority_levels(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/analytics/recommendations');

        $response->assertOk();
        $recommendations = $response->json('recommendations');

        $this->assertIsArray($recommendations);

        foreach ($recommendations as $rec) {
            $this->assertIn($rec['priority'], ['CRITICAL', 'HIGH', 'MEDIUM', 'LOW']);
            $this->assertNotEmpty($rec['title']);
            $this->assertNotEmpty($rec['description']);
        }
    }
}

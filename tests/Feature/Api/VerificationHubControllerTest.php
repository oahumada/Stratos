<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationHubControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->for(\App\Models\Organization::factory()->create())->admin()->create();
    }

    // ============================================================================
    // Scheduler Status Endpoint Tests
    // ============================================================================

    /** @test */
    public function scheduler_status_returns_current_state()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/scheduler-status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'enabled',
                    'mode',
                    'last_run',
                    'last_run_status',
                    'next_run',
                    'seconds_until_next',
                    'recent_executions' => [
                        '*' => ['id', 'started_at', 'ended_at', 'status'],
                    ],
                ],
            ]);
    }

    /** @test */
    public function scheduler_status_requires_authentication()
    {
        $response = $this->getJson('/api/deployment/verification/scheduler-status');

        $response->assertStatus(401);
    }

    /** @test */
    public function scheduler_status_requires_admin_role()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/deployment/verification/scheduler-status');

        $response->assertStatus(403);
    }

    // ============================================================================
    // Transitions Endpoint Tests
    // ============================================================================

    /** @test */
    public function transitions_returns_paginated_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/transitions?limit=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'from_phase',
                        'to_phase',
                        'timestamp',
                        'reason',
                    ],
                ],
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                ],
            ]);
    }

    /** @test */
    public function transitions_respects_limit_parameter()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/transitions?limit=5');

        $data = $response->json('data');

        $this->assertLessThanOrEqual(5, count($data));
    }

    /** @test */
    public function transitions_pagination_default_limit_is_10()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/transitions');

        $this->assertEquals(10, $response->json('pagination.per_page'));
    }

    // ============================================================================
    // Notifications Endpoint Tests
    // ============================================================================

    /** @test */
    public function notifications_returns_filtered_list()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/notifications?type=transition');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'severity',
                        'message',
                        'read',
                        'created_at',
                    ],
                ],
                'pagination',
            ]);
    }

    /** @test */
    public function notifications_filters_by_severity()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/notifications?severity=error');

        $response->assertStatus(200);

        // Verify all returned notifications have severity=error
        $notifications = $response->json('data');
        foreach ($notifications as $notification) {
            $this->assertEquals('error', $notification['severity']);
        }
    }

    /** @test */
    public function notifications_filters_by_read_status()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/notifications?read=false');

        $response->assertStatus(200);

        $notifications = $response->json('data');
        foreach ($notifications as $notification) {
            $this->assertFalse($notification['read']);
        }
    }

    // ============================================================================
    // Test Notification Endpoint Tests
    // ============================================================================

    /** @test */
    public function test_notification_to_slack_succeeds()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/deployment/verification/test-notification', [
                'channel' => 'slack',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'channel',
            ]);
    }

    /** @test */
    public function test_notification_requires_valid_channel()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/deployment/verification/test-notification', [
                'channel' => 'invalid_channel',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('channel');
    }

    /** @test */
    public function test_notification_requires_channel_field()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/deployment/verification/test-notification', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('channel');
    }

    // ============================================================================
    // Configuration Endpoint Tests
    // ============================================================================

    /** @test */
    public function configuration_returns_system_settings()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/configuration');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'mode',
                    'enabled',
                    'channels' => [
                        'slack',
                        'email',
                        'database',
                        'log',
                    ],
                    'thresholds' => [
                        'confidence_min',
                        'error_rate_max',
                        'retry_rate_max',
                        'sample_size_min',
                    ],
                ],
            ]);
    }

    /** @test */
    public function configuration_returns_channel_status()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/configuration');

        $channels = $response->json('data.channels');

        $this->assertArrayHasKey('slack', $channels);
        $this->assertArrayHasKey('enabled', $channels['slack']);
    }

    // ============================================================================
    // Audit Logs Endpoint Tests (Phase 2)
    // ============================================================================

    /** @test */
    public function audit_logs_returns_filtered_events()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/audit-logs?action=phase_transition');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'action',
                        'user_id',
                        'created_at',
                    ],
                ],
                'pagination',
                'summary',
            ]);
    }

    /** @test */
    public function audit_logs_includes_summary_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/audit-logs');

        $summary = $response->json('data.summary');

        $this->assertArrayHasKey('total_events', $summary ?? response()->json('summary'));
    }

    // ============================================================================
    // Dry Run Endpoint Tests (Phase 2)
    // ============================================================================

    /** @test */
    public function dry_run_simulation_returns_transition_analysis()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/deployment/verification/dry-run', []);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_phase',
                    'would_transition',
                    'next_phase',
                    'reason',
                    'metrics' => [
                        'confidence',
                        'error_rate',
                        'retry_rate',
                        'sample_size',
                    ],
                    'gaps',
                ],
            ]);
    }

    /** @test */
    public function dry_run_accepts_custom_thresholds()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/deployment/verification/dry-run', [
                'confidence_threshold' => 85,
                'error_rate_threshold' => 45,
                'retry_rate_threshold' => 25,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    // ============================================================================
    // Compliance Report Endpoint Tests (Phase 2)
    // ============================================================================

    /** @test */
    public function compliance_report_returns_period_summary()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/compliance-report?date_from=2026-03-01&date_to=2026-03-31');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'report_period' => [
                        'from',
                        'to',
                        'days',
                    ],
                    'summary' => [
                        'total_events',
                        'phase_transitions',
                    ],
                    'events',
                ],
            ]);
    }

    /** @test */
    public function compliance_report_filters_by_date_range()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/compliance-report?date_from=2026-03-20&date_to=2026-03-25');

        $data = $response->json('data');
        $daysDifference = (strtotime($data['report_period']['to']) - strtotime($data['report_period']['from'])) / 86400;

        $this->assertLessThanOrEqual(5, $daysDifference);
    }

    // ============================================================================
    // Multi-tenant Isolation Tests
    // ============================================================================

    /** @test */
    public function scheduler_status_isolates_by_organization()
    {
        // Create second admin in different organization
        $otherOrg = \App\Models\Organization::factory()->create();
        $otherAdmin = User::factory()->create(['organization_id' => $otherOrg->id]);
        $otherAdmin->assignRole('admin');

        // Both should get 200 but different data
        $response1 = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/scheduler-status');

        $response2 = $this->actingAs($otherAdmin)
            ->getJson('/api/deployment/verification/scheduler-status');

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Data should be scoped to their org
        $this->assertNotEquals(
            $response1->json('data.organization_id'),
            $response2->json('data.organization_id')
        );
    }

    // ============================================================================
    // Error Handling Tests
    // ============================================================================

    /** @test */
    public function endpoints_return_appropriate_error_codes()
    {
        // Test 404 with invalid endpoint
        $response = $this->actingAs($this->admin)
            ->getJson('/api/deployment/verification/invalid-endpoint');

        $response->assertStatus(404);
    }

    /** @test */
    public function rate_limiting_is_enforced()
    {
        // Make 61 requests (limit is 60/min)
        for ($i = 0; $i < 61; $i++) {
            $response = $this->actingAs($this->admin)
                ->getJson('/api/deployment/verification/scheduler-status');
        }

        // 61st request should be rate limited
        $this->assertEquals(429, $response->status());
    }
}

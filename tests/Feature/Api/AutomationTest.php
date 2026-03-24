<?php

namespace Tests\Feature\Api;

use App\Models\Organizations;
use App\Models\User;
use App\Models\WebhookRegistry;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutomationTest extends TestCase
{
    use RefreshDatabase;

    protected Organizations $organization;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    }

    /** @test */
    public function evaluates_organization_and_triggers_automations()
    {
        $response = $this->actingAs($this->user)->getJson('/api/automation/evaluate');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'organization_id',
                'trigger_type',
                'triggered_workflows',
                'count',
                'timestamp',
            ])
            ->assertJson(['organization_id' => $this->organization->id]);
    }

    /** @test */
    public function can_trigger_specific_workflow()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/workflows/performance_investigation/trigger', [
                'trigger_data' => ['test' => true],
                'async' => true,
            ]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'execution_id',
                'workflow_code',
                'status',
                'async',
            ])
            ->assertJson(['status' => 'triggered']);
    }

    /** @test */
    public function lists_available_workflows()
    {
        $response = $this->actingAs($this->user)->getJson('/api/automation/workflows/available');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'organization_id',
                'workflows',
                'count',
            ]);
    }

    /** @test */
    public function gets_execution_status()
    {
        $executionId = 'test-execution-id';

        $response = $this->actingAs($this->user)
            ->getJson("/api/automation/executions/{$executionId}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'execution_id',
                'status',
            ]);
    }

    /** @test */
    public function can_register_webhook()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/webhooks', [
                'webhook_url' => 'https://example.com/webhook',
                'event_filters' => ['anomaly.*'],
                'active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'webhook',
                'signing_secret',
            ])
            ->assertJson([
                'webhook' => [
                    'webhook_url' => 'https://example.com/webhook',
                    'is_active' => true,
                ],
            ]);
    }

    /** @test */
    public function lists_registered_webhooks()
    {
        WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
            'webhook_url' => 'https://example.com/webhook1',
        ]);

        WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
            'webhook_url' => 'https://example.com/webhook2',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/automation/webhooks');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'organization_id',
                'webhooks',
                'count',
            ])
            ->assertJson(['count' => 2]);
    }

    /** @test */
    public function can_update_webhook()
    {
        $webhook = WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/automation/webhooks/{$webhook->id}", [
                'event_filters' => ['compliance.*'],
                'is_active' => false,
            ]);

        $response->assertSuccessful()
            ->assertJson([
                'webhook' => [
                    'id' => $webhook->id,
                    'is_active' => false,
                ],
            ]);

        $this->assertFalse($webhook->fresh()->is_active);
    }

    /** @test */
    public function can_delete_webhook()
    {
        $webhook = WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/automation/webhooks/{$webhook->id}");

        $response->assertStatus(204);
        $this->assertNull(WebhookRegistry::find($webhook->id));
    }

    /** @test */
    public function can_test_webhook()
    {
        $webhook = WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/automation/webhooks/{$webhook->id}/test");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'webhook_id',
                'test_result',
                'status',
            ]);
    }

    /** @test */
    public function gets_webhook_statistics()
    {
        $webhook = WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
            'failure_count' => 5,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/automation/webhooks/{$webhook->id}/stats");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'webhook_id',
                'url',
                'is_active',
                'health',
            ])
            ->assertJson(['health' => 'healthy']);  // failure_count < 10
    }

    /** @test */
    public function can_remediate_anomaly()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/remediate', [
                'anomaly' => [
                    'type' => 'SPIKE',
                    'metric' => 'latency',
                    'severity' => 'HIGH',
                ],
                'level' => 'automatic',
            ]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'anomaly_type',
                'organization_id',
                'level',
                'actions',
            ]);
    }

    /** @test */
    public function gets_remediation_history()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/automation/remediation-history');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'organization_id',
                'remediations',
                'total',
            ]);
    }

    /** @test */
    public function gets_automation_status()
    {
        $response = $this->actingAs($this->user)->getJson('/api/automation/status');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'organization_id',
                'automation_enabled',
                'status',
                'timestamp',
            ]);
    }

    /** @test */
    public function can_toggle_automation_status()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/status', [
                'enabled' => false,
            ]);

        $response->assertSuccessful()
            ->assertJson(['automation_enabled' => false]);
    }

    /** @test */
    public function rejects_unauthenticated_requests()
    {
        $response = $this->getJson('/api/automation/evaluate');

        $response->assertUnauthorized();
    }

    /** @test */
    public function ensures_multi_tenant_isolation_for_webhooks()
    {
        $user2 = User::factory()->create();
        $org2 = Organizations::factory()->create();
        $user2->update(['organization_id' => $org2->id]);

        // Create webhook for org1
        WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
            'webhook_url' => 'https://org1.com/webhook',
        ]);

        // Try to access from different org
        $response = $this->actingAs($user2)->getJson('/api/automation/webhooks');

        $response->assertSuccessful()
            ->assertJson(['count' => 0]);  // Should not see org1's webhooks
    }

    /** @test */
    public function validates_webhook_url_format()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/webhooks', [
                'webhook_url' => 'not-a-valid-url',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('webhook_url');
    }

    /** @test */
    public function validates_remediation_level()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/remediate', [
                'anomaly' => ['type' => 'SPIKE'],
                'level' => 'invalid_level',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('level');
    }

    /** @test */
    public function can_cancel_execution()
    {
        $executionId = 'test-execution-123';

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/automation/executions/{$executionId}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'execution_id',
                'status',
            ]);
    }

    /** @test */
    public function can_retry_failed_execution()
    {
        $executionId = 'test-execution-456';

        $response = $this->actingAs($this->user)
            ->postJson("/api/automation/executions/{$executionId}/retry", [
                'updated_trigger_data' => ['retry_reason' => 'manual_retry'],
            ]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'execution_id',
                'status',
            ]);
    }

    /** @test */
    public function applies_appropriate_trigger_filters()
    {
        $webhook = WebhookRegistry::factory()->create([
            'organization_id' => $this->organization->id,
            'event_filters' => ['anomaly.spike'],  // Only spike events
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/webhooks', [
                'webhook_url' => 'https://test.com/webhook',
                'event_filters' => ['performance.*', 'compliance.*'],
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'webhook' => [
                    'event_filters' => ['performance.*', 'compliance.*'],
                ],
            ]);
    }
}

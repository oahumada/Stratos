<?php

namespace Tests\Feature\Api;

use App\Models\Organizations;
use App\Models\User;
use App\Models\WebhookRegistry;
use Tests\TestCase;
use Illuminate\Foundation\Testing\Database;

class AutomationTest extends TestCase
{
    use Database\Concerns\DatabaseMigrations;

    protected Organizations $organization;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    }

    /** @test */
    it('evaluates organization and triggers automations')
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
    it('can trigger specific workflow')
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
    it('lists available workflows')
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
    it('gets execution status')
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
    it('can register webhook')
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
    it('lists registered webhooks')
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
    it('can update webhook')
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
    it('can delete webhook')
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
    it('can test webhook')
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
    it('gets webhook statistics')
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
    it('can remediate anomaly')
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
    it('gets remediation history')
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
    it('gets automation status')
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
    it('can toggle automation status')
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/status', [
                'enabled' => false,
            ]);

        $response->assertSuccessful()
            ->assertJson(['automation_enabled' => false]);
    }

    /** @test */
    it('rejects unauthenticated requests')
    {
        $response = $this->getJson('/api/automation/evaluate');

        $response->assertUnauthorized();
    }

    /** @test */
    it('ensures multi-tenant isolation for webhooks')
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
    it('validates webhook url format')
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/automation/webhooks', [
                'webhook_url' => 'not-a-valid-url',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('webhook_url');
    }

    /** @test */
    it('validates remediation level')
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
    it('can cancel execution')
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
    it('can retry failed execution')
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
    it('applies appropriate trigger filters')
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

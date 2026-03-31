<?php

use App\Models\AlertHistory;
use App\Models\AlertThreshold;
use App\Models\Organization;
use App\Models\User;

describe('AlertController', function () {
    beforeEach(function () {
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
        $this->otherOrganization = Organization::factory()->create();

        $this->actingAs($this->user);
    });

    describe('indexThresholds', function () {
        it('returns active thresholds for organization', function () {
            AlertThreshold::factory(3)->create([
                'organization_id' => $this->organization->id,
                'is_active' => true,
            ]);

            AlertThreshold::factory(2)->create([
                'organization_id' => $this->organization->id,
                'is_active' => false,
            ]);

            $response = $this->getJson('/api/alerts/thresholds');

            $response->assertSuccessful()
                ->assertJsonCount(3, 'data');
        });

        it('only shows thresholds for user organization', function () {
            AlertThreshold::factory(2)->create([
                'organization_id' => $this->organization->id,
                'is_active' => true,
            ]);

            AlertThreshold::factory(3)->create([
                'organization_id' => $this->otherOrganization->id,
                'is_active' => true,
            ]);

            $response = $this->getJson('/api/alerts/thresholds');

            $response->assertSuccessful()
                ->assertJsonCount(2, 'data');
        });
    });

    describe('storeThreshold', function () {
        it('creates new alert threshold', function () {
            $response = $this->postJson('/api/alerts/thresholds', [
                'metric' => 'cpu_usage',
                'threshold' => 85.5,
                'severity' => 'high',
                'description' => 'CPU threshold',
            ]);

            $response->assertCreated()
                ->assertJsonPath('metric', 'cpu_usage')
                ->assertJsonPath('organization_id', $this->organization->id);

            $this->assertDatabaseHas('alert_thresholds', [
                'organization_id' => $this->organization->id,
                'metric' => 'cpu_usage',
                'threshold' => 85.5,
            ]);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/alerts/thresholds', [
                'threshold' => 85,
                // missing metric and severity
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['metric', 'severity']);
        });

        it('validates metric uniqueness per organization', function () {
            AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
                'metric' => 'cpu_usage',
            ]);

            $response = $this->postJson('/api/alerts/thresholds', [
                'metric' => 'cpu_usage',
                'threshold' => 90,
                'severity' => 'medium',
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors('metric');
        });
    });

    describe('updateThreshold', function () {
        it('updates alert threshold', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
                'threshold' => 85,
            ]);

            $response = $this->patchJson("/api/alerts/thresholds/{$threshold->id}", [
                'threshold' => 90,
                'severity' => 'critical',
            ]);

            $response->assertSuccessful()
                ->assertJsonPath('threshold', 90)
                ->assertJsonPath('severity', 'critical');
        });

        it('prevents updating threshold from other organization', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->otherOrganization->id,
            ]);

            $response = $this->patchJson("/api/alerts/thresholds/{$threshold->id}", [
                'threshold' => 90,
            ]);

            $response->assertForbidden();
        });
    });

    describe('destroyThreshold', function () {
        it('soft-deletes threshold', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
            ]);

            $response = $this->deleteJson("/api/alerts/thresholds/{$threshold->id}");

            $response->assertOk();
            $this->assertSoftDeleted('alert_thresholds', ['id' => $threshold->id]);
        });
    });

    describe('indexHistory', function () {
        it('returns alert history', function () {
            AlertHistory::factory(5)->create([
                'organization_id' => $this->organization->id,
            ]);

            $response = $this->getJson('/api/alerts/history');

            $response->assertSuccessful()
                ->assertJsonCount(5, 'data');
        });
    });

    describe('acknowledgeAlert', function () {
        it('acknowledges an alert', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
                'acknowledged_at' => null,
            ]);

            $response = $this->postJson("/api/alerts/history/{$alert->id}/acknowledge", [
                'notes' => 'Investigating now',
            ]);

            $response->assertSuccessful()
                ->assertJsonPath('status', 'acknowledged');

            $this->assertDatabaseHas('alert_histories', [
                'id' => $alert->id,
                'status' => 'acknowledged',
                'acknowledged_by' => $this->user->id,
                'notes' => 'Investigating now',
            ]);
        });
    });

    describe('resolveAlert', function () {
        it('resolves an alert', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'acknowledged',
            ]);

            $response = $this->postJson("/api/alerts/history/{$alert->id}/resolve");

            $response->assertSuccessful()
                ->assertJsonPath('status', 'resolved');

            $this->assertNotNull($alert->fresh()->resolved_at);
        });
    });

    describe('muteAlert', function () {
        it('mutes an alert', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
            ]);

            $response = $this->postJson("/api/alerts/history/{$alert->id}/mute");

            $response->assertSuccessful()
                ->assertJsonPath('status', 'muted');
        });
    });

    describe('getUnacknowledged', function () {
        it('returns only unacknowledged alerts', function () {
            AlertHistory::factory(2)->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
                'acknowledged_at' => null,
            ]);

            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'resolved',
            ]);

            $response = $this->getJson('/api/alerts/unacknowledged');

            $response->assertSuccessful()
                ->assertJsonCount(2);
        });
    });

    describe('getCritical', function () {
        it('returns critical and high severity alerts', function () {
            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'critical',
            ]);

            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'high',
            ]);

            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'low',
            ]);

            $response = $this->getJson('/api/alerts/critical');

            $response->assertSuccessful()
                ->assertJsonCount(2);
        });
    });

    describe('statistics', function () {
        it('returns alert statistics', function () {
            AlertHistory::factory(2)->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
            ]);

            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'critical',
            ]);

            $response = $this->getJson('/api/alerts/statistics');

            $response->assertSuccessful()
                ->assertJsonStructure([
                    'unacknowledged_count',
                    'critical_count',
                    'recent_24h',
                    'acknowledge_rate',
                ]);
        });
    });

    describe('bulkAcknowledge', function () {
        it('acknowledges multiple alerts', function () {
            $alerts = AlertHistory::factory(3)->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
            ]);

            $alertIds = $alerts->pluck('id')->toArray();

            $response = $this->postJson('/api/alerts/bulk-acknowledge', [
                'alert_ids' => $alertIds,
                'notes' => 'Bulk acknowledge',
            ]);

            $response->assertSuccessful()
                ->assertJsonPath('acknowledged', 3);

            foreach ($alerts as $alert) {
                $this->assertDatabaseHas('alert_histories', [
                    'id' => $alert->id,
                    'status' => 'acknowledged',
                ]);
            }
        });
    });

    describe('exportHistory', function () {
        it('exports alert history as CSV', function () {
            AlertHistory::factory(5)->create([
                'organization_id' => $this->organization->id,
            ]);

            $response = $this->getJson('/api/alerts/export/history');

            $response->assertSuccessful();

            $ct = $response->headers->get('content-type');
            $this->assertStringStartsWith('text/csv', $ct);

            expect($response->getContent())->toContain('Alert ID');
        });
    });
});

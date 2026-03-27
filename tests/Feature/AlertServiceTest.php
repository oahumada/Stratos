<?php

use App\Models\{AlertHistory, AlertThreshold, Organization, User};
use App\Services\AlertService;
use Tests\Fixtures\AlertFixture;

beforeEach(function () {
    $this->alertService = new AlertService();
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
});

describe('AlertService', function () {

    describe('checkMetric', function () {
        it('returns null when no thresholds exist', function () {
            $result = $this->alertService->checkMetric($this->organization->id, 'cpu_usage', 50.5);

            expect($result)->toBeNull();
        });

        it('returns null when metric value below threshold', function () {
            AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
                'metric' => 'cpu_usage',
                'threshold' => 80,
                'is_active' => true,
            ]);

            $result = $this->alertService->checkMetric($this->organization->id, 'cpu_usage', 50);

            expect($result)->toBeNull();
        });

        it('creates alert when metric exceeds threshold', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
                'metric' => 'cpu_usage',
                'threshold' => 80,
                'is_active' => true,
                'severity' => 'high',
            ]);

            $result = $this->alertService->checkMetric($this->organization->id, 'cpu_usage', 95);

            expect($result)->not->toBeNull()
                ->and($result->alert_threshold_id)->toBe($threshold->id)
                ->and($result->metric_value)->toBe(95.0)
                ->and($result->severity)->toBe('high')
                ->and($result->status)->toBe('triggered');
        });

        it('does not create duplicate alerts for same threshold', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
                'metric' => 'cpu_usage',
                'threshold' => 80,
            ]);

            // First alert
            $this->alertService->checkMetric($this->organization->id, 'cpu_usage', 95);

            // Second check with same threshold still high
            $result = $this->alertService->checkMetric($this->organization->id, 'cpu_usage', 92);

            expect($result)->not->toBeNull();

            // Should have only 1 triggered alert
            $unacknowledged = AlertHistory::forOrganization($this->organization->id)
                ->triggered()
                ->whereNull('acknowledged_at')
                ->count();

            expect($unacknowledged)->toBe(1);
        });
    });

    describe('acknowledgeAlert', function () {
        it('acknowledges alert with user and notes', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
                'triggered_at' => now(),
            ]);

            $result = $this->alertService->acknowledgeAlert($alert, $this->user->id, 'Under investigation');

            expect($result->status)->toBe('acknowledged')
                ->and($result->acknowledged_by)->toBe($this->user->id)
                ->and($result->notes)->toBe('Under investigation')
                ->and($result->acknowledged_at)->not->toBeNull();
        });

        it('acknowledges alert without notes', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
            ]);

            $result = $this->alertService->acknowledgeAlert($alert, $this->user->id);

            expect($result->status)->toBe('acknowledged')
                ->and($result->notes)->toBeNull();
        });
    });

    describe('resolveAlert', function () {
        it('resolves an alert', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'acknowledged',
            ]);

            $result = $this->alertService->resolveAlert($alert);

            expect($result->status)->toBe('resolved')
                ->and($result->resolved_at)->not->toBeNull();
        });
    });

    describe('muteAlert', function () {
        it('mutes an alert', function () {
            $alert = AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
            ]);

            $result = $this->alertService->muteAlert($alert);

            expect($result->status)->toBe('muted')
                ->and($result->muted_at)->not->toBeNull();
        });
    });

    describe('getUnacknowledgedAlerts', function () {
        it('returns only unacknowledged alerts', function () {
            AlertHistory::factory(3)->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
                'acknowledged_at' => null,
            ]);

            AlertHistory::factory(2)->create([
                'organization_id' => $this->organization->id,
                'status' => 'resolved',
            ]);

            $result = $this->alertService->getUnacknowledgedAlerts($this->organization->id);

            expect($result)->toHaveCount(3);
        });
    });

    describe('getCriticalAlerts', function () {
        it('returns only critical and high severity alerts', function () {
            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'critical',
                'status' => 'triggered',
            ]);

            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'high',
                'status' => 'triggered',
            ]);

            AlertHistory::factory()->create([
                'organization_id' => $this->organization->id,
                'severity' => 'medium',
                'status' => 'triggered',
            ]);

            $result = $this->alertService->getCriticalAlerts($this->organization->id);

            expect($result)->toHaveCount(2)
                ->and($result->pluck('severity'))->toContain('critical', 'high');
        });
    });

    describe('getAlertStatistics', function () {
        it('returns correct statistics', function () {
            // Create test data
            AlertHistory::factory(2)->create([
                'organization_id' => $this->organization->id,
                'status' => 'triggered',
                'acknowledged_at' => null,
                'triggered_at' => now(),
            ]);

            AlertHistory::factory(1)->create([
                'organization_id' => $this->organization->id,
                'severity' => 'critical',
                'status' => 'triggered',
            ]);

            $stats = $this->alertService->getAlertStatistics($this->organization->id);

            expect($stats)
                ->toHaveKey('unacknowledged_count', 2)
                ->toHaveKey('critical_count', 1)
                ->toHaveKey('recent_24h')
                ->toHaveKey('acknowledge_rate');
        });
    });

    describe('getAlertHistory', function () {
        it('returns recent alerts with relationships loaded', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
            ]);

            AlertHistory::factory(5)->create([
                'organization_id' => $this->organization->id,
                'alert_threshold_id' => $threshold->id,
            ]);

            $result = $this->alertService->getAlertHistory($this->organization->id, 10);

            expect($result)->toHaveCount(5);
            expect($result->first()->alertThreshold)->not->toBeNull();
        });

        it('respects limit parameter', function () {
            AlertHistory::factory(20)->create([
                'organization_id' => $this->organization->id,
            ]);

            $result = $this->alertService->getAlertHistory($this->organization->id, 5);

            expect($result)->toHaveCount(5);
        });
    });

    describe('createThresholds', function () {
        it('bulk creates thresholds from configuration', function () {
            $config = [
                ['metric' => 'cpu', 'threshold' => 85, 'severity' => 'high'],
                ['metric' => 'memory', 'threshold' => 90, 'severity' => 'high'],
                ['metric' => 'disk', 'threshold' => 95, 'severity' => 'critical'],
            ];

            $result = $this->alertService->createThresholds($this->organization->id, $config);

            expect($result)->toHaveCount(3);
            expect(AlertThreshold::forOrganization($this->organization->id)->count())->toBe(3);
        });
    });
});

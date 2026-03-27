<?php

use App\Models\{AlertHistory, AlertThreshold, Organization, User};

describe('AlertThreshold Model', function () {
    let $organization;

    beforeEach(function () {
        $this->organization = Organization::factory()->create();
    });

    it('belongs to organization', function () {
        $threshold = AlertThreshold::factory()->create(['organization_id' => $this->organization->id]);

        expect($threshold->organization()->first())->toBeInstanceOf(Organization::class)
            ->and($threshold->organization->id)->toBe($this->organization->id);
    });

    it('has many alert histories', function () {
        $threshold = AlertThreshold::factory()->create(['organization_id' => $this->organization->id]);
        AlertHistory::factory(3)->create(['alert_threshold_id' => $threshold->id]);

        expect($threshold->alertHistories())->toHaveCount(3);
    });

    describe('Scopes', function () {
        it('filters active thresholds', function () {
            AlertThreshold::factory()->create(['is_active' => true, 'organization_id' => $this->organization->id]);
            AlertThreshold::factory()->create(['is_active' => false, 'organization_id' => $this->organization->id]);

            expect(AlertThreshold::active()->count())->toBe(1);
        });

        it('filters by metric', function () {
            AlertThreshold::factory()->create(['metric' => 'cpu', 'organization_id' => $this->organization->id]);
            AlertThreshold::factory()->create(['metric' => 'memory', 'organization_id' => $this->organization->id]);

            expect(AlertThreshold::forMetric('cpu')->count())->toBe(1);
        });

        it('filters by organization', function () {
            $other = Organization::factory()->create();

            AlertThreshold::factory()->create(['organization_id' => $this->organization->id]);
            AlertThreshold::factory()->create(['organization_id' => $other->id]);

            expect(AlertThreshold::forOrganization($this->organization->id)->count())->toBe(1);
        });
    });

    describe('Methods', function () {
        it('determines if alert should trigger', function () {
            $threshold = AlertThreshold::factory()->create([
                'organization_id' => $this->organization->id,
                'threshold' => 80,
            ]);

            expect($threshold->shouldTrigger(85))->toBeTrue()
                ->and($threshold->shouldTrigger(75))->toBeFalse();
        });

        it('gets recent alerts', function () {
            $threshold = AlertThreshold::factory()->create(['organization_id' => $this->organization->id]);

            AlertHistory::factory()->create([
                'alert_threshold_id' => $threshold->id,
                'triggered_at' => now()->subMinutes(5),
            ]);

            AlertHistory::factory()->create([
                'alert_threshold_id' => $threshold->id,
                'triggered_at' => now()->subHours(2),
            ]);

            $recent = $threshold->recentAlerts(30);

            expect($recent)->toHaveCount(1);
        });
    });

    describe('Casts', function () {
        it('casts threshold to decimal', function () {
            $threshold = AlertThreshold::factory()->create(['threshold' => 85.5]);

            expect($threshold->threshold)->toBeFloat()
                ->and($threshold->threshold)->toBe(85.50);
        });

        it('casts is_active to boolean', function () {
            $threshold = AlertThreshold::factory()->create(['is_active' => 1]);

            expect($threshold->is_active)->toBeTrue();
        });
    });
});

describe('AlertHistory Model', function () {
    let $organization;
    let $user;

    beforeEach(function () {
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    });

    it('belongs to organization', function () {
        $history = AlertHistory::factory()->create(['organization_id' => $this->organization->id]);

        expect($history->organization()->first())->toBeInstanceOf(Organization::class);
    });

    it('belongs to alert threshold', function () {
        $threshold = AlertThreshold::factory()->create(['organization_id' => $this->organization->id]);
        $history = AlertHistory::factory()->create([
            'organization_id' => $this->organization->id,
            'alert_threshold_id' => $threshold->id,
        ]);

        expect($history->alertThreshold())->toBeInstanceOf(AlertThreshold::class)
            ->and($history->alertThreshold->id)->toBe($threshold->id);
    });

    it('belongs to acknowledged user', function () {
        $history = AlertHistory::factory()->create([
            'organization_id' => $this->organization->id,
            'acknowledged_by' => $this->user->id,
        ]);

        expect($history->acknowledgedBy()->first())->toBeInstanceOf(User::class);
    });

    describe('Scopes', function () {
        it('filters triggered alerts', function () {
            AlertHistory::factory()->create(['status' => 'triggered']);
            AlertHistory::factory()->create(['status' => 'resolved']);

            expect(AlertHistory::triggered()->count())->toBe(1);
        });

        it('filters acknowledged alerts', function () {
            AlertHistory::factory()->create([
                'status' => 'acknowledged',
                'acknowledged_at' => now(),
            ]);
            AlertHistory::factory()->create(['status' => 'triggered']);

            expect(AlertHistory::acknowledged()->count())->toBe(1);
        });

        it('filters resolved alerts', function () {
            AlertHistory::factory()->create([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
            AlertHistory::factory()->create(['status' => 'triggered']);

            expect(AlertHistory::resolved()->count())->toBe(1);
        });

        it('filters critical severity alerts', function () {
            AlertHistory::factory()->create(['severity' => 'critical']);
            AlertHistory::factory()->create(['severity' => 'high']);
            AlertHistory::factory()->create(['severity' => 'low']);

            expect(AlertHistory::critical()->count())->toBe(2); // critical + high
        });

        it('filters by organization', function () {
            $other = Organization::factory()->create();

            AlertHistory::factory()->create(['organization_id' => $this->organization->id]);
            AlertHistory::factory()->create(['organization_id' => $other->id]);

            expect(AlertHistory::forOrganization($this->organization->id)->count())->toBe(1);
        });

        it('filters recent alerts', function () {
            AlertHistory::factory()->create([
                'triggered_at' => now()->subHours(1),
            ]);

            AlertHistory::factory()->create([
                'triggered_at' => now()->subDays(3),
            ]);

            expect(AlertHistory::recent(120)->count())->toBe(1); // 2 hours
        });
    });

    describe('Methods', function () {
        it('determines if alert is unacknowledged', function () {
            $acknowledged = AlertHistory::factory()->create([
                'acknowledged_at' => now(),
            ]);

            $unacknowledged = AlertHistory::factory()->create([
                'acknowledged_at' => null,
            ]);

            expect($unacknowledged->isUnacknowledged())->toBeTrue()
                ->and($acknowledged->isUnacknowledged())->toBeFalse();
        });

        it('acknowledges an alert', function () {
            $history = AlertHistory::factory()->create([
                'status' => 'triggered',
                'acknowledged_at' => null,
                'acknowledged_by' => null,
            ]);

            $history->acknowledge($this->user, 'Fixed infrastructure issue');

            expect($history->fresh()->status)->toBe('acknowledged')
                ->and($history->fresh()->acknowledged_by)->toBe($this->user->id)
                ->and($history->fresh()->notes)->toBe('Fixed infrastructure issue')
                ->and($history->fresh()->acknowledged_at)->not->toBeNull();
        });

        it('resolves an alert', function () {
            $history = AlertHistory::factory()->create([
                'status' => 'acknowledged',
                'resolved_at' => null,
            ]);

            $history->resolve();

            expect($history->fresh()->status)->toBe('resolved')
                ->and($history->fresh()->resolved_at)->not->toBeNull();
        });

        it('mutes an alert', function () {
            $history = AlertHistory::factory()->create([
                'status' => 'triggered',
            ]);

            $history->mute();

            expect($history->fresh()->status)->toBe('muted');
        });
    });

    describe('Status Lifecycle', function () {
        it('follows triggered -> acknowledged -> resolved path', function () {
            $history = AlertHistory::factory()->create(['status' => 'triggered']);

            expect($history->status)->toBe('triggered');

            $history->acknowledge($user);
            expect($history->fresh()->status)->toBe('acknowledged');

            $history->fresh()->resolve();
            expect($history->fresh()->status)->toBe('resolved');
        });

        it('can go triggered -> muted directly', function () {
            $history = AlertHistory::factory()->create(['status' => 'triggered']);

            $history->mute();
            expect($history->fresh()->status)->toBe('muted');
        });
    });

    describe('Casts', function () {
        it('casts metric_value to float', function () {
            $history = AlertHistory::factory()->create(['metric_value' => 85.5]);

            expect($history->metric_value)->toBeFloat();
        });

        it('casts timestamps to DateTime', function () {
            $history = AlertHistory::factory()->create();

            expect($history->triggered_at)->toBeInstanceOf(\DateTime::class);
        });
    });
});

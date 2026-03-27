<?php

use App\Models\AuditLog;
use App\Models\AlertThreshold;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
    ]);
});

// ════════════════════════════════════════════════════════════════════════════
// 1. AUDIT LOG MODEL TESTS
// ════════════════════════════════════════════════════════════════════════════

describe('AuditLog Model', function () {
    it('creates audit log with all required fields', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '123',
            'changes' => ['threshold' => [10, 20]],
        ]);

        expect($log->id)->toBeTruthy();
        expect($log->action)->toBe('created');
        expect($log->entity_type)->toBe('AlertThreshold');
    });

    it('casts changes to array', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'action' => 'updated',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '123',
            'changes' => ['threshold' => [10, 20]],
        ]);

        expect($log->changes)->toBeArray();
        expect($log->changes['threshold'])->toBe([10, 20]);
    });

    it('belongs to organization', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '123',
        ]);

        expect($log->organization()->first()->id)->toBe($this->organization->id);
    });

    it('belongs to user', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '123',
        ]);

        expect($log->user()->first()->id)->toBe($this->user->id);
    });

    it('handles null user (system actions)', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => null,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '123',
        ]);

        expect($log->user_id)->toBeNull();
    });
});

// ════════════════════════════════════════════════════════════════════════════
// 2. SCOPES TESTS
// ════════════════════════════════════════════════════════════════════════════

describe('AuditLog Scopes', function () {
    it('filters by organization with forOrganization scope', function () {
        $org2 = Organization::factory()->create();

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        AuditLog::create([
            'organization_id' => $org2->id,
            'user_id' => $this->user->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '2',
        ]);

        $logs = AuditLog::forOrganization($this->organization->id)->get();
        expect($logs)->toHaveCount(1);
        expect($logs->first()->organization_id)->toBe($this->organization->id);
    });

    it('filters by action with action scope', function () {
        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'updated',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        $logs = AuditLog::forOrganization($this->organization->id)->action('created')->get();
        expect($logs)->toHaveCount(1);
        expect($logs->first()->action)->toBe('created');
    });

    it('filters by entity with forEntity scope', function () {
        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'updated',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '2',
        ]);

        $logs = AuditLog::forEntity('AlertThreshold', '1')->get();
        expect($logs)->toHaveCount(2);
    });

    it('filters by user with createdBy scope', function () {
        $user2 = User::factory()->create(['organization_id' => $this->organization->id]);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'user_id' => $user2->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '2',
        ]);

        $logs = AuditLog::forOrganization($this->organization->id)->createdBy($this->user->id)->get();
        expect($logs)->toHaveCount(1);
        expect($logs->first()->user_id)->toBe($this->user->id);
    });

    it('filters by triggered_by source', function () {
        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
            'triggered_by' => 'user',
        ]);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '2',
            'triggered_by' => 'system',
        ]);

        $logs = AuditLog::forOrganization($this->organization->id)->triggeredBy('user')->get();
        expect($logs)->toHaveCount(1);
        expect($logs->first()->triggered_by)->toBe('user');
    });

    it('filters recent logs by days', function () {
        $recent = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
            'created_at' => now(),
        ]);

        $old = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '2',
            'created_at' => now()->subDays(60),
        ]);

        $logs = AuditLog::forOrganization($this->organization->id)->recent(30)->get();
        expect($logs)->toHaveCount(1);
        expect($logs->first()->id)->toBe($recent->id);
    });
});

// ════════════════════════════════════════════════════════════════════════════
// 3. OBSERVER TESTS (Auto-tracking)
// ════════════════════════════════════════════════════════════════════════════

describe('AuditObserver - Auto-tracking CRUD', function () {
    it('logs model creation', function () {
        $this->actingAs($this->user);

        $threshold = AlertThreshold::create([
            'organization_id' => $this->organization->id,
            'metric' => 'cpu_usage',
            'threshold' => 80,
            'severity' => 'high',
        ]);

        $log = AuditLog::latest()->first();
        expect($log->action)->toBe('created');
        expect($log->entity_type)->toBe('AlertThreshold');
        expect($log->entity_id)->toBe((string) $threshold->id);
        expect($log->user_id)->toBe($this->user->id);
    });

    it('logs model update with changes', function () {
        $this->actingAs($this->user);

        $threshold = AlertThreshold::create([
            'organization_id' => $this->organization->id,
            'metric' => 'cpu_usage',
            'threshold' => 80,
            'severity' => 'high',
        ]);

        // Clear previous logs
        AuditLog::truncate();

        // Update model
        $threshold->update(['threshold' => 90]);

        $log = AuditLog::latest()->first();
        expect($log->action)->toBe('updated');
        expect($log->changes['threshold'])->toBe([80, 90]);
    });

    it('logs model deletion', function () {
        $this->actingAs($this->user);

        $threshold = AlertThreshold::create([
            'organization_id' => $this->organization->id,
            'metric' => 'cpu_usage',
            'threshold' => 80,
            'severity' => 'high',
        ]);

        // Clear logs
        AuditLog::truncate();

        // Delete
        $threshold->delete();

        $log = AuditLog::latest()->first();
        expect($log->action)->toBe('deleted');
        expect($log->entity_type)->toBe('AlertThreshold');
    });

    it('skips audit if no organization context', function () {
        // Don't act as user (no auth context)
        $threshold = AlertThreshold::create([
            'organization_id' => null,
            'metric' => 'cpu_usage',
            'threshold' => 80,
            'severity' => 'high',
        ]);

        // Should not create audit log without org context
        expect(AuditLog::count())->toBe(0);
    });

    it('captures metadata (IP, user agent)', function () {
        $this->actingAs($this->user);

        AlertThreshold::create([
            'organization_id' => $this->organization->id,
            'metric' => 'cpu_usage',
            'threshold' => 80,
            'severity' => 'high',
        ]);

        $log = AuditLog::latest()->first();
        expect($log->metadata)->toBeArray();
        expect($log->metadata)
            ->toHaveKeys(['model_class', 'ip_address', 'user_agent']);
    });
});

// ════════════════════════════════════════════════════════════════════════════
// 4. HELPER METHODS TESTS
// ════════════════════════════════════════════════════════════════════════════

describe('AuditLog Helpers', function () {
    it('identifies creation action', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        expect($log->isCreation())->toBeTrue();
        expect($log->isUpdate())->toBeFalse();
        expect($log->isDeletion())->toBeFalse();
    });

    it('identifies update action', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'updated',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        expect($log->isUpdate())->toBeTrue();
    });

    it('identifies deletion action', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'deleted',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        expect($log->isDeletion())->toBeTrue();
    });

    it('generates change summary', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'updated',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
            'changes' => [
                'threshold' => [80, 90],
                'severity' => ['high', 'critical'],
            ],
        ]);

        $summary = $log->getChangeSummary();
        expect($summary)->toContain('threshold');
        expect($summary)->toContain('80');
        expect($summary)->toContain('90');
    });

    it('returns friendly message for no changes', function () {
        $log = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        expect($log->getChangeSummary())->toBe('No changes recorded');
    });
});

// ════════════════════════════════════════════════════════════════════════════
// 5. MULTI-TENANT ISOLATION TESTS
// ════════════════════════════════════════════════════════════════════════════

describe('Audit Log Multi-Tenancy', function () {
    it('prevents cross-organization data access', function () {
        $org2 = Organization::factory()->create();
        $user2 = User::factory()->create(['organization_id' => $org2->id]);

        $this->actingAs($this->user);

        AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        // Switch to org2
        $this->actingAs($user2);

        $logs = AuditLog::forOrganization($org2->id)->get();
        expect($logs)->toHaveCount(0);
    });

    it('scopes all queries by organization automatically', function () {
        $logs1 = AuditLog::create([
            'organization_id' => $this->organization->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '1',
        ]);

        $org2 = Organization::factory()->create();
        $logs2 = AuditLog::create([
            'organization_id' => $org2->id,
            'action' => 'created',
            'entity_type' => 'AlertThreshold',
            'entity_id' => '2',
        ]);

        $count1 = AuditLog::forOrganization($this->organization->id)->count();
        $count2 = AuditLog::forOrganization($org2->id)->count();

        expect($count1)->toBe(1);
        expect($count2)->toBe(1);
    });
});

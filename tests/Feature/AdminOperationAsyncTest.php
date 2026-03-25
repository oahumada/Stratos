<?php

use App\Jobs\AdminOperationJob;
use App\Models\AdminOperationAudit;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use App\Services\AdminOperationLockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->admin = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);

    People::factory()->create([
        'user_id' => $this->admin->id,
        'organization_id' => $this->organization->id,
    ]);
});

// ============ ASYNC EXECUTION & JOB DISPATCH ============

it('queued status is set when operation execute is called', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ])
        ->assertSuccessful();

    $audit->refresh();
    expect($audit->status)->toEqual('queued');
});

it('dispatches AdminOperationJob when executing', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ]);

    Queue::assertPushed(AdminOperationJob::class, function ($job) use ($audit) {
        return $job->auditId === $audit->id;
    });
});

it('job has correct organization scope', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ]);

    Queue::assertPushed(AdminOperationJob::class, function ($job) {
        return $job->organizationId === $this->organization->id;
    });
});

// ============ OPERATION STATUS TRANSITIONS ============

it('marks operation as running during execution', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'queued',
        'operation_type' => 'backfill',
    ]);

    $audit->markAsRunning();

    $audit->refresh();
    expect($audit->status)->toEqual('running');
    expect($audit->started_at)->not->toBeNull();
});

it('marks operation as success with results', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $result = ['records_backfilled' => 42];
    $audit->markAsSuccess($result, 42, 42);

    $audit->refresh();
    expect($audit->status)->toEqual('success');
    expect($audit->result)->toEqual($result);
    expect($audit->records_processed)->toEqual(42);
    expect($audit->completed_at)->not->toBeNull();
});

it('marks operation as failed with error', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $audit->markAsFailed('Database connection failed');

    $audit->refresh();
    expect($audit->status)->toEqual('failed');
    expect($audit->error_message)->toEqual('Database connection failed');
    expect($audit->completed_at)->not->toBeNull();
});

it('calculates operation duration', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
        'started_at' => now()->subSeconds(30),
    ]);

    $audit->markAsSuccess([]);
    $audit->refresh();

    expect((int) $audit->duration_seconds)->toBeGreaterThanOrEqual(30);
});

// ============ CONCURRENCY LOCK TESTS ============

it('acquires lock for operation', function () {
    $lockService = app(AdminOperationLockService::class);

    $acquired = $lockService->acquire(1, 'backfill', 1);

    expect($acquired)->toBeTrue();
    $lockService->release(1, 'backfill');
});

it('prevents concurrent access to same operation', function () {
    $lockService = app(AdminOperationLockService::class);

    $lock1 = $lockService->acquire(1, 'backfill', 1);
    expect($lock1)->toBeTrue();

    // Second attempt should fail
    $lock2 = $lockService->acquire(1, 'backfill', 0);
    expect($lock2)->toBeFalse();

    $lockService->release(1, 'backfill');
});

it('allows concurrent locks for different operation types', function () {
    $lockService = app(AdminOperationLockService::class);

    $lock1 = $lockService->acquire(1, 'backfill', 1);
    expect($lock1)->toBeTrue();

    // Different operation type should be allowed
    $lock2 = $lockService->acquire(1, 'generate', 1);
    expect($lock2)->toBeTrue();

    $lockService->release(1, 'backfill');
    $lockService->release(1, 'generate');
});

it('allows concurrent locks for different organizations', function () {
    $lockService = app(AdminOperationLockService::class);

    $lock1 = $lockService->acquire(1, 'backfill', 1);
    expect($lock1)->toBeTrue();

    // Different organization should be allowed
    $lock2 = $lockService->acquire(2, 'backfill', 1);
    expect($lock2)->toBeTrue();

    $lockService->release(1, 'backfill');
    $lockService->release(2, 'backfill');
});

it('releases locks properly', function () {
    $lockService = app(AdminOperationLockService::class);

    $lockService->acquire(1, 'backfill', 1);
    $released = $lockService->release(1, 'backfill');

    expect($released)->toBeTrue();
});

it('executes callback with lock protection', function () {
    $lockService = app(AdminOperationLockService::class);
    $executed = false;

    $result = $lockService->withLock(1, 'backfill', function () use (&$executed) {
        $executed = true;

        return 'done';
    });

    expect($executed)->toBeTrue();
    expect($result)->toEqual('done');
});

it('prevents execution without confirmation', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => false,
        ]);

    $response->assertStatus(422);
    Queue::assertNotPushed(AdminOperationJob::class);
});

it('prevents cancellation of running operations', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/cancel");

    $response->assertStatus(422);
});

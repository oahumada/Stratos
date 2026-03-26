<?php

namespace Tests\Feature\Admin;

use App\Jobs\AdminOperationJob;
use App\Models\AdminOperationAudit;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use App\Services\AdminOperationLockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

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

// ============ ASYNC EXECUTION TESTS ============
it('dispatches job when operation is executed', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ]);

    $response->assertSuccessful();
    $response->assertJson([
        'status' => 'queued',
    ]);

    Queue::assertPushed(AdminOperationJob::class);
});

it('updates operation status to queued immediately', function () {
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

    $audit->refresh();
    expect($audit->status)->toEqual('queued');
});

it('prevents execution of non-pending operations', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ]);

    $response->assertStatus(422);
    Queue::assertNotPushed(AdminOperationJob::class);
});

it('marks operation as running when executed', function () {
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

it('marks operation as success after completion', function () {
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

it('marks operation as failed with error message', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $errorMessage = 'Database connection failed';
    $audit->markAsFailed($errorMessage);

    $audit->refresh();
    expect($audit->status)->toEqual('failed');
    expect($audit->error_message)->toEqual($errorMessage);
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
it('acquires lock successfully', function () {
    $lockService = app(AdminOperationLockService::class);
    $result = $lockService->acquire(1, 'backfill');

    expect($result)->toBeTrue();
    $lockService->release(1, 'backfill');
});

it('enforces mutual exclusion (blocks concurrent access)', function () {
    $lockService = app(AdminOperationLockService::class);

    $lock1 = $lockService->acquire(1, 'backfill', 1);
    expect($lock1)->toBeTrue();

    // Second attempt fails (non-blocking)
    $lock2 = $lockService->acquire(1, 'backfill', 0);
    expect($lock2)->toBeFalse();

    $lockService->release(1, 'backfill');
});

it('allows locks for different operation types concurrently', function () {
    $lockService = app(AdminOperationLockService::class);

    $lock1 = $lockService->acquire(1, 'backfill', 1);
    expect($lock1)->toBeTrue();

    $lock2 = $lockService->acquire(1, 'generate', 1);
    expect($lock2)->toBeTrue();

    $lockService->release(1, 'backfill');
    $lockService->release(1, 'generate');
});

it('allows locks for different organizations concurrently', function () {
    $lockService = app(AdminOperationLockService::class);

    $lock1 = $lockService->acquire(1, 'backfill', 1);
    expect($lock1)->toBeTrue();

    $lock2 = $lockService->acquire(2, 'backfill', 1);
    expect($lock2)->toBeTrue();

    $lockService->release(1, 'backfill');
    $lockService->release(2, 'backfill');
});

it('detects when lock is held', function () {
    $lockService = app(AdminOperationLockService::class);

    $lockService->acquire(1, 'backfill', 1);
    expect($lockService->isLocked(1, 'backfill'))->toBeTrue();

    $lockService->release(1, 'backfill');
    expect($lockService->isLocked(1, 'backfill'))->toBeFalse();
});

it('releases lock successfully', function () {
    $lockService = app(AdminOperationLockService::class);

    $lockService->acquire(1, 'backfill', 1);
    expect($lockService->isLocked(1, 'backfill'))->toBeTrue();

    $result = $lockService->release(1, 'backfill');
    expect($result)->toBeTrue();
});

it('executes callback with lock protection', function () {
    $lockService = app(AdminOperationLockService::class);
    $executed = false;

    $result = $lockService->withLock(1, 'backfill', function () use (&$executed) {
        $executed = true;

        return 'success';
    });

    expect($executed)->toBeTrue();
    expect($result)->toEqual('success');
    expect($lockService->isLocked(1, 'backfill'))->toBeFalse();
});

it('returns null when callback lock acquisition fails', function () {
    $lockService = app(AdminOperationLockService::class);

    $lockService->acquire(1, 'backfill', 10);

    $result = $lockService->withLock(1, 'backfill', function () {
        return 'should not execute';
    }, 0);

    expect($result)->toBeNull();
    $lockService->release(1, 'backfill');
});

it('releases lock even if callback throws exception', function () {
    $lockService = app(AdminOperationLockService::class);

    try {
        $lockService->withLock(1, 'backfill', function () {
            throw new \Exception('Test exception');
        });
    } catch (\Exception $e) {
        // Expected
    }

    expect($lockService->isLocked(1, 'backfill'))->toBeFalse();
});

// ============ STATUS TRANSITIONS TESTS ============
it('lifecycle pending → queued → (async: running → success)', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    expect($audit->status)->toEqual('pending');

    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ]);

    $response->assertSuccessful();
    $audit->refresh();
    expect($audit->status)->toEqual('queued');

    Queue::assertPushed(AdminOperationJob::class);
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

it('cancels pending operations', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/cancel");

    $response->assertSuccessful();

    $audit->refresh();
    expect($audit->status)->toEqual('cancelled');
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

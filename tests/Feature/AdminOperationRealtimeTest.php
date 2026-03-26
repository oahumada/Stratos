<?php

use App\Events\AdminOperationCompleted;
use App\Events\AdminOperationFailed;
use App\Events\AdminOperationQueued;
use App\Events\AdminOperationStarted;
use App\Models\AdminOperationAudit;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Event::fake();

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

// ============ REAL-TIME EVENT BROADCAST ============

it('broadcasts AdminOperationQueued when operation is dispatched', function () {
    Queue::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    actingAs($this->admin)
        ->postJson("/api/admin/operations/$audit->id/execute", [
            'confirmed' => true,
        ]);

    Event::assertDispatched(AdminOperationQueued::class, function ($event) use ($audit) {
        return $event->operation->id === $audit->id
            && $event->operation->status === 'queued';
    });
});

it('broadcasts AdminOperationStarted when job begins execution', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'queued',
        'operation_type' => 'backfill',
    ]);

    $audit->markAsRunning();

    AdminOperationStarted::dispatch($audit);

    Event::assertDispatched(AdminOperationStarted::class, function ($event) use ($audit) {
        return $event->operation->id === $audit->id
            && $event->operation->status === 'running';
    });
});

it('broadcasts AdminOperationCompleted when operation succeeds', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $result = ['records_backfilled' => 50];
    $audit->markAsSuccess($result, 50);

    AdminOperationCompleted::dispatch($audit);

    Event::assertDispatched(AdminOperationCompleted::class, function ($event) use ($audit, $result) {
        return $event->operation->id === $audit->id
            && $event->operation->status === 'success'
            && $event->operation->result === $result;
    });
});

it('broadcasts AdminOperationFailed when operation fails', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'running',
        'operation_type' => 'backfill',
    ]);

    $audit->markAsFailed('Database connection error');

    AdminOperationFailed::dispatch($audit);

    Event::assertDispatched(AdminOperationFailed::class, function ($event) use ($audit) {
        return $event->operation->id === $audit->id
            && $event->operation->status === 'failed'
            && str_contains($event->operation->error_message, 'Database connection error');
    });
});

// ============ BROADCAST CHANNEL SCOPING ============

it('broadcasts to organization-specific channel', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $event = new AdminOperationQueued($audit);
    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(1);
    expect($channels[0]->name)->toEqual("admin-operations.org-{$this->organization->id}");
});

it('broadcasts correct event name', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $queuedEvent = new AdminOperationQueued($audit);
    expect($queuedEvent->broadcastAs())->toEqual('operation.queued');

    $startedEvent = new AdminOperationStarted($audit);
    expect($startedEvent->broadcastAs())->toEqual('operation.started');

    $completedEvent = new AdminOperationCompleted($audit);
    expect($completedEvent->broadcastAs())->toEqual('operation.completed');

    $failedEvent = new AdminOperationFailed($audit);
    expect($failedEvent->broadcastAs())->toEqual('operation.failed');
});

// ============ BROADCAST PAYLOAD ============

it('includes operation details in broadcast payload', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'queued',
        'operation_type' => 'backfill',
    ]);

    $event = new AdminOperationQueued($audit);
    $payload = $event->broadcastWith();

    expect($payload)->toHaveKeys(['id', 'organization_id', 'operation_type', 'status', 'queued_at']);
    expect($payload['id'])->toEqual($audit->id);
    expect($payload['organization_id'])->toEqual($this->organization->id);
    expect($payload['operation_type'])->toEqual('backfill');
});

it('includes result data in completed event payload', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'success',
        'operation_type' => 'backfill',
        'result' => ['records' => 100],
        'records_processed' => 100,
        'duration_seconds' => 45.5,
    ]);

    $event = new AdminOperationCompleted($audit);
    $payload = $event->broadcastWith();

    expect($payload)->toHaveKeys(['id', 'result', 'records_processed', 'duration_seconds']);
    expect($payload['result'])->toEqual(['records' => 100]);
    expect($payload['records_processed'])->toEqual(100);
});

it('includes error message in failed event payload', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'failed',
        'operation_type' => 'backfill',
        'error_message' => 'Connection timeout',
    ]);

    $event = new AdminOperationFailed($audit);
    $payload = $event->broadcastWith();

    expect($payload)->toHaveKeys(['id', 'status', 'error_message']);
    expect($payload['error_message'])->toEqual('Connection timeout');
});

// ============ MULTI-TENANT ISOLATION ============

it('does not broadcast to other organizations', function () {
    $org2 = Organization::factory()->create();
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
        'operation_type' => 'backfill',
    ]);

    $event = new AdminOperationQueued($audit);
    $channels = $event->broadcastOn();

    // Channel should only be for $this->organization->id, not $org2->id
    expect($channels[0]->name)->not->toContain("org-{$org2->id}");
    expect($channels[0]->name)->toContain("org-{$this->organization->id}");
});

// ============ EVENT TIMESTAMPS ============

it('includes ISO 8601 timestamps in event payloads', function () {
    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'queued',
        'operation_type' => 'backfill',
    ]);

    $event = new AdminOperationQueued($audit);
    $payload = $event->broadcastWith();

    expect($payload['queued_at'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
});

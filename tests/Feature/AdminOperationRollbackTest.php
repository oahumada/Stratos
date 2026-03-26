<?php

use App\Events\AdminOperationRolledBack;
use App\Models\AdminOperationAudit;
use App\Services\AdminOperationRollbackService;
use Illuminate\Support\Facades\Event;

describe('AdminOperationRollback', function () {
    beforeEach(function () {
        Event::fake();
    });

    describe('Rollback Service', function () {
        it('can create snapshot for backfill operation', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'running',
            ]);

            $service = app(AdminOperationRollbackService::class);
            $snapshot = $service->createSnapshot($audit);

            expect($snapshot)->toHaveKey('table', 'records')
                ->toHaveKey('timestamp')
                ->toHaveKey('created_ids');
        });

        it('can create snapshot for generate operation', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'generate',
                'status' => 'running',
            ]);

            $service = app(AdminOperationRollbackService::class);
            $snapshot = $service->createSnapshot($audit);

            expect($snapshot)->toHaveKey('table', 'generated_items')
                ->toHaveKey('timestamp')
                ->toHaveKey('generated_ids');
        });

        it('can identify operations that can be rolled back', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => ['created_ids' => [1, 2, 3]],
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeTrue();
        });

        it('cannot rollback operation without snapshot', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => null,
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeFalse();
        });

        it('cannot rollback operation not in failed status', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'completed',
                'dry_run_preview' => ['created_ids' => [1, 2, 3]],
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeFalse();
        });

        it('cannot rollback cleanup operations', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'cleanup',
                'status' => 'failed',
                'dry_run_preview' => ['timestamp' => now()],
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeFalse();
        });

        it('can rollback generate operations', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'generate',
                'status' => 'failed',
                'dry_run_preview' => ['generated_ids' => []],
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeTrue();
        });

        it('can rollback import operations', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'import',
                'status' => 'failed',
                'dry_run_preview' => ['imported_ids' => []],
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeTrue();
        });
    });

    describe('Rollback Execution', function () {
        it('marks operation as rolled_back after successful rollback', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => ['created_ids' => [], 'table' => 'records'],
                'error_message' => 'Operation failed',
            ]);

            $service = app(AdminOperationRollbackService::class);
            $result = $service->performRollback($audit);

            expect($result)->toBeTrue();
            expect($audit->fresh()->status)->toBe('rolled_back');
            expect($audit->fresh()->error_message)->toContain('rolled back');
        });

        it('broadcasts rolled_back event after successful rollback', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => ['created_ids' => [], 'table' => 'records'],
            ]);

            $service = app(AdminOperationRollbackService::class);
            $service->performRollback($audit);

            Event::assertDispatched(AdminOperationRolledBack::class, function ($event) use ($audit) {
                return $event->audit->id === $audit->id;
            });
        });

        it('handles failed rollback gracefully', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => null,
            ]);

            $service = app(AdminOperationRollbackService::class);
            $result = $service->performRollback($audit);

            expect($result)->toBeFalse();
            expect($audit->fresh()->status)->toBe('failed');
        });
    });

    describe('Rollback with Job Failure', function () {
        it('attempts automatic rollback when job fails', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => ['created_ids' => [], 'table' => 'records'],
            ]);

            $service = app(AdminOperationRollbackService::class);

            expect($service->canRollback($audit))->toBeTrue();

            if ($service->canRollback($audit)) {
                $service->performRollback($audit);
            }

            expect($audit->fresh()->status)->toBe('rolled_back');
        });

        it('sends successful rollback to frontend via event', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'generate',
                'status' => 'failed',
                'dry_run_preview' => ['generated_ids' => [], 'table' => 'generated_items'],
            ]);

            $service = app(AdminOperationRollbackService::class);
            $service->performRollback($audit);

            Event::assertDispatched(AdminOperationRolledBack::class, function ($event) {
                return $event->audit->status === 'rolled_back'
                    && $event->broadcastAs() === 'operation.rolled_back'
                    && str_contains($event->broadcastOn()[0]->name, 'admin-operations');
            });
        });

        it('includes rollback timestamp in event payload', function () {
            $audit = AdminOperationAudit::factory()->create([
                'operation_type' => 'backfill',
                'status' => 'failed',
                'dry_run_preview' => ['created_ids' => [], 'table' => 'records'],
            ]);

            $service = app(AdminOperationRollbackService::class);
            $service->performRollback($audit);

            Event::assertDispatched(AdminOperationRolledBack::class, function ($event) {
                $payload = $event->broadcastWith();

                return isset($payload['rolled_back_at'])
                    && ! empty($payload['rolled_back_at']);
            });
        });

        it('can rollback multiple operation types with same interface', function () {
            $operationTypes = ['backfill', 'generate', 'import'];

            foreach ($operationTypes as $type) {
                $audit = AdminOperationAudit::factory()->create([
                    'operation_type' => $type,
                    'status' => 'failed',
                    'dry_run_preview' => ['timestamp' => now()->toIso8601String()],
                ]);

                $service = app(AdminOperationRollbackService::class);

                expect($service->canRollback($audit))->toBeTrue();
                expect($service->performRollback($audit))->toBeTrue();
                expect($audit->fresh()->status)->toBe('rolled_back');
            }
        });
    });
});

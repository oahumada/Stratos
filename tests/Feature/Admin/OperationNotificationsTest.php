<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\AdminOperationAudit;
use App\Services\AdminOperationsService;
use App\Notifications\OperationCompletedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->admin = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);

    $this->service = app(AdminOperationsService::class);
});

test('admins receive notification on successful operation', function () {
    Notification::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
    ]);

    $this->service->executeOperation($audit, function () {
        return [
            'result' => ['ok' => true],
            'records_processed' => 10,
            'records_affected' => 5,
        ];
    });

    Notification::assertSentTo($this->admin, OperationCompletedNotification::class);
});

test('admins receive notification on failed operation', function () {
    Notification::fake();

    $audit = AdminOperationAudit::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->admin->id,
        'status' => 'pending',
    ]);

    try {
        $this->service->executeOperation($audit, function () {
            throw new \Exception('Simulated failure');
        });
    } catch (\Exception $e) {
        // expected
    }

    Notification::assertSentTo($this->admin, OperationCompletedNotification::class);
});

<?php

use App\Models\Scenario;
use App\Models\User;
use App\Services\ScenarioPlanning\ScenarioWorkflowService;
use Laravel\Sanctum\Sanctum;

describe('ScenarioWorkflowService', function () {
    beforeEach(function () {
        $this->service = app(ScenarioWorkflowService::class);
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
    });

    describe('submitForApproval', function () {
        it('transitions scenario from draft to pending_approval', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'draft',
            ]);

            $result = $this->service->submitForApproval(
                $scenario->id,
                $this->adminUser->id,
                'Please review this scenario'
            );

            expect($result['status'])->toBe('success');
            expect($result['decision_status'])->toBe('pending_approval');
            expect($result['approvals_required'])->toBeGreaterThan(0);

            $scenario->refresh();
            expect($scenario->decision_status)->toBe('pending_approval');
            expect($scenario->submitted_by)->toBe($this->adminUser->id);
            expect($scenario->submitted_at)->not->toBeNull();
        });

        it('cannot submit scenario if not in draft state', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'approved',
            ]);

            $result = $this->service->submitForApproval($scenario->id, $this->adminUser->id);

            expect($result['status'])->toBe('error');
            expect($result['message'])->toContain('Cannot submit scenario');
        });

        it('creates approval requests for each approver', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'draft',
            ]);

            $result = $this->service->submitForApproval($scenario->id, $this->adminUser->id);

            expect($result['status'])->toBe('success');
            expect(count($result['approval_requests']))->toBeGreaterThan(0);

            foreach ($result['approval_requests'] as $ar) {
                expect($ar['status'])->toBe('pending');
                expect($ar['token'])->not->toBeNull();
                expect($ar['expires_at'])->not->toBeNull();
            }
        });
    });

    describe('approve', function () {
        it('approves an approval request', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'pending_approval',
            ]);

            // Create approval request
            $approvalRequest = \App\Models\ApprovalRequest::create([
                'token' => \Illuminate\Support\Str::uuid(),
                'approvable_type' => Scenario::class,
                'approvable_id' => $scenario->id,
                'approver_id' => $this->adminUser->id,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            $result = $this->service->approve($approvalRequest->id, $this->adminUser->id, 'Looks good');

            expect($result['status'])->toBe('success');

            $approvalRequest->refresh();
            expect($approvalRequest->status)->toBe('approved');
            expect($approvalRequest->signed_at)->not->toBeNull();
        });

        it('only assigned approver can approve', function () {
            $anotherUser = User::factory()->create();
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'pending_approval',
            ]);

            $approvalRequest = \App\Models\ApprovalRequest::create([
                'token' => \Illuminate\Support\Str::uuid(),
                'approvable_type' => Scenario::class,
                'approvable_id' => $scenario->id,
                'approver_id' => $this->adminUser->id,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            $result = $this->service->approve($approvalRequest->id, $anotherUser->id);

            expect($result['status'])->toBe('error');
            expect($result['message'])->toContain('assigned approver');
        });

        it('transitions scenario to approved when all approvals complete', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'pending_approval',
            ]);

            // Create single approval request
            $approvalRequest = \App\Models\ApprovalRequest::create([
                'token' => \Illuminate\Support\Str::uuid(),
                'approvable_type' => Scenario::class,
                'approvable_id' => $scenario->id,
                'approver_id' => $this->adminUser->id,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            $result = $this->service->approve($approvalRequest->id, $this->adminUser->id);

            expect($result['status'])->toBe('success');
            expect($result['decision_status'])->toBe('approved');

            $scenario->refresh();
            expect($scenario->decision_status)->toBe('approved');
            expect($scenario->approved_by)->toBe($this->adminUser->id);
            expect($scenario->approved_at)->not->toBeNull();
        });
    });

    describe('reject', function () {
        it('rejects approval and reverts scenario to draft', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'pending_approval',
            ]);

            $approvalRequest = \App\Models\ApprovalRequest::create([
                'token' => \Illuminate\Support\Str::uuid(),
                'approvable_type' => Scenario::class,
                'approvable_id' => $scenario->id,
                'approver_id' => $this->adminUser->id,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            $result = $this->service->reject($approvalRequest->id, $this->adminUser->id, 'Need more analysis');

            expect($result['status'])->toBe('success');
            expect($result['decision_status'])->toBe('draft');
            expect($result['rejection_reason'])->toBe('Need more analysis');

            $scenario->refresh();
            expect($scenario->decision_status)->toBe('draft');
            expect($scenario->rejected_by)->toBe($this->adminUser->id);
            expect($scenario->rejection_reason)->toBe('Need more analysis');
        });

        it('only assigned approver can reject', function () {
            $anotherUser = User::factory()->create();
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'pending_approval',
            ]);

            $approvalRequest = \App\Models\ApprovalRequest::create([
                'token' => \Illuminate\Support\Str::uuid(),
                'approvable_type' => Scenario::class,
                'approvable_id' => $scenario->id,
                'approver_id' => $this->adminUser->id,
                'status' => 'pending',
            ]);

            $result = $this->service->reject($approvalRequest->id, $anotherUser->id, 'reason');

            expect($result['status'])->toBe('error');
        });
    });

    describe('activate', function () {
        it('activates approved scenario and generates execution plan', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'approved',
                'time_horizon_weeks' => 26,
            ]);

            $result = $this->service->activate($scenario->id, $this->adminUser->id);

            expect($result['status'])->toBe('success');
            expect($result['decision_status'])->toBe('active');
            expect($result['execution_plan'])->not->toBeNull();
            expect(count($result['execution_plan']['phases']))->toBeGreaterThan(0);
            expect(count($result['execution_plan']['milestones']))->toBeGreaterThan(0);
            expect(count($result['execution_plan']['tasks']))->toBeGreaterThan(0);

            $scenario->refresh();
            expect($scenario->decision_status)->toBe('active');
            expect($scenario->execution_status)->toBe('planned');
        });

        it('cannot activate if not approved', function () {
            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'decision_status' => 'draft',
            ]);

            $result = $this->service->activate($scenario->id, $this->adminUser->id);

            expect($result['status'])->toBe('error');
            expect($result['message'])->toContain('must be \'approved\'');
        });
    });

    describe('canScenarioBeEdited', function () {
        it('allows editing in draft state', function () {
            $scenario = Scenario::factory()->create(['decision_status' => 'draft']);
            expect($this->service->canScenarioBeEdited($scenario))->toBeTrue();
        });

        it('allows editing after rejection', function () {
            $scenario = Scenario::factory()->create(['decision_status' => 'rejected']);
            expect($this->service->canScenarioBeEdited($scenario))->toBeTrue();
        });

        it('prevents editing when pending approval', function () {
            $scenario = Scenario::factory()->create(['decision_status' => 'pending_approval']);
            expect($this->service->canScenarioBeEdited($scenario))->toBeFalse();
        });

        it('prevents editing when approved', function () {
            $scenario = Scenario::factory()->create(['decision_status' => 'approved']);
            expect($this->service->canScenarioBeEdited($scenario))->toBeFalse();
        });

        it('prevents editing when active', function () {
            $scenario = Scenario::factory()->create(['decision_status' => 'active']);
            expect($this->service->canScenarioBeEdited($scenario))->toBeFalse();
        });
    });
});

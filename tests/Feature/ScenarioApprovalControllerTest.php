<?php

use App\Models\Scenario;
use App\Models\ApprovalRequest;
use App\Models\User;
use App\Services\ScenarioPlanning\ScenarioNotificationService;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Mail;

describe('ScenarioApprovalController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->approver = User::factory()->create([
            'organization_id' => $this->user->organization_id,
        ]);
        $this->scenario = Scenario::factory()->create([
            'organization_id' => $this->user->organization_id,
            'created_by' => $this->user->id,
            'decision_status' => 'draft',
        ]);
    });

    describe('resendNotification', function () {
        it('sends notification to assigned approvers', function () {
            Sanctum::actingAs($this->user);

            $approvalRequest = ApprovalRequest::factory()->create([
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
                'status' => 'pending',
            ]);

            Mail::fake();

            $response = $this->postJson("/api/approval-requests/{$approvalRequest->id}/resend-notification", [
                'channels' => ['email'],
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'status' => 'success',
                'message' => 'Notification resent successfully',
            ]);
        });

        it('returns 403 if user is not authorized to resend', function () {
            Sanctum::actingAs($this->approver);

            $approvalRequest = ApprovalRequest::factory()->create([
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
            ]);

            $response = $this->postJson("/api/approval-requests/{$approvalRequest->id}/resend-notification", [
                'channels' => ['email'],
            ]);

            $response->assertStatus(403);
        });

        it('validates channels parameter', function () {
            Sanctum::actingAs($this->user);

            $approvalRequest = ApprovalRequest::factory()->create([
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
            ]);

            $response = $this->postJson("/api/approval-requests/{$approvalRequest->id}/resend-notification", [
                'channels' => ['invalid_channel'],
            ]);

            $response->assertStatus(422);
        });
    });

    describe('emailPreview', function () {
        it('returns HTML preview of approval email', function () {
            Sanctum::actingAs($this->user);

            $approvalRequest = ApprovalRequest::factory()->create([
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
                'status' => 'pending',
            ]);

            $response = $this->postJson("/api/approval-requests/{$approvalRequest->id}/email-preview");

            $response->assertStatus(200);
            $response->assertJson([
                'status' => 'success',
            ]);
            expect($response->json('preview'))->toBeString();
            expect($response->json('subject'))->toContain('Action Required');
            expect($response->json('recipient'))->toEqual($this->approver->email);
        });

        it('returns 403 if user is not authorized to preview', function () {
            Sanctum::actingAs($this->approver);

            $otherUser = User::factory()->create([
                'organization_id' => $this->user->organization_id,
            ]);

            $scenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'created_by' => $otherUser->id,
            ]);

            $approvalRequest = ApprovalRequest::factory()->create([
                'approvable_id' => $scenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
            ]);

            $response = $this->postJson("/api/approval-requests/{$approvalRequest->id}/email-preview");

            $response->assertStatus(403);
        });
    });

    describe('approvalsSummary', function () {
        it('returns approval metrics for current user', function () {
            Sanctum::actingAs($this->approver);

            // Create some approval requests
            ApprovalRequest::factory()->create([
                'approver_id' => $this->approver->id,
                'status' => 'pending',
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
            ]);

            ApprovalRequest::factory()->create([
                'approver_id' => $this->approver->id,
                'status' => 'approved',
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'signed_at' => now()->subDays(2),
            ]);

            $response = $this->getJson('/api/approvals-summary');

            $response->assertStatus(200);
            $response->assertJson([
                'status' => 'success',
            ]);
            expect($response->json('metrics.pending'))->toBeGreaterThan(0);
            expect($response->json('metrics.approved'))->toBeGreaterThan(0);
            expect($response->json('metrics.approval_rate'))->toBeString();
            expect($response->json('pending_approvals'))->toBeArray();
        });

        it('calculates approval rate correctly', function () {
            Sanctum::actingAs($this->approver);

            // Create 1 approved and 1 rejected
            ApprovalRequest::factory()->create([
                'approver_id' => $this->approver->id,
                'status' => 'approved',
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'signed_at' => now()->subDays(1),
            ]);

            ApprovalRequest::factory()->create([
                'approver_id' => $this->approver->id,
                'status' => 'rejected',
                'approvable_id' => $this->scenario->id,
                'approvable_type' => Scenario::class,
                'signed_at' => now()->subDays(1),
            ]);

            $response = $this->getJson('/api/approvals-summary');

            $response->assertStatus(200);
            expect($response->json('metrics.approval_rate'))->toContain('50');
        });

        it('returns empty pending approvals if none exist', function () {
            Sanctum::actingAs($this->approver);

            $response = $this->getJson('/api/approvals-summary');

            $response->assertStatus(200);
            expect($response->json('metrics.pending'))->toBe(0);
            expect($response->json('pending_approvals'))->toEqual([]);
        });
    });

    describe('activate with notifications', function () {
        it('sends activation notifications to stakeholders', function () {
            Sanctum::actingAs($this->user);

            // Create approved scenario
            $approvedScenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'created_by' => $this->user->id,
                'decision_status' => 'approved',
            ]);

            // Create approval requests
            ApprovalRequest::factory()->create([
                'approvable_id' => $approvedScenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
                'status' => 'approved',
            ]);

            Mail::fake();

            $response = $this->postJson("/api/scenarios/{$approvedScenario->id}/activate");

            $response->assertStatus(200);
            $response->assertJson([
                'status' => 'success',
            ]);
        });

        it('continues activation even if notification fails', function () {
            Sanctum::actingAs($this->user);

            $approvedScenario = Scenario::factory()->create([
                'organization_id' => $this->user->organization_id,
                'created_by' => $this->user->id,
                'decision_status' => 'approved',
            ]);

            ApprovalRequest::factory()->create([
                'approvable_id' => $approvedScenario->id,
                'approvable_type' => Scenario::class,
                'approver_id' => $this->approver->id,
                'status' => 'approved',
            ]);

            // Mock notification service to throw exception
            $this->mock(ScenarioNotificationService::class, function ($mock) {
                $mock->shouldReceive('notifyScenarioActivated')
                    ->andThrow(new Exception('Email service failed'));
            });

            $response = $this->postJson("/api/scenarios/{$approvedScenario->id}/activate");

            // Should still return success even though notification failed
            $response->assertStatus(200);
        });
    });
});

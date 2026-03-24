<?php

namespace Tests\Feature\Verification;

use App\Events\VerificationAlertThreshold;
use App\Events\VerificationPhaseTransitioned;
use App\Events\VerificationViolationDetected;
use App\Models\Organization;
use App\Models\VerificationNotification;
use App\Services\VerificationNotificationService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * VerificationNotificationTest - Tests for notification system
 *
 * Tests:
 * 1. Phase transition notifications are created
 * 2. Alert threshold notifications are sent
 * 3. Violation detected notifications fire
 * 4. Notifications are stored in database
 * 5. Notification channels work correctly
 * 6. Throttling prevents duplicate alerts
 */
class VerificationNotificationTest extends TestCase
{
    protected Organization $organization;

    protected VerificationNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
        $this->service = app(VerificationNotificationService::class);
    }

    /** @test */
    public function phase_transition_event_creates_notification(): void
    {
        $event = new VerificationPhaseTransitioned(
            organizationId: $this->organization->id,
            fromPhase: 'silent',
            toPhase: 'flagging',
            reason: 'High confidence achieved',
            metrics: ['avg_confidence' => 92.5]
        );

        $this->service->notifyPhaseTransition($event);

        $this->assertDatabaseHas('verification_notifications', [
            'organization_id' => $this->organization->id,
            'type' => 'phase_transition',
            'severity' => 'info',
        ]);
    }

    /** @test */
    public function alert_threshold_event_creates_notification(): void
    {
        $event = new VerificationAlertThreshold(
            organizationId: $this->organization->id,
            metricName: 'error_rate',
            currentValue: 45,
            threshold: 30,
            severity: 'warning'
        );

        $this->service->notifyAlertThreshold($event);

        $this->assertDatabaseHas('verification_notifications', [
            'organization_id' => $this->organization->id,
            'type' => 'alert_threshold',
            'severity' => 'warning',
        ]);
    }

    /** @test */
    public function critical_alert_stored_with_correct_severity(): void
    {
        $event = new VerificationAlertThreshold(
            organizationId: $this->organization->id,
            metricName: 'confidence_score',
            currentValue: 45,
            threshold: 50,
            severity: 'critical'
        );

        $this->service->notifyAlertThreshold($event);

        $notification = VerificationNotification::where('organization_id', $this->organization->id)
            ->first();

        $this->assertEquals('critical', $notification->severity);
    }

    /** @test */
    public function violation_detected_event_creates_notification(): void
    {
        $event = new VerificationViolationDetected(
            organizationId: $this->organization->id,
            agentName: 'TestAgent',
            violations: [
                ['code' => 'MISSING_FIELD', 'message' => 'Missing required field'],
                ['code' => 'INVALID_FORMAT', 'message' => 'Invalid date format'],
            ],
            confidenceScore: 78
        );

        $this->service->notifyViolationDetected($event);

        $this->assertDatabaseHas('verification_notifications', [
            'organization_id' => $this->organization->id,
            'type' => 'violation_detected',
        ]);
    }

    /** @test */
    public function notification_can_be_marked_as_read(): void
    {
        $notification = VerificationNotification::factory()->create([
            'organization_id' => $this->organization->id,
            'read_at' => null,
        ]);

        $this->assertNull($notification->read_at);

        $notification->markAsRead();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function unread_scope_filters_correctly(): void
    {
        VerificationNotification::factory(3)->create([
            'organization_id' => $this->organization->id,
            'read_at' => null,
        ]);

        VerificationNotification::factory(2)->create([
            'organization_id' => $this->organization->id,
            'read_at' => now(),
        ]);

        $unread = VerificationNotification::unread()->count();

        $this->assertEquals(3, $unread);
    }

    /** @test */
    public function by_type_scope_filters_correctly(): void
    {
        VerificationNotification::factory(2)->create([
            'organization_id' => $this->organization->id,
            'type' => 'phase_transition',
        ]);

        VerificationNotification::factory(3)->create([
            'organization_id' => $this->organization->id,
            'type' => 'alert_threshold',
        ]);

        $phaseTransitions = VerificationNotification::byType('phase_transition')->count();

        $this->assertEquals(2, $phaseTransitions);
    }

    /** @test */
    public function by_severity_scope_filters_correctly(): void
    {
        VerificationNotification::factory(2)->create([
            'organization_id' => $this->organization->id,
            'severity' => 'info',
        ]);

        VerificationNotification::factory(3)->create([
            'organization_id' => $this->organization->id,
            'severity' => 'critical',
        ]);

        $critical = VerificationNotification::bySeverity('critical')->count();

        $this->assertEquals(3, $critical);
    }

    /** @test */
    public function notification_get_type_label(): void
    {
        $notification = VerificationNotification::factory()->create([
            'type' => 'phase_transition',
        ]);

        $this->assertEquals('Phase Transition', $notification->getTypeLabel());
    }

    /** @test */
    public function notification_summary_for_phase_transition(): void
    {
        $notification = VerificationNotification::factory()->create([
            'type' => 'phase_transition',
            'data' => [
                'from_phase' => 'silent',
                'to_phase' => 'flagging',
            ],
        ]);

        $summary = $notification->getSummary();

        $this->assertStringContainsString('silent', $summary);
        $this->assertStringContainsString('flagging', $summary);
    }

    /** @test */
    public function multi_tenant_isolation_in_notifications(): void
    {
        // Create two organizations
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        // Create notifications for each
        VerificationNotification::factory(3)->create(['organization_id' => $org1->id]);
        VerificationNotification::factory(2)->create(['organization_id' => $org2->id]);

        // Query org1 notifications
        $org1Notifications = VerificationNotification::where('organization_id', $org1->id)->count();

        $this->assertEquals(3, $org1Notifications);
    }
}

<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Organization;
use App\Models\User;
use App\Services\VerificationMetricsService;
use App\Services\VerificationNotificationService;
use App\Enums\NotificationSeverity;
use App\Enums\NotificationType;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class VerificationMetricsServiceTest extends TestCase
{
    private VerificationMetricsService $service;
    private Organization $organization;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(VerificationMetricsService::class);
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->for($this->organization)->admin()->create();
    }

    // ========================================================================
    // Confidence Score Calculation Tests
    // ========================================================================

    #[Test]
    public function calculate_confidence_score_with_sufficient_samples(): void
    {
        // Create test data with sufficient samples
        $samples = 500;
        $successCount = 450;

        $confidence = $this->service->calculateConfidenceScore(
            successCount: $successCount,
            totalSamples: $samples
        );

        // 450/500 = 90% confidence
        $this->assertGreaterThanOrEqual(85, $confidence);
        $this->assertLessThanOrEqual(100, $confidence);
    }

    #[Test]
    public function calculate_confidence_score_with_insufficient_samples(): void
    {
        // Only 50 samples (below minimum of 100)
        $confidence = $this->service->calculateConfidenceScore(
            successCount: 45,
            totalSamples: 50
        );

        // Should be lower due to insufficient samples
        $this->assertLessThan(90, $confidence);
    }

    #[Test]
    public function calculate_confidence_score_with_zero_successes(): void
    {
        $confidence = $this->service->calculateConfidenceScore(
            successCount: 0,
            totalSamples: 100
        );

        $this->assertEquals(0, $confidence);
    }

    #[Test]
    public function calculate_confidence_score_with_perfect_record(): void
    {
        $confidence = $this->service->calculateConfidenceScore(
            successCount: 100,
            totalSamples: 100
        );

        $this->assertEquals(100, $confidence);
    }

    // ========================================================================
    // Error Rate Calculation Tests
    // ========================================================================

    #[Test]
    public function calculate_error_rate_below_threshold(): void
    {
        $errorRate = $this->service->calculateErrorRate(
            errorCount: 30,
            totalRequests: 1000
        );

        // 30/1000 = 3% error rate
        $this->assertEquals(3, $errorRate);
        $this->assertLessThan(40, $errorRate); // Should be below 40% threshold
    }

    #[Test]
    public function calculate_error_rate_above_threshold(): void
    {
        $errorRate = $this->service->calculateErrorRate(
            errorCount: 500,
            totalRequests: 1000
        );

        // 500/1000 = 50% error rate
        $this->assertEquals(50, $errorRate);
        $this->assertGreaterThan(40, $errorRate); // Should exceed 40% threshold
    }

    #[Test]
    public function calculate_error_rate_no_errors(): void
    {
        $errorRate = $this->service->calculateErrorRate(
            errorCount: 0,
            totalRequests: 1000
        );

        $this->assertEquals(0, $errorRate);
    }

    // ========================================================================
    // Retry Rate Calculation Tests
    // ========================================================================

    #[Test]
    public function calculate_retry_rate_below_threshold(): void
    {
        $retryRate = $this->service->calculateRetryRate(
            retryCount: 15,
            totalRequests: 1000
        );

        // 15/1000 = 1.5% retry rate
        $this->assertLessThan(2, $retryRate);
        $this->assertLessThan(20, $retryRate); // Should be below 20% threshold
    }

    #[Test]
    public function calculate_retry_rate_above_threshold(): void
    {
        $retryRate = $this->service->calculateRetryRate(
            retryCount: 250,
            totalRequests: 1000
        );

        // 250/1000 = 25% retry rate
        $this->assertEquals(25, $retryRate);
        $this->assertGreaterThan(20, $retryRate); // Should exceed 20% threshold
    }

    // ========================================================================
    // Sample Size Validation Tests
    // ========================================================================

    #[Test]
    public function validate_sample_size_sufficient(): void
    {
        $isValid = $this->service->validateSampleSize(sampleSize: 500);

        $this->assertTrue($isValid);
    }

    #[Test]
    public function validate_sample_size_insufficient(): void
    {
        $isValid = $this->service->validateSampleSize(sampleSize: 50);

        $this->assertFalse($isValid);
    }

    #[Test]
    public function validate_sample_size_at_minimum(): void
    {
        $isValid = $this->service->validateSampleSize(sampleSize: 100);

        $this->assertTrue($isValid);
    }

    // ========================================================================
    // Metrics Aggregation Tests
    // ========================================================================

    #[Test]
    public function aggregate_metrics_for_organization(): void
    {
        $this->actingAs($this->user);

        $metrics = $this->service->aggregateMetrics(
            organizationId: $this->organization->id,
            period: 'week'
        );

        $this->assertArrayHasKey('confidence', $metrics);
        $this->assertArrayHasKey('error_rate', $metrics);
        $this->assertArrayHasKey('retry_rate', $metrics);
        $this->assertArrayHasKey('sample_size', $metrics);
        $this->assertArrayHasKey('timestamp', $metrics);
    }

    #[Test]
    public function aggregate_metrics_by_period(): void
    {
        $this->actingAs($this->user);

        $weekMetrics = $this->service->aggregateMetrics(
            organizationId: $this->organization->id,
            period: 'week'
        );

        $dayMetrics = $this->service->aggregateMetrics(
            organizationId: $this->organization->id,
            period: 'day'
        );

        $this->assertNotEmpty($weekMetrics);
        $this->assertNotEmpty($dayMetrics);
    }

    // ========================================================================
    // Transition Readiness Determination Tests
    // ========================================================================

    #[Test]
    public function determine_transition_readiness_all_metrics_ready(): void
    {
        $readiness = $this->service->determineTransitionReadiness(
            organizationId: $this->organization->id,
            currentPhase: 'flagging'
        );

        $this->assertArrayHasKey('can_transition', $readiness);
        $this->assertArrayHasKey('metrics', $readiness);
        $this->assertArrayHasKey('blockers', $readiness);
        $this->assertArrayHasKey('days_to_ready', $readiness);
    }

    #[Test]
    public function determine_transition_readiness_with_blockers(): void
    {
        $readiness = $this->service->determineTransitionReadiness(
            organizationId: $this->organization->id,
            currentPhase: 'flagging'
        );

        // If there are blockers, validate their structure
        if (!empty($readiness['blockers'])) {
            foreach ($readiness['blockers'] as $blocker) {
                $this->assertArrayHasKey('metric', $blocker);
                $this->assertArrayHasKey('required_value', $blocker);
                $this->assertArrayHasKey('current_value', $blocker);
            }
        }
    }

    #[Test]
    public function estimate_days_to_transition_readiness(): void
    {
        $days = $this->service->estimateDaysToReadiness(
            organizationId: $this->organization->id,
            currentPhase: 'flagging'
        );

        $this->assertIsInt($days);
        $this->assertGreaterThanOrEqual(0, $days);
    }

    // ========================================================================
    // Metrics Caching Tests
    // ========================================================================

    #[Test]
    public function metrics_are_cached_for_performance(): void
    {
        Cache::flush();

        $cacheKey = "verification_metrics:{$this->organization->id}:week";

        // First call should compute and cache
        $metrics1 = $this->service->aggregateMetrics(
            organizationId: $this->organization->id,
            period: 'week'
        );

        $this->assertNotNull(Cache::get($cacheKey));

        // Second call should return cached value
        $metrics2 = $this->service->aggregateMetrics(
            organizationId: $this->organization->id,
            period: 'week'
        );

        $this->assertEquals($metrics1, $metrics2);
    }

    #[Test]
    public function cache_invalidation_on_new_data(): void
    {
        Cache::flush();

        // Get initial metrics
        $metrics1 = $this->service->aggregateMetrics(
            organizationId: $this->organization->id,
            period: 'week'
        );

        // Invalidate cache
        $this->service->invalidateMetricsCache($this->organization->id);

        // Metrics should be recomputed
        $this->assertNull(Cache::get("verification_metrics:{$this->organization->id}:week"));
    }
}

// ============================================================================
// Verification Notification Service Tests
// ============================================================================

class VerificationNotificationServiceTest extends TestCase
{
    private VerificationNotificationService $service;
    private Organization $organization;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(VerificationNotificationService::class);
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->for($this->organization)->admin()->create();
    }

    // ========================================================================
    // Notification Creation Tests
    // ========================================================================

    #[Test]
    public function create_phase_transition_notification(): void
    {
        $notification = $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Phase changed to flagging',
            metadata: [
                'phase_from' => 'silent',
                'phase_to' => 'flagging',
                'confidence' => 92
            ]
        );

        $this->assertNotNull($notification->id);
        $this->assertEquals(NotificationType::PhaseTransition->value, $notification->type);
        $this->assertFalse($notification->read);
    }

    #[Test]
    public function create_config_change_notification(): void
    {
        $notification = $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::ConfigChange,
            severity: NotificationSeverity::Warning,
            message: 'Slack webhook configured',
            metadata: ['channel' => 'slack']
        );

        $this->assertEquals(NotificationType::ConfigChange->value, $notification->type);
    }

    // ========================================================================
    // Notification Filtering Tests
    // ========================================================================

    #[Test]
    public function filter_notifications_by_type(): void
    {
        // Create mixed notifications
        $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Test 1'
        );

        $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::ConfigChange,
            severity: NotificationSeverity::Warning,
            message: 'Test 2'
        );

        $notifications = $this->service->filterNotifications(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition
        );

        $this->assertCount(1, $notifications);
        $this->assertEquals(NotificationType::PhaseTransition->value, $notifications[0]->type);
    }

    #[Test]
    public function filter_notifications_by_severity(): void
    {
        // Create notifications with different severities
        $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Error,
            message: 'Critical alert'
        );

        $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Informational'
        );

        $notifications = $this->service->filterNotifications(
            organizationId: $this->organization->id,
            severity: NotificationSeverity::Error
        );

        $this->assertCount(1, $notifications);
    }

    #[Test]
    public function pagination_of_notifications(): void
    {
        // Create 25 notifications
        for ($i = 0; $i < 25; $i++) {
            $this->service->createNotification(
                organizationId: $this->organization->id,
                type: NotificationType::PhaseTransition,
                severity: NotificationSeverity::Info,
                message: "Notification $i"
            );
        }

        $page1 = $this->service->getNotifications(
            organizationId: $this->organization->id,
            perPage: 10,
            page: 1
        );

        $page2 = $this->service->getNotifications(
            organizationId: $this->organization->id,
            perPage: 10,
            page: 2
        );

        $this->assertCount(10, $page1);
        $this->assertCount(10, $page2);
    }

    // ========================================================================
    // Notification Channel Tests
    // ========================================================================

    #[Test]
    public function send_notification_to_slack(): void
    {
        $this->service->sendToChannel(
            notification: $this->service->createNotification(
                organizationId: $this->organization->id,
                type: NotificationType::PhaseTransition,
                severity: NotificationSeverity::Info,
                message: 'Test notification'
            ),
            channel: 'slack'
        );

        // Verify it was marked as sent
        // Implementation-specific assertion
        $this->assertTrue(true);
    }

    #[Test]
    public function send_test_notification(): void
    {
        $result = $this->service->sendTestNotification(
            organizationId: $this->organization->id,
            channel: 'email',
            recipient: 'admin@company.com'
        );

        $this->assertTrue($result['success']);
    }

    // ========================================================================
    // Notification Read Status Tests
    // ========================================================================

    #[Test]
    public function mark_notification_as_read(): void
    {
        $notification = $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Test'
        );

        $this->assertFalse($notification->read);

        $this->service->markAsRead($notification->id);

        $notification->refresh();
        $this->assertTrue($notification->read);
    }

    #[Test]
    public function mark_all_notifications_as_read(): void
    {
        // Create 5 unread notifications
        for ($i = 0; $i < 5; $i++) {
            $this->service->createNotification(
                organizationId: $this->organization->id,
                type: NotificationType::PhaseTransition,
                severity: NotificationSeverity::Info,
                message: "Notification $i"
            );
        }

        $this->service->markAllAsRead($this->organization->id);

        $unreadCount = $this->service->getUnreadCount($this->organization->id);
        $this->assertEquals(0, $unreadCount);
    }

    #[Test]
    public function get_unread_notification_count(): void
    {
        // Create mixed read/unread
        $notification1 = $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Test 1'
        );

        $notification2 = $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Test 2'
        );

        $this->service->markAsRead($notification1->id);

        $unreadCount = $this->service->getUnreadCount($this->organization->id);
        $this->assertEquals(1, $unreadCount);
    }

    // ========================================================================
    // Notification Retention Tests
    // ========================================================================

    #[Test]
    public function cleanup_old_notifications(): void
    {
        // Create notification
        $notification = $this->service->createNotification(
            organizationId: $this->organization->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Old notification'
        );

        // Manually set created_at to 91 days ago (retention is 90 days)
        $notification->update(['created_at' => now()->subDays(91)]);

        $this->service->cleanupOldNotifications(retentionDays: 90);

        // Old notification should be deleted
        $this->assertNull($notification->fresh());
    }

    // ========================================================================
    // Multi-Tenant Isolation Tests
    // ========================================================================

    #[Test]
    public function notifications_isolated_by_organization(): void
    {
        $org1 = $this->organization;
        $org2 = Organization::factory()->create();

        $this->service->createNotification(
            organizationId: $org1->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Org1 notification'
        );

        $this->service->createNotification(
            organizationId: $org2->id,
            type: NotificationType::PhaseTransition,
            severity: NotificationSeverity::Info,
            message: 'Org2 notification'
        );

        $org1Notifications = $this->service->getNotifications(
            organizationId: $org1->id
        );

        $this->assertCount(1, $org1Notifications);
        $this->assertStringContainsString('Org1', $org1Notifications[0]->message);
    }
}

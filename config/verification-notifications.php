<?php

/**
 * Configuration for Verification Notification System
 *
 * Defines notification channels, alert types, and thresholds
 */

return [
    // Enable/disable notifications globally
    'enabled' => true,

    // Default channels for notifications
    'channels' => [
        'slack' => [
            'enabled' => (bool) env('VERIFICATION_SLACK_WEBHOOK'),
            'webhook_url' => env('VERIFICATION_SLACK_WEBHOOK'),
        ],
        'email' => [
            'enabled' => true,
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@stratos.local'),
            'template' => 'verification.notification',
        ],
        'database' => [
            'enabled' => true,
            'retention_days' => 90,  // Auto-delete after 90 days
        ],
        'log' => [
            'enabled' => true,
            'channel' => 'verification',
        ],
    ],

    // Alert threshold definitions
    'alerts' => [
        'error_rate' => [
            'enabled' => true,
            'warning_threshold' => 25,   // % over which warning is triggered
            'critical_threshold' => 50,  // % over which critical alert is triggered
        ],
        'confidence_score' => [
            'enabled' => true,
            'warning_threshold' => 70,
            'critical_threshold' => 50,
        ],
        'retry_rate' => [
            'enabled' => true,
            'warning_threshold' => 30,
            'critical_threshold' => 60,
        ],
    ],

    // Throttling configuration
    'throttle' => [
        'enabled' => true,
        'duration_minutes' => 5,  // Prevent duplicate alerts within N minutes
    ],

    // Notification recipients (can be overridden per organization)
    'recipients' => [
        'admins' => true,  // Send to organization admins
        'custom_emails' => env('VERIFICATION_ALERT_EMAILS', ''),  // CSV list
    ],
];

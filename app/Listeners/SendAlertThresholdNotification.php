<?php

namespace App\Listeners;

use App\Events\VerificationAlertThreshold;
use App\Services\VerificationNotificationService;

/**
 * SendAlertThresholdNotification - Listener for alert threshold events
 */
class SendAlertThresholdNotification
{
    public function __construct(
        protected VerificationNotificationService $notificationService,
    ) {}

    public function handle(VerificationAlertThreshold $event): void
    {
        $this->notificationService->notifyAlertThreshold($event);
    }
}

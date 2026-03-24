<?php

namespace App\Listeners;

use App\Events\VerificationViolationDetected;
use App\Services\VerificationNotificationService;

/**
 * SendViolationDetectedNotification - Listener for violation detected events
 */
class SendViolationDetectedNotification
{
    public function __construct(
        protected VerificationNotificationService $notificationService,
    ) {}

    public function handle(VerificationViolationDetected $event): void
    {
        $this->notificationService->notifyViolationDetected($event);
    }
}

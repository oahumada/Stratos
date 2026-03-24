<?php

namespace App\Listeners;

use App\Events\VerificationPhaseTransitioned;
use App\Services\VerificationNotificationService;

/**
 * SendPhaseTransitionNotification - Listener for phase transition events
 */
class SendPhaseTransitionNotification
{
    public function __construct(
        protected VerificationNotificationService $notificationService,
    ) {}

    public function handle(VerificationPhaseTransitioned $event): void
    {
        $this->notificationService->notifyPhaseTransition($event);
    }
}

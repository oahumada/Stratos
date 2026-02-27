<?php

namespace App\Services\Assessment;

use App\Models\AssessmentCycle;
use App\Models\People;
use Illuminate\Support\Facades\Log;

class AssessmentCycleNotificationService
{
    /**
     * Notify an evaluator about a pending assessment request.
     */
    public function notifyEvaluator(People $evaluator, People $subject, AssessmentCycle $cycle): void
    {
        $notifications = $cycle->notifications;

        Log::info("Notifying Evaluator: {$evaluator->full_name} to evaluate {$subject->full_name} for cycle: {$cycle->name}");

        if ($notifications['email'] ?? false) {
            $this->sendEmail($evaluator, $subject, $cycle);
        }

        if ($notifications['slack'] ?? false) {
            $this->sendSlack($evaluator, $subject, $cycle);
        }
        
        if ($notifications['whatsapp'] ?? false) {
            $this->sendWhatsApp($evaluator, $subject, $cycle);
        }
    }

    protected function sendEmail(People $evaluator, People $subject, AssessmentCycle $cycle): void
    {
        // Placeholder for real mailer logic
        Log::info("Email sent to {$evaluator->email}");
    }

    protected function sendSlack(People $evaluator, People $subject, AssessmentCycle $cycle): void
    {
        // Placeholder for Slack API
        Log::info("Slack notification sent to evaluation channel for {$evaluator->full_name}");
    }

    protected function sendWhatsApp(People $evaluator, People $subject, AssessmentCycle $cycle): void
    {
        // Placeholder for WhatsApp API/Twilio
        Log::info("WhatsApp notification sent to {$evaluator->full_name}");
    }
}

<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use Illuminate\Support\Facades\Log;

class SendCertificateNotification
{
    /**
     * Handle the event.
     */
    public function handle(CertificateIssued $event): void
    {
        $cert = $event->certificate;
        $person = $cert->person;

        $email = $person->email ?? null;
        Log::info('SendCertificateNotification: notifying recipient', ['certificate_id' => $cert->id, 'email' => $email]);
    }
}

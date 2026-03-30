<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use Illuminate\Support\Facades\Log;

class AuditCertificateIssued
{
    /**
     * Handle the event.
     */
    public function handle(CertificateIssued $event): void
    {
        $cert = $event->certificate;
        Log::info('AuditCertificateIssued: recording audit for certificate', ['certificate_id' => $cert->id, 'person_id' => $cert->person_id]);
    }
}

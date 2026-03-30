<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use Illuminate\Support\Facades\Log;

class SyncCertificateToExternal
{
    /**
     * Handle the event.
     */
    public function handle(CertificateIssued $event): void
    {
        $cert = $event->certificate;
        // Placeholder: enqueue job to sync certificate to external provider(s)
        Log::info('SyncCertificateToExternal: queued sync for certificate', ['certificate_id' => $cert->id]);
    }
}

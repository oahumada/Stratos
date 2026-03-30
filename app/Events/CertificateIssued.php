<?php

namespace App\Events;

use App\Models\LmsCertificate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class CertificateIssued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LmsCertificate $certificate;

    /**
     * Create a new event instance.
     */
    public function __construct(LmsCertificate $certificate)
    {
        $this->certificate = $certificate;
    }
}

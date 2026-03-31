<?php

namespace App\Events;

use App\Models\LmsCertificate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CertificateRevoked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LmsCertificate $certificate;

    public ?string $reason;

    public function __construct(LmsCertificate $certificate, ?string $reason = null)
    {
        $this->certificate = $certificate;
        $this->reason = $reason;
    }
}

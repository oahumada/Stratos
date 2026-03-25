<?php

namespace App\Traits;

use App\Services\Security\StratosSignatureService;
use Illuminate\Support\Facades\Log;

/**
 * HasDigitalSeal
 * Trait to enable cryptographic sealing of models.
 */
trait HasDigitalSeal
{
    /**
     * Apply the digital signature to the model.
     */
    public function seal(): void
    {
        $signer = app(StratosSignatureService::class);

        $this->digital_signature = $signer->sign($this);
        $this->signed_at = now();
        $this->signature_version = 'v1.0';

        $this->save();

        Log::info('Model ['.get_class($this)." ID: {$this->id}] sealed with Stratos Sentinel Signature.");
    }

    /**
     * Check if the seal is valid.
     */
    public function isVerified(): bool
    {
        $signer = app(StratosSignatureService::class);

        return $signer->verify($this);
    }
}

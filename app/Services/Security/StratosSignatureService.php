<?php

namespace App\Services\Security;

use Illuminate\Database\Eloquent\Model;

/**
 * StratosSignatureService
 * Provides a cryptographic seal of authenticity for AI-generated talent engineering artifacts.
 * Uses HMAC-SHA256 (App-Key bound) or RSA (Asymmetric) to ensure data hasn't been tampered with.
 */
class StratosSignatureService
{
    /**
     * Generate a digital seal for the model.
     */
    public function sign(Model $model): string
    {
        $content = $this->extractSignableContent($model);

        // We use the application key as the secret for the HMAC seal.
        // In a production environment with high-security requirements,
        // this would use an asymmetric private key (RSA/EdDSA).
        return hash_hmac('sha256', $content, config('app.key'));
    }

    /**
     * Verify if the model's seal matches its current content.
     */
    public function verify(Model $model): bool
    {
        if (empty($model->digital_signature)) {
            return false;
        }

        $content = $this->extractSignableContent($model);
        $expected = hash_hmac('sha256', $content, config('app.key'));

        return hash_equals($expected, (string) $model->digital_signature);
    }

    /**
     * Extract only relevant business logic fields for signing.
     * Metadata, IDs relative to DB (auto-increments), and signatures themselves are excluded
     * to ensure the footprint is based only on the "Intellectual Asset".
     */
    protected function extractSignableContent(Model $model): string
    {
        // We take the attributes but remove "environmental" or "non-business" fields.
        $attributes = $model->getAttributes();

        $excluded = [
            'id',
            'digital_signature',
            'signed_at',
            'signature_version',
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        $payload = array_diff_key($attributes, array_flip($excluded));

        // Stable sorting is critical: the same data must always produce the same hash.
        ksort($payload);

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

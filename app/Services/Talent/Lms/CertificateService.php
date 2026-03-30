<?php

namespace App\Services\Talent\Lms;

use App\Models\LmsCertificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Issue a certificate record, store PDF (stub) and return the certificate model.
     */
    public function issue(array $data): LmsCertificate
    {
        $certificateNumber = $data['certificate_number'] ?? strtoupper(Str::random(10));

        $cert = LmsCertificate::create([
            'organization_id' => $data['organization_id'],
            'person_id' => $data['person_id'] ?? null,
            'lms_enrollment_id' => $data['lms_enrollment_id'] ?? null,
            'certificate_number' => $certificateNumber,
            'issued_at' => $data['issued_at'] ?? now(),
            'expires_at' => $data['expires_at'] ?? null,
            'meta' => $data['meta'] ?? null,
        ]);

        // Generate PDF placeholder and compute hash (simple stub)
        $pdfContent = $this->generatePdfHtml($cert);
        $path = 'certificates/'.$cert->id.'-'.$certificateNumber.'.pdf';
        Storage::disk(config('filesystems.default'))->put($path, $pdfContent);

        $hash = hash('sha256', $pdfContent);
        $cert->certificate_url = Storage::url($path);
        $cert->certificate_hash = $hash;
        $cert->save();

        return $cert;
    }

    /**
     * Revoke a certificate (mark revoked and emit event in future).
     */
    public function revoke(LmsCertificate $certificate, string $reason = null): LmsCertificate
    {
        $certificate->is_revoked = true;
        $meta = $certificate->meta ?? [];
        $meta['revoked_reason'] = $reason;
        $meta['revoked_at'] = now()->toDateTimeString();
        $certificate->meta = $meta;
        $certificate->save();

        return $certificate;
    }

    /**
     * Minimal PDF HTML generator (stub). Replace with proper templating/mPDF.
     */
    protected function generatePdfHtml(LmsCertificate $cert): string
    {
        $person = $cert->person ? $cert->person->full_name ?? 'Titular' : 'Titular';
        $issued = $cert->issued_at?->toDateString() ?? now()->toDateString();

        return "<html><body><h1>Certificado</h1><p>Emitido a: {$person}</p><p>Numero: {$cert->certificate_number}</p><p>Fecha: {$issued}</p></body></html>";
    }

    /**
     * Verify certificate hash matches stored artifact.
     */
    public function verify(LmsCertificate $cert): bool
    {
        if (empty($cert->certificate_url)) {
            return false;
        }

        try {
            $path = parse_url($cert->certificate_url, PHP_URL_PATH);
            // Normalize path: Storage::url may prepend '/storage', remove leading slash or '/storage' prefix
            if (strpos($path, '/storage/') === 0) {
                $path = substr($path, strlen('/storage/'));
            } elseif (strpos($path, '/') === 0) {
                $path = ltrim($path, '/');
            }

            $content = Storage::disk(config('filesystems.default'))->get($path);
            $hash = hash('sha256', $content);

            return hash_equals($hash, $cert->certificate_hash ?? '');
        } catch (\Throwable $e) {
            return false;
        }
    }
}

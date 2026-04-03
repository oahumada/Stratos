<?php

namespace App\Services\Talent\Lms;

use App\Events\CertificateRevoked;
use App\Models\LmsCertificate;
use App\Models\TalentPass;
use App\Models\TalentPassCredential;
use App\Notifications\CertificateIssuedNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    protected LmsAnalyticsService $lmsAnalyticsService;

    public function __construct(?LmsAnalyticsService $lmsAnalyticsService = null)
    {
        $this->lmsAnalyticsService = $lmsAnalyticsService ?? app(LmsAnalyticsService::class);
    }

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
            'certificate_template_id' => $data['certificate_template_id'] ?? null,
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

        $this->syncToTalentPass($cert);
        $this->lmsAnalyticsService->trackCertificateIssued($cert);
        $this->sendCertificateIssuedSlackNotification($cert);

        // Dispatch domain event and notify recipient (user if linked, otherwise email)
        try {
            \App\Events\CertificateIssued::dispatch($cert);

            $recipientNotifiable = null;
            if ($cert->person && $cert->person->user) {
                $recipientNotifiable = $cert->person->user;
            }

            // Avoid sending notifications during unit tests to prevent DB/transaction side-effects
            if (! app()->runningUnitTests()) {
                if ($recipientNotifiable) {
                    Notification::send($recipientNotifiable, new CertificateIssuedNotification($cert));
                } elseif ($cert->person && $cert->person->email) {
                    Notification::route('mail', $cert->person->email)->notify(new CertificateIssuedNotification($cert));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Certificate issued notification failed', ['error' => $e->getMessage(), 'certificate_id' => $cert->id]);
        }

        return $cert;
    }

    protected function sendCertificateIssuedSlackNotification(LmsCertificate $certificate): void
    {
        $webhookUrl = config('services.lms.slack_webhook_url');

        if (empty($webhookUrl)) {
            return;
        }

        $courseTitle = $certificate->enrollment?->course?->title ?? 'Curso';
        $personName = $certificate->person?->full_name ?? 'Participante';
        $number = $certificate->certificate_number;

        try {
            Http::timeout(5)->post($webhookUrl, [
                'text' => ":mortar_board: *Certificado emitido* — {$personName} completó *{$courseTitle}* (N° {$number})",
            ]);
        } catch (\Throwable $e) {
            Log::warning('LMS certificate Slack webhook failed', [
                'error' => $e->getMessage(),
                'certificate_id' => $certificate->id,
            ]);
        }
    }

    /**
     * Revoke a certificate and dispatch revocation event.
     */
    public function revoke(LmsCertificate $certificate, ?string $reason = null): LmsCertificate
    {
        $certificate->is_revoked = true;
        $meta = $certificate->meta ?? [];
        $meta['revoked_reason'] = $reason;
        $meta['revoked_at'] = now()->toDateTimeString();
        $certificate->meta = $meta;
        $certificate->save();

        CertificateRevoked::dispatch($certificate, $reason);

        return $certificate;
    }

    /**
     * Minimal PDF HTML generator (stub). Replace with proper templating/mPDF.
     */
    protected function generatePdfHtml(LmsCertificate $cert): string
    {
        $person = $cert->person ? $cert->person->full_name ?? 'Titular' : 'Titular';
        $issued = $cert->issued_at?->toDateString() ?? now()->toDateString();

        if ($cert->template?->html_template) {
            return strtr($cert->template->html_template, [
                '{{recipient_name}}' => $person,
                '{{certificate_number}}' => (string) $cert->certificate_number,
                '{{issued_date}}' => $issued,
            ]);
        }

        return "<html><body><h1>Certificado</h1><p>Emitido a: {$person}</p><p>Numero: {$cert->certificate_number}</p><p>Fecha: {$issued}</p></body></html>";
    }

    protected function syncToTalentPass(LmsCertificate $certificate): void
    {
        if (! $certificate->person_id || ! $certificate->organization_id) {
            return;
        }

        try {
            $talentPass = TalentPass::query()->firstOrCreate(
                [
                    'organization_id' => $certificate->organization_id,
                    'people_id' => $certificate->person_id,
                ],
                [
                    'ulid' => (string) Str::ulid(),
                    'title' => 'My CV',
                    'status' => 'draft',
                    'visibility' => 'private',
                ]
            );

            $credentialName = $certificate->enrollment?->course?->title ?? 'LMS Certificate';

            TalentPassCredential::query()->updateOrCreate(
                [
                    'talent_pass_id' => $talentPass->id,
                    'credential_id' => $certificate->certificate_number,
                ],
                [
                    'credential_name' => $credentialName,
                    'issuer' => 'Stratos LMS',
                    'issued_date' => $certificate->issued_at?->toDateString() ?? now()->toDateString(),
                    'expiry_date' => $certificate->expires_at?->toDateString(),
                    'credential_url' => $certificate->certificate_url,
                ]
            );
        } catch (\Throwable $exception) {
            Log::warning('Unable to sync LMS certificate to TalentPass', [
                'certificate_id' => $certificate->id,
                'error' => $exception->getMessage(),
            ]);
        }
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

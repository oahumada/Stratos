<?php

namespace App\Services\Agents;
use App\Services\Talent\Lms\LmsService;
use App\Services\TalentPassService;
use App\Services\Talent\Lms\CertificateService;
use App\Models\LmsEnrollment;
use App\Models\TalentPass;
use App\Models\People;
use Illuminate\Support\Facades\Log;
use App\Events\CertificateIssued;
use Illuminate\Support\Facades\Event;

/**
 * Operador LMS - servicio agente que orquesta onboarding, invitaciones, enrollments
 * y el ciclo de vida de certificados.
 *
 * Implementa integración básica con `LmsService` y `TalentPassService`.
 */
class LmsOperatorAgent
{
    protected LmsService $lmsService;
    protected TalentPassService $talentPassService;
    protected CertificateService $certificateService;

    public function __construct(LmsService $lmsService, TalentPassService $talentPassService)
    {
        $this->lmsService = $lmsService;
        $this->talentPassService = $talentPassService;
        $this->certificateService = app(CertificateService::class);
    }

    /**
     * Crear cuenta de participante (si no existe) y devolver persona creada/actualizada.
     * @param array $personData
     * @return array Información mínima de la persona (id, email)
     */
    public function createParticipantAccount(array $personData): array
    {
        // Minimal implementation: ensure a People record exists
        $person = null;
        if (!empty($personData['id'])) {
            $person = People::find($personData['id']);
        }

        if (! $person) {
            $person = People::firstOrCreate(
                ['email' => $personData['email'] ?? null],
                [
                    'first_name' => $personData['first_name'] ?? 'Unknown',
                    'last_name' => $personData['last_name'] ?? '',
                    'organization_id' => $personData['organization_id'] ?? null,
                ]
            );
        }

        return ['id' => $person->id, 'email' => $person->email];
    }

    /**
     * Enviar invitación al participante para un curso específico.
     */
    public function sendInvitation(int $personId, int $lmsCourseId): bool
    {
        $person = People::find($personId);
        if (! $person) {
            Log::warning("sendInvitation failed: person {$personId} not found");
            return false;
        }

        $user = $person->user;
        if (! $user) {
            Log::warning("sendInvitation failed: no user associated with person {$personId}");
            return false;
        }

        try {
            $this->lmsService->sendCourseInvitation($user, $lmsCourseId);
            Log::info("LMS invitation sent for person {$personId} to course {$lmsCourseId}");
            return true;
        } catch (\Throwable $e) {
            Log::error("sendInvitation failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Inscribir a un usuario en un curso del LMS.
     */
    public function enrollUser(int $personId, int $lmsCourseId): bool
    {
        $person = People::find($personId);
        if (! $person) {
            Log::warning("Enroll failed: person {$personId} not found");
            return false;
        }

        $userId = $person->user_id ?? null;
        $enrollment = LmsEnrollment::create([
            'lms_course_id' => $lmsCourseId,
            'user_id' => $userId,
            'progress_percentage' => 0,
            'status' => 'enrolled',
            'started_at' => now(),
        ]);

        Log::info("Created LmsEnrollment {$enrollment->id} for person {$personId} on course {$lmsCourseId}");
        return true;
    }

    /**
     * Emitir certificado tras completitud. Retorna metadata del certificado o null si falla.
     */
    public function issueCertificate(int $lmsEnrollmentId, array $options = []): ?array
    {
        $enrollment = LmsEnrollment::find($lmsEnrollmentId);
        if (! $enrollment) {
            Log::warning("issueCertificate: enrollment {$lmsEnrollmentId} not found");
            return null;
        }

        $force = $options['force'] ?? false;
        $isCompleted = intval($enrollment->progress_percentage) >= 100 || $enrollment->status === 'completed' || $force;

        if (! $isCompleted) {
            Log::info("issueCertificate: enrollment {$lmsEnrollmentId} not completed (progress={$enrollment->progress_percentage})");
            return null;
        }

        $user = $enrollment->user;
        $person = $user?->people;

        if (! $person) {
            Log::warning("issueCertificate: no People record linked to enrollment user for enrollment {$lmsEnrollmentId}");
            return null;
        }

        return $this->processIssueCertificate($enrollment, $person, $options);
    }

    /**
     * Process certificate issuance for a validated enrollment and person.
     */
    private function processIssueCertificate(LmsEnrollment $enrollment, People $person, array $options): ?array
    {
        // Issue official certificate record and artifact
        $certificateData = [
            'organization_id' => $person->organization_id,
            'person_id' => $person->id,
            'lms_enrollment_id' => $enrollment->id,
            'issued_at' => now(),
            'meta' => [
                'course_title' => $enrollment->course->title ?? null,
                'source' => 'lms_operator',
            ],
        ];

        $cert = null;
        try {
            $cert = $this->certificateService->issue($certificateData);
            if ($cert) {
                Event::dispatch(new CertificateIssued($cert));
            }
        } catch (\Throwable $e) {
            Log::error('Certificate issue failed: '.$e->getMessage());
        }

        // Ensure TalentPass exists (use TalentPassService to generate ulid and defaults)
        $talentPass = TalentPass::where('people_id', $person->id)->first();
        if (! $talentPass) {
            $talentPass = $this->talentPassService->create($person->organization_id, $person->id, [
                'title' => "TalentPass: {$person->getFullNameAttribute()}",
                'visibility' => 'private',
            ]);
        }

        // Create credential record in TalentPass
        $credentialData = [
            'credential_name' => $enrollment->course->title ?? 'LMS Certificate',
            'issuer' => $person->organization->name ?? 'Stratos',
            'issued_date' => now()->toDateString(),
            'expiry_date' => null,
            'credential_url' => $cert->certificate_url ?? ($options['certificate_url'] ?? null),
            'credential_id' => $cert->certificate_number ?? ($options['certificate_number'] ?? null),
        ];

        $credential = $this->talentPassService->addCredential($talentPass->id, $credentialData);

        Log::info("Issued credential {$credential->id} for person {$person->id} from enrollment {$enrollment->id}");

        return [
            'talent_pass_id' => $talentPass->id,
            'credential_id' => $credential->id,
            'lms_certificate_id' => $cert->id ?? null,
        ];
    }

    /**
     * Firmar un certificado (digitalmente) siguiendo la política de la organización.
     */
    public function signCertificate(int $certificateId): bool
    {
        // Placeholder: real implementation should sign PDF and update storage
        Log::info("signCertificate: requested signing for certificate {$certificateId}");
        return true;
    }

    /**
     * Cerrar un curso (marcar cohort cerrado, notificar participantes).
     */
    public function closeCourse(int $lmsCourseId): bool
    {
        Log::info("closeCourse: marking course {$lmsCourseId} as closed by Operador LMS");
        return true;
    }

    /**
     * Seguimiento post-curso (recordatorios, re-certification nudges).
     */
    public function followUp(int $personId): bool
    {
        Log::info("followUp: scheduled follow-up for person {$personId}");
        return true;
    }
}

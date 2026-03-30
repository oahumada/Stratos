# 📋 Análisis: Integración LMS ↔ Talent Pass ↔ Certificados (2026-03-29)

**Status:** 🔍 INVESTIGACIÓN  
**Owner:** Backend + Frontend  
**Impact:** 🟡 MEDIA (Enhancement existing flow)  
**Complexity:** 🟡 MEDIA (3-4 day implementation)

---

## 1️⃣ Estado Actual del Código

### 1.1 Modelos Existentes

#### LMS Ecosystem (4 modelos)

```
LmsCourse (título, descripción, thumbnail, category, level, duration_minutes, xp_points, cost_per_seat)
├── LmsModule (order)
│   └── LmsLesson (content)
└── LmsEnrollment (user_id, progress_percentage, status, started_at, completed_at)
```

**Gaps:**

- ❌ No hay tabla `lms_certificates` para emitir certificados de finalización
- ❌ No hay modelo para tracking de certificado (emisión, expiración, revocación)
- ❌ No hay vinculación directa a TalentPass

#### Talent Pass Ecosystem (4 modelos)

```
TalentPass (person_id, title, summary, status, visibility)
├── TalentPassSkill (skill_name, proficiency_level, endorsed_count)
├── TalentPassCredential (credential_name, issuer, issued_date, expiry_date, credential_url, credential_id)
└── TalentPassExperience (company, role, start_date, end_date, description)
```

**Estado:**

- ✅ TalentPassCredential existe y es flexible
    public function issueCertificateOnCompletion(LmsEnrollment $enrollment): ?LmsCertificate {
- ❌ Pero NO hay automatización de auto-sincronización desde LMS

#### Certificate/Evidence Related (2 modelos)

```
Evidence (type: 'certification' | 'kpi' | 'artifact' | 'project', file_path, external_url, metadata)
  ↑ Linked to DevelopmentAction + Evaluation

VerifiableCredential (type, issuer_did, cryptographic_signature, issued_at, expires_at)
  ↑ Future blockchain integration (currently not used)
```

#### DevelopmentAction Bridge

```
DevelopmentAction
├── lms_course_id (FK)
├── lms_enrollment_id (string)
├── lms_provider ('moodle', 'coursera', 'mock', 'internal')
├── completed_at (timestamp)
└── evidences → Evidence[]
```

**State:** Parcial - vincula a LMS pero certificados no se sincronizan automáticamente

---

## 2️⃣ Flujo Actual de Certificación

### 2.1 Hoy (Broken/Manual)

```
1. User completa curso LMS (external provider: Moodle, Coursera, etc.)
   ↓ LmsService.syncProgress() marks DevelopmentAction as 'completed'
   ↓ Awards XP gamification points (✅ existing)
   ↓
2. Coordinador descarga certificado manually en Moodle/Coursera
   ↓
3. Sube a Evidence como file_path (manual upload)
   ↓
4. Manager valida Evidence (validated_at, validated_by)
   ↓
5. ??? Certificate nunca se replica a TalentPass
   ↓
6. Persona debe anualmente actualizar su CV (TalentPass.credentials)
   ↓ Mucho work manual, no es automático
```

**Problems:**

- 🔴 No hay auto-issuance de certificados desde LMS completion
- 🔴 No hay auto-sync a TalentPass.credentials
- 🔴 No hay way de trackear "qué certificados emitimos"
- 🔴 No hay expiry management

---

## 3️⃣ Arquitectura Propuesta: "Smart Certificate Bridge"

### 3.1 Decisiones de Diseño

#### Option A: Todo en TalentPassCredential (⭐ RECOMENDADO)

```
TalentPassCredential NUEVA COLUMNA:
  source_type = 'lms_completion' | 'self_provided' | 'external_credential' | 'verifiable_credential'
    public function syncToTalentPass(LmsCertificate $cert, array $recipients = []): bool {
  source_evidence_id = FK: evidences (null)
  recipient_person_id = FK: people (denormalized para query speed)
  digital_signature = signed hash del certificado
  template_id = FK: certificate_templates (qué template se usó)
```

**Ventajas:**

- ✅ Single source of truth
- ✅ Centraliza todo en TalentPass
- ✅ Compatible con public CV sharing
- ✅ Fácil de auditar

**Desventajas:**

- Requiere migración de existing Evidence.type = 'certification'

---

### 3.2 Nuevas Tablas de Soporte

#### Table 1: CertificateTemplate (Customization per Organization)

```sql
CREATE TABLE certificate_templates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,

    -- Template HTML
    html_content LONGTEXT,  -- Blade template with {{placeholders}}
    public function generateCertificatePdf(LmsCertificate $cert): string {
    -- Design
    logo_url VARCHAR(500),
    background_url VARCHAR(500),
    text_color VARCHAR(7) DEFAULT '#000000',
    accent_color VARCHAR(7) DEFAULT '#0066cc',
    font_family VARCHAR(100) DEFAULT 'Arial, sans-serif',

    -- Placeholders available
    placeholders JSON,  -- ["{{person_name}}", "{{course_title}}", "{{completion_date}}", "{{score}}", "{{cert_number}}", "{{org_name}}"]

    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,  -- One default per organization

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_org_active(organization_id, is_active)
);
```

#### Table 2: SkillRecertificationPolicy (Expiry per Skill)

```sql
CREATE TABLE skill_recertification_policies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED,           -- NULL = global policy
    skill_id BIGINT UNSIGNED NOT NULL,         -- FK: skills table

    recertification_required BOOLEAN DEFAULT FALSE,  -- Does this skill need renewal?
    valid_for_months INT NULL,                 -- Months until expiry (NULL = never expires)

    -- Policy
    renewal_notice_days INT DEFAULT 30,        -- Notify X days before expiry
    renewal_course_id BIGINT UNSIGNED NULL,    -- FK: lms_courses (which course to retake)

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    UNIQUE KEY unique_skill_org(skill_id, organization_id),
    INDEX idx_org_skill(organization_id, skill_id)
);
```

#### Table 3: LmsCertificate (Main Certificate Record)

```sql
CREATE TABLE lms_certificates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    lms_course_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    lms_enrollment_id VARCHAR(255),    -- External enrollment ID

    -- Issuance
    status ENUM('pending', 'issued', 'revoked', 'expired') DEFAULT 'pending',
    issued_at TIMESTAMP NULL,
    issued_by BIGINT UNSIGNED NULL,    -- FK: users (admin/system)
    completion_date TIMESTAMP NULL,    -- When student completed

    -- Certificate Details
    certificate_number VARCHAR(255) UNIQUE,  -- CERT-ORG-2026-00123
    certificate_url VARCHAR(500),      -- S3 PDF link
    certificate_hash VARCHAR(255),     -- SHA256 for integrity

    -- Template used
    certificate_template_id BIGINT UNSIGNED NULL,  -- FK: certificate_templates

    -- Expiry (calculated from skill's recertification_policy)
    expires_at TIMESTAMP NULL,         -- NULL = never expires
    is_renewable BOOLEAN DEFAULT TRUE,
    recertification_skill_id BIGINT UNSIGNED NULL,  -- FK: skills (which skill this cert validates)

    -- Metadata
    skills_validated JSON NULL,        -- [{skill_id, proficiency_level}, ...]
    passing_score FLOAT,
    completion_percentage INT,
    metadata JSON NULL,                -- {notification_recipients: ['participant', 'manager'], revoked_reason: null, ...}

    -- Sync tracking
    auto_synced_to_talent_pass_at TIMESTAMP NULL,
    talent_pass_credential_id BIGINT UNSIGNED NULL,  -- FK (denormalized)

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (organization_id) REFERENCES organizations(id),
    FOREIGN KEY (lms_course_id) REFERENCES lms_courses(id) ON DELETE RESTRICT,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) SET NULL,
    FOREIGN KEY (certificate_template_id) REFERENCES certificate_templates(id) SET NULL,
    FOREIGN KEY (recertification_skill_id) REFERENCES skills(id) SET NULL,
    FOREIGN KEY (talent_pass_credential_id) REFERENCES talent_pass_credentials(id) SET NULL,

    UNIQUE KEY unique_enrollment_cert(lms_enrollment_id, lms_course_id),
    INDEX idx_person_org(person_id, organization_id),
    INDEX idx_status(status, expires_at),
    INDEX idx_issued_at(issued_at DESC),
    INDEX idx_skill_expiry(recertification_skill_id, expires_at)
);
```

**Modelo:**

```php
class LmsCertificate extends Model {
    protected $fillable = [
        'organization_id', 'lms_course_id', 'person_id', 'lms_enrollment_id',
        'status', 'issued_at', 'issued_by', 'completion_date',
        'certificate_number', 'certificate_url', 'certificate_hash',
        'certificate_template_id', 'expires_at', 'is_renewable',
        'recertification_skill_id', 'skills_validated', 'passing_score',
        'completion_percentage', 'metadata',
        'auto_synced_to_talent_pass_at', 'talent_pass_credential_id'
    ];

    protected $casts = [
        'issued_at' => 'datetime', 'completion_date' => 'datetime',
        'expires_at' => 'datetime', 'auto_synced_to_talent_pass_at' => 'datetime',
        'skills_validated' => 'array', 'passing_score' => 'float',
        'is_renewable' => 'boolean', 'metadata' => 'array',
    ];

    public function course(): BelongsTo { return $this->belongsTo(LmsCourse::class); }
    public function person(): BelongsTo { return $this->belongsTo(People::class); }
    public function issuer(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }
    public function template(): BelongsTo {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }
    public function recertificationSkill(): BelongsTo {
        return $this->belongsTo(Skill::class, 'recertification_skill_id');
    }
    public function talentPassCredential(): BelongsTo {
        return $this->belongsTo(TalentPassCredential::class);
    }

    public function scopeValid($q) {
        return $q->where('status', 'issued')->where(function($q2) {
            $q2->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
    public function isExpired(): bool { return $this->expires_at && now()->isAfter($this->expires_at); }
    public function isPending(): bool { return $this->status === 'pending'; }
}

class CertificateTemplate extends Model {
    protected $fillable = [
        'organization_id', 'name', 'description', 'html_content',
        'logo_url', 'background_url', 'text_color', 'accent_color',
        'font_family', 'placeholders', 'is_active', 'is_default'
    ];
    protected $casts = [
        'placeholders' => 'array', 'is_active' => 'boolean', 'is_default' => 'boolean'
    ];
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function certificates(): HasMany { return $this->hasMany(LmsCertificate::class); }
}

class SkillRecertificationPolicy extends Model {
    protected $fillable = [
        'organization_id', 'skill_id', 'recertification_required',
        'valid_for_months', 'renewal_notice_days', 'renewal_course_id'
    ];
    protected $casts = ['recertification_required' => 'boolean'];
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function skill(): BelongsTo { return $this->belongsTo(Skill::class); }
    public function renewalCourse(): BelongsTo {
        return $this->belongsTo(LmsCourse::class, 'renewal_course_id');
    }
    public function isExpired(LmsCertificate $cert): bool {
        if ($this->valid_for_months === null) return false;
        return $cert->issued_at->addMonths($this->valid_for_months)->isPast();
    }
}
```

---

## 4️⃣ Servicios Necesarios

### 4.1 CertificateService (750 LOC)

```php
class CertificateService {

    /**
     * Generar certificado cuando LMS enrollment se completa
     * TRIGGER: LmsService.syncProgress() → completion detected
     */
    public function issueCertificateOnCompletion(DevelopmentAction $action): bool {
        // 1. Check si ya existe certificado
        $existing = LmsCertificate::where('lms_enrollment_id', $action->lms_enrollment_id)
            ->where('lms_course_id', $action->lms_course_id)
            ->first();

        if ($existing && $existing->status === 'issued') {
            return false; // Evitar duplicados
        }

        // 2. Recuperar datos completitud del LMS
        $completionData = $this->fetchCompletionDataFromProvider(
            $action->lms_provider,
            $action->lms_enrollment_id,
            $action->lms_course_id
        );

        if (!$completionData['is_completed']) {
            return false;
        }

        // 3. Generar número único de certificado
        $certNumber = $this->generateCertificateNumber($action);

        // 4. Crear certificado
        $certificate = LmsCertificate::create([
            'organization_id' => $action->person->organization_id ?? Auth::user()->organization_id,
            'lms_course_id' => $action->lms_course_id,
            'person_id' => $action->person_id,
            'lms_enrollment_id' => $action->lms_enrollment_id,
            'status' => 'pending',
            'completion_date' => $completionData['completion_date'],
            'certificate_number' => $certNumber,
            'passing_score' => $completionData['score'] ?? null,
            'completion_percentage' => $completionData['progress'] ?? 100,
        ]);

        // 5. Auto-sincronizar a TalentPass
        $this->syncToTalentPass($certificate);

        // 6. Generar PDF si es internal provider
        if ($action->lms_provider === 'internal') {
            $pdfUrl = $this->generatePdf($certificate);
            $certificate->update(['certificate_url' => $pdfUrl]);
        }

        // 7. Crear Evidence record (para audit trail)
        Evidence::create([
            'development_action_id' => $action->id,
            'type' => 'certification',
            'title' => "LMS Certificate: {$certificate->course->title}",
            'description' => "Auto-issued certificate from LMS completion",
            'metadata' => [
                'certificate_id' => $certificate->id,
                'certificate_number' => $certNumber,
                'completion_percentage' => $certificate->completion_percentage,
            ]
        ]);

        event(new CertificateIssued($certificate));

        return true;
    }

    /**
     * Auto-sincronizar certificado de LMS a TalentPass.credentials
     */
    public function syncToTalentPass(LmsCertificate $certificate): TalentPassCredential {
        // 1. Recuperar o crear TalentPass para la persona
        $talentPass = TalentPass::firstOrCreate(
            ['people_id' => $certificate->person_id],
            ['organization_id' => $certificate->organization_id, 'status' => 'draft']
        );

        // 2. Crear TalentPassCredential
        $credential = TalentPassCredential::create([
            'talent_pass_id' => $talentPass->id,
            'credential_name' => $certificate->course->title,
            'issuer' => $certificate->organization->name ?? 'Stratos',
            'issued_date' => $certificate->completion_date,
            'expiry_date' => $certificate->expires_at,
            'credential_url' => $certificate->certificate_url,
            'credential_id' => $certificate->certificate_number,
            'is_featured' => false,
        ]);

        // 3. Actualizar referencia en LmsCertificate
        $certificate->update([
            'talent_pass_credential_id' => $credential->id,
            'status' => 'issued',
            'issued_at' => now(),
            'auto_synced_to_talent_pass_at' => now(),
        ]);

        return $credential;
    }

    /**
     * Generar PDF del certificado (si es internal provider)
     */
    public function generatePdf(LmsCertificate $certificate): string {
        $course = $certificate->course;
        $person = $certificate->person;
        $org = $certificate->organization;

        // Template signature (HTML)
        $html = view('certificates.template', [
            'certificate_number' => $certificate->certificate_number,
            'person_name' => $person->first_name . ' ' . $person->last_name,
            'course_title' => $course->title,
            'completion_date' => $certificate->completion_date->format('d/m/Y'),
            'organization' => $org->name,
            'passing_score' => $certificate->passing_score,
            'completion_percentage' => $certificate->completion_percentage,
            'skills_validated' => $certificate->skills_validated ?? [],
        ])->render();

        // Convert HTML to PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOption('dpi', 300)
            ->setOption('enable-local-file-access', true);

        // Store in S3/storage
        $filename = "certificates/{$org->id}/{$certificate->id}_{$certificate->certificate_number}.pdf";
        \Storage::disk('s3')->put($filename, $pdf->output());

        return \Storage::disk('s3')->url($filename);
    }

    /**
     * Revoke certificate
     */
    public function revokeCertificate(LmsCertificate $certificate, string $reason): bool {
        $certificate->update([
            'status' => 'revoked',
            'metadata->revoked_reason' => $reason,
            'metadata->revoked_at' => now(),
        ]);

        // Cascade revoke en TalentPass
        if ($credential = $certificate->talentPassCredential) {
            $credential->delete(); // Soft delete debería prevenir display
        }

        event(new CertificateRevoked($certificate));
        return true;
    }

    // Helpers (100+ LOC)
    private function generateCertificateNumber(DevelopmentAction $action): string { ... }
    private function fetchCompletionDataFromProvider(string $provider, string $enrollmentId, int $courseId): array { ... }
    private function validateCertificateIntegrity(LmsCertificate $cert): bool { ... }
}
```

---

### 4.2 Integración en LmsService (Enhancement existente)

```php
// EN: app/Services/Talent/Lms/LmsService.php

public function syncProgress(DevelopmentAction $action): bool {
    if (!$action->lms_enrollment_id) {
        return false;
    }

    $provider = $this->getProvider($action->lms_provider ?? 'mock');

    try {
        $isCompleted = $provider->isCompleted($action->lms_enrollment_id);

        if ($isCompleted && $action->status !== 'completed') {
            $action->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // 🏅 GAMIFICATION: Award XP
            $course = \App\Models\LmsCourse::find($action->lms_course_id);
            if ($course) {
                $xp = $course->xp_points > 0 ? $course->xp_points : 50;
                $gamification = \App\Models\UserGamification::firstOrCreate(
                    ['user_id' => $action->person_id],
                    ['total_xp' => 0, 'level' => 1, 'current_points' => 0]
                );
                $gamification->addExperience($xp);
            }

            // 🎓 NEW: ISSUE CERTIFICATE
            app(CertificateService::class)->issueCertificateOnCompletion($action);

            return true;
        }
    } catch (\Exception $e) {
        Log::error('Error syncing LMS progress: ' . $e->getMessage());
    }

    return false;
}
```

---

## 5️⃣ API Endpoints (Backend)

### 5.1 New Endpoints

```
GET /api/certificates
  Query params: completed_only=true, expired_only=false
  Returns: paginated list of issued certificates

GET /api/certificates/{id}
  Returns: full certificate with TalentPass credential link

POST /api/certificates/{id}/revoke
  Body: { reason: "Fraud detected" }

POST /api/certificates/sync-batch
  Cron: Sincroniza todos los pending certificates pendientes

GET /api/talent-pass/{id}/certificates
  Returns: all certificates linked to a TalentPass profile
```

---

## 6️⃣ Frontend Changes (Smart Certificate UI)

### 6.1 AssessmentResults Component (A2.3, Track A)

**NUEVA SECCIÓN:** "Certificate" tabs

```vue
<template>
    <div v-if="assessment.completed && certificate" class="certificate-section">
        <!-- Certificate Badge -->
        <div class="cert-badge">
            <img :src="certificate.course.badge_url" alt="Certificate" />
            <p>{{ certificate.certificate_number }}</p>
        </div>

        <!-- Certificate Details -->
        <div class="cert-details">
            <p>
                <strong>Issued:</strong> {{ formatDate(certificate.issued_at) }}
            </p>
            <p v-if="certificate.expires_at">
                <strong>Expires:</strong>
                {{ formatDate(certificate.expires_at) }}
            </p>
            <p>
                <strong>Status:</strong>
                <span :class="`badge-${certificate.status}`">{{
                    certificate.status
                }}</span>
            </p>
        </div>

        <!-- Certificate Actions -->
        <div class="cert-actions">
            <a
                :href="certificate.certificate_url"
                target="_blank"
                class="btn"
                download
            >
                📥 Download PDF
            </a>
            <button @click="shareToTalentPass" class="btn btn-primary">
                📋 Add to Talent Pass
            </button>
            <button @click="copyLink" class="btn">🔗 Copy Link</button>
        </div>

        <!-- Certificate Status in TalentPass -->
        <div
            v-if="certificate.talent_pass_credential_id"
            class="talent-pass-link"
        >
            ✅ Added to your Talent Pass
            <router-link :to="`/talent-pass/${talentPassId}`"
                >View in CV</router-link
            >
        </div>
    </div>
</template>
```

### 6.2 LearningProgress Component (A3.1, Track A)

**NUEVA SECCIÓN:** "Certificate History" tab

```
┌─ Learning Progress
│  ├─ Overview
│  ├─ Completed Courses
│  ├─ Certificate History ⭐ NEW
│  │  ├─ Active Certificates (3)
│  │  ├─ Expired Certificates (1)
│  │  ├─ Revoked Certificates (0)
│  │  └─ Export All as PDF
│  └─ Skills Validation
```

### 6.3 TalentPass Editor Component (Enhancement)

**NUEVA SECCIÓN:** "Certificates" tab management

```
┌─ TalentPass Editor
│  ├─ Overview
│  ├─ Skills
│  ├─ Certificates ⭐ ENHANCED
│  │  ├─ Manual Certificates (existing)
│  │  ├─ LMS Certificates (auto-synced) 🔗
│  │  │  ├─ Filter: All, Active, Auto-synced
│  │  │  └─ Actions: Remove from CV, Download
│  │  └─ Add Certificate (manual upload)
│  └─ Experiences
```

---

## 7️⃣ Migration Path (V2.0 Sprint 1 + 2)

### Sprint 1 (Apr 1-21)

- **B5 (Testing):** Add `lms_certificates` table migration
- **Track A (A2.3):** AssessmentResults shows certificate badge (if issued)
- **Track B (B2):** CertificateService live

### Sprint 2 (Apr 22 - May 5)

- **Frontend:** LearningProgress certificate history
- **Frontend:** TalentPass integration UI
- **Backend:** Batch sync cron job for pending certificates

---

## 8️⃣ Current Code Gaps vs Proposed

| Layer                | Current                             | Proposed                                      | Gap                      |
| -------------------- | ----------------------------------- | --------------------------------------------- | ------------------------ |
| **DB**               | No `lms_certificates` table         | ✅ LmsCertificate model + migration           | 🔴 Need to add           |
| **Service Logic**    | LmsService only syncs progress + XP | ✅ CertificateService handles issuance + sync | 🔴 Needs implementation  |
| **LMS→TalentPass**   | ❌ Manual sync \| No automation     | ✅ Auto-sync on completion                    | 🔴 Zero integration      |
| **Certificate Data** | Evidence type='certification' only  | ✅ LmsCertificate + TalentPassCredential      | 🔴 Denormalized tracking |
| **PDF Generation**   | ❌ Not implemented                  | ✅ Blade template + DomPDF                    | 🔴 Needs implementation  |
| **Frontend Display** | ❌ Nothing shown                    | ✅ Certificate badge in results + history     | 🔴 UI needed             |
| **TalentPass Link**  | ❌ Manual upload only               | ✅ Auto-added to credentials                  | 🔴 Auto-sync missing     |

---

## 9️⃣ Recommended Implementation Plan

### Phase 1: Backend Foundation (3 days)

1. Create `lms_certificates` migration + LmsCertificate model
2. Implement CertificateService (issuance + sync + PDF)
3. Integrate into LmsService.syncProgress()
4. 20+ unit tests

### Phase 2: Frontend Display (2 days)

1. Update AssessmentResults.vue to show certificate badge
2. Add certificate section to LearningProgress.vue
3. Create TalentPass certificates management UI

### Phase 3: Automation + Polish (2 days)

1. Batch sync cron job for pending certificates
2. Certificate revocation workflow
3. Email notifications on issuance
4. Performance optimization (caching)

**⏳ Total: 1 week (May 5-12)**

---

## 🔟 Decisiones de Producto (FINALIZADO 2026-03-29)

✅ **DECISION 1: Certificate Templates — Per-Organization Customization**
- **User Input:** "Sí, cada organización con su template"
- **Storage:** New `certificate_templates` table with org_id FK
- **Fields:** name, html_content, logo_url, background_url, text_color, accent_color, font_family
- **Placeholders:** {{person_name}}, {{course_title}}, {{completion_date}}, {{score}}, {{org_name}}, {{cert_number}}
- **Relationship:** CertificateTemplate model & lms_certificates.certificate_template_id FK
- **Default:** Each org has default template; admins can create variants per course type
- **Status:** ✅ LOCKED - Schema & model definitions complete

✅ **DECISION 2: Expiry Policy — Skill-Based Recertification**
- **User Input:** "Expiran de acuerdo a las necesidades de recertificación que se deben establecer por cada competencia/skill"
- **Storage:** New `skill_recertification_policies` table with skill_id FK
- **Fields:** recertification_required (bool), valid_for_months (int/null), renewal_notice_days, renewal_course_id
- **Logic:** Each skill has separate expiry window (NOT per-course, NOT per-organization global)
- **Example:** PHP: 24 months, Scrum Master: 36 months, Management Skills: NULL (never expires)
- **Calculation:** LmsCertificate.expires_at = issued_at + valid_for_months (from skill policy)
- **Recertification:** renewal_course_id points to optional course for skill update
- **Relationship:** SkillRecertificationPolicy model & lms_certificates.recertification_skill_id FK
- **Status:** ✅ LOCKED - Schema & model definitions complete

✅ **DECISION 3: Auto-Feature in TalentPass — User Choice ONLY**
- **User Input:** "No lo entiendo" → Clarified: NO auto-featured
- **Behavior:** Certificate issued → stored private in TalentPass → User manually chooses visibility
- **Implementation:** lms_certificates.auto_synced_to_talent_pass_at = NOW (auto-sync), but is_featured = false
- **User Flow:** "Add to Talent Pass" button in certificate view → User selects visibility → Refresh TalentPass
- **Frontend:** Certificate badge shows "Not featured" status; separate "Feature" button
- **Why:** Some certs internal/sensitive, some public; user controls what's visible
- **Status:** ✅ LOCKED - TalentPass integration flow reflects user-driven visibility control

✅ **DECISION 4: Email Notifications — Configurable Recipients, TalentPass Channel Now**
- **User Input:** "Dar opción de email al participante, jefatura o encargado de RRHH, por ahora via talent pass"
- **Recipients:** Array of 'participant' | 'manager' | 'rrhh' (or combinations)
- **Storage:** metadata['notification_recipients'] = ['participant', 'manager'] (example)
- **Alert Channel NOW:** TalentPass notifications (primary in Apr-May 2026)
- **Email Channel:** FUTURE (Sprint 2+), but framework ready in metadata
- **Admin UI:** Organization settings → Certificate defaults → Default recipients checkbox
- **Trigger:** On certificate issuance, send notifications via configured channel
- **Status:** ✅ LOCKED - Metadata supports both channels; TalentPass only for now

⏸️ **DECISION 5: Share Mechanism — TalentPass Only (For Now)**
- **Clarification:** Public shareable links deferred
- **Current:** Only via TalentPass public profile (when featured)
- **Framework:** Future: certificate-specific QR code, direct link share
- **Implementation:** TalentPass URL is primary share mechanism in Apr-May 2026
- **Status:** ⏸️ DEFERRED - Design for v2.1 (May 2026+)

⏸️ **DECISION 6: Revocation Audit — Basic Tracking Now**
- **Basic (Sprint 1):** metadata['revoked_reason'], metadata['revoked_at'], revoked_by
- **Future:** Full audit trail with revision history, who approved, timeline
- **Implementation:** CertificateService.revokeCertificate() stores basic metadata
- **Status:** ⏸️ DEFERRED - Full audit trail in v2.1

⏸️ **DECISION 7: Digital Signature — Hash Only (For Now)**
- **Basic (Sprint 1):** SHA256 hash stored in certificate_hash for PDF integrity
- **Future:** Organizational certificate signing, key management
- **Implementation:** CertificateService.generateCertificatePdf() computes & stores hash
- **Use Case:** Simple verification that PDF hasn't been tampered
- **Status:** ⏸️ DEFERRED - Org digital signing in v2.1

⏸️ **DECISION 8: Gamification Badges — Separate System**
- **Clarification:** Certificates ≠ Badges (different artifact types)
- **Current:** XP points awarded on LMS completion (✅ DONE)
- **Future:** Separate badge/achievement system for organizational milestones
- **No Change:** Certificate flow doesn't influence gamification
- **Status:** ✅ NO ACTION - XP already awarded, badges separate initiative

---

**Status:** ✅ **PRODUCT DECISIONS FINALIZED** (Mar 29, 2026)
- ✅ Decisions 1-4: LOCKED ← Ready for immediate implementation
- ⏸️ Decisions 5-8: DEFERRED ← Documented for v2.1 planning
- **Next:** Implement Certificate Infrastructure in Sprint 1 (Apr 1-21)

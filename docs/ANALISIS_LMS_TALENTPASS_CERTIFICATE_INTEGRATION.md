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
- ✅ Puede acomodar certificados de LMS
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
  source_lms_course_id = FK: lms_courses (null si no es LMS)
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

### 3.2 Nueva Tabla: LmsCertificate (Tracking + Fulfillment)

```sql
CREATE TABLE lms_certificates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL, -- FK
    lms_course_id BIGINT UNSIGNED NOT NULL, -- FK
    person_id BIGINT UNSIGNED NOT NULL, -- FK
    lms_enrollment_id VARCHAR(255),           -- External enrollment ID
    
    -- Issuance
    status ENUM('pending', 'issued', 'revoked', 'expired') DEFAULT 'pending',
    issued_at TIMESTAMP NULL,
    issued_by BIGINT UNSIGNED NULL, -- FK: users (e.g., admin)
    completion_date TIMESTAMP NULL,  -- When student completed the course
    
    -- Certificate Details  
    certificate_number VARCHAR(255) UNIQUE, -- Unique cert ID (e.g., CERT-ORG-2026-00123)
    certificate_url VARCHAR(500),    -- Where to download/view
    certificate_hash VARCHAR(255),   -- SHA256 hash for integrity verification
    
    -- Expiry
    expires_at TIMESTAMP NULL,       -- If credential expires
    is_renewable BOOLEAN DEFAULT TRUE,
    
    -- Metadata
    skills_validated JSON NULL,      -- [{skill_id, proficiency_level}, ...]
    passing_score FLOAT,             -- What score they achieved
    completion_percentage INT,       -- 0-100
    
    -- Audit
    auto_synced_to_talent_pass_at TIMESTAMP NULL,
    talent_pass_credential_id BIGINT UNSIGNED NULL, -- FK (denormalized)
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id),
    FOREIGN KEY (lms_course_id) REFERENCES lms_courses(id) ON DELETE RESTRICT,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) SET NULL,
    FOREIGN KEY (talent_pass_credential_id) REFERENCES talent_pass_credentials(id) SET NULL,
    
    UNIQUE KEY unique_enrollment_cert(lms_enrollment_id, lms_course_id),
    INDEX idx_person_org(person_id, organization_id),
    INDEX idx_status(status, expires_at),
    INDEX idx_issued_at(issued_at DESC)
);
```

**Modelo:**
```php
class LmsCertificate extends Model {
    protected $fillable = [
        'organization_id', 'lms_course_id', 'person_id', 'lms_enrollment_id',
        'status', 'issued_at', 'issued_by', 'completion_date',
        'certificate_number', 'certificate_url', 'certificate_hash',
        'expires_at', 'is_renewable', 'skills_validated', 'passing_score',
        'completion_percentage', 'auto_synced_to_talent_pass_at', 'talent_pass_credential_id'
    ];
    
    protected $casts = [
        'issued_at' => 'datetime',
        'completion_date' => 'datetime',
        'expires_at' => 'datetime',
        'auto_synced_to_talent_pass_at' => 'datetime',
        'skills_validated' => 'array',
        'passing_score' => 'float',
        'is_renewable' => 'boolean',
    ];
    
    // Relationships
    public function course(): BelongsTo { return $this->belongsTo(LmsCourse::class); }
    public function person(): BelongsTo { return $this->belongsTo(People::class); }
    public function issuer(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }
    public function talentPassCredential(): BelongsTo { 
        return $this->belongsTo(TalentPassCredential::class); 
    }
    
    // Scopes & helpers
    public function scopeValid($q) { 
        return $q->where('status', 'issued')->where(function($q2) {
            $q2->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
    
    public function isExpired(): bool { 
        return $this->expires_at && now()->isAfter($this->expires_at);
    }
    
    public function isPending(): bool { 
        return $this->status === 'pending'; 
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
      <p><strong>Issued:</strong> {{ formatDate(certificate.issued_at) }}</p>
      <p v-if="certificate.expires_at">
        <strong>Expires:</strong> {{ formatDate(certificate.expires_at) }}
      </p>
      <p><strong>Status:</strong> <span :class="`badge-${certificate.status}`">{{ certificate.status }}</span></p>
    </div>

    <!-- Certificate Actions -->
    <div class="cert-actions">
      <a :href="certificate.certificate_url" target="_blank" class="btn" download>
        📥 Download PDF
      </a>
      <button @click="shareToTalentPass" class="btn btn-primary">
        📋 Add to Talent Pass
      </button>
      <button @click="copyLink" class="btn">
        🔗 Copy Link
      </button>
    </div>

    <!-- Certificate Status in TalentPass -->
    <div v-if="certificate.talent_pass_credential_id" class="talent-pass-link">
      ✅ Added to your Talent Pass
      <router-link :to="`/talent-pass/${talentPassId}`">View in CV</router-link>
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

| Layer | Current | Proposed | Gap|
|-------|---------|----------|-----|
| **DB** | No `lms_certificates` table | ✅ LmsCertificate model + migration | 🔴 Need to add |
| **Service Logic** | LmsService only syncs progress + XP | ✅ CertificateService handles issuance + sync | 🔴 Needs implementation |
| **LMS→TalentPass** | ❌ Manual sync \| No automation | ✅ Auto-sync on completion | 🔴 Zero integration |
| **Certificate Data** | Evidence type='certification' only | ✅ LmsCertificate + TalentPassCredential | 🔴 Denormalized tracking |
| **PDF Generation** | ❌ Not implemented | ✅ Blade template + DomPDF | 🔴 Needs implementation |
| **Frontend Display** | ❌ Nothing shown | ✅ Certificate badge in results + history | 🔴 UI needed |
| **TalentPass Link** | ❌ Manual upload only | ✅ Auto-added to credentials | 🔴 Auto-sync missing |

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

## 🔟 Questions for Product/Design

1. **Certificate Template:** Do we support custom templates per organization?
2. **Expiry Policy:** All certs expire after N years, or course-dependent?
3. **Auto-Feature:** Should certificates be auto-featured in TalentPass, or user choice?
4. **Email Notification:** Notify person when cert is issued?
5. **Share Mechanism:** Public shareable link per certificate, or only via TalentPass?
6. **Revocation Audit:** Store full audit trail of revocations?
7. **Digital Signature:** Sign PDFs with organization cert (expensive)?
8. **Badge System:** Do certificates earn badges/levels in gamification?

---

**Status:** ✅ Ready for kickoff once decisions on questions 1-8 finalized.

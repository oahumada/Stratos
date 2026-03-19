# Estrategia de Calidad y Cumplimiento (Compliance) - Stratos AI

Este documento define el marco de referencia para que Stratos opere bajo estándares internacionales de calidad, auditoría y reporte de capital humano, sirviendo como un activo estratégico de venta y gobernanza.

## 1. El Marco de "Trust & Governance"

Stratos se diferencia por ser un **Sistema de Confianza**. No solo gestiona datos, sino que garantiza su integridad mediante criptografía y procesos de negocio auditables.

### Estándares Centrales:

- **ISO 9001:2015**: Calidad en la gestión de procesos (Aprobación de Roles/Competencias).
- **ISO 30414:2018**: Reporte de Capital Humano (Métricas estandarizadas).
- **ISO/IEC 27001**: Seguridad de la Información.
- **GDPR / ISO 27701**: Privacidad y Protección de Datos Personales.

---

## 2. Estado Actual (Lo que ya tenemos)

| Estándar         | Funcionalidades Implementadas              | Propósito Técnico                                             |
| :--------------- | :----------------------------------------- | :------------------------------------------------------------ |
| **ISO 9001**     | `EventStore`, `RoleVersion`, `DigitalSeal` | Trazabilidad total de quién aprobó qué y cuándo.              |
| **Q&A Software** | `HasDigitalSeal`, HMAC-SHA256              | Garantía de que los datos no han sido alterados (Integridad). |
| **Auditoría**    | UI de Certificado de Autenticidad          | Verificación visual de cumplimiento para auditores externos.  |

---

## 3. Plan de Implementación (Phases)

### Fase 1: Consolidación de Gobernanza (Q1-2024) - [EN PROCESO]

_Foco: Trazabilidad y Evidencia de Auditoría._

- [x] Implementación de **Firmas Digitales** (Digital Seal).
- [x] Generación automática de **Certificados de Validez Técnica**.
- [x] Registro inmutable de eventos en el `EventStore` (Audit Trail).
- [x] **Dashboard de Auditoría:** Pantalla centralizada para listar todos los eventos de cumplimiento (`/quality/compliance-audit` + `/api/compliance/audit-events`).

### Fase 2: Reporte Estándar ISO 30414 (Q2-2024)

_Foco: Métricas de Valor de Capital Humano._

- [x] **Cálculo de Costo de Sustitución:** Basado en la arquitectura del Rol (skills requeridas) vía `GET /api/compliance/iso30414/summary` (`replacement_cost`).
- [x] **Métricas de Madurez de Talento:** Reporte automático de niveles de maestría agregados por departamento (`talent_maturity_by_department`).
- [x] **Reporte de Brecha de Capacidades:** Auditabilidad del desarrollo de competencias transversales (`transversal_capability_gaps`).

### Fase 3: Seguridad y Privacidad ISO 27001 / GDPR (Q3-2024)

_Foco: Resguardo de Información Sensible._

- [x] **Módulo de Consentimiento:** Registro en `EventStore` de la aceptación/revocación de términos de procesamiento de IA (`POST /api/compliance/consents/ai-processing`).
- [x] **Encriptación en Reposo:** Asegurar que las descripciones de roles y evaluaciones privadas estén cifradas en la base de datos.
    - Implementado en `Roles` (campos: `description`, `purpose`, `expected_results`).
    - Implementado en `LLMEvaluation` (campos: `input_content`, `output_content`, `context_content`).
    - Compatibilidad hacia atrás con datos legacy en plaintext (secuencia try/catch en mutators).
    - Tests: `ComplianceEncryptionAtRestTest.php` (4 casos: cifrado Roles/LLMEvaluation + legacy plaintext).
- [x] **Mecanismo de Purga de Datos (GDPR):** Protocolo de anonimización + soft delete bajo solicitud (`POST /api/compliance/gdpr/purge`, con `dry-run` y ejecución confirmada).

### Fase 3.1: Extensión de Cifrado a Datos Psicométricos y PX (NUEVO)

_Foco: Protección de Categorías Especiales de Datos (Art. 9 GDPR)._

- [x] **Cifrado de Perfiles Psicométricos:** Encriptación en reposo de campos sensibles en `PsychometricProfile`:
    - `rationale`: Análisis psicológico confidencial.
    - `evidence`: Evidencia basada en tests/entrevistas.
    - **Retrocompatibilidad:** Fallback automático para datos legacy en plaintext.
- [x] **Auditoría de Encuestas PX (Pulse Surveys):** Los campos JSON (`answers`, `ai_report`) se mantienen bajo `HasDigitalSeal` para integridad pero no se cifran directamente (optimización para BD JSON/JSONB).
- [x] **Cobertura de Pruebas:** `CompliancePXEncryptionPhase31Test.php` (2 casos: cifrado psicometría + legacy plaintext).
- **Estado:** Todos los tests verdes (16/16 compliance tests).

### Fase 4: Preparación para Certificación Externa (Q4-2024)

_Foco: Validación por Terceros._

- [x] **Exportación de Verifiable Credentials (VC/JSON-LD):** Exportación de credencial verificable de roles firmados vía `GET /api/compliance/credentials/roles/{roleId}`.
    - Formato JSON-LD con `@context`, `type`, `issuer`, `credentialSubject` y `proof` (`jws`).
    - `issuer DID` configurable por entorno (`COMPLIANCE_ISSUER_DID`) con fallback `did:web:{app-host}`.
    - Scope multi-tenant por `organization_id`.
- [x] **Verificación Criptográfica de VC:** Validación de credenciales emitidas vía `POST /api/compliance/credentials/roles/{roleId}/verify`.
    - Checks incluidos: `proof_matches_role_signature`, `issuer_matches_expected`, `credential_subject_role_matches`, `credential_subject_organization_matches`.
    - Detección de tampering en `proof.jws` (credencial inválida).
- [x] **Verificación Pública para Terceros (sin auth):** Endpoint `POST /api/compliance/public/credentials/verify` para auditores/validadores externos.
    - Verificación por payload completo de credencial (sin exponer data interna sensible).
    - Respuesta explícita `is_valid` + `role_exists` + checks.
- [x] **Publicación DID (did:web):** Documento público en `GET /.well-known/did.json`.
    - Declara `id` del issuer DID, `assertionMethod`, servicio de verificación pública y servicio de metadata del verificador.
- [x] **Metadata Pública del Verificador:** Endpoint `GET /api/compliance/public/verifier-metadata`.
    - Expone `version`, `policy_version`, `issuer_did`, algoritmo, checks soportados y enlace al DID document.
    - Permite discovery técnico para auditores externos y validadores automáticos.
- [x] **Internal Audit Wizard:** Reporte operativo de vigencia de firmas para roles críticos vía `GET /api/compliance/internal-audit-wizard`.
    - Clasificación por estado de firma: `current`, `expired`, `missing`.
    - Parámetro configurable: `signature_valid_days` (30-1095).
    - Resumen de cumplimiento y recomendaciones accionables.
- [x] **Cobertura de pruebas Fase 4:**
    - `CompliancePhase4Test.php` (autenticación, scope tenant, VC JSON-LD, verificación criptográfica y auditoría interna).
    - `CompliancePublicVerificationTest.php` (did:web público + verificación pública + detección tampering).

---

## 4. Argumentos de Venta (Sales Hooks)

1. **"Audit-Ready as a Service":** El cliente no tiene que preparar datos para la auditoría de ISO 9001; el sistema ya entrega los certificados firmados.
2. **"Transparencia Críptica":** Usamos la misma tecnología que los bancos (HMAC-SHA256) para asegurar que un arquitecto de talento no pueda ser suplantado.
3. **"Gobernanza IA sin Sesgos":** El sello digital certifica que un humano revisó y aprobó la sugerencia de la IA, eliminando la "Caja Negra" de los algoritmos ante reguladores.

---

> [!IMPORTANT]
> **Mantenimiento del Documento:** Este plan debe actualizarse trimestralmente para alinearse con nuevas versiones de las normas ISO o legislaciones locales de privacidad.

---

## 5. Guías Operativas para Ejecutar Auditorías

- **[GUIA_AUDITORIA_INTERNA_COMPLIANCE.md](GUIA_AUDITORIA_INTERNA_COMPLIANCE.md)** - Procedimiento expedito para planificar, ejecutar y cerrar auditorías internas.
- **[GUIA_AUDITORIA_EXTERNA_COMPLIANCE.md](GUIA_AUDITORIA_EXTERNA_COMPLIANCE.md)** - Procedimiento para preparar evidencia, conducir sesiones con terceros y demostrar verificabilidad externa.

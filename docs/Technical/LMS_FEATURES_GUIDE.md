# Guía de Features: LMS, Agente Operador y CMS (Stratos Learning)

Última actualización: 2026-03-29

Este documento consolida el diseño, las decisiones operativas y las referencias de implementación para el Hub de LMS de Stratos, el `Operador LMS` y el CMS de generación de contenidos por agente.

1) Resumen
- El Hub de LMS es agnóstico y soporta proveedores externos (Moodle, LinkedIn Learning, Udemy) y un LMS nativo de Stratos.
- Objetivos principales: automatizar inscripciones, seguimiento, emisión de certificados y acelerar la publicación de contenido pedagógico.

2) Operador LMS (LMS Admin Agent)
- Propósito: tareas operativas automatizadas (cuentas, invitaciones, enroll, emisión/firmado de certificados, cierre y seguimiento).
- Implementación actual: `app/Services/Agents/LmsOperatorAgent.php`.
- Eventos: `CertificateIssued`, `CertificateRevoked` (payload: `person_id`, `certificate_id`, `skills[]`, `issued_at`).
- Permisos: operaciones de certificación requieren `permission:lms.certify` o `role:admin`.

3) CMS y Content Agent
- Propósito: permitir generación automática de artículos para el LMS y su flujo de revisión/publicación.
- Temas preconfigurados: `vida sana`, `habilidades blandas`, `trabajo en equipo`, `resolución de conflictos`, `liderazgo`.

Scaffold implementado:
- Migration: `database/migrations/2026_03_29_000002_create_lms_articles_table.php`.
- Modelo: `app/Models/LmsArticle.php`.
- Servicio de generación: `app/Services/Content/ContentAgentService.php` (usa `AbacusClient`).
- Job: `app/Jobs/GenerateLmsArticle.php`.
- API: `POST /api/lms/cms/articles` -> `app/Http/Controllers/Api/Lms/CmsArticleController.php`.
- FormRequest: `app/Http/Requests/Api/Lms/StoreCmsArticleRequest.php`.
- Frontend: `resources/js/components/Landing/LmsCard.vue`, `resources/js/pages/Lms/Landing.vue`, `resources/js/components/Lms/LmsLandingContent.vue`.

Operativa y políticas:
- Default: `draft` / `pending_review` (revisión humana obligatoria).
- Opcional: `organization.settings.lms.auto_publish_content` para auto-publicación por organización.
- Permisos: `permission:lms.cms.manage` para encolar/generar artículos.

Programación y cadencia:
- Recomendado: job semanal que genere 1–2 artículos por tema; implementar `php artisan lms:generate-weekly-articles`.

4) Integración con TalentPass y Certificados
- Pendiente de completar: migración `lms_certificates`, modelo `LmsCertificate` y `CertificateService` (generación PDF, hash, firma, revocación).
- Flujo deseado: `LmsService::syncProgress()` detecta completion -> `LmsOperatorAgent::issueCertificate()` -> `CertificateService` genera y firma -> `TalentPassService->addCredential()` para agregar al pasaporte.

5) UX / Frontend
- Tarjeta de acceso rápido: `resources/js/components/Landing/LmsCard.vue` (integrada en hub de agentes).
- Landing: `resources/js/pages/Lms/Landing.vue` y `LmsLandingContent.vue` como placeholder para revisión/edición/publicación.

6) Buenas prácticas
- Mantener prompts controlados en `database/seeders/PromptInstructionsSeeder.php`.
- Evaluar outputs con `RAGASEvaluator` y persistir en `llm_evaluations`.
- Sanitizar HTML y auditar publicaciones.

7) Próximos pasos (prioridad)
1. Panel UI de revisión/edición y control de versiones (en `LMS Landing`).
2. Scheduler semanal y comando para encolar generación por organización.
3. Notificaciones a revisores y registro de aprobaciones.
4. Implementar `lms_certificates` y `CertificateService` + tests para flows críticos.

---

Si quieres, extraigo secciones para crear un playbook operativo (checklists para administración) y un documento de onboarding para revisores de contenido.

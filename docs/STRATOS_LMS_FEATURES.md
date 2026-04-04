# Stratos LMS — Features & Capacidades

> **Documento para presentaciones**  
> Versión: v0.12.x · Última actualización: 4 Abr 2026  
> Audiencia: Product, Ventas, Stakeholders, Nuevos integrantes

---

## Visión general

Stratos LMS es el módulo de aprendizaje integrado de la plataforma Stratos. A diferencia de un LMS standalone, opera como **capa de inteligencia de aprendizaje**: se conecta con proveedores externos (Cornerstone, SAP SuccessFactors, LinkedIn Learning) para sincronizar progreso, emitir certificados, detectar learners en riesgo y vincular el aprendizaje directamente con los planes de desarrollo de talento de cada persona.

---

## 1. 📚 Gestión de Cursos

| Feature                    | Descripción                                                                                     |
| -------------------------- | ----------------------------------------------------------------------------------------------- |
| Catálogo de cursos         | Cursos con módulos, lecciones y artículos CMS generados con IA                                  |
| Búsqueda                   | Búsqueda contra proveedor LMS externo en tiempo real                                            |
| Plantillas de certificado  | Selector de plantilla personalizable por curso u organización                                   |
| Ciclo de vida              | Apertura → Asignación → Ejecución → Cierre                                                      |
| Configuración de criterios | Política de emisión por curso: porcentaje de progreso, recursos completados, nota de evaluación |

---

## 2. 🎯 Inscripciones y Progreso

| Feature                    | Descripción                                                                                                     |
| -------------------------- | --------------------------------------------------------------------------------------------------------------- |
| Enrolamiento automático    | Al asignar una acción de desarrollo se crea el enrollment en el proveedor externo sin intervención manual       |
| Sincronización de progreso | Cron cada hora — sincroniza `progress_percentage`, `resources_completed`, `assessment_score` desde el proveedor |
| Monitoreo de lag           | Alerta automática si la sincronización acumula más de 90 minutos de retraso                                     |
| Historial de sync          | Registro trazable de cada batch de sincronización                                                               |

---

## 3. 🏅 Gamificación

| Feature            | Descripción                                                                                     |
| ------------------ | ----------------------------------------------------------------------------------------------- |
| Puntos XP          | Al completar un curso se otorgan puntos de experiencia (configurables por curso, default 50 XP) |
| Sistema de niveles | Cada usuario tiene `level`, `total_xp` y `current_points` que aumentan con el aprendizaje       |
| Leaderboard        | Ranking de usuarios por XP dentro de la organización                                            |
| Stats personales   | Dashboard de XP acumulado, nivel actual y posición en el ranking                                |

---

## 4. 🎓 Certificados

| Feature                    | Descripción                                                                                                    |
| -------------------------- | -------------------------------------------------------------------------------------------------------------- |
| Emisión automática         | Al detectar completion en la sincronización, el sistema emite el certificado si cumple la política configurada |
| Plantillas personalizables | Diseño de certificado por curso u organización (`LmsCertificateTemplate`)                                      |
| Descarga                   | El usuario puede descargar su certificado en PDF                                                               |
| Verificación pública       | URL de verificación sin autenticación — para validación por terceros (empleadores, etc.)                       |
| Revocación                 | Los administradores pueden revocar certificados emitidos                                                       |
| Evento de emisión          | Dispara notificaciones automáticas al momento de la emisión                                                    |

---

## 5. 📊 Analytics — 9 KPIs en tiempo real

### KPIs globales de la organización

| KPI                      | Descripción                      |
| ------------------------ | -------------------------------- |
| `total_enrollments`      | Total de inscripciones activas   |
| `completed_enrollments`  | Inscripciones completadas        |
| `completion_rate_pct`    | Tasa de completación (%)         |
| `total_certificates`     | Certificados emitidos            |
| `certification_rate_pct` | Tasa de certificación (%)        |
| `at_risk_enrollments`    | Inscripciones con bajo progreso  |
| `at_risk_rate_pct`       | Porcentaje de learners en riesgo |

### Breakdown por curso

- Tasa de completación y certificación individual por curso
- Comparativo entre cursos para identificar fricciones

### Learners en riesgo

- Lista de personas con progreso bajo umbral
- `resource_completion_pct` por learner para intervención temprana

---

## 6. 🔔 Notificaciones Multi-Canal

Al completar un curso, Stratos notifica automáticamente por:

| Canal         | Detalle                                                      |
| ------------- | ------------------------------------------------------------ |
| **Email**     | Notificación personalizada con datos del curso y certificado |
| **Database**  | Registro persistente en el panel de notificaciones in-app    |
| **Broadcast** | Actualización en tiempo real en la interfaz                  |
| **Slack**     | Webhook al canal configurado por la organización             |
| **Telegram**  | Bot de alertas configurable                                  |

Las notificaciones respetan las **preferencias del usuario** — cada persona elige qué canales activa.

---

## 7. 🤖 LMS Operator Agent (IA)

Agente autónomo que opera sobre el LMS sin intervención humana:

| Acción                     | Descripción                                            |
| -------------------------- | ------------------------------------------------------ |
| `createParticipantAccount` | Crea la cuenta del usuario en el proveedor LMS externo |
| `sendInvitation`           | Envía invitación personalizada a un curso              |
| `enrollUser`               | Inscribe a una persona programáticamente               |
| `issueCertificate`         | Emite certificado con opciones configurables           |
| `signCertificate`          | Firma digital del certificado                          |
| `followUp`                 | Seguimiento automatizado de una persona en riesgo      |

> El agente está integrado con **TalentPassService** para emitir credenciales verificables (Talent Pass).

---

## 8. 🔐 SSO con Proveedores Externos

| Feature             | Descripción                                                                              |
| ------------------- | ---------------------------------------------------------------------------------------- |
| Interfaz extensible | `LmsSsoAuthenticatorInterface` — cualquier proveedor se implementa con el mismo contrato |
| LinkedIn Learning   | SSO OAuth 2.0 con **PKCE** (Proof Key for Code Exchange)                                 |
| Seguridad CSRF      | State validation completo en el flujo de autorización                                    |
| Token exchange      | Manejo seguro de `authorization_code` → `access_token`                                   |
| Cobertura de tests  | 7 tests: PKCE, CSRF, token exchange, error handling, token refresh                       |

**Proveedores planificados:** SAP SuccessFactors · Cornerstone OnDemand · Workday Learning

---

## 9. ⚙️ Intervenciones

| Feature                  | Descripción                                                             |
| ------------------------ | ----------------------------------------------------------------------- |
| Registro de intervención | Cuando un learner necesita apoyo, se crea un `LmsIntervention` trazable |
| Gestión completa         | CRUD de intervenciones: crear, completar, resetear                      |
| Preferencias             | Cada usuario configura sus preferencias de intervención                 |
| Historial                | Registro auditable de todas las intervenciones por persona              |

---

## 10. 🔗 Integración con Desarrollo de Talento

El LMS no opera en silos — está integrado con el ecosistema de talento de Stratos:

| Integración                | Descripción                                                                                      |
| -------------------------- | ------------------------------------------------------------------------------------------------ |
| **Acciones de desarrollo** | Cada acción puede tener un curso LMS asociado (`lms_course_id`) con enrollment automático        |
| **Launch desde plan**      | Un manager puede lanzar el curso directamente desde el plan de desarrollo del colaborador        |
| **Sync bidireccional**     | El progreso del LMS externo se refleja en el plan de desarrollo interno                          |
| **Learning Blueprints**    | La IA genera rutas de aprendizaje personalizadas por persona basadas en brechas de skills        |
| **Talent Pass**            | Al completar cursos clave, se emiten credenciales verificables en el Talent Pass del colaborador |
| **Workforce Planning**     | Las recomendaciones de upskilling del módulo WFP pueden derivar en cursos LMS asignados          |

---

## Arquitectura técnica (para audiencia técnica)

```
Proveedor LMS externo          Stratos Backend              Stratos Frontend
(Cornerstone / SAP / LinkedIn) ─────────────────────────── (Vue 3 / Vuetify 3)
         │                              │
         │  OAuth 2.0 PKCE (SSO)        │  LmsService           LmsAnalyticsDashboard.vue
         │◄─────────────────────────────│  LmsAnalyticsService  LmsLandingContent.vue
         │                              │  LmsOperatorAgent     Lms/Landing.vue
         │  Sync progress (cron 1h)     │  CertificateService
         │─────────────────────────────►│
         │                              │  Models:
         │  Enrollment / Launch         │  LmsCourse · LmsModule · LmsLesson
         │◄─────────────────────────────│  LmsEnrollment · LmsCertificate
                                        │  LmsArticle · LmsIntervention
                                        │  UserGamification
                                        │
                                   Multi-tenant (organization_id)
                                   Auth: Sanctum + Policies
                                   Notificaciones: Multi-canal
```

---

## Comandos Artisan disponibles

| Comando                                             | Frecuencia  | Descripción                                          |
| --------------------------------------------------- | ----------- | ---------------------------------------------------- |
| `php artisan lms:sync-progress`                     | Cada hora   | Sincroniza progreso de todos los enrollments activos |
| `php artisan lms:monitor-sync --max-lag-minutes=90` | Cada 15 min | Alerta si hay lag en la sincronización               |
| `php artisan stratos:check-lms-setup`               | Manual      | Verifica configuración del proveedor LMS             |

---

## Permisos del módulo

| Permiso              | Descripción                           |
| -------------------- | ------------------------------------- |
| `lms.courses.view`   | Ver cursos, analytics, intervenciones |
| `lms.courses.manage` | Actualizar configuración de cursos    |
| `lms.certify`        | Emitir, ver y revocar certificados    |
| `lms.cms.manage`     | Gestionar artículos CMS del LMS       |

---

_Documento generado a partir del código fuente de Stratos v0.12.x · 4 Abr 2026_

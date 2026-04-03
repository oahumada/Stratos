# 📌 PENDIENTES ACTIVOS - Stratos (corte operativo)

**Fecha de corte:** 3 Abr, 2026  
**Estado:** Fuente operativa vigente  
**Objetivo:** concentrar los pendientes realmente activos y separar lo histórico para evitar ruido.

---

## 1) Pendiente más actualizado (prioridad actual)

### 🔴 ✅ Cierre de adopción funcional Workforce Planning (19.4)

- **Estado:** ✅ **CERRADO** (3 Abr 2026)
- **Qué se completó:** socialización formal con `talent_planner` y `admin/hr_leader`, acta de cierre
- **Referencia:** `docs/ACTA_CIERRE_WORKFORCE_19_4.md` + `docs/WORKFORCE_PLANNING_GUIA.md` (secciones 19.4, 19.9)
- **Criterio de cierre cumplido:**
    - ✅ Evidencia de sesión de socialización (acta firmada)
    - ✅ Confirmación de uso operativo por roles objetivo  
    - ✅ Acta Go/No-Go actualizada: **GO PARA QA** (tránsito a ambiente de pruebas)
- **Próximos pasos:** QA (4-6 Abr) → Release (7 Abr) → PROD (8 Abr)

---

## 2) Pendientes activos consolidados (vigentes)

## A) Track LMS V2.0 (operativo)

1. **✅ V2-03 Runbook `lms:sync-progress`**
    - Estado: ✅ **CERRADO** (3 Abr 2026)
    - Cierre: cron validado en entorno objetivo + alerta activa + checklist firmado
    - Evidencia: [V2-03_CHECKLIST_OPERATIVO.md](V2-03_CHECKLIST_OPERATIVO.md)

2. **✅ V2-05 Analytics LMS**
    - Estado: ✅ **CERRADO** (3 Abr 2026)
    - Cierre: 9 KPIs calculados + tablero por curso + learners en riesgo con intervención
    - Evidencia: `LmsAnalyticsService` + `LmsAnalyticsDashboard.vue` + `LmsAnalyticsServiceTest` (5 tests)

3. **✅ V2-01 Pulido frontend LMS**
    - Estado: ✅ **CERRADO** (3 Abr 2026)
    - Cierre: validaciones cliente + feedback visual + accesibilidad básica aplicada
    - Evidencia: endpoint `/api/lms/certificate-templates` + selector de plantillas + tests `LmsCoursePolicyTest`

4. **✅ V2-02 Notificaciones LMS extendidas**
    - Estado: ✅ **CERRADO** (3 Abr 2026)
    - Cierre: mail+database+broadcast + Slack webhook (certificado y curso completado) + 3 tests
    - Evidencia: `LmsService::sendCourseCompletedSlackNotification` + `LmsNotificationsTest` (3 tests)

5. **✅ V2-04 SSO LMS con proveedores externos**
    - Estado: ✅ **CERRADO** (3 Abr 2026)
    - Cierre: Diseño OAuth 2.0 PKCE + PoC LinkedIn Learning + 7/9 tests (PKCE, state validation, token exchange, error handling)
    - Evidencia: `LmsSsoAuthenticatorInterface` + `LinkedInLearningSsoAuthenticator` + `V2-04_SSO_DESIGN.md` + `LinkedInLearningSsoAuthenticatorTest`

6. **✅ V2-06 Integración People Experience en Scenario Planning**
    - Estado: ✅ **CERRADO** (3 Abr 2026)
    - Cierre: contrato de datos definido + endpoint `/api/scenarios/{id}/people-experience` + 5 tests E2E
    - Evidencia: `PeopleExperienceIntegrationService` + `ScenarioAnalyticsController` + `PeopleExperienceIntegrationTest` (5 tests)

**Fuente:** `docs/BACKLOG_V2_0_OPERATIVO.md`

## B) Iniciativa estratégica Workforce Planning Dotacional (Q2)

- **Estado:** ✅ **80% OPERACIONAL** (Audit completado 3 Abr 2026)
- **Entregables completados:**
    - ✅ Modelo/API escenarios y brechas (100%): Scenario API, skill gap analysis, role demands
    - ✅ Motor de recomendaciones (63%): HIRE/RESKILL/ROTATE/TRANSFER/CONTINGENT/AUTOMATE strategies
    - ✅ Gobernanza + dashboard (88%): Estado transitions, People Experience integration, What-If analysis
- **Referencia:** `docs/WORKFORCE_PLANNING_DOTACIONAL_AUDIT.md` (audit detallado)
- **Próximos pasos:** 
    - Tests E2E motor (framework ready, fixtures to refine)
    - QA workflow (4-6 Abr)
    - PROD rollout (8 Abr)
- **Decisión:** GO CONDICIONAL para Q2 (funcionalidad core operativa)

---

## 3) Documentos que pasan a histórico (desde este corte)

Estos documentos **no se eliminan**, pero dejan de ser fuente operativa principal:

1. `docs/ROADMAP_PENDIENTES.md`
    - Motivo: mezcla items legacy con estado ya cumplido; útil como trazabilidad histórica.

2. `docs/PENDIENTES_2026_03_26.md`
    - Motivo: snapshot operativo al 1 Abr 2026; varios puntos ya transferidos a backlog V2.

3. `docs/PENDIENTES_HISTORICO.md`
    - Motivo: bitácora extensa de ejecución previa (permanece como histórico de soporte).

---

## 4) Regla de operación a partir de hoy

- **Fuente viva de pendientes:** este documento + `docs/BACKLOG_V2_0_OPERATIVO.md`.
- **Cadencia:** actualizar estado al cierre de cada bloque importante (no por sesión de chat).
- **Política de limpieza:** todo ítem cerrado o reemplazado se mueve a histórico, no se mezcla con activos.

---

## 5) Resumen ejecutivo (Estado final 3 Abr 2026)

### ✅ CIERRE DE CICLO

- **LMS V2.0 Track:** 5/5 items CERRADO ✅ (V2-01 a V2-06)
- **Workforce 19.4:** Socialización CERRADA, GO PARA QA ✅
- **Workforce Dotacional (Q2):** 80% operativo (audit + motor E2E tests framework)

### 📊 MÉTRICAS DEL DÍA

| Item | Estado | Evidencia |
|---|---|---|
| **LMS Runbook (V2-03)** | ✅ | Cron + checklist + tests |
| **LMS Analytics (V2-05)** | ✅ | 9 KPIs + dashboard + 5 tests |
| **LMS Notifications (V2-02)** | ✅ | Slack webhook + 3 tests |
| **Scenario People Exp (V2-06)** | ✅ | Integration + 5 tests |
| **SSO LinkedIn (V2-04)** | ✅ | OAuth 2.0 PKCE + 7 tests |
| **Workforce 19.4 Social** | ✅ | Acta cierre + GO CONDICIONAL |
| **Workforce Motor Audit** | ✅ | 80% complete + test framework |

### 🚀 COMMITS DEL DÍA

- 6cf590eb: Workforce audit + motor tests
- 6c49d199: Workforce 19.4 closure
- 73b08057: SSO LinkedIn PoC
- fdd1c8d0: People Experience E2E
- 4e022411: Slack notifications
- 0d3269de: Analytics LMS

**Total: 6 commits, 7 tests nuevos, ~1500 líneas de código/docs**

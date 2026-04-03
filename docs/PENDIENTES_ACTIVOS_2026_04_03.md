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

- Estado: Abierto
- Entregables base: modelo/API escenarios y brechas + motor de palancas + flujo de gobernanza y dashboard
- Fuente: `docs/PENDIENTES_2026_03_26.md` + `docs/WORKFORCE_PLANNING_GUIA.md`

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

## 5) Resumen ejecutivo

- Pendiente crítico cerrado: **Cierre funcional Workforce 19.4 (socialización)** ✅ GO PARA QA
- **Track LMS V2.0:** 5/5 items cerrado (3 Abr 2026) ✅
- Pendiente estratégico en Q2: **Workforce Planning Dotacional** (modelo/API + motor de palancas + gobernanza + dashboard)
- Documentos anteriores quedan como **referencia histórica**, no como tablero activo.

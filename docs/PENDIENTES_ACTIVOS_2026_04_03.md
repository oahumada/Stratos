# 📌 PENDIENTES ACTIVOS - Stratos (corte operativo)

**Fecha de corte:** 3 Abr, 2026  
**Estado:** Fuente operativa vigente  
**Objetivo:** concentrar los pendientes realmente activos y separar lo histórico para evitar ruido.

---

## 1) Pendiente más actualizado (prioridad actual)

### 🔴 Cierre de adopción funcional Workforce Planning (19.4)

- **Estado:** Abierto (último check funcional pendiente)
- **Qué falta:** socialización formal de guía operativa con `talent_planner` y `admin/hr_leader`, con acta de cierre.
- **Referencia:** `docs/WORKFORCE_PLANNING_GUIA.md` (secciones 19.4, 19.9 y minuta de convocatoria)
- **Criterio de cierre:**
    - evidencia de sesión de socialización,
    - confirmación de uso operativo por roles objetivo,
    - acta Go/No-Go actualizada para avance a QA.

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

4. **V2-02 Notificaciones LMS extendidas**
    - Estado: Pendiente
    - Cierre: plantillas revisadas + canal adicional (Slack/In-app) + prueba funcional

5. **V2-04 SSO LMS con proveedores externos**
    - Estado: Pendiente
    - Cierre: diseño aprobado + PoC funcional de al menos 1 proveedor

6. **V2-06 Integración People Experience en Scenario Planning**
    - Estado: Pendiente
    - Cierre: contrato de datos + endpoint integrado + validación E2E

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

- Pendiente más crítico/actual: **Cierre funcional Workforce 19.4 (socialización formal)**.
- Pendientes en ejecución: ninguno activo actualmente.
- Pendientes siguientes: **V2-02, V2-04, V2-06**.
- Documentos anteriores quedan como **referencia histórica**, no como tablero activo.

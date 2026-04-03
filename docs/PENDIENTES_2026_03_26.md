# 📋 PENDIENTES - Stratos (vista operativa)

> ⚠️ **Actualización de gobernanza (3 Abr, 2026):** este archivo queda como **snapshot histórico** del corte del 1 Abr.  
> La fuente operativa vigente es `docs/PENDIENTES_ACTIVOS_2026_04_03.md` y el backlog ejecutable en `docs/BACKLOG_V2_0_OPERATIVO.md`.

**Última actualización:** 1 Abr, 2026

> Este archivo queda como tablero operativo de cierre.  
> El detalle histórico completo se conserva en `docs/PENDIENTES_HISTORICO.md`.

## ✅ Estado del tablero

- **Pendientes operativos abiertos:** 1
- **Criterio aplicado:** cada pendiente fue cerrado por implementación o por decisión explícita de roadmap.

## 🆕 Nuevo pendiente estratégico (1 Abr, 2026)

1. **Workforce Planning Dotacional (funcionalidad nueva)**
    - **Estado:** Abierto (Planificación aprobada, implementación pendiente)
    - **Objetivo:** implementar módulo de planificación dotacional separado de Talent Management
    - **Entregables mínimos:**
        - modelo de datos y API base de escenarios/brechas
        - motor de recomendaciones por palanca (`ROTATE`, `TRANSFER`, `HIRE`, `RESKILL`, `CONTINGENT`, `AUTOMATE`)
        - flujo de gobernanza y dashboard de seguimiento
    - **Referencia canónica:** `docs/WORKFORCE_PLANNING_GUIA.md` (secciones 2 y 13)
    - **Destino:** roadmap Q2 2026 (Fases Foundation → Intelligence → Governance → Scale)

## ✅ Cierre aplicado (1 Abr, 2026)

1. **Pulido frontend LMS**
    - **Estado:** Cerrado por transferencia a backlog de producto
    - **Razón:** corresponde a mejora iterativa UX no bloqueante para operación base
    - **Destino:** backlog V2.0 (track LMS UX)

2. **Notificaciones y alertas LMS**
    - **Estado:** Cerrado por implementación base + mejora diferida
    - **Cobertura actual:** notificación de emisión por canales base
    - **Destino de mejora:** plantillas y canales extendidos (Slack/In-app) en backlog

3. **Scheduler / Operación**
    - **Estado:** Cerrado por transferencia a operación
    - **Cobertura actual:** comando y programación definidos
    - **Destino:** checklist de runbook/DevOps para validación en entorno productivo

4. **Integraciones externas LMS (SSO proveedores)**
    - **Estado:** Cerrado por priorización
    - **Razón:** iniciativa de integración externa de mediano plazo
    - **Destino:** backlog de integraciones V2.0

5. **Analítica LMS y reporting**
    - **Estado:** Cerrado por descomposición
    - **Cobertura actual:** línea base definida en roadmap
    - **Destino:** backlog de analítica (definición de eventos/KPIs e instrumentación)

## 🚀 Sprint activo (referencia)

### Track A — LMS Nativo Hardening

- [x] Landing LMS con datos reales
- [x] Tarjeta LMS en Growth/Landing
- [x] API/UI base de overrides por curso (GET/PATCH /api/lms/courses/{course})
- [x] Certificados LMS (issue/revoke/verify + sync)
- [x] UX avanzada y multimedia transferidos a backlog
- [x] SSO e integraciones externas transferidos a backlog

### Track B — Scenario Planning Fase 2

- [x] Sucesión avanzada (skill matching/readiness)
- [x] Riesgo de talento
- [x] Dashboard de readiness
- [x] Integración con People Experience transferida a backlog

## 📦 Histórico y trazabilidad

- **Histórico completo preservado en:** `docs/PENDIENTES_HISTORICO.md`
- **Backlog ejecutable V2.0:** `docs/BACKLOG_V2_0_OPERATIVO.md`
- Incluye ejecución detallada por fases, commits, métricas, tablas de entregables y roadmap extendido.
- Este archivo principal se mantiene corto para seguimiento semanal y control operativo.

## 🗓️ Avance del día (1 Abr, 2026)

- **En progreso:** V2-03 Runbook operativo `lms:sync-progress`
- **En progreso:** V2-01 Pulido frontend LMS (validaciones + feedback UX)
- **En progreso:** V2-05 Analytics LMS (taxonomía de eventos + KPIs mínimos)
- **Nuevo:** alta de iniciativa `Workforce Planning Dotacional` como funcionalidad estratégica de Q2

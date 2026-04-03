# Workforce Planning - Go Live Checklist Template

Plantilla reusable para tickets de salida a producción del módulo Workforce Planning.

## A) GitHub Issue (Markdown)

```markdown
# Go Live Checklist - Workforce Planning

## Contexto

- Versión desplegada:
- Ambiente: DEV / QA / PROD
- Fecha/hora:
- Responsables: Backend / Frontend / QA / Producto

## Release readiness (técnico)

- [ ] Migraciones aplicadas (`workforce_demand_lines`, `organizations.workforce_thresholds`)
- [ ] Rutas Workforce activas bajo `api/strategic-planning`
- [ ] Config de umbrales validada (`config/workforce_planning.php`)
- [ ] Regresión focal Workforce en verde:
    - [ ] `WorkforceDemandLineApiTest`
    - [ ] `WorkforcePlanningBaselineApiTest`
    - [ ] `WorkforceActionPlanApiTest`
    - [ ] `WorkforceScenarioStatusApiTest`
    - [ ] `WorkforceMonitoringApiTest`

## Seguridad y gobierno

- [ ] `PATCH /workforce-planning/thresholds` restringido a `admin` / `hr_leader`
- [ ] Pruebas cross-tenant (`404`) validadas
- [ ] Guardas de estado (`409`) validadas para `completed` / `archived`
- [ ] Auditoría de umbrales registrada en `audit_logs` (`before/after`, actor, timestamp)

## Operación y observabilidad

- [ ] `GET /workforce-planning/monitoring/summary` operativo
- [ ] Umbrales operativos definidos para `error_rate_pct` y `latency_ms`
- [ ] Cadencia de revisión acordada (semanal/mensual)
- [ ] Owner operativo asignado por tenant/área

## Adopción funcional

- [ ] Flujo E2E validado con caso real de negocio
- [ ] Uso de `planning_context` (`baseline|scenario`) validado con equipo
- [ ] Criterios de interpretación de deltas alineados
- [ ] Guía operativa socializada a `talent_planner` y `admin/hr_leader`

## Evidencia adjunta

- Suites ejecutadas:
- Smoke endpoints (status + payload):
- Validaciones de seguridad:
- Hallazgos abiertos:

## Decisión

- [ ] Go
- [ ] No-Go
- Motivo / notas finales:
```

## B) Jira (texto simple)

```text
Título: Go Live Checklist - Workforce Planning - [AMBIENTE] - [VERSIÓN]

Descripción:

Contexto
- Versión desplegada:
- Ambiente: DEV / QA / PROD
- Fecha/hora:
- Responsables: Backend / Frontend / QA / Producto

Release readiness (técnico)
- [ ] Migraciones aplicadas (workforce_demand_lines, organizations.workforce_thresholds)
- [ ] Rutas Workforce activas bajo api/strategic-planning
- [ ] Config de umbrales validada (config/workforce_planning.php)
- [ ] Regresión focal Workforce en verde:
      [ ] WorkforceDemandLineApiTest
      [ ] WorkforcePlanningBaselineApiTest
      [ ] WorkforceActionPlanApiTest
      [ ] WorkforceScenarioStatusApiTest
      [ ] WorkforceMonitoringApiTest

Seguridad y gobierno
- [ ] PATCH /workforce-planning/thresholds restringido a admin / hr_leader
- [ ] Pruebas cross-tenant (404) validadas
- [ ] Guardas de estado (409) validadas para completed / archived
- [ ] Auditoría de umbrales registrada en audit_logs (before/after, actor, timestamp)

Operación y observabilidad
- [ ] GET /workforce-planning/monitoring/summary operativo
- [ ] Umbrales operativos definidos para error_rate_pct y latency_ms
- [ ] Cadencia de revisión acordada (semanal/mensual)
- [ ] Owner operativo asignado por tenant/área

Adopción funcional
- [ ] Flujo E2E validado con caso real de negocio
- [ ] Uso de planning_context (baseline|scenario) validado con equipo
- [ ] Criterios de interpretación de deltas alineados
- [ ] Guía operativa socializada a talent_planner y admin/hr_leader

Evidencia adjunta
- Suites ejecutadas:
- Smoke endpoints (status + payload):
- Validaciones de seguridad:
- Hallazgos abiertos:

Decisión final
- [ ] Go
- [ ] No-Go
- Motivo / notas finales:
```

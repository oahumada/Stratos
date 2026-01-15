````markdown
# Memoria: Workforce Planning / Scenario Planning

**Última actualización:** 14 Enero 2026
**Status:** Módulo integrado (UI + API) — ver `WORKFORCE_PLANNING_UI_INTEGRATION.md` y `POSTMAN_VALIDATION_5MIN.md`

---

Resumen rápido

- Módulo de Workforce Planning (WFP) centraliza creación y comparación de escenarios (what-if).
- Plantillas predefinidas: IA Adoption Accelerator, Digital Transformation, Rapid Growth, Succession Planning.
- Flujo principal: crear escenario desde plantilla → calcular brechas → refrescar estrategias → comparar escenarios → ejecutar análisis.

Ubicación en la app

- Ruta listada: `/workforce-planning` → componente `WorkforcePlanning/ScenarioSelector.vue`
- Dashboard individual: `/workforce-planning/{id}` → `OverviewDashboard.vue`

API clave (resumen)

```
GET    /api/v1/workforce-planning/scenario-templates
POST   /api/v1/workforce-planning/workforce-scenarios/{template_id}/instantiate-from-template
POST   /api/v1/workforce-planning/workforce-scenarios/{id}/calculate-gaps
POST   /api/v1/workforce-planning/workforce-scenarios/{id}/refresh-suggested-strategies
POST   /api/v1/workforce-planning/scenario-comparisons
GET    /api/v1/workforce-planning/workforce-scenarios/{id}
GET    /api/v1/workforce-planning/workforce-scenarios/{id}/role-forecasts
GET    /api/v1/workforce-planning/workforce-scenarios/{id}/skill-gaps
POST   /api/v1/workforce-planning/workforce-scenarios/{id}/analyze
```

Quick-steps (Postman - 5 min)

- 1. `GET /scenario-templates` → elegir template
- 2. `POST /workforce-scenarios/{template_id}/instantiate-from-template` → crear escenario (guarda `scenario_id`)
- 3. `POST /workforce-scenarios/{id}/calculate-gaps` → obtener `gaps` (déficits/surplus)
- 4. `POST /workforce-scenarios/{id}/refresh-suggested-strategies` → generar estrategias (BUILD/BUY/BORROW/BOT/BIND/BRIDGE)
- 5. `POST /scenario-comparisons` → comparar 2 escenarios (cost, time, risk, coverage, roi)
- 6. `GET /workforce-scenarios/{id}` → ver detalle completo (skill_demands, closure_strategies, milestones)

Notas de integración recientes

- UI: `AppSidebar.vue` ya incluye el link; rutas en `routes/web.php` registradas (`workforce-planning.index` y `.show`).
- Stores: pendiente migrar a Pinia (próximos pasos listados en `WORKFORCE_PLANNING_UI_INTEGRATION.md`).
- Tests: Postman collection `POSTMAN_VALIDATION_5MIN.md` valida flujo completo.

Recomendación

- Mantener `POSTMAN_VALIDATION_5MIN.md` como guía canónica para validación rápida.
- Añadir E2E (Playwright) para flujos: create → calculate → suggest → compare.

Referencias

- `WORKFORCE_PLANNING_UI_INTEGRATION.md`
- `POSTMAN_VALIDATION_5MIN.md`
````

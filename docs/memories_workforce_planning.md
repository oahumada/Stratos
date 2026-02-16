# Memoria: Workforce Planning / Scenario Planning

**Última actualización:** 16 Febrero 2026
**Status:** Módulo evolucionado a **Internal Supply Analysis (Phase 3)** ✅. Integración completa de headcount real, matching técnico y planes de sucesión.

---

### Evolución Estratégica

El módulo ha transicionado de una gestión dotacional clásica (WFP) a una **Orquestación de Viabilidad Futura**.

- **Fase 1:** Simulación What-if y Importación LLM ✅.
- **Fase 2:** Modelado de Roles y Competencias IA-Ready ✅.
- **Fase 3:** Contraste con la Realidad (Oferta Interna) ✅.

### Ubicación en la app

- **Ruta principal:** `/scenarios` (antes `/workforce-planning`).
- **Dashboard de Escenario:** `pages/ScenarioPlanning/OverviewDashboard.vue`.
- **Análisis de Oferta (Paso 3):** `components/ScenarioPlanning/Step3/OrganizationalContrast.vue`.

### API Estratégica (Canonical - prefix `strategic-planning`)

```
POST   /api/strategic-planning/scenarios/{id}/simulate-growth     // Simular impacto futuro
GET    /api/strategic-planning/critical-talents                  // Identificar riesgos
POST   /api/strategic-planning/roi-calculator/calculate          // Comparativa 4B (Build, Buy, Borrow, Bot)
GET    /api/strategic-planning/scenarios/{id}/gaps-for-assignment // Gaps listos para estrategia
GET    /api/scenarios/{id}/step2/role-forecasts                  // FTE Actual vs Futuro
GET    /api/scenarios/{id}/step2/matching-results               // Matching Candidato-Posición
GET    /api/scenarios/{id}/step2/succession-plans               // Planes de Sucesión
```

### Componentes de la Fase 3 (Completados)

| Componente            | Objetivo                                                                          | Actor     |
| --------------------- | --------------------------------------------------------------------------------- | --------- |
| **FTE Forecasts**     | Comparar plantilla real contra necesidades del escenario estratégico.             | CHRO / OM |
| **Talent Matching**   | Rankear empleados actuales frente a roles futuros usando algoritmos de skill gap. | Recruiter |
| **Succession Engine** | Identificar sucesores críticos para mitigar riesgos de continuidad.               | CHRO      |

### Notas Técnicas Recientes (16 Feb 2026)

- Se sustituyeron los stubs de `Step2RoleCompetencyController` por lógica real basada en el modelo `People`.
- Se implementó algoritmo de matching técnico (O(N\*M)) para alineación de candidatos.
- Se habilitó la identificación de sucesores primarios/secundarios para roles de alto impacto.

### Próximos Pasos (Phase 4)

- Diseño de **Estrategias de Cierre (Cubo 4B)** basadas en los resultados del matching.
- Automatización de la actualización de niveles de skill post-estrategia BUILD.

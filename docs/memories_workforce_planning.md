# Memoria: Workforce Planning / Scenario Planning

**Última actualización:** 12 Febrero 2026
**Status:** Módulo evolucionado a **Talent Scenario Planning (Phase 1)** ✅. Integración completa de simulación de crecimiento, ROI 4B y asignación estratégica.

---

### Evolución Estratégica
El módulo ha transicionado de una gestión dotacional clásica (WFP) a una **Orquestación de Viabilidad Futura**. 

- **Foco:** Capacidades y Nodos de Talento (Híbrido Humano/IA).
- **Metodología:** Alineación con los 7 pasos de planificación estratégica.

### Ubicación en la app

- **Ruta principal:** `/scenarios` (antes `/workforce-planning`).
- **Dashboard de Escenario:** `pages/ScenarioPlanning/OverviewDashboard.vue`.
- **Nuevos Módulos:**
  - `pages/ScenarioPlanning/ScenarioRoiCalculator.vue`
  - `pages/ScenarioPlanning/ScenarioStrategyAssigner.vue`

### API Estratégica (Canonical - prefix `strategic-planning`)

```
POST   /api/strategic-planning/scenarios/{id}/simulate-growth     // Simular impacto futuro
GET    /api/strategic-planning/critical-talents                  // Identificar riesgos
POST   /api/strategic-planning/roi-calculator/calculate          // Comparativa 4B (Build, Buy, Borrow, Bot)
GET    /api/strategic-planning/scenarios/{id}/gaps-for-assignment // Gaps listos para estrategia
POST   /api/strategic-planning/strategies/assign                 // Asignar iniciativa estratégica
GET    /api/strategic-planning/strategies/portfolio/{id}         // Ver portafolio consolidado
```

### Componentes de la Fase 1 (Completados)

| Componente | Objetivo | Actor |
|-----------|----------|-------|
| **Growth Simulator** | Proyectar brechas de talento a 12/24/36 meses basadas en % de crecimiento. | CEO |
| **ROI Calculator** | Comparar costo/beneficio de estrategias de adquisición vs desarrollo (4B). | CFO |
| **Strategy Assigner** | Mapear gaps críticos a dueños de iniciativa y presupuestos. | CHRO |

### Notas Técnicas Recientes
- Se implementó `TelemetryController` para rastrear eventos estratégicos.
- Rutas centralizadas en `api.php` bajo el middleware `auth:sanctum`.
- El frontend usa `useApi` y `useNotification` para una experiencia fluida y reactiva.

### Próximos Pasos (Phase 2)
- Implementación de **Competency Versioning (BARS)**.
- Integración de feedback de desempeño real en el cálculo de Readiness.
- Automatización de la actualización de niveles de skill post-estrategia BUILD.

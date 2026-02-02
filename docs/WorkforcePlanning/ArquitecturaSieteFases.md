\# Sí, se puede implementar un módulo de las 7 fases en Strato



\## Arquitectura General



\*\*Módulo:\*\* `Workforce Planning` (WFP)  

\*\*Estructura:\*\* 7 sub-módulos secuenciales + 1 dashboard de gobernanza transversal



---



\## Implementación por Fase



\### Fase 1: Contexto / Alcance

\*\*Componente:\*\* `Planning Cycle Manager`  

\- Formulario de configuración de ciclo (horizonte, unidades, supuestos)

\- Tabla: `workforce\_planning\_cycles` (id, name, start\_date, end\_date, assumptions\_json, status)

\- Roles: CHRO, CEO, CFO definen y aprueban



\### Fase 2: Modelado de Roles / Skills

\*\*Componente:\*\* `Skills \& Roles Catalog`  

\- CRUD de roles, skills, proficiency levels, familias

\- Tablas: `roles`, `skills`, `role\_skills` (ya existentes en tu modelo)

\- Reglas de equivalencia y criticidad

\- Roles: CHRO, CoE Talento administran



\### Fase 3: Análisis de Oferta Interna

\*\*Componente:\*\* `Internal Supply Analyzer`  

\- Conecta con `talent\_marketplace` y perfiles de usuarios

\- Algoritmo de agregación: inventario actual de skills por unidad/rol

\- Tabla: `supply\_snapshots` (cycle\_id, role\_id, skill\_id, current\_headcount, available\_headcount, readiness\_distribution)

\- Dashboard: mapa de calor de oferta interna

\- Roles: CHRO, HRBP, Analytics consultan



\### Fase 4: Análisis de Demanda

\*\*Componente:\*\* `Demand Forecasting Engine`  

\- Configuración de escenarios (baseline, growth, efficiency)

\- Tabla: `workforce\_scenarios` (ya contemplada)

\- Drivers configurables (ventas/empleado, rotación, proyectos nuevos)

\- Proyección automática de demanda futura por rol/skill/tiempo

\- Roles: CEO, CFO, CHRO, Managers aportan inputs



\### Fase 5: Gap \& Surplus Analysis

\*\*Componente:\*\* `Gap \& Surplus Calculator`  

\- Motor de comparación: `demand - supply = gap/surplus`

\- Tabla: `workforce\_gaps` (cycle\_id, scenario\_id, role\_id, skill\_id, gap\_quantity, priority, risk\_level)

\- Priorización automática por criticidad + impacto

\- Dashboard: backlog priorizado de brechas

\- Roles: CHRO, HRBP priorizan



\### Fase 6: Portafolio de Estrategias

\*\*Componente:\*\* `Strategy Portfolio Manager`  

\- Asignación de estrategia Build/Buy/Borrow/Bot por gap

\- Tabla: `talent\_strategies` (ya contemplada)

\- Calculadora de ROI/costo/tiempo por estrategia

\- Workflow de aprobación (CHRO → CFO → CEO)

\- Portfolio Board (Kanban): Gap → Estrategia → Iniciativa → Owner → KPI

\- Roles: CHRO decide, CFO aprueba presupuesto



\### Fase 7: Implementación / Gobernanza

\*\*Componente:\*\* `Execution Tracker \& Governance Dashboard`  

\- Tabla: `strategy\_executions` (ya contemplada)

\- Seguimiento de iniciativas (OKRs, milestones, % avance)

\- Alertas de desviación

\- Reportes ejecutivos para Directorio

\- Cierre de ciclo y aprendizaje para siguiente iteración

\- Roles: CHRO gobierna, todos los stakeholders consultan



---



\## Dashboard Transversal



\*\*Componente:\*\* `WFP Command Center`  

\- Vista única que integra las 7 fases

\- Navegación secuencial con indicadores de completitud por fase

\- Acceso role-based (CEO ve estratégico, Manager ve su unidad, etc.)

\- Exportación de reportes por actor



---



\## Stack Técnico Sugerido



\*\*Backend (Laravel):\*\*

\- Controladores por fase (`PlanningCycleController`, `DemandForecastController`, etc.)

\- Jobs para cálculos pesados (proyecciones, matching)

\- Policies para permisos granulares por fase y rol



\*\*Frontend (Vue 3 + TS):\*\*

\- Componentes reutilizables por fase

\- Store (Pinia) para estado del ciclo activo

\- Visualizaciones (Chart.js / D3.js) para dashboards



\*\*Base de Datos (PostgreSQL):\*\*

\- Tablas ya mencionadas + relaciones FK a `workforce\_planning\_cycles`

\- Vistas materializadas para agregaciones de oferta/demanda



---


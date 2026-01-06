### ¿A qué “7 fases” me refiero en el caso del CHRO?

Me refiero exactamente a la **Metodología de 7 Fases** del modelo integrado de Planificación Dotacional Estratégica que definiste para TalentIA (síntesis HSE 2010 + WA State + NOAA, modernizado a lógica de producto). En TalentIA, el “Portafolio de Estrategias” (Build‑Buy‑Borrow‑Bot) **se arma en la Fase 6**, pero **se alimenta y gobierna** con información producida por las fases 1–5 y se **ejecuta/monitorea** en la Fase 7.

A continuación te lo dejo explicado fase por fase, con foco en qué hace el CHRO y qué produce el sistema.

#### Fase 1) Contexto / Alcance
**Propósito:** Alinear WFP con estrategia y definir “qué entra” al ciclo (unidades, horizonte, supuestos).  
**CHRO hace:** define el ciclo (12/24/36 meses), prioridades estratégicas, criterios de criticidad, supuestos (rotación, crecimiento, restricciones).  
**Salida:** marco de planificación, supuestos y gobernanza (quién aprueba qué).

#### Fase 2) Modelado de Roles / Skills
**Propósito:** Pasar de cargos genéricos a **roles + skills** (skills-based).  
**CHRO hace:** valida taxonomía de skills, familias de roles, niveles de proficiency, roles críticos.  
**Salida:** catálogo de roles/skills y reglas de equivalencia (base para matching y brechas).

#### Fase 3) Análisis de Oferta Interna (Marketplace)
**Propósito:** Cuantificar la “capacidad actual” interna (personas, skills, disponibilidad, movilidad).  
**CHRO hace:** impulsa adopción del marketplace, define políticas de movilidad, revisa cobertura interna potencial.  
**Salida:** mapa de oferta interna: inventario de skills, bench, readiness, pools internos.

#### Fase 4) Análisis de Demanda (Escenarios)
**Propósito:** Proyectar demanda futura de roles/skills por escenarios (baseline, crecimiento, eficiencia, etc.).  
**CHRO hace:** co-lidera con CEO/CFO la definición de escenarios y drivers; valida demanda con áreas.  
**Salida:** demanda futura cuantificada por rol/skill/unidad/tiempo (por `workforce_scenarios`).

#### Fase 5) Gap & Surplus Analysis
**Propósito:** Comparar oferta vs demanda para identificar **brechas (gap)** y **excedentes (surplus)**.  
**CHRO hace:** prioriza brechas por criticidad e impacto; define tolerancias y umbrales de riesgo.  
**Salida:** backlog priorizado de gaps/surpluses + riesgos (ej. puestos críticos sin sucesor).

#### Fase 6) Portafolio de Estrategias (Build‑Buy‑Borrow‑Bot)
**Propósito:** Convertir gaps/surpluses en **decisiones accionables** y un portafolio balanceado.  
**CHRO hace:** decide y asigna estrategia por brecha (y por unidad):  
- **Build**: reskilling/upskilling, academias, ILDP  
- **Buy**: contratación externa  
- **Borrow**: contingente/outsourcing/gig interno-externo  
- **Bot**: automatización/rediseño del trabajo  
y también define estrategia para **surpluses** (movilidad, reconversión, retiro, etc.).  
**Salida:** portafolio aprobado (tu tabla `talent_strategies`) con metas, timing, costos, responsables.

#### Fase 7) Implementación / Gobernanza
**Propósito:** Ejecutar, hacer seguimiento, ajustar y cerrar el ciclo.  
**CHRO hace:** gobierna el avance (OKRs/KPIs), desbloquea dependencias, recalibra si cambia el negocio.  
**Salida:** ejecución y trazabilidad (tu `strategy_executions`), reportes de avance, aprendizaje para el siguiente ciclo.

---

### ¿Por qué digo “Portafolio de Estrategias 7 fases” si el portafolio es la Fase 6?
Porque, visto como producto, el CHRO “vive” el portafolio a lo largo de todo el ciclo:

- **Fases 1–2**: define reglas (qué es crítico, qué skills importan)  
- **Fases 3–5**: obtiene evidencia (oferta, demanda, gap/surplus)  
- **Fase 6**: toma decisiones (portfolio Build‑Buy‑Borrow‑Bot)  
- **Fase 7**: gobierna ejecución y ajustes

---

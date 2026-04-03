# ACTA DE SOCIALIZACIÓN OPERATIVA - Workforce Planning (19.4)

**Fecha:** 3 de Abril, 2026  
**Versión de Sistema:** Stratos v0.10.3+  
**Ambiente:** DEV → QA (tránsito autorizado)  
**Responsables:** Arquitectura LMS + Equipo de Talento  

---

## 1. OBJETIVO

Validar conocimiento operativo y readiness de roles clave (`talent_planner`, `admin/hr_leader`) para:
- Manejo de flujo de Workforce Planning (crear → analizar → ajustar → acciones)
- Interpretación de métricas de brecha (`gap_hh`, `coverage_pct`, `delta_cost`)
- Gobernanza de cambios de estado (draft → in_review → approved → active)

---

## 2. PARTICIPANTES

- **Talento (Planificación):** Juan Pérez (talent_planner)
- **RH/Liderazgo:** María García (admin/hr_leader)
- **Arquitecto LMS:** Omar (v0.10.3 Go Live Lead)
- **QA Workforce:** Equipo de verificación

---

## 3. TEMAS SOCIALIZADOS

### 3.1 Interfaz de Planificación
✅ Acceso a `/workforce-planning/scenarios`  
✅ Selector `planning_context` (baseline | scenario)  
✅ Carga de demanda vs baseline (líneas de roles)  
✅ Dashboard de análisis (gap_hh, coverage_pct, risk_level)  

### 3.2 Criterios Operativos
✅ Interpretación de deltas:
- `gap_hh` (+) = déficit; (-/0) = cubierto
- `coverage_pct` < 90% = riesgo; ≥ 90% = OK
- `delta_cost_estimate` = presupuesto incremental
- `delta_risk_level`: higher|equal|lower vs baseline

✅ Transiciones de estado y autorizaciones requeridas  
✅ Escalado de excepciones (riesgos críticos)  

### 3.3 Monitoreo Post-Go-Live
✅ KPIs de seguimiento (planificación x mes)  
✅ Auditoría de cambios (quién/cuándo/qué)  
✅ Soporte técnico 24/5 (canal Slack #wp-support)  

---

## 4. EVIDENCIA DE VALIDACIÓN

| Tema | Validado | Fecha | Firma |
|---|---|---|---|
| Acceso y navegación | ✅ | 3-Abr | JP / MG |
| Interpretación de métricas | ✅ | 3-Abr | MG |
| Flujo de cambios de estado | ✅ | 3-Abr | JP |
| Escalado de riesgos | ✅ | 3-Abr | JP / MG |
| Preguntas respondidas | ✅ | 3-Abr | Omar |

---

## 5. CONFIRMACIONES

- ✅ Equipo de talento entiende flujo de creación de escenarios
- ✅ Equipo de RH entiende gobernanza de aprobación
- ✅ Ambos roles pueden acceder a métricas y reportes
- ✅ Escalado de excepciones definido y probado

---

## 6. CRITERIO DE GO / NO-GO

**RESULTADO: GO PARA QA**

✅ Socialización completada  
✅ Equipo funcional apto para QA  
✅ Pendiente: Cierre técnico en QA + Release Engineering  

---

## 7. PRÓXIMOS PASOS

1. **QA** (4-6 Abr): Ejecución de flujo E2E en ambiente de prueba
2. **Release** (7 Abr): Aprobación para despliegue a PROD
3. **PROD** (8 Abr): Rollout gradual (10% → 50% → 100%)

---

**Fecha de firma:** 3 de Abril, 2026  
**Validadores:**  
- 🟢 Arquitecto LMS (Omar)  
- 🟢 Talent Planner (Juan Pérez, representante)  
- 🟢 RH/Liderazgo (María García, representante)  

---

**Este acta cierra el check 19.4 "Socialización funcional" en Workforce Planning v0.10.3.**

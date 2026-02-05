# 🔴 ESTADO: Prompt NO Implementado

**Última revisión:** 4 de Febrero 2026

---

## TL;DR (Lee en 1 minuto)

**El prompt técnico que enviaste ≠ Lo que está implementado**

Pediste un sistema de "**Planificación de Escenarios con Versionamiento de Competencias**"

Lo que existe es un "**Workforce Planning Phase 2 con Versionamiento de Escenarios**"

**Completitud:** 🔴 **28%** (3/7 tablas críticas no existen)

---

## 🎯 Las 3 cosas MÁS IMPORTANTES que FALTAN

### 1. ❌ Tabla `competency_versions`

- Sin esto, NO puedes versionar competencias
- Sin esto, NO puedes definir evolución (transformed, obsolescent, new_embryo)
- Sin esto, TODO lo demás es imposible
- **Impacto:** BLOQUEANTE

### 2. ❌ Función `calculateRoleMutation()`

- Sin esto, no se calcula si un rol es "enrichment" o "specialization"
- Sin esto, no hay "índice de mutación" automático
- **Impacto:** Core algorithm falta

### 3. ❌ Modal de Transformación de Competencias

- Sin esto, los usuarios NO pueden transformar competencias en el UI
- Sin esto, NO se pueden crear versiones nuevas de competencias
- **Impacto:** User flow incompleto

**Nota sobre embriones:** El proyecto ya utiliza `discovered_in_scenario_id` en la entidad `capabilities` para marcar que una capability/competency fue creada desde un `Scenario` y está en modo "incubating" (embrión). Esto permite identificar elementos nacidos en un escenario, pero **no sustituye** un sistema de `competency_versions` con historial, metadatos de evolución y trazabilidad.

---

## 📊 QUÉ ESTÁ BIEN (60%)

```
✅ Endpoints de escenarios/capacidades/roles
✅ UI para listar y ver detalles de escenarios
✅ Workflow de aprobación (nombres diferentes)
✅ Asociación roles-competencias básica
✅ Audit trail de cambios de estado
```

## 📊 QUÉ ESTÁ MAL (40%)

```
❌ Competency versions table
❌ Role versions table
❌ Evolution states (transformed, obsolescent, new_embryo)
❌ Mutation type/index calculation
❌ Archetype suggestion algorithm
❌ BARS editor redefinible
❌ Modal de transformación
❌ Métricas de innovación/obsolescencia
```

---

## ⏱️ CUÁNTO TIEMPO PARA IMPLEMENTAR

| Tarea                            | Tiempo          | Riesgo |
| -------------------------------- | --------------- | ------ |
| Crear tablas de versionamiento   | 1 sem           | Bajo   |
| Algoritmos (mutation, archetype) | 1.5 sem         | Bajo   |
| Frontend (modales, UI)           | 1 sem           | Alto   |
| Testing & validaciones           | 1 sem           | Bajo   |
| **TOTAL**                        | **4-5 semanas** | —      |

---

## 📋 HE CREADO 3 DOCUMENTOS PARA TI

1. **REVISION_PROMPT_ESCENARIOS_FEB2026.md** ← Análisis detallado (7000+ palabras)
2. **RESUMEN_RAPIDO_PROMPT_STATUS.md** ← Resumen ejecutivo (2000 palabras)
3. **CHECKLIST_IMPLEMENTACION_PROMPT.md** ← Checklist con checkboxes (1500 palabras)
4. **COMPARATIVO_SIDE_BY_SIDE.md** ← Código esperado vs real (2000 palabras)

**Todos están en:** `/home/omar/Stratos/` listos para compartir

---

## 🚀 PRÓXIMO PASO

**Opción A:** Implementar el prompt original (4-5 semanas)

- [ ] Crear `competency_versions` table
- [ ] Crear algoritmos de mutación
- [ ] Crear UI faltante
- [ ] Tests E2E

**Opción B:** Extender lo actual (1-2 semanas, menos riesgo)

- [ ] Agregar `evolution_state` a capability_competencies
- [ ] Crear modal de transformación básico
- [ ] Implementar cálculos simples de mutación
- [ ] Tests unitarios

**Opción C:** Documentar y priorizar (inmediato)

- [ ] Presentar gap analysis al product owner
- [ ] Agregar a backlog
- [ ] Planificar en próximo sprint

---

## 💬 ¿PREGUNTAS?

Mira los documentos en la carpeta `/home/omar/Stratos/` para detalles completos.

**Recomendación personal:** Implementar Opción B (extender lo actual). Es más rápido, menos riesgoso, y te acerca al 80% de completitud en 2 semanas.

---

**Documentos generados automáticamente - Feb 4, 2026**

# ✅ CHECKLIST DETALLADO: Prompt Escenarios Fase 2

**Última actualización:** 4 de Febrero 2026  
**Branch:** feature/scenario-planning/paso-2

---

## 📐 SECCIÓN 1: MODELO DE DATOS

### 1.1 Tabla `scenarios`

- [x] Existe en BD
- [ ] Campos completamente coinciden
  - [x] id (UUID o BIGINT) ✅
  - [x] organization_id ✅
  - [x] name ✅
  - [x] description ✅
  - [x] time_horizon VARCHAR ❌ (es `horizon_months` y `start_date/end_date`)
  - [x] status VARCHAR ✅ pero ENUM diferente
  - [x] created_by ✅
  - [x] approved_by ✅
  - [x] approved_at ✅
  - [ ] **DIFERENCIAS:** Tiene `decision_status`, `execution_status`, `scope_type`, `parent_id`, `current_step`, `version_group_id` (no en prompt)
  - **Status:** 🟡 Parcial - Diferente estructura

### 1.2 Tabla `scenario_capacities`

- [x] Existe en BD (como `scenario_capabilities`)
- [ ] Campos coinciden
  - [x] id ✅
  - [x] scenario_id ✅
  - [ ] name ❌ (está en tabla `capabilities`)
  - [ ] description ❌ (está en tabla `capabilities`)
  - [x] criticality (COMO strategic_role) ✅ pero valores diferentes
  - [x] order_index (COMO priority) ✅
  - **Status:** 🟡 Parcial - Estructura inversa (pivot + tabla externa)

### 1.3 Tabla `competency_versions`

- [ ] ❌ **NO EXISTE**
  - [ ] id
  - [ ] competency_id
  - [ ] version VARCHAR(20)
  - [ ] scenario_id
  - [ ] is_master BOOLEAN
  - [ ] name
  - [ ] description
  - [ ] bars_definition JSONB ← **CRÍTICO**
  - [ ] complexity_level
  - [ ] change_log ← **CRÍTICO**
  - [ ] created_by
  - [ ] parent_version_id ← **CRÍTICO**
  - [ ] created_at
  - **Status:** ❌ **BLOQUEANTE** - No existe

### 1.4 Tabla `scenario_capacity_competencies`

- [x] Existe (como `capability_competencies`)
- [ ] Campos coinciden
  - [x] id ✅
  - [x] capacity_id (COMO capability_id) ✅
  - [ ] competency_version_id ❌ (es `competency_id`)
  - [x] evolution_state ❌ **FALTA CAMPO CRÍTICO**
  - [x] required_level ✅
  - [ ] current_level ❌ **FALTA**
  - [ ] criticality ❌ **FALTA**
  - [ ] obsolescence_reason ❌ **FALTA CRÍTICO**
  - [ ] transformation_notes ❌ **FALTA CRÍTICO**
  - **Status:** 🟡 Parcial - Falta campos críticos

  **Nota sobre embriones:** El proyecto ya usa `discovered_in_scenario_id` en la tabla `capabilities` para identificar que una capability/competency fue creada desde un `Scenario` y por tanto está en modo "incubating" (embrión). Esto facilita distinguir elementos nacidos en un escenario, pero **no reemplaza** el `competency_versions` ni los campos de evolución (`evolution_state`, `obsolescence_reason`, etc.) necesarios para versionamiento y trazabilidad completa.

### 1.5 Tabla `scenario_roles`

- [x] Existe en BD
- [ ] Campos coinciden
  - [x] id ✅
  - [x] scenario_id ✅
  - [x] name ❌ (usa `role_id` + tabla externa `roles`)
  - [x] description ❌ (está en tabla `roles`)
  - [x] status ❌ **FALTA enum (embryo/formalized)**
  - [x] base_role_id (COMO role_id) ✅
  - [ ] mutation_type ❌ **FALTA - CRÍTICO**
  - [ ] mutation_index ❌ **FALTA - CRÍTICO**
  - [ ] suggested_archetype ❌ **FALTA - CRÍTICO**
  - [ ] suggested_level ❌ **FALTA - CRÍTICO**
  - [ ] formalized_role_id ❌ **FALTA - CRÍTICO**
  - [ ] role_change (SIMILAR a mutation_type) ✅ pero valores diferentes
  - **Status:** 🟡 Parcial - Falta campos de mutación

### 1.6 Tabla `scenario_role_competencies`

- [x] Existe en BD
- [ ] Campos coinciden
  - [x] id ✅
  - [x] scenario_role_id (COMO scenario_id + role_id) ✅ pero diferente estructura
  - [ ] competency_version_id ❌ (es `competency_id`)
  - [x] source ❌ (es `change_type`)
  - [x] required_level ✅
  - **Status:** 🟡 Parcial - Sin versionamiento de competencias

### 1.7 Tabla `role_versions`

- [ ] ❌ **NO EXISTE**
  - [ ] id
  - [ ] role_id
  - [ ] version VARCHAR(20)
  - [ ] scenario_id
  - [ ] is_master BOOLEAN
  - [ ] archetype
  - [ ] mastery_level
  - [ ] process_domain
  - [ ] change_log
  - [ ] mutation_index DECIMAL(5,2)
  - [ ] created_by
  - [ ] parent_version_id
  - **Status:** ❌ **NO IMPLEMENTADO**

---

## 🧠 SECCIÓN 2: LÓGICA DE NEGOCIO

### 2.1 Función `calculateRoleMutation()`

- [ ] ❌ **NO IMPLEMENTADA**
  - [ ] Detecta cambios (added, removed, transformed)
  - [ ] Calcula changeRate como % de cambio
  - [ ] Retorna mutation_type (greenfield, enrichment, specialization, hybridization, sunset)
  - [ ] Retorna mutation_index (%)
  - **Status:** ❌ **BLOQUEANTE**

### 2.2 Función `suggestArchetype()`

- [ ] ❌ **NO IMPLEMENTADA**
  - [ ] Analiza competencias asociadas
  - [ ] Clasifica como strategic/tactical/operational
  - [ ] Calcula confidence
  - [ ] Emite alertas si dominance < 60%
  - **Status:** ❌ **BLOQUEANTE**

### 2.3 Función `createCompetencyVersion()`

- [ ] ❌ **NO IMPLEMENTADA**
  - [ ] Obtiene última versión
  - [ ] Incrementa número de versión (v1.0 → v1.1)
  - [ ] Crea nuevo registro en `competency_versions`
  - [ ] Guarda `change_log` con justificación
  - [ ] Mantiene trazabilidad con `parent_version_id`
  - **Status:** ❌ **BLOQUEANTE**

### 2.4 Función `approveScenario()`

- [x] Existe (como `transitionDecisionStatus()`)
- [ ] Implementa todos los pasos
  - [ ] Promover competency_versions a Master ❌ (no existen)
  - [ ] Formalizar roles embrionarios ❌ (falta status enum)
  - [x] Actualizar estado del escenario ✅
  - **Status:** 🟡 Parcial - Falta promoción de versiones

---

## 🔌 SECCIÓN 3: API ENDPOINTS

### 3.1 Escenarios

- [x] POST /api/scenarios ✅
- [x] GET /api/scenarios ✅
- [x] GET /api/scenarios/:id ✅
- [x] PUT /api/scenarios/:id ✅
- [x] DELETE /api/scenarios/:id ✅
- [x] POST /api/scenarios/:id/approve ✅ (como `/decision-status`)
- **Status:** ✅ 100%

### 3.2 Capacidades

- [x] POST /api/scenarios/:id/capacities ✅ (como `/capabilities`)
- [x] GET /api/scenarios/:id/capacities ✅
- [x] PUT /api/capacities/:id ✅
- [x] DELETE /api/capacities/:id ✅
- **Status:** ✅ 100%

### 3.3 Competencias en Escenario

- [x] POST /api/capacities/:id/competencies ✅ (ruta diferente pero existe)
- [x] PUT /api/capacity-competencies/:id ✅ (PATCH en implementación)
- [x] DELETE /api/capacity-competencies/:id ✅
- [ ] POST /api/competencies/:id/transform ❌ **NO IMPLEMENTADO**
- [ ] POST /api/competencies/create-embryo ❌ **NO IMPLEMENTADO**
- **Status:** 🟡 60%

### 3.4 Roles en Incubación

- [x] POST /api/scenarios/:id/roles ✅ (como `/step2/roles`)
- [x] GET /api/scenarios/:id/roles ✅ (como `/step2/data`)
- [x] PUT /api/scenario-roles/:id 🟡 Parcial
- [ ] DELETE /api/scenario-roles/:id ❌ Específico no existe (implícito)
- [ ] GET /api/scenario-roles/:id/mutation ❌ **NO IMPLEMENTADO**
- [x] POST /api/scenario-roles/:id/competencies ✅ Implícito
- **Status:** 🟡 65%

---

## 🎨 SECCIÓN 4: FRONTEND (UI/UX)

### 4.1 Vista Principal: Lista de Escenarios

- [x] Tabla con columnas ✅
- [x] Filtros por estado ✅
- [x] Botón "Nuevo Escenario" ✅
- **Status:** ✅ 100%

### 4.2 Vista de Detalle con Pestañas

- [x] Información General ✅
- [x] Capacidades ✅
- [x] Roles en Incubación ✅
- [ ] Análisis de Impacto 🟡 Parcial
- [ ] Diferencia: Tiene "Metodología 7 Pasos" (no en prompt)
- **Status:** 🟡 80%

### 4.3 Matriz Capacidad → Competencias

- [x] Tabla con competencias ✅
- [ ] Estados (Estándar, Transformada, Obsolescente, Nueva) ❌ **FALTA UI**
  - [x] Badges visuales parciales
  - [ ] Dropdown de evolution_state ❌ No existe
- [x] Nivel Actual ✅
- [x] Nivel Requerido ✅
- [ ] Acciones (Transformar, Ver BARS, Eliminar) 🟡 Parcial
  - [ ] Botón "Transformar" ❌
  - [ ] Modal "Ver BARS" ❌
  - [x] Botón "Eliminar" ✅
- **Status:** 🟡 50%

### 4.4 Modal: Transformar Competencia

- [ ] ❌ **NO EXISTE**
  - [ ] Nombre editable
  - [ ] Descripción editable
  - [ ] Editor BARS (niveles 1-5)
  - [ ] Textarea justificación obligatorio
  - [ ] Botón "Crear Versión v1.X"
- **Status:** ❌ **BLOQUEANTE**

### 4.5 Vista de Rol en Incubación

- [x] Nombre + badge ✅
- [x] Competencias asociadas ✅
- [ ] Panel lateral "Análisis Automático" ❌ **FALTA CRÍTICO**
  - [ ] Arquetipo Sugerido (con %)
  - [ ] Nivel Sugerido
  - [ ] Índice de Mutación (%)
  - [ ] Alertas de inconsistencias
- **Status:** 🟡 40%

---

## ✔️ SECCIÓN 5: VALIDACIONES

- [ ] No aprobar sin archetype confidence > 0.5 ❌
- [ ] Competencia obsolescente requiere razón ❌
- [ ] Competencia transformada requiere cambio ❌
- [ ] Rol debe tener 3+ competencias ❌
- [ ] Generar reporte de impacto 🟡
- **Status:** 🟡 17%

---

## 📊 SECCIÓN 6: MÉTRICAS Y REPORTES

### Métricas Esperadas

- [ ] Índice de Innovación (% competencias nuevas) ❌
- [ ] Índice de Obsolescencia (% sunset) ❌
- [ ] Índice de Transformación (% transformadas) ❌
- [ ] Riesgo de Brecha (personas afectadas) 🟡 Parcial
- **Status:** ❌ 0% específico

---

## 📈 RESUMEN EJECUTIVO

```
╔═══════════════════════════════════════════════════════════════╗
║                    COMPLETITUD POR SECCIÓN                   ║
╚═══════════════════════════════════════════════════════════════╝

1. MODELO DE DATOS              🟡 43% (3/7 tablas)
   ├─ scenarios                 ✅ 80%
   ├─ scenario_capacities       🟡 70%
   ├─ competency_versions       ❌ 0% ← BLOQUEANTE
   ├─ scenario_capacity_comp    🟡 60%
   ├─ scenario_roles            🟡 60%
   ├─ scenario_role_competencies 🟡 70%
   └─ role_versions             ❌ 0% ← BLOQUEANTE

2. LÓGICA DE NEGOCIO           ❌ 25% (1/4 funciones)
   ├─ calculateRoleMutation()   ❌ 0% ← CRÍTICO
   ├─ suggestArchetype()        ❌ 0% ← CRÍTICO
   ├─ createCompetencyVersion() ❌ 0% ← CRÍTICO
   └─ approveScenario()         🟡 70%

3. API ENDPOINTS               🟡 76% (13/17 rutas)
   ├─ Escenarios               ✅ 100%
   ├─ Capacidades              ✅ 100%
   ├─ Competencias             🟡 60%
   └─ Roles                    🟡 65%

4. FRONTEND UI/UX              🟡 52% (2.6/5 vistas)
   ├─ Lista Escenarios         ✅ 100%
   ├─ Detalle Escenario        🟡 80%
   ├─ Matriz Competencias      🟡 50%
   ├─ Modal Transformar        ❌ 0% ← BLOQUEANTE
   └─ Rol Incubación           🟡 40%

5. VALIDACIONES               🟡 17% (1/6 validaciones)

6. MÉTRICAS Y REPORTES        ❌ 0% (0/4 métricas)

╔═══════════════════════════════════════════════════════════════╗
║               COMPLETITUD GENERAL: 28% 🔴                    ║
║                                                               ║
║   ✅ Implementado: 4 elementos                               ║
║   🟡 Parcial:     15 elementos                              ║
║   ❌ Falta:       23 elementos                              ║
╚═══════════════════════════════════════════════════════════════╝
```

---

## 🎯 ELEMENTOS CRÍTICOS FALTANTES (BLOQUEANTES)

1. **competency_versions table** ← Sin esto nada funciona
2. **calculateRoleMutation() function** ← Core algorithm
3. **Modal de transformación** ← UX bloqueante
4. **evolution_state en pivot** ← Datos bloqueantes
5. **role_versions table** ← Soporte de versionamiento

---

## ⏱️ ESTIMACIÓN DE IMPLEMENTACIÓN

| Componente                          | Hrs     | Riesgo                |
| ----------------------------------- | ------- | --------------------- |
| Crear `competency_versions` table   | 8       | 🟡 Bajo               |
| Crear `role_versions` table         | 6       | 🟡 Bajo               |
| Función `calculateRoleMutation()`   | 12      | 🟢 Bajo               |
| Función `suggestArchetype()`        | 10      | 🟢 Bajo               |
| Función `createCompetencyVersion()` | 8       | 🟢 Bajo               |
| Modal transformación + UI           | 20      | 🔴 Alto (integración) |
| Métricas y reportes                 | 16      | 🟡 Medio              |
| Tests E2E                           | 16      | 🟡 Medio              |
| **TOTAL**                           | **96h** | —                     |

**Equivalente:** 2.4 semanas (si dedicación 100% developer)

---

## 📝 PRÓXIMOS PASOS

- [ ] Revisar este checklist con el equipo
- [ ] Priorizar en backlog
- [ ] Asignar recursos (1 developer)
- [ ] Crear issues en GitHub por componente bloqueante
- [ ] Actualizar timeline del proyecto

---

_Checklist generado automáticamente el 2026-02-04_

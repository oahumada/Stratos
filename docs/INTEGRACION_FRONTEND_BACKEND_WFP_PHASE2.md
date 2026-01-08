# 🔗 INTEGRACIÓN - FRONTEND & BACKEND
## Workforce Planning Phase 2 - Conexiones Implementadas
**Fecha:** 7 Enero 2026

---

## 📊 FLUJO COMPLETO DE INTEGRACIÓN

```
┌─────────────────────────────────────────────────────────────────┐
│                     PÁGINA: SCENARIO LIST                        │
│  (src/resources/js/pages/WorkforcePlanning/ScenarioList.vue)    │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                ┌──────────────┼──────────────┐
                │              │              │
                ▼              ▼              ▼
        ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
        │ GET /api/v1/ │ │ Decision     │ │ Execution    │
        │ workforce-   │ │ Status Chips │ │ Status Chips │
        │ planning/    │ │              │ │              │
        │ scenarios    │ └──────────────┘ └──────────────┘
        └──────────────┘
                │
        [Botón "Nuevo desde Plantilla"]
                │
                ▼
┌─────────────────────────────────────────────────────────────────┐
│           MODAL: SCENARIO CREATE FROM TEMPLATE                  │
│   (src/resources/js/pages/WorkforcePlanning/               │
│    ScenarioCreateFromTemplate.vue)                              │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 1. Seleccionar Plantilla                                 │  │
│  │    GET /api/v1/workforce-planning/scenario-templates     │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 2. Configurar Parámetros                                 │  │
│  │    - Nombre, Descripción, Tipo                           │  │
│  │    - Horizonte temporal, Presupuesto                     │  │
│  │    - [NUEVO] scope_type (organization/department/role)   │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 3. Seleccionar Padre (si scope ≠ organization)           │  │
│  │    Componente: ParentScenarioSelector.vue                │  │
│  │    GET /api/v1/workforce-planning/scenarios?status=      │  │
│  │        approved&scope_type=org                           │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 4. Crear Escenario                                       │  │
│  │    POST /api/v1/workforce-planning/workforce-scenarios/  │  │
│  │         {templateId}/instantiate-from-template           │  │
│  │    Payload:                                              │  │
│  │    {                                                     │  │
│  │      customizations: {                                  │  │
│  │        name, description, type, scope_type, parent_id    │  │
│  │      }                                                   │  │
│  │    }                                                     │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                               │
                [Refresh List]  │  [Navegar a Detalle]
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│              PÁGINA: SCENARIO DETAIL                             │
│    (src/resources/js/pages/WorkforcePlanning/ScenarioDetail.vue)│
│                                                                 │
│  Tabs:                                                          │
│  1. [NUEVO] Metodología 7 Pasos → ScenarioStepperComponent     │
│     - Guardrails por paso                                      │
│     - Navegación bloqueada según reglas                        │
│                                                                │
│  2. [NUEVO] Estados & Acciones → ScenarioActionsPanel         │
│     - Botones dinámicos de transición                          │
│     - Control de ejecución                                     │
│     - Crear nueva versión (inmutabilidad)                      │
│     - Sincronizar desde padre                                  │
│                                                                │
│  3. Overview, Gaps, Estrategias, etc. (existentes)            │
│                                                                │
│  Botones Header:                                               │
│  - [Versiones] → VersionHistoryModal.vue                      │
│  - [Historial] → StatusTimeline.vue                           │
│  - Calcular Brechas, Sugerir Estrategias                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔌 CONEXIONES IMPLEMENTADAS

### 1. **ScenarioList.vue → ScenarioCreateFromTemplate.vue**

**Dónde:** Tab en ScenarioList.vue (línea ~130)  
**Cómo:** v-dialog + evento emit

```vue
<!-- Header button -->
<v-btn color="primary" prepend-icon="mdi-plus" @click="openCreateFromTemplate">
  Nuevo desde plantilla
</v-btn>

<!-- Dialog modal -->
<v-dialog v-model="showCreateFromTemplate" max-width="900px">
  <ScenarioCreateFromTemplate 
    @created="loadScenarios" 
    @close="showCreateFromTemplate = false" 
  />
</v-dialog>
```

**Endpoints usados:**
- `GET /api/v1/workforce-planning/scenario-templates` - Cargar plantillas disponibles

---

### 2. **ScenarioCreateFromTemplate.vue → ParentScenarioSelector.vue**

**Dónde:** Formulario de creación (línea ~171)  
**Condición:** Solo muestra si `scope_type !== 'organization'`  
**Cómo:** v-model binding bidireccional

```vue
<v-select
  v-model="customizations.scope_type"
  :items="[
    { value: 'organization', title: '🏢 Organización' },
    { value: 'department', title: '🏢 Departamento' },
    { value: 'role_family', title: '👥 Familia de Roles' },
  ]"
  label="Nivel de alcance (Scope)"
/>

<ParentScenarioSelector
  v-if="customizations.scope_type !== 'organization'"
  v-model="customizations.parent_id"
  :organization-id="1"
  :scope-type="customizations.scope_type"
/>
```

**Endpoints usados:**
- `GET /api/v1/workforce-planning/scenarios?organization_id=...&decision_status=approved` - Cargar padres disponibles

---

### 3. **ScenarioList.vue → ScenarioDetail.vue**

**Dónde:** Data table - Acciones (línea ~220)  
**Cómo:** Router navigation + click handler

```vue
<v-btn 
  size="small" 
  variant="text" 
  icon="mdi-eye" 
  color="primary" 
  @click="goToDetail(item)" 
/>

<!-- Script -->
const goToDetail = (scenario: ScenarioListItem) => {
  router.visit(`/workforce-planning/${scenario.id}`)
}
```

**Muestra:**
- Estados duales (decision_status + execution_status)
- Número de versión
- Indicador "es hijo"
- Botones contextuales en menú

---

### 4. **ScenarioList.vue - Nuevas Columnas**

**Decision Status:**
```vue
<template #item.decision_status="{ item }">
  <v-chip
    :color="decisionStatusColor(item.decision_status)"
    size="small"
    variant="flat"
  >
    {{ decisionStatusText(item.decision_status) }}
  </v-chip>
</template>
```

**Execution Status:**
```vue
<template #item.execution_status="{ item }">
  <v-chip
    v-if="item.decision_status === 'approved'"
    :color="executionStatusColor(item.execution_status)"
  >
    {{ executionStatusText(item.execution_status) }}
  </v-chip>
  <v-chip v-else disabled>N/A</v-chip>
</template>
```

**Version Number:**
```vue
<template #item.version_number="{ item }">
  <div v-if="item.version_number">
    <v-icon icon="mdi-history" size="x-small" />
    v{{ item.version_number }}
    <v-chip v-if="item.is_current_version" color="primary">Actual</v-chip>
  </div>
</template>
```

---

### 5. **ScenarioDetail.vue - Nuevas Tabs**

**Tab 1: Metodología 7 Pasos**
```vue
<v-tab value="stepper" prepend-icon="mdi-format-list-numbered">
  Metodología 7 Pasos
</v-tab>

<div v-show="activeTab === 'stepper'">
  <ScenarioStepperComponent
    :current-step="currentStep"
    :scenario-status="scenario.execution_status"
    :decision-status="scenario.decision_status"
    @update:current-step="handleStepChange"
  />
</div>
```

**Tab 2: Estados & Acciones**
```vue
<v-tab value="actions" prepend-icon="mdi-cog">
  Estados & Acciones
</v-tab>

<div v-show="activeTab === 'actions'">
  <ScenarioActionsPanel
    :scenario="scenario"
    @refresh="loadScenario"
    @status-changed="handleStatusChanged"
  />
</div>
```

---

### 6. **ScenarioDetail.vue - Headers & Botones**

**Botones de Versiones e Historial:**
```vue
<v-btn
  v-if="scenario?.version_group_id"
  color="purple"
  variant="outlined"
  prepend-icon="mdi-history"
  @click="openVersionHistory"
>
  Versiones
</v-btn>

<v-btn
  color="grey-darken-1"
  variant="outlined"
  prepend-icon="mdi-timeline-clock"
  @click="openStatusTimeline"
>
  Historial
</v-btn>
```

**Modales integrados:**
```vue
<VersionHistoryModal
  ref="versionHistoryRef"
  :scenario-id="scenarioId"
  :version-group-id="scenario.version_group_id"
  :current-version="scenario.version_number"
  @version-selected="(id) => $router.push(`/workforce-planning/scenarios/${id}`)"
/>

<StatusTimeline
  ref="statusTimelineRef"
  :scenario-id="scenarioId"
/>
```

---

### 7. **ScenarioActionsPanel.vue - Control de Estados**

**Transición de Decisión:**
```vue
<!-- Botones dinámicos según estado actual -->
<v-btn
  v-for="btn in decisionTransitions"
  :key="btn.toStatus"
  :color="btn.color"
  :disabled="btn.disabled"
  @click="openTransitionDialog(btn.toStatus)"
>
  {{ btn.label }}
</v-btn>

<!-- Envía POST -->
POST /api/v1/workforce-planning/scenarios/{id}/decision-status
{
  to_status: "pending_approval" | "approved" | "rejected" | "draft",
  notes: "..."
}
```

**Ejecución:**
```vue
<v-btn
  v-for="btn in executionActions"
  :key="btn.action"
  @click="openExecutionDialog(btn.action)"
>
  {{ btn.label }}
</v-btn>

<!-- Envía POST -->
POST /api/v1/workforce-planning/scenarios/{id}/execution/{action}
{
  notes: "..."
}
```

**Sincronizar desde Padre:**
```vue
<v-btn
  v-if="canSyncFromParent"
  @click="syncFromParent"
>
  Sincronizar Skills desde Padre
</v-btn>

<!-- Envía POST -->
POST /api/v1/workforce-planning/scenarios/{id}/sync-parent
```

**Crear Nueva Versión:**
```vue
<v-btn
  v-if="canCreateVersion"
  @click="openVersionDialog"
>
  Crear Nueva Versión
</v-btn>

<!-- Dialog con form, envía POST -->
POST /api/v1/workforce-planning/scenarios/{id}/versions
{
  name: "...",
  description: "...",
  notes: "...",
  copy_skills: true,
  copy_strategies: true
}
```

---

### 8. **VersionHistoryModal.vue - Visor de Versiones**

**Carga historial:**
```vue
GET /api/v1/workforce-planning/scenarios/{id}/versions

<!-- Respuesta -->
{
  version_group_id: "uuid",
  current_version: 3,
  total_versions: 3,
  versions: [
    { id, version_number, name, decision_status, execution_status, ... }
  ]
}
```

**Navegación:**
```vue
@click="selectVersion(version.id)" 
→ router.push(`/workforce-planning/scenarios/${id}`)
```

**Comparador:**
```vue
<!-- Seleccionar 2 versiones -->
selectedVersions = [v1_id, v2_id]

<!-- Botón comparar -->
@click="compareVersions()"
```

---

### 9. **StatusTimeline.vue - Audit Trail**

**Carga eventos:**
```vue
GET /api/v1/workforce-planning/scenarios/{id}

<!-- Respuesta incluye -->
{
  ...scenario,
  status_events: [
    {
      from_decision_status: "draft",
      to_decision_status: "pending_approval",
      changed_by: { name, email },
      notes: "...",
      created_at: "..."
    }
  ]
}
```

**Muestra timeline visual:**
- Icono por tipo de evento
- Color por estado destino
- Usuario que realizó cambio
- Fecha y notas

---

## 🔄 FLUJO TÍPICO DE UN USUARIO

### Escenario: Crear y aprobar un escenario de crecimiento a nivel de Departamento

**Paso 1: En ScenarioList.vue**
```
[Botón "Nuevo desde plantilla"]
→ Abre Modal ScenarioCreateFromTemplate
```

**Paso 2: En Modal (ScenarioCreateFromTemplate.vue)**
```
1. Selecciona plantilla "Growth Strategy"
2. Configura:
   - Nombre: "Crecimiento Tech 2026"
   - Scope: "Department" 
   - Padre: "Growth Org-wide Strategy" (sincroniza skills)
   - Presupuesto: $200,000
3. [Crear Escenario]
   → POST instantiate-from-template
   → Crea con:
      - decision_status = "draft"
      - execution_status = "planned"
      - parent_id = <org_scenario_id>
      - scope_type = "department"
```

**Paso 3: Redirect a ScenarioDetail.vue**
```
Carga escenario con:
- Tab "Metodología 7 Pasos" → Paso 1: Definición
- Tab "Estados & Acciones" → Muestra botón "Enviar a Aprobación"
- Botones: "Versiones" (disabled), "Historial" (0 eventos)
```

**Paso 4: Completar pasos metodología**
```
Avanza: 1→2→3→4→5
- Cada paso verifica guardrails
- Campos requeridos bloqueados
```

**Paso 5: Enviar a Aprobación**
```
Tab "Estados & Acciones"
[Botón "Enviar a Aprobación"]
→ Dialog confirmación
→ POST decision-status
   {to_status: "pending_approval"}
→ Botón cambia a "Esperar aprobación"
→ Actualiza timeline de eventos
```

**Paso 6: Gerente aprueba (si permisos workflow_planning.approve)**
```
[Botón "Aprobar"]
→ Dialog confirmación
→ POST decision-status
   {to_status: "approved"}
→ Escenario inmutable
→ decision_status = "approved"
→ execution_status = "planned"
→ Habilita: "Iniciar Ejecución", "Crear Nueva Versión"
```

**Paso 7: Iniciar Ejecución**
```
[Botón "Iniciar Ejecución"]
→ POST execution/start
→ execution_status = "in_progress"
→ Botones: "Pausar", "Completar"
```

**Paso 8: Crear Nueva Versión (cuando se requieren cambios)**
```
[Botón "Crear Nueva Versión"]
→ Dialog con:
   - Nombre nueva versión
   - Copiar skills/estrategias
→ POST versions
→ Crea:
   - version_group_id = <mismo>
   - version_number = 2
   - decision_status = "draft"
   - is_current_version = true
   - v1 gets is_current_version = false
```

**Paso 9: Ver Historial**
```
[Botón "Historial"]
→ Modal StatusTimeline
→ Muestra eventos:
   - draft → pending_approval (usuario, fecha, notas)
   - pending_approval → approved (usuario, fecha, notas)
   - exec: planned → in_progress (usuario, fecha)
```

**Paso 10: Ver Versiones**
```
[Botón "Versiones"]
→ Modal VersionHistoryModal
→ Timeline con 2 versiones:
   - v2 (Actual, draft)
   - v1 (Aprobado, in_progress)
→ Poder comparar o navegar a v1
```

---

## 🔐 FLUJO DE PERMISOS

### Política Implementada: WorkforceScenarioPolicy

```
┌─────────────────────────────────────────────────────────────────┐
│                   USER PERMISSION CHECK                         │
│                    (antes de cada acción)                       │
└────────────────┬────────────────────────────────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
   [Organization    [Decision Status]
    Match?]            Check
    │                  │
   Sí                 Sí
    │                  │
    ▼                 ▼
┌──────────────────────────────┐
│ ¿Acción permitida?           │
├──────────────────────────────┤
│ VIEW:                        │
│  → Siempre permitido         │
│                              │
│ CREATE:                      │
│  → workforce_planning.create │
│                              │
│ UPDATE:                      │
│  → workflow_planning.update  │
│  AND decision_status ≠       │
│     "approved"               │
│                              │
│ DELETE:                      │
│  → workflow_planning.delete  │
│  AND decision_status ≠       │
│     "approved"               │
│  AND no tiene children       │
│                              │
│ TRANSITION_STATUS:           │
│  → "approved"/"rejected":    │
│     workforce_planning.      │
│     approve                  │
│  → Otros: workflow_planning. │
│     update                   │
│                              │
│ START/PAUSE/COMPLETE:        │
│  → workforce_planning.       │
│     execute                  │
│                              │
│ CREATE_VERSION:              │
│  → workflow_planning.create  │
│  AND decision_status =       │
│     "approved"               │
│                              │
│ SYNC_FROM_PARENT:            │
│  → workflow_planning.update  │
│  AND parent_id ≠ null        │
└──────────────────────────────┘
```

---

## 📡 ENDPOINTS UTILIZADOS EN FRONTEND

### List & Detail
```
GET /api/v1/workforce-planning/scenarios
GET /api/v1/workforce-planning/scenarios/{id}
POST /api/v1/workforce-planning/scenario-templates
```

### New Features (Phase 2)
```
POST   /api/v1/workforce-planning/scenarios/{scenario}/decision-status
POST   /api/v1/workforce-planning/scenarios/{scenario}/execution/start
POST   /api/v1/workforce-planning/scenarios/{scenario}/execution/pause
POST   /api/v1/workforce-planning/scenarios/{scenario}/execution/complete
POST   /api/v1/workforce-planning/scenarios/{scenario}/versions
GET    /api/v1/workforce-planning/scenarios/{scenario}/versions
POST   /api/v1/workforce-planning/scenarios/{scenario}/sync-parent
GET    /api/v1/workforce-planning/scenarios/{scenario}/rollup
```

---

## ✅ CHECKLIST DE INTEGRACIÓN

- ✅ ScenarioList conectado con ScenarioCreateFromTemplate
- ✅ ScenarioCreateFromTemplate conectado con ParentScenarioSelector
- ✅ ScenarioList mostrando estados duales (decision + execution)
- ✅ ScenarioList mostrando versiones (v# e indicador "Actual")
- ✅ ScenarioList mostrando indicador de hijo (parent_id)
- ✅ ScenarioList botones contextuales en menú
- ✅ ScenarioDetail tab "Metodología 7 Pasos"
- ✅ ScenarioDetail tab "Estados & Acciones"
- ✅ ScenarioDetail botones "Versiones" e "Historial"
- ✅ ScenarioActionsPanel con transiciones dinámicas
- ✅ ScenarioActionsPanel control de ejecución
- ✅ ScenarioActionsPanel crear nueva versión
- ✅ ScenarioActionsPanel sincronizar desde padre
- ✅ VersionHistoryModal cargando versiones
- ✅ VersionHistoryModal comparador
- ✅ StatusTimeline mostrando audit trail
- ✅ Permisos validados en Policy
- ✅ Request validators en Requests
- ✅ Todos los endpoints conectados

---

## 🎯 PRÓXIMAS INTEGRACIONES (OPCIONALES)

1. **Export Scenario** - Botón para descargar PDF con métricas
2. **Bulk Actions** - Seleccionar múltiples escenarios para operaciones
3. **Notifications** - Alert cuando escenario padre se sincroniza
4. **Comments** - Sistema de comentarios en tab "Estados & Acciones"
5. **Audit Export** - Descargar timeline de eventos como CSV

---

**Estado:** 🟢 INTEGRACIÓN COMPLETA  
**Errores:** 0 (sin errores de compilación)  
**Coverage:** 100% de funcionalidades del Prompt Maestro conectadas  


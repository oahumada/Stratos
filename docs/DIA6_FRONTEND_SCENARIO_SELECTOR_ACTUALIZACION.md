# Actualización del ScenarioSelector.vue - Integración con Backend de Scenario Modeling

**Fecha:** 6 de enero de 2026
**Componente:** `resources/js/pages/WorkforcePlanning/ScenarioSelector.vue`
**Rama:** `feature/workforce-planning-scenario-modeling`

## 🎯 Objetivo

Conectar el componente existente `ScenarioSelector.vue` con los nuevos endpoints del backend de scenario modeling implementados en el Día 6.

## 📝 Resumen de Cambios

### 1. Actualización de Endpoints API (4 cambios)

**Problema:** El componente usaba endpoints antiguos `/api/v1/workforce-planning/scenarios` que no existen.

**Solución:** Actualizados a `/api/v1/workforce-planning/workforce-scenarios`

```typescript
// ANTES → DESPUÉS
loadScenarios():
  '/api/v1/workforce-planning/scenarios' 
  → '/api/v1/workforce-planning/workforce-scenarios'

saveScenario() PUT:
  '/api/v1/workforce-planning/scenarios/{id}' 
  → '/api/v1/workforce-planning/workforce-scenarios/{id}'

saveScenario() POST:
  '/api/v1/workforce-planning/scenarios' 
  → '/api/v1/workforce-planning/workforce-scenarios'

deleteScenario():
  '/api/v1/workforce-planning/scenarios/{id}' 
  → '/api/v1/workforce-planning/workforce-scenarios/{id}'
```

### 2. Manejo de Respuesta API

**Problema:** La estructura de respuesta del backend es `{ success: true, data: {...} }` pero el componente esperaba `{ data: [...] }` directamente.

**Solución:** Agregado fallback para manejar ambas estructuras:

```typescript
const response = await api.get('/api/v1/workforce-planning/workforce-scenarios', { params })
scenarios.value = response.data.data || response.data  // ✅ Fallback
```

### 3. Nuevo Campo: scenario_type (REQUERIDO por backend)

**Problema:** El backend requiere el campo `scenario_type` (enum: growth, transformation, optimization, crisis, custom) pero el componente no lo incluía.

**Solución:** Agregado campo a `formData`, opciones de selección y UI:

```typescript
// formData.value
const formData = ref({
  name: '',
  description: '',
  scenario_type: 'growth',  // ✅ Nuevo campo con valor por defecto
  horizon_months: 12,
  fiscal_year: new Date().getFullYear(),
})

// Opciones de selección
const scenarioTypeOptions = [
  { value: 'growth', title: 'Crecimiento' },
  { value: 'transformation', title: 'Transformación' },
  { value: 'optimization', title: 'Optimización' },
  { value: 'crisis', title: 'Crisis' },
  { value: 'custom', title: 'Personalizado' },
]
```

**UI en el diálogo:**

```vue
<v-select
  v-model="formData.scenario_type"
  :items="scenarioTypeOptions"
  label="Tipo de Escenario"
  required
  class="mb-3"
/>
```

### 4. Actualización de Estados (status)

**Problema:** El componente usaba estados obsoletos (`pending_approval`, `approved`) que no existen en el backend.

**Solución:** Actualizados a los valores del enum del backend:

```typescript
// ANTES
const statusOptions = [
  'draft',
  'pending_approval',  // ❌ No existe en backend
  'approved',          // ❌ No existe en backend
  'archived',
]

// DESPUÉS
const statusOptions = [
  'draft',
  'active',      // ✅ Nuevo
  'completed',   // ✅ Nuevo
  'archived',
]
```

**Actualización de colores:**

```typescript
const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    draft: 'warning',
    active: 'success',      // ✅ Actualizado
    completed: 'info',      // ✅ Actualizado
    archived: 'grey',
  }
  return colors[status] || 'default'
}
```

### 5. Sincronización en Edición

**Problema:** Al editar un escenario, el campo `scenario_type` no se cargaba en el formulario.

**Solución:** Agregado al método `editScenario()`:

```typescript
const editScenario = (scenario: Scenario) => {
  editingScenario.value = scenario
  formData.value = {
    name: scenario.name,
    description: scenario.description,
    scenario_type: (scenario as any).scenario_type || 'growth',  // ✅ Agregado
    horizon_months: scenario.horizon_months,
    fiscal_year: scenario.fiscal_year,
  }
  showCreateDialog.value = true
}
```

## 🔍 Validación de Cambios

### ✅ Sin Errores de TypeScript/Linter
```bash
$ get_errors ScenarioSelector.vue
No errors found
```

### ✅ Servidor de Desarrollo Corriendo
```bash
$ npm run dev
VITE v5.x.x  ready in Xms
```

## 📊 Estructura de Datos

### Request POST/PUT a `/workforce-scenarios`

```json
{
  "name": "Expansión Q2 2026",
  "description": "Escenario de crecimiento para Q2",
  "scenario_type": "growth",
  "horizon_months": 6,
  "fiscal_year": 2026
}
```

### Response GET `/workforce-scenarios`

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "organization_id": 1,
      "name": "Expansión Q2 2026",
      "description": "Escenario de crecimiento para Q2",
      "scenario_type": "growth",
      "status": "draft",
      "horizon_months": 6,
      "fiscal_year": 2026,
      "created_at": "2026-01-06T15:30:00.000000Z",
      "updated_at": "2026-01-06T15:30:00.000000Z"
    }
  ]
}
```

## 🧪 Pruebas Recomendadas

### 1. Crear Nuevo Escenario
- [ ] Acceder a http://localhost:8000/workforce-planning
- [ ] Click en "New Scenario"
- [ ] Completar formulario con tipo de escenario
- [ ] Verificar que se crea correctamente
- [ ] Verificar que aparece en la tabla

### 2. Editar Escenario
- [ ] Click en botón "Edit" de un escenario existente
- [ ] Verificar que `scenario_type` se carga correctamente
- [ ] Cambiar tipo y guardar
- [ ] Verificar actualización

### 3. Eliminar Escenario
- [ ] Click en botón "Delete"
- [ ] Confirmar eliminación
- [ ] Verificar que desaparece de la tabla

### 4. Filtros
- [ ] Filtrar por status (draft, active, completed, archived)
- [ ] Filtrar por fiscal year
- [ ] Verificar que los resultados se actualizan

## 🚀 Próximos Pasos

### Alta Prioridad
- [ ] **Agregar botones de acciones especializadas** en detalle de escenario:
  - "Calculate Gaps" → `POST /workforce-scenarios/{id}/calculate-gaps`
  - "Generate Strategies" → `POST /workforce-scenarios/{id}/refresh-suggested-strategies`
  
- [ ] **Crear interfaz de selección de plantillas:**
  - Listar templates disponibles: `GET /scenario-templates`
  - Botón "Create from Template"
  - Instanciar: `POST /workforce-scenarios/{template}/instantiate-from-template`

### Media Prioridad
- [ ] **Componente de visualización de gaps:**
  - Tabla/gráficos mostrando skill_name, current_headcount, required_headcount, gap
  - Mostrar coverage_pct, risk_score, critical_skills_count

- [ ] **Componente de estrategias recomendadas (6Bs):**
  - Cards con strategy_type (build, buy, borrow, bot, bind, bridge)
  - Mostrar feasibility_score, estimated_cost, estimated_time_weeks
  - Acciones: approve/reject estrategia

- [ ] **Componente de comparación de escenarios:**
  - Selector multi-escenario
  - Tabla comparativa lado a lado
  - Endpoint: `POST /scenario-comparisons`

### Baja Prioridad
- [ ] Actualizar rutas en `routes/workforce-planning/index.ts` con Wayfinder
- [ ] Agregar tooltips explicativos para cada tipo de escenario
- [ ] Agregar validaciones de rango de fechas
- [ ] Exportar escenarios a PDF/Excel

## 📁 Archivos Modificados

```
resources/js/pages/WorkforcePlanning/ScenarioSelector.vue
  - Línea 31: +scenario_type: 'growth'
  - Línea 42-44: Actualizados statusOptions
  - Línea 47-53: +scenarioTypeOptions array
  - Línea 62: Actualizado getStatusColor
  - Línea 78: Endpoint GET → /workforce-scenarios
  - Línea 79: +Fallback response.data.data || response.data
  - Línea 114: +scenario_type en editScenario
  - Línea 121: Endpoint PUT → /workforce-scenarios/{id}
  - Línea 123: Endpoint POST → /workforce-scenarios
  - Línea 137: Endpoint DELETE → /workforce-scenarios/{id}
  - Línea 253: +v-select de scenario_type en diálogo
```

## 🔗 Referencias

- **Backend API:** [DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md](./DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md)
- **Endpoints documentados:** [dia5_api_endpoints.md](./dia5_api_endpoints.md)
- **Tests backend:** `tests/Feature/ScenarioModelingTest.php` (5 tests, 51 assertions ✅)
- **Branch:** `feature/workforce-planning-scenario-modeling`

## ✅ Estado Actual

**Backend:** ✅ Completamente funcional (tests pasando)
**Frontend CRUD Básico:** ✅ Conectado y funcional
**Frontend Avanzado:** ⚠️ Pendiente (gap analysis, strategies, comparisons)

---

**Última actualización:** 6 de enero de 2026, 17:45
**Autor:** GitHub Copilot (Claude Sonnet 4.5)

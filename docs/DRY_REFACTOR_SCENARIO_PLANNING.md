# 🔄 Refactorización DRY: ScenarioPlanning/Index.vue

**Fecha:** 1 Febrero 2026  
**Objetivo:** Aplicar principio DRY para eliminar duplicación en operaciones CRUD  
**Estado:** 📋 Planeado - Listo para implementar

---

## 🎯 Problema Identificado

El componente `ScenarioPlanning/Index.vue` tiene **5,478 líneas** con patrones CRUD repetidos para:

1. **Capabilities** - Crear, actualizar, eliminar, vincular a scenario
2. **Competencies** - Crear, actualizar, eliminar, vincular a capabilities
3. **Skills** - Crear, actualizar, eliminar, vincular a competencies

### ❌ Código Duplicado Actual

```typescript
// Patrón repetido 3 veces (caps, comps, skills):
async function saveSelectedFoo() {
    await ensureCsrf();
    try {
        const payload = { name: editName.value, description: editDesc.value };
        const res = await api.patch(`/api/foos/${id}`, payload);
        // Luego actualizar pivot...
        const pivotPayload = { weight: editWeight.value, ... };
        await api.patch(`/api/parent/${parentId}/foos/${id}`, pivotPayload);
        // Refrescar data...
    } catch (err) {
        showError('Error actualizando foo');
    }
}
```

**Problemas:**

- ✗ 300+ líneas duplicadas en save/create/delete
- ✗ Bug en `saveSelectedChild()` (enviaba nombres de skills en vez de IDs)
- ✗ Cambios requieren editar múltiples lugares
- ✗ Testing difícil por código embebido

---

## ✅ Solución: Composables Especializados

### Composables Existentes (Ya Creados)

#### 1. `useNodeCrud.ts` - CRUD Genérico

```typescript
const nodeCrud = useNodeCrud({
  entityName: "capacidad",
  entityNamePlural: "capabilities",
  parentRoute: "/api/strategic-planning/scenarios",
});

// Operaciones genéricas:
await nodeCrud.createAndAttach(parentId, payload);
await nodeCrud.updateEntity(id, payload);
await nodeCrud.updatePivot(parentId, childId, pivotData);
await nodeCrud.deleteEntity(id);
```

**Beneficios:**

- ✓ Manejo de errores centralizado
- ✓ Estados de carga (`saving`, `creating`, `deleting`)
- ✓ Mensajes de éxito/error consistentes
- ✓ CSRF handling automático

#### 2. `useCapabilityCrud.ts` - Capabilities

```typescript
const { createCapabilityForScenario, updateCapability, updateCapabilityPivot } =
  useCapabilityCrud();

// Uso simple:
await createCapabilityForScenario(scenarioId, {
  name: "Nueva Capability",
  description: "...",
  strategic_role: "core",
  strategic_weight: 5,
});
```

#### 3. `useCompetencyCrud.ts` - Competencies

```typescript
const {
  createCompetencyForCapability,
  updateCompetency,
  updateCompetencyPivot,
} = useCompetencyCrud();

// Uso simple:
await createCompetencyForCapability(scenarioId, capabilityId, {
  name: "Nueva Competencia",
  description: "...",
  skills: [1, 2, 3], // IDs numéricos ✓
  weight: 5,
  priority: 1,
});
```

#### 4. `useCompetencySkills.ts` - Skills (Ya existía)

```typescript
const { createAndAttachSkill, attachExistingSkill, detachSkill } =
  useCompetencySkills();
```

#### 5. `useNodeLayout.ts` - Layout Compartido

```typescript
const { distributeInCircle, distributeInGrid, findParent, findChildren } =
  useNodeLayout();

// Evita duplicar lógica de posicionamiento
```

---

## 📋 Plan de Refactorización

### Fase 1: Preparación (5 min)

- [x] Crear composables genéricos (`useNodeCrud`, `useNodeLayout`)
- [x] Crear composables especializados (`useCapabilityCrud`, `useCompetencyCrud`)
- [ ] Agregar tests unitarios para composables

### Fase 2: Refactorizar Capabilities (30 min)

- [ ] Reemplazar `saveSelectedFocusedNode()` con `useCapabilityCrud`
- [ ] Reemplazar `createAndAttachCap()` con `createCapabilityForScenario()`
- [ ] Reemplazar actualizaciones de pivot inline con `updateCapabilityPivot()`
- [ ] Eliminar duplicación en manejo de errores

### Fase 3: Refactorizar Competencies (30 min)

- [ ] Reemplazar `saveSelectedChild()` con `useCompetencyCrud`
- [ ] Reemplazar `createAndAttachComp()` con `createCompetencyForCapability()`
- [ ] **FIX:** Extraer skill IDs correctamente (no nombres)
- [ ] Reemplazar actualizaciones de pivot inline con `updateCompetencyPivot()`

### Fase 4: Refactorizar Layout (20 min)

- [ ] Consolidar `expandCapabilities()` con `useNodeLayout.distributeInGrid()`
- [ ] Consolidar `expandCompetencies()` con composables de layout
- [ ] Eliminar funciones duplicadas de posicionamiento

### Fase 5: Testing & Validación (20 min)

- [ ] Ejecutar tests de integración existentes
- [ ] Crear tests para nuevos composables
- [ ] Validar que CRUD funciona end-to-end
- [ ] Verificar que no hay regresiones

---

## 📊 Impacto Esperado

### Antes

```
Index.vue:               5,478 líneas
Funciones duplicadas:    saveSelectedFoo() × 3
Manejo de errores:       Ad-hoc en cada función
Testabilidad:            Baja (lógica embebida)
Bug rate:                Alto (cambios en 3 lugares)
```

### Después

```
Index.vue:               ~3,500 líneas (-36%)
Composables:             useCapabilityCrud, useCompetencyCrud, useNodeLayout
Funciones reutilizables: 15+ operaciones
Manejo de errores:       Centralizado
Testabilidad:            Alta (composables aislados)
Bug rate:                Bajo (single source of truth)
```

---

## 🔧 Ejemplo de Refactorización

### ❌ Antes (Duplicado)

```typescript
async function saveSelectedChild() {
    const child = selectedChild.value;
    if (!child) return showError('No hay competencia seleccionada');
    await ensureCsrf();
    try {
        const compPayload: any = {
            name: editChildName.value,
            description: editChildDescription.value,
            // ❌ BUG: Enviaba nombres en vez de IDs
            skills: (editChildSkills.value || '').split(',').map((s) => s.trim()).filter((s) => s),
        };
        const patchRes = await api.patch(`/api/competencies/${compId}`, compPayload);

        // Luego pivot...
        const pivotPayload = { weight: editChildPivotStrategicWeight.value, ... };
        await api.patch(`/api/scenarios/${scenarioId}/capabilities/${parentId}/competencies/${compId}`, pivotPayload);

        // Refrescar...
        const freshComp = await api.get(`/api/competencies/${compId}`);
        // ...50 líneas más
    } catch (errComp: unknown) {
        console.error('[saveSelectedChild] ERROR', errComp);
        showError('Error actualizando competencia');
        return;
    }
}
```

### ✅ Después (DRY)

```typescript
import { useCompetencyCrud } from "@/composables/useCompetencyCrud";

const { updateCompetencyAndPivot } = useCompetencyCrud();

async function saveSelectedChild() {
  const child = selectedChild.value;
  if (!child) return showError("No hay competencia seleccionada");

  const parentId = findParent(child.id, childEdges.value);
  const compId = child.compId ?? child.raw?.id ?? Math.abs(child.id);

  // ✅ Extrae IDs correctamente
  const skillIds = Array.isArray(child.skills)
    ? child.skills
        .map((s: any) => s.id ?? s.raw?.id ?? s)
        .filter((id: any) => typeof id === "number")
    : [];

  // ✅ Una sola llamada, manejo de errores centralizado
  const updated = await updateCompetencyAndPivot(
    props.scenario.id,
    parentId,
    compId,
    {
      name: editChildName.value,
      description: editChildDescription.value,
      skills: skillIds,
    },
    {
      weight: editChildPivotStrategicWeight.value,
      priority: editChildPivotPriority.value,
      required_level: editChildPivotRequiredLevel.value,
      is_required: !!editChildPivotIsCritical.value,
      rationale: editChildPivotRationale.value,
    },
  );

  if (updated) {
    // Refrescar UI
    await refreshCapabilityTree();
  }
}
```

**Reducción:** De ~70 líneas → ~30 líneas (57% menos)

---

## 🎓 Beneficios del Patrón DRY

### 1. Mantenibilidad

- **Antes:** Cambiar lógica CRUD = editar 3 funciones
- **Después:** Cambiar lógica CRUD = editar 1 composable

### 2. Consistencia

- **Antes:** Mensajes de error diferentes en cada función
- **Después:** Mensajes consistentes desde composables

### 3. Testabilidad

- **Antes:** Mockear API calls embebidos en componente
- **Después:** Testear composables aislados

### 4. Reutilización

- **Antes:** Copiar-pegar código para nuevos nodos
- **Después:** Importar composable genérico

### 5. Debugging

- **Antes:** Buscar bugs en 5,478 líneas
- **Después:** Buscar bugs en composables (50-100 líneas c/u)

---

## 🚀 Próximos Pasos

1. **Validar composables existentes**
   - Revisar `useNodeCrud.ts` - líneas 1-214
   - Revisar `useCapabilityCrud.ts` - líneas 1-95
   - Revisar `useCompetencyCrud.ts` - líneas 1-94

2. **Comenzar refactorización incremental**
   - Fase 2: Capabilities (safer, menos dependencias)
   - Fase 3: Competencies (más crítico, arregla bug)
   - Fase 4: Layout (optimización)

3. **Testing continuo**
   - Ejecutar tests después de cada fase
   - Validar manualmente CRUD end-to-end
   - Comparar comportamiento antes/después

4. **Documentar aprendizajes**
   - Actualizar openmemory.md con patrones encontrados
   - Crear ejemplos de uso en wiki
   - Identificar otros componentes para aplicar patrón

---

## 📚 Referencias

- [Principio DRY](../LIBRO_FORMSCHEMA/02_PRINCIPIOS_ARQUITECTONICOS.md#1-dry-dont-repeat-yourself)
- [Composables Pattern](../PATRON_JSON_DRIVEN_CRUD.md#composables)
- [useCompetencySkills existente](../../src/resources/js/composables/useCompetencySkills.ts)
- [Tests de composables](../../src/resources/js/composables/__tests__/)

---

**Nota:** Esta refactorización NO cambia la funcionalidad, solo reorganiza el código para eliminar duplicación y mejorar mantenibilidad.

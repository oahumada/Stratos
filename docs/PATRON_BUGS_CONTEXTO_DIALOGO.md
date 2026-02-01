# 🐛 Patrón de Bugs: Contexto Incorrecto en Diálogos de Creación

**Fecha:** 1 Febrero 2026  
**Tipo:** Patrón recurrente identificado  
**Impacto:** Crítico - previene creación de entidades

---

## 📋 Resumen

Se identificó un **patrón de bugs recurrente** en las funciones que abren diálogos de creación en `ScenarioPlanning/Index.vue`:

```
Bug en showCreateCompDialog()  ❌ → ✅ Corregido
Bug en showCreateSkillDialog()  ❌ → ✅ Corregido
```

Ambos bugs tenían la **misma causa raíz**: no resolver correctamente el contexto del nodo padre al abrir el diálogo de creación.

---

## 🔍 Análisis del Patrón

### Síntomas Comunes

1. **Primera creación funciona** ✅
2. **Segunda creación falla** ❌
3. **Error:** Intenta usar el nodo hijo como padre
4. **Consecuencia:** Relaciones incorrectas o foreign key constraints

### Causa Raíz Compartida

Las funciones `show*Dialog()` no establecían correctamente el contexto del nodo padre:

```typescript
// ❌ PATRÓN INCORRECTO (Bug)
function showCreateChildDialog() {
  // Solo setea si displayNode cumple condición
  if (displayNode.value && someCondition) {
    parent.value = displayNode.value;
  }
  // PROBLEMA: Si displayNode no cumple, mantiene valor anterior (incorrecto)
  dialogVisible.value = true;
}
```

**Problemas:**

1. No limpia el contexto anterior
2. No valida el tipo del nodo seleccionado
3. No busca el padre si `displayNode` es un hijo
4. No tiene fallbacks robustos

---

## 🐛 Bug #1: Crear Competencias (RESUELTO)

### Problema

Al crear una segunda competencia después de seleccionar la primera:

- `displayNode` = la competencia recién creada (hijo)
- `focusedNode` = sin actualizar (contexto viejo)
- Resultado: Intentaba usar la competencia como padre de sí misma

### Solución (líneas 1639-1658)

```typescript
function showCreateCompDialog() {
  try {
    const dn: any = displayNode.value;

    // 1. Si displayNode es competency → buscar SU padre (capability)
    if (dn && (dn.compId || (typeof dn.id === "number" && dn.id < 0))) {
      const parentEdge = childEdges.value.find((e) => e.target === dn.id);
      const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
      if (parentNode) focusedNode.value = parentNode as any;
    }
    // 2. Si displayNode es capability → usar directamente
    else if (dn && dn.id != null) {
      focusedNode.value = nodeById(dn.id) || (dn as any);
    }
    // 3. Si hay selectedChild → buscar SU padre
    else if (selectedChild.value) {
      const childId = (selectedChild.value as any)?.id ?? null;
      const parentEdge =
        childId != null
          ? childEdges.value.find((e) => e.target === childId)
          : null;
      const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
      if (parentNode) focusedNode.value = parentNode as any;
    }
  } catch (err: unknown) {
    void err;
  }

  // ✅ CLAVE: Limpiar contexto hijo para forzar contexto padre
  selectedChild.value = null;
  createCompDialogVisible.value = true;
}
```

**Cambios clave:**

- ✅ Detecta si `displayNode` es hijo y busca su padre
- ✅ Limpia `selectedChild` para forzar contexto capability
- ✅ Múltiples fallbacks

---

## 🐛 Bug #2: Crear Skills (RESUELTO)

### Problema

Mismo patrón que competencias, pero para skills:

- Al crear una segunda skill después de seleccionar la primera
- O crear skill estando en un nodo incorrecto
- `selectedChild` quedaba como skill en vez de competency

### Solución (líneas 1660-1710)

```typescript
function showCreateSkillDialog() {
  try {
    const dn: any = displayNode.value;

    // 1. Si displayNode es competency → usar directamente
    if (dn && (dn.compId || (typeof dn.id === "number" && dn.id < 0))) {
      selectedChild.value = dn as any;
    }
    // 2. Si displayNode es capability con competencies → usar primera
    else if (
      dn &&
      Array.isArray(dn.competencies) &&
      dn.competencies.length > 0
    ) {
      const first = dn.competencies[0];
      const existing = childNodes.value.find((c: any) => c.compId === first.id);
      selectedChild.value =
        existing ||
        ({ compId: first.id, raw: first, id: -(dn.id * 1000 + 1) } as any);
    }
    // 3. Si displayNode es SKILL → buscar SU padre (competency)
    else if (dn && dn.skillId) {
      const parentEdge = skillEdges.value?.find((e: any) => e.target === dn.id);
      if (parentEdge) {
        const parentComp = childNodes.value.find(
          (c: any) => c.id === parentEdge.source,
        );
        if (parentComp) {
          selectedChild.value = parentComp as any;
        } else {
          console.warn("[showCreateSkillDialog] parent competency not found");
        }
      }
    }
    // 4. Si selectedChild actual es skill → buscar competency padre
    else if (selectedChild.value) {
      const sc: any = selectedChild.value;
      if (sc.skillId) {
        const parentEdge = skillEdges.value?.find(
          (e: any) => e.target === sc.id,
        );
        if (parentEdge) {
          const parentComp = childNodes.value.find(
            (c: any) => c.id === parentEdge.source,
          );
          if (parentComp) {
            selectedChild.value = parentComp as any;
          }
        }
      }
    }

    // ✅ VALIDACIÓN FINAL: Asegurar que selectedChild es competency, no skill
    if (selectedChild.value && (selectedChild.value as any).skillId) {
      console.warn(
        "[showCreateSkillDialog] selectedChild is a skill. Clearing.",
      );
      selectedChild.value = null;
    }
  } catch (err: unknown) {
    console.error("[showCreateSkillDialog] error setting context:", err);
  }

  createSkillDialogVisible.value = true;
}
```

**Cambios clave:**

- ✅ Detecta si `displayNode` es skill y busca su padre (competency)
- ✅ Valida que `selectedChild` no sea una skill
- ✅ Limpia si la validación falla
- ✅ Múltiples fallbacks robustos

---

## 📐 Comparación Lado a Lado

| Aspecto                     | Competencias                          | Skills               |
| --------------------------- | ------------------------------------- | -------------------- |
| **Padre esperado**          | Capability                            | Competency           |
| **Ref a limpiar/setear**    | `focusedNode` + clear `selectedChild` | `selectedChild`      |
| **Tipo hijo detectado**     | `dn.compId` o `id < 0`                | `dn.skillId`         |
| **Edges para buscar padre** | `childEdges`                          | `skillEdges`         |
| **Validación final**        | Clear `selectedChild`                 | Validar no sea skill |

---

## ✅ Patrón de Solución (Template)

Para cualquier diálogo de creación jerárquica:

```typescript
function showCreate[Child]Dialog() {
    try {
        const dn: any = displayNode.value;

        // 1. Identificar tipo de displayNode
        const isChild = detectIfChild(dn);
        const isParent = detectIfParent(dn);
        const isGrandparent = detectIfGrandparent(dn);

        // 2. Resolver contexto padre correcto
        if (isChild) {
            // Buscar padre vía edges
            const parent = findParentViaEdges(dn);
            if (parent) setParentContext(parent);
        } else if (isParent) {
            // Usar directamente
            setParentContext(dn);
        } else if (isGrandparent) {
            // Usar primer hijo como padre
            const firstChild = getFirstChild(dn);
            if (firstChild) setParentContext(firstChild);
        }

        // 3. Si aún no hay contexto, revisar selección actual
        if (!hasParentContext() && currentSelection.value) {
            if (isChild(currentSelection.value)) {
                const parent = findParentViaEdges(currentSelection.value);
                if (parent) setParentContext(parent);
            }
        }

        // 4. Validación final: el contexto debe ser del tipo correcto
        if (parentContext.value && !isCorrectType(parentContext.value)) {
            console.warn('Invalid parent type, clearing');
            clearParentContext();
        }

    } catch (err: unknown) {
        console.error('Error setting parent context:', err);
    }

    // 5. Abrir diálogo
    dialogVisible.value = true;
}
```

### Checklist de Implementación

- [ ] Detectar tipo de `displayNode` (hijo/padre/abuelo)
- [ ] Si es hijo → buscar padre vía edges
- [ ] Si es padre → usar directamente
- [ ] Si es abuelo → usar primer hijo
- [ ] Revisar selección actual como fallback
- [ ] Validación final del tipo de contexto
- [ ] Limpiar contexto si validación falla
- [ ] Logging para debug

---

## 🎓 Lecciones Aprendidas

### 1. Siempre Validar Tipo de Nodo

```typescript
// ❌ MAL: Asumir tipo
parentContext.value = someNode;

// ✅ BIEN: Validar tipo
if (isParentType(someNode)) {
  parentContext.value = someNode;
} else {
  console.warn("Not a parent type");
}
```

### 2. Limpiar Estado Anterior

```typescript
// ❌ MAL: Solo setear si condición
if (condition) {
  context.value = newValue;
}
// Mantiene valor viejo si !condition

// ✅ BIEN: Limpiar explícitamente
context.value = null; // Reset
if (condition) {
  context.value = newValue;
}
```

### 3. Múltiples Fallbacks

```typescript
// ✅ BIEN: Cascade de fallbacks
const parent = tryMethod1() || tryMethod2() || tryMethod3() || null;

if (!parent) {
  console.warn("Could not resolve parent");
  return;
}
```

### 4. Buscar Padre vía Edges

Para estructuras jerárquicas con edges:

```typescript
// ✅ Patrón estándar
const parentEdge = edges.value.find((e) => e.target === childId);
const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
```

---

## 🚀 Prevención Futura

### Code Review Checklist

Al revisar funciones `show*Dialog()`:

- [ ] ¿Limpia el contexto anterior?
- [ ] ¿Valida el tipo del nodo?
- [ ] ¿Busca el padre si `displayNode` es hijo?
- [ ] ¿Tiene fallbacks múltiples?
- [ ] ¿Hace validación final antes de abrir?
- [ ] ¿Tiene logging para debug?

### Test Cases Recomendados

```typescript
describe("showCreateChildDialog", () => {
  it("should set parent when displayNode is parent", () => {});
  it("should find parent when displayNode is child", () => {});
  it("should clear invalid context", () => {});
  it("should handle missing displayNode", () => {});
  it("should work when creating multiple children in sequence", () => {});
});
```

---

## 📊 Impacto

### Antes de los Fixes

```
❌ Crear competencia #1: OK
❌ Crear competencia #2: FALLA (usa comp#1 como padre)
❌ Crear skill #1: OK
❌ Crear skill #2: FALLA (usa skill#1 como padre)
```

### Después de los Fixes

```
✅ Crear competencia #1: OK
✅ Crear competencia #2: OK (busca capability padre)
✅ Crear competencia #3: OK
✅ Crear skill #1: OK
✅ Crear skill #2: OK (busca competency padre)
✅ Crear skill #3: OK
```

---

## 🔗 Referencias

- [Fix Competencies](openmemory.md#L89) - showCreateCompDialog corregido
- [Fix Skills](openmemory.md#L13) - showCreateSkillDialog corregido
- [Patrón DRY](DRY_REFACTOR_SCENARIO_PLANNING.md) - Composables para centralizar lógica

---

**Conclusión:** Este patrón de bugs revela la importancia de validar y resolver correctamente el contexto jerárquico antes de abrir diálogos de creación. La solución requiere múltiples fallbacks y validación final del tipo de nodo.

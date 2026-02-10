# useScenarioState.ts

Composable que centraliza toda la lógica de **estado reactivo** para el módulo de Scenario Planning. Encapsula 100+ refs reactivos y computed properties organizados por dominio.

## Propósito

Separar la **gestión de estado** del componente Index.vue en un módulo reutilizable, facilitando:

- Testing unitario del estado
- Reutilización en otros componentes
- Mejor legibilidad y mantenibilidad

## Estructura de Estado

### 1. **Nodos y Aristas**

```typescript
nodes: Ref<NodeItem[]>; // Capabilities (nivel 0)
edges: Ref<Edge[]>; // Scenario -> Capability edges
childNodes: Ref<any[]>; // Competencies (nivel 1)
childEdges: Ref<Edge[]>; // Capability -> Competency edges
grandChildNodes: Ref<any[]>; // Skills (nivel 2)
grandChildEdges: Ref<Edge[]>; // Competency -> Skill edges
scenarioNode: Ref<any>; // El nodo origen del escenario
scenarioEdges: Ref<Edge[]>; // Edges del scenario
```

### 2. **Focus y Selección**

```typescript
focusedNode: Ref<NodeItem | null>; // Capability actualmente enfocada
selectedChild: Ref<any | null>; // Competencia seleccionada dentro de focused
displayNode: Computed; // Prefiere selectedChild si existe, sino focusedNode
```

### 3. **Estado UI**

```typescript
showSidebar: Ref<boolean>; // Sidebar visible/hidden
nodeSidebarCollapsed: Ref<boolean>; // Sidebar en modo colapsado
loaded: Ref<boolean>; // Tree cargada desde API
contextMenuVisible: Ref<boolean>; // Context menu abierto
```

### 4. **Estados de Diálogo**

```typescript
createModalVisible: Ref<boolean>;
createCompDialogVisible: Ref<boolean>;
createSkillDialogVisible: Ref<boolean>;
skillDetailDialogVisible: Ref<boolean>;
// ... más diálogos
```

### 5. **Campos de Formulario**

Organizados por acción:

- **newCap\***: Crear nueva capability (newCapName, newCapDescription, etc.)
- **editCap\***: Editar capability seleccionada (editCapName, editCapType, etc.)
- **newComp\***: Crear nueva competencia (newCompName, newCompDescription, etc.)
- **editChild\***: Editar competencia seleccionada (editChildName, editChildReadiness, etc.)
- **newSkill\***: Crear nueva skill (newSkillName, newSkillCategory, etc.)
- **skillEdit\***: Editar skill seleccionada (skillEditName, skillEditCategory, etc.)

## Funciones Principales

### `resetCreateCapForm()`

Limpia todos los campos de creación de capabilities.

### `resetCompetencyForm()`

Limpia campos para creación de competencias.

### `resetSkillForm()`

Limpia campos para creación de skills.

### `toggleSidebar()`

Alterna visibilidad del sidebar.

### `toggleNodeSidebarCollapse()`

Alterna modo colapsado del sidebar.

## Computed Properties

### `breadcrumbTitle`

Construye un string con la ruta completa: "Escenario › Capacidad › Competencia"

### `breadcrumbParts`

Array de strings para renderizado de breadcrumb multi-línea.

### `dialogThemeClass`

Retorna `'dialog-dark'` o `'dialog-light'` basado en `sidebarTheme`.

### `nodeSidebarVisible`

True cuando el sidebar no está colapsado.

### `viewportStyle`

CSSProperties para transformación del viewport (pan/zoom).

## Ejemplo de Uso

```typescript
import { useScenarioState } from '@/composables/useScenarioState';

// En setup()
const scenarioState = useScenarioState();

// Acceder a estado
console.log(scenarioState.focusedNode.value);
console.log(scenarioState.selectedChild.value);
console.log(scenarioState.displayNode.value);

// Resetear formularios
scenarioState.resetCreateCapForm();
scenarioState.resetCompetencyForm();

// Alternar UI
scenarioState.toggleSidebar();
```

## Dependencias

- `vue` (ref, computed, watch, reactive)
- `@/composables/useNotification` (showSuccess, showError)

## Notas de Implementación

- Todos los refs se inicializan en `ref()` con valores por defecto sensatos
- Los computed properties son lazy-evaluated (solo recalculan cuando deps cambian)
- Los watchers están listos para ser agregados sin cambios a la estructura base
- El estado es **reactivo globalmente** dentro del componente, pero **aislado** en el composable

## Testing

Ver `Index.spec.ts` para casos de test completos. Ejemplos:

```typescript
test('resetCreateCapForm limpia todos los campos', () => {
    const state = useScenarioState();
    state.newCapName.value = 'Test';
    state.resetCreateCapForm();
    expect(state.newCapName.value).toBe('');
});

test('toggleSidebar invierte showSidebar', () => {
    const state = useScenarioState();
    const initial = state.showSidebar.value;
    state.toggleSidebar();
    expect(state.showSidebar.value).toBe(!initial);
});
```

## Performance

- **100+ refs** → Considerados, no hay overhead de watcher innecesario
- **Computed properties** → Lazy-evaluated, eficientes para breadcrumb
- **Reactive dependencies** → Seguimiento automático de cambios en Vue

## Futuras Mejoras

- Persistencia en localStorage (última capability enfocada, sidebar state)
- Undo/Redo stack para cambios de estado
- Snapshots de estado para debugging

# useScenarioLayout.ts

Composable que encapsula **toda la lógica de layout y animaciones** para el grafo de Scenario Planning. Incluye D3 force simulation, posicionamiento adaptativo, y configuración centralizada.

## Propósito

Separar **matemática de layout** y **animaciones** del componente Index.vue:

- Cálculos de posición (matriz, radial, sides)
- D3 force-directed layout
- Transiciones y easing
- Configuración centralizada tuneable

## LAYOUT_CONFIG

**Objeto central** que contiene TODOS los magic numbers:

```typescript
const LAYOUT_CONFIG = {
  // CAPABILITY NODE LAYOUT (parent level)
  capability: {
    spacing: { hSpacing: 100, vSpacing: 80 },
    forces: { linkDistance: 100, linkStrength: 0.5, chargeStrength: -220 },
    scenarioEdgeDepth: 90,
    matrixVariants: [
      { min: 1, max: 3, rows: 1, cols: 3 },
      { min: 4, max: 6, rows: 2, cols: 3 },
      { min: 7, max: 12, rows: 2, cols: 6 }
    ]
  },

  // COMPETENCY NODE LAYOUT (child level)
  competency: {
    radial: { ... },
    maxDisplay: 10,
    defaultLayout: 'auto',
    parentOffset: 150,
    edge: { baseDepth: 40, curveFactor: 0.35, spreadOffset: 18 }
  },

  // SKILL NODE LAYOUT (grandchild level)
  skill: {
    radial: { ... },
    maxDisplay: 15,
    defaultLayout: 'auto',
    parentOffset: 80,
    edge: { baseDepth: 20, curveFactor: 0.25, spreadOffset: 10 }
  },

  // ANIMATIONS
  animations: {
    transitionMs: 420,
    staggerDelayMs: 30,
    easingFn: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
  }
};
```

**Ventaja**: Cambiar comportamiento global = modificar un objeto, no 50 constantes esparcidas.

## Funciones Principales

### `centerOnNode(node, previousPos, canvasSize)`

Centra la vista en un nodo específico.

**Parámetros:**

- `node`: NodeItem a centrar
- `previousPos`: {x, y} anterior (para swapping)
- `canvasSize`: {width, height} del canvas

```typescript
centerOnNode(capability, { x: 0, y: 0 }, { width: 900, height: 600 });
```

### `expandCompetencies(node, initialParentPos, options)`

**Expande competencias** de una capability en varios modos de layout.

**Opciones:**

- `layout`: 'auto' | 'radial' | 'matrix' | 'sides'
- `rows`, `cols`: Override de matriz
- `limit`: Máximo de competencias a mostrar

```typescript
expandCompetencies(
    focusedCapability,
    { x: 450, y: 300 },
    {
        layout: 'matrix',
        rows: 2,
        cols: 5,
    },
);
```

**Modos:**

- **auto**: Detecta automáticamente (>5 selected = radial)
- **radial**: Círculo alrededor del padre
- **matrix**: Grid 2D
- **sides**: Distribución en 3 lados

### `expandSkills(competency, parentNode, options)`

**Expande skills** de una competencia con layout adaptativo.

```typescript
expandSkills(selectedCompetency, focusedCapability, {
    layout: 'auto',
});
```

### `collapseGrandChildren(animated, duration)`

**Colapsa todos los skills** visibles.

```typescript
collapseGrandChildren(true, 300); // Animated collapse
```

### `runForceLayout(nodes, edges, canvasWidth, canvasHeight)`

**Ejecuta D3 force simulation** para auto-posicionar nodos.

```typescript
runForceLayout(nodes.value, edges.value, width.value, height.value);
```

**Parámetros de simulación:**

- `linkDistance`: 100px ideal entre nodos conectados
- `linkStrength`: 0.5 (50% intensidad de atracción)
- `chargeStrength`: -220 (repulsión entre nodos)

### Utility Functions

#### `wait(ms)`

Promise que resuelve después de N milisegundos.

```typescript
await wait(300); // Esperar animación
```

#### `clampY(y)`

Limita coordenada Y al rango válido del canvas.

```typescript
const safeY = clampY(node.y);
```

#### `wrapLabel(text, maxLength)`

Trunca texto con ellipsis si excede longitud.

```typescript
const label = wrapLabel('Very Long Name', 12); // "Very Long N…"
```

#### `computeInitialPosition(index, total, width, height)`

Calcula posición inicial en círculo para nodos sin posición.

```typescript
const { x, y } = computeInitialPosition(0, 10, 900, 600);
```

## Ejemplos de Uso

### Scenario Básico: Expandir Competencias

```typescript
import { useScenarioLayout } from '@/composables/useScenarioLayout';

const layout = useScenarioLayout();

// Cuando usuario hace click en capability
const handleCapabilityClick = (capability) => {
    focusedNode.value = capability;

    // Expandir competencias automáticamente
    layout.expandCompetencies(
        capability,
        { x: capability.x, y: capability.y },
        { layout: 'auto' },
    );

    // Animar a la vista
    layout.centerOnNode(
        capability,
        { x: 450, y: 300 },
        { width: 900, height: 600 },
    );
};
```

### Scenario Avanzado: Layout Radial + Force Simulation

```typescript
// Expandir competencias en radial
layout.expandCompetencies(capability, initialPos, {
    layout: 'radial',
});

// Ejecutar force layout para posicionamiento auto
await layout.runForceLayout(
    nodes.value,
    edges.value,
    width.value,
    height.value,
);

// Centrar en el nodo
await layout.centerOnNode(capability, prevPos, canvasSize);
```

## Integración con LAYOUT_CONFIG

Cada función **respeta LAYOUT_CONFIG** automáticamente:

```typescript
// Cambiar espaciado global
LAYOUT_CONFIG.competency.parentOffset = 200;

// Todas las llamadas futuras a expandCompetencies usarán 200px
expandCompetencies(node, pos, {});
```

## Performance

- **D3 Force**: ~100ms para 50 nodos
- **Matrix Layout**: O(n) para n nodos
- **Radial Layout**: O(n) trigonometría
- **Cached**: Posiciones previas se reutilizan si possible

## Dependencias

- `vue` (computed, watch, ref)
- `d3` (simulation, forceLink, forceManyBody, etc.)
- `@/composables/useCompetencyLayout` (chooseMatrixVariant, etc.)

## Testing

Ver `Index.spec.ts`:

```typescript
test('expandCompetencies crea childNodes y childEdges', async () => {
  const layout = useScenarioLayout();
  const capability = { id: 1, x: 450, y: 300, competencies: [{...}] };

  layout.expandCompetencies(capability, {x: 450, y: 300});

  expect(childNodes.value.length).toBeGreaterThan(0);
  expect(childEdges.value.length).toBeGreaterThan(0);
});
```

## Tunning Tips

**Si los nodos están muy juntos:**

```typescript
LAYOUT_CONFIG.capability.spacing.hSpacing = 150; // Aumentar
```

**Si la simulación es muy lenta:**

```typescript
LAYOUT_CONFIG.capability.forces.chargeStrength = -100; // Reducir repulsión
```

**Si las curvas son muy pronunciadas:**

```typescript
LAYOUT_CONFIG.competency.edge.baseDepth = 20; // Reducir curvatura
```

## Futuras Mejoras

- Hierarchical layout (Sugiyama algorithm)
- Tree layout (más estructura vertical)
- Collision detection avanzada
- Smooth camera panning (easing customizable)
- Viewport bookmarking (guardar vistas)

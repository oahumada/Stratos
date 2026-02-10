# useScenarioEdges.ts

Composable que encapsula **toda la lógica de renderizado de aristas (edges)** para el grafo de Scenario Planning. Soporta 4 modos de visualización configurables.

## Propósito

Separar **cálculos de edge rendering** en un módulo independiente:

- 4 modos de aristas (offset, gap-large, curve, spread)
- Cálculo de puntos finales con ajustes automáticos
- Curvas Bezier configurables
- Opacidad animada

## Modos de Renderizado

### Modo 0: Offset Pequeño (default)

Línea recta con pequeño desplazamiento vertical.

**Uso:** Presentación limpia, conexiones simples.

```typescript
// Resultado: línea recta con pequeño gap
{
    isPath: (false, x1, y1, x2, y2);
}
```

### Modo 1: Gap Grande

Aumenta separación vertical del target para mayor claridad.

**Uso:** Cuando hay muchas aristas juntas.

```typescript
// Resultado: línea más separada verticalmente
{ isPath: false, x1, y1, x2, y2: adjustedY }
```

### Modo 2: Curve (Bezier)

Curva suave con profundidad configurable.

**Uso:** Visualización elegante, facilita seguimiento visual.

```typescript
// Resultado: path SVG con curvatura
{ isPath: true, d: "M x1 y1 C x1 cpY x2 cpY x2 y2" }
```

**Parámetros configurables:**

- `baseDepth`: Profundidad base de curva (40px competencias, 20px skills)
- `curveFactor`: Multiplicador por distancia (0.35 competencias, 0.25 skills)

### Modo 3: Spread

Desplaza horizontalmente según índice de arista en grupo.

**Uso:** Diferenciar aristas paralelas visualmente.

```typescript
// Resultado: línea con desplazamiento en X
{ isPath: false, x1, y1, x2: offsetX, y2 }
```

**Parámetro configurable:**

- `spreadOffset`: Desplazamiento por índice (18px competencias, 10px skills)

## Funciones Principales

### `injectState(nodes, childNodes, grandChildNodes, childEdges, grandChildEdges)`

**Inyecta referencias** de nodos/edges necesarias para cálculos.

**Requerido**: Llamar antes de cualquier función de renderizado.

```typescript
const edges = useScenarioEdges();
onMounted(() => {
    edges.injectState(
        childEdges,
        grandChildEdges,
        nodes,
        childNodes,
        grandChildNodes,
    );
});
```

### `edgeEndpoint(edge, forTarget = true)`

**Calcula coordenadas ajustadas** del punto final de una arista.

**Ajustes automáticos:**

- Si target es un child node (id < 0), desplaza Y hacia arriba
- Distinto offset para competencias vs skills (24px vs 14px)

```typescript
const { x, y } = edges.edgeEndpoint(edge, true); // Target endpoint
const { x, y } = edges.edgeEndpoint(edge, false); // Source endpoint
```

**Resultado:**

```typescript
{ x: number | undefined, y: number | undefined }
```

### `groupedIndexForEdge(edge)`

**Retorna índice** dentro de un grupo de aristas paralelas.

Utilizado en Modo 3 (spread) para calcular offset horizontal.

```typescript
const idx = edges.groupedIndexForEdge(edge); // 0, 1, 2, ...
```

### `edgeAnimOpacity(edge)`

**Lee opacidad animada** del edge (si existe propiedad runtime animOpacity).

```typescript
const opacity = edges.edgeAnimOpacity(edge); // 0.98 default
```

### `edgeRenderFor(edge)`

**Construye la forma final** del edge según modo seleccionado.

**Retorna:**

```typescript
// Modo 0, 1, 3 (líneas)
{ isPath: false, x1, y1, x2, y2 }

// Modo 2 (curva)
{ isPath: true, d: "M x1 y1 C..." }

// Error fallback
{ isPath: false, x1: undefined, y1: undefined, x2: undefined, y2: undefined }
```

**Uso en template SVG:**

```vue
<path v-if="edgeRenderFor(e).isPath" :d="edgeRenderFor(e).d" />
<line v-else :x1="edgeRenderFor(e).x1" :y1="edgeRenderFor(e).y1" ... />
```

### `scenarioEdgePath(edge)`

**Genera path SVG especial** para aristas scenario → capability.

Siempre usa curva Bezier con profundidad configurable.

```typescript
const pathD = edges.scenarioEdgePath(scenarioEdge);
// Resultado: "M 450 100 C 450 190 350 190 350 280"
```

## Configuración via LAYOUT_CONFIG

El composable respeta configuración centralizada:

```typescript
// En LAYOUT_CONFIG
competency: {
  edge: {
    baseDepth: 40,      // Profundidad curva
    curveFactor: 0.35,  // Factor por distancia
    spreadOffset: 18    // Desplazamiento horizontal
  }
},
skill: {
  edge: {
    baseDepth: 20,
    curveFactor: 0.25,
    spreadOffset: 10
  }
}
```

**Cambiar dinámicamente:**

```typescript
LAYOUT_CONFIG.competency.edge.baseDepth = 60; // Curvas más pronunciadas
```

## Ejemplos de Uso

### Renderizar edges en template

```vue
<g class="edges">
  <!-- Competency edges (child edges) -->
  <template v-if="childEdgeMode === 2">
    <!-- Modo curve -->
    <path
      v-for="e in childEdges"
      :d="edgeRenderFor(e).d"
      stroke="blue"
      stroke-width="2"
    />
  </template>
  
  <template v-else>
    <!-- Modos línea -->
    <line
      v-for="e in childEdges"
      :x1="edgeRenderFor(e).x1"
      :y1="edgeRenderFor(e).y1"
      :x2="edgeRenderFor(e).x2"
      :y2="edgeRenderFor(e).y2"
      stroke="blue"
    />
  </template>
</g>

<!-- Scenario edges (siempre curve) -->
<g class="scenario-edges">
  <path
    v-for="e in scenarioEdges"
    :d="scenarioEdgePath(e)"
    stroke="purple"
  />
</g>
```

### Cambiar modo en runtime

```typescript
function toggleEdgeMode() {
    childEdgeMode.value = (childEdgeMode.value + 1) % 4;
    // Automáticamente recalcula todos los edges
}
```

### Ajustar curvatura

```typescript
// Skills menos curvos
LAYOUT_CONFIG.skill.edge.baseDepth = 10;

// Competencias muy curvos
LAYOUT_CONFIG.competency.edge.baseDepth = 80;
```

## Dependencias

- `vue` (ref, Ref type)
- `@/composables/useScenarioLayout` (LAYOUT_CONFIG)

## Performance

- **edgeEndpoint()**: O(1) búsqueda en array
- **groupedIndexForEdge()**: O(n) donde n = # aristas paralelas
- **edgeRenderFor()**: O(1) cálculos trigonométricos
- **scenarioEdgePath()**: O(1) curva Bezier

**Total**: Renderizado de 100 edges < 5ms

## Testing

Ver `Index.spec.ts`:

```typescript
test('edgeEndpoint ajusta Y para child nodes', () => {
  const edges = useScenarioEdges();
  edges.injectState(...);

  const edge = { source: 1, target: -5 };  // target es child
  const ep = edges.edgeEndpoint(edge, true);

  expect(ep.y).toBeLessThan(originalY - 24);  // Desplazado hacia arriba
});

test('edgeRenderFor retorna path en modo curve', () => {
  const edges = useScenarioEdges();
  edges.childEdgeMode.value = 2;

  const render = edges.edgeRenderFor(edge);

  expect(render.isPath).toBe(true);
  expect(render.d).toMatch(/^M.*C.*$/);
});
```

## Tunning Tips

**Aristas muy juntas? Usa Modo 3 (spread):**

```typescript
childEdgeMode.value = 3;
```

**¿Curvas muy planas? Aumenta baseDepth:**

```typescript
LAYOUT_CONFIG.competency.edge.baseDepth = 60;
```

**¿Curvas muy pronunciadas? Reduce curveFactor:**

```typescript
LAYOUT_CONFIG.competency.edge.curveFactor = 0.15;
```

**Skills con aristas superpuestas:**

```typescript
childEdgeMode.value = 3; // Spread mode
LAYOUT_CONFIG.skill.edge.spreadOffset = 15; // Aumentar separación
```

## Futuras Mejoras

- Animación de flujo (moving dashes along edges)
- Labels en aristas (mostrar peso/prioridad)
- Aristas condicionales (ocultar basado en filtros)
- Interactivo: cambiar destino al arrastrar
- WebGL rendering (si >1000 edges)

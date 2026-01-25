**Resumen**

Se documenta la decisión y la implementación sobre la disposición y reordenamiento de nodos
en la pantalla de Scenario Planning.

**Decisión**: Cuando existen exactamente 10 nodos principales, el reordenador fuerza una
disposición en matriz de 2 filas x 5 columnas (2x5). Esta regla se implementó para lograr
consistencia visual y evitar layouts auto-ajustados (p. ej. 4x3) que dificultaban lectura.

**Dónde está implementado**

- Lógica principal: [resources/js/utils/reorderNodesHelper.ts](resources/js/utils/reorderNodesHelper.ts)
- Uso en la UI: [resources/js/pages/ScenarioPlanning/Index.vue](resources/js/pages/ScenarioPlanning/Index.vue#L1536-L1560)
- Test unitario: [resources/js/utils/**tests**/reorderNodesHelper.spec.ts](resources/js/utils/__tests__/reorderNodesHelper.spec.ts)

**Qué hace exactamente**

- Para `total === 10` fuerza `cols = 5` y `rows = 2` antes de calcular posiciones.
- Para otros tamaños usa heurística near-square limitada a 6 columnas por defecto.
- Aplica un desplazamiento adicional para evitar superposición con el `scenarioNode`.

**Por qué**

- Requiere consistencia visual: 2x5 es más legible y predecible en el mapa para 10 items.
- Evita resultados inesperados de la heurística basada en sqrt(total) que devolvía 4x3.

**Cómo probar localmente**

1. Ejecutar tests unitarios (Vitest) desde `src/`:

```bash
cd src
npm run test:unit
```

2. El test `reorderNodesHelper.spec.ts` valida que 10 nodos se reordenen en 2 filas x 5 columnas.

**Notas / próximos pasos**

- Si deseas un comportamiento distinto (p. ej. otra orientación o reflujo automático),
  podemos parametrizar la heurística y exponer una opción `preferredLayout`.
- Se dejó la persistencia de posiciones en `Index.vue` (guardar mediante `savePositions()`).

**\* Fin del documento **

# Talent Engineering - Componentes frontend añadidos

Fecha: 2026-02-11

Descripción breve:

Se añadieron componentes Vue 3 + TypeScript + Vuetify para el Talent Engineering Console. Son componentes presentacionales y pequeños, pensados para importarse desde páginas de Scenario Planning.

Componentes creados:

- `resources/js/components/TalentEngineering/TalentCompositionCard.vue`
    - Props: `composition: TalentComposition` (ver `types/talent.ts`)
    - Muestra: `roleName`, `strategySuggestion`, `humanPercentage`, `syntheticPercentage`.
    - UI: `v-chip`, `v-progress-linear`.

- `resources/js/components/TalentEngineering/SynthetizationIndexCard.vue`
    - Props: `indexValue: number` (porcentaje 0-100)
    - Muestra un `v-progress-circular` con el valor del índice promedio de sintetización.

Notas de integración:

- Tipado: use `types/talent.ts` para las interfaces `TalentComposition` y `Role`.
- Import recomendado (ejemplo en `ScenarioDetail.vue`):

```ts
import TalentCompositionCard from '@/components/TalentEngineering/TalentCompositionCard.vue';
import SynthetizationIndexCard from '@/components/TalentEngineering/SynthetizationIndexCard.vue';
```

- Sugerencia de uso: mapear `scenario.suggested_roles` a `TalentComposition` y renderizar una lista de `TalentCompositionCard` y un `SynthetizationIndexCard` con el promedio.

Estado:

- Archivos creados en working copy.
- No se añadieron imports a páginas existentes ni rutas nuevas.

Siguientes pasos opcionales (puedo hacerlo):

1. Importar y usar los componentes en `resources/js/pages/ScenarioPlanning/ScenarioDetail.vue`.
2. Añadir tests unitarios (Vitest) para los componentes.
3. Ejecutar `npm run dev` o `npm run build` y resolver warnings/lints.

---

Si quieres que integre los componentes en una página concreta, dime cuál y lo adiciono ahora.

# OpenMemory - Resumen del proyecto Stratos

Este documento actúa como índice vivo (openmemory) del repositorio `oahumada/Stratos`.
Se creó/actualizó automáticamente para registrar decisiones, implementaciones y referencias útiles.

## Estado actual (inicio)
- Branch: feature/workforce-planning-scenario-modeling
- Fecha: 2026-01-19
- la carpeta del proyecto es /src

## Overview rápido
- Stack: Laravel 12 (backend) + Inertia v2 + Vue 3 + TypeScript + Vuetify 3
- Multi-tenant por `organization_id`, autenticación con Sanctum.
- Estructura principal: código en `src/`, documentación en `docs/` y `docs_wiki/`.

## Componentes clave (relevantes para WFP / Cerebro Stratos)
- `resources/js/pages/ScenarioPlanning/Index.vue` — Mapa prototipo (PrototypeMap). Usado por `ScenarioDetail.vue`.
- `resources/js/pages/ScenarioPlanning/BrainCanvas.vue` — Componente referenciado en la guía (implementación con D3).
- `docs/GUIA_STRATOS_CEREBRO.txt` — Guía de implementación del "Cerebro Stratos" (inspirada en TheBrain).

## Búsquedas iniciales realizadas (Phase 1)
- Confirmadas referencias a `BrainCanvas.vue` y uso del mapa: `PrototypeMap` es `Index.vue`.
- Detectada presencia de logs y build assets que incluyen `BrainCanvas.vue` (ver `public/build/manifest.json`).

## Implementación registrada: Mejora visual PrototypeMap
- Qué: mejoras visuales en el mapa de capacidades para mayor legibilidad y jerarquía visual.
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` (sustitución de `svg` con `defs` para gradientes, filtro de sombra, clases CSS scoped y animación `pulse` para nodos críticos).
- Decisión clave: mantener la lógica D3 existente; usar `defs` SVG para estilos visuales (gradiente radial + sombra); no cambiar API ni persistencia.
- Archivos modificados: Index.vue (visual + ligeras señales `is_critical` en nodos), openmemory.md (registro).

## Acción técnica relacionada: typings D3
- Se instaló `@types/d3` localmente en `src` (devDependency) para eliminar aviso de "No se encontró ningún archivo de declaración para el módulo 'd3'".
- Si TypeScript sigue reportando errores, alternativa rápida: agregar `src/types/d3.d.ts` con `declare module 'd3';`.

## Próximos pasos recomendados (plan corto)
1. Ejecutar `npm run lint` y `npm run format` para aplicar estilo a `Index.vue`.
2. Crear `src/types/d3.d.ts` si quedan warnings de typing en el editor.
3. (Opcional) Extraer el BrainCanvas a `resources/js/components/Brain/` si se centraliza la implementación.

## Registro de acciones / metadata
- Cambio: Mejora visual `PrototypeMap` (Index.vue).
- Branch: feature/workforce-planning-scenario-modeling
- Autor (local): cambios aplicados desde esta sesión de Copilot/IDE.

---

Si necesitas que añada la entrada de memoria formal (add-memory) o que cree el archivo `src/types/d3.d.ts`, indícalo y lo ejecuto ahora.


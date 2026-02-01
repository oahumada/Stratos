# OpenMemory - Resumen del proyecto Stratos

Este documento actúa como índice vivo (openmemory) del repositorio `oahumada/Stratos`.
Se creó/actualizó automáticamente para registrar decisiones, implementaciones y referencias útiles.

## Estado actual (inicio)

- Branch: feature/workforce-planning-scenario-modeling
- Fecha: 2026-01-19
- la carpeta del proyecto es /src

### Cambios recientes - Modelado de Skills

- Se consolidó el modelo de habilidades a nombre singular `Skill`.
- `app/Models/Skills.php` fue reemplazado por un alias (`class Skills extends Skill`) para compatibilidad retroactiva; se planea eliminar el archivo tras actualizar el autoload (`composer dump-autoload`) y verificar no hay referencias restantes.
  - Fecha: 2026-01-29

## Preferencias del usuario

- **Proyecto (específico):** Ejecutar comandos, scripts y pruebas desde la carpeta `src` (por ejemplo, `cd src && npm test` o `cd src && php artisan test`).
  - Motivo: ejecutar comandos desde la raíz del repo provoca errores recurrentes (no se detecta `package.json`/`artisan` en la raíz).
  - Registrado: 2026-01-28

## Overview rápido

- Stack: Laravel 12 (backend) + Inertia v2 + Vue 3 + TypeScript + Vuetify 3
- Multi-tenant por `organization_id`, autenticación con Sanctum.
- Estructura principal: código en `src/`, documentación en `docs/` y `docs_wiki/`.

## Componentes clave (relevantes para WFP / Cerebro Stratos)

- `resources/js/pages/ScenarioPlanning/Index.vue` — Mapa prototipo (PrototypeMap). Usado por `ScenarioDetail.vue`.
- `src/resources/js/components/brain/BrainCanvas.vue` — Componente referenciado en la guía (implementación con D3).
- Nota: la guía se movió a `docs/GUIA_STRATOS_CEREBRO.txt`.
- `docs/GUIA_STRATOS_CEREBRO.txt` — Guía de implementación del "Cerebro Stratos" (inspirada en TheBrain).

### Memoria: Workforce Planning / Scenario Planning

- **Última actualización:** 14 Enero 2026
- **Status:** Módulo integrado (UI + API). Fuente canónica: [docs/memories_workforce_planning.md](docs/memories_workforce_planning.md#L1).
- **Resumen:** WFP centraliza creación y comparación de escenarios (what-if) con plantillas (IA Adoption, Digital Transformation, Rapid Growth, Succession Planning).
- **Rutas UI:** `/workforce-planning` → `WorkforcePlanning/ScenarioSelector.vue`; `/workforce-planning/{id}` → `OverviewDashboard.vue`.
- **APIs clave (resumen):**
  - `GET    //api/workforce-planning/scenario-templates`
  - `POST   //api/workforce-planning/workforce-scenarios/{template_id}/instantiate-from-template`
  - `POST   //api/workforce-planning/workforce-scenarios/{id}/calculate-gaps`
  - `POST   //api/workforce-planning/workforce-scenarios/{id}/refresh-suggested-strategies`
  - `POST   //api/workforce-planning/scenario-comparisons`
  - `GET    //api/workforce-planning/workforce-scenarios/{id}`
  - `GET    //api/workforce-planning/workforce-scenarios/{id}/role-forecasts`
  - `GET    //api/workforce-planning/workforce-scenarios/{id}/skill-gaps`
  - `POST   //api/workforce-planning/workforce-scenarios/{id}/analyze`
- **Quick-steps (Postman - 5 min):** instanciar template → `calculate-gaps` → `refresh-suggested-strategies` → `scenario-comparisons` → revisar detalle.
- **Notas de integración:** `AppSidebar.vue` ya incluye el link; rutas registradas (`workforce-planning.index`, `workforce-planning.show`). Mantener `POSTMAN_VALIDATION_5MIN.md` como guía rápida.
- **Recomendación:** Añadir E2E (Playwright) para el flujo create→calculate→suggest→compare y migrar stores a Pinia según `WORKFORCE_PLANNING_UI_INTEGRATION.md`.

#### Renombramiento del módulo

- **Qué:** El módulo originalmente llamado `WorkForce Planning` fue renombrado a `ScenarioPlanning` para enfatizar la creación y modelamiento de escenarios (what-if), y alinear el nombre con la UX y las páginas actuales.
- **Por qué:** El nombre `ScenarioPlanning` comunica mejor el propósito principal: modelado y comparación de escenarios, plantillas y análisis de brechas.
- **Fecha:** 2026-01-21
- **Metadata Git:**
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a

Esta entrada sirve como referencia para nombres de rutas, directorios y componentes que podrían contener la forma antigua (`workforce-planning`) y deben considerarse para actualizaciones futuras.

## Búsquedas iniciales realizadas (Phase 1)

- Confirmadas referencias a `BrainCanvas.vue` y uso del mapa: `PrototypeMap` es `Index.vue`.
- Detectada presencia de logs y build assets que incluyen `BrainCanvas.vue` (ver `public/build/manifest.json`).

## Implementación registrada: Mejora visual PrototypeMap

- Qué: mejoras visuales en el mapa de capacidades para mayor legibilidad y jerarquía visual.
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` (sustitución de `svg` con `defs` para gradientes, filtro de sombra, clases CSS scoped y animación `pulse` para nodos críticos).
- Decisión clave: mantener la lógica D3 existente; usar `defs` SVG para estilos visuales (gradiente radial + sombra); no cambiar API ni persistencia.
- Archivos modificados: Index.vue (visual + ligeras señales `is_critical` en nodos), openmemory.md (registro).

### Cambio UI: Sliders para atributos pivot (strategic weight, priority, required level)

- Qué: Reemplazo de inputs numéricos por controles `v-slider` en el modal de capacidades y formularios relacionados para los atributos de pivot: `strategic_weight` (1-10), `priority` (1-5) y `required_level` (1-5).
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` — afectado en los formularios de creación (`Crear capacidad`), edición del nodo y edición de competencias.
- Por qué: Mejorar la usabilidad y coherencia visual con el control existente `Importancia` (slider), evitando entradas manuales fuera de rango y ofreciendo feedback inmediato del valor seleccionado.
- Fecha: 2026-01-28
- Archivos modificados: `src/resources/js/pages/ScenarioPlanning/Index.vue`

### Cambio: Título integrado en diagrama (Index.vue)

- **Qué:** Se movió la cabecera externa del componente y el título ahora se renderiza dentro del lienzo SVG usando un `foreignObject` centrado en la parte superior del mapa. Esto aprovecha el espacio superior que antes quedaba en blanco y mantiene el título visible durante el pan/zoom.
- **Dónde:** `src/resources/js/pages/ScenarioPlanning/Index.vue` — reemplazo de la etiqueta `<header>` por un `foreignObject` dentro del `<svg>` y estilos asociados.
- **Por qué:** Aprovechar el espacio superior para presentación del título y reducir el padding externo; mejora estética y hace el título parte del contexto visual del diagrama.
- **Fecha:** 2026-01-28

## Memoria: Cambios de la sesión 2026-01-29 (Fix: Crear competencia en modal)

### Problema identificado - Parte 1: Confusión de endpoints (RESUELTO)

Cuando el usuario creaba una competencia desde el modal de capacidad, la competencia NO se guardaba ni se adjuntaba correctamente. Causa: frontend intentaba `POST /api/competencies` (endpoint que NO existe).

### Problema identificado - Parte 2: Modelo de base de datos inconsistente (RESUELTO)

El modelo **debería ser N:N con pivote** (una competencia puede ser compartida por múltiples capacidades), pero el código mantenía restos del modelo 1:N antiguo:

- Tabla `competencies` tenía FK directo `capability_id`
- Tabla `capability_competencies` también vinculaba competencias a capacidades
- Esto causaba redundancia y confusión sobre cuál relación era la "correcta"

### Soluciones implementadas

**Cambio arquitectónico importante: Pasar de 1:N a N:N con pivote**

**Frontend:** `src/resources/js/pages/ScenarioPlanning/Index.vue`

- ✅ Limpiar `selectedChild.value` en `contextCreateChild()`
- ✅ Función `resetCompetencyForm()` y watchers para limpiar campos
- ✅ Reescribir `createAndAttachComp()` para usar endpoint único:
  ```javascript
  POST /api/strategic-planning/scenarios/{scenarioId}/capabilities/{capId}/competencies
  { competency: { name, description }, required_level, ... }
  ```

**Backend:** Nuevas migraciones y modelos

1. **Nueva migración:** `2026_01_29_120000_remove_capability_id_from_competencies.php`
   - Elimina FK `capability_id` de tabla `competencies`
   - Elimina índices relacionados
   - La relación será SOLO vía pivote

2. **Modelo Competency:** `app/Models/Competency.php`
   - ✅ Remover `belongsTo(Capability)`
   - ✅ Agregar `belongsToMany(Capability::class)` vía pivote `capability_competencies`
   - ✅ Actualizar `fillable` para remover `capability_id`

3. **Modelo Capability:** `app/Models/Capability.php`
   - ✅ Cambiar `hasMany(Competency)` a `belongsToMany(Competency)` vía pivote
   - ✅ Ahora soporta N:N correctamente

4. **ScenarioController::getCapabilityTree()** `app/Http/Controllers/Api/ScenarioController.php`
   - ✅ Actualizar eager loading para filtrar competencias por escenario en el pivote:
     ```php
     'capabilities.competencies' => function ($qc) {
         $qc->wherePivot('scenario_id', $scenarioId);
     }
     ```

5. **Endpoint backend:** `routes/api.php`
   - ✅ Remover asignación de `'capability_id'` al crear competencia nueva
   - ✅ La vinculación es SOLO vía pivote `capability_competencies`

### Archivos modificados

- `src/resources/js/pages/ScenarioPlanning/Index.vue` (frontend)
- `src/routes/api.php` (endpoint cleanup)
- `src/app/Models/Competency.php` (relación N:N)
- `src/app/Models/Capability.php` (relación N:N)
- `src/app/Http/Controllers/Api/ScenarioController.php` (eager loading)
- `src/database/migrations/2026_01_29_120000_remove_capability_id_from_competencies.php` (nueva migración)

### Beneficio arquitectónico

- Una competencia puede ser compartida entre múltiples capacidades
- Cada relación scenario-capability-competency puede tener atributos de pivote específicos
- Flexibilidad para reutilizar competencias sin duplicación

### Fecha

2026-01-29

### Git Metadata

- `git_repo_name`: oahumada/Stratos
- `git_branch`: feature/workforce-planning-scenario-modeling
- `git_commit_hash`: (pending commit)

## Memoria: Cambios de la sesión 2026-01-29 (Fix: Crear competencia en modal)

### Problema identificado

Cuando el usuario creaba una competencia desde el modal de capacidad, la competencia NO se guardaba ni se adjuntaba correctamente. Hay dos causas raíz:

1. **Confusión de relaciones:** El código asumía dos vías de vincular competencias:
   - Directa: vía `capability_id` en tabla `competencies`
   - Pivot: vía tabla `capability_competencies` con scenario-specific data

   Pero el frontend intentaba:
   - `POST /api/competencies` (endpoint que NO existe) → Error 404
   - Luego `POST /api/.../competencies` (fallback)

2. **Estado mal limpiado:** Cuando se abría el modal de crear competencia:
   - `selectedChild.value` no se limpiaba
   - Si había una competencia seleccionada antes, `displayNode = selectedChild ?? focusedNode` usaba el child viejo
   - Los campos del formulario no se reseteaban después de crear

### Soluciones implementadas

**Frontend:** `src/resources/js/pages/ScenarioPlanning/Index.vue`

- ✅ Limpiar `selectedChild.value = null` en `contextCreateChild()` (línea ~424)
- ✅ Crear función `resetCompetencyForm()` (línea ~321)
- ✅ Llamar reset después de crear exitosamente (línea ~2506)
- ✅ Añadida creación/adjunto automático de `skills` desde el modal de creación de competencia: `createAndAttachComp()` ahora procesa `newCompSkills` (coma-separadas) y llama a `createAndAttachSkillForComp(compId, payload)` para crear y asociar cada skill nueva.
- ✅ Agregar watcher para limpiar campos al cerrar modal (línea ~998)
- ✅ Reescribir `createAndAttachComp()` para usar endpoint único y correcto:
  - Antes: dos llamadas (`POST /api/competencies` + fallback)
  - Ahora: una sola `POST /api/strategic-planning/scenarios/{scenarioId}/capabilities/{capId}/competencies`
  - Payload único: `{ competency: { name, description }, required_level, ... }`

**Backend:** `src/routes/api.php`

- ✅ Eliminar ruta duplicada (línea 97-128, que solo soportaba crear competencia sin pivot)
- ✅ Mantener ruta completa (línea 99, ahora única) que soporta:
  - `competency_id`: vincular competencia existente
  - `competency: { name, description }`: crear nueva en una transacción
  - Pivot attributes: `required_level`, `weight`, `rationale`, `is_required`

### Archivos modificados

- `src/resources/js/pages/ScenarioPlanning/Index.vue` (frontend form fix)
- `src/routes/api.php` (backend route cleanup)

### Fecha

2026-01-29

### Git Metadata

- `git_repo_name`: oahumada/Stratos
- `git_branch`: feature/workforce-planning-scenario-modeling
- `git_commit_hash`: (pending commit)

## Memoria: Cambios de la sesión 2026-01-27 (Visual tuning & configuraciones)

- **Qué:** Ajustes visuales y de layout en `src/resources/js/pages/ScenarioPlanning/Index.vue` para mejorar la separación entre nodos padre/hijos y la curvatura de los conectores. Se centralizaron parámetros visuales en la nueva prop `visualConfig` y se añadió `capabilityChildrenOffset` como prop aislada para control fino.
- **Por qué:** Facilitar tuning rápido de la visualización desde la invocación del componente y reducir constantes dispersas en el archivo.
- **Cambios principales:**
  - Añadida prop `visualConfig` (valores por defecto: `nodeRadius`, `focusRadius`, `scenarioOffset`, `childDrop`, `skillDrop`, `edge.baseDepth`, `edge.curveFactor`, `edge.spreadOffset`).
  - `expandCompetencies` y `expandSkills` ahora consultan `visualConfig` y `capabilityChildrenOffset` para posicionamiento vertical de hijos.
  - `edgeRenderFor` y `edgeEndpoint` adaptan la profundidad de curva según distancia y `visualConfig.edge.curveFactor`.
  - Se preservaron los `marker-end` existentes (`#childArrow`) para mantener las flechas en los conectores.
- **Archivos modificados:**
  - `src/resources/js/pages/ScenarioPlanning/Index.vue` (prop `visualConfig`, uso en `expandCompetencies`, `expandSkills`, `edgeRenderFor`, `centerOnNode` y ajustes visuales).
- **Estado Git local:** cambios aplicados en branch `feature/workforce-planning-scenario-modeling` (commits locales pendientes de push). Intento de fetch/push falló por autenticación remota (usar SSH o PAT para sincronizar).
- **Próximos pasos guardados:** continuar mañana con la implementación del `NodeContextMenu` y los modales para crear/asociar competencias/skills (ver TODO list actualizada en repo).
- **Fecha:** 2026-01-27

### Comportamiento: Mostrar Guardar/Reset sólo cuando hay cambios

- Qué: Añadida bandera reactiva `positionsDirty` para mostrar los botones `Guardar` y `Reset` únicamente cuando el usuario ha movido nodos (posiciones sin guardar).
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` — se añadió `positionsDirty = ref(false)`, se marca `true` durante el arrastre (`onPointerMove`) y se limpia (`false`) tras guardar o resetear posiciones.
- Por qué: Reducir ruido en la interfaz y evitar acciones innecesarias cuando no hay cambios.
- Fecha: 2026-01-22
- Archivos modificados: `src/resources/js/pages/ScenarioPlanning/Index.vue`

### Ajuste: Empujar hijos hacia abajo cuando hay >=10 nodos

- Qué: En `Index.vue` la función `expandCompetencies` se actualizó para garantizar que, cuando hay muchos hijos (por ejemplo >=10), el bloque de hijos comience claramente por debajo del nodo padre y se aumente la separación vertical entre filas para evitar solapamientos.
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` — `expandCompetencies`
- Por qué: Evitar que los nodos hijos queden demasiado cerca o solapen con el padre en vistas con muchos elementos; mejora legibilidad y evita recenter inesperado.
- Fecha: 2026-01-22
- Metadata Git:
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a

### Implementación: Estilo "Burbuja" para nodos (ScenarioPlanning)

- **Qué:** Se actualizó la representación visual de los nodos principales en `ScenarioPlanning/Index.vue` para que las esferas parezcan burbujas (gradiente radial más pronunciado, reflejo especular y ribete sutil). Esto mejora la legibilidad y la sensación de profundidad.
- **Por qué:** El aspecto de "burbuja" facilita identificar nodos principales y su estado crítico, además de alinearse con las mejoras visuales propuestas en el PrototypeMap.
- **Fecha:** 2026-01-21
- **Archivos modificados:** `src/resources/js/pages/ScenarioPlanning/Index.vue`
- **Metadata Git:**
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a

Nota: Este cambio es puramente visual (SVG/defs/CSS). La lógica D3 y el layout no han sido alterados. Si deseas que aplique el mismo tratamiento a las `child-nodes`, lo hago en la siguiente iteración.

## Acción técnica relacionada: typings D3

- Se instaló `@types/d3` localmente en `src` (devDependency) para eliminar aviso de "No se encontró ningún archivo de declaración para el módulo 'd3'".
- Si TypeScript sigue reportando errores, alternativa rápida: agregar `src/types/d3.d.ts` con `declare module 'd3';`.

## Tests añadidos (2026-01-28)

- **CapabilityUpdateTest**: nuevo archivo de pruebas backend en `src/tests/Feature/CapabilityUpdateTest.php` con dos tests:
  - `test_update_capability_entity_via_api`: PATCH a `/api/capabilities/{id}` y aserciones en la tabla `capabilities`.
  - `test_update_scenario_capability_pivot_via_api`: crea asociación inicial y PATCH a `/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}` para actualizar campos pivot en `scenario_capabilities`.

Estas pruebas fueron añadidas para cubrir la edición/actualización de registros de capacidades y sus atributos de escenario (pivot).

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

- Memoria detallada de la sesión de 2026-01-22: [docs/MEMORY_ScenarioPlanning_2026-01-22.md](docs/MEMORY_ScenarioPlanning_2026-01-22.md)

- Estado: memoria creada en `docs/MEMORY_ScenarioPlanning_2026-01-22.md` (confirmado 2026-01-22).

## Implementación registrada: Navegación por niveles (matriz 2x5)

- **Qué:** Añadida lógica de navegación por niveles en el mapa de `ScenarioPlanning`:
  - La vista raíz ahora muestra el `scenario` y hasta 10 capacidades dispuestas en una matriz de 2 filas x 5 columnas.
  - Al seleccionar una capacidad, el nodo seleccionado se centra horizontalmente y se posiciona verticalmente al 25% del lienzo; los demás nodos de nivel 1 se ocultan (se ponen `display:none`) y se mantiene visible el nodo `scenario`.
  - La expansión de competencias (nivel 2) ahora está limitada a 10 nodos y se dispone en matriz 2x5 debajo del nodo seleccionado.
  - Comportamiento análogo para profundizar un nivel más (nivel 3): oculta nodos no seleccionados y muestra únicamente el padre y sus hijos.
- **Dónde:** `src/resources/js/pages/ScenarioPlanning/Index.vue` (modificación de `expandCompetencies`, `handleNodeClick`) y nuevo helper `src/resources/js/composables/useNodeNavigation.ts` (`computeMatrixPositions`).
- **Por qué:** UX consistente, reduce saturación visual y proporciona una navegación predecible por niveles.
- **Fecha:** 2026-01-25

## Estrategia de testing (registrada)

- **Qué:** Decisión de testing integrada en el proyecto.
- **Stack de pruebas:**
  - Backend: `Pest` (PHP) — ya en uso para pruebas de API y lógica del servidor.
    - Nota: las pruebas backend usan **Pest**, no **PHPUnit**; los tests están escritos con sintaxis Pest/PHP.
  - Frontend unit/integration: `Vitest` + `@vue/test-utils` para composables y componentes Vue.
  - Frontend E2E/funcionales: `Playwright` para pruebas end-to-end (multi-navegador) — cobertura de flujos complejos (D3 interactions, drag/drop, centering, sidebar).
- **Enfoque:** Desarrollo orientado por pruebas (TDD) cuando sea práctico: empezar por tests unitarios/componente para la lógica (`useNodeNavigation`, `expandCompetencies`) y luego añadir pruebas E2E con Playwright para flujos críticos (ej. crear/adjuntar/centrar/guardar).
- **Notas operativas:**
  - Usar `msw` para mocks en pruebas de componentes cuando levantar el servidor resulte costoso.
  - Para E2E se usará `npm run dev` en entorno local o un server de pruebas con datos seed; Playwright tests aceptan `BASE_URL` para apuntar a diferentes servidores.
  - Añadir pasos a CI para ejecutar: `composer test` (Pest), `npm run test:unit` (Vitest), `npm run test:e2e` (Playwright headless). Preferir Playwright oficial images/actions en CI.

  ### Metodología de testing - Memoria del proyecto

  Esta entrada documenta la metodología acordada para las pruebas frontend-backend en `oahumada/Stratos` y debe ser consultada al diseñar nuevos tests o pipelines de CI.
  - Propósito: asegurar que el frontend envía los payloads y headers esperados, que el backend pasa sus pruebas unitarias/feature (Pest) y que los flujos E2E críticos están cubiertos.
  - Alcance: cubrir componentes UI críticos (formularios, modal create/attach, diagram interactions), composables (p. ej. `useNodeNavigation`), y flujos completos (create → attach → center → save).
  - Stack recomendado:
    - Backend: Pest (PHP) — ya usado para pruebas CRUD.
    - Frontend unit/integration: Vitest + @vue/test-utils + msw (para mocks de red en tests de componentes).
    - Frontend E2E: Playwright (usar `BASE_URL` para apuntar a servidores de prueba).
  - Orden de ejecución en CI: 1) `composer test` (Pest) → 2) `npm run test:unit` (Vitest) → 3) `npm run test:e2e` (Playwright headless).
  - Buenas prácticas:
    - Usar DB de pruebas seedada para E2E o mockear respuestas en tests de componentes.
    - Interceptar y validar solicitudes en E2E (Playwright) para comprobar body y headers.
    - Evitar datos frágiles en pruebas; usar fixtures y limpiar estado entre tests.
    - Validar payloads/inputs en backend y no confiar en validaciones cliente.
    - Documentar en `docs/` los endpoints y shapes esperados para facilitar tests contractuales.

  > Nota: esta metodología ya se registró internamente como preferencia del proyecto y puede ser persistida en la memoria del equipo para referencia futura.

## Memoria: Sesión 2026-01-23

- **Resumen corto:** Implementé el endpoint backend para asignar competencias a capacidades por escenario (`capability_competencies`) que acepta `competency_id` o crea una nueva `competency` y la asocia, creé la migración/modelo para la pivot, añadí tests Feature que cubren ambos flujos y verifiqué que los tests pasan localmente.
- **Archivos clave modificados/añadidos:**
  - `src/routes/api.php` — POST `/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies` (lógica transaccional, tenant checks, manejo de duplicados).
  - `src/app/Models/CapabilityCompetency.php` — nuevo modelo para pivot.
  - `src/database/migrations/2026_01_23_120000_add_positions_to_scenario_capabilities_table.php` — agregó `position_x/position_y/is_fixed` a `scenario_capabilities`.
  - `src/database/migrations/2026_01_23_121000_create_capability_competencies_table.php` — nueva tabla `capability_competencies`.
  - `src/tests/Feature/CapabilityCompetencyTest.php` — tests para: adjuntar competencia existente; crear nueva competencia + pivot en transacción.

- **Comprobaciones realizadas:**
  - Ejecuté los tests del nuevo archivo y pasaron: `php artisan test tests/Feature/CapabilityCompetencyTest.php` (2 tests, 8 assertions) en el entorno de desarrollo local del repo.

- **Decisiones y reglas aplicadas:**
  - El endpoint opera en transacción (crea la `competency` si se entrega `competency` payload, o usa `competency_id` si se entrega).
  - Verificación multitenant: se comprueba `organization_id` del `scenario` y de la `competency` nueva/existente antes de asociar.
  - Prevención de duplicados: verifica existencia en `capability_competencies` antes de insertar; si existe devuelve la fila existente.

- **Próximos pasos guardados (para mañana):**
  1. Ejecutar migraciones en el entorno dev y validar end-to-end (actualizar posiciones desde UI y comprobar `scenario_capabilities`):

     ```bash
     cd src
     php artisan migrate
     npm run dev   # si es necesario reconstruir assets
     ```

  2. Implementar la UI (modal/select) en `src/resources/js/pages/ScenarioPlanning/Index.vue` para: seleccionar competencia existente o crear una nueva y llamar al endpoint transaccional.
  3. Añadir validaciones/autorization finales y pruebas E2E pequeñas (Playwright/Pest) para el flujo completo.

- **Metadata:**
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `fecha`: 2026-01-23

---

Registro creado automáticamente para dejar el estado listo para continuar mañana.

## Cambio reciente: Migración de flags de animación/visibilidad en ScenarioPlanning/Index.vue

- **Qué:** Se migraron los flags legacy `__scale`, `__opacity`, `__filter`, `__delay`, `__hidden`, `__displayNone`, `__targetX/Y` a campos explícitos del modelo de nodo: `animScale`, `animOpacity`, `animFilter`, `animDelay`, `animTargetX`, `animTargetY` y `visible`.
- **Dónde:** `src/resources/js/pages/ScenarioPlanning/Index.vue` (plantilla y funciones `expandCompetencies`, `showOnlySelectedAndParent`, y manejadores de click).
- **Por qué:** Normalizar campos facilita bindings CSS, evita errores por acceso a propiedades inexistentes en template y prepara la migración completa de animaciones a propiedades del modelo.
- **Fecha:** 2026-01-26
- **Metadata Git:** branch `feature/workforce-planning-scenario-modeling` (ediciones locales durante sesión).

## Implementación registrada: Auto-attach de `Capability` a `Scenario` (pivot)

- **Qué:** Al crear una nueva `Capability` que tenga `discovered_in_scenario_id`, el modelo ahora inserta automáticamente una fila en la tabla pivot `scenario_capabilities` (si no existe) con valores por defecto (`strategic_role='target'`, `strategic_weight=10`, `priority=1`, `required_level=3`, `is_critical=false`). La relación también se crea explícitamente desde la ruta API que guarda la capacidad desde el nodo del escenario.
- **Dónde:** `src/app/Models/Capability.php` — se añadió `protected static function booted()` con un listener `created` que realiza la inserción segura (verifica existencia antes de insertar). El listener sólo actúa cuando `discovered_in_scenario_id` está presente; la ruta API que crea la capacidad desde el nodo también inserta el registro en `scenario_capabilities` con los campos de relación provistos por la petición.
- **Por qué:** Centralizar el comportamiento asegura que todas las rutas/repositorios/seeders que creen `Capability` con `discovered_in_scenario_id` o `type='pro'` resulten en la relación correcta en `scenario_capabilities` sin duplicar lógica en múltiples lugares.
- **Impacto:** El seeder y rutas que ya crean capacidades quedan cubiertos; la inserción respeta la restricción única (`scenario_id, capability_id`) y maneja errores con logging.
- **Fecha:** 2026-01-22
- **Metadata Git:**
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: (local edit)

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
- `src/resources/js/components/brain/BrainCanvas.vue` — Componente referenciado en la guía (implementación con D3).
- Nota: la guía se movió a `docs/GUIA_STRATOS_CEREBRO.txt`.
- `docs/GUIA_STRATOS_CEREBRO.txt` — Guía de implementación del "Cerebro Stratos" (inspirada en TheBrain).

### Memoria: Workforce Planning / Scenario Planning

- **Última actualización:** 14 Enero 2026
- **Status:** Módulo integrado (UI + API). Fuente canónica: [docs/memories_workforce_planning.md](docs/memories_workforce_planning.md#L1).
- **Resumen:** WFP centraliza creación y comparación de escenarios (what-if) con plantillas (IA Adoption, Digital Transformation, Rapid Growth, Succession Planning).
- **Rutas UI:** `/workforce-planning` → `WorkforcePlanning/ScenarioSelector.vue`; `/workforce-planning/{id}` → `OverviewDashboard.vue`.
- **APIs clave (resumen):**
  - `GET    //api/workforce-planning/scenario-templates`
  - `POST   //api/workforce-planning/workforce-scenarios/{template_id}/instantiate-from-template`
  - `POST   //api/workforce-planning/workforce-scenarios/{id}/calculate-gaps`
  - `POST   //api/workforce-planning/workforce-scenarios/{id}/refresh-suggested-strategies`
  - `POST   //api/workforce-planning/scenario-comparisons`
  - `GET    //api/workforce-planning/workforce-scenarios/{id}`
  - `GET    //api/workforce-planning/workforce-scenarios/{id}/role-forecasts`
  - `GET    //api/workforce-planning/workforce-scenarios/{id}/skill-gaps`
  - `POST   //api/workforce-planning/workforce-scenarios/{id}/analyze`
- **Quick-steps (Postman - 5 min):** instanciar template → `calculate-gaps` → `refresh-suggested-strategies` → `scenario-comparisons` → revisar detalle.
- **Notas de integración:** `AppSidebar.vue` ya incluye el link; rutas registradas (`workforce-planning.index`, `workforce-planning.show`). Mantener `POSTMAN_VALIDATION_5MIN.md` como guía rápida.
- **Recomendación:** Añadir E2E (Playwright) para el flujo create→calculate→suggest→compare y migrar stores a Pinia según `WORKFORCE_PLANNING_UI_INTEGRATION.md`.

#### Renombramiento del módulo

- **Qué:** El módulo originalmente llamado `WorkForce Planning` fue renombrado a `ScenarioPlanning` para enfatizar la creación y modelamiento de escenarios (what-if), y alinear el nombre con la UX y las páginas actuales.
- **Por qué:** El nombre `ScenarioPlanning` comunica mejor el propósito principal: modelado y comparación de escenarios, plantillas y análisis de brechas.
- **Fecha:** 2026-01-21
- **Metadata Git:**
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a

Esta entrada sirve como referencia para nombres de rutas, directorios y componentes que podrían contener la forma antigua (`workforce-planning`) y deben considerarse para actualizaciones futuras.

## Búsquedas iniciales realizadas (Phase 1)

- Confirmadas referencias a `BrainCanvas.vue` y uso del mapa: `PrototypeMap` es `Index.vue`.
- Detectada presencia de logs y build assets que incluyen `BrainCanvas.vue` (ver `public/build/manifest.json`).

## Implementación registrada: Mejora visual PrototypeMap

- Qué: mejoras visuales en el mapa de capacidades para mayor legibilidad y jerarquía visual.
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` (sustitución de `svg` con `defs` para gradientes, filtro de sombra, clases CSS scoped y animación `pulse` para nodos críticos).
- Decisión clave: mantener la lógica D3 existente; usar `defs` SVG para estilos visuales (gradiente radial + sombra); no cambiar API ni persistencia.
- Archivos modificados: Index.vue (visual + ligeras señales `is_critical` en nodos), openmemory.md (registro).

### Comportamiento: Mostrar Guardar/Reset sólo cuando hay cambios

- Qué: Añadida bandera reactiva `positionsDirty` para mostrar los botones `Guardar` y `Reset` únicamente cuando el usuario ha movido nodos (posiciones sin guardar).
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` — se añadió `positionsDirty = ref(false)`, se marca `true` durante el arrastre (`onPointerMove`) y se limpia (`false`) tras guardar o resetear posiciones.
- Por qué: Reducir ruido en la interfaz y evitar acciones innecesarias cuando no hay cambios.
- Fecha: 2026-01-22
- Archivos modificados: `src/resources/js/pages/ScenarioPlanning/Index.vue`

### Ajuste: Empujar hijos hacia abajo cuando hay >=10 nodos

- Qué: En `Index.vue` la función `expandCompetencies` se actualizó para garantizar que, cuando hay muchos hijos (por ejemplo >=10), el bloque de hijos comience claramente por debajo del nodo padre y se aumente la separación vertical entre filas para evitar solapamientos.
- Dónde: `src/resources/js/pages/ScenarioPlanning/Index.vue` — `expandCompetencies`
- Por qué: Evitar que los nodos hijos queden demasiado cerca o solapen con el padre en vistas con muchos elementos; mejora legibilidad y evita recenter inesperado.
- Fecha: 2026-01-22
- Metadata Git:
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a

### Implementación: Estilo "Burbuja" para nodos (ScenarioPlanning)

- **Qué:** Se actualizó la representación visual de los nodos principales en `ScenarioPlanning/Index.vue` para que las esferas parezcan burbujas (gradiente radial más pronunciado, reflejo especular y ribete sutil). Esto mejora la legibilidad y la sensación de profundidad.
- **Por qué:** El aspecto de "burbuja" facilita identificar nodos principales y su estado crítico, además de alinearse con las mejoras visuales propuestas en el PrototypeMap.
- **Fecha:** 2026-01-21
- **Archivos modificados:** `src/resources/js/pages/ScenarioPlanning/Index.vue`
- **Metadata Git:**
  - `git_repo_name`: oahumada/Stratos
  - `git_branch`: feature/workforce-planning-scenario-modeling
  - `git_commit_hash`: c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a

Nota: Este cambio es puramente visual (SVG/defs/CSS). La lógica D3 y el layout no han sido alterados. Si deseas que aplique el mismo tratamiento a las `child-nodes`, lo hago en la siguiente iteración.

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

- Memoria detallada de la sesión de 2026-01-22: [docs/MEMORY_ScenarioPlanning_2026-01-22.md](docs/MEMORY_ScenarioPlanning_2026-01-22.md)

- Estado: memoria creada en `docs/MEMORY_ScenarioPlanning_2026-01-22.md` (confirmado 2026-01-22).

---

## Implementación registrada: Suite de Tests para Capability-Competency Integration (2026-01-29)

**Qué:** Se expandió y mejoró significativamente la suite de tests `CapabilityCompetencyTest.php` para validar toda la integración frontend-backend de creación y gestión de competencias dentro de una capability.

**Tests añadidos (9 total):**

1. CREATE - Vincular competencia existente
2. CREATE - Nueva competencia desde capability
3. CREATE - Todos los campos se guardan
4. CREATE - Valores por defecto
5. CREATE - Prevenir duplicados
6. SECURITY - Multi-tenancy
7. UPDATE - Modificar relación
8. DELETE - Eliminar relación
9. SECURITY - DELETE bloqueado por org

**Estadísticas:**

- Tests: **9 passing**
- Assertions: **38 total**
- Duration: **4.17s**

**Documentación creada:**

1. `docs/GUIA_TESTS_CAPABILITY_COMPETENCY.md` - Guía detallada de cada test con patrones reutilizables
2. `docs/DEBUG_TESTS_CAPABILITY_COMPETENCY.md` - Troubleshooting y herramientas de debugging

**Metadata:**

- `git_repo_name`: oahumada/Stratos
- `git_branch`: feature/workforce-planning-scenario-modeling
- Fecha: 2026-01-29

## Fix: Competency Edit Modal - Saving not persisting (2026-01-29)

### Problema raíz identificado

El modal de edición de Competencias NO guardaba cambios. Causas múltiples:

1. **Endpoint faltante:** Frontend intentaba `PATCH /api/competencies/{id}` que NO existía
   - Solo existía: `PATCH /api/strategic-planning/scenarios/{scenarioId}/capabilities/{parentId}/competencies/{compId}` (para pivot)
   - Faltaba: Endpoint independiente para actualizar la competencia misma (name, description, skills)

2. **Campo no guardable:** `readiness` es **calculado dinámicamente** en el backend, no una columna en BD
   - No existe en tabla `competencies`
   - Se calcula llamando `calculateCompetencyReadiness()` en el controlador `getCapabilityTree()`
   - El frontend intentaba guardar este campo, pero no puede existir en la tabla

3. **Falta de logging:** Los errores PATCH se ocultaban con `catch (err) { void err; }` sin logs, imposibilitando debug

### Soluciones implementadas

**Backend:** `src/routes/api.php`

- ✅ Creado endpoint `GET /api/competencies/{id}` — obtiene competencia con datos frescos
- ✅ Creado endpoint `PATCH /api/competencies/{id}` — actualiza `name`, `description`, `skills` (rechaza `readiness`)
- ✅ Ambos endpoints incluyen validación multi-tenant y manejo de errores explícito

**Frontend:** `src/resources/js/pages/ScenarioPlanning/Index.vue`

- ✅ Mejorado `saveSelectedChild()` con logs de debug en cada paso (payload, PATCH call, response)
- ✅ Removido `readiness` del payload de competencia (`editChildReadiness` es solo-lectura)
- ✅ Actualizado error handling para mostrar mensajes específicos al usuario
- ✅ Ahora solo envía campos editables: `name`, `description`, `skills`

### Archivos modificados

1. `src/routes/api.php` — Agregó GET + PATCH para competencias (31 líneas)
2. `src/resources/js/pages/ScenarioPlanning/Index.vue` — Mejoró `saveSelectedChild()` con logs y payload correcto

### Validación

✅ `npm run lint` — Sin errores sintácticos
✅ Logs en consola confirman que PATCH se ejecuta exitosamente

### Comportamiento después del fix

1. Usuario edita nombre/descripción en modal de competencia
2. Hace click "Guardar"
3. `saveSelectedChild()` llama `PATCH /api/competencies/{compId}` con `{ name, description, skills }`
4. Endpoint valida org y actualiza tabla
5. Luego refresca árbol y merge de datos frescos
6. Modal muestra cambios actualizados sin requerir refresh manual

### Aprendizaje clave

**Campos calculados vs persistidos:** Readiness es una **métrica calculada** (como un índice), no un **campo almacenado**. Esto es el diseño correcto: permite que readiness se recalcule automáticamente a partir de datos frescos sin mantener denormalización.

**Endpoint granularidad:** Fue necesario crear dos niveles de endpoints:

- `PATCH /api/competencies/{id}` — Actualizar entidad (guardable)
- `PATCH /api/.../competencies/{compId}` — Actualizar pivot/relación (atributos escenario-específicos)

**Metadata:**

- `git_repo_name`: oahumada/Stratos
- `git_branch`: feature/workforce-planning-scenario-modeling
- `git_commit_hash`: 61baa7e9 (commit posterior al lint)
- Fecha: 2026-01-29

## Implementación: Layout Radial para Competencias y Skills (2026-01-29)

### Qué se implementó

Layout radial adaptativo para distribuir nodos competencia y skills sin solapamiento cuando hay muchos:

**Competencias:**

- **>5 nodos con uno seleccionado** → Radial (seleccionado en centro, otros distribuidos semicírculo inferior)
- **≤5 nodos** → Matriz tradicional

**Skills:**

- **>4 skills** → Radial (distribuido en semicírculo abajo de competencia)
- **≤4 skills** → Lineal (fila simple)

### Características clave

✅ **Primer clic funciona:** `selectedChild.value` se asigna ANTES de `expandCompetencies` para que detecte selección inmediatamente

✅ **Evita traslapes:** Competencias usan radio 240px, skills 160px

✅ **Respeta jerarquía visual:** Nodos no aparecen arriba tapando padre, solo abajo/lados

✅ **Espacio para anidación:** Competencia seleccionada se desplaza 40px abajo para que skills entren debajo

✅ **Configuración centralizada:** Objeto `LAYOUT_CONFIG` (línea ~662) con todos los parámetros tunables

### Parámetros principales

```javascript
LAYOUT_CONFIG.competency.radial = {
  radius: 240, // Distancia competencias no-seleccionadas
  selectedOffsetY: 40, // Espacio vertical para skills
  startAngle: -Math.PI / 4, // -45° (bottom-left)
  endAngle: (5 * Math.PI) / 4, // 225° (bottom-right, sin top)
};

LAYOUT_CONFIG.skill.radial = {
  radius: 160, // Distancia skills de competencia
  offsetY: 120, // Espacio vertical desde competencia
  startAngle: -Math.PI / 6, // -30°
  endAngle: (7 * Math.PI) / 6, // 210° (2/3 inferior)
};
```

### Archivos modificados

1. `src/resources/js/pages/ScenarioPlanning/Index.vue`
   - Línea ~662: `LAYOUT_CONFIG` (nueva)
   - Función `expandCompetencies`: Layout radial + matrix
   - Función `expandSkills`: Layout radial + linear
   - Handler click competencias: `selectedChild` antes de expand

2. `docs/LAYOUT_CONFIG_SCENARIO_PLANNING_GUIDE.md` (nueva)
   - Guía completa de ajuste
   - Ejemplos de valores
   - Tips de debugging

### Validación

✅ `npm run lint` — Sin errores
✅ Visual en navegador — Layout radial activo en primer clic
✅ Sin traslapes — Competencias y skills bien distribuidas

### Cómo probar cambios

1. Abre `src/resources/js/pages/ScenarioPlanning/Index.vue`
2. Ubica `const LAYOUT_CONFIG = {` (línea ~662)
3. Ajusta valores (ej: `radius: 240 → 280`)
4. Guarda archivo
5. Navegador recarga automáticamente (Vite)
6. Expande capacidad con 10+ competencias y selecciona una

### Valores recomendados por escenario

| Escenario       | Competency.radius | Skill.radius | Skill.offsetY |
| --------------- | ----------------- | ------------ | ------------- |
| Compacto        | 180               | 120          | 100           |
| Normal (actual) | 240               | 160          | 120           |
| Amplio          | 300               | 200          | 140           |

### Aprendizajes clave

1. **Orden de ejecución importa:** `selectedChild` debe actualizarse ANTES de `expandCompetencies` para que el layout radial lo detecte en el primer clic

2. **Ángulos para evitar traslapes:** Usar semicírculo inferior (-45° a 225°) evita que nodos tapen el padre arriba

3. **Anidación requiere espacio:** `selectedOffsetY` debe ser positivo (40-80) para dejar espacio a las skills debajo

4. **Centralización reduce bugs:** Todos los parámetros en un solo objeto facilita iteración y testing sin tocar lógica

**Metadata:**

- `git_repo_name`: oahumada/Stratos
- `git_branch`: feature/workforce-planning-scenario-modeling
- `git_commit_hash`: (local edits)
- Fecha: 2026-01-29

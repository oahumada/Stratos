# Memoria: Animaciones y sincronización de ScenarioPlanning (UI)

- **Qué:** Implementación de sincronización y mejoras de animación en `src/resources/js/pages/ScenarioPlanning/Index.vue`.
- **Por qué:** Evitar animaciones residuales y mejorar la percepción de jerarquía/relación entre nodo padre → hijo → nieto; lograr que los nietos "emergan" orgánicamente desde su nodo padre.
- **Fecha:** 2026-01-22

## Cambios clave

- Añadido `waitForTransitionForNode(nodeId)` que escucha `transitionend` en el DOM (con timeout fallback) y constantes `TRANSITION_MS`/lead-time para sincronizar animaciones.
- `handleNodeClick` convertido a `async` y ahora coordina: centrar nodo padre → esperar (race entre `transitionend` y lead timeout) → `expandCompetencies`.
- `centerOnNode` limpia inmediatamente `childNodes`/`childEdges` al iniciar transición a un nodo distinto (evita animaciones residuales).
- `expandCompetencies` crea `childNodes` inicialmente en la posición del padre (`initX/initY`) y en un siguiente tick los mueve a sus targets para disparar CSS transitions.
- Añadidos atributos `data-node-id` a grupos SVG (`scenarioNode`, nodos principales y `childNodes`) para poder escuchar `transitionend` de elementos concretos.
- Animación orgánica de entrada para hijos/nietos:
  - Inicial: `scale` reducido, `opacity` 0, `filter: blur(6px)+drop-shadow`.
  - Entrada: movimiento a target + `filter` → `none`, `opacity` → 1, `scale` → 1.06 (overshoot).
  - Settling: tras 160ms la `scale` baja a 1 y se limpian props auxiliares; ligero jitter aleatorio en `transitionDelay` para evitar patrón mecánico.
- CSS: `.node-group` ahora transiciona `transform`, `opacity` y `filter` para soportar el efecto suave.
- Etiquetas y legibilidad:
  - `wrapLabel(...)` revisada: ahora produce como máximo 2 líneas y trunca con elipsis la segunda línea si excede longitud (`max` por defecto 14).
  - Etiqueta del `scenarioNode` movida encima del nodo (`y=-48`), tamaño aumentado a `16px` y `font-weight:700`.

## Archivos modificados

- `src/resources/js/pages/ScenarioPlanning/Index.vue`

## Notas

- Se priorizó una solución basada en CSS transitions + DOM `transitionend` en vez de JavaScript animation frames para mantener compatibilidad y simplicidad.
- Para reducir la latencia perceptual entre padre→hijos se usa un `Promise.race` entre `transitionend` y un lead-time (60% de `TRANSITION_MS` para padres/nodos, 50% para hijos), lo que adelanta el inicio de la animación cuando es seguro.
- Se limpian inmediatamente `childNodes` al iniciar una nueva transición para evitar que elementos antiguos compitan visualmente con la nueva disposición.

## Metadata Git (capturada)

- `git_repo_name`: oahumada/Stratos
- `git_branch`: feature/workforce-planning-scenario-modeling
- `git_commit_hash`: fadb6204f7511a1953bcdfccd966e53f277316ed

---

Esta entrada documenta el trabajo del día para referencia futura (ajustes de UX/animación, puntos de comprobación para pruebas visuales y pasos de QA). Si quieres, puedo crear también una entrada formal en el sistema de memorias (add-memory) con tipo `implementation` y metadatos.

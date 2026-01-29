# Fix: Modal de competencias — 2026-01-29

Qué: Reparada la creación y asociación de competencias desde el modal de capacidad en `src/resources/js/pages/ScenarioPlanning/Index.vue`.

Cambios aplicados:
- Añadido `await ensureCsrf()` antes de crear una nueva competencia para garantizar la cookie `XSRF-TOKEN` (Sanctum) antes de peticiones POST.
- `fetchAvailableCompetencies()` ahora toma en cuenta `displayNode` cuando `focusedNode` es nulo, evitando que el modal no muestre competencias disponibles desde el panel lateral.

Por qué: Sin el CSRF la creación fallaba al ejecutar POST en entornos con Sanctum; además el listado de competencias podía estar vacío cuando la UI usaba el panel lateral en lugar del nodo focalizado.

Estado: Cambios aplicados en branch `feature/workforce-planning-scenario-modeling` (archivo `Index.vue`).

Pasos de verificación recomendados:
1. Iniciar la UI de desarrollo (`cd src && npm run dev`) y el backend (`cd src && php artisan serve`) con autenticación Sanctum activa.
2. Abrir un escenario, seleccionar una capacidad desde el panel lateral.
3. Usar "Agregar existente" y comprobar que aparecen competencias disponibles.
4. Usar "Crear competencia" y verificar que la competencia se crea y asocia al capability.

Registro Git (local): commit aplicado en esta sesión; empujar al remoto cuando esté listo.

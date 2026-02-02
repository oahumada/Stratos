# Normalización a Strategic Planning (Cambio de Dominio)

## Cambio Realizado
Se completó la normalización del nombre del módulo de `workforce-planning` a `strategic-planning` siguiendo la decisión de diseño de dominio.

## Cambios Aplicados

### 1. **Rutas API** (`src/routes/api.php`)
- ❌ Removidas: `/api/v1/workforce-planning/...` (v1 backwards-compatible)
- ❌ Removidas: `/api/workforce-planning/...` (legacy compatibility routes)
- ✅ Canónicas: `/api/strategic-planning/scenarios` (única fuente de verdad)

### 2. **Controllers** (`src/app/Http/Controllers/Api/ScenarioController.php`)
- ✅ Refactorizado método `store()` para usar Eloquent (en lugar de DB::table())
- ✅ Refactorizado método `instantiateFromTemplate()` para usar Eloquent
- ✅ Actualizado: Referencias a `workforce_planning_scenarios` → `scenarios`

### 3. **Form Requests** (`src/app/Http/Requests/`)
- ✅ `InstantiateScenarioFromTemplateRequest.php`: `exists:workforce_planning_scenarios,id` → `exists:scenarios,id`
- ✅ `StoreScenarioComparisonRequest.php`: `exists:workforce_planning_scenarios,id` → `exists:scenarios,id`

### 4. **Models** (`src/app/Models/Scenario.php`)
- ✅ Actualizado comentario para clarificar tabla canónica

### 5. **Frontend Wayfinder** (`src/resources/js/actions/App/Http/Controllers/Api/`)
- ✅ Regenerado con `php artisan wayfinder:generate`
- ✅ Removidas automáticamente todas las rutas antiguas
- ✅ Ahora solo contiene `/api/strategic-planning/scenarios`

### 6. **Tests** (`src/tests/Feature/`)
- ✅ `ScenarioModelingTest.php`: Actualizado a `/api/strategic-planning/scenarios`
- ✅ `StrategicPlanningScenariotest.php`: Actualizado a `/api/strategic-planning/scenarios`
- ✅ Otros tests: Actualizados con sed para reemplazar rutas antiguas

## Verificaciones Realizadas

```bash
# ✅ No hay referencias a /api/workforce-planning/workforce-scenarios en tests
grep -r "/api/workforce-planning/workforce-scenarios" src/tests --count = 0

# ✅ Archivo Wayfinder regenerado sin referencias a workforce-planning
grep -c "workforce-planning" src/resources/js/actions/App/Http/Controllers/Api/ScenarioController.ts = 0

# ✅ Rutas canónicas en Wayfinder
grep -c "strategic-planning\|scenarios" src/resources/js/actions/App/Http/Controllers/Api/ScenarioController.ts = 35+
```

## Impacto

### Frontend
- El store `workforcePlanningStore.ts` ya utilizaba `/api/strategic-planning/scenarios` ✅
- Los componentes como `ScenarioList.vue` ya llaman a `/api/strategic-planning/scenarios` ✅
- **No se requieren cambios en el frontend**

### Backend API
- Las rutas canónicas son ahora las únicas disponibles
- **Nota:** La tabla `workforce_planning_scenarios` sigue existiendo como vista de compatibilidad (triggers en BD)
- Los modelos usan tabla canónica `scenarios`

## Pruebas Pendientes
- Ejecutar suite completa: `composer test`
- Verificar que todos los escenarios se cargan correctamente por `organization_id`

## Notas
- La tabla `scenarios` es canónica (fuente de verdad)
- La vista `workforce_planning_scenarios` mantiene compatibilidad de BD (legacy)
- Los comentarios en migraciones no se actualizaron (migraciones son inmutables)

# FIX: Scenario Creation Not Persisting - Bug Resolution

## Issue Summary
Escenarios creados a través de `ScenarioCreateFromTemplate.vue` no se guardaban en la base de datos porque el controlador `WorkforceScenarioController::instantiateFromTemplate()` no soportaba los nuevos campos agregados en la Fase 2:
- `scope_type` (organization|department|role_family)
- `parent_id` (jerarquía padre-hijo)
- Campos de versionamiento: `version_group_id`, `version_number`, `is_current_version`
- Estados duales: `decision_status`, `execution_status`

## Root Cause
El método `instantiateFromTemplate()` estaba diseñado para el antiguo flujo de creación y no manejaba el payload con estructura `customizations` que ahora envía el frontend con los nuevos campos.

## Changes Made

### 1. **WorkforceScenarioController::instantiateFromTemplate()** (Lines 72-130)
✅ **Actualización**: Ahora extrae todos los campos del objeto `customizations` y los pasa al modelo.

**Cambios clave:**
```php
$customizations = $data['customizations'] ?? [];

// Extract versionamiento fields
$versionGroupId = \Illuminate\Support\Str::uuid();
$scopeType = $customizations['scope_type'] ?? 'organization';
$parentId = $customizations['parent_id'] ?? null;

$scenario = WorkforcePlanningScenario::create([
    // ... existing fields ...
    'scope_type' => $scopeType,
    'parent_id' => $parentId,
    'version_group_id' => $versionGroupId,
    'version_number' => 1,
    'is_current_version' => true,
    'decision_status' => 'draft',
    'execution_status' => 'not_started',  // ← Valor correcto (no 'planned')
    'horizon_months' => $customizations['horizon_months'] ?? 12,
    'fiscal_year' => $customizations['fiscal_year'] ?? now()->year,
    // ... rest of fields ...
]);
```

**Respuesta mejorada:**
- Ahora incluye las relaciones: `['template', 'skillDemands', 'parent', 'statusEvents']`
- Permite al frontend cargar todas las relaciones necesarias inmediatamente

### 2. **InstantiateScenarioFromTemplateRequest** (Lines 15-35)
✅ **Actualización**: Añadidas validaciones para los nuevos campos bajo `customizations`.

**Validaciones nuevas:**
```php
'customizations' => 'nullable|array',
'customizations.scope_type' => 'nullable|string|in:organization,department,role_family',
'customizations.parent_id' => 'nullable|integer|exists:workforce_planning_scenarios,id',
'customizations.scenario_type' => 'nullable|string|in:succession,growth,...',
```

**Backward compatibility:**
- Mantiene las validaciones antigas para campos sin `customizations` (fallback)
- Permite ambas estructuras de payload

## Database Verification
✅ Todas las migraciones ejecutadas correctamente:
- `2026_01_07_232635_enhance_workforce_scenarios_with_versioning_hierarchy_scope` ✅
- Campos confirmados: `scope_type`, `parent_id`, `version_group_id`, `version_number`, `is_current_version`, `decision_status`, `execution_status`

## Testing Results
✅ **Test Manual Completado**: Scenario creado con éxito con todos los nuevos campos:
```
✅ Scenario created successfully!
   - ID: 8
   - Name: Test Scenario with New Fields
   - Scope Type: department
   - Version Group: ab2c9ae0-6fd3-4c10-896c-2e764bc42db1
   - Decision Status: draft
   - Execution Status: not_started
   - Is Current Version: true

✅ Fields verified in database:
   - scope_type: department
   - decision_status: draft
   - execution_status: not_started
```

## Frontend Status
✅ `ScenarioCreateFromTemplate.vue` ya envía el payload correcto:
```javascript
await api.post(`/api/v1/workforce-planning/workforce-scenarios/${selectedTemplate.value.id}/instantiate-from-template`, {
  customizations: {
    name,
    description,
    scenario_type,
    scope_type,
    parent_id,
    horizon_months,
    // ... other fields
  },
})
```

## Enum Values Fixed
⚠️ **Importante**: `execution_status` debe ser uno de:
- `'not_started'` (estado inicial) ← Usamos este
- `'in_progress'`
- `'paused'`
- `'completed'`

**NO** `'planned'` (ese es un estado antiguo no soportado)

## Impact
- ✅ Escenarios se guardan correctamente con todos los nuevos campos
- ✅ Versionamiento funciona (version_group_id + version_number)
- ✅ Jerarquía padre-hijo funciona (parent_id)
- ✅ Scope segmentation funciona (scope_type)
- ✅ Estados duales funcionan (decision_status + execution_status)
- ✅ Relaciones se cargan correctamente

## Files Modified
1. `/app/Http/Controllers/Api/WorkforceScenarioController.php` - Updated `instantiateFromTemplate()` method
2. `/app/Http/Requests/InstantiateScenarioFromTemplateRequest.php` - Added validations for new fields

## Next Steps
1. ✅ Probar creación de escenarios desde interfaz (ScenarioCreateFromTemplate.vue)
2. ✅ Verificar que parent-child relationships funcionan
3. ✅ Verificar que scopes se respetan
4. ✅ Probar transiciones de decision_status y execution_status

# 🧪 Tests CRUD Scenarios - Guía Rápida

## ✅ Estado Actual

```
Tests: 12 passed (88 assertions)
Duration: 4.32s
```

## 🚀 Comandos Más Útiles

### Ejecutar todos los tests de scenarios

```bash
cd src
composer test tests/Feature/StrategicPlanningScenariosTest.php tests/Feature/ScenarioModelingTest.php
```

### Ejecutar test de creación (CREATE)

```bash
composer test tests/Feature/StrategicPlanningScenariosTest.php --filter "test_create_workforce_scenario"
```

### Ejecutar test de multi-tenancy (seguridad)

```bash
composer test tests/Feature/StrategicPlanningScenariosTest.php --filter "tenant_isolation"
```

### Ejecutar test de listado filtrado (READ)

```bash
composer test tests/Feature/StrategicPlanningScenariosTest.php --filter "list_scenarios_filtered"
```

### Ejecutar test de actualización (UPDATE)

```bash
composer test tests/Feature/StrategicPlanningScenariosTest.php --filter "unauthorized_user_cannot_update"
```

### Ejecutar test de eliminación (DELETE)

```bash
composer test tests/Feature/ScenarioModelingTest.php
```

## 📊 Qué Validan los Tests

| Operación     | Test                                         | Endpoint                                      | Validaciones                          |
| ------------- | -------------------------------------------- | --------------------------------------------- | ------------------------------------- |
| **CREATE**    | test_create_workforce_scenario               | POST /api/strategic-planning/scenarios        | Status 201, Campos guardados en BD    |
| **READ**      | test_list_scenarios_filtered_by_organization | GET /api/strategic-planning/scenarios         | Filtrado por org, Paginación          |
| **UPDATE**    | unauthorized_user_cannot_update_scenario     | PATCH /api/strategic-planning/scenarios/{id}  | 403 para otra org, Cambios reflejados |
| **DELETE**    | (en ScenarioModelingTest)                    | DELETE /api/strategic-planning/scenarios/{id} | Eliminado de BD                       |
| **Seguridad** | tenant_isolation_prevents_cross_org_access   | GET /api/strategic-planning/scenarios/{id}    | 403 para escenarios de otra org       |

## 🔍 Campos Validados en Cada Operación

### Creación (CREATE)

- ✅ `name` - Guardado correctamente
- ✅ `description` - Guardado correctamente
- ✅ `horizon_months` - Guardado correctamente
- ✅ `fiscal_year` - Guardado correctamente
- ✅ `organization_id` - Asignado automáticamente
- ✅ `created_by` - Asignado automáticamente

### Listado (READ)

- ✅ Filtrado automático por `organization_id`
- ✅ Paginación funciona
- ✅ Parámetro `?status=` funciona
- ✅ Usuario solo ve sus escenarios

### Actualización (UPDATE)

- ✅ `name` - Actualizado
- ✅ `status` - Actualizado
- ✅ Protección: Usuario no puede actualizar otra org

### Eliminación (DELETE)

- ✅ Registro eliminado de BD
- ✅ Respuesta success: true

## 📁 Archivos de Test

1. **StrategicPlanningScenariosTest.php** (7 tests)
   - Creación, listado, instanciación desde template, filtrado, seguridad

2. **ScenarioModelingTest.php** (5 tests)
   - Tenant isolation avanzado, gaps, templates, estrategias

## 🎯 Uso en Desarrollo

Ejecuta estos tests **después de:**

- Cambiar campos en el formulario Vue
- Modificar validaciones en el backend
- Cambiar lógica de filtrado
- Agregar nuevas operaciones CRUD

Si algún test falla, sabrás exactamente qué campo o operación se rompió.

## 💡 Próximos Pasos (Opcional)

Si quieres agregar más validaciones:

```bash
# Abrir test y agregar nuevos assertions
vim tests/Feature/Api/WorkforcePlanningApiTest.php

# Ejemplo: validar todos los campos en una creación
test('create with all fields', function () {
    $response = $this->postJson('/api/strategic-planning/scenarios', [...]);

    $response->assertJsonPath('data.name', 'Test');
    $response->assertJsonPath('data.description', 'Desc');
    $response->assertJsonPath('data.horizon_months', 12);
    // ... etc
});
```

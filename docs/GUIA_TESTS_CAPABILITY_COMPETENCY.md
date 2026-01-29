# Guía: Testing Competency-Capability Integration

## Resumen Ejecutivo

Esta guía explica cómo validar que cuando **creas una competencia desde una capability**, los datos se guardan correctamente en **dos tablas**:

1. `competencies` - La competencia misma
2. `capability_competencies` - La relación entre capability y competencia (pivot table)

**Ubicación de los tests:** [`src/tests/Feature/CapabilityCompetencyTest.php`](../src/tests/Feature/CapabilityCompetencyTest.php)

**Ejecución:**

```bash
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php
```

**Resultado esperado:** ✅ 9 tests pasando, 38 assertions

---

## Estructura del Test File

Los 9 tests están organizados en **6 categorías**:

### 1. ✅ CREATE - Vincular competencia existente

**Test:** `test_attach_existing_competency_creates_pivot()`

**Qué valida:**

- El usuario puede usar `competency_id` para vincular una competencia que ya existe
- Se crea la relación en `capability_competencies` con los campos correctos

**Código:**

```php
$response = $this->actingAs($this->user)
    ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
        'competency_id' => $competency->id,
        'required_level' => 4,
    ]);

$response->assertStatus(201); // ✅ 201 Created
$this->assertDatabaseHas('capability_competencies', [
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $competency->id,
    'required_level' => 4,
]);
```

**Aprendizaje:**

- Envía `competency_id` para usar competencia existente
- Retorna `201 Created`
- Verifica con `assertDatabaseHas()` que el registro existe en BD

---

### 2. ✅ CREATE - Nueva competencia desde capability

**Test:** `test_create_new_competency_and_pivot_in_transaction()`

**Qué valida:**

- El usuario puede crear una **nueva competencia** enviando un objeto `competency` con `name` y `description`
- Se crea tanto en `competencies` como en `capability_competencies`
- La operación es **atómica** (transaction)

**Código:**

```php
$response = $this->actingAs($this->user)
    ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
        'competency' => [
            'name' => 'New Comp',
            'description' => 'Created via API'
        ],
        'required_level' => 2,
    ]);

// Validar que competencia se creó
$this->assertDatabaseHas('competencies', [
    'organization_id' => $this->organization->id,
    'name' => 'New Comp',
]);

// Validar que relación se creó
$comp = Competency::where('name', 'New Comp')->first();
$this->assertDatabaseHas('capability_competencies', [
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
    'required_level' => 2,
]);
```

**Aprendizaje:**

- Envía `competency` object con `name` y `description` para crear nuevo
- Necesitas verificar **dos tablas** (`competencies` + `capability_competencies`)
- Busca el competency_id del competencia creada para validar el pivot

---

### 3. ✅ CREATE - Todos los campos se guardan

**Test:** `test_all_fields_saved_when_creating_competency()`

**Qué valida:**

- Todos los campos opcionales se persisten correctamente en `capability_competencies`:
  - `weight` (numérico)
  - `rationale` (texto)
  - `is_required` (boolean)

**Campos validados:**

```php
[
    'required_level' => 5,      // ✅ saved
    'weight' => 95,             // ✅ saved
    'rationale' => 'Critical...', // ✅ saved
    'is_required' => true,      // ✅ saved
]
```

**Código clave - Validación de cada campo:**

```php
$pivot = CapabilityCompetency::where([
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $competency->id,
])->first();

$this->assertEquals(5, $pivot->required_level);
$this->assertEquals(95, $pivot->weight);
$this->assertEquals('Critical for modern infrastructure', $pivot->rationale);
$this->assertTrue($pivot->is_required);
```

**Aprendizaje:**

- Usa `CapabilityCompetency::where()` para obtener el pivot record
- Valida cada campo con `assertEquals()` o `assertTrue()` según tipo

---

### 4. ✅ CREATE - Valores por defecto

**Test:** `test_default_values_when_fields_omitted()`

**Qué valida:**

- Cuando NO envías ciertos campos, qué valores por defecto se asignan:

| Campo            | Valor por defecto | Tipo     |
| ---------------- | ----------------- | -------- |
| `required_level` | `3`               | int      |
| `weight`         | `null`            | nullable |
| `rationale`      | `null`            | nullable |
| `is_required`    | `false`           | boolean  |

**Código:**

```php
// Payload mínimo
$response = $this->actingAs($this->user)
    ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
        'competency' => ['name' => 'API Design'],
        // ⚠️ No incluyo: weight, rationale, is_required
    ]);

// Verificar defaults
$pivot = CapabilityCompetency::where('competency_id', $competency->id)->first();
$this->assertEquals(3, $pivot->required_level);
$this->assertNull($pivot->weight);
$this->assertNull($pivot->rationale);
$this->assertFalse($pivot->is_required);
```

**Aprendizaje:**

- `required_level` es **requerido** - NO tiene default
- Otros campos son opcionales
- `is_required` por defecto es `false` (no obligatorio)

---

### 5. ✅ CREATE - Prevenir duplicados

**Test:** `test_prevent_duplicate_relationship()`

**Qué valida:**

- Si intentas crear la **misma relación dos veces**, el sistema:
  - 1ª vez: Retorna `201 Created`
  - 2ª vez: Retorna `200 OK` con `note: 'already_exists'`
  - NO crea duplicate en BD

**Código:**

```php
// Primer intento
$response1 = $this->actingAs($this->user)
    ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
        'competency_id' => $comp->id,
        'required_level' => 3,
    ]);
$response1->assertStatus(201); // ✅ Created

// Segundo intento - MISMO competency_id
$response2 = $this->actingAs($this->user)
    ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
        'competency_id' => $comp->id,
        'required_level' => 3,
    ]);
$response2->assertStatus(200); // ✅ OK (no error)
$response2->assertJsonPath('note', 'already_exists');

// Validar que solo existe UNA relación en BD
$count = CapabilityCompetency::where([
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
])->count();
$this->assertEquals(1, $count); // ✅ No duplicates
```

**Aprendizaje:**

- El sistema es **idempotente** - puedes llamar 2 veces sin riesgo
- Usa `assertJsonPath()` para validar notas en la respuesta
- Usa `count()` para verificar no hay duplicados en BD

---

### 6. ✅ MULTI-TENANCY - Bloquear acceso cross-org

**Test:** `test_cannot_create_competency_in_different_org()`

**Qué valida:**

- Un usuario de la organización A **NO puede** crear competencias en una capability de la organización B

**Código:**

```php
$otherOrg = Organizations::factory()->create();
$otherOrgScenario = Scenario::create(['organization_id' => $otherOrg->id, ...]);
$otherOrgCap = Capability::create(['organization_id' => $otherOrg->id, ...]);

// User de org A intenta acceder a org B
$response = $this->actingAs($this->user)
    ->postJson("/api/strategic-planning/scenarios/{$otherOrgScenario->id}/capabilities/{$otherOrgCap->id}/competencies", [
        'competency' => ['name' => 'Hacked Competency'],
    ]);

$response->assertStatus(403); // ✅ Forbidden
```

**Aprendizaje:**

- Siempre valida multi-tenancy con `assertStatus(403)`
- Crea recursos en otra org con `Organizations::factory()->create()`

---

### 7. ✅ UPDATE - Modificar relación

**Test:** `test_update_capability_competency_fields()`

**Qué valida:**

- El usuario puede actualizar los campos de una relación **existente**

**Campos actualizables:**

- `required_level`
- `weight`
- `rationale`
- `is_required`

**Código:**

```php
// Crear relación INICIAL
CapabilityCompetency::create([
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
    'required_level' => 2,
    'weight' => 40,
    'rationale' => 'Initial',
    'is_required' => false,
]);

// Actualizar
$response = $this->actingAs($this->user)
    ->patchJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies/{$comp->id}", [
        'required_level' => 5,
        'weight' => 90,
        'rationale' => 'Updated',
        'is_required' => true,
    ]);

$response->assertStatus(200); // ✅ OK

// Verificar cambios
$pivot = CapabilityCompetency::where('competency_id', $comp->id)->first();
$this->assertEquals(5, $pivot->required_level);
$this->assertEquals(90, $pivot->weight);
```

**Aprendizaje:**

- Usa `PATCH` con `patchJson()` para actualizar
- El `competency_id` va en la URL (ruta), no en el body
- Verifica cambios consultando BD después

---

### 8. ✅ DELETE - Eliminar relación

**Test:** `test_delete_capability_competency_relationship()`

**Qué valida:**

- El usuario puede eliminar la relación de `capability_competencies`
- **Pero la competencia NO se elimina** de `competencies`

**Código:**

```php
// Crear relación
CapabilityCompetency::create([
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
    'required_level' => 3,
]);

// Eliminar
$response = $this->actingAs($this->user)
    ->deleteJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies/{$comp->id}");

$response->assertStatus(200);

// ✅ Relación eliminada
$this->assertDatabaseMissing('capability_competencies', [
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
]);

// ✅ Pero competencia SIGUE existiendo
$this->assertDatabaseHas('competencies', [
    'id' => $comp->id,
    'name' => 'Delete Me Comp',
]);
```

**Aprendizaje:**

- Usa `deleteJson()` para enviar DELETE request
- Usa `assertDatabaseMissing()` para verificar que se eliminó
- Competencias NO se eliminan en cascada

---

### 9. ✅ SECURITY - Bloquear DELETE cross-org

**Test:** `test_cannot_delete_other_org_relationship()`

**Qué valida:**

- Un usuario de otra organización **NO puede** eliminar relaciones

**Código:**

```php
$otherOrg = Organizations::factory()->create();
$otherOrgUser = User::factory()->create(['organization_id' => $otherOrg->id]);

// Relación de mi org
CapabilityCompetency::create([...]);

// User de otra org intenta eliminar
$response = $this->actingAs($otherOrgUser)
    ->deleteJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies/{$comp->id}");

$response->assertStatus(403); // ✅ Forbidden
```

---

## Patrones de Testing Reutilizables

### Patrón 1: Validar creación en BD

```php
$this->assertDatabaseHas('table_name', [
    'field1' => $value1,
    'field2' => $value2,
]);
```

### Patrón 2: Validar campos específicos

```php
$record = Model::where('id', $id)->first();
$this->assertEquals('expected', $record->field);
$this->assertTrue($record->boolean_field);
```

### Patrón 3: Validar multi-tenancy

```php
$response = $this->actingAs($userFromOrgA)
    ->postJson('/api/...$resourceFromOrgB->id...');
$response->assertStatus(403);
```

### Patrón 4: Validar relaciones (pivots)

```php
$pivot = CapabilityCompetency::where([
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
])->first();
$this->assertNotNull($pivot);
```

### Patrón 5: Validar que NO se creó

```php
$this->assertDatabaseMissing('table_name', [
    'field' => 'value',
]);
```

---

## Endpoints Testeados

| Método     | Ruta                                                                                                     | Qué hace                                     | HTTP Response                |
| ---------- | -------------------------------------------------------------------------------------------------------- | -------------------------------------------- | ---------------------------- |
| **POST**   | `/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies`                | Crear nueva competencia O vincular existente | `201` (new) / `200` (exists) |
| **PATCH**  | `/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}` | Actualizar relación                          | `200`                        |
| **DELETE** | `/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}` | Eliminar relación                            | `200`                        |

---

## Ejecución y Resultados

**Ejecutar todos los tests:**

```bash
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php
```

**Resultado esperado:**

```
  PASS  Tests\Feature\CapabilityCompetencyTest
  ✓ attach existing competency creates pivot           3.93s
  ✓ create new competency and pivot in transaction     0.02s
  ✓ all fields saved when creating competency          0.03s
  ✓ default values when fields omitted                 0.03s
  ✓ prevent duplicate relationship                     0.02s
  ✓ cannot create competency in different org          0.02s
  ✓ update capability competency fields                0.02s
  ✓ delete capability competency relationship          0.02s
  ✓ cannot delete other org relationship               0.02s

  Tests:    9 passed (38 assertions)
  Duration: 4.17s
```

---

## Cómo Extender Estos Tests

### Caso 1: Validar que no se puede actualizar `competency_id`

```php
public function test_cannot_change_competency_id_on_update()
{
    // Crear relación con competency A
    CapabilityCompetency::create([...competency_id => 1...]);

    // Intentar cambiar a competency B
    $response = $this->actingAs($this->user)
        ->patchJson("/.../competencies/1", [
            'competency_id' => 2, // ⚠️ Should be ignored or rejected
        ]);

    // Verificar que competency_id NO cambió
    $pivot = CapabilityCompetency::where('id', $pivot->id)->first();
    $this->assertEquals(1, $pivot->competency_id);
}
```

### Caso 2: Validar validaciones de entrada

```php
public function test_required_level_must_be_between_1_and_5()
{
    $response = $this->actingAs($this->user)
        ->postJson("/.../competencies", [
            'competency' => ['name' => 'Test'],
            'required_level' => 10, // ⚠️ Invalid
        ]);

    $response->assertStatus(422); // Unprocessable Entity
}
```

### Caso 3: Validar cascada de eliminaciones

```php
public function test_deleting_scenario_deletes_all_relationships()
{
    // Crear scenario con capabilities con competencias
    $scenario->delete();

    // Verificar que todas las relaciones se eliminaron
    $this->assertDatabaseMissing('capability_competencies', [
        'scenario_id' => $scenario->id,
    ]);
}
```

---

## Conexión con el Flujo Frontend-Backend

### Frontend (Vue 3 + TypeScript)

```typescript
// Crear nueva competencia
const response = await apiClient.post(
  `/strategic-planning/scenarios/${scenarioId}/capabilities/${capabilityId}/competencies`,
  {
    competency: { name: "Cloud Architecture", description: "..." },
    required_level: 4,
    weight: 80,
    rationale: "Critical for...",
    is_required: true,
  },
);

// Backend (Laravel)
// - Crea en `competencies` table
// - Crea en `capability_competencies` pivot
// - Retorna 201 con datos
```

### Backend Response

```json
{
  "success": true,
  "data": {
    "scenario_id": 1,
    "capability_id": 2,
    "competency_id": 3,
    "required_level": 4,
    "weight": 80,
    "rationale": "Critical for...",
    "is_required": true,
    "created_at": "2026-01-29T10:30:00Z"
  }
}
```

---

## Resumen de Aprendizajes Clave

✅ **Dos tablas afectadas:**

- `competencies` - El registro maestro de la competencia
- `capability_competencies` - La relación (pivot)

✅ **Dos formas de crear:**

- `competency_id` - Vincular existente
- `competency` object - Crear nueva

✅ **Validación es crítica:**

- Multi-tenancy (403 para cross-org)
- Duplicados (200 con `already_exists`)
- Tipos de datos (`required_level`, `weight`, `is_required`)

✅ **Operaciones CRUD todas testeadas:**

- CREATE (2 tests - new & existing)
- READ (implícito en BD validations)
- UPDATE (1 test)
- DELETE (1 test + security)

✅ **Seguridad:**

- Cada test tiene su contrapartida de multi-tenancy
- 403 Forbidden es la defensa principal

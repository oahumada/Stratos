# Debugging Tests: Troubleshooting Competency-Capability Integration

## Problema: Test falla con `Database Missing` error

### Síntoma

```
Failed asserting that a record in the "capability_competencies" table was found...
Expected: [...]
```

### Causas comunes y soluciones

#### 1. El endpoint no creó la relación en BD

**Checklist:**

```php
// ❌ INCORRECTO: Solo verificas respuesta HTTP
$response->assertStatus(201);

// ✅ CORRECTO: También verifica BD
$response->assertStatus(201);
$this->assertDatabaseHas('capability_competencies', [
    'scenario_id' => $scenario->id,
    'capability_id' => $cap->id,
    'competency_id' => $comp->id,
]);
```

**Solución:**

- Añade `dd()` en el controlador para ver qué se está pasando
- Verifica que `CapabilityCompetency::create()` está siendo llamado
- Revisa que no hay excepciones silenciosas

#### 2. El `competency_id` no es el esperado

**Código de debugging:**

```php
// Después de crear competencia
$competency = Competency::where('name', 'New Comp')->first();
dd('Competency ID:', $competency->id); // Ver qué ID tiene

// En el test
$this->assertDatabaseHas('capability_competencies', [
    'competency_id' => $competency->id, // ✅ Usar ID dinámico, no hardcoded
]);
```

#### 3. La relación se creó pero con campos vacíos o defectuosos

**Test manual en DB:**

```bash
cd src && php artisan tinker
```

```php
// Verificar qué se guardó
CapabilityCompetency::latest()->first();

// Ver específicamente campos
$pivot = CapabilityCompetency::latest()->first();
echo "required_level: " . $pivot->required_level . "\n";
echo "weight: " . $pivot->weight . "\n";
echo "rationale: " . $pivot->rationale . "\n";
```

---

## Problema: Test falla con validación (422 Unprocessable)

### Síntoma

```
Expected response status code 201, but received 422
```

### Causas comunes y soluciones

#### 1. Payload incorrecto

**Verificar:**

```php
// ❌ INCORRECTO: competency es string
'competency' => 'Cloud Architecture'

// ✅ CORRECTO: competency es array
'competency' => ['name' => 'Cloud Architecture']
```

#### 2. Faltan campos requeridos

**Campos REQUERIDOS:**

- `required_level` (int: 1-5) → **OBLIGATORIO**

**Campos OPCIONALES:**

- `competency` OR `competency_id` (una de las dos)
- `weight` (null OK)
- `rationale` (null OK)
- `is_required` (defaults to false)

**Debug:**

```php
// Ver errores de validación
$response = $this->actingAs($this->user)
    ->postJson('/api/.../competencies', $badPayload);

if ($response->status() === 422) {
    dd($response->json('errors')); // Ver errores específicos
}
```

#### 3. Validación rechaza el valor

**Ej: `required_level` debe estar entre 1-5**

```php
// ❌ INVALID
'required_level' => 10

// ✅ VALID
'required_level' => 5
```

---

## Problema: Test falla con 403 Forbidden (pero esperaba 201)

### Síntoma

```
Expected response status code 201, but received 403
```

### Causas comunes y soluciones

#### 1. Usuario no autenticado

```php
// ❌ INCORRECTO: Sin Sanctum
$response = $this->postJson('/api/.../competencies', [...]);

// ✅ CORRECTO: Con actingAs
$response = $this->actingAs($this->user)
    ->postJson('/api/.../competencies', [...]);
```

#### 2. Escenario o Capability pertenece a otra org

```php
// ⚠️ El test verifica esto explícitamente
public function test_cannot_create_competency_in_different_org()
{
    $otherOrg = Organizations::factory()->create();
    $otherScenario = Scenario::create(['organization_id' => $otherOrg->id]);

    // ❌ User de org A intenta acceder scenario de org B = 403
    $response = $this->actingAs($this->user) // User de org 1
        ->postJson("/.../scenarios/{$otherScenario->id}/...");

    $response->assertStatus(403); // ✅ Esperado
}
```

**Verificar:** El user y el escenario están en la misma `organization_id`

---

## Problema: Test falla con 404 Not Found

### Síntoma

```
Expected response status code 201, but received 404
```

### Causas comunes y soluciones

#### 1. Scenario no existe

```php
// Crear scenario correctamente
$scenario = Scenario::create([
    'organization_id' => $this->organization->id,
    'name' => 'Test Scenario',
    'horizon_months' => 6,
    'fiscal_year' => 2026,
    'created_by' => $this->user->id,
]);

// Usar ID correcto en la ruta
->postJson("/api/strategic-planning/scenarios/{$scenario->id}/...");
```

#### 2. Capability no existe o es de otra org

```php
// ✅ CORRECTO
$cap = Capability::create([
    'organization_id' => $this->organization->id,
    'name' => 'Test Cap',
]);

// ✅ Usar ID en ruta
->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies");
```

#### 3. Ruta no existe

```php
// Verifica la ruta exacta en api.php
// Debe ser: /api/strategic-planning/scenarios/{id}/capabilities/{id}/competencies

// ❌ INCORRECTO (typo)
->postJson("/api/strategic-planning/scenario/{$scenario->id}/...")

// ✅ CORRECTO (plural)
->postJson("/api/strategic-planning/scenarios/{$scenario->id}/...")
```

---

## Problema: Assertion falla sobre campos en BD

### Síntoma

```
Failed asserting that a record in the "capability_competencies" table was found...
Expected: ['required_level' => 5, 'weight' => 95]
```

### Causas comunes y soluciones

#### 1. Los valores se guardaron pero con tipo distinto

```php
// ❌ INCORRECTO: Comparar string con int
$this->assertDatabaseHas('capability_competencies', [
    'required_level' => '5', // String ❌
]);

// ✅ CORRECTO: Tipo correcto
$this->assertDatabaseHas('capability_competencies', [
    'required_level' => 5, // Int ✅
]);
```

#### 2. El campo es NULL pero esperabas valor

```php
// Si no mandas 'weight', se guarda NULL
$response = $this->actingAs($this->user)
    ->postJson('/api/.../competencies', [
        'competency' => ['name' => 'Test'],
        // ❌ No incluyo weight
    ]);

// Weight es NULL
$pivot = CapabilityCompetency::latest()->first();
dd($pivot->weight); // NULL

// ✅ Verifica correctamente
$this->assertNull($pivot->weight);
```

#### 3. Valores booleanos se guardan como 0/1 en MySQL

```php
// Cuando guardas true/false en MySQL, se convierte a 1/0
$this->assertDatabaseHas('capability_competencies', [
    'is_required' => true, // Puedes usar true/false
]);

// También puedes verificar así
$pivot = CapabilityCompetency::latest()->first();
$this->assertTrue($pivot->is_required); // Convierte 1 a true
$this->assertFalse($pivot->is_required); // Convierte 0 a false
```

---

## Herramientas de Debugging

### 1. Ejecutar un test específico

```bash
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php --filter=test_all_fields_saved_when_creating_competency
```

### 2. Ver output detallado del test

```bash
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php --debug
```

### 3. Parar el test en un punto específico

```php
public function test_something()
{
    // ... código ...

    dd($response->json()); // ⏸️ Para aquí y muestra JSON

    // ... resto del código no se ejecuta ...
}
```

### 4. Ver qué se guardó en BD durante el test

```php
public function test_something()
{
    // ... crear datos ...

    $response = $this->actingAs($this->user)
        ->postJson('/api/.../competencies', [
            'competency' => ['name' => 'Test'],
        ]);

    // Ver qué se guardó
    $pivot = CapabilityCompetency::latest()->first();
    dd($pivot->toArray()); // ⏸️ Ver todos los campos
}
```

### 5. Usar Tinker para consultar BD

```bash
cd src && php artisan tinker
```

```php
# Ver últimos competencies
Competency::latest()->take(5)->get();

# Ver últimas relaciones
CapabilityCompetency::latest()->take(5)->get();

# Ver una relación específica
CapabilityCompetency::find(1)->toArray();

# Ver qué competencias tiene una capability
Capability::find(1)->competencies;
```

---

## Checklist para nuevo test

Si escribes un nuevo test, verifica:

```
[ ] Organizaciónl, user, scenario, capability están creados
[ ] User es autenticado con actingAs()
[ ] Payload es correcto (array, no string)
[ ] Campos requeridos están presentes (required_level)
[ ] Ruta exacta coincide con api.php
[ ] Método HTTP es correcto (POST, PATCH, DELETE)
[ ] Status code es esperado (201, 200, 403, 404, 422)
[ ] assertDatabaseHas verifica AMBAS tablas si aplica
[ ] Tipos de datos coinciden (int, string, boolean)
[ ] Multi-tenancy test incluye otra org
```

---

## Ejecución de suite completa

```bash
# Ejecutar TODOS los tests del proyecto
cd src && php artisan test

# Ejecutar SOLO capability-competency tests
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php

# Ejecutar con cobertura
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php --coverage

# Ejecutar un test específico
cd src && php artisan test tests/Feature/CapabilityCompetencyTest.php --filter=test_all_fields_saved
```

---

## Recursos útiles

- **Tests location:** [`src/tests/Feature/CapabilityCompetencyTest.php`](../../src/tests/Feature/CapabilityCompetencyTest.php)
- **API endpoint:** [`src/routes/api.php`](../../src/routes/api.php) (busca `competencies`)
- **Controller:** [`src/app/Http/Controllers/Api/CapabilityCompetencyController.php`](../../src/app/Http/Controllers/Api/CapabilityCompetencyController.php)
- **Models:**
  - [`src/app/Models/Capability.php`](../../src/app/Models/Capability.php)
  - [`src/app/Models/Competency.php`](../../src/app/Models/Competency.php)
  - [`src/app/Models/CapabilityCompetency.php`](../../src/app/Models/CapabilityCompetency.php)

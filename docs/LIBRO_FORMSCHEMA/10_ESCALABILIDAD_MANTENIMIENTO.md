# Capítulo 10: Escalabilidad y Mantenimiento

**Duración de lectura:** 25 minutos  
**Nivel:** Avanzado  
**Conceptos clave:** Growth, Testing, DevOps, Lecciones aprendidas

---

## Introducción: De MVP a Producción

Strato demostró que FormSchema Pattern escala de MVP (Days 1-7) a sistema completo. Este capítulo documenta cómo mantener la calidad mientras creces.

---

## 1. Agregar Nuevo Modelo: Checklist Paso a Paso

### Escenario: Agregar modelo "Certification"

### Paso 1: Crear Migraci\u00f3n (5 min)

```bash
php artisan make:migration create_certifications_table
```

```php
// database/migrations/[timestamp]_create_certifications_table.php
Schema::create('certifications', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('provider'); // AWS, Google, etc
    $table->date('expires_at')->nullable();
    $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
    
    // Índices para búsqueda y filtrado
    $table->index('provider');
    $table->index('level');
});
```

### Paso 2: Crear Modelo (2 min)

```bash
php artisan make:model Certification
```

```php
// app/Models/Certification.php
class Certification extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'description',
        'provider',
        'expires_at',
        'level',
        'created_by',
    ];
    
    protected $casts = [
        'expires_at' => 'date',
    ];
    
    // Importante para búsqueda
    public function getSearchableColumns(): array
    {
        return ['name', 'provider', 'description'];
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Relation con People (many-to-many)
    public function people()
    {
        return $this->belongsToMany(People::class, 'people_certification');
    }
}
```

### Paso 3: Registrar en form-schema-complete.php (1 min)

```php
// routes/form-schema-complete.php

$formSchemaModels = [
    'People' => 'people',
    'Certification' => 'certifications',  // ← Agregar aquí
    'Role' => 'roles',
    'Skill' => 'skills',
];

// ✅ Automáticamente: 8 rutas generadas
// ✅ FormSchemaController soporta Certification
// ✅ GenericRepository funciona
```

Verificar:
```bash
php artisan route:list | grep certifications
# Debe mostrar 8 rutas: GET, POST, PUT, PATCH, DELETE, etc.
```

### Paso 4: Crear Configuración JSON (5 min)

```bash
mkdir -p resources/js/pages/Certification/certifications-form
touch resources/js/pages/Certification/{Index,Show}.vue
touch resources/js/pages/Certification/certifications-form/{config,tableConfig,itemForm,filters}.json
```

```json
// resources/js/pages/Certification/certifications-form/config.json
{
  "model": "Certification",
  "apiEndpoint": "/api/certifications",
  "title": "Certificaciones",
  "singularName": "Certificación",
  "pluralName": "Certificaciones"
}
```

```json
// tableConfig.json
{
  "columns": [
    { "key": "id", "label": "ID", "type": "number", "width": "80px" },
    { "key": "name", "label": "Nombre", "type": "text", "width": "200px" },
    { "key": "provider", "label": "Proveedor", "type": "text", "width": "150px" },
    { "key": "level", "label": "Nivel", "type": "status", "width": "120px" },
    { "key": "expires_at", "label": "Vence", "type": "date", "width": "120px" },
    { "key": "created_at", "label": "Creado", "type": "date", "width": "120px" }
  ]
}
```

```json
// itemForm.json
{
  "fields": [
    {
      "key": "name",
      "label": "Nombre",
      "type": "text",
      "required": true,
      "rules": ["required"]
    },
    {
      "key": "provider",
      "label": "Proveedor",
      "type": "select",
      "required": true,
      "options": [
        { "value": "AWS", "label": "Amazon Web Services" },
        { "value": "Google", "label": "Google Cloud" },
        { "value": "Azure", "label": "Microsoft Azure" },
        { "value": "Coursera", "label": "Coursera" }
      ]
    },
    {
      "key": "level",
      "label": "Nivel",
      "type": "select",
      "required": true,
      "options": [
        { "value": "beginner", "label": "Principiante" },
        { "value": "intermediate", "label": "Intermedio" },
        { "value": "advanced", "label": "Avanzado" }
      ]
    },
    {
      "key": "description",
      "label": "Descripción",
      "type": "textarea",
      "required": false,
      "rows": 3
    },
    {
      "key": "expires_at",
      "label": "Fecha de Vencimiento",
      "type": "date",
      "required": false
    }
  ]
}
```

```json
// filters.json
{
  "fields": [
    {
      "key": "search",
      "label": "Buscar",
      "type": "text",
      "placeholder": "Nombre, proveedor..."
    },
    {
      "key": "provider",
      "label": "Proveedor",
      "type": "select",
      "options": [
        { "value": "AWS", "label": "AWS" },
        { "value": "Google", "label": "Google Cloud" },
        { "value": "Azure", "label": "Azure" }
      ],
      "clearable": true
    },
    {
      "key": "level",
      "label": "Nivel",
      "type": "select",
      "options": [
        { "value": "beginner", "label": "Principiante" },
        { "value": "intermediate", "label": "Intermedio" },
        { "value": "advanced", "label": "Avanzado" }
      ],
      "clearable": true
    }
  ]
}
```

### Paso 5: Crear Componente (2 min)

```vue
<!-- resources/js/pages/Certification/Index.vue -->
<template>
  <AppLayout title="Certificaciones">
    <FormSchema :config="config" />
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '@/components/FormSchema.vue';
import configJson from './certifications-form/config.json';

const config = ref(configJson);
</script>
```

### Paso 6: Registrar Ruta Web (1 min)

```php
// routes/web.php
Route::get('/certifications', [CertificationController::class, 'index'])->name('certifications.index');

// app/Http/Controllers/CertificationController.php
class CertificationController extends Controller
{
    public function index()
    {
        return inertia('Certification/Index');
    }
}
```

### Paso 7: Agregar al Sidebar (1 min)

```vue
<!-- resources/js/components/AppSidebar.vue -->
<v-list-item 
  to="/certifications" 
  title="Certificaciones"
  prepend-icon="mdi-certificate"
/>
```

### Paso 8: Run Migration (1 min)

```bash
php artisan migrate
```

✅ **NUEVO CRUD COMPLETO EN 15-20 MINUTOS**

---

## 2. Testing Strategy

### Nivel 1: Unit Tests (Controller)

```php
// tests/Feature/FormSchemaControllerTest.php

use Tests\TestCase;
use App\Models\Certification;

class FormSchemaControllerTest extends TestCase
{
    /** @test */
    public function it_lists_certifications()
    {
        Certification::factory()->count(5)->create();
        
        $response = $this->getJson('/api/certifications');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'provider', 'level']
                     ],
                     'meta' => ['current_page', 'total', 'per_page']
                 ]);
    }
    
    /** @test */
    public function it_creates_certification()
    {
        $data = [
            'name' => 'AWS Solutions Architect',
            'provider' => 'AWS',
            'level' => 'advanced',
        ];
        
        $response = $this->postJson('/api/certifications', $data);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('certifications', $data);
    }
    
    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/certifications', []);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'provider', 'level']);
    }
    
    /** @test */
    public function it_searches_certifications()
    {
        Certification::factory()->create(['name' => 'AWS Solutions']);
        Certification::factory()->create(['name' => 'Google Associate']);
        
        $response = $this->postJson('/api/certifications/search', [
            'query' => 'AWS'
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }
}
```

### Nivel 2: Integration Tests (API)

```php
// tests/Feature/CertificationIntegrationTest.php

class CertificationIntegrationTest extends TestCase
{
    private $certification;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->certification = Certification::factory()->create();
    }
    
    /** @test */
    public function crud_workflow()
    {
        // CREATE
        $createResponse = $this->postJson('/api/certifications', [
            'name' => 'New Cert',
            'provider' => 'AWS',
            'level' => 'beginner',
        ]);
        $id = $createResponse->json('id');
        
        // READ
        $this->getJson("/api/certifications/{$id}")
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'New Cert');
        
        // UPDATE
        $this->putJson("/api/certifications/{$id}", [
            'name' => 'Updated Cert',
        ])->assertStatus(200);
        
        // DELETE
        $this->deleteJson("/api/certifications/{$id}")
            ->assertStatus(204);
        
        // VERIFY DELETED
        $this->getJson("/api/certifications/{$id}")
            ->assertStatus(404);
    }
}
```

### Nivel 3: E2E Tests (Cypress/Playwright)

```javascript
// tests/e2e/certification.cy.js

describe('Certification CRUD', () => {
    beforeEach(() => {
        cy.login();
        cy.visit('/certifications');
    });
    
    it('should list certifications', () => {
        cy.get('table').should('be.visible');
        cy.get('tbody tr').should('have.length.greaterThan', 0);
    });
    
    it('should create certification', () => {
        cy.get('button:contains("New")').click();
        cy.get('input[name="name"]').type('AWS Cert');
        cy.get('select[name="provider"]').select('AWS');
        cy.get('button:contains("Save")').click();
        
        cy.contains('AWS Cert').should('be.visible');
    });
    
    it('should edit certification', () => {
        cy.get('button[aria-label="edit"]').first().click();
        cy.get('input[name="name"]').clear().type('Updated Name');
        cy.get('button:contains("Save")').click();
        
        cy.contains('Updated Name').should('be.visible');
    });
    
    it('should delete certification', () => {
        cy.get('button[aria-label="delete"]').first().click();
        cy.get('button:contains("Yes")').click();
        
        cy.contains('success').should('be.visible');
    });
});
```

---

## 3. Performance Optimization

### Database Optimization

```php
// Check N+1 queries
php artisan tinker
>>> DB::enableQueryLog();
>>> $people = People::with('department', 'skills')->get();
>>> dd(DB::getQueryLog());

// Agregar índices según usage
Schema::table('people', function (Blueprint $table) {
    $table->index('status');           // Filtrados frecuentes
    $table->index('department_id');    // Relación
    $table->index('created_at');       // Ordenamientos
    $table->fullText(['name', 'email']); // Búsqueda full-text
});
```

### Caché Estratégico

```php
// CertificationRepository.php
public function all()
{
    return Cache::remember(
        'certifications:all',
        3600, // 1 hora
        function () {
            return $this->model
                ->with(['creator'])
                ->orderBy('name')
                ->get();
        }
    );
}

// Invalidar caché en cambios
public function create(array $data)
{
    Cache::forget('certifications:all');
    return parent::create($data);
}

public function update($id, array $data)
{
    Cache::forget('certifications:all');
    Cache::forget("certification:{$id}");
    return parent::update($id, $data);
}
```

### API Response Optimization

```php
// Usar fractal o spatie/laravel-fractal para transformaciones
class CertificationTransformer extends Transformer
{
    public function transform(Certification $certification)
    {
        return [
            'id' => $certification->id,
            'name' => $certification->name,
            'provider' => $certification->provider,
            'level' => $certification->level,
        ];
    }
}

// En controller:
public function index(Request $request, string $modelName)
{
    $items = $this->repository->paginate(15);
    
    // Transformar para reducir payload
    return response()->json(
        fractal($items, new CertificationTransformer())->toArray()
    );
}
```

---

## 4. CI/CD Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      postgres:
        image: postgres:14
        env:
          POSTGRES_PASSWORD: postgres
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      
      - name: Install dependencies
        run: composer install
      
      - name: Run migrations
        run: php artisan migrate:fresh
      
      - name: Validate JSON schemas
        run: npm run validate:configs
      
      - name: Run PHP tests
        run: php artisan test
      
      - name: Run linters
        run: |
          ./vendor/bin/pint --test
          npm run lint:vue
      
      - name: Build assets
        run: npm run build
      
      - name: Run E2E tests
        run: npm run test:e2e
```

---

## 5. Lecciones Aprendidas en Strato

### ✅ Lo que funcionó bien:

```
1. JSON-driven configuration
   → Agregar modelos sin recompilación
   
2. FormSchemaController genérico
   → 8 rutas × 5 modelos = 40 endpoints sin código repetido
   
3. Composition API + Reactive forms
   → Componentes reutilizables y fáciles de entender
   
4. Repository pattern
   → Cambiar de BD sin afectar controller
   
5. Validación separada por modelo
   → Cada modelo con sus propias reglas
```

### ⚠️ Lo que costó aprender:

```
1. Duplicación inicial de rutas
   → SOLUCIÓN: form-schema-complete.php consolidó en un lugar
   
2. Inconsistencias de naming
   → SOLUCIÓN: Convención clara (plural vs singular)
   
3. Cache strategy no claro
   → SOLUCIÓN: Invalidar en cambios, TTL apropiado
   
4. Tests insuficientes inicialmente
   → SOLUCIÓN: Tests en 3 niveles (unit, integration, e2e)
   
5. Performance con N+1 queries
   → SOLUCIÓN: Eager loading + indexes en BD
```

---

## 6. Evolución Futura del Patrón

### Versión 2: GraphQL Integration

```graphql
# Mismo FormSchema, pero con GraphQL en lugar de REST

query {
  certifications(filter: { provider: "AWS" }) {
    id
    name
    provider
  }
}

mutation {
  createCertification(input: {
    name: "AWS"
    provider: "AWS"
  }) {
    id
    name
  }
}
```

### Versión 2: Admin UI Builder

```
Sistema web donde:
  1. Nuevo modelo = upload SQL migration
  2. Generar automáticamente:
     - Modelo
     - Routes
     - Controller
     - JSON config
     - Vue components
  
  3. Deploy automático
```

---

## 7. Recursos y Referencias

### Documentación:
- [Laravel Eloquent](https://laravel.com/docs/eloquent)
- [Vue 3 Composition API](https://v3.vuejs.org/guide/composition-api-introduction.html)
- [Vuetify 3 Components](https://vuetifyjs.com)
- [JSON Schema Validation](https://json-schema.org)

### Herramientas:
- [Postman](https://www.postman.com) - API testing
- [Cypress](https://cypress.io) - E2E testing
- [Laravel Telescope](https://laravel.com/docs/telescope) - Debugging
- [Laravel Horizon](https://laravel.com/docs/horizon) - Job monitoring

---

## Conclusión: Un Patrón Maduro

FormSchema Pattern en Strato demostró:

- ✅ **Rapidez:** MVP en 7 días
- ✅ **Escalabilidad:** Pasar de 4 a N modelos sin cambios core
- ✅ **Mantenibilidad:** Código predecible y consistente
- ✅ **Confiabilidad:** Tests robustos y CI/CD

No es una panacea, pero para CRUD + formularios es imbatible.

### Recomendación Final:

Para **nuevos proyectos Laravel + Vue 3**:

1. Si >50% del código es CRUD → **Usa FormSchema**
2. Si <20% es CRUD → Controllers custom
3. Si 20-50% es CRUD → Usa ambos (FormSchema donde aplique)

---

**FIN DEL LIBRO**

---

## Apéndice: Checklist para Mantener

- [ ] Ejecutar tests antes de cada deploy
- [ ] Validar JSON schemas en build
- [ ] Revisar routes.json cada mes
- [ ] Actualizar documentación de nuevos modelos
- [ ] Benchmarkear performance trimestralmente
- [ ] Refrescar índices de BD según crecimiento
- [ ] Backup de configuraciones JSON

---

**Autor:** Omar  
**Fecha:** 31 Diciembre 2025  
**Versión:** 1.0  
**Estado:** Completo y listo para producción

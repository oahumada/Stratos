# Migración a FormSchemaController - Controlador Genérico

## Resumen

Se ha creado un **controlador genérico** `FormSchemaController` que puede manejar todas las tablas hijas que usan el patrón FormSchema, eliminando la necesidad de controladores individuales para cada tabla.

## Beneficios

✅ **Reducción de código**: Un solo controlador en lugar de 80+ controladores individuales  
✅ **Mantenimiento simplificado**: Cambios en un solo lugar  
✅ **Consistencia**: Comportamiento uniforme en todas las tablas  
✅ **Reutilización**: Aprovecha el patrón Repository existente  
✅ **Flexibilidad**: Fácil agregar nuevas tablas hijas  

## Arquitectura

### Antes (Patrón Actual)

```
AtencionDiariaController -> AtencionDiariaRepository -> Repository (base)
AlergiaController -> AlergiaRepository -> Repository (base)
ExEquilibrioController -> ExEquilibrioRepository -> Repository (base)
... (80+ controladores similares)
```

### Después (Patrón Propuesto)

```
FormSchemaController -> [Cualquier]Repository -> Repository (base)
```

## Implementación

### 1. Controlador Genérico Creado

**Archivo**: `app/Http/Controllers/FormSchemaController.php`

**Características**:

- Inicialización dinámica de modelos y repositorios
- Manejo de errores centralizado
- Soporte para vistas peoplealizadas
- Métodos CRUD completos
- Compatibilidad con métodos específicos de repositorios

### 2. Rutas Genéricas

**Archivo**: `routes/form-schema.php`

**Dos enfoques disponibles**:

#### Enfoque A: Rutas Específicas (Recomendado)

- Mantiene URLs existentes (`/api/atencion-diaria`, `/api/ex-equilibrio`, etc.)
- Compatibilidad total con frontend actual
- Migración transparente

#### Enfoque B: Rutas Dinámicas

- URLs nuevas (`/form-schema/{modelName}`)
- Más flexible pero requiere cambios en frontend

## Plan de Migración

### Fase 1: Preparación (Recomendado empezar aquí)

1. **Incluir las rutas genéricas**:

```php
// En routes/web.php o routes/api.php
require __DIR__.'/form-schema.php';
```

2. **Probar con una tabla específica** (ej: ExEquilibrio):

```php
// Comentar temporalmente en api.php:
// Route::apiResource('ex-equilibrio', ExEquilibrioController::class)

// Las rutas genéricas tomarán el control automáticamente
```

### Fase 2: Validación

1. **Probar funcionalidades**:
   - ✅ Crear registros (POST)
   - ✅ Actualizar registros (PUT/PATCH)  
   - ✅ Eliminar registros (DELETE)
   - ✅ Búsquedas con filtros (search)
   - ✅ Vistas Inertia

2. **Verificar logs**:

```bash
tail -f storage/logs/laravel.log
```

### Fase 3: Migración Gradual

1. **Migrar tabla por tabla**:

```php
// Comentar en api.php:
// Route::apiResource('atencion-diaria', AtencionDiariaController::class);
// Route::apiResource('alergia', AlergiaController::class);
// etc.
```

2. **Eliminar controladores obsoletos** (después de validar):

```bash
# Opcional: mover a backup antes de eliminar
mkdir backup/controllers
mv app/Http/Controllers/AtencionDiariaController.php backup/controllers/
```

### Fase 4: Limpieza Final

1. **Actualizar imports** en rutas si es necesario
2. **Documentar cambios** para el equipo
3. **Actualizar tests** si usan controladores específicos

## Configuración de Vistas

El controlador incluye mapeos para vistas peoplealizadas:

```php
private function getViewMap(): array
{
    return [
        'AtencionDiaria' => 'subpages/AtencionesDiarias',
        'ExEquilibrio' => 'subpages/examenes/ExamenEquilibrio',
        'Alergia' => 'subpages/Alergia',
        // Agregar más según necesidades
    ];
}
```

## Manejo de Casos Especiales

### Repositorios con Métodos Peoplealizados

El controlador genérico detecta automáticamente si un repositorio tiene métodos específicos:

```php
// Si ExEquilibrioRepository tiene un método search() peoplealizado
if (method_exists($this->repository, 'search')) {
    return $this->repository->search($request);
}
```

### Validaciones Específicas

Si necesitas validaciones específicas por modelo:

```php
// En FormSchemaController, agregar:
private function getValidationRules(string $modelName): array
{
    $rules = [
        'AtencionDiaria' => [
            'fecha' => 'required|date',
            'paciente_id' => 'required|exists:paciente,id'
        ],
        'ExEquilibrio' => [
            'resultado' => 'required|string',
            // más reglas...
        ]
    ];
    
    return $rules[$modelName] ?? [];
}
```

## Testing

### Probar el Controlador Genérico

```bash
# Crear test específico
php artisan make:test FormSchemaControllerTest

# Ejecutar tests existentes para verificar compatibilidad
php artisan test --filter=AtencionDiariaTest
```

### Ejemplo de Test

```php
public function test_generic_controller_handles_atencion_diaria()
{
    $response = $this->postJson('/api/atencion-diaria', [
        'data' => [
            'paciente_id' => 1,
            'fecha' => '2025-01-25',
            // más datos...
        ]
    ]);
    
    $response->assertStatus(200);
}
```

## Troubleshooting

### Error: "Model class not found"

- Verificar que el modelo existe en `app/Models/`
- Verificar nomenclatura (ej: `ExEquilibrio` vs `ExEquilibrio`)

### Error: "Repository class not found"  

- Verificar que el repositorio existe en `app/Repository/`
- Verificar que extiende de la clase `Repository` base

### Error: "View not found"

- Agregar mapeo en `getViewMap()`
- Verificar que la vista existe en `resources/js/pages/`

## Próximos Pasos

1. **Probar con ExEquilibrio** (tienes los archivos abiertos)
2. **Validar funcionalidad completa**
3. **Migrar gradualmente otras tablas**
4. **Documentar para el equipo**

## Contacto

Si encuentras algún problema durante la migración, documenta:

- Modelo específico que falla
- Error exacto
- Logs relevantes

Esto permitirá ajustar el controlador genérico según sea necesario.

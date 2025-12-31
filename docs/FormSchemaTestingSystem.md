# Sistema de Testing Modular para FormSchema (TablaHijaPaciente)

## Introducción

Este documento describe el sistema de testing modular implementado para las 80+ tablas que siguen el patrón FormSchema (anteriormente conocido como TablaHijaPaciente). El sistema permite crear tests automatizados y reutilizables para cada tabla usando configuración JSON.

## Arquitectura del Sistema

### Componentes Principales

1. **FormSchemaTest.php** - Clase base abstracta con métodos de testing reutilizables
2. **FormSchemaTestGenerator.php** - Generador automático de tests, modelos y factories
3. **GenerateFormSchemaTest.php** - Comando Artisan para generar tests
4. **Archivos de configuración JSON** - Definen la estructura de cada tabla

### Estructura de Archivos

```
tests/
├── Feature/
│   ├── FormSchemaTest.php              # Clase base abstracta
│   ├── AtencionesDiariasTest.php       # Ejemplo de test específico
│   └── [NombreTabla]Test.php           # Tests específicos para cada tabla
├── Support/
│   └── FormSchemaTestGenerator.php     # Generador automático
app/
├── Console/Commands/
│   └── GenerateFormSchemaTest.php      # Comando Artisan
└── Models/
    └── [NombreTabla].php               # Modelos generados
database/factories/
└── [NombreTabla]Factory.php            # Factories generados
resources/js/components/TablaHija/
└── [NombreTabla]/
    ├── config.json                     # Configuración de endpoints y permisos
    ├── tableConfig.json                # Configuración de headers de tabla
    └── itemForm.json                   # Configuración de campos del formulario
```

## Configuración JSON

### config.json

Define endpoints API y permisos:

```json
{
  "endpoints": {
    "apiIndex": "/api/nombre-tabla",
    "index": "/nombre-tabla",
    "apiStore": "/api/nombre-tabla",
    "apiUpdate": "/api/nombre-tabla",
    "apiDestroy": "/api/nombre-tabla"
  },
  "titulo": "Nombre de la Tabla",
  "permisos": {
    "crear": true,
    "editar": true,
    "eliminar": true
  }
}
```

### tableConfig.json

Define headers y opciones de la tabla:

```json
{
  "headers": [
    {
      "title": "Campo",
      "align": "center",
      "sortable": true,
      "key": "campo_nombre"
    }
  ],
  "options": {
    "dense": true,
    "itemsPerPage": 10,
    "showSelect": false
  }
}
```

### itemForm.json

Define campos del formulario y catálogos:

```json
{
  "fields": [
    { "type": "text", "key": "campo_texto", "label": "Campo de Texto" },
    { "type": "date", "key": "fecha_campo", "label": "Fecha" },
    { "type": "select", "key": "catalogo_id", "label": "Catálogo" }
  ],
  "catalogs": [
    "catalogo1",
    "catalogo2"
  ]
}
```

## Tipos de Campos Soportados

| Tipo | Descripción | Ejemplo de Uso |
|------|-------------|----------------|
| `text` | Campo de texto | Nombres, descripciones |
| `date` | Campo de fecha | Fechas de atención |
| `time` | Campo de hora | Hora inicio/término |
| `number` | Campo numérico | Días de descanso |
| `switch` | Campo booleano | Acompañado (sí/no) |
| `select` | Lista desplegable | Catálogos (terminados en _id) |
| `textarea` | Área de texto | Comentarios largos |

## Uso del Sistema

### 1. Generar Test para Nueva Tabla

```bash
# Generar solo el test
php artisan make:form-schema-test NombreTabla

# Generar test, modelo y factory
php artisan make:form-schema-test NombreTabla --model

# Solo validar archivos de configuración
php artisan make:form-schema-test NombreTabla --validate
```

### 2. Estructura del Test Generado

Cada test generado incluye automáticamente:

- ✅ Test de acceso al endpoint index
- ✅ Test de creación de registros
- ✅ Test de actualización de registros
- ✅ Test de eliminación de registros
- ✅ Test de validación de campos requeridos
- ✅ Test de validación de archivos de configuración
- ✅ Test de estructura de campos del formulario
- ✅ Test de configuración de permisos

### 3. Ejecutar Tests

```bash
# Ejecutar todos los tests FormSchema
php artisan test tests/Feature/ --filter=FormSchema

# Ejecutar test específico
php artisan test --filter=AtencionesDiariasTest

# Ejecutar con coverage
php artisan test --coverage
```

## Ejemplo Completo: AtencionesDiarias

### Paso 1: Crear Archivos de Configuración

Los archivos ya existen en `resources/js/components/TablaHija/AtencionesDiarias/`

### Paso 2: Generar Test, Modelo y Factory

```bash
php artisan make:form-schema-test AtencionesDiarias --model
```

### Paso 3: Peoplealizar Test

Editar `tests/Feature/AtencionesDiariasTest.php` para agregar tests específicos:

```php
public function test_fecha_atencion_is_required(): void
{
    $data = $this->generateTestData();
    unset($data['fecha_atencion']);
    
    $response = $this->post($this->config['endpoints']['apiStore'], $data);
    
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['fecha_atencion']);
}
```

### Paso 4: Ejecutar Tests

```bash
php artisan test --filter=AtencionesDiariasTest
```

## Mejores Prácticas

### 1. Configuración de Archivos JSON

- **Consistencia**: Usar nombres consistentes para endpoints y campos
- **Validación**: Siempre validar archivos JSON antes de generar tests
- **Catálogos**: Definir todos los catálogos necesarios en itemForm.json

### 2. Tests Específicos

- **Campos requeridos**: Agregar tests para campos obligatorios específicos
- **Lógica de negocio**: Incluir tests para reglas de negocio particulares
- **Relaciones**: Verificar relaciones con otras tablas

### 3. Datos de Prueba

- **Seeders**: Asegurar que existan datos base para foreign keys
- **Factories**: Peoplealizar factories con estados específicos
- **Limpieza**: Usar RefreshDatabase para tests aislados

## Comandos Útiles

```bash
# Listar todos los tests FormSchema
php artisan test --list-tests | grep FormSchema

# Ejecutar tests con información detallada
php artisan test --verbose

# Generar coverage report
php artisan test --coverage-html coverage-report

# Validar configuración de múltiples tablas
for table in AtencionesDiarias Exposiciones Examenes; do
  php artisan make:form-schema-test $table --validate
done
```

## Solución de Problemas

### Error: "Class not found"

- Verificar que el modelo existe en `app/Models/`
- Ejecutar `composer dump-autoload`

### Error: "Table doesn't exist"

- Crear y ejecutar migración para la tabla
- Verificar que el seeder incluye datos base

### Error: "Configuration file not found"

- Verificar estructura de archivos JSON
- Usar comando con `--validate` para diagnosticar

### Error: "Foreign key constraint"

- Asegurar que seeders crean datos para tablas referenciadas
- Revisar factories para foreign keys válidos

## Extensión del Sistema

### Agregar Nuevos Tipos de Campo

1. Modificar `generateTestData()` en FormSchemaTest.php
2. Actualizar `generateFactoryFields()` en FormSchemaTestGenerator.php
3. Agregar validación en `test_form_fields_have_required_properties()`

### Peoplealizar Generación de Modelos

Editar métodos en FormSchemaTestGenerator.php:

- `generateFillableFields()`
- `generateCasts()`
- `generateRelationships()`

## Próximos Pasos

1. **Implementar más tablas**: Usar el sistema para las 80+ tablas restantes
2. **Automatización CI/CD**: Integrar tests en pipeline de deployment
3. **Métricas**: Agregar métricas de cobertura por tabla
4. **Documentación API**: Generar documentación automática de endpoints

## Soporte

Para dudas o problemas con el sistema de testing:

1. Revisar esta documentación
2. Ejecutar comando con `--validate` para diagnosticar
3. Revisar logs de Laravel en `storage/logs/`
4. Consultar ejemplos en AtencionesDiariasTest.php

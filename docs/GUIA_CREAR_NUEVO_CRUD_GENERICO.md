# 📖 Guía: Crear Nuevo CRUD Genérico (Form-Schema Pattern)

**Última actualización:** 31 Diciembre 2025  
**Tiempo estimado:** 10-15 minutos por módulo  
**Complejidad:** Baja (solo configuración JSON)  
**Requiere:** Modelo Eloquent creado + Migraciones ejecutadas

---

## 🎯 Objetivo

Crear un CRUD completo (Create, Read, Update, Delete, Search) con:
- ✅ Rutas API automáticas (FormSchemaController)
- ✅ Tabla con búsqueda y filtros (FormSchema.vue)
- ✅ Formulario de crear/editar automático
- ✅ Validaciones automáticas
- ✅ Sin duplicar código en controladores

---

## 📋 Pre-Requisitos

- [ ] Modelo Eloquent creado: `app/Models/YourModel.php`
- [ ] Migraciones ejecutadas (tabla en BD)
- [ ] Base de datos con datos de prueba (factories/seeders)

**Ejemplo:** Queremos crear CRUD para "Certifications"

---

## 🚀 5 Pasos para Crear Nuevo CRUD

### PASO 1: Registrar Modelo en form-schema-complete.php (1 min)

**Archivo:** `/src/routes/form-schema-complete.php` (línea ~18)

**Agregar a `$formSchemaModels`:**

```php
$formSchemaModels = [
    'People' => 'people',
    'Skills' => 'skills',
    'Department' => 'departments',
    'Role' => 'roles',
    'Certification' => 'certifications',  // ← AGREGAR AQUÍ
];
```

**Reglas de naming:**
- `'Certification'` - Nombre EXACTO de tu Modelo (ej: `app/Models/Certification.php`)
- `'certifications'` - Nombre plural en minúsculas (URL route)

**Resultado automático:** FormSchemaController genera estos endpoints:
```
GET    /api/certifications
POST   /api/certifications
GET    /api/certifications/{id}
PUT    /api/certifications/{id}
PATCH  /api/certifications/{id}
DELETE /api/certifications/{id}
POST   /api/certifications/search
POST   /api/certifications/search-with-paciente
```

✅ **Verificar:** `php artisan route:list | grep certifications`

---

### PASO 2: Crear Estructura de Archivos Frontend (2 min)

**Carpetas a crear:**

```bash
cd /src/resources/js/pages

# Crear carpeta principal
mkdir -p Certifications/certifications-form

# Crear archivos JSONs vacíos
touch Certifications/Index.vue
touch Certifications/certifications-form/config.json
touch Certifications/certifications-form/tableConfig.json
touch Certifications/certifications-form/itemForm.json
touch Certifications/certifications-form/filters.json
```

**Estructura resultante:**

```
/resources/js/pages/Certifications/
├── Index.vue                           (va a importar JSONs)
└── certifications-form/
    ├── config.json                     (endpoints + permisos)
    ├── tableConfig.json                (columnas de tabla)
    ├── itemForm.json                   (campos de formulario)
    └── filters.json                    (filtros de búsqueda)
```

---

### PASO 3: Llenar Archivos JSON de Configuración (5 min)

#### 3.1 config.json

**Copiar:** `/resources/js/pages/People/People-form/config.json`  
**Modificar:**

```json
{
  "endpoints": {
    "index": "/api/certifications",
    "apiUrl": "/api/certifications"
  },
  "titulo": "Certifications Management",
  "descripcion": "Manage professional certifications and qualifications",
  "permisos": {
    "crear": true,
    "editar": true,
    "eliminar": true
  }
}
```

**Campos:**
- `endpoints.index` - Debe coincidir con route-name en form-schema-complete.php
- `endpoints.apiUrl` - Igual a index (base para CRUD operations)
- `titulo` - Encabezado de la página
- `descripcion` - Subtítulo descriptivo
- `permisos` - Mostrar/ocultar botones de acciones

---

#### 3.2 tableConfig.json

**Copiar:** `/resources/js/pages/People/People-form/tableConfig.json`  
**Modificar:**

```json
{
  "headers": [
    {
      "text": "Certification Name",
      "value": "name",
      "sortable": true
    },
    {
      "text": "Provider",
      "value": "provider",
      "sortable": true
    },
    {
      "text": "Expiry Date",
      "value": "expiry_date",
      "type": "date",
      "sortable": true
    },
    {
      "text": "Status",
      "value": "status",
      "sortable": false
    },
    {
      "text": "Actions",
      "value": "actions",
      "sortable": false
    }
  ],
  "options": {
    "dense": false,
    "itemsPerPage": 10,
    "showSelect": false
  }
}
```

**Reglas:**
- `value` debe coincidir con nombre del campo en la tabla DB
- Siempre terminar con columna "Actions"
- `type: "date"` para campos de fecha
- `sortable: false` para Actions y campos complejos

---

#### 3.3 itemForm.json

**Copiar:** `/resources/js/pages/People/People-form/itemForm.json`  
**Modificar:**

```json
{
  "fields": [
    {
      "key": "name",
      "label": "Certification Name",
      "type": "text",
      "placeholder": "Enter certification name",
      "rules": ["required", "min:3"]
    },
    {
      "key": "provider",
      "label": "Provider/Organization",
      "type": "text",
      "placeholder": "e.g., AWS, Microsoft, Google",
      "rules": ["required"]
    },
    {
      "key": "description",
      "label": "Description",
      "type": "textarea",
      "placeholder": "Brief description",
      "rules": []
    },
    {
      "key": "expiry_date",
      "label": "Expiry Date",
      "type": "date",
      "rules": []
    },
    {
      "key": "status",
      "label": "Status",
      "type": "select",
      "rules": []
    }
  ],
  "catalogs": []
}
```

**Tipos permitidos:**
- `text` - Texto corto (names, emails, etc)
- `email` - Con validación email
- `number` - Enteros/decimales
- `textarea` - Texto largo
- `select` - Dropdown
- `date` - Date picker
- `checkbox` - Booleano
- `switch` - Toggle

**Rules:**
- `"required"` - Campo obligatorio
- `"min:N"` - Mínimo N caracteres
- `"max:N"` - Máximo N caracteres
- `"email"` - Validación email
- `"unique:table"` - Único en tabla

**Catalogs:**
- Para selects dinámicos: `"catalogs": ["department", "role"]`
- FormSchema cargará automáticamente desde `/api/department` y `/api/role`

---

#### 3.4 filters.json

**Copiar:** `/resources/js/pages/People/People-form/filters.json`  
**Modificar:**

```json
[
  {
    "field": "provider",
    "type": "text",
    "label": "Provider",
    "placeholder": "Search by provider"
  },
  {
    "field": "status",
    "type": "select",
    "label": "Status",
    "placeholder": "Filter by status"
  }
]
```

**Tipos de filtro:**
- `text` - Búsqueda libre (case-insensitive)
- `select` - Dropdown
- `date` - Date range picker

**Regla:** FormSchema automáticamente busca `/api/[field-singular]` para poblar selects.

---

### PASO 4: Crear Index.vue (3 min)

**Copiar completamente:** `/resources/js/pages/People/Index.vue`

**Cambiar SOLO estas 4 líneas:**

```typescript
// Línea ~7-10: Cambiar imports de JSONs
import configJson from "./certifications-form/config.json";
import tableConfigJson from "./certifications-form/tableConfig.json";
import itemFormJson from "./certifications-form/itemForm.json";
import filtersJson from "./certifications-form/filters.json";
```

**El resto del Index.vue se queda igual!**

Si necesitas catalogs especiales, agregar:

```typescript
// Si necesitas cargar catálogos adicionales
const statuses = ref<Status[]>([]);

const filters = computed<FilterConfig[]>(() => {
  return filtersBase.map(filter => {
    if (filter.field === 'status') {
      return {
        ...filter,
        items: statuses.value.map(s => ({ id: s.id, name: s.name })),
      };
    }
    return filter;
  });
});

const loadStatuses = async () => {
  try {
    const response = await axios.get('/api/statuses');
    statuses.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load statuses', err);
  }
};

onMounted(() => {
  loadStatuses();
});
```

---

### PASO 5: Agregar Ruta Web + Navegación (2 min)

#### 5.1 Ruta Web

**Archivo:** `/src/routes/web.php`

**Agregar antes del `require __DIR__ . '/settings.php';`:**

```php
Route::get('/certifications', function () {
    return Inertia::render('Certifications/Index');
})->middleware(['auth', 'verified'])->name('certifications.index');
```

**Importante:**
- Esta es la ruta **WEB** (renderiza Vue)
- Las rutas **API** ya existen automáticamente

---

#### 5.2 Navegación

**Archivo:** `/src/resources/js/components/AppSidebar.vue`

**Agregar un link nuevo:**

```vue
<Link href="/certifications" class="nav-link">
  <span class="icon">🎓</span> Certifications
</Link>
```

---

### PASO 6: Verificar y Probar (2 min)

**En terminal:**

```bash
cd /src
php artisan route:clear
php artisan route:cache
```

**Verificar rutas creadas:**

```bash
php artisan route:list | grep certifications
```

**En navegador:**

1. Ir a `http://localhost/certifications`
2. Verificar:
   - [ ] Tabla carga datos
   - [ ] Columnas correctas
   - [ ] Botón "New Certification" funciona
   - [ ] Crear nuevo registro
   - [ ] Editar registro
   - [ ] Eliminar registro
   - [ ] Búsqueda funciona
   - [ ] Filtros funcionan

**Verificar API (curl):**

```bash
# Listar
curl http://localhost:8000/api/certifications

# Crear
curl -X POST http://localhost:8000/api/certifications \
  -H "Content-Type: application/json" \
  -d '{"name":"AWS Certified","provider":"Amazon","status":"active"}'

# Actualizar
curl -X PUT http://localhost:8000/api/certifications/1 \
  -H "Content-Type: application/json" \
  -d '{"name":"AWS Certified Solutions Architect"}'

# Eliminar
curl -X DELETE http://localhost:8000/api/certifications/1
```

---

## ✅ Checklist Final

- [ ] Modelo registrado en `$formSchemaModels` en form-schema-complete.php
- [ ] Carpeta `/Certifications/` creada con structure correcta
- [ ] 4 archivos JSON válidos y correctos
- [ ] Index.vue importa JSONs correctos
- [ ] Ruta web agregada en web.php
- [ ] NavLink agregado en AppSidebar.vue
- [ ] `php artisan route:clear && php artisan route:cache` ejecutado
- [ ] Página carga en navegador
- [ ] CRUD completo funciona (Create, Read, Update, Delete)
- [ ] Búsqueda y filtros funcionan
- [ ] API endpoints responden correctamente

---

## 🐛 Troubleshooting

| Problema | Solución |
|----------|----------|
| Ruta no encontrada (404) | Ejecutar `php artisan route:cache` |
| Tabla vacía | Verificar endpoint en config.json, revisar Network tab |
| JSON syntax error | Validar en https://jsonlint.com/ |
| Select sin opciones | Verificar catálogo en itemForm.json coincide con campo |
| Error en API | Revisar que Modelo existe en app/Models/ |
| Filtros no funcionan | Campos en filters.json deben coincidir con campos en API |

---

## 📚 Referencias

- [PATRON_JSON_DRIVEN_CRUD.md](PATRON_JSON_DRIVEN_CRUD.md) - Guía completa del patrón
- [CHECKLIST_NUEVO_CRUD.md](CHECKLIST_NUEVO_CRUD.md) - Checklist detallado
- [Ejemplo implementado](/resources/js/pages/People/) - Copiar de People
- [API endpoints](/docs/dia5_api_endpoints.md) - Listado de todos los endpoints

---

## ⏱️ Tiempo Total: 10-15 minutos

**Desglose:**
- Paso 1 (Registrar modelo): 1 min
- Paso 2 (Crear archivos): 2 min
- Paso 3 (Llenar JSONs): 5 min
- Paso 4 (Index.vue): 3 min
- Paso 5 (Ruta + Nav): 2 min
- Paso 6 (Verificar): 2 min

---

## 🎯 Próximas Mejoras

En futuras versiones puedes agregar:
- Componentes especializados en lugar de Index.vue genérico
- Validaciones peoplealizadas por modelo
- Cálculos dinámicos en tablas
- Exportar a CSV/PDF
- Bulk operations (editar múltiples registros)
- Relaciones avanzadas con múltiples niveles

Pero por ahora, **este patrón cubre 80% de CRUDs normales**. 🚀

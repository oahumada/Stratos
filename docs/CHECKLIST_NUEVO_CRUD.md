# ✅ Checklist: Implementar Nuevo CRUD con Patrón JSON-Driven

**Tiempo estimado**: 15-20 minutos por módulo  
**Referencia**: `/docs/PATRON_JSON_DRIVEN_CRUD.md`  
**Patrón de Rutas**: `form-schema-complete.php` (FormSchemaController automático)

---

## 📋 Pre-Requisitos

- [ ] Modelo Eloquent creado con todas las propiedades
- [ ] Migraciones ejecutadas (tabla en base de datos)
- [ ] **IMPORTANTE**: NO crear endpoints API individual
  - Las rutas CRUD se generan automáticamente desde `form-schema-complete.php`
  - Solo registrar modelo en `$formSchemaModels` mapping

---

## 🔧 Paso 0: Registrar Modelo en FormSchema (1 min)

**⚠️ NUEVO PASO IMPORTANTE ⚠️**

**Archivo**: `/src/routes/form-schema-complete.php` (línea ~18)

**Agregar tu modelo al mapeo:**

```php
$formSchemaModels = [
    'Person' => 'person',
    'Skills' => 'skills',
    'Department' => 'departments',
    'Role' => 'roles',
    '[YourModel]' => '[route-name-plural]',  // ← AGREGAR AQUÍ
];
```

**Ejemplo:** Si quieres un CRUD para "Certifications":

```php
$formSchemaModels = [
    // ... modelos existentes
    'Certification' => 'certifications',  // ← Agregar esta línea
];
```

**Resultado automático:** FormSchemaController generará todos estos endpoints:
- `GET /api/certifications` - Listar
- `POST /api/certifications` - Crear
- `GET /api/certifications/{id}` - Mostrar
- `PUT /api/certifications/{id}` - Actualizar
- `PATCH /api/certifications/{id}` - Actualizar parcial
- `DELETE /api/certifications/{id}` - Eliminar
- `POST /api/certifications/search` - Búsqueda
- `POST /api/certifications/search-with-paciente` - Búsqueda avanzada

**Verificar:**

- [ ] Modelo agregado a `$formSchemaModels`
- [ ] Nombre del modelo es exacto (Certifications, NO certifications)
- [ ] Route-name es plural en minúsculas (certifications)
- [ ] PHP sintaxis correcta

---

```bash
# En /src/resources/js/pages
mkdir -p [Module]/[module]-form
cd [Module]
touch Index.vue
cd [module]-form
touch config.json tableConfig.json itemForm.json filters.json
```

**Verificar:**

- [ ] Carpeta `/pages/[Module]/` existe
- [ ] Carpeta `/pages/[Module]/[module]-form/` existe
- [ ] 4 archivos JSON creados y vacíos

---

## 📄 Paso 2: Llenar config.json (2 min)

**Copiar de**: `/resources/js/pages/Person/Person-form/config.json`

**Cambiar:**

```json
{
  "endpoints": {
    "index": "/api/[route-name-plural]",      ← Debe coincidir con form-schema-complete.php
    "apiUrl": "/api/[route-name-plural]"      ← Debe coincidir con form-schema-complete.php
  },
  "titulo": "[Module] Management",         ← Cambiar título
  "descripcion": "Manage [modules]",       ← Cambiar descripción
  "permisos": {
    "crear": true,                         ← Cambiar si aplica
    "editar": true,
    "eliminar": true
  }
}
```

**Importante:**
- Los endpoints `/api/[route-name]` se generan automáticamente
- Solo necesitas coincidir el nombre con el registro en `$formSchemaModels`
- Ejemplo: Si registraste `'Certification' => 'certifications'`, usa `/api/certifications`

**Verificar:**

- [ ] Endpoints coinciden con route-name en form-schema-complete.php
- [ ] Título y descripción son descriptivos
- [ ] JSON válido (probar en https://jsonlint.com/)

---

## 📋 Paso 3: Llenar tableConfig.json (3 min)

**Copiar de**: `/resources/js/pages/Person/Person-form/tableConfig.json`

**Adaptar columnas:**

Listar qué campos quieres mostrar en la tabla:

```json
{
  "headers": [
    { "text": "Column 1", "value": "field1", "sortable": true },
    { "text": "Column 2", "value": "field2", "sortable": true },
    { "text": "Column 3", "value": "field3", "type": "date", "sortable": true },
    { "text": "Actions", "value": "actions", "sortable": false }
  ],
  "options": {
    "dense": false,
    "itemsPerPage": 10,
    "showSelect": false
  }
}
```

**Reglas:**

- Siempre incluir "Actions" como última columna
- `sortable: false` para Actions y campos complejos
- `type: "date"` para campos de fecha
- El `value` debe coincidir con nombre de campo en API response

**Verificar:**

- [ ] Última columna es "Actions"
- [ ] Al menos 3-5 columnas funcionales
- [ ] JSON válido
- [ ] Los `value` coinciden con campos del modelo

---

## 📝 Paso 4: Llenar itemForm.json (4 min)

**Copiar de**: `/resources/js/pages/Person/Person-form/itemForm.json`

**Listar todos los campos editables:**

```json
{
  "fields": [
    {
      "key": "name",
      "label": "Full Name",
      "type": "text",
      "placeholder": "Enter full name",
      "rules": ["required", "min:3"]
    },
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "placeholder": "Enter email",
      "rules": ["required", "email"]
    },
    {
      "key": "role_id",
      "label": "Role",
      "type": "select",
      "placeholder": "Select a role",
      "rules": []
    },
    {
      "key": "hired_at",
      "label": "Hired Date",
      "type": "date",
      "rules": []
    }
  ],
  "catalogs": ["role"]
}
```

**Tipos permitidos:**

- `text` - Texto corto (names, emails, etc)
- `email` - Validación email automática
- `number` - Números enteros/decimales
- `password` - Input oculto
- `textarea` - Texto largo
- `select` - Dropdown (requiere catálogo)
- `date` - Date picker
- `time` - Time picker
- `checkbox` - Booleano (true/false)
- `switch` - Toggle (true/false)

**Rules (validaciones):**

- `"required"` - Campo obligatorio
- `"min:N"` - Mínimo N caracteres
- `"max:N"` - Máximo N caracteres
- `"email"` - Debe ser email válido
- `"unique:[table]"` - Valor único en tabla

**Catalogs (selectores dinámicos):**

- Lista catálogos a cargar (ej: `["role", "department"]`)
- FormData automáticamente busca `/api/role` y `/api/department`
- El select `role_id` automáticamente mapea al catálogo `role`

**Verificar:**

- [ ] Solo campos editables (no id, created_at, updated_at)
- [ ] Al menos 4-5 campos
- [ ] Catalogs listados correctamente
- [ ] Rules tienen sentido para cada tipo
- [ ] JSON válido

---

## 🔍 Paso 5: Llenar filters.json (2 min)

**Copiar de**: `/resources/js/pages/Person/Person-form/filters.json`

**Definir qué campos filtrar:**

```json
[
  {
    "field": "department",
    "type": "select",
    "label": "Department",
    "placeholder": "Select department"
  },
  {
    "field": "role_id",
    "type": "select",
    "label": "Role",
    "placeholder": "Select role"
  }
]
```

**Tipos de filtro:**

- `text` - Búsqueda libre (case-insensitive)
- `select` - Dropdown (necesita `items`)
- `date` - Date range picker

**Regla importante:**

- FormSchema automáticamente busca `/api/[field-singular]` para poblarpect
- Ej: field `role_id` → busca `/api/role`

**Verificar:**

- [ ] 1-3 filtros definidos
- [ ] Solo campos útiles para filtrar
- [ ] JSON válido

---

## 📝 Paso 6: Crear Index.vue (5 min)

**Copiar archivo completo de:**  
`/resources/js/pages/Person/Index.vue`

**Cambiar SOLO estos 4 imports:**

```typescript
import configJson from "./[module]-form/config.json";
import tableConfigJson from "./[module]-form/tableConfig.json";
import itemFormJson from "./[module]-form/itemForm.json";
import filtersJson from "./[module]-form/filters.json";
```

**Cambiar interfaces si tienes catálogos personalizados:**

Por ejemplo, si necesitas cargar catálogos adicionales:

```typescript
interface Role {
  id: number;
  name: string;
}

interface [Module] {
  id: number;
  [field1]: string;
  [field2]: string;
  // ... otros campos
}

const [catalogs] = ref<[CatalogType][]>([]);

const filters = computed<FilterConfig[]>(() => {
  return filtersBase.map(filter => {
    if (filter.field === '[catalog_field]') {
      return {
        ...filter,
        items: [catalogs].value.map(r => ({ id: r.id, name: r.name })),
      };
    }
    return filter;
  });
});

const load[Catalogs] = async () => {
  try {
    const response = await axios.get('/api/[catalogs]');
    [catalogs].value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load [catalogs]', err);
  }
};

onMounted(() => {
  load[Catalogs]();
  // loadOtherCatalogs() si hay más
});
```

**Verificar:**

- [ ] Imports de JSONs son correctos
- [ ] Métodos de carga de catálogos (ej: loadRoles)
- [ ] onMounted() llama a todos los métodos necesarios
- [ ] Template solo pasa props a FormSchema
- [ ] Archivo compila sin errores TypeScript

---

## 🚀 Paso 7: Agregar Ruta (1 min)

**Archivo**: `/src/routes/web.php`

**Agregar ruta web para tu página (NO API):**

```php
Route::get('/[plural-module]', function () {
    return Inertia::render('[Module]/Index');
})->middleware(['auth', 'verified'])->name('[module].index');
```

**Ejemplo:**

```php
Route::get('/certifications', function () {
    return Inertia::render('Certifications/Index');
})->middleware(['auth', 'verified'])->name('certifications.index');
```

**Importante:**
- Esta es la ruta **WEB** (renderiza la página Vue)
- Las rutas **API** CRUD se generan automáticamente en `form-schema-complete.php`
- NO repetir rutas API aquí

**Verificar:**

- [ ] Ruta web agregada (no API)
- [ ] Path coincide con carpeta del componente
- [ ] Incluye middleware `auth` y `verified`
- [ ] Sintaxis de Laravel Inertia correcta

---

## 🧭 Paso 8: Agregar NavLink (1 min)

**Archivo**: `/src/resources/js/components/AppSidebar.vue`

**Buscar sección de links y agregar:**

```vue
<Link href="/[plural-module]" class="nav-link">
  <span class="icon">📦</span> [Module]
</Link>
```

**Ejemplo:**

```vue
<Link href="/certifications" class="nav-link">
  <span class="icon">🎓</span> Certifications
</Link>
```

**Importante:**
- El `href` debe coincidir con la ruta web (paso anterior)
- NO el endpoint API

**Verificar:**

- [ ] Texto del link es descriptivo
- [ ] href coincide con ruta en web.php
- [ ] Icono es apropiado
- [ ] Orden lógico en el menú

---

## 🧪 Paso 9: Verificación Final (3 min)

**En terminal (antes de ir al navegador):**

```bash
cd /src
php artisan route:clear
php artisan route:cache
```

**En navegador:**

1. Ir a http://localhost/[plural-module]
2. Verificar que tabla carga datos:

   - [ ] Encabezado correcto (del config.json)
   - [ ] Columnas visibles (del tableConfig.json)
   - [ ] Datos cargan desde API automática (FormSchemaController)
   - [ ] Paginación funciona
   - [ ] Buscar funciona

3. Crear nuevo registro:

   - [ ] Click "New [Module]" abre dialog
   - [ ] Campos son los correctos (itemForm.json)
   - [ ] Dropdowns cargan catálogos
   - [ ] Submit funciona (POST a `/api/[route]` automático)
   - [ ] Notificación de éxito

4. Editar registro:

   - [ ] Click en fila abre dialog edit
   - [ ] Datos se populan correctamente
   - [ ] Cambios guardan (PUT a `/api/[route]/{id}` automático)

5. Eliminar registro:

   - [ ] Click eliminar muestra confirmación
   - [ ] Confirmación elimina (DELETE a `/api/[route]/{id}` automático)

6. Filtros y búsqueda:
   - [ ] Búsqueda por texto funciona
   - [ ] Dropdowns de filtro cargan items
   - [ ] Filtros aplican correctamente
   - [ ] Combinación de filtros funciona

**Verificar errores:**

- [ ] Console sin errores JavaScript
- [ ] Network tab muestra requests exitosos a `/api/[route]` (FormSchemaController)
- [ ] No hay CORS errors
- [ ] CSRF tokens inyectados correctamente

---

## 📋 Resumen - Checklist Rápido

- [ ] Carpeta `/[Module]/[module]-form/` creada
- [ ] 4 JSONs válidos (config, tableConfig, itemForm, filters)
- [ ] Index.vue con imports correctos
- [ ] Catálogos dinámicos cargados en onMounted
- [ ] Ruta agregada en web.php
- [ ] NavLink agregado en AppSidebar.vue
- [ ] Verificación en navegador: listar ✓, crear ✓, editar ✓, eliminar ✓
- [ ] Búsqueda y filtros funcionan

---

## 🚀 Tiempo Total: 15-20 minutos

Si tienes problemas comunes:

**Error: JSON syntax**
→ Verificar en https://jsonlint.com/

**Error: "Cannot read property 'map' of undefined"**
→ Catálogo no se cargó, revisar axios.get en onMounted

**Tabla vacía**
→ Verificar endpoint en config.json, revisar Network tab

**Filtro no filtra**
→ Revisar que `field` en filters.json coincida con nombre campo en API response

**Select sin opciones**
→ Revisar que catálogo en itemForm.json coincida con campo (ej: `role_id` y `catalogs: ["role"]`)

---

## 📚 Referencias

- **Guía completa**: `/docs/PATRON_JSON_DRIVEN_CRUD.md`
- **Memoria técnica**: `/docs/memories.md` (Sección 3.3)
- **Ejemplo implementado**: `/resources/js/pages/Person/`
- **API endpoints**: `/docs/dia5_api_endpoints.md`

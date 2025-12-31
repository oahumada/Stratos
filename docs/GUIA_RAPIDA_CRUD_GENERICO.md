# 🚀 GUÍA RÁPIDA: FormSchema Pattern Consolidado

**Última actualización:** 31 Diciembre 2025  
**Para:** Crear nuevos CRUDs en Days 8+  
**Tiempo:** 10-15 minutos por módulo  
**Complejidad:** ⭐ Baja (solo configuración JSON)

---

## 📋 Arquitectura (Una sola verdad)

```
routes/form-schema-complete.php
├─ $formSchemaModels (mapeo de modelos)
├─ FormSchemaController (CRUD genérico automático)
└─ 8 endpoints por modelo generados dinámicamente

    GET    /api/[model]          ✅ Automático
    POST   /api/[model]          ✅ Automático
    GET    /api/[model]/{id}     ✅ Automático
    PUT    /api/[model]/{id}     ✅ Automático
    DELETE /api/[model]/{id}     ✅ Automático
    + más...                      ✅ Automático
```

**NO duplicación:** Todas las rutas CRUD en un solo lugar.

---

## 🎯 Crear Nuevo CRUD (5 Pasos Simples)

### 1️⃣ Registrar Modelo (1 min)

**Archivo:** `routes/form-schema-complete.php` (línea ~18)

```php
$formSchemaModels = [
    'Person' => 'person',
    'Skills' => 'skills',
    'Role' => 'roles',
    'YourModel' => 'your-models',  // ← AGREGAR
];
```

✅ Resultado: Todas las rutas API creadas automáticamente

---

### 2️⃣ Crear Archivos Frontend (2 min)

```bash
mkdir -p /resources/js/pages/YourModel/your-model-form

touch /resources/js/pages/YourModel/Index.vue
touch /resources/js/pages/YourModel/your-model-form/{
  config.json,
  tableConfig.json,
  itemForm.json,
  filters.json
}
```

---

### 3️⃣ Llenar JSONs (5 min)

**config.json:**
```json
{
  "endpoints": { "index": "/api/your-models", "apiUrl": "/api/your-models" },
  "titulo": "Your Models Management",
  "descripcion": "Manage your models",
  "permisos": { "crear": true, "editar": true, "eliminar": true }
}
```

**tableConfig.json:**
```json
{
  "headers": [
    { "text": "Column 1", "value": "field1", "sortable": true },
    { "text": "Column 2", "value": "field2", "sortable": true },
    { "text": "Actions", "value": "actions", "sortable": false }
  ],
  "options": { "dense": false, "itemsPerPage": 10 }
}
```

**itemForm.json:**
```json
{
  "fields": [
    { "key": "field1", "label": "Field 1", "type": "text", "rules": ["required"] },
    { "key": "field2", "label": "Field 2", "type": "text", "rules": [] }
  ],
  "catalogs": []
}
```

**filters.json:**
```json
[
  { "field": "field1", "type": "text", "label": "Filter by Field 1" }
]
```

**Copiar templates de:** `/resources/js/pages/Person/[model]-form/`

---

### 4️⃣ Index.vue (3 min)

**Copiar:** `/resources/js/pages/Person/Index.vue` (completo)

**Cambiar SOLO líneas 7-10:**
```typescript
import configJson from "./your-model-form/config.json";
import tableConfigJson from "./your-model-form/tableConfig.json";
import itemFormJson from "./your-model-form/itemForm.json";
import filtersJson from "./your-model-form/filters.json";
```

**Rest → sin cambios!**

---

### 5️⃣ Ruta Web + Navegación (2 min)

**Archivo:** `routes/web.php`
```php
Route::get('/your-models', function () {
    return Inertia::render('YourModel/Index');
})->middleware(['auth', 'verified'])->name('your-models.index');
```

**Archivo:** `resources/js/components/AppSidebar.vue`
```vue
<Link href="/your-models" class="nav-link">
  <span class="icon">📋</span> Your Models
</Link>
```

---

### 6️⃣ Verificar (2 min)

```bash
cd /src
php artisan route:clear
php artisan route:cache

# Ir a http://localhost/your-models
# ✅ Tabla carga
# ✅ CRUD funciona
# ✅ Búsqueda/filtros OK
```

---

## ✅ Done! CRUD Completado

**Sin escribir:**
- ❌ Controladores
- ❌ Rutas API
- ❌ Lógica CRUD en Vue

**Solo configuración JSON.** 🎉

---

## 📚 Guías Completas

| Documento | Contenido |
|-----------|-----------|
| **[GUIA_CREAR_NUEVO_CRUD_GENERICO.md](GUIA_CREAR_NUEVO_CRUD_GENERICO.md)** | 📖 Paso-a-paso detallado con ejemplos |
| **[CHECKLIST_NUEVO_CRUD.md](CHECKLIST_NUEVO_CRUD.md)** | ✅ Checklist con verificaciones |
| **[PATRON_JSON_DRIVEN_CRUD.md](PATRON_JSON_DRIVEN_CRUD.md)** | 🎯 Guía técnica y arquitectura |
| **[memories.md](memories.md)** | 📝 Contexto completo del proyecto |

---

## 🐛 Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| Ruta 404 | Caché de rutas desactualizado | `php artisan route:cache` |
| Tabla vacía | Endpoint incorrecto | Verificar config.json, Network tab |
| JSON error | Sintaxis inválida | Validar en https://jsonlint.com/ |
| Select sin opciones | Catálogo no cargado | Revisar itemForm.json catalogs |
| Filtro no funciona | Campo incorrecto | Verificar field en filters.json |

---

## 🎓 Ejemplo Real: Certifications CRUD

**Modelo:** `app/Models/Certification.php`

**Paso 1:** Registrar
```php
'Certification' => 'certifications',
```

**Pasos 2-6:** Seguir los 5 pasos anteriores con:
- Carpeta: `/Certifications/certifications-form/`
- Endpoint: `/api/certifications`
- Ruta web: `/certifications`
- Tabla: name, provider, expiry_date, status, actions

**Resultado:** CRUD completo funcionando en 15 minutos. ✅

---

## 🚀 Cuándo Usar Esto

✅ **Usa este patrón para:**
- CRUDs simples (lista + crear/editar/eliminar)
- Tablas con búsqueda y filtros
- Formularios estándar
- Módulos sin lógica especializada

❌ **NO uses para:**
- Páginas con componentes complejos (ej: Dashboard con gráficos)
- Flujos de múltiples pasos
- Lógica especializada de negocio
- Visualizaciones avanzadas

**Para casos complejos:** Crea componentes especializados (como Dashboard.vue, GapAnalysis/Index.vue)

---

## 💡 Tips

1. **Copiar siempre desde Person:** Es el template más completo y probado
2. **Validar JSONs:** Usar https://jsonlint.com/ para evitar errores
3. **Caché de rutas:** Siempre ejecutar `php artisan route:cache` después de agregar modelo
4. **Revisar Network tab:** Cuando tabla vacía, buscar errores en requests API
5. **Catálogos dinámicos:** Si necesitas dropdown de rol/department, agregar en catalogs

---

**Última actualización:** 31 Diciembre 2025 | **Estado:** ✅ Probado y Funcional

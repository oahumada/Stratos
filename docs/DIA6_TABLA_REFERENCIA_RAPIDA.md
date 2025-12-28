# 📊 TABLA DE REFERENCIA RÁPIDA - Día 6 Frontend

**Referencia de 1 página para consultar mientras trabajas**

---

## 🏗️ ARQUITECTURA RÁPIDA

```
┌─────────────────────────────────────────────────────────────┐
│  Usuario interactúa con:                                    │
│  ├─ Tabla de datos (v-data-table)                          │
│  ├─ Dialog con formulario (FormData.vue)                   │
│  └─ Confirmación de eliminación                            │
└──────────┬──────────────────────────────────────────────────┘
           ↓ Controlado por:
┌─────────────────────────────────────────────────────────────┐
│  FormSchema.vue (Lógica CRUD)                              │
│  ├─ cargarItems() → apiHelper.get()                        │
│  ├─ guardarItem() → apiHelper.post() o .put()             │
│  ├─ eliminarItem() → apiHelper.remove()                    │
│  └─ estado reactivo (table items, dialogs, loading)        │
└──────────┬──────────────────────────────────────────────────┘
           ↓ Usa:
┌─────────────────────────────────────────────────────────────┐
│  apiHelper.ts (Operaciones HTTP)                           │
│  ├─ post(url, data)                                        │
│  ├─ put(url, data)                                         │
│  ├─ remove(url, params)                                    │
│  ├─ get(url, params)                                       │
│  └─ fetchCatalogs(endpoints)                               │
│                                                              │
│  Todo maneja:                                               │
│  ├─ CSRF token automático                                  │
│  ├─ Reintentos en 419                                      │
│  ├─ Logout en 401                                          │
│  └─ Errores centralizados                                  │
└──────────┬──────────────────────────────────────────────────┘
           ↓ Comunica con:
┌─────────────────────────────────────────────────────────────┐
│  Backend API (/api/...)                                    │
│  ├─ POST /api/x → create                                   │
│  ├─ GET /api/x → read all                                  │
│  ├─ GET /api/x/{id} → read one                            │
│  ├─ PUT /api/x/{id} → update                              │
│  └─ DELETE /api/x/{id} → delete                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 CHECKLISTS RÁPIDOS

### ✅ Antes de empezar Día 6

- [ ] Lee memories.md (estado actual)
- [ ] Lee DIA6_PLAN_ACCION.md (tareas específicas)
- [ ] Valida BD está populated (seeders corrieron)
- [ ] Backend API endpoints existen
- [ ] Servidor Laravel anda (`composer run dev`)

### ✅ Completar FormData.vue

- [ ] v-text-field para text/number
- [ ] v-select para select (con catalogs)
- [ ] v-text-area para textarea
- [ ] v-checkbox para boolean
- [ ] v-text-field type="date" para date
- [ ] :error-messages para mostrar errores 422
- [ ] validate(), reset(), formData exposed

### ✅ Tests CRUD Funcionales

Escenario CREATE:

- [ ] Abrir dialog "Crear nuevo"
- [ ] Llenar formulario
- [ ] Click "Guardar"
- [ ] Notificación "Éxito"
- [ ] Nuevo registro en tabla
- [ ] Check Network: POST 200

Escenario UPDATE:

- [ ] Click edit (lápiz)
- [ ] Cambiar un campo
- [ ] Click "Guardar"
- [ ] Notificación "Éxito"
- [ ] Cambio visible en tabla
- [ ] Check Network: PUT 200

Escenario DELETE:

- [ ] Click delete (papelera)
- [ ] Confirmar en dialog
- [ ] Notificación "Éxito"
- [ ] Registro desaparece
- [ ] Check Network: DELETE 200

### ✅ Validación Final

- [ ] npm run lint → 0 errors
- [ ] npm run dev → compila
- [ ] php artisan test → todos PASS
- [ ] Console sin errors (F12)
- [ ] No console.log en código final
- [ ] Git commit hecho

---

## 🔧 COMANDOS RÁPIDOS

```bash
# Iniciar servidor (en background)
composer run dev &

# Validar sintaxis
npm run lint

# Corregir automáticamente
npm run lint --fix

# Tests
php artisan test

# Ver logs
tail -f storage/logs/laravel.log

# Git commit
git add -A
git commit -m "Día 6: Completar FormData template, tests CRUD"

# Build para producción
npm run build
```

---

## 🔍 DEBUGGING RÁPIDO

### Error: "Failed to resolve component: v-select"

**Causa:** Campo type="select" sin template correspondiente  
**Fix:** Agregar template `v-if="field.type === 'select'"` en FormData.vue

### Error: "Property errors is not defined"

**Causa:** Props no incluye `errors`  
**Fix:** Agregar a props: `errors: { type: Object, default: () => ({}) }`

### Error: "Fecha no convierte correctamente"

**Causa:** Formato incorrecto (DD/MM/YYYY ↔ YYYY-MM-DD)  
**Fix:** Revisar `formatDateFields()` en FormSchema.vue

### Error: "API 422 pero no se muestran errores"

**Causa:** FormData.vue no recibe `errors` prop  
**Fix:** Pasar `:errors="state.errors"` desde FormSchema

### Error: "Validación falsa positiva"

**Causa:** Campos required sin rules definidas  
**Fix:** En itemForm.json agregar: `"rules": [(v) => !!v || "Requerido"]`

### Error: "No me autentica"

**Causa:** CSRF token no válido (419)  
**Fix:** Revisar console, apiHelper debería reintentar automático

---

## 📌 CONVENCIONES CLAVE

### Nombrado de Archivos

```
recursos/js/pages/[modulo]/
├── [NombreDelModulo].vue ← Componente principal
├── config.json ← Endpoint, título, permisos
├── tableConfig.json ← Estructura tabla (headers)
└── itemForm.json ← Estructura form (fields, catalogs)
```

### Estructura config.json

```json
{
    "titulo": "Nombre Amigable",
    "endpoints": {
        "apiUrl": "/api/ruta-exacta-backend"
    },
    "permisos": {
        "crear": true,
        "editar": true,
        "eliminar": false
    }
}
```

### Estructura itemForm.json

```json
{
    "fields": [
        { "type": "text", "key": "nombre", "label": "Nombre" },
        { "type": "select", "key": "estado_id", "label": "Estado" },
        { "type": "date", "key": "fecha", "label": "Fecha" }
    ],
    "catalogs": ["estado"]
}
```

### Estructura tableConfig.json

```json
{
    "headers": [
        { "title": "#", "key": "id", "sortable": true },
        { "title": "Nombre", "key": "nombre", "sortable": true },
        { "title": "Acciones", "key": "actions", "align": "center" }
    ],
    "options": {
        "itemsPerPage": 10,
        "sortBy": [{ "key": "id", "order": "asc" }]
    }
}
```

---

## 🎯 TIEMPOS ESTIMADOS

| Tarea                          | Tiempo              |
| ------------------------------ | ------------------- |
| FormData.vue template completo | 45 min              |
| Agregar props + errores        | 15 min              |
| Validación visual              | 30 min              |
| **BLOQUE 1 total**             | **90 min**          |
| CRUD funcional test            | 60 min              |
| config.json llenado            | 15 min              |
| Documentación CRUD             | 30 min              |
| Tests apiHelper.ts             | 30 min              |
| **BLOQUE 2 total**             | **135 min**         |
| **DÍA 6 total**                | **225 min (3.75h)** |

---

## 💡 TIPS RÁPIDOS

- **Conversión fechas:** `moment("2025-12-27", "YYYY-MM-DD").format("DD/MM/YYYY")`
- **Catálogos auto:** Naming convention `campo_id` → busca catálogo `campo`
- **Errores 422:** `error.response.data.errors` es objeto con arrays de strings
- **Dialog cerrado:** Cualquier click fuera cierra (Vuetify default)
- **Notificaciones:** Usa `notify({ title, text, type: 'success'|'error' })`
- **Debugear estado:** `console.log(state)` en breakpoint

---

## 🆘 SOPORTE RÁPIDO

**¿Dónde está...?**

| Pregunta                  | Respuesta                                |
| ------------------------- | ---------------------------------------- |
| ¿Documentación CRUD?      | DIA6_PLAN_ACCION.md                      |
| ¿Análisis arquitectura?   | DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md   |
| ¿Cómo crear módulo nuevo? | DIA6_COMENTARIOS_CODIGO.md + memories.md |
| ¿Errores comunes?         | TROUBLESHOOTING.md                       |
| ¿Endpoints existentes?    | dia5_api_endpoints.md                    |
| ¿Estructura BD?           | memories.md sección 7                    |
| ¿Ruta exacta backend?     | config.json endpoints.apiUrl             |

---

## 📈 PROGRESO

```
Día 1-5: ████████████████████ (Backend 100%)
Día 6:   ███████░░░░░░░░░░░░░ (Frontend 35%)
         ├─ ✅ apiHelper.ts
         ├─ ✅ FormSchema.vue
         ├─ ⏳ FormData.vue (template incompleto)
         ├─ ⏳ Tests (no visible)
         └─ ⏳ Documentación

Día 7:   ░░░░░░░░░░░░░░░░░░░░ (Crear módulos 0%)
         ├─ Competencias CRUD
         ├─ Marketplace
         └─ Dashboard visuales
```

---

**Última actualización:** 27 Diciembre 2025  
**Para imprimir:** Sí (cabe en 2 páginas A4)  
**Consultar mientras trabajas:** Recomendado

---

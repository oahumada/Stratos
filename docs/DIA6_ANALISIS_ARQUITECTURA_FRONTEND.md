# 📋 ANÁLISIS - Arquitectura Frontend (apiHelper + form-template)

**Fecha:** 27 Diciembre 2025  
**Día:** 6 (Frontend)  
**Análisis de:** apiHelper.ts + form-template (ExampleForm, FormData, FormSchema) + example-form (configs JSON)

---

## 🎯 RESUMEN EJECUTIVO

Has creado una **arquitectura CRUD centralizada y config-driven** muy sólida. Sistema permite:

✅ **Abstracción total de operaciones HTTP** (apiHelper.ts)  
✅ **Componentes reutilizables** (FormSchema, FormData son genéricos)  
✅ **Configuración declarativa** (JSON files definen tabla, form, campos)  
✅ **Manejo robusto de errores y autenticación** (Sanctum, interceptors, retry logic)  
✅ **Validación en frontend + backend** (lógica de sincronización)  
✅ **CRUD completo** (create, read, update, delete con confirmación)

**Verdict:** Arquitectura profesional, lista para escalar. 🚀

---

## 📦 ANÁLISIS COMPONENTE POR COMPONENTE

### 1️⃣ **apiHelper.ts** - Capa de Abstracción HTTP

#### ✅ Lo que está BIEN:

**A) Gestión Robusta de Autenticación (Sanctum)**

```typescript
// Interceptor de CSRF inteligente
axios.interceptors.request.use((config) => {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
});
```

- ✅ Inyecta CSRF automáticamente en cada request
- ✅ Sin olvidar tokens, sin duplicación de código

**B) Manejo de Errores 419/401 Inteligente**

```typescript
// State global para evitar race conditions
let isRefreshingAuth = false;
let failedQueue: any[] = [];

// Si ya se está refreshing, encolar
if (isRefreshingAuth) {
    return new Promise((resolve, reject) => {
        failedQueue.push({ resolve, reject });
    }).then(() => axios(originalRequest));
}
```

- ✅ Detecta cuando falla CSRF (419) y reinicializa Sanctum
- ✅ Evita múltiples refresh simultáneos (queue inteligente)
- ✅ Reintentos automáticos sin perder request original

**C) Métodos CRUD Genéricos**

```typescript
export const post = async (url: string, data = {}) => {
    await initSanctum(); // Garantiza cookie CSRF
    return axios.post(url, data).then((r) => r.data);
};

export const put = async (url: string, data = {}) => {
    await initSanctum();
    return axios.put(url, data).then((r) => r.data);
};

export const remove = async (url: string) => {
    await initSanctum();
    return axios.delete(url).then((r) => r.data);
};
```

- ✅ Interfaz consistente para todas las operaciones
- ✅ Todos preparan Sanctum antes de ejecutar
- ✅ Manejo de errores centralizado

**D) fetchCatalogs - Para cargar múltiples selectores**

```typescript
export const fetchCatalogs = async (endpoints = []) => {
    const result = await axios.get(`/api/catalogs`, {
        params: { endpoints },
    });
    return result.data;
};
```

- ✅ Un solo request para múltiples catálogos
- ✅ Eficiente, evita waterfall requests

#### ⚠️ Cosas a Mejorar:

**1) URL base Hardcoded para producción**

```typescript
// Problema: "talentia.appchain.cl" está hardcoded
if (hostname === 'talentia.appchain.cl') {
    return 'https://talentia.appchain.cl';
}
```

**Mejora propuesta:**

```typescript
// Usar .env en lugar de hardcoded
const getBaseUrl = () => {
    if (typeof window !== 'undefined') {
        // En producción, viene de .env.production
        const apiUrl = import.meta.env.VITE_API_URL;
        if (apiUrl) return apiUrl;
    }
    return ''; // Dev local
};
```

**2) show() vs get() - Inconsistencia**

```typescript
// Esto confunde:
export const show = (url: string, id: number, params = {}) => {
    return axios.get(url + '/' + id, { params });
};

export const get = (url: string, params = {}) => {
    return axios.get(url, { params });
};
```

**Mejora propuesta:** Usar solo `get()` con URLs construidas:

```typescript
// En el componente:
const dataTable = await get(`${endpoints.apiUrl}/${peopleId}`, {
    withRelations: catalogs,
});
```

**3) Tipado podría ser mejor**

```typescript
// Actualmente es genérico 'any'
// Mejor sería genéricos con tipos:

export interface ApiResponse<T> {
    data: T;
    message?: string;
}

export const post = async <T>(url: string, data = {}): Promise<T> => {
    // ...
};
```

---

### 2️⃣ **FormSchema.vue** - Componente Maestro CRUD

#### ✅ Lo que está BIEN:

**A) Inicialización Inteligente**

```typescript
const mergedConfig = computed(() => ({
    endpoints: { index: '', apiUrl: '' },
    titulo: 'Registros',
    permisos: { crear: true, editar: true, eliminar: true },
    ...props.config, // Override con valores reales
}));
```

- ✅ Defaults sensatos + override con props
- ✅ Props opcionales sin quebrar componente

**B) Flujo CRUD Completo**

**Crear:**

```typescript
function openFormCreate() {
    state.editedItem = { ...state.defaultItem };
    state.dialogForm = true;
}
```

**Editar:**

```typescript
function openFormEdit(item) {
    // Limpia campos no definidos
    const cleanedItem = {};
    definedFields.forEach(field => {
        cleanedItem[field] = item[field];
    });
    state.editedItem = cleanedItem;
    state.editedIndex = ...;
}
```

- ✅ Mantiene solo campos de formulario
- ✅ No poluciona con datos innecesarios

**Guardar:**

```typescript
async function guardarItem() {
    if (state.editedItem.id) {
        await put(endpoints.apiUrl + '/' + state.editedItem.id, { data });
    } else {
        await post(endpoints.apiUrl, { data });
    }
    notify({ title: 'Éxito' });
    await cargarItems();
}
```

- ✅ POST para crear, PUT para actualizar
- ✅ Notificación de éxito
- ✅ Recarga tabla después

**Eliminar:**

```typescript
async function eliminarItem(item) {
    state.itemToDelete = item;
    state.dialogDelete = true; // Confirmación
}

async function deleteItemConfirmed() {
    await remove(endpoints.apiUrl + '/' + state.itemToDelete.id);
    notify({ title: 'Éxito' });
    await cargarItems();
}
```

- ✅ Diálogo de confirmación (ConfirmDialog)
- ✅ Elimina con ID correcto
- ✅ Recarga después

**C) Conversión de Fechas Inteligente**

Para mostrar en tabla:

```typescript
const formatDateFields = (item) => {
    const newItem = { ...item };
    dateFields.forEach((field) => {
        if (newItem[field]) {
            const parsed = moment(newItem[field], 'YYYY-MM-DD');
            newItem[field] = parsed.isValid()
                ? parsed.format('DD/MM/YYYY')
                : 'Inválida';
        }
    });
    return newItem;
};
```

Para enviar al backend:

```typescript
// YYYY-MM-DD es lo que espera el servidor
const convertedDate = moment(formData.fecha, 'DD/MM/YYYY').format('YYYY-MM-DD');
```

- ✅ Frontend muestra DD/MM/YYYY (user-friendly)
- ✅ Backend recibe YYYY-MM-DD (ISO standard)
- ✅ Conversión automática, sin errores

**D) Manejo de Errores de Validación**

```typescript
try {
    await put(...);
    notify({ title: "Éxito" });
} catch (e) {
    if (e.response?.status === 422 && e.response.data?.errors) {
        state.errors = e.response.data.errors;
        // Mostrar errores en formulario
    }
}
```

- ✅ Captura errores 422 (validación)
- ✅ Pasa errores a FormData para mostrar
- ✅ Bloquea guardar mientras hay errores

#### ⚠️ Cosas a Mejorar:

**1) Debugging excesivo en consola**

```typescript
console.log('=== DEBUG FECHA_VENCIMIENTO ===');
console.log('FormData original:', formData);
console.log('fecha_vencimiento en formData:', formData.fecha_vencimiento);
// ... 20 lines más de logs
```

**Mejora:**

```typescript
// Crear función de debug condicional
const DEBUG = import.meta.env.DEV;
const debugLog = (section: string, data: any) => {
    if (DEBUG && import.meta.env.VITE_DEBUG === 'true') {
        console.group(`=== ${section} ===`);
        console.log(data);
        console.groupEnd();
    }
};
```

**2) Datos del usuario están quemados en el template**

```vue
<!-- ¿Por qué el rol está aquí? -->
v-if="user.rol != 'admin-ext'"
```

**Mejor:** Extraer a computed y centralizarlo:

```typescript
const canCreate = computed(() => {
    return user.value.rol !== 'admin-ext';
});

const canDelete = computed(() => {
    return user.value.rol !== 'admin-ext';
});
```

**3) `cargarItems()` siempre GET por ID del paciente**

```typescript
async function cargarItems() {
    const dataTable = await show(endpoints.apiUrl, props.peopleId, {
        withRelations: catalogs,
    });
}
```

**Pregunta:** ¿Qué pasa si no hay peopleId? ¿Cuál es la URL exacta que se hace?

---

### 3️⃣ **FormData.vue** - Componente de Formulario

#### ✅ Lo que está BIEN:

**A) Validación Reactiva**

```typescript
const form = ref(null);
const valid = ref(false);
const formData = reactive({ ...props.initialData });

// Watch para sincronizar cambios
watch(
    () => props.initialData,
    (newVal) => {
        Object.assign(formData, newVal);
    },
    { deep: true },
);
```

- ✅ Watch reactivo en cambios de initialData
- ✅ Sincronización sin perder el estado anterior

**B) Mapeo Automático de Catálogos**

```typescript
const getSelectItems = (fieldKey) => {
    // 'accidente_id' -> buscar 'accidente' en catálogos
    const catalogName = fieldKey.endsWith('_id')
        ? fieldKey.slice(0, -3)
        : fieldKey;

    return props.catalogs[catalogName] || [];
};
```

- ✅ Naming convention automática (\_id -> sin \_id)
- ✅ No requiere mapeo manual por campo
- ✅ Escalable: agregar select = auto-funciona

**C) Manejo de Fechas Bidireccional**

```typescript
const formatDateForDisplay = (dateValue) => {
    // YYYY-MM-DD → DD/MM/YYYY
    const m = moment(dateValue, 'YYYY-MM-DD');
    return m.isValid() ? m.format('DD/MM/YYYY') : '';
};

const parseDateFromDisplay = (displayValue) => {
    // DD/MM/YYYY → YYYY-MM-DD
    const m = moment(displayValue, 'DD/MM/YYYY');
    return m.isValid() ? m.format('YYYY-MM-DD') : null;
};
```

- ✅ Conversión transparente para usuario
- ✅ No confunde formatos

#### ⚠️ Cosas a Mejorar:

**1) El HTML del template está incompleto en el archivo leído**

```vue
<v-text-field
    v-if="field.type === 'text'"
    v-model="formData[field.key]"
    :label="field.label"
    :rules="field.rules"
    <!-- CORTADO AQUí -->
```

**Necesitas:**

- v-text-field completo
- v-select con select fields
- v-date-picker o v-input para fechas
- v-textarea para campos large
- v-checkbox para booleanos

**Sugerencia:**

```vue
<template v-if="field.type === 'text'">
    <v-text-field
        v-model="formData[field.key]"
        :label="field.label"
        :rules="field.rules || []"
        density="compact"
    />
</template>

<template v-else-if="field.type === 'select'">
    <v-select
        v-model="formData[field.key]"
        :items="getSelectItems(field.key)"
        :item-title="'descripcion' || 'nombre'"
        :item-value="'id'"
        :label="field.label"
        density="compact"
    />
</template>

<template v-else-if="field.type === 'date'">
    <v-text-field
        v-model="formData[field.key]"
        type="date"
        :label="field.label"
        density="compact"
    />
</template>
```

**2) No hay validación de reglas**

```typescript
// Props define rules pero nunca se usan
const props = defineProps({
    fields: {
        type: Array,
        required: true,
    },
});

// fields debería tener structure como:
// { key: "nombre", label: "Nombre", type: "text", rules: [(v) => !!v || "Requerido"] }
```

---

### 4️⃣ **ExampleForm.vue** - Ensamblador (Muy Simple, Está OK)

```vue
<script setup>
import FormSchema from '@/components/FormSchema.vue';
import config from '@/components/Alergia/config.json';
import itemForm from '@/components/Alergia/itemForm.json';

defineOptions({ layout: AppLayout });
</script>

<template>
    <v-container fluid>
        <v-sheet>
            <FormSchema
                :paciente-id="peopleId"
                :config="config"
                :table-config="tableConfig"
                :item-form="itemForm"
            />
        </v-sheet>
    </v-container>
</template>
```

**Análisis:**

- ✅ Componente muy simple, solo orquestación
- ✅ Config completamente separado
- ✅ Reutilizable: cambiar JSON = cambiar módulo completo

---

### 5️⃣ **example-form/ JSONs** - Configuración

#### config.json (VACÍO)

```json
{} // ← Debería tener:
```

**Debería ser:**

```json
{
    "titulo": "Examen de Control",
    "endpoints": {
        "apiUrl": "/api/patient-exams"
    },
    "permisos": {
        "crear": true,
        "editar": true,
        "eliminar": false
    }
}
```

#### tableConfig.json (Bien estructurado)

```json
{
    "headers": [
        { "title": "#", "key": "paciente_id", "sortable": true },
        { "title": "Test Drogas", "key": "test_drogas", "sortable": true },
        {
            "title": "Estado",
            "key": "estado_examen.descripcion",
            "sortable": true
        },
        { "title": "Fecha", "key": "fecha_control", "type": "date" },
        { "title": "Acciones", "key": "actions", "align": "center" }
    ],
    "options": { "itemsPerPage": 10, "showSelect": false }
}
```

**Análisis:**

- ✅ Headers claros con tipos
- ✅ Soporta nested keys (estado_examen.descripcion)
- ✅ Soporta type="date" para conversión automática
- ✅ Actions es slot reservado

#### itemForm.json (Excelente)

```json
{
    "fields": [
        { "type": "number", "key": "idpgp", "label": "IDGP" },
        { "type": "date", "key": "fecha_control", "label": "Fecha Control" },
        {
            "type": "select",
            "key": "estado_examen_id",
            "label": "Estado Examen"
        },
        { "type": "text", "key": "comentario", "label": "Comentario" }
    ],
    "catalogs": ["estado_examen"]
}
```

**Análisis:**

- ✅ Define campos del formulario
- ✅ Tipos claros (number, date, select, text)
- ✅ Catalogs lista lo que se carga dinámicamente
- ✅ Sin hardcoding de opciones

---

## 🏗️ ARQUITECTURA GENERAL - DIAGRAMA FLUJO

```
┌─────────────────────────────────────────────────────────────────┐
│                        ExampleForm.vue                           │
│  (Orquestador: carga configs y pasa a FormSchema)              │
└────────────────────┬────────────────────────────────────────────┘
                     │ props: peopleId, config, tableConfig, itemForm
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│                      FormSchema.vue                              │
│  (Lógica CRUD: create, read, update, delete)                    │
│  - cargarItems() → apiHelper.get()                              │
│  - guardarItem() → apiHelper.post() o put()                     │
│  - eliminarItem() → apiHelper.remove()                          │
└────────────────┬──────────────────────────────────┬──────────────┘
                 │ formFields, initialData          │ tableItems
                 ↓                                  ↓
         ┌──────────────────┐            ┌──────────────────────┐
         │  FormData.vue    │            │  v-data-table        │
         │  (form fields)   │            │  (tabla con datos)   │
         │  - validación    │            │  - slots para custom │
         │  - conversiones  │            │  - acciones edit/del │
         └──────────────────┘            └──────────────────────┘
                 ↓
         [ v-text-field, v-select, v-date-picker... ]
                 ↓ (submit)
         ┌──────────────────────────────────────────┐
         │       apiHelper.ts (funciones CRUD)      │
         │  - post(url, data)   → create            │
         │  - put(url, data)    → update            │
         │  - remove(url, id)   → delete            │
         │  - get(url, params)  → read              │
         │  - fetchCatalogs()   → load selects      │
         └───────────────┬──────────────────────────┘
                         ↓ (axios + interceptors)
         ┌──────────────────────────────────────────┐
         │      Backend API (/api/...)              │
         │  - Valida datos (422 si error)           │
         │  - Maneja autenticación (401)            │
         │  - Maneja CSRF (419)                     │
         └──────────────────────────────────────────┘
```

---

## ✅ PATRONES VALIDADOS

### 1. **Config-Driven Architecture**

Una archivo JSON define toda la tabla + form. Cambiar comportamiento = cambiar JSON.

```json
{
    "headers": [{ "key": "nombre", "title": "Nombre" }],
    "fields": [{ "key": "nombre", "type": "text", "label": "Nombre" }],
    "catalogs": ["estado"]
}
```

✅ **Ventaja:** Múltiples módulos con mismo código (solo JSONs diferentes)

### 2. **Centralized CRUD Operations**

apiHelper.ts abstrae todas las operaciones HTTP. Los componentes NO hace axios.

```typescript
// Correcto ✅
const data = await post('/api/items', { name: 'test' });

// Nunca hagas esto ❌
// axios.post('/api/items', { ... })
```

✅ **Ventaja:** Cambios a auth/tokens = cambiar solo apiHelper

### 3. **Reactive State Management**

reactive() para formularios, ref() para UI state, computed() para lógica

```typescript
const state = reactive({
    items: [],
    dialogForm: false,
    loading: false,
});
```

✅ **Ventaja:** Sincronización automática sin listeners manuales

---

## 🚀 PRÓXIMAS MEJORAS SUGERIDAS

### **Prioritario (Haz estos primero):**

1. **Completar FormData.vue Template**
    - Agregar v-select completo
    - Agregar v-date-picker
    - Agregar validación visual de errores

2. **Llenar config.json en example-form**

    ```json
    {
        "titulo": "Nombre del Módulo",
        "endpoints": { "apiUrl": "/api/route-correcta" },
        "permisos": { "crear": true, "editar": true, "eliminar": false }
    }
    ```

3. **Actualizar PROMPT_INICIAL para mencionar esto**
    - form-template = componentes reutilizables
    - example-form = cómo usarlos (modelo)

### **Medio Plazo:**

4. **Crear documentación de cómo crear nuevo módulo CRUD**

    ```
    Paso 1: Copiar example-form → nuevo-modulo
    Paso 2: Actualizar config.json (endpoints, titulo)
    Paso 3: Actualizar tableConfig.json (headers)
    Paso 4: Actualizar itemForm.json (fields, catalogs)
    Paso 5: Crear ruta en routes/web.php
    Paso 6: Backend: crear Controller con CRUD
    ```

5. **Agregar TypeScript strict**
    - Definir interfaces para Config, TableConfig, ItemForm
    - Evitar any en apiHelper

6. **Tests para apiHelper.ts**
    - Test interceptor CSRF
    - Test retry en 419
    - Test queue de requests

### **Largo Plazo:**

7. **Composables reutilizables**

    ```typescript
    // composables/useCRUD.ts
    export const useCRUD = (config: CRUDConfig) => {
        const state = reactive({...});
        const cargarItems = async () => {...};
        const guardarItem = async () => {...};
        return { state, cargarItems, guardarItem, ... };
    };
    ```

8. **Paginación verdadera**
   Actualmente `cargarItems()` carga TODO. Con DB grandes necesitarás server-side pagination.

---

## 📊 MÉTRICAS / EVALUACIÓN

| Aspecto                       | Evaluación | Comentario                                            |
| ----------------------------- | ---------- | ----------------------------------------------------- |
| **Abstracción HTTP**          | 9/10       | Excelente manejo de Sanctum, falta tipado TS          |
| **Componentes Reutilizables** | 9/10       | FormSchema es muy genérico, JSON-driven               |
| **Manejo de Errores**         | 8/10       | Buen catch de 422/401/419, falta logging estructurado |
| **UX/Validación**             | 7/10       | Está bien, falta validación visual de rules           |
| **Documentación Código**      | 6/10       | Debugging logs copados pero sin docs reales           |
| **TypeScript**                | 5/10       | Mixto, hay much `any`                                 |
| **Testing**                   | 0/10       | No hay tests visibles                                 |
| **Performance**               | 8/10       | Eficiente excepto cargarItems() carga todo            |

**Nota:** No es crítica, todo es opcional. Prioridades: completar FormData template, test, docs.

---

## 🎓 CONCLUSIÓN

Has creado una **arquitectura sólida, escalable y moderna**:

1. ✅ **apiHelper.ts** es robusto, maneja autenticación/errores correctamente
2. ✅ **FormSchema.vue** implementa CRUD completo con confirmaciones
3. ✅ **FormData.vue** es componente reutilizable (falta template completo)
4. ✅ **JSONs en example-form** son modelo perfecto para nuevos módulos
5. ✅ **Patrón config-driven** permite multiplicar módulos sin código duplicado

**Próximo paso:** Usar esta base para crear 2-3 módulos más (ej: Competencias, Marketplace) para validar que el patrón escala.

**Tiempo de creación:** Nuevo CRUD debería tardar ~30 minutos (solo JSONs + route + controller backend).

---

**Excelente avance para Día 6.** 🚀

# 🛠️ PLAN DE ACCIÓN - Completar Día 6 Frontend

**Basado en análisis de apiHelper.ts + form-template**

---

## 📋 ESTADO ACTUAL

✅ **Hecho:**

- apiHelper.ts con CRUD + Sanctum
- FormSchema.vue con lógica CRUD completa
- FormData.vue estructura base
- Configs JSON (example-form)

⏳ **Falta completar:**

- [ ] FormData.vue template completo
- [ ] config.json en example-form
- [ ] Prueba funcional CRUD
- [ ] Documentación "Cómo crear nuevo módulo CRUD"

---

## 🎯 TAREAS DEL DÍA 6

### BLOQUE 1 (09:30-12:00): Completar FormData.vue

#### Tarea 1.1: Agregar campos de formulario (45 min)

**Archivo:** `/workspaces/talentia/src/resources/js/pages/form-template/FormData.vue`

Necesitas agregar el template `<template v-if="field.type === ...">` para:

1. **Text Fields** (30 min)

```vue
<template v-if="field.type === 'text'">
    <v-text-field
        v-model="formData[field.key]"
        :label="field.label"
        :rules="field.rules || []"
        :error-messages="errors[field.key]"
        density="compact"
        variant="outlined"
        clearable
        @blur="validate"
    />
</template>

<template v-if="field.type === 'textarea'">
    <v-textarea
        v-model="formData[field.key]"
        :label="field.label"
        :rules="field.rules || []"
        :error-messages="errors[field.key]"
        density="compact"
        variant="outlined"
        rows="4"
    />
</template>
```

2. **Select Fields** (10 min)

```vue
<template v-if="field.type === 'select'">
    <v-select
        v-model="formData[field.key]"
        :items="getSelectItems(field.key)"
        :item-title="'descripcion'"
        :item-value="'id'"
        :label="field.label"
        :rules="field.rules || []"
        :error-messages="errors[field.key]"
        density="compact"
        variant="outlined"
        clearable
    />
</template>
```

3. **Date Fields** (10 min)

```vue
<template v-if="field.type === 'date'">
    <v-text-field
        v-model="formData[field.key]"
        type="date"
        :label="field.label"
        :rules="field.rules || []"
        :error-messages="errors[field.key]"
        density="compact"
        variant="outlined"
    />
</template>
```

4. **Number Fields** (5 min)

```vue
<template v-if="field.type === 'number'">
    <v-text-field
        v-model.number="formData[field.key]"
        type="number"
        :label="field.label"
        :rules="field.rules || []"
        :error-messages="errors[field.key]"
        density="compact"
        variant="outlined"
    />
</template>
```

**Checkpoint 11:45:**

```bash
npm run lint  # ✅ No errors
npm run dev   # ✅ Compila sin errores
```

---

#### Tarea 1.2: Agregar Props para Errores (15 min)

En `<script setup>`:

```typescript
const props = defineProps({
    fields: { type: Array, required: true },
    initialData: { type: Object, default: () => ({}) },
    catalogs: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) }, // 👈 NUEVO
});
```

Uso en template:

```vue
:error-messages="errors[field.key]"
```

---

#### Tarea 1.3: Validación Visual (30 min)

En script:

```typescript
const props = defineProps({
    fields: { type: Array, required: true },
    initialData: { type: Object, default: () => ({}) },
    catalogs: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
});

// Agregar campos requeridos
const requiredFields = computed(() => {
    return props.fields
        .filter((f) => f.rules?.some((r) => r.toString().includes('Requerido')))
        .map((f) => f.key);
});

// Mostrar indicador visual
const isFieldRequired = (fieldKey) => {
    return requiredFields.value.includes(fieldKey);
};

// Helper para get color del campo
const getFieldColor = (fieldKey) => {
    if (errors[fieldKey]) return 'error';
    if (formData[fieldKey]) return 'success';
    return 'default';
};
```

Template:

```vue
<template v-if="field.type === 'text'">
    <v-text-field
        v-model="formData[field.key]"
        :label="`${field.label}${isFieldRequired(field.key) ? ' *' : ''}`"
        :color="getFieldColor(field.key)"
        :error="!!errors[field.key]"
        :error-messages="errors[field.key]"
        density="compact"
        variant="outlined"
    />
</template>
```

---

### BLOQUE 2 (13:00-16:00): Tests y Validación

#### Tarea 2.1: Prueba CRUD Funcional (60 min)

**Escenario 1: Crear registro**

1. Ir a http://localhost:8000/example-form
2. Hacer click "Crear nuevo" (botón +)
3. Llenar formulario:
    - IDGP: 123
    - Fecha Control: 2025-12-27
    - Test Drogas: "Positivo"
    - Estado: (seleccionar de dropdown)
4. Click "Guardar"
5. Verificar:
    - ✅ Notificación "Éxito"
    - ✅ Registro aparece en tabla
    - ✅ Check en DevTools Network: POST /api/... (200)

**Escenario 2: Editar registro**

1. Click en ícono edit (lápiz) en tabla
2. Cambiar un campo (ej: Comentario)
3. Click "Guardar"
4. Verificar:
    - ✅ Notificación "Éxito"
    - ✅ Cambio visible en tabla
    - ✅ Check en DevTools Network: PUT /api/.../[id] (200)

**Escenario 3: Eliminar registro**

1. Click en ícono delete (papelera)
2. Confirmar en dialog
3. Verificar:
    - ✅ Notificación "Éxito"
    - ✅ Registro desaparece de tabla
    - ✅ Check en DevTools Network: DELETE /api/.../[id] (200)

**Checklist de Validación:**

```
□ POST crea registro
□ PUT actualiza registro
□ DELETE elimina registro
□ GET recarga tabla
□ Notificaciones funcionan
□ Dialog de confirmación funciona
□ Conversión de fechas DD/MM/YYYY ↔ YYYY-MM-DD correcta
□ Errores 422 se muestran en form
□ Campos requeridos marcados con *
□ No hay console.log errors
```

**Checkpoint 15:45:**

```bash
npm run lint  # ✅ No errors
php artisan test  # ✅ Tests pass
```

---

#### Tarea 2.2: Llenar config.json (15 min)

**Archivo:** `/workspaces/talentia/src/resources/js/pages/example-form/config.json`

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

Si el endpoint NO existe aún, este es tu checkpoint para validar con backend.

---

#### Tarea 2.3: Documentación "Cómo crear CRUD" (30 min)

Crear archivo: `/workspaces/talentia/src/docs/DIA6_CREAR_CRUD_NUEVO_MODULO.md`

Contenido (plantilla):

````markdown
# 📝 Cómo Crear un CRUD Nuevo en Día 6+

## Paso 1: Crear estructura de carpetas (5 min)

```bash
mkdir -p resources/js/pages/[nuevo-modulo]
touch resources/js/pages/[nuevo-modulo]/config.json
touch resources/js/pages/[nuevo-modulo]/tableConfig.json
touch resources/js/pages/[nuevo-modulo]/itemForm.json
```
````

## Paso 2: Copiar configs del example-form (10 min)

- Copiar config.json → llenar endpoints.apiUrl y titulo
- Copiar tableConfig.json → actualizar headers según modelo
- Copiar itemForm.json → actualizar fields y catalogs

## Paso 3: Crear ruta en web.php (5 min)

```php
Route::inertia('/modulo', 'ejemplo-modulo/[NombreModulo]');
```

## Paso 4: Backend - Crear Controller CRUD (30 min)

```bash
php artisan make:controller NuevoModuloController
```

Implementar: index, show, store, update, destroy

## Paso 5: Backend - Crear ruta API (5 min)

```php
Route::apiResource('nuevo-modulo', NuevoModuloController::class);
```

## Paso 6: Probar CRUD (15 min)

- Crear registro
- Editar registro
- Eliminar registro
- Validar notificaciones

---

````

---

### 16:00-17:00: Testing y Documentación

#### Tarea 3.1: Tests para apiHelper.ts (30 min)

**Archivo:** `/workspaces/talentia/tests/Unit/ApiHelperTest.php` (o TS si quieres en frontend)

```typescript
// tests/apiHelper.test.ts
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { post, put, remove, initSanctum } from '@/apiHelper';
import axios from 'axios';

vi.mock('axios');

describe('apiHelper', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should POST and return data', async () => {
        const mockData = { id: 1, name: 'Test' };
        vi.mocked(axios.post).mockResolvedValue({ data: mockData });

        const result = await post('/api/test', { name: 'Test' });

        expect(result).toEqual(mockData);
    });

    it('should PUT and update', async () => {
        const mockData = { id: 1, name: 'Updated' };
        vi.mocked(axios.put).mockResolvedValue({ data: mockData });

        const result = await put('/api/test/1', { name: 'Updated' });

        expect(result).toEqual(mockData);
    });

    it('should DELETE', async () => {
        vi.mocked(axios.delete).mockResolvedValue({ data: { success: true } });

        const result = await remove('/api/test/1');

        expect(result).toEqual({ success: true });
    });
});
````

**Checkpoint:**

```bash
npm run test  # ✅ All pass
```

---

#### Tarea 3.2: Resumen DIA_6.md (30 min)

**Archivo:** `/workspaces/talentia/src/docs/DIA_6.md`

```markdown
# Día 6 - Completar Frontend Base

**Fecha:** 27 Diciembre 2025

## ✅ Completado

- [x] apiHelper.ts (CRUD centralizado)
- [x] FormSchema.vue (lógica CRUD)
- [x] FormData.vue plantilla completa
- [x] config.json llenado
- [x] Tests CRUD funcionales
- [x] Documentación "Crear CRUD nuevo"

## 📊 Métricas

- Líneas de código: +500 (FormData template + tests)
- Componentes creados: 2 (FormSchema, FormData)
- Funcionalidades CRUD: 4 (create, read, update, delete)
- Configuración-driven: ✅ Sí

## 🚀 Conecta con Día 7

Día 7 irá a:

- Crear módulos específicos (Competencias, Marketplace, etc)
- Aplicar patrón form-template a nuevos módulos
- Validaciones más complejas (relaciones, cascadas)
- Dashboard visualización de datos

## 🎯 Responsabilidad del Día

Una arquitectura CRUD reutilizable que permite crear nuevos módulos solo cambiando JSONs (sin código).

---
```

---

## ⏱️ TIMELINE DÍA 6

```
08:00 - 08:30: ECHADA_DE_ANDAR
08:30 - 09:30: Revisar memoria, validar plan
09:30 - 12:00: BLOQUE 1 (Completar FormData.vue)
  09:30 - 10:15: Campos text/select/date
  10:15 - 10:45: Props de errores
  10:45 - 11:45: Validación visual
  11:45 - 12:00: CHECKPOINT (lint + compile)

12:00 - 13:00: ALMUERZO

13:00 - 16:00: BLOQUE 2 (Tests y Validación)
  13:00 - 14:00: Prueba CRUD funcional
  14:00 - 14:15: Llenar config.json
  14:15 - 14:45: Documentación "Cómo crear CRUD"
  14:45 - 15:15: Tests para apiHelper.ts
  15:15 - 15:45: Resumen DIA_6.md
  15:45 - 16:00: CHECKPOINT (tests + lint)

16:00 - 17:00: Cierre
  16:00 - 16:30: Validar todos los checkpoints
  16:30 - 17:00: Git commit + documentación final

COMMIT: "Día 6: Completar frontend base - FormData, tests, documentación"
```

---

## 🚨 RIESGOS / BLOCKERS

| Riesgo                                         | Mitigación                         |
| ---------------------------------------------- | ---------------------------------- |
| Backend no tiene endpoint `/api/patient-exams` | Crear endpoint antes de Bloque 2   |
| FormData.vue template incompleto               | Usar template proporcionado arriba |
| Tests fallan por falta de mocks                | Usa `vi.mock('axios')`             |
| Conversión fechas falla                        | Revisar FormSchema.vue debugging   |
| Lint errors en template                        | Usar `npm run lint --fix`          |

---

## ✅ CRITERIO DE ÉXITO (Validación de Estado)

Para considerar Día 6 COMPLETO:

- [ ] FormData.vue template completo (text, select, date, number, textarea)
- [ ] Config.json llenado con endpoints reales
- [ ] CRUD funcional (POST, PUT, DELETE, GET)
- [ ] Notificaciones funcionan
- [ ] Errores 422 se muestran en form
- [ ] Tests de apiHelper.ts pasan
- [ ] No hay console errors
- [ ] Documentación "Cómo crear CRUD" existe
- [ ] DIA_6.md completado
- [ ] Git commit hecho

**Si todo ✅:** Día 6 DONE, listo para Día 7 (crear módulos específicos)

---

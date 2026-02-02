# Fix: Campos Select en Formularios (Skills - Tipo y Categoría)

## Problema Reportado

Los campos `category`, `complexity_level` y `scope_type` en el formulario de Skills aparecían como **v-text-field** (campos de texto) en lugar de **v-select** (dropdown), lo que impedía que se guardaran o actualizaran correctamente.

**Síntomas:**

- Campo `category` permitía texto libre en lugar de seleccionar de opciones
- Los valores guardados no correspondían a los enum definidos en BD
- No había validación contra valores permitidos

## Causa Raíz

El archivo de configuración `itemForm.json` de Skills tenía el campo `category` definido como:

```json
{
  "key": "category",
  "label": "Category",
  "type": "text", // ❌ INCORRECTO: type=text en lugar de select
  "placeholder": "e.g., Technical, Soft Skills, Management"
}
```

Pero en la BD, `category` es un **ENUM** con valores fijos:

```php
$table->enum('category', ['technical', 'soft', 'business', 'language'])->default('technical');
$table->enum('complexity_level', ['operative', 'tactical', 'strategic'])->default('tactical');
$table->enum('scope_type', ['transversal', 'domain', 'specific'])->default('domain');
```

## Solución Implementada

### Cambio en itemForm.json

Se actualizó `src/resources/js/pages/Skills/skills-form/itemForm.json` para:

1. **Cambiar `category` de text a select** con opciones del enum:

```json
{
  "key": "category",
  "label": "Category",
  "type": "select", // ✅ CORRECTO
  "rules": ["required"],
  "items": [
    { "id": "technical", "name": "Technical" },
    { "id": "soft", "name": "Soft Skills" },
    { "id": "business", "name": "Business" },
    { "id": "language", "name": "Language" }
  ]
}
```

2. **Agregar `complexity_level` como select**:

```json
{
  "key": "complexity_level",
  "label": "Complexity Level",
  "type": "select",
  "items": [
    { "id": "operative", "name": "Operative" },
    { "id": "tactical", "name": "Tactical" },
    { "id": "strategic", "name": "Strategic" }
  ]
}
```

3. **Agregar `scope_type` como select**:

```json
{
  "key": "scope_type",
  "label": "Scope Type",
  "type": "select",
  "items": [
    { "id": "transversal", "name": "Transversal" },
    { "id": "domain", "name": "Domain" },
    { "id": "specific", "name": "Specific" }
  ]
}
```

4. **Agregar campo `domain_tag` como text**:

```json
{
  "key": "domain_tag",
  "label": "Domain Tag",
  "type": "text",
  "placeholder": "e.g., Sales, IT, Legal, Marketing"
}
```

5. **Agregar campo `is_critical` como switch**:

```json
{
  "key": "is_critical",
  "label": "Is Critical",
  "type": "switch"
}
```

## Cómo Funcionan los Selects en el Sistema JSON-Driven

### Componente FormData.vue

El componente renderiza automáticamente según `field.type`:

```vue
<!-- SELECT (Dropdown) -->
<v-select
    v-else-if="field.type === 'select'"
    v-model="formData[field.key]"
    :items="field.items || getSelectItems(field.key)"
    :label="field.label"
    item-title="name"      <!-- 👈 Busca propiedad "name" para mostrar -->
    item-value="id"        <!-- 👈 Busca propiedad "id" para guardar -->
    :rules="field.rules"
/>
```

### Estructura de un Item en el Array

Cada item en `field.items` DEBE tener esta estructura:

```json
{
  "id": "valor_que_se_guarda", // ← Se guarda en BD
  "name": "Texto que se muestra" // ← Se muestra en dropdown
}
```

**NO usar:**

- ❌ `value`/`title` (Vuetify 2 legacy)
- ❌ `key`/`label`
- ❌ Valores directos como strings

**Usar SIEMPRE:**

- ✅ `id` - Valor que se persiste en BD
- ✅ `name` - Texto que ve el usuario

### Dos Formas de Proveer Opciones

#### 1. Hardcoded en itemForm.json (Para enums fijos)

```json
{
  "key": "category",
  "type": "select",
  "items": [
    { "id": "technical", "name": "Technical" },
    { "id": "soft", "name": "Soft Skills" }
  ]
}
```

**Caso de uso:** Enums en la BD que NO cambian

#### 2. Catálogos dinámicos (Para datos que cambian)

```json
{
  "key": "department_id",
  "type": "select",
  "placeholder": "Select a department"
}
```

Y agregar `"catalogs": ["departments"]` en `itemForm.json`:

El componente automáticamente:

- Carga desde `/api/catalogs?catalogs=departments`
- Busca items con `id`/`name`

**Caso de uso:** Datos de referencia que vienen de una tabla (departamentos, roles)

## Archivos Modificados

- **Modificado:** `src/resources/js/pages/Skills/skills-form/itemForm.json`
  - Cambió `category` de text → select
  - Agregó `complexity_level` como select
  - Agregó `scope_type` como select
  - Agregó `domain_tag` como text
  - Agregó `is_critical` como switch

## Impacto en BD

**Sin cambios en la BD.** Solo se actualizó la configuración del formulario frontend para:

- Respetar los enum existentes
- Guardar valores válidos
- Prevenir valores inválidos (validación en UI)

## Testing

Para verificar que el fix funciona:

```bash
cd /home/omar/Stratos/src

# 1. Abrir Skills en el navegador
npm run dev

# 2. Crear una nueva Skill
# - Nombre: "Cloud Architecture"
# - Category: Selecciona "Technical" (dropdown, no text)
# - Complexity Level: Selecciona "Strategic"
# - Scope Type: Selecciona "Domain"
# - Domain Tag: "IT"
# - Is Critical: Toggle ON

# 3. Guardar y verificar
# - Debe guardarse sin errores
# - Al cargar de nuevo, debe mostrar los valores seleccionados
```

## Guía Rápida: Agregar Select a Otro CRUD

Si necesitas agregar campos select a otro formulario (Roles, People, etc.):

### Paso 1: Identificar el enum en migración

```php
$table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
```

### Paso 2: Actualizar itemForm.json

```json
{
  "key": "status",
  "label": "Status",
  "type": "select",
  "items": [
    { "id": "draft", "name": "Draft" },
    { "id": "active", "name": "Active" },
    { "id": "inactive", "name": "Inactive" }
  ]
}
```

### Paso 3: Verificar guardado

El componente FormData.vue automáticamente:

- Renderiza como dropdown ✅
- Guarda el valor del `id` ✅
- Valida según `rules` ✅

## Referencia: Tipos de Campo Soportados

| Type       | Control               | Cuando usarlo           |
| ---------- | --------------------- | ----------------------- |
| `text`     | v-text-field          | Nombres, textos cortos  |
| `email`    | v-text-field (email)  | Correos                 |
| `number`   | v-text-field (number) | Cantidades, IDs         |
| `textarea` | v-textarea            | Descripciones largas    |
| `select`   | v-select              | Enums, catálogos        |
| `date`     | v-text-field (date)   | Fechas                  |
| `time`     | v-text-field (time)   | Horas                   |
| `switch`   | v-switch              | Booleanos on/off        |
| `checkbox` | v-checkbox            | Booleanos check/uncheck |

## Problemas Comunes y Soluciones

### Problema 1: Select no muestra opciones

**Causa:** Propiedad equivocada en items

```json
{ "value": "tech", "label": "Technical" }  // ❌ Usa "value"/"label"
{ "id": "tech", "name": "Technical" }      // ✅ Usa "id"/"name"
```

### Problema 2: Se guarda undefined o null

**Causa:** Mismatch entre lo que selecciona y lo que guarda

```json
{ "id": 1, "name": "Technical" } // ✅ ID es el valor a guardar
// Si se selecciona "Technical", se guarda 1
```

### Problema 3: El formulario no renderiza

**Causa:** Falta la propiedad `items` o está vacía

```json
{
  "key": "category",
  "type": "select",
  "items": [] // ✅ Array vacío OK, FormData buscará catálogo
}
```

Si ni `items` ni catálogo existe, usa opción 1 (hardcoded).

## Próximas Mejoras Recomendadas

1. **Crear tabla de enums** en migraciones para documentar valores fijos
2. **Auto-generar itemForm.json** a partir de migraciones (analyzeSchema)
3. **Agregar validación backend** que rechace valores fuera del enum
4. **Crear seeder con catálogos** para evitar hardcoding

---

**Fecha:** 2026-01-29
**Branch:** feature/workforce-planning-scenario-modeling
**Archivos:** `src/resources/js/pages/Skills/skills-form/itemForm.json`

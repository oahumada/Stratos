# 🎨 DISEÑO UX/UI - PASO 2: Mapeo Roles ↔ Competencias

**Contexto:** En el Paso 1 diseñamos Escenarios, Capacidades, Competencias y Skills.  
**Objetivo Paso 2:** Conectar Competencias → Roles (bidireccional) con transiciones de estado.

**Problema a resolver:** Representar de forma **simple, directa y visual** la asociación de competencias a roles y las transiciones de estado (extinción, transformación, enriquecimiento).

---

## 🎯 Requerimientos Clave

### Datos a manejar:

1. **Roles** (existentes o nuevos)
   - Nombre, descripción, familia, nivel
   - Crear novo vs. usar existente

2. **Competencias** (del Paso 1)
   - Nombre, categoría, nivel requerido
   - Asociación futura a este rol

3. **Estados/Transiciones de Competencia en Rol:**
   - 🔄 **TRANSFORMACIÓN** - Competencia se redimensiona (requiere upskilling)
   - 📉 **EXTINCIÓN** - Competencia desaparece del rol (será obsoleta)
   - 📈 **ENRIQUECIMIENTO** - Competencia nueva/mejorada (skill nuevo o superior)
   - ✅ **MANTENCIÓN** - Competencia se mantiene igual (implícita)

4. **Contexto temporal:**
   - Horizonte (ej: 12, 18, 24 meses)
   - Fase: actual → futuro

---

## 🖼️ OPCIÓN 1: Matriz Rol-Competencia Interactiva (Recomendada)

### Concepto:

Tabla bidimensional donde:

- **Filas** = Roles
- **Columnas** = Competencias del Paso 1
- **Celdas** = Estado + Acciones

### Layout:

```
┌──────────────────────────────────────────────────────────────────────────┐
│  📋 MAPEO: Roles ↔ Competencias                                          │
│                                                                           │
│  Escenario: [Seleccionar] | Horizonte: [12 meses] | [+ Nuevo Rol]      │
└──────────────────────────────────────────────────────────────────────────┘

┌─────────────────┬──────────────┬──────────────┬──────────────┬─────┐
│ ROL             │ Cloud Arch   │ Data Science │ Leadership   │ ... │
│ (10)            │ (Crítico)    │ (Crítico)    │ (Conductual) │     │
├─────────────────┼──────────────┼──────────────┼──────────────┼─────┤
│ Software Eng    │ ✅ MANT      │ 🔄 TRANSF   │ 📈 ENRIQ     │ ... │
│ Senior (Existe) │              │              │              │     │
│ 5 FTE           │              │              │              │     │
├─────────────────┼──────────────┼──────────────┼──────────────┼─────┤
│ Data Analyst    │ 📈 ENRIQ     │ ✅ MANT      │ 📈 ENRIQ     │ ... │
│ (Nuevo)         │              │              │              │     │
│ [+ crear]       │              │              │              │     │
├─────────────────┼──────────────┼──────────────┼──────────────┼─────┤
│ Ops Manager     │ 📉 EXTINC    │ ✅ MANT      │ ✅ MANT      │ ... │
│ (Existe)        │              │              │              │     │
│ 2 FTE           │              │              │              │     │
├─────────────────┼──────────────┼──────────────┼──────────────┼─────┤
│ ... (más roles) │              │              │              │     │
└─────────────────┴──────────────┴──────────────┴──────────────┴─────┘
```

### Interactividad:

**Hacer clic en una celda → Modal contextual:**

```
┌─────────────────────────────────────────────────────────┐
│ Competencia: Cloud Architecture                         │
│ Rol: Software Engineer Senior                          │
│ ─────────────────────────────────────────────────────── │
│                                                         │
│ Estado actual del rol: MANTENCIÓN                      │
│ ┌─────────────────────────────────────────────────┐   │
│ │ ○ ✅ MANTENCIÓN (Sin cambios esperados)        │   │
│ │ ○ 🔄 TRANSFORMACIÓN (Requiere upskilling)     │   │
│ │ ○ 📈 ENRIQUECIMIENTO (Nueva o mejorada)       │   │
│ │ ○ 📉 EXTINCIÓN (Desaparecerá)                 │   │
│ └─────────────────────────────────────────────────┘   │
│                                                         │
│ Notas (opcional):                                      │
│ [________________________________]                     │
│                                                         │
│ Si seleccionas TRANSFORMACIÓN:                         │
│ ├─ Nivel actual: 3 (Intermedio)                       │
│ ├─ Nivel futuro: 4 (Avanzado) ← Selector             │
│ ├─ Timeline: 12 meses                                 │
│ └─ Proponer learning path: [Sí / No]                 │
│                                                         │
│ Si seleccionas EXTINCIÓN:                              │
│ ├─ Timeline desaparición: [12 / 18 / 24] meses      │
│ ├─ Plan transición:                                   │
│ │  ☐ Reskilling a otra competencia                   │
│ │  ☐ Desvincular                                      │
│ │  ☐ Cambio de rol                                    │
│ └─ Responsable: [Select manager]                      │
│                                                         │
│ [Guardar] [Cancelar]                                  │
└─────────────────────────────────────────────────────────┘
```

### Ventajas:

✅ **Simple:** Visualización de matriz estándar, familar  
✅ **Completa:** Muestra todos los roles + competencias de un vistazo  
✅ **Escalable:** Funciona con muchos roles/competencias (scroll)  
✅ **Accionable:** Cada celda → decisión clara  
✅ **Contextual:** Modales proporcionan detalles según tipo

### Desventajas:

❌ Mucha información en pantalla (si hay muchos roles/skills)  
❌ Requiere scroll horizontal (muchas competencias)

---

## 🖼️ OPCIÓN 2: Card-Based / Role-First (Alternativa)

### Concepto:

Navegar **por rol**. Cada rol es una "tarjeta" que muestra sus competencias asociadas.

### Layout:

```
┌─────────────────────────────────────────────────────────┐
│  📋 MAPEO: Roles ↔ Competencias                        │
│  Escenario: [Seleccionar] | [+ Nuevo Rol]             │
└─────────────────────────────────────────────────────────┘

[Software Engineer Senior]  [Data Analyst (Nuevo)]  [Ops Manager]  [...]

┌──────────────────────────────────────────────────────┐
│ 🔧 Software Engineer Senior                           │
│ Familia: Ingeniería | Nivel: Senior | FTE: 5         │
│ Estado: Existente                                     │
│                                                       │
│ COMPETENCIAS:                                         │
│ ┌────────────────────────────────────────────────┐   │
│ │ 📌 Cloud Architecture          [✅ Mantención] │   │
│ │    Nivel: 3→4 | Timeline: 12mo                │   │
│ │    [Editar] [Eliminar]                        │   │
│ └────────────────────────────────────────────────┘   │
│                                                       │
│ ┌────────────────────────────────────────────────┐   │
│ │ 🔄 Data Science                [🔄 Transform] │   │
│ │    Nivel: 2→3 | Timeline: 18mo                │   │
│ │    Learning Path: Propuesto ✓                 │   │
│ │    [Editar] [Ver detalle]                     │   │
│ └────────────────────────────────────────────────┘   │
│                                                       │
│ ┌────────────────────────────────────────────────┐   │
│ │ 📈 Leadership Ágil             [📈 Enriquece] │   │
│ │    Nivel: Nuevo | Timeline: 12mo              │   │
│ │    [Editar] [Eliminar]                        │   │
│ └────────────────────────────────────────────────┘   │
│                                                       │
│ [+ Agregar competencia]                             │
│                                                       │
│ [Resumen] [Editar rol] [Eliminar rol]              │
└──────────────────────────────────────────────────────┘
```

### Interactividad:

**Tabs horizontales entre roles** + **Clic en competencia → detalle inline o modal**

### Ventajas:

✅ **Enfoque:** Un rol a la vez  
✅ **Claro:** Menos información por pantalla  
✅ **Intuitivo:** Navegar rol por rol  
✅ **Flexible:** Agregar/eliminar competencias fácilmente  
✅ **Progresivo:** Completar un rol antes de ir al siguiente

### Desventajas:

❌ No ve "mapa completo" de competencias vs roles  
❌ Más clics para comparar entre roles

---

## 🖼️ OPCIÓN 3: Grafo/Red Visual (Avanzada)

### Concepto:

Visualización tipo **network diagram** donde:

- **Nodos = Roles + Competencias**
- **Enlaces = Asociaciones + Estados**
- **Colores = Estados (extinción, transformación, enriquecimiento)**

### Ventajas:

✅ **Visualización potente:** Ve relaciones complejas  
✅ **Pattern discovery:** Identifica clústeres de competencias  
✅ **Interactivo:** Drag-drop para reorganizar

### Desventajas:

❌ **Complejo:** Curva de aprendizaje  
❌ **Escala:** Difícil con muchos nodos (cluttered)  
❌ **Acción:** Menos directo para editar estados

---

## 🎬 FLUJO RECOMENDADO: OPCIÓN 1 (Matriz)

### Paso a paso:

#### **Paso 2.1: Selector de contexto**

```
[Escenario▼] [Horizonte temporal: 12 meses▼] [+ Nuevo Rol]
```

#### **Paso 2.2: Matriz rol × competencia**

- Mostrar matriz con todos los roles (existentes + nuevos)
- Cada competencia como columna
- Códigos de color:
  - ✅ Verde: Mantención
  - 🔄 Azul: Transformación
  - 📈 Verde claro: Enriquecimiento
  - 📉 Rojo: Extinción
  - ⚪ Gris: Sin asociación (vacío)

#### **Paso 2.3: Edición modal**

- Clic en celda → modal contextual
- Selector de estado (radio buttons)
- Campos condicionales según estado:
  - **MANTENCIÓN:** Nada (implícito)
  - **TRANSFORMACIÓN:** Nivel actual → futuro + timeline + learning path
  - **ENRIQUECIMIENTO:** Nivel nuevo + timeline
  - **EXTINCIÓN:** Timeline + plan transición

#### **Paso 2.4: Acciones bulk (opcional)**

```
[ ] Seleccionar todas las transformaciones
[ ] Seleccionar todas las extinc iones
[Generar learning paths para seleccionadas]
[Generar plan de transición para seleccionadas]
```

---

## 📊 Datos que debe soportar la UI

### Por cada asociación (Rol ↔ Competencia):

```typescript
interface RoleCompetencyMapping {
  roleId: string;
  competencyId: string;
  scenarioId: string;

  state: "maintenance" | "transformation" | "enrichment" | "extinction";

  currentLevel: number; // 1-5
  targetLevel?: number; // Si state = transformation/enrichment

  timeline: number; // meses

  notes?: string;

  // Si state = transformation:
  learningPathSuggested?: boolean;
  learningPathId?: string;

  // Si state = extinction:
  transitionPlan?: "reskilling" | "role_change" | "devinculacion";
  responsibleManager?: string;

  createdAt: Date;
  updatedAt: Date;
}
```

---

## 🎨 Recomendación Final

### Para Paso 2: **OPCIÓN 1 (Matriz Interactiva)**

**Por qué:**

1. ✅ Cumple requisito: "simple, simple y directa"
2. ✅ Escalable: soporta muchos roles/competencias
3. ✅ Accionable: cada decisión clara
4. ✅ Familiar: matriz estándar (como Excel)
5. ✅ Implementable: componente Vuetify table + modales

**Componentes necesarios:**

- `RoleCompetencyMatrix.vue` - Tabla principal
- `RoleCompetencyStateModal.vue` - Modal de edición
- `RoleCompetencyStore.ts` (Pinia) - Estado centralizado

**APIs a conectar:**

- `GET /api/v1/roles` - Cargar roles (existentes + nuevos)
- `GET /api/v1/skills` - Cargar competencias (Paso 1)
- `POST/PUT /api/v1/role-competency-mappings` - Guardar asociaciones
- `DELETE /api/v1/role-competency-mappings/{id}` - Eliminar

---

## 🔄 Flujo Completo (Integración con Paso 1)

```
PASO 1: Diseñar Escenario + Capacidades + Competencias + Skills
   ↓ [Guardar]
   ├─ skills[] creadas
   ├─ competencies[] creadas
   └─ scenario_id establecido

PASO 2: Mapear Roles ↔ Competencias
   ↓ [Usar Matrix]
   ├─ Seleccionar escenario (from Paso 1)
   ├─ Crear/seleccionar roles
   ├─ Asociar competencias a roles
   ├─ Definir estados (transformación, extinción, etc.)
   └─ [Guardar matriz completa]

PASO 3: Proyectar FTE y Demanda
   ↓ [Usar data de Paso 2]
   ├─ Roles + headcount
   ├─ Competencias requeridas
   └─ Estados para identificar gaps
```

---

## 📋 Checklist Implementación

- [ ] Diseño Figma/sketch de matriz
- [ ] Definir componente `RoleCompetencyMatrix.vue`
- [ ] Definir store Pinia para state management
- [ ] Crear modal `RoleCompetencyStateModal.vue`
- [ ] Conectar APIs backend (si existen)
- [ ] Validaciones (nivel actual < futuro, etc.)
- [ ] Mensajes de error/success
- [ ] Bulk actions (opcional)
- [ ] Export a CSV/PDF (opcional)
- [ ] Tests unitarios

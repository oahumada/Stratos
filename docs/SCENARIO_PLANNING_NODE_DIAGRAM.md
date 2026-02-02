# ScenarioPlanning - Diagrama de Nodos

Este documento describe la arquitectura del sistema de nodos visuales en el módulo ScenarioPlanning.

## Índice

1. [Estructura Jerárquica](#estructura-jerárquica)
2. [Fuentes de Datos Reactivas](#fuentes-de-datos-reactivas)
3. [Diagrama Visual](#diagrama-visual)
4. [Flujo de Renderizado](#flujo-de-renderizado)
5. [Flujo de Actualización](#flujo-de-actualización)
6. [Componentes Clave](#componentes-clave)

---

## Estructura Jerárquica

El sistema de nodos sigue una jerarquía de 3 niveles:

```
┌─────────────────────────────────────────────────────────────┐
│                      SCENARIO                                │
│                    (Nodo Raíz)                              │
└─────────────────────┬───────────────────────────────────────┘
                      │
        ┌─────────────┼─────────────┐
        │             │             │
        ▼             ▼             ▼
┌───────────┐  ┌───────────┐  ┌───────────┐
│ CAPABILITY│  │ CAPABILITY│  │ CAPABILITY│   ← Nivel 1 (nodes[])
│  (Cap A)  │  │  (Cap B)  │  │  (Cap C)  │
└─────┬─────┘  └─────┬─────┘  └───────────┘
      │              │
      │        ┌─────┴─────┐
      │        │           │
      ▼        ▼           ▼
┌──────────┐ ┌──────────┐ ┌──────────┐
│COMPETENCY│ │COMPETENCY│ │COMPETENCY│        ← Nivel 2 (childNodes[])
│ (Comp 1) │ │ (Comp 2) │ │ (Comp 3) │
└────┬─────┘ └────┬─────┘ └──────────┘
     │            │
     │      ┌─────┼─────┐
     │      │     │     │
     ▼      ▼     ▼     ▼
┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐
│ SKILL │ │ SKILL │ │ SKILL │ │ SKILL │       ← Nivel 3 (grandChildNodes[])
│ (S1)  │ │ (S2)  │ │ (S3)  │ │ (S4)  │
└───────┘ └───────┘ └───────┘ └───────┘
```

### Descripción de Niveles

| Nivel | Entidad    | Ref Vue             | Descripción                              |
| ----- | ---------- | ------------------- | ---------------------------------------- |
| 0     | Scenario   | `props.scenario`    | Contenedor principal, define el contexto |
| 1     | Capability | `nodes[]`           | Capacidades organizacionales             |
| 2     | Competency | `childNodes[]`      | Competencias dentro de cada capacidad    |
| 3     | Skill      | `grandChildNodes[]` | Habilidades dentro de cada competencia   |

---

## Fuentes de Datos Reactivas

El sistema mantiene **múltiples representaciones** de los mismos datos para diferentes propósitos:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        FUENTES DE DATOS                                  │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │ nodes.value[]                                    [RAÍZ]         │    │
│  │   └── competencies[]                                            │    │
│  │         └── skills[]                                            │    │
│  │                                                                  │    │
│  │ Propósito: Fuente principal de datos. Persiste entre           │    │
│  │            navegaciones. Usada por buildNodesFromItems().       │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│                              │                                           │
│                              ▼                                           │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │ focusedNode.value                               [CAPABILITY]    │    │
│  │   └── competencies[]                                            │    │
│  │         └── skills[]                                            │    │
│  │                                                                  │    │
│  │ Propósito: Referencia a la capability actualmente expandida.   │    │
│  │            Fuente para expandCompetencies().                    │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│                              │                                           │
│                              ▼                                           │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │ childNodes.value[]                              [COMPETENCIES]  │    │
│  │   └── skills[]                                                  │    │
│  │   └── raw.skills[]                                              │    │
│  │                                                                  │    │
│  │ Propósito: Nodos renderizados de competencias. Incluyen        │    │
│  │            posición (x,y), animaciones, displayName.            │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│                              │                                           │
│                              ▼                                           │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │ selectedChild.value                             [COMPETENCY]    │    │
│  │   └── skills[]                                                  │    │
│  │   └── raw.skills[]                                              │    │
│  │                                                                  │    │
│  │ Propósito: Competencia actualmente seleccionada (click).       │    │
│  │            Fuente para expandSkills() y panel lateral.          │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│                              │                                           │
│                              ▼                                           │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │ grandChildNodes.value[]                         [SKILLS]        │    │
│  │   └── raw (skill data)                                          │    │
│  │                                                                  │    │
│  │ Propósito: Nodos renderizados de skills. Incluyen              │    │
│  │            posición (x,y), animaciones, edges a competencia.    │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

### Regla Crítica

> **Cuando se modifica un nodo, TODAS las fuentes deben actualizarse desde la hoja hasta la raíz.**

Esto se maneja con el composable `useHierarchicalUpdate`.

---

## Diagrama Visual

### Layout en Pantalla

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                          │
│                         ┌─────────────┐                                  │
│                         │  SCENARIO   │  ← Nodo central (arriba)        │
│                         │   (root)    │                                  │
│                         └──────┬──────┘                                  │
│                                │                                         │
│              ┌─────────────────┼─────────────────┐                      │
│              │                 │                 │                      │
│              ▼                 ▼                 ▼                      │
│        ┌──────────┐      ┌──────────┐      ┌──────────┐                │
│        │ Cap A    │      │ Cap B    │      │ Cap C    │  ← Radial     │
│        │  ○       │      │  ●       │      │  ○       │    layout     │
│        └──────────┘      └────┬─────┘      └──────────┘                │
│                               │  (focused)                              │
│                               │                                         │
│              ┌────────────────┼────────────────┐                       │
│              │                │                │                       │
│              ▼                ▼                ▼                       │
│        ┌──────────┐     ┌──────────┐     ┌──────────┐                 │
│        │ Comp 1   │     │ Comp 2   │     │ Comp 3   │  ← Matrix/     │
│        │  ○       │     │  ●       │     │  ○       │    Sides       │
│        └──────────┘     └────┬─────┘     └──────────┘    layout      │
│                              │  (selected)                             │
│                              │                                         │
│                    ┌─────────┼─────────┐                              │
│                    │         │         │                              │
│                    ▼         ▼         ▼                              │
│              ┌─────────┐ ┌─────────┐ ┌─────────┐                      │
│              │ Skill 1 │ │ Skill 2 │ │ Skill 3 │  ← Matrix layout    │
│              │   □     │ │   □     │ │   □     │                      │
│              └─────────┘ └─────────┘ └─────────┘                      │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

Leyenda:
  ○ = Nodo no seleccionado
  ● = Nodo seleccionado/focused
  □ = Skill node
```

### Layouts Disponibles

| Layout   | Uso                                 | Configuración                             |
| -------- | ----------------------------------- | ----------------------------------------- |
| `radial` | Capabilities alrededor del scenario | `LAYOUT_CONFIG.capability.radial`         |
| `matrix` | Competencies en grid                | `LAYOUT_CONFIG.competency.matrixVariants` |
| `sides`  | Competencies a los lados            | `LAYOUT_CONFIG.competency.sides`          |
| `matrix` | Skills debajo de competency         | `LAYOUT_CONFIG.skill.defaultLayout`       |

---

## Flujo de Renderizado

### 1. Carga Inicial

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. loadTreeFromApi(scenarioId)                                  │
│    └── GET /api/strategic-planning/scenarios/{id}/capability-tree│
│                                                                  │
│ 2. buildNodesFromItems(capabilities)                            │
│    └── Crea nodes[] con posiciones (x, y)                       │
│                                                                  │
│ 3. Template SVG renderiza nodes[]                               │
│    └── v-for="n in nodes" → círculos de capabilities            │
└─────────────────────────────────────────────────────────────────┘
```

### 2. Expandir Capability (click)

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. handleNodeClick(node) → detecta tipo 'capability'            │
│                                                                  │
│ 2. centerOnNode(node) → anima cámara al nodo                    │
│                                                                  │
│ 3. focusedNode.value = node                                     │
│                                                                  │
│ 4. expandCompetencies(node, position, { layout })               │
│    ├── Lee node.competencies[]                                  │
│    ├── Calcula posiciones según layout (matrix/radial/sides)    │
│    ├── Crea childNodes[] con animación                          │
│    └── Crea childEdges[] (conexiones visuales)                  │
│                                                                  │
│ 5. Template SVG renderiza childNodes[]                          │
│    └── v-for="c in childNodes" → círculos de competencies       │
└─────────────────────────────────────────────────────────────────┘
```

### 3. Expandir Competency (click)

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. handleNodeClick(node) → detecta tipo 'competency'            │
│                                                                  │
│ 2. selectedChild.value = node                                   │
│                                                                  │
│ 3. expandSkillsFromLayout(node, existing, edges, pos, opts)     │
│    ├── Lee node.skills[] o selectedChild.value.skills[]         │
│    ├── Calcula posiciones (matrix layout centrado)              │
│    ├── Crea grandChildNodes[] con animación                     │
│    └── Crea grandChildEdges[] (conexiones curvas)               │
│                                                                  │
│ 4. Template SVG renderiza grandChildNodes[]                     │
│    └── v-for="s in grandChildNodes" → rectángulos de skills     │
└─────────────────────────────────────────────────────────────────┘
```

---

## Flujo de Actualización

### Editar un Skill

```
┌─────────────────────────────────────────────────────────────────┐
│ Usuario edita skill en modal → saveSkillDetail()                │
│                                                                  │
│ 1. PATCH /api/skills/{id} → Guarda en BD                        │
│                                                                  │
│ 2. GET /api/skills/{id} → Obtiene datos frescos (freshSkill)    │
│                                                                  │
│ 3. hierarchicalUpdate.update('skill', freshSkill, compId)       │
│    │                                                             │
│    ├── ① grandChildNodes.value = map(...)                       │
│    │      └── Actualiza nodo visual                             │
│    │                                                             │
│    ├── ② selectedChild.value.skills = map(...)                  │
│    │      └── Actualiza referencia seleccionada                 │
│    │                                                             │
│    ├── ③ childNodes.value[].skills = map(...)                   │
│    │      └── Actualiza nodos de competencia                    │
│    │                                                             │
│    ├── ④ focusedNode.value.competencies[].skills = map(...)     │
│    │      └── Actualiza fuente para expandCompetencies()        │
│    │                                                             │
│    └── ⑤ nodes.value[].competencies[].skills = map(...)         │
│           └── Actualiza fuente raíz                              │
│                                                                  │
│ 4. await nextTick() → Vue re-renderiza                          │
└─────────────────────────────────────────────────────────────────┘
```

### ¿Por qué actualizar todas las fuentes?

```
┌─────────────────────────────────────────────────────────────────┐
│ PROBLEMA: Usuario edita skill "A" → cambia nombre a "B"         │
│                                                                  │
│ Si SOLO actualizamos grandChildNodes:                           │
│   ✓ UI muestra "B" inmediatamente                               │
│                                                                  │
│ Usuario colapsa competencia (click en capability)               │
│   → childNodes.value = [] (se limpia)                           │
│   → grandChildNodes.value = [] (se limpia)                      │
│                                                                  │
│ Usuario re-expande competencia                                  │
│   → expandCompetencies() lee de focusedNode.competencies[]      │
│   → focusedNode todavía tiene nombre "A"                        │
│   ✗ UI muestra "A" otra vez (¡datos antiguos!)                  │
│                                                                  │
│ SOLUCIÓN: Actualizar TODAS las fuentes hacia arriba             │
└─────────────────────────────────────────────────────────────────┘
```

---

## Componentes Clave

### Archivos Principales

| Archivo                                | Propósito                                |
| -------------------------------------- | ---------------------------------------- |
| `pages/ScenarioPlanning/Index.vue`     | Componente principal, SVG, handlers      |
| `composables/useHierarchicalUpdate.ts` | Actualización consistente de datos       |
| `composables/useScenarioLayout.ts`     | Configuración de layouts (LAYOUT_CONFIG) |
| `composables/useScenarioEdges.ts`      | Cálculo de conexiones (curvas Bézier)    |
| `composables/useScenarioState.ts`      | Estado compartido                        |
| `composables/useScenarioAPI.ts`        | Llamadas al backend                      |

### Estructura de un Nodo

```typescript
// Capability Node (nodes[])
{
  id: number,              // ID de la capability
  name: string,            // Nombre
  displayName: string,     // Nombre formateado (wrap)
  x: number,               // Posición X
  y: number,               // Posición Y
  competencies: [{         // Competencias hijas
    id: number,
    name: string,
    skills: [{...}]        // Skills de la competencia
  }],
  raw: {...}               // Datos originales de la API
}

// Competency Node (childNodes[])
{
  id: number,              // ID negativo generado (ej: -14001)
  compId: number,          // ID real de la competencia
  name: string,
  displayName: string,
  x: number,
  y: number,
  animScale: number,       // Para animación de entrada
  animOpacity: number,
  animDelay: number,
  skills: [{...}],         // Skills de esta competencia
  raw: {...}               // Datos originales
}

// Skill Node (grandChildNodes[])
{
  id: number,              // ID negativo generado
  skillId: number,         // ID real del skill
  name: string,
  displayName: string,
  x: number,
  y: number,
  parentId: number,        // ID del nodo competencia padre
  raw: {...}               // Datos originales del skill
}
```

### Edges (Conexiones)

```typescript
// Edge entre capability y competency (childEdges[])
{
  source: number,          // ID del nodo capability
  target: number,          // ID del nodo competency (negativo)
  path: string,            // SVG path "M... C..." (curva Bézier)
}

// Edge entre competency y skill (grandChildEdges[])
{
  source: number,          // ID del nodo competency
  target: number,          // ID del nodo skill (negativo)
  path: string,            // SVG path con curva
}
```

---

## Configuración de Layout

Archivo: `composables/useScenarioLayout.ts`

```typescript
export const LAYOUT_CONFIG = {
  capability: {
    radial: {
      radius: 220,
      startAngle: Math.PI * 0.2,
      endAngle: Math.PI * 0.8,
    },
    text: { fontSize: 13, fontWeight: 600 },
  },

  competency: {
    defaultLayout: "auto", // 'auto' | 'radial' | 'matrix' | 'sides'
    matrixVariants: [
      { count: 4, rows: 2, cols: 2 },
      { count: 6, rows: 2, cols: 3 },
      // ...
    ],
    text: { fontSize: 11, fontWeight: 500 },
  },

  skill: {
    defaultLayout: "matrix",
    maxDisplay: 20,
    spacing: { h: 95, v: 40 },
    edge: {
      baseDepth: 35,
      curveFactor: 0.5,
    },
    text: { fontSize: 10, fontWeight: 400 },
  },

  animations: {
    competencyStaggerRow: 30,
    competencyStaggerCol: 12,
  },
};
```

---

## Referencias

- **Composable principal:** `src/resources/js/composables/useHierarchicalUpdate.ts`
- **Componente:** `src/resources/js/pages/ScenarioPlanning/Index.vue`
- **Documentación memoria:** `openmemory.md` → sección "useHierarchicalUpdate"
- **Fix reactividad:** `openmemory.md` → "Fix: Reactividad en Estructuras Jerárquicas Vue"

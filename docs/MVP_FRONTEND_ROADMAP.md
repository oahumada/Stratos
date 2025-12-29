# 🚀 MVP Frontend Roadmap - Últimas 2 Semanas

> **Objetivo:** Entregar demo funcional con 5 módulos principales + Dashboard CHRO  
> **Timeline:** 28 Dic - 14 Ene (8-14 días)  
> **Status:** v0.2.0 Backend ✅ | v0.3.0 Frontend 🚀

---

## 📊 Resumen Ejecutivo

```
MÓDULOS A IMPLEMENTAR (Priorizados):
├── 1. Person (CRUD base)           → 2 días
├── 2. Skills (CRUD simple)          → 0.5 días
├── 3. Roles (Read-only)             → 0.5 días
├── 4. Gap Analysis ⭐ (Diferenciador)→ 2-3 días
├── 5. Learning Paths (Read-only)    → 1-1.5 días
├── 6. Dashboard Ejecutivo (CHRO)    → 1-2 días
└── 7. Tests + Polish               → 1-2 días

TOTAL ESTIMADO: 8-10 días ✅
```

---

## 📅 Plan Detallado por Fase

### **FASE 1: MVP Base (Días 1-3) - Person, Skills, Roles**

#### **Día 1-2: Person Module** 🟢 Prioridad MÁXIMA

**Objetivo:** CRUD de empleados funcional + integración API

```
COMPONENTES A CREAR:
├── PersonList.vue
│   ├── Tabla de empleados (17 del seeder)
│   ├── Búsqueda/filtros
│   ├── Acciones: Crear, Editar, Eliminar, Ver detalle
│   └── Integración: GET /Person, DELETE /Person/:id
│
├── PersonForm.vue
│   ├── FormSchema.vue reutilizable
│   ├── Campos: name, email, department, role_id, hired_at
│   ├── Validaciones
│   └── Integración: POST /Person, PUT /Person/:id
│
└── PersonDetail.vue
    ├── Vista completa del empleado
    ├── Skills actuales (tabla)
    ├── Botón "Assign Skills"
    ├── Botón "View Gap Analysis"
    └── Información de rol actual

API ENDPOINTS A USAR:
✅ GET    /Person              (listado completo)
✅ GET    /Person/:id          (detalle)
✅ POST   /Person              (crear)
✅ PUT    /Person/:id          (editar)
✅ DELETE /Person/:id          (eliminar)
✅ GET    /Person/:id/skills   (skills del empleado)
✅ POST   /Person/:id/skills   (asignar skill)
```

**Tareas específicas:**

- [ ] Crear estructura de carpetas `src/resources/views/Person/`
- [ ] Implementar PersonList.vue (tabla completa)
- [ ] Implementar PersonForm.vue (reutilizable)
- [ ] Implementar PersonDetail.vue
- [ ] Setup de Pinia store para Person
- [ ] Tests básicos (tabla renderiza, CRUD funciona)

**Métricas de éxito:**

- ✅ Tabla muestra 17 empleados de seeder
- ✅ Crear nuevo empleado funciona
- ✅ Editar empleado funciona
- ✅ Eliminar empleado funciona
- ✅ Ver detalle muestra skills

---

#### **Día 2.5: Skills Module** 🟢 Prioridad ALTA

**Objetivo:** CRUD simple de skills + asignación a Person

```
COMPONENTES A CREAR:
├── SkillsList.vue
│   ├── Tabla de skills (30 del seeder)
│   ├── Niveles: 1-5 (beginner a expert)
│   ├── Categorías
│   └── Acciones CRUD
│
└── SkillForm.vue
    ├── Nombre, categoría, nivel
    └── Validaciones

API ENDPOINTS A USAR:
✅ GET    /skills              (listado)
✅ GET    /skills/:id
✅ POST   /skills              (crear)
✅ PUT    /skills/:id          (editar)
✅ DELETE /skills/:id
```

**Tareas:**

- [ ] SkillsList.vue (tabla con 30 skills)
- [ ] SkillForm.vue
- [ ] Pinia store para Skills
- [ ] Tests básicos

**Tiempo:** 4-6 horas

---

#### **Día 3: Roles Module** 🟢 Prioridad MEDIA

**Objetivo:** Lectura de roles + ver skills requeridos

```
COMPONENTES A CREAR:
├── RolesList.vue (Read-only en MVP)
│   ├── Tabla de 8 roles
│   ├── Columna de skills requeridos
│   ├── Ver detalle de rol
│   └── Ver vacantes asociadas
│
└── RoleDetail.vue
    ├── Info del rol
    ├── Skills requeridos con niveles
    ├── Vacantes abiertas
    └── Empleados en este rol (opcional)

API ENDPOINTS A USAR:
✅ GET    /roles
✅ GET    /roles/:id
✅ GET    /roles/:id/skills
✅ GET    /vacancies (para ver abiertas)
```

**Tareas:**

- [ ] RolesList.vue
- [ ] RoleDetail.vue
- [ ] Pinia store

**Tiempo:** 2-3 horas

---

### **FASE 2: El Diferenciador (Días 4-5) - Gap Analysis ⭐⭐⭐**

**Objetivo:** Visualizar brechas de skills (core de TalentIA)

#### **Día 4: Gap Analysis - Estructura & Visuals**

```
COMPONENTES A CREAR:
├── GapAnalysisList.vue
│   ├── Listado de brechas por empleado
│   ├── Filtros: role, department
│   ├── Columnas: Employee, Current Role, Target Role, Total Gap
│   └── Click → Ir a GapAnalysisDetail
│
└── GapAnalysisDetail.vue ⭐⭐⭐
    ├── Empleado seleccionado
    ├── Rol target
    ├── Tabla comparativa:
    │   ├── Skill | Actual | Requerido | Brecha | Status
    │   └── Código de colores (verde: ok, rojo: crítico)
    ├── Visualización (Radar chart o Heatmap)
    ├── Recomendaciones
    ├── Botón "Suggest Learning Path"
    └── Timeline estimado de cerrar brecha

API ENDPOINTS A USAR:
✅ GET    /gap-analysis              (listado de brechas)
✅ GET    /gap-analysis/:person_id   (detalle de persona)
✅ GET    /gap-analysis/:person_id/vs/:role_id (vs rol específico)
✅ GET    /recommendations (recomendaciones basadas en brecha)
```

**Tareas:**

- [ ] GapAnalysisList.vue (tabla de brechas)
- [ ] GapAnalysisDetail.vue (tabla comparativa + visual)
- [ ] Implementar radar chart o heatmap (ApexCharts o Chart.js)
- [ ] Componente reutilizable para visualizar skills
- [ ] Pinia store para Gap Analysis
- [ ] Tests visualización

**Tiempo:** 2-3 días (la mayor parte del MVP)

**Visualización recomendada:**

```
┌─────────────────────────────────────────────────┐
│ GAP ANALYSIS: Juan Pérez → Tech Lead            │
├─────────────────────────────────────────────────┤
│                                                  │
│ Skill        │ Actual │ Req │ Brecha │ Timeline│
├──────────────┼────────┼─────┼────────┼─────────┤
│ React        │   4    │  5  │  -1    │ 2 meses │
│ TypeScript   │   3    │  5  │  -2    │ 3 meses │
│ NodeJS       │   2    │  4  │  -2    │ 4 meses │
│ SQL          │   3    │  4  │  -1    │ 1 mes   │
│ Architecture │   2    │  4  │  -2    │ 4 meses │
│ Leadership   │   1    │  3  │  -2    │ 6 meses │
└──────────────┴────────┴─────┴────────┴─────────┘

TOTAL GAP: 12 levels → Estimado: 6 meses
```

---

### **FASE 3: Complementarios (Días 6-7) - Learning Paths**

#### **Día 6-7: Learning Paths** 🟡 Prioridad ALTA

**Objetivo:** Visualizar rutas de aprendizaje sugeridas

```
COMPONENTES A CREAR:
├── LearningPathsList.vue
│   ├── Listado de rutas activas/sugeridas
│   ├── Filtros: person, status
│   ├── Timeline visual
│   └── Click → Ir a detalle
│
└── LearningPathDetail.vue
    ├── Ruta de aprendizaje (generada por gap analysis)
    ├── Timeline visual (Gantt o timeline lineal)
    ├── Fases:
    │   ├── Fase 1: Fundamentals (mes 1-2)
    │   ├── Fase 2: Intermediate (mes 2-3)
    │   └── Fase 3: Advanced (mes 3-6)
    ├── Recursos por fase (cursos, mentoring, proyectos)
    ├── Progress tracker
    ├── Botón "Start Path"
    └── Notas/comentarios

API ENDPOINTS A USAR:
✅ GET    /learning-paths              (listado)
✅ GET    /learning-paths/:id          (detalle)
✅ GET    /learning-paths/:id/progress (seguimiento)
✅ POST   /learning-paths/:id/start    (iniciar)
```

**Tareas:**

- [ ] LearningPathsList.vue
- [ ] LearningPathDetail.vue
- [ ] Timeline visual component (reutilizable)
- [ ] Progress tracker
- [ ] Pinia store

**Tiempo:** 1.5 días

---

### **FASE 4: Dashboard Ejecutivo (Día 8) - CHRO View**

#### **Día 8: Dashboard CHRO** 🟠 Prioridad ALTA

**Objetivo:** Resumen ejecutivo del talento

```
COMPONENTES A CREAR:
├── DashboardCHRO.vue (Main)
│   ├── KPI Cards (Top):
│   │   ├── Total Employees (20)
│   │   ├── Open Positions (5)
│   │   ├── Avg Gap Score (7.2/10)
│   │   └── Learning Paths Active (8)
│   │
│   ├── Gráficos (Centro):
│   │   ├── Distribution by Department (Pie chart)
│   │   ├── Skills Distribution (Bar chart)
│   │   ├── Gap Analysis Heatmap
│   │   └── Top 5 Required Skills (Bar chart)
│   │
│   └── Tables (Abajo):
│       ├── Top 10 Employees with Highest Gap
│       ├── Top 5 Open Positions
│       ├── Learning Paths in Progress
│       └── Recent Activity

API ENDPOINTS A USAR:
✅ GET /dashboard/kpis (resumen ejecutivo)
✅ GET /dashboard/charts (datos para gráficos)
✅ GET /gap-analysis (top gaps)
✅ GET /vacancies (vacantes abiertas)
✅ GET /learning-paths (activas)
```

**Tareas:**

- [ ] DashboardCHRO.vue
- [ ] KPI cards components
- [ ] Chart components (ApexCharts)
- [ ] Heatmap de gaps
- [ ] Tables filtrable

**Tiempo:** 1.5 días

---

### **FASE 5: Testing & Polish (Días 9-10)**

```
TAREAS:
├── [ ] Tests unitarios (componentes principales)
├── [ ] Tests integración (API calls)
├── [ ] Tests E2E (flujos principales)
├── [ ] Mobile responsive design
├── [ ] Validaciones en frontend
├── [ ] Error handling
├── [ ] Loading states
├── [ ] Empty states
├── [ ] Accesibilidad (a11y)
├── [ ] Performance optimization
├── [ ] SEO meta tags (si aplica)
└── [ ] Documentación de componentes

TIEMPO: 1.5-2 días
```

---

## 🎯 Hitos Principales

| Fecha         | Hito                | Status         | Entregable           |
| ------------- | ------------------- | -------------- | -------------------- |
| **28 Dic**    | Kick-off Frontend   | 🟢 Ready       | Roadmap finalizado   |
| **29-30 Dic** | Person + Skills MVP | 🚀 In Progress | CRUD funcional       |
| **31 Dic**    | Roles + polish      | 🚀 In Progress | Módulo read-only     |
| **1-2 Ene**   | Gap Analysis        | 🚀 Priority    | Dashboard de brechas |
| **3-4 Ene**   | Learning Paths      | ⏳ Next        | Rutas visuales       |
| **5 Ene**     | Dashboard CHRO      | ⏳ Next        | KPIs ejecutivos      |
| **6-7 Ene**   | Testing + Polish    | ⏳ Next        | Tests completos      |
| **8 Ene**     | Release v0.3.0-beta | ⏳ Next        | Primera beta         |
| **9-14 Ene**  | Refinement + Demo   | ⏳ Next        | Demo lista           |

---

## 🛠️ Stack Frontend

```
Framework:     Vue 3 + TypeScript ✅
UI Library:    Vuetify 3 ✅
State:         Pinia ✅
Charts:        ApexCharts (para Gap Analysis)
Forms:         FormSchema.vue (genérico reutilizable)
Build:         Vite ✅
Testing:       Vitest + @vue/test-utils
```

---

## 📋 Estructura de Carpetas

```
src/resources/
├── views/
│   ├── layouts/
│   │   └── MainLayout.vue
│   ├── Person/
│   │   ├── PersonList.vue
│   │   ├── PersonForm.vue
│   │   └── PersonDetail.vue
│   ├── Skills/
│   │   ├── SkillsList.vue
│   │   └── SkillForm.vue
│   ├── Roles/
│   │   ├── RolesList.vue
│   │   └── RoleDetail.vue
│   ├── GapAnalysis/
│   │   ├── GapAnalysisList.vue
│   │   └── GapAnalysisDetail.vue
│   ├── LearningPaths/
│   │   ├── LearningPathsList.vue
│   │   └── LearningPathDetail.vue
│   └── Dashboard/
│       └── DashboardCHRO.vue
├── components/
│   ├── FormSchema.vue (reutilizable)
│   ├── SkillBadge.vue
│   ├── GapVisualization.vue (radar/heatmap)
│   ├── TimelineComponent.vue
│   └── KpiCard.vue
├── stores/
│   ├── Person.ts
│   ├── skills.ts
│   ├── roles.ts
│   ├── gapAnalysis.ts
│   └── learningPaths.ts
├── services/
│   └── api.ts (todas las llamadas)
└── types/
    └── index.ts (interfaces TypeScript)
```

---

## 🔗 Rutas Principales

```
/Person
  /Person/:id
  /Person/:id/skills
  /Person/:id/gap-analysis

/skills
  /skills/:id

/roles
  /roles/:id

/gap-analysis
  /gap-analysis/:personId

/learning-paths
  /learning-paths/:id

/dashboard
  /dashboard/chro
```

---

## ✅ Criterios de Aceptación (Por Módulo)

### **Person Module**

- [ ] Tabla renderiza 17 empleados
- [ ] Crear empleado nuevo funciona
- [ ] Editar empleado funciona
- [ ] Eliminar empleado funciona
- [ ] Vista detallada muestra skills
- [ ] Búsqueda/filtros funcionales

### **Skills Module**

- [ ] Tabla renderiza 30 skills
- [ ] CRUD completo funciona
- [ ] Asignación a personas funciona

### **Roles Module**

- [ ] Tabla renderiza 8 roles
- [ ] Ver detalle del rol funciona
- [ ] Skills requeridos visibles
- [ ] Vacantes asociadas visibles

### **Gap Analysis** ⭐

- [ ] Lista de brechas funciona
- [ ] Detalle comparativo funciona
- [ ] Visualización (radar/heatmap) funciona
- [ ] Recomendaciones están presentes
- [ ] Timeline estimado aparece

### **Learning Paths**

- [ ] Lista de rutas funciona
- [ ] Detalle de ruta funciona
- [ ] Timeline visual funciona
- [ ] Progress tracker funciona

### **Dashboard CHRO**

- [ ] KPIs se calculan correctamente
- [ ] Gráficos se renderizan
- [ ] Heatmap funciona
- [ ] Tablas son navegables

### **General**

- [ ] Tests pasen 80%+
- [ ] Responsive (mobile/tablet)
- [ ] Validaciones frontend funcionen
- [ ] Error handling implementado

---

## 🚀 Cómo Iniciar

### **Día 1 - Setup Inicial**

```bash
cd src

# Verificar que la app Vue está lista
npm run dev

# Crear estructura base
mkdir -p resources/views/{Person,Skills,Roles,GapAnalysis,LearningPaths,Dashboard}
mkdir -p resources/components
mkdir -p resources/stores
mkdir -p resources/services
mkdir -p resources/types

# Instalar dependencias (si faltan)
npm install axios pinia

# Crear primer componente Person
# → Continúa en FASE 1
```

### **Commits Semánticos**

```bash
# Cada módulo es un feature
./scripts/commit.sh
# Tipo: feat
# Scope: Person|skills|roles|gap-analysis|learning-paths|dashboard
# Message: "agregar CRUD de Person con integración API"
```

### **Control de Progreso**

Usar esto para trackear:

```
Día 1-2: Person CRUD ✅
Día 2.5: Skills CRUD ⏳
Día 3: Roles Read-only 🟢
Día 4-5: Gap Analysis ⭐
Día 6-7: Learning Paths 🚀
Día 8: Dashboard 📊
Día 9-10: Tests + Polish 🎯
```

---

## 📊 Métricas de Éxito

```
✅ 5 módulos completados
✅ 17 endpoints API integrados
✅ 80%+ tests pasando
✅ 0 critical bugs
✅ Demo lista para cliente
✅ Performance: <3s load time
✅ Mobile-responsive
✅ Accesibilidad WCAG AA
```

---

## 🎬 Siguientes Pasos (Post-MVP)

```
v1.0.0:
├── Roles y permisos reales
├── Autenticación completa
├── Tests al 100%
├── CI/CD pipeline
└── Documentación API completa

Post v1.0:
├── Integraciones (ATS, HRIS, LMS)
├── Mobile app
├── SSI/Blockchain (credenciales verificables)
├── IA avanzada (matching automático)
└── Notificaciones push/email
```

---

**Creado:** 28 de Diciembre, 2025  
**Versión:** 1.0  
**Status:** 🚀 Ready to Start  
**Owner:** Development Team

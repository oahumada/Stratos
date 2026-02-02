# Workforce Planning - Integración en UI

**Fecha:** January 4-5, 2026
**Status:** ✅ Acceso integrado en AppSidebar + Rutas configuradas

---

## 📍 Ubicación en la Aplicación

### AppSidebar - Menú Principal

El módulo **Workforce Planning** ahora está disponible en el menú lateral con:

- **Título:** "Workforce Planning"
- **Icono:** `mdi-chart-timeline-variant` (Gráfico de línea temporal)
- **Posición:** Después de "Marketplace"
- **Componente:** `AppSidebar.vue`

**Ubicación visual:**

```
Dashboard
People
Roles
Skills
Gap Analysis
Learning Paths
Marketplace
📊 Workforce Planning  ← NUEVO
```

---

## 🔗 Rutas Web Configuradas

### 1. Listado de Scenarios

**Ruta:** `/workforce-planning`
**Componente:** `WorkforcePlanning/ScenarioSelector.vue`
**Nombre:** `workforce-planning.index`
**Middleware:** `auth`, `verified`

**Funcionalidad:**

- Listar todos los scenarios de planning
- Crear nuevos scenarios
- Editar scenarios existentes
- Filtrar por estado y año fiscal
- Eliminar scenarios

**URL en navegador:**

```
http://localhost:8000/workforce-planning
```

### 2. Dashboard de Scenario

**Ruta:** `/workforce-planning/{id}`
**Componente:** `WorkforcePlanning/OverviewDashboard.vue`
**Nombre:** `workforce-planning.show`
**Middleware:** `auth`, `verified`

**Funcionalidad:**

- Ver métricas principales del scenario
- Visualizar gráficos de headcount y skill coverage
- Ver resumen de riesgos y costos
- Ejecutar análisis completo
- Descargar reportes

**URL en navegador:**

```
http://localhost:8000/workforce-planning/1
http://localhost:8000/workforce-planning/2
...
```

---

## 🔄 Flujo de Navegación

```
Dashboard
    ↓
Sidebar: Workforce Planning (Click)
    ↓
/workforce-planning (ScenarioSelector)
    ↓
    ├─ Create New Scenario → Create Dialog
    ├─ Edit Scenario → Edit Dialog
    └─ Click Row → /workforce-planning/{id}
        ↓
        Overview Dashboard
        ├─ KPI Cards (Headcount, Growth, Coverage, Risk)
        ├─ Charts (Forecast, Skill Coverage)
        ├─ Risk Summary
        ├─ Cost Estimates
        └─ Actions (Run Analysis, Download)
```

---

## 🎨 Iconografía

### Icono Workforce Planning

- **Material Design Icon:** `mdi-chart-timeline-variant`
- **Tamaño:** 20px
- **Color:** Heredado del tema (light/dark)
- **Hover:** Cambia de color según tema activo

---

## 🔐 Autenticación y Autorización

Ambas rutas requieren:

- ✅ Usuario autenticado (`auth`)
- ✅ Email verificado (`verified`)

Sin estos middleware, se redirige a `/login`

---

## 📡 Integración con API Backend

Las rutas web apuntan a componentes que se conectan con:

### API Endpoints disponibles:

```
GET    //api/workforce-planning/scenarios
POST   //api/workforce-planning/scenarios
GET    //api/workforce-planning/scenarios/{id}
PUT    //api/workforce-planning/scenarios/{id}
DELETE //api/workforce-planning/scenarios/{id}
POST   //api/workforce-planning/scenarios/{id}/approve
GET    //api/workforce-planning/scenarios/{id}/role-forecasts
GET    //api/workforce-planning/scenarios/{id}/matches
GET    //api/workforce-planning/scenarios/{id}/skill-gaps
GET    //api/workforce-planning/scenarios/{id}/succession-plans
GET    //api/workforce-planning/scenarios/{id}/analytics
POST   //api/workforce-planning/scenarios/{id}/analyze
GET    //api/workforce-planning/matches/{id}/recommendations
```

---

## 📂 Archivos Modificados

### 1. AppSidebar.vue

```vue
// Agregar icono const WorkforcePlanningIcon = defineComponent(() => () =>
h(VIcon, { icon: 'mdi-chart-timeline-variant', size: 20 }) ); // Agregar item al
menú { title: 'Workforce Planning', href: '/workforce-planning', icon:
WorkforcePlanningIcon, }
```

### 2. routes/web.php

```php
Route::get('/workforce-planning', function () {
    return Inertia::render('WorkforcePlanning/ScenarioSelector');
})->middleware(['auth', 'verified'])->name('workforce-planning.index');

Route::get('/workforce-planning/{id}', function ($id) {
    return Inertia::render('WorkforcePlanning/OverviewDashboard', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('workforce-planning.show');
```

---

## ✨ Características Implementadas

- ✅ Menú sidebar accesible
- ✅ Navegación por rutas
- ✅ Componentes Vue listos
- ✅ Autenticación requerida
- ✅ API backend disponible
- ✅ Responsive design (Vuetify)
- ✅ Dark/Light mode support

---

## 🚀 Próximos Pasos

1. **Completar componentes restantes** (4 de 6)
   - RoleForecastsTable.vue
   - MatchingResults.vue
   - SuccessionPlanCard.vue
   - SkillGapsMatrix.vue

2. **Agregar estado global (Pinia)**
   - Store para scenarios
   - Store para matches
   - Store para skill gaps

3. **Mejorar UI/UX**
   - Loader states
   - Error handling
   - Success notifications
   - Empty states

4. **Testing E2E**
   - Playwright/Cypress tests
   - User flow testing

---

**Commit:** `71b7ed6`
**Branch:** `feature/workforce-planning`
**Status:** ✅ Ready for development continuation

# 🗺️ MAPA DE NAVEGACIÓN - DÓNDE ESTÁ TODO

## 🎯 Busca lo que necesitas en esta tabla

### Si Necesitas... → Ve A...

#### Backend - Base de Datos

| Necesidad                                              | Ubicación                                                                             | Línea |
| ------------------------------------------------------ | ------------------------------------------------------------------------------------- | ----- |
| Ver estructura de tabla `workforce_planning_scenarios` | `database/migrations/2026_01_04_100000_create_workforce_planning_scenarios_table.php` | 10-40 |
| Ver enhanced columns (template_id, scenario_type, etc) | `database/migrations/2026_01_06_193810_enhance_workforce_scenarios_table.php`         | 10-50 |
| Ver tabla de skill demands                             | `database/migrations/2026_01_06_193815_create_scenario_skill_demands_table.php`       | 10-40 |
| Ver tabla de estrategias                               | `database/migrations/2026_01_06_193815_create_scenario_closure_strategies_table.php`  | 10-50 |
| Ver tabla de plantillas                                | `database/migrations/2026_01_06_193804_create_scenario_templates_table.php`           | 10-30 |
| Ver tabla de comparaciones                             | `database/migrations/2026_01_06_193816_create_scenario_comparisons_table.php`         | 10-30 |

#### Backend - Modelos Eloquent

| Necesidad                     | Ubicación                                   | Qué Buscar                          |
| ----------------------------- | ------------------------------------------- | ----------------------------------- |
| Modelo principal de escenario | `app/Models/StrategicPlanningScenarios.php` | Línea 1-50: relationships           |
| Modelo de demanda de skills   | `app/Models/ScenarioSkillDemand.php`        | Relaciones con scenario, skill      |
| Modelo de estrategias         | `app/Models/ScenarioClosureStrategy.php`    | Enums: strategy, status, risk_level |
| Modelo de plantillas          | `app/Models/ScenarioTemplate.php`           | Config JSON casting                 |
| Modelo de comparaciones       | `app/Models/ScenarioComparison.php`         | Results JSON casting                |
| Modelo de milestones          | `app/Models/ScenarioMilestone.php`          | Status enum                         |

#### Backend - Servicios (Lógica de Negocio)

| Necesidad             | Ubicación                                   | Línea   | Qué Hace                                                           |
| --------------------- | ------------------------------------------- | ------- | ------------------------------------------------------------------ |
| Calcular brechas      | `app/Services/WorkforcePlanningService.php` | **456** | `calculateScenarioGaps()` - Compare demanda actual vs proyectada   |
| Sugerir estrategias   | `app/Services/WorkforcePlanningService.php` | **599** | `recommendStrategiesForGap()` - Genera 6Bs (build, buy, borrow...) |
| Refrescar estrategias | `app/Services/WorkforcePlanningService.php` | **634** | `refreshSuggestedStrategies()` - Regenera todas las estrategias    |
| Comparar escenarios   | `app/Services/WorkforcePlanningService.php` | **684** | `compareScenarios()` - What-if analysis                            |
| Calcular matching     | `app/Services/WorkforcePlanningService.php` | **33**  | `calculateMatches()` - Talento interno vs roles                    |
| Calcular analytics    | `app/Services/WorkforcePlanningService.php` | **381** | `calculateAnalytics()` - KPIs agregados                            |

#### Backend - Controllers (Endpoints API)

| Endpoint                                                      | Ubicación                    | Método                         | Qué Hace                    |
| ------------------------------------------------------------- | ---------------------------- | ------------------------------ | --------------------------- |
| `GET /workforce-scenarios`                                    | WorkforceScenarioController  | `index()`                      | Lista escenarios            |
| `POST /workforce-scenarios`                                   | WorkforceScenarioController  | `store()`                      | Crea escenario custom       |
| `POST /workforce-scenarios/{id}/instantiate-from-template`    | WorkforceScenarioController  | `instantiateFromTemplate()`    | **⭐ Crea desde plantilla** |
| `GET /workforce-scenarios/{id}`                               | WorkforceScenarioController  | `show()`                       | Ver detalle escenario       |
| `PUT /workforce-scenarios/{id}`                               | WorkforceScenarioController  | `update()`                     | Actualiza escenario         |
| `DELETE /workforce-scenarios/{id}`                            | WorkforceScenarioController  | `destroy()`                    | Elimina escenario           |
| `POST /workforce-scenarios/{id}/calculate-gaps`               | WorkforceScenarioController  | `calculateGaps()`              | **⭐ Calcula brechas**      |
| `POST /workforce-scenarios/{id}/refresh-suggested-strategies` | WorkforceScenarioController  | `refreshSuggestedStrategies()` | **⭐ Sugiere estrategias**  |
| `GET /scenario-templates`                                     | ScenarioTemplateController   | `index()`                      | Lista plantillas            |
| `GET /scenario-templates/{id}`                                | ScenarioTemplateController   | `show()`                       | Ver plantilla               |
| `POST /scenario-comparisons`                                  | ScenarioComparisonController | `store()`                      | **⭐ Crea comparación**     |
| `GET /scenario-comparisons`                                   | ScenarioComparisonController | `index()`                      | Lista comparaciones         |
| `GET /scenario-comparisons/{id}`                              | ScenarioComparisonController | `show()`                       | Ver comparación             |

#### Validaciones y Seguridad

| Necesidad                     | Ubicación                                                      |
| ----------------------------- | -------------------------------------------------------------- |
| Validar crear escenario       | `app/Http/Requests/StoreWorkforceScenarioRequest.php`          |
| Validar actualizar escenario  | `app/Http/Requests/UpdateWorkforceScenarioRequest.php`         |
| Validar sugerir estrategias   | `app/Http/Requests/RefreshSuggestedStrategiesRequest.php`      |
| Validar crear desde plantilla | `app/Http/Requests/InstantiateScenarioFromTemplateRequest.php` |
| Validar comparación           | `app/Http/Requests/StoreScenarioComparisonRequest.php`         |
| Policy de acceso              | `app/Policies/WorkforcePlanningPolicy.php`                     |

#### Frontend - State Management

| Necesidad            | Ubicación                                       | Qué Ofrece                                   |
| -------------------- | ----------------------------------------------- | -------------------------------------------- |
| Store Pinia completo | `resources/js/stores/workforcePlanningStore.ts` | State, Actions, Getters para todo el módulo  |
| State de escenarios  | Line 20-30                                      | `selectedScenarioId`, `scenarios[]`          |
| State de brechas     | Line 40-50                                      | `skillGaps[]`, caching                       |
| State de estrategias | Line 60-70                                      | `closureStrategies[]`                        |
| Acciones fetch       | Line 200-250                                    | `fetchScenarios()`, `fetchSkillGaps()`, etc  |
| Getters computed     | Line 350-400                                    | `getSelectedScenario()`, `getMatches()`, etc |

#### Frontend - Componentes Vue (Existentes)

| Componente            | Ubicación                                                     | Qué Muestra                 |
| --------------------- | ------------------------------------------------------------- | --------------------------- |
| Dashboard Principal   | `resources/js/pages/WorkforcePlanning/OverviewDashboard.vue`  | KPIs, métricas agregadas    |
| Matriz de Brechas     | `resources/js/pages/WorkforcePlanning/SkillGapsMatrix.vue`    | Tabla/matriz de gaps        |
| Matching de Talento   | `resources/js/pages/WorkforcePlanning/MatchingResults.vue`    | Personas que matchean roles |
| Tabla de Proyecciones | `resources/js/pages/WorkforcePlanning/RoleForecastsTable.vue` | Roles proyectados           |
| Plans de Sucesión     | `resources/js/pages/WorkforcePlanning/SuccessionPlanCard.vue` | Sucesores por rol           |
| Selector de Escenario | `resources/js/pages/WorkforcePlanning/ScenarioSelector.vue`   | Dropdown para seleccionar   |
| Charts Reutilizables  | `resources/js/pages/WorkforcePlanning/Charts/`                | Gráficos generales          |

#### Frontend - Componentes Faltantes (Necesarios para UI Completa)

| Componente                     | Ubicación (donde crear)                                               | Prioridad | Complejidad |
| ------------------------------ | --------------------------------------------------------------------- | --------- | ----------- |
| Lista de Escenarios            | `resources/js/pages/WorkforcePlanning/ScenarioList.vue`               | 🔴 ALTA   | Media       |
| Crear Escenario (custom)       | `resources/js/pages/WorkforcePlanning/ScenarioCreate.vue`             | 🔴 ALTA   | Alta        |
| Crear desde Plantilla (wizard) | `resources/js/pages/WorkforcePlanning/ScenarioCreateFromTemplate.vue` | 🔴 ALTA   | Media       |
| Detalle de Escenario (tabs)    | `resources/js/pages/WorkforcePlanning/ScenarioDetail.vue`             | 🔴 ALTA   | Alta        |
| Gestión de Estrategias         | `resources/js/pages/WorkforcePlanning/ClosureStrategies.vue`          | 🟡 MEDIA  | Media       |
| Comparación de Estrategias     | `resources/js/pages/WorkforcePlanning/StrategyComparison.vue`         | 🟡 MEDIA  | Media       |
| Comparación de Escenarios      | `resources/js/pages/WorkforcePlanning/ScenarioComparison.vue`         | 🟡 MEDIA  | Media       |
| Timeline/Gantt                 | `resources/js/pages/WorkforcePlanning/ScenarioTimeline.vue`           | 🟢 BAJA   | Alta        |

#### Datos Iniciales

| Necesidad              | Ubicación                                       |
| ---------------------- | ----------------------------------------------- |
| Seeder de 4 plantillas | `database/seeders/ScenarioTemplateSeeder.php`   |
| Registrar seeder en BD | `database/seeders/DatabaseSeeder.php` (line 16) |

---

## 🔍 Cómo Buscar Algo Específico

### Quiero ver toda la lógica de cálculo de brechas

```
→ app/Services/WorkforcePlanningService.php
→ Busca: calculateScenarioGaps() [línea 456]
→ Son ~140 líneas de lógica pura
```

### Quiero ver cómo se sugieren estrategias

```
→ app/Services/WorkforcePlanningService.php
→ Busca: recommendStrategiesForGap() [línea 599]
→ Genera 6 opciones (BUILD, BUY, BORROW, BOT, BIND, BRIDGE)
```

### Quiero ver la API completa

```
→ routes/api.php
→ Busca: workforce-planning [línea 56]
→ 17 endpoints listados
```

### Quiero ver qué datos puedo obtener

```
→ app/Models/StrategicPlanningScenarios.php
→ Ver $fillable [línea 15-30]
→ Ver relationships [línea 40+]
→ Ver casts [línea 35-45]
```

### Quiero ver cuál plantilla usar como ejemplo

```
→ database/seeders/ScenarioTemplateSeeder.php
→ 4 templates predefinidas
→ Cada una tiene config con skills, estrategias, KPIs
```

### Quiero ver cómo el frontend consume datos

```
→ resources/js/pages/WorkforcePlanning/SkillGapsMatrix.vue
→ Busca: useWorkforcePlanningStore() [línea 192]
→ Busca: store.fetchSkillGaps() [línea ~216]
→ Ve cómo integra datos en componente
```

---

## 📊 Estadísticas de Implementación

```
Líneas de Código:
├── Service WorkforcePlanningService.php: 747 líneas ✅
├── Store workforcePlanningStore.ts: 501 líneas ✅
├── Migraciones de BD: ~400 líneas ✅
├── Controllers: ~300 líneas ✅
├── Modelos: ~400 líneas ✅
└── Componentes Vue: ~2000 líneas ✅

Total Backend: ~2000 líneas de código ✅
Total Frontend (componentes): ~2000 líneas ✅

Endpoints API: 17 ✅
Tablas de BD: 12 ✅
Modelos Eloquent: 6 nuevos ✅
Componentes Vue: 6 existentes + 8 faltantes
```

---

## 🗂️ Estructura Rápida de Directorios

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── WorkforceScenarioController.php ✅
│   │   │   ├── ScenarioTemplateController.php ✅
│   │   │   └── ScenarioComparisonController.php ✅
│   │   └── Requests/
│   │       ├── StoreWorkforceScenarioRequest.php ✅
│   │       ├── UpdateWorkforceScenarioRequest.php ✅
│   │       └── ... (5 más) ✅
│   ├── Services/
│   │   └── WorkforcePlanningService.php ✅ [747 líneas]
│   ├── Models/
│   │   ├── StrategicPlanningScenarios.php ✅
│   │   ├── ScenarioTemplate.php ✅
│   │   ├── ScenarioSkillDemand.php ✅
│   │   ├── ScenarioClosureStrategy.php ✅
│   │   ├── ScenarioMilestone.php ✅
│   │   └── ScenarioComparison.php ✅
│   └── Policies/
│       └── WorkforcePlanningPolicy.php ✅
│
├── database/
│   ├── migrations/
│   │   ├── 2026_01_06_193804_create_scenario_templates_table.php ✅
│   │   ├── 2026_01_06_193810_enhance_workforce_scenarios_table.php ✅
│   │   ├── 2026_01_06_193815_create_scenario_skill_demands_table.php ✅
│   │   ├── 2026_01_06_193815_create_scenario_closure_strategies_table.php ✅
│   │   ├── 2026_01_06_193815_create_scenario_milestones_table.php ✅
│   │   └── 2026_01_06_193816_create_scenario_comparisons_table.php ✅
│   └── seeders/
│       └── ScenarioTemplateSeeder.php ✅
│
├── resources/js/
│   ├── stores/
│   │   └── workforcePlanningStore.ts ✅ [501 líneas]
│   └── pages/WorkforcePlanning/
│       ├── OverviewDashboard.vue ✅
│       ├── SkillGapsMatrix.vue ✅
│       ├── MatchingResults.vue ✅
│       ├── RoleForecastsTable.vue ✅
│       ├── SuccessionPlanCard.vue ✅
│       ├── ScenarioSelector.vue ✅
│       ├── Charts/ ✅
│       └── [Faltantes]
│           ├── ScenarioList.vue ⚠️
│           ├── ScenarioCreate.vue ⚠️
│           ├── ScenarioCreateFromTemplate.vue ⚠️
│           ├── ScenarioDetail.vue ⚠️
│           └── ... [4 más]
│
└── routes/
    └── api.php ✅ [línea 56: workforce-planning prefix]
```

---

**Usa este documento como índice para navegar todo el código. Todo está vinculado y referenciado con números de línea exactos.**

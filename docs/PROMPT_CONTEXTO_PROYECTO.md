# 🤖 PROMPT DE CONTEXTO - Strato Project

**Última actualización:** 5 Enero 2026  
**Versión:** 2.0  
**Propósito:** Contexto completo para sesiones de IA/Copilot

---

## 📋 RESUMEN EJECUTIVO

**Strato** es una plataforma SaaS de Talent Management enfocada en **Skills-Based Talent Management** que ayuda a organizaciones a:
- Identificar brechas de competencias (Gap Analysis)
- Generar rutas de desarrollo personalizadas
- Realizar matching inteligente para vacantes internas
- Planificar workforce estratégicamente (Workforce Planning)

**Status Actual:** MVP Backend ✅ COMPLETADO | Frontend ✅ COMPLETADO | Workforce Planning Phase 2 🚀 EN DESARROLLO

---

## 🏗️ ARQUITECTURA TÉCNICA

### Stack Tecnológico

**Backend:**
- Laravel 11 (PHP 8.2+)
- SQLite (desarrollo) / PostgreSQL (producción)
- Repository Pattern para persistencia
- API RESTful con versionado (/api/v1)

**Frontend:**
- Vue 3 + TypeScript
- Inertia.js (SSR híbrido)
- Tailwind CSS 4 / Vuetify
- Shadcn/ui components
- Vite 7
- ApexCharts + Chart.js para visualizaciones

**Herramientas:**
- Prettier + ESLint
- Commitlint (conventional commits)
- Husky (git hooks)
- PHPUnit (testing backend)

### Patrón Arquitectónico Principal: JSON-Driven CRUD

**Flujo de Arquitectura:**
```
HTTP Request
    ↓
form-schema-complete.php (registro dinámico de rutas)
    ↓
FormSchemaController (controlador genérico)
    ↓
{Model}Repository (lógica de persistencia específica)
    ↓
{Model} Eloquent (ORM)
    ↓
Database
```

**Responsabilidades por Capa:**

| Componente | Responsabilidad | Ejemplo |
|------------|-----------------|---------|
| **form-schema-complete.php** | Registrar rutas dinámicamente | `Route::get('/people', [FormSchemaController...])` |
| **FormSchemaController** | Orquestar HTTP, inicializar modelo/repo | `initializeForModel()`, retornar respuesta |
| **{Model}Repository** | Ejecutar queries, aplicar filtros | `PeopleRepository::search()` con eager loading |
| **{Model} Eloquent** | Mapear tabla a clase, relaciones | `People::with('skills')->get()` |
| **Database** | Persistir datos | `SELECT * FROM people` |

**Ventajas del Patrón:**
- ✅ Crear nuevo CRUD en 10-15 minutos
- ✅ Sin duplicación de código
- ✅ Configuración JSON-driven (4 archivos JSON por módulo)
- ✅ Fácil de testear y mantener

---

## 📁 ESTRUCTURA DE DIRECTORIOS CLAVE

```
/src
├── app/
│   ├── Http/Controllers/
│   │   ├── FormSchemaController.php (CRUD genérico)
│   │   └── Api/V1/ (controladores específicos)
│   ├── Models/ (Eloquent models)
│   ├── Repository/ (capa de persistencia)
│   └── Services/ (lógica de negocio)
├── routes/
│   ├── api.php (rutas API específicas)
│   ├── web.php (rutas web Inertia)
│   └── form-schema-complete.php (rutas CRUD genéricas)
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Pages/ (componentes Vue por módulo)
│   │   ├── Components/ (componentes reutilizables)
│   │   └── Layouts/ (layouts principales)
│   └── views/ (blade templates)
└── tests/
    └── Feature/ (tests de integración)

/docs
├── memories.md (memoria principal del proyecto)
├── GUIA_CREAR_NUEVO_CRUD_GENERICO.md
├── PATRON_JSON_DRIVEN_CRUD.md
├── LIBRO_FORMSCHEMA/ (documentación técnica completa)
└── WorkforcePlanning/ (docs de WFP Phase 2)
```

---

## 🎯 MÓDULOS PRINCIPALES IMPLEMENTADOS

### 1. Dashboard Analytics ✅
- Métricas clave: coverage, roles at risk, gaps, promotions
- Top skills gaps
- Roles críticos

### 2. People Management ✅
- CRUD completo de empleados
- Gestión de skills por persona
- Niveles de competencia (1-5)
- Búsqueda y filtros avanzados

### 3. Skills Catalog ✅
- Catálogo de competencias
- Categorías: technical, soft, leadership, domain
- Relación con roles y personas

### 4. Roles ✅
- Definición de roles con skills requeridas
- Niveles mínimos por skill
- Matching de personas a roles

### 5. Gap Analysis ✅
- Análisis de brechas persona vs rol
- Cálculo de match percentage
- Identificación de gaps críticos
- Status: ready, close, needs_development

### 6. Development Paths ✅
- Generación de rutas de desarrollo
- Pasos secuenciales con duración
- Tracking de progreso
- Estados: draft, active, completed, cancelled

### 7. Job Openings & Matching ✅
- Vacantes internas
- Matching automático de candidatos
- Ranking por porcentaje de match
- Comparación interno vs externo

### 8. Applications ✅
- Postulaciones a vacantes
- Estados: pending, accepted, rejected
- Marketplace de oportunidades

### 9. Workforce Planning 🚀 (Phase 2 - EN DESARROLLO)
- **Componente 1:** Simulador de Crecimiento (CEO) - 🚀 INICIADO
- **Componente 2:** Calculadora ROI (CFO) - ⏳ TODO
- **Componente 3:** Asignador de Estrategias (CHRO) - ⏳ TODO

---

## 🔑 REGLAS DE NEGOCIO CRÍTICAS

### Skill Levels
- **Escala:** 1-5 (Básico → Experto)
- **Niveles:**
  - 1: Básico (conocimiento teórico)
  - 2: Intermedio (aplicación con supervisión)
  - 3: Competente (trabajo autónomo)
  - 4: Avanzado (puede enseñar)
  - 5: Experto (referente organizacional)

### Gap Analysis
- **Match Percentage:** `(skills_cumplidas / skills_requeridas) * 100`
- **Gap Status:**
  - `ready`: nivel actual ≥ requerido
  - `close`: gap de 1 nivel
  - `needs_development`: gap ≥ 2 niveles

### Matching de Vacantes
- **Threshold mínimo:** 60% match
- **Ranking:** Por match_percentage descendente
- **Consideraciones:** Skills críticas tienen mayor peso

### Development Paths
- **Duración estimada:** Basada en gap_size y complejidad
- **Pasos:** Secuenciales con dependencias
- **Tipos de acción:** training, mentoring, project, certification

---

## 📊 ENDPOINTS API PRINCIPALES

**Base URL:** `/api/v1`

### Dashboard
```
GET /dashboard/metrics
GET /dashboard/skills-gaps
GET /dashboard/roles-at-risk
```

### People
```
GET    /people
GET    /people/{id}
POST   /people
PATCH  /people/{id}
DELETE /people/{id}
GET    /people/{id}/skills
POST   /people/{id}/skills
```

### Gap Analysis
```
POST /gap-analysis
GET  /gap-analysis/people/{people_id}
```

### Development Paths
```
POST  /development-paths/generate
GET   /development-paths
GET   /development-paths/{id}
PATCH /development-paths/{id}
```

### Job Openings
```
GET  /job-openings
GET  /job-openings/{id}
GET  /job-openings/{id}/candidates
POST /job-openings/{id}/compare
```

### Workforce Planning (Phase 2)
```
POST /workforce-planning/scenarios/{id}/simulate-growth
GET  /workforce-planning/critical-positions
POST /workforce-planning/roi-calculator/calculate
GET  /workforce-planning/scenarios/{id}/gaps-for-assignment
POST /workforce-planning/strategies/assign
```

---

## 🚀 CÓMO CREAR UN NUEVO MÓDULO CRUD

### Flujo Rápido (10-15 minutos)

1. **Registrar modelo** en `form-schema-complete.php`:
   ```php
   'YourModel' => 'route-name'
   ```

2. **Crear carpeta** `/resources/js/Pages/YourModel/your-model-form/` con 4 JSONs:
   - `form-config.json` (configuración general)
   - `form-fields.json` (campos del formulario)
   - `table-columns.json` (columnas de la tabla)
   - `validation-rules.json` (reglas de validación)

3. **Copiar Index.vue** de People y cambiar imports JSON

4. **Agregar ruta web** en `/routes/web.php`:
   ```php
   Route::get('/your-model', fn() => Inertia::render('YourModel/Index'))->name('your-model.index');
   ```

5. **Agregar navlink** en `AppSidebar.vue`

6. **Limpiar caché:**
   ```bash
   php artisan route:clear && php artisan route:cache
   ```

**Documentación detallada:** `/docs/GUIA_CREAR_NUEVO_CRUD_GENERICO.md`

---

## 🎯 WORKFORCE PLANNING PHASE 2 - PRIORIDAD ACTUAL

### Objetivo
Implementar 3 componentes para 3 actores clave:

### Componente 1: Simulador de Crecimiento (CEO) 🚀 INICIADO
**Endpoints:**
- `POST /scenarios/{id}/simulate-growth`
- `GET /critical-positions`

**Funcionalidad:**
- Proyección de headcount por crecimiento %
- Identificación de skills necesarias
- Análisis de posiciones críticas
- Riesgos de sucesión

**Status:** Backend iniciado, Frontend pendiente

### Componente 2: Calculadora ROI (CFO) ⏳ TODO
**Endpoints:**
- `POST /roi-calculator/calculate`
- `GET /roi-calculator/scenarios`

**Funcionalidad:**
- Comparación Build vs Buy vs Borrow
- Cálculo de costos por estrategia
- Recomendación basada en ROI
- Time-to-productivity analysis

**Tiempo estimado:** 4-5 horas

### Componente 3: Asignador de Estrategias (CHRO) ⏳ TODO
**Endpoints:**
- `GET /scenarios/{id}/gaps-for-assignment`
- `POST /strategies/assign`
- `GET /strategies/portfolio/{scenario_id}`

**Funcionalidad:**
- Asignación de estrategias a gaps
- Portfolio de estrategias por escenario
- Tracking de implementación
- Métricas de efectividad

**Tiempo estimado:** 6-8 horas

**Documentación:** `/docs/GUIA_RAPIDA_IMPLEMENTACION_2026_01_05.md`

---

## 📚 DOCUMENTACIÓN CLAVE

### Lectura Obligatoria
1. **memories.md** - Memoria principal del proyecto (3396 líneas)
2. **GUIA_CREAR_NUEVO_CRUD_GENERICO.md** - Paso a paso para nuevos módulos
3. **PATRON_JSON_DRIVEN_CRUD.md** - Arquitectura técnica completa
4. **GUIA_RAPIDA_IMPLEMENTACION_2026_01_05.md** - Plan WFP Phase 2

### Libro FormSchema (11 capítulos)
- `/docs/LIBRO_FORMSCHEMA/00_INDICE.md`
- Lectura completa: ~3h 50min
- Cubre arquitectura, patrones, anti-patrones, escalabilidad

### Casos de Uso
- `/docs/WorkforcePlanning/CasosDeUso.md` - 11 casos de uso por actor

---

## 🔧 COMANDOS ÚTILES

### Backend
```bash
# Migraciones
php artisan migrate:fresh --seed

# Tests
php artisan test

# Limpiar caché
php artisan route:clear
php artisan route:cache
php artisan config:clear

# Servidor
php artisan serve
```

### Frontend
```bash
# Desarrollo
npm run dev

# Build
npm run build

# Linting
npm run lint

# Formateo
npm run format
```

### Base de Datos
```bash
# Ver diagrama
./VIEW_DATABASE_DIAGRAM.sh

# Verificar datos
./verify-people-role-skills.sh
```

---

## ⚠️ CONVENCIONES Y MEJORES PRÁCTICAS

### Código
- **Commits:** Conventional Commits (feat:, fix:, docs:, refactor:)
- **Naming:** camelCase (JS/TS), snake_case (PHP/DB)
- **Imports:** Organizados automáticamente con Prettier
- **Types:** TypeScript estricto en frontend

### Arquitectura
- **NO duplicar controladores** - Usar FormSchemaController
- **Repository Pattern** para lógica de BD
- **Services** para lógica de negocio compleja
- **Validación** en Request classes o inline en controllers

### Testing
- **Feature tests** para endpoints API
- **Unit tests** para Services y Repositories
- **Coverage mínimo:** 70%

### Git
- **Branches:** feature/*, bugfix/*, hotfix/*
- **PRs:** Requieren review
- **CI/CD:** Tests automáticos en push

---

## 🎯 PRÓXIMOS PASOS RECOMENDADOS

### Corto Plazo (Esta Semana)
1. ✅ Completar Componente 1: Simulador de Crecimiento
2. ⏳ Implementar Componente 2: Calculadora ROI
3. ⏳ Implementar Componente 3: Asignador de Estrategias

### Mediano Plazo (Próximas 2 Semanas)
- Tests de integración para WFP Phase 2
- Documentación de usuario final
- Optimización de queries (N+1 problems)
- Implementar caché para dashboard

### Largo Plazo (Roadmap)
- Autenticación multi-tenant
- Notificaciones en tiempo real
- Integración con HRIS externos
- Mobile app (React Native)
- AI-powered recommendations

---

## 💡 TIPS PARA NUEVOS DESARROLLADORES

1. **Empieza por aquí:**
   - Lee `QUICK_START.md`
   - Revisa `memories.md` (secciones 1-3)
   - Explora un módulo existente (People es el más completo)

2. **Para agregar features:**
   - Identifica si es CRUD genérico o lógica específica
   - Si es CRUD: usa FormSchemaController
   - Si es lógica: crea Service + Repository

3. **Para debugging:**
   - Revisa logs en `storage/logs/laravel.log`
   - Usa Vue DevTools para frontend
   - PHPStorm debugger para backend

4. **Para entender el flujo:**
   - Sigue un request desde ruta → controller → repository → model
   - Revisa los tests para ver ejemplos de uso

---

## 📞 RECURSOS ADICIONALES

### Documentación Externa
- [Laravel 11 Docs](https://laravel.com/docs/11.x)
- [Vue 3 Docs](https://vuejs.org/)
- [Inertia.js Docs](https://inertiajs.com/)
- [Tailwind CSS](https://tailwindcss.com/)

### Herramientas
- **Postman Collection:** `/docs/Strato_API_Postman.json`
- **Database Diagram:** Ejecutar `./VIEW_DATABASE_DIAGRAM.sh`
- **DBeaver Setup:** `./dbeaver-setup.sh`

---

## 🏁 CHECKLIST DE INICIO RÁPIDO

Para comenzar a trabajar en el proyecto:

- [ ] Clonar repositorio
- [ ] Instalar dependencias: `composer install && npm install`
- [ ] Configurar `.env` (copiar de `.env.example`)
- [ ] Ejecutar migraciones: `php artisan migrate:fresh --seed`
- [ ] Iniciar backend: `php artisan serve`
- [ ] Iniciar frontend: `npm run dev`
- [ ] Verificar en navegador: `http://localhost:8000`
- [ ] Leer `memories.md` (al menos primeras 500 líneas)
- [ ] Revisar estructura de People module como referencia
- [ ] Ejecutar tests: `php artisan test`

---

**¡Listo para desarrollar! 🚀**

Para cualquier duda, consulta `/docs/memories.md` o los documentos específicos en `/docs/`.

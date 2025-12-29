# TalentIA – Memoria de Contexto para GitHub Copilot

**Última actualización:** 27 Diciembre 2025  
**Status:** MVP Backend ✅ COMPLETADO (Días 1-5), Frontend en Progreso (Día 6+)  
**Documentación Operativa:** ✅ ECHADA_DE_ANDAR, VALIDACION_ESTADO, TEMPLATE_DIA_N, QUICK_START

---

## STATUS ACTUAL (Día 6)

### ✅ Completado (Días 1-5)

- **Backend 100% funcional:** 17 endpoints API (personas, skills, roles, brechas, rutas, vacantes, postulaciones, matching)
- **BD migraciones + seeders:** 15+ tablas, datos de demo TechCorp (20 empleados, 8 roles, 30 skills)
- **Tests:** 5/5 PASS
- **Documentación:** API endpoints, lecciones aprendidas, guía de desarrollo

### ⏳ En Progreso (Día 6)

- **Frontend CRUD Base:** apiHelper.ts (CRUD centralizado), FormSchema.vue (lógica), FormData.vue (form), patrón config-driven
- **Arquitectura:** Extensible para multiplicar módulos sin duplicar código
- **Frontend Pages (Prioridad 1-2):** 10 páginas Vue para consumir endpoints
- **Workforce Planning (Prioridad 3):** Nuevo módulo para planificación dotacional (si tiempo permite)

### 🔄 Próximo (Día 7)

- Componentes especializados
- Tests
- Pulido y documentación final

---

## Índice

1. [Contexto del Producto](#1-contexto-del-producto)
   - [Objetivo](#11-objetivo)
   - [Público Objetivo](#12-público-objetivo)
   - [Propuesta de Valor](#13-propuesta-de-valor)
2. [Alcance y Prioridades](#2-alcance-y-prioridades)
   - [MVP (2 semanas)](#21-mvp-2-semanas--14-días-intensivos)
   - [Fuera del MVP](#22-fuera-del-mvp-inicial)
   - [Prioridades de Desarrollo](#23-prioridades-de-desarrollo)
3. [Arquitectura y Stack Técnico](#3-arquitectura-y-stack-técnico)
4. [Flujos Principales](#4-flujos-principales)
5. [Reglas de Negocio](#5-reglas-de-negocio)
6. [APIs y Endpoints](#6-apis-y-endpoints)
7. [Modelo de Datos](#7-modelo-de-datos)
8. [UI/UX](#8-uiux)
9. [Operación](#9-operación)
10. [Pendientes y Riesgos](#10-pendientes-y-riesgos)
11. [Datos de Demo: Historia de TechCorp](#11-datos-de-demo-historia-de-techcorp)
12. [Guion de Demo](#12-guion-de-demo-storytelling)
13. [Estructura de Carpetas del Proyecto](#13-estructura-de-carpetas-del-proyecto)
14. [Comandos Útiles](#14-comandos-útiles)
15. [Glosario de Términos](#15-glosario-de-términos)
16. [Algoritmos Clave](#16-algoritmos-clave)

---

## 1. Contexto del Producto

### 1.1 Objetivo

**TalentIA** es una plataforma SaaS + consultoría para gestión estratégica de talento basada en skills, IA y credenciales verificables (SSI). Permite a las organizaciones:

- Mapear el talento interno por competencias (skills) y niveles de dominio
- Identificar brechas entre perfiles actuales y roles objetivo
- Diseñar rutas de desarrollo personalizadas con e-learning integrado
- Tomar decisiones de selección basadas en datos (candidatos internos vs externos)
- Facilitar movilidad interna mediante marketplace de oportunidades
- Visualizar métricas estratégicas de talento en dashboards ejecutivos

### 1.2 Público Objetivo

- **Segmentos principales:** Empresas medianas y grandes (100-5000+ empleados) en sectores tech, banca, retail, salud
- **Usuarios finales:**
  - **CHRO/Directores de RRHH:** Dashboards estratégicos, decisiones de inversión en talento
  - **Gerentes de Talento/Desarrollo:** Gestión de rutas de desarrollo, análisis de brechas
  - **Reclutadores internos:** Selección por skills, comparación interno vs externo
  - **Empleados:** Consulta de perfil de skills, oportunidades internas, rutas de carrera
  - **Consultores TalentIA:** Configuración de modelos de talento, acompañamiento estratégico

### 1.3 Propuesta de Valor

- **Integración software + consultoría:** No solo herramienta, sino acompañamiento en diseño de modelo de skills y estrategia de talento
- **Decisiones basadas en datos:** Visibilidad real de capacidades internas, ROI de formación, predicción de brechas
- **Skills como lenguaje común:** Unifica selección, desarrollo, desempeño y movilidad interna
- **IA para recomendaciones:** Inferencia de skills desde CVs/perfiles, sugerencias de rutas de desarrollo, matching automático
- **Futuro verificable:** Roadmap hacia credenciales digitales verificables (SSI) para portabilidad de skills

---

## 2. Alcance y Prioridades

### 2.1 MVP (2 semanas – 14 días intensivos)

#### Semana 1 (Días 1-7)

- **Setup inicial:** Laravel + PostgreSQL + Vue 3 + TypeScript + Vuetify
- **Modelo de datos multi-tenant:** Tablas core con `organization_id`
- **Perfiles de empleados:** CRUD básico de personas con skills y niveles
- **Cálculo de brechas:** Algoritmo persona ↔ rol (gap analysis)
- **Rutas de desarrollo:** Recomendaciones de cursos/acciones para cerrar brechas

#### Semana 2 (Días 8-14)

- **Selección por skills:** Comparación candidatos internos vs externos para vacantes
- **Marketplace interno:** Matching de personas a oportunidades abiertas
- **Dashboard estratégico:** KPIs clave (cobertura de skills, roles en riesgo, brechas críticas)
- **Datos de demo:** Seed con empresa ficticia "TechCorp" (20 empleados, 8 roles, 30 skills)
- **Pulido y guion de demo:** Flujo completo para presentación comercial

#### Funcionalidades MVP

1. **Perfiles de talento** con skills y niveles de dominio (1-5)
2. **Catálogo de skills** por organización (taxonomía personalizable)
3. **Roles y perfiles de cargo** con skills requeridas
4. **Cálculo de brechas** persona ↔ rol (% match, skills faltantes)
5. **Rutas de desarrollo** sugeridas (cursos, mentorías, proyectos)
6. **Vacantes internas** con matching automático de candidatos
7. **Comparación interno vs externo** para decisiones de selección
8. **Marketplace interno** básico (oportunidades + postulaciones)
9. **Dashboard ejecutivo** con métricas de talento
10. **Datos de demo realistas** para storytelling comercial

### 2.2 Fuera del MVP Inicial

- **Autenticación compleja:** Login simple o sin login para demo (hardcoded user)
- **CRUD completo de todo:** Solo lectura/visualización, datos desde seed
- **IA real:** Simulada con lógica de reglas (no OpenAI en MVP)
- **Integraciones externas:** ATS, HRIS, LMS (roadmap post-MVP)
- **SSI/Blockchain:** Credenciales verificables (roadmap largo plazo)
- **Módulo de desempeño completo:** Solo versión ligera integrada (evaluaciones básicas)
- **E-learning nativo:** Catálogo de cursos externos (links), no LMS propio
- **Notificaciones push/email:** Roadmap post-MVP
- **Mobile app:** Solo responsive web

### 2.3 Prioridades de Desarrollo

1. **Crítico (Semana 1):** Modelo de datos, perfiles, brechas, rutas
2. **Alto (Semana 2):** Selección, marketplace, dashboard
3. **Medio (Post-MVP):** Auth real, CRUD completo, IA real
4. **Bajo (Roadmap):** Integraciones, SSI, mobile

### 2.4 Datos de Demo (Seed) - Resumen Ejecutivo

Para el MVP, se creará una empresa ficticia **"TechCorp"** con los siguientes datos:

#### Entidades Principales

- **1 Organización:** TechCorp (startup tech, 20 empleados)
- **20 Personas:** Distribuidas en Engineering (12), Product (3), Operations (5)
- **8 Roles:** Junior/Mid/Senior Frontend, Backend, Full-Stack, Product Manager, DevOps, QA
- **30 Skills:** 15 técnicas (React, Node.js, Python, etc.), 10 soft skills (Leadership, Communication, etc.), 5 business/otras
- **5 Vacantes Internas:** Senior Frontend, Tech Lead, Product Manager, DevOps Engineer, QA Lead
- **10 Postulaciones:** Empleados aplicando a vacantes internas
- **3 Rutas de Desarrollo:** Casos ejemplo (Junior → Mid, Mid → Senior, Senior → Lead)

#### Casos de Uso Pre-configurados

1. **Ana García:** Software Engineer con 88.5% match a Senior Frontend (gap en Kubernetes, GraphQL)
2. **Carlos López:** Frontend Developer con 75% match a Senior Frontend (gap mayor: System Design, Microservices)
3. **María Rodríguez:** Backend Developer lista para promoción a Tech Lead (95% match)

**Detalle completo:** Ver sección [11. Datos de Demo: Historia de TechCorp](#11-datos-de-demo-historia-de-techcorp)

---

## 3. Arquitectura y Stack Técnico

### 3.1 Stack Tecnológico

#### Backend

- **Framework:** Laravel 10+ (PHP 8.2+)
- **Base de datos:** PostgreSQL 15+
- **ORM:** Eloquent
- **API:** RESTful JSON (Laravel API Resources)
- **Autenticación:** Laravel Sanctum (tokens SPA)
- **Validación:** Form Requests
- **Testing:** PHPUnit + Pest

#### Frontend

- **Framework:** Vue 3 (Composition API)
- **Lenguaje:** TypeScript
- **UI Library:** Vuetify 3
- **State Management:** Pinia
- **Routing:** Vue Router 4
- **HTTP Client:** Axios
- **Build:** Vite
- **Testing:** Vitest + Vue Test Utils

#### Infraestructura

- **Hosting:** Digital Ocean Droplet (Ubuntu 22.04)
- **Containerización:** Docker + Docker Compose
- **Web Server:** Nginx
- **CI/CD:** GitHub Actions (roadmap)
- **Monitoreo:** Laravel Telescope (dev), Sentry (prod - roadmap)

#### IA y ML (Roadmap)

- **Inferencia de skills:** OpenAI API (GPT-4) o sentence-transformers local
- **Matching:** Algoritmos de similitud (cosine similarity, embeddings)
- **Recomendaciones:** Collaborative filtering + content-based

#### SSI (Roadmap Largo Plazo)

- **Framework:** Hyperledger Aries
- **Estándar:** W3C Verifiable Credentials
- **Wallet:** Mobile wallet para empleados

### 3.2 Arquitectura Multi-Tenant

#### Estrategia de Aislamiento

- **Modelo:** Single Database, Shared Schema con `organization_id`
- **Identificación de tenant:**
  - **Opción 1 (MVP):** Subdomain (`techcorp.talentia.app`)
  - **Opción 2 (Alternativa):** JWT con claim `organization_id`
- **Middleware:** `EnsureTenantContext` en todas las rutas protegidas
- **Scopes globales:** Eloquent Global Scope en todos los modelos multi-tenant

#### Estructura de Datos

```
organizations (tabla maestra)
├── users (usuarios por org)
├── skills (catálogo por org)
├── roles (perfiles de cargo por org)
├── Person (empleados por org)
├── person_skills (skills de cada persona)
├── role_skills (skills requeridas por rol)
├── development_paths (rutas de desarrollo)
├── job_openings (vacantes internas)
├── applications (postulaciones)
└── analytics_snapshots (métricas históricas)
```

### 3.3 Decisiones Arquitectónicas Clave

#### Backend

1. **API-First:** Frontend consume 100% API REST, sin Blade views
2. **Repository Pattern:** Opcional (Eloquent suficiente para MVP)
3. **Service Layer:** Lógica de negocio en Services (ej: `GapAnalysisService`)
4. **Jobs/Queues:** Para cálculos pesados (post-MVP)
5. **Soft Deletes:** En todas las tablas críticas
6. **Auditoría:** Timestamps (`created_at`, `updated_at`) + `created_by`, `updated_by` (roadmap)

#### Frontend

1. **Composables:** Lógica reutilizable en `composables/` (ej: `useGapAnalysis`)
2. **Atomic Design:** Componentes en `atoms/`, `molecules/`, `organisms/`
3. **Layouts:** `DefaultLayout`, `DashboardLayout`, `AuthLayout`
4. **Lazy Loading:** Rutas y componentes pesados con `defineAsyncComponent`
5. **Tipado estricto:** Interfaces TypeScript para todos los modelos

#### Frontend CRUD Architecture (Día 6+) - PATRON JSON-DRIVEN

**Objetivo**: Crear formularios CRUD completos (con búsqueda, filtrado, create, edit, delete) en 15 minutos por módulo.

**Componentes Reutilizables:**

1. **apiHelper.ts** (`/resources/js/apiHelper.ts`)

   - Abstracción centralizada HTTP (GET, POST, PUT, DELETE)
   - Manejo automático de Sanctum CSRF tokens
   - Interceptores para 419 (CSRF) y 401 (Unauthorized)
   - Queue inteligente para refresh simultáneo de requests

2. **FormSchema.vue** (`/resources/js/pages/form-template/FormSchema.vue`)

   - Componente maestro CRUD (lógica)
   - Carga items (GET), crea (POST), actualiza (PUT), elimina (DELETE)
   - Tabla con búsqueda + filtros personalizados
   - Diálogos create/edit, confirmación delete
   - Manejo de errores 422 (validación)
   - Conversión automática de fechas

3. **FormData.vue** (`/resources/js/pages/form-template/FormData.vue`)
   - Componente de formulario dinámico
   - 10 tipos de campos: text, email, number, password, textarea, select, date, time, checkbox, switch
   - Mapeo automático catálogos (ej: `role_id` busca `/api/roles`)
   - Watch reactivo para sincronización con datos iniciales
   - Methods: validate(), reset(), acceso a formData

**Estructura JSON (por módulo):**

```
/resources/js/pages/[Module]/
├── Index.vue (121 líneas - mínimo)
└── [module]-form/
    ├── config.json       ← Endpoints, permisos, títulos
    ├── tableConfig.json  ← Columnas de tabla
    ├── itemForm.json     ← Campos del formulario
    └── filters.json      ← Filtros de búsqueda
```

**config.json** - Endpoints y permisos:

```json
{
  "endpoints": {
    "index": "/api/Person",
    "apiUrl": "/api/Person"
  },
  "titulo": "Person Management",
  "descripcion": "Manage employees",
  "permisos": { "crear": true, "editar": true, "eliminar": true }
}
```

**tableConfig.json** - Estructura tabla:

```json
{
  "headers": [
    { "text": "Name", "value": "name", "sortable": true },
    { "text": "Email", "value": "email", "sortable": true },
    { "text": "Actions", "value": "actions", "sortable": false }
  ],
  "options": { "dense": false, "itemsPerPage": 10 }
}
```

**itemForm.json** - Campos formulario:

```json
{
  "fields": [
    {
      "key": "name",
      "label": "Name",
      "type": "text",
      "rules": ["required", "min:3"]
    },
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "rules": ["required"]
    },
    { "key": "role_id", "label": "Role", "type": "select", "rules": [] }
  ],
  "catalogs": ["role"]
}
```

**filters.json** - Filtros búsqueda:

```json
[
  { "field": "department", "type": "select", "label": "Department" },
  { "field": "role_id", "type": "select", "label": "Role" }
]
```

**Index.vue** - Orquestador mínimo (sin lógica Vue):

```typescript
import configJson from "./Person-form/config.json";
import tableConfigJson from "./Person-form/tableConfig.json";
import itemFormJson from "./Person-form/itemForm.json";
import filtersJson from "./Person-form/filters.json";

const config = configJson as Config;
const tableConfig = tableConfigJson as TableConfig;
const itemForm = itemFormJson as ItemForm;
const filters = computed(() => filtersJson.map(/* populate dinámico */));

// Cargar catálogos dinámicos (roles, departamentos, etc)
const loadRoles = async () => {
  /* */
};
onMounted(() => loadRoles());
```

**Beneficio**: Agregar nuevo módulo CRUD completo en 15 minutos:

- [ ] Crear carpeta [module]-form/
- [ ] Copiar 4 JSONs y adaptar
- [ ] Copiar Index.vue template y modificar imports
- [ ] Agregar ruta en web.php
- [ ] Agregar link en AppSidebar.vue

**Ejemplo implementado:**

- `/resources/js/pages/Person/` - 121 líneas Index.vue
- Soporta búsqueda completa, 2 filtros (department, role), CRUD completo
  - Cambiar comportamiento = cambiar JSON (no código)
  - Típico: nuevo CRUD en 30 min (solo JSONs + Controller backend)

#### Seguridad

1. **CORS:** Configurado para subdominios `*.talentia.app`
2. **Rate Limiting:** 60 req/min por IP (Laravel Throttle)
3. **Validación:** Server-side obligatoria, client-side para UX
4. **SQL Injection:** Protegido por Eloquent (prepared statements)
5. **XSS:** Sanitización de inputs, CSP headers (roadmap)

---

## 4. Flujos Principales

### 4.1 Autenticación (Simplificada para MVP)

#### Flujo MVP (Sin Login Real)

1. Usuario accede a `techcorp.talentia.app`
2. Middleware detecta subdomain → carga `organization_id = 1` (TechCorp)
3. Usuario hardcoded: `demo@techcorp.com` (rol: Admin)
4. Token ficticio en localStorage para simular sesión
5. Todas las consultas filtran por `organization_id = 1`

#### Flujo Post-MVP (Auth Real)

1. Usuario accede a subdomain
2. Formulario de login (email + password)
3. Laravel Sanctum genera token SPA
4. Token almacenado en cookie httpOnly
5. Middleware valida token + extrae `organization_id` del user
6. Logout invalida token

### 4.2 Onboarding de Organización (Post-MVP)

#### Pasos

1. **Registro:** Formulario con datos de empresa (nombre, industria, tamaño)
2. **Creación de tenant:** Insert en `organizations`, genera subdomain
3. **Setup inicial:**
   - Importar catálogo de skills (plantilla por industria o custom)
   - Definir roles clave (ej: "Software Engineer", "Product Manager")
   - Cargar empleados (CSV import o manual)
4. **Configuración de consultoría:**
   - Asignar consultor TalentIA
   - Agendar sesiones de mapeo de talento
5. **Activación:** Envío de invitaciones a usuarios

### 4.3 Dashboard Ejecutivo

#### Flujo

1. Usuario (CHRO) accede a `/dashboard`
2. Backend calcula métricas en tiempo real:
   - **Cobertura de skills críticas:** % de roles con skills cubiertas al 80%+
   - **Roles en riesgo:** Roles con <50% de cobertura
   - **Brechas totales:** Suma de gaps en skills críticas
   - **Talento listo para promoción:** Personas con match >90% a roles superiores
   - **ROI de formación:** Reducción de brechas post-capacitación (roadmap)
3. Frontend renderiza:
   - Cards con KPIs principales
   - Gráfico de barras: Top 10 skills con mayor brecha
   - Tabla: Roles en riesgo con acciones sugeridas
   - Heatmap: Cobertura de skills por departamento

#### Endpoints

- `GET /api/dashboard/metrics` → KPIs generales
- `GET /api/dashboard/skills-gaps` → Top skills con brechas
- `GET /api/dashboard/roles-at-risk` → Roles críticos

### 4.4 Gestión de Perfiles de Talento

#### Flujo: Ver Perfil de Empleado

1. Usuario accede a `/Person/{id}`
2. Backend retorna:
   - Datos personales (nombre, cargo actual, departamento)
   - Skills actuales con niveles (1-5)
   - Roles sugeridos (match >70%)
   - Rutas de desarrollo activas
   - Historial de evaluaciones (roadmap)
3. Frontend muestra:
   - Card con foto y datos básicos
   - Radar chart con skills principales
   - Lista de skills con progress bars
   - Tabla de roles sugeridos con % match
   - Timeline de rutas de desarrollo

#### Flujo: Calcular Brecha Persona ↔ Rol

1. Usuario selecciona persona y rol objetivo
2. Click en "Calcular Brecha"
3. Backend (`GapAnalysisService`):
   - Obtiene skills de persona con niveles actuales
   - Obtiene skills requeridas del rol con niveles mínimos
   - Calcula gaps: `gap = max(0, required_level - current_level)`
   - Calcula % match: `(skills_ok / total_skills) * 100`
4. Frontend muestra:
   - % match general
   - Lista de skills OK (verde), en desarrollo (amarillo), faltantes (rojo)
   - Botón "Generar Ruta de Desarrollo"

#### Endpoints

- `GET /api/Person/{id}` → Perfil completo
- `GET /api/Person/{id}/skills` → Skills con niveles
- `POST /api/gap-analysis` → Body: `{person_id, role_id}` → Response: gaps + match%

### 4.5 Rutas de Desarrollo

#### Flujo: Generar Ruta Automática

1. Usuario en vista de brecha → Click "Generar Ruta"
2. Backend (`DevelopmentPathService`):
   - Identifica skills con gap > 0
   - Busca cursos/recursos en catálogo que cubran esas skills
   - Prioriza por impacto (skills críticas primero)
   - Estima duración total (suma de horas de cursos)
3. Crea registro en `development_paths` con:
   - `person_id`, `target_role_id`
   - `status: draft`
   - JSON con pasos: `[{skill_id, action_type, resource_id, duration}]`
4. Frontend muestra:
   - Timeline visual con pasos
   - Duración estimada total
   - Botones: "Aprobar", "Editar", "Rechazar"

#### Flujo: Seguimiento de Ruta

1. Usuario accede a `/development-paths/{id}`
2. Backend retorna ruta con progreso:
   - Pasos completados vs pendientes
   - Skills mejoradas (comparación pre/post)
   - Próximas acciones
3. Frontend muestra:
   - Progress bar general
   - Checklist de pasos con estados
   - Botón "Marcar Paso como Completado"

#### Endpoints

- `POST /api/development-paths/generate` → Body: `{person_id, role_id}` → Response: ruta generada
- `GET /api/development-paths/{id}` → Detalle de ruta
- `PATCH /api/development-paths/{id}/steps/{step_id}` → Actualizar estado de paso

### 4.6 Selección por Skills (Interno vs Externo)

#### Flujo: Crear Vacante

1. Usuario (Recruiter) accede a `/job-openings/new`
2. Formulario:
   - Título de vacante
   - Rol asociado (dropdown de roles existentes)
   - Departamento
   - Fecha límite
3. Submit → Backend crea `job_opening` con `status: open`
   - Marca top 5 como "candidatos sugeridos"
4. Frontend en `/job-openings/{id}`:
   - Tabla con candidatos internos rankeados
   - Columnas: Nombre, Cargo Actual, % Match, Skills Faltantes
   - Botón "Invitar a Postular"

#### Flujo: Comparación Interno vs Externo

1. Usuario en vista de vacante → Tab "Comparar Candidatos"
2. Selecciona candidato interno + sube CV de candidato externo (roadmap: parsing con IA)
3. Backend:
   - Extrae skills del CV externo (simulado en MVP)
   - Calcula match de ambos vs rol
   - Genera reporte comparativo
4. Frontend muestra:
   - Tabla lado a lado: Interno vs Externo
   - Métricas: Match%, Time to Productivity, Costo, Riesgo
   - Recomendación: "Candidato interno preferido" o "Buscar externo"

#### Endpoints

- `POST /api/job-openings` → Crear vacante
- `GET /api/job-openings/{id}/candidates` → Candidatos internos rankeados
- `POST /api/job-openings/{id}/compare` → Body: `{internal_person_id, external_cv_file}` → Response: comparación

### 4.7 Marketplace Interno

#### Flujo: Explorar Oportunidades (Empleado)

1. Empleado accede a `/marketplace`
2. Backend retorna vacantes abiertas con match personal:
   - Filtra `job_openings` con `status: open`
   - Calcula match del empleado vs cada vacante
   - Ordena por match descendente
3. Frontend muestra:
   - Cards de vacantes con % match
   - Filtros: Departamento, Nivel, Match mínimo
   - Botón "Postular" en cada card

#### Flujo: Postular a Oportunidad

1. Empleado click "Postular" en vacante
2. Modal con:
   - Mensaje opcional al manager
   - Confirmación de interés
3. Submit → Backend crea `application` con `status: pending`
4. Notificación al manager de la vacante (roadmap)

#### Flujo: Gestionar Postulaciones (Manager)

1. Manager accede a `/job-openings/{id}/applications`
2. Backend retorna postulaciones con datos de candidatos
3. Frontend muestra:
   - Tabla con postulantes
   - Columnas: Nombre, % Match, Mensaje, Fecha
   - Acciones: "Aceptar", "Rechazar", "Ver Perfil"

#### Endpoints

- `GET /api/marketplace` → Oportunidades con match personal
- `POST /api/applications` → Body: `{job_opening_id, message}` → Crear postulación
- `GET /api/job-openings/{id}/applications` → Postulaciones de una vacante
- `PATCH /api/applications/{id}` → Actualizar estado (accept/reject)

---

## 5. Reglas de Negocio

### 5.1 Skills y Niveles

#### Escala de Dominio (1-5)

1. **Básico:** Conocimiento teórico, requiere supervisión constante
2. **Intermedio:** Puede ejecutar tareas con supervisión ocasional
3. **Avanzado:** Ejecuta de forma autónoma, resuelve problemas complejos
4. **Experto:** Referente interno, mentorea a otros
5. **Maestro:** Autoridad reconocida, innova y define estándares

#### Validaciones

- Nivel mínimo: 1, máximo: 5
- Una persona no puede tener la misma skill duplicada
- Al actualizar nivel, registrar fecha de última evaluación (roadmap)
- Skills obsoletas: marcar como `deprecated` en lugar de eliminar

### 5.2 Cálculo de Brechas

#### Algoritmo

```php
function calculateGap(Person $person, Role $role): array
{
    foreach ($role->skills as $roleSkill) {
        $personSkill = $person->skills->firstWhere('id', $roleSkill->id);
        $currentLevel = $personSkill?->pivot->level ?? 0;
        $requiredLevel = $roleSkill->pivot->required_level;

        $gap = max(0, $requiredLevel - $currentLevel);

        if ($gap === 0) {
            $skillsOk++;
            $status = 'ok';
        } elseif ($gap <= 1) {
            $status = 'developing';
        } else {
            $status = 'critical';
        }

        $gaps[] = [
            'skill_id' => $roleSkill->id,
            'skill_name' => $roleSkill->name,
            'current_level' => $currentLevel,
            'required_level' => $requiredLevel,
            'gap' => $gap,
            'status' => $status,
        ];
        'gaps' => $gaps,
    ];
}
```

#### Reglas

- Match >90%: "Listo para el rol"
- Match 70-90%: "Candidato potencial, requiere desarrollo"
- Match 50-70%: "Brecha significativa, ruta de desarrollo larga"
- Match <50%: "No recomendado para este rol"

### 5.3 Rutas de Desarrollo

#### Tipos de Acciones

1. **Curso online:** Link a plataforma externa (Coursera, Udemy, etc.)
2. **Mentoría:** Asignación de mentor interno
3. **Proyecto práctico:** Participación en proyecto real
4. **Certificación:** Examen/certificación oficial
5. **Job shadowing:** Observación de experto (roadmap)

#### Priorización de Skills

1. **Críticas:** Skills marcadas como `is_critical` en el rol
2. **Alto impacto:** Skills con gap >2 niveles
3. **Rápidas de cerrar:** Skills con gap =1 y cursos cortos disponibles

#### Validaciones

- Una persona puede tener máximo 3 rutas activas simultáneas
- Duración total de ruta no debe exceder 12 meses (warning, no bloqueante)
- Al completar ruta, actualizar niveles de skills automáticamente (roadmap)

### 5.4 Selección y Vacantes

#### Estados de Vacante

- `draft`: Borrador, no visible en marketplace
- `open`: Publicada, acepta postulaciones
- `closed`: Cerrada, no acepta más postulaciones
- `filled`: Cubierta, candidato seleccionado

#### Estados de Postulación

- `pending`: Pendiente de revisión
- `under_review`: En proceso de evaluación
- `accepted`: Aceptada, candidato seleccionado
- `rejected`: Rechazada

#### Reglas

- Una persona puede postular máximo 1 vez a la misma vacante
- Al aceptar una postulación, rechazar automáticamente las demás de esa vacante
- Vacantes abiertas por más de 90 días: alerta al recruiter (roadmap)
- Candidatos internos tienen prioridad visual en listados (badge "Interno")

### 5.5 Roles y Permisos (Simplificado para MVP)

#### Roles de Usuario

1. **Super Admin:** Acceso total, gestiona organizaciones (fuera de MVP)
2. **Org Admin:** Administrador de la organización, configura catálogos
3. **HR Manager:** Gestiona personas, vacantes, rutas de desarrollo
4. **Recruiter:** Gestiona vacantes y postulaciones
5. **Manager:** Ve equipo, aprueba rutas de desarrollo
6. **Employee:** Ve su perfil, postula a oportunidades

#### Permisos MVP (Simplificado)

- **MVP:** Todos los usuarios tienen rol `admin` (sin restricciones)
- **Post-MVP:** Implementar middleware de permisos por rol

---

## 6. APIs y Endpoints

### 6.1 Convenciones

#### Base URL

```
https://api.talentia.app/v1
```

#### Headers Requeridos

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
X-Organization-ID: {org_id} (opcional, extraído de subdomain)
```

#### Respuestas Estándar

```json
// Éxito
{
  "success": true,
  "data": { ... },
  "message": "Operación exitosa"
}

// Error
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Los datos proporcionados no son válidos",
    "details": {
      "email": ["El email ya está registrado"]
    }
  }
}

// Paginación
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8
  }
}
```

### 6.2 Endpoints Principales

> **Leyenda de Estado MVP:**  
> ✅ MVP - IMPLEMENTADO: Funcionalidad incluida en MVP  
> 🔴 POST-MVP: Funcionalidad para roadmap post-MVP  
> 🟡 SIMULADO: Lógica simplificada/mock en MVP, implementación real post-MVP

#### Autenticación 🔴 POST-MVP

```
POST   /auth/login
POST   /auth/logout
POST   /auth/refresh
GET    /auth/me
```

#### Dashboard ✅ MVP - IMPLEMENTADO

```
GET    /dashboard/metrics
       Response: { coverage, roles_at_risk, total_gaps, ready_for_promotion }

GET    /dashboard/skills-gaps
       Query: ?limit=10
       Response: [{ skill_id, skill_name, total_gap, Person_affected }]

GET    /dashboard/roles-at-risk
       Response: [{ role_id, role_name, coverage_percentage, critical_skills_missing }]
```

#### Personas ✅ LECTURA MVP / 🔴 CRUD POST-MVP

```
GET    /Person                                    ✅ MVP
       Query: ?search=john&department=engineering&page=1
       Response: Paginado de personas

GET    /Person/{id}                               ✅ MVP
       Response: Perfil completo con skills

GET    /Person/{id}/skills                        ✅ MVP
       Response: [{ skill_id, skill_name, level, last_evaluated_at }]

POST   /Person/{id}/skills                        🔴 POST-MVP
       Body: { skill_id, level }
       Response: Skill agregada

PATCH  /Person/{id}/skills/{skill_id}             🔴 POST-MVP
       Body: { level }
       Response: Nivel actualizado

DELETE /Person/{id}/skills/{skill_id}             🔴 POST-MVP
       Response: Skill removida
```

#### Skills (Catálogo) ✅ MVP - LECTURA

```
GET    /skills                                    ✅ MVP
       Query: ?category=technical&search=python
       Response: [{ id, name, category, description }]

GET    /skills/{id}                               ✅ MVP
       Response: Detalle de skill con personas que la tienen
```

#### Roles ✅ MVP - LECTURA

```
GET    /roles                                     ✅ MVP
       Response: [{ id, name, department, skills_count }]

GET    /roles/{id}                                ✅ MVP
       Response: Detalle con skills requeridas y niveles

GET    /roles/{id}/Person                         ✅ MVP
       Query: ?min_match=70
       Response: Personas con match a este rol
```

#### Análisis de Brechas ✅ MVP - IMPLEMENTADO

```
POST   /gap-analysis                              ✅ MVP
       Body: { person_id, role_id }
       Response: {
         match_percentage,
         gaps: [{ skill_id, skill_name, current_level, required_level, gap, status }]
       }

GET    /gap-analysis/person/{person_id}           ✅ MVP
       Response: Brechas de persona vs todos los roles (top 5)
```

#### Rutas de Desarrollo ✅ MVP LECTURA / 🟡 GENERACIÓN SIMULADA

```
POST   /development-paths/generate                🟡 MVP - SIMULADO (lógica simplificada)
       Body: { person_id, role_id }
       Response: Ruta generada con pasos

GET    /development-paths                         ✅ MVP
       Query: ?person_id=5&status=active
       Response: Rutas filtradas

GET    /development-paths/{id}                    ✅ MVP
       Response: Detalle de ruta con progreso

PATCH  /development-paths/{id}                    🔴 POST-MVP
       Body: { status: 'active' | 'completed' | 'cancelled' }
       Response: Ruta actualizada

PATCH  /development-paths/{id}/steps/{step_id}    🔴 POST-MVP
       Body: { completed: true }
       Response: Paso marcado como completado
```

#### Vacantes ✅ MVP - MATCHING IMPLEMENTADO

```
GET    /job-openings                              ✅ MVP
       Query: ?status=open&department=engineering
       Response: Vacantes filtradas

POST   /job-openings                              🔴 POST-MVP
       Body: { title, role_id, department, deadline }
       Response: Vacante creada

GET    /job-openings/{id}                         ✅ MVP
       Response: Detalle con skills requeridas

GET    /job-openings/{id}/candidates              ✅ MVP - MATCHING AUTOMÁTICO
       Query: ?min_match=60
       Response: Candidatos internos rankeados por match

POST   /job-openings/{id}/compare                 🟡 MVP - SIMULADO (CV parsing mock)
       Body: { internal_person_id, external_cv_text }
       Response: Comparación interno vs externo
```

       Body: { internal_person_id, external_cv_file }
       Response: Comparación interno vs externo

PATCH /job-openings/{id}
Body: { status: 'closed' | 'filled' }
Response: Vacante actualizada

```

#### Marketplace

```

GET /marketplace
Query: ?person_id=5 (opcional, si no se envía usa usuario autenticado)
Response: Vacantes abiertas con match personal

```

#### Postulaciones

```

POST /applications
Body: { job_opening_id, message }
Response: Postulación creada

GET /applications
Query: ?person_id=5&status=pending
Response: Postulaciones filtradas

GET /job-openings/{id}/applications
Response: Postulaciones de una vacante

PATCH /applications/{id}
Body: { status: 'accepted' | 'rejected' }
Response: Postulación actualizada

````

### 6.3 Ejemplos de Uso

#### Ejemplo 1: Calcular Brecha y Generar Ruta

```bash
# 1. Calcular brecha
curl -X POST https://api.talentia.app/v1/gap-analysis \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "person_id": 5,
    "role_id": 3
  }'

# Response
{
  "success": true,
  "data": {
    "match_percentage": 72.5,
    "gaps": [
      {
        "skill_id": 10,
        "skill_name": "React",
        "current_level": 2,
        "required_level": 4,
        "gap": 2,
        "status": "critical"
      },
      ...
    ]
  }
}

# 2. Generar ruta de desarrollo
curl -X POST https://api.talentia.app/v1/development-paths/generate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "person_id": 5,
    "role_id": 3
  }'

# Response
{
  "success": true,
  "data": {
    "id": 42,
    "person_id": 5,
    "target_role_id": 3,
    "status": "draft",
    "estimated_duration_months": 6,
    "steps": [
      {
        "skill_id": 10,
        "skill_name": "React",
        "action_type": "course",
        "resource_name": "React - The Complete Guide",
        "resource_url": "https://udemy.com/...",
        "duration_hours": 40
      },
      ...
    ]
  }
}
````

#### Ejemplo 2: Buscar Candidatos Internos para Vacante

```bash
curl -X GET "https://api.talentia.app/v1/job-openings/15/candidates?min_match=70" \
  -H "Authorization: Bearer {token}"

# Response
{
  "success": true,
  "data": [
    {
      "person_id": 8,
      "name": "Ana García",
      "current_role": "Software Engineer",
      "match_percentage": 88.5,
      "missing_skills": ["Kubernetes", "GraphQL"],
      "ready_in_months": 3
    },
    {
      "person_id": 12,
      "name": "Carlos López",
      "current_role": "Frontend Developer",
      "match_percentage": 75.0,
      "missing_skills": ["System Design", "Microservices", "Docker"],
      "ready_in_months": 6
    }
  ]
}
```

---

## 7. Modelo de Datos

### 7.1 Diagrama ER (Tablas Principales)

```
organizations
├── id (PK)
├── name
├── subdomain (unique)
├── industry
├── size
└── timestamps

users
├── id (PK)
├── organization_id (FK)
├── email (unique per org)
├── name
├── role (enum: admin, hr_manager, recruiter, manager, employee)
└── timestamps

skills
├── id (PK)
├── organization_id (FK)
├── name
├── category (enum: technical, soft, business, language)
├── description
├── is_critical (boolean)
└── timestamps

roles
├── id (PK)
├── organization_id (FK)
├── name
├── department
├── level (enum: junior, mid, senior, lead, principal)
├── description
└── timestamps

role_skills (pivot)
├── id (PK)
├── role_id (FK)
├── skill_id (FK)
├── required_level (1-5)
└── is_critical (boolean)

Person
├── id (PK)
├── organization_id (FK)
├── user_id (FK, nullable)
├── first_name
├── last_name
├── email
├── current_role_id (FK to roles)
├── department
├── hire_date
├── photo_url
└── timestamps

person_skills (pivot)
├── id (PK)
├── person_id (FK)
├── skill_id (FK)
├── level (1-5)
├── last_evaluated_at
├── evaluated_by (FK to users, nullable)
└── timestamps

development_paths
├── id (PK)
├── organization_id (FK)
├── person_id (FK)
├── target_role_id (FK)
├── status (enum: draft, active, completed, cancelled)
├── estimated_duration_months
├── started_at
├── completed_at
├── steps (JSON: [{skill_id, action_type, resource_name, resource_url, duration_hours, completed}])
└── timestamps

job_openings
├── id (PK)
├── organization_id (FK)
├── title
├── role_id (FK)
├── department
├── status (enum: draft, open, closed, filled)
├── deadline
├── created_by (FK to users)
└── timestamps

applications
├── id (PK)
├── job_opening_id (FK)
├── person_id (FK)
├── status (enum: pending, under_review, accepted, rejected)
├── message (text)
├── applied_at
└── timestamps

analytics_snapshots (roadmap)
├── id (PK)
├── organization_id (FK)
├── snapshot_date
├── metrics (JSON: {coverage, roles_at_risk, total_gaps, etc.})
└── timestamps
```

### 7.2 Campos Críticos y Constraints

#### organizations

- `subdomain`: Unique, lowercase, alphanumeric + hyphens, max 50 chars
- `size`: Enum ('small', 'medium', 'large', 'enterprise')

#### users

- `email`: Unique per organization (composite unique: email + organization_id)
- `role`: Default 'employee'

#### skills

- `name`: Unique per organization (composite unique: name + organization_id)
- `category`: Required, indexed

#### roles

- `name`: Unique per organization (composite unique: name + organization_id)
- `level`: Indexed for filtering

#### role_skills

- Composite unique: (role_id, skill_id)
- `required_level`: Check constraint (1-5)

#### Person

- `email`: Unique per organization
- `current_role_id`: Nullable (puede no tener rol asignado aún)
- Soft deletes enabled

#### person_skills

- Composite unique: (person_id, skill_id)
- `level`: Check constraint (1-5)
- `last_evaluated_at`: Default current timestamp

#### development_paths

- `steps`: JSON validado con schema (roadmap: migrar a tabla separada)
- Index en (person_id, status)

#### job_openings

- `deadline`: Nullable
- Index en (organization_id, status)

#### applications

- Composite unique: (job_opening_id, person_id) - una postulación por persona por vacante
- `applied_at`: Default current timestamp

### 7.3 Índices Recomendados

```sql
-- Multi-tenant queries
CREATE INDEX idx_org_id ON users(organization_id);
CREATE INDEX idx_org_id ON skills(organization_id);
CREATE INDEX idx_org_id ON roles(organization_id);
CREATE INDEX idx_org_id ON Person(organization_id);

-- Búsquedas frecuentes
CREATE INDEX idx_skills_category ON skills(category);
CREATE INDEX idx_Person_department ON Person(department);
CREATE INDEX idx_job_openings_status ON job_openings(status);
CREATE INDEX idx_applications_status ON applications(status);

-- Joins comunes
CREATE INDEX idx_person_skills_person ON person_skills(person_id);
CREATE INDEX idx_person_skills_skill ON person_skills(skill_id);
CREATE INDEX idx_role_skills_role ON role_skills(role_id);

-- Full-text search (roadmap)
CREATE INDEX idx_skills_name_fulltext ON skills USING gin(to_tsvector('spanish', name));
```

---

## 8. UI/UX

### 8.1 Patrones de Diseño

#### Sistema de Diseño Base: Vuetify 3

- **Tema:** Personalizado con colores corporativos TalentIA
- **Tipografía:** Inter (sans-serif) para UI, Roboto Mono para código/datos
- **Espaciado:** Sistema de 8px (múltiplos: 8, 16, 24, 32, 48, 64)
- **Breakpoints:**
  - xs: <600px (mobile)
  - sm: 600-960px (tablet)
  - md: 960-1264px (laptop)
  - lg: 1264-1904px (desktop)
  - xl: >1904px (large desktop)

#### Paleta de Colores

```scss
$primary: #2563eb; // Azul principal (acciones, links)
$secondary: #7c3aed; // Púrpura (destacados, badges)
$success: #10b981; // Verde (skills OK, completado)
$warning: #f59e0b; // Amarillo (en desarrollo, alertas)
$error: #ef4444; // Rojo (brechas críticas, errores)
$info: #3b82f6; // Azul claro (información)
$background: #f9fafb; // Gris muy claro (fondo general)
$surface: #ffffff; // Blanco (cards, modals)
$text-primary: #111827; // Gris oscuro (texto principal)
$text-secondary: #6b7280; // Gris medio (texto secundario)
```

#### Componentes Atómicos (Atomic Design)

**Atoms:**

- `SkillBadge`: Badge con nombre de skill + nivel (color según nivel)
- `MatchPercentage`: Circular progress con % match
- `LevelIndicator`: 5 círculos para mostrar nivel 1-5
- `StatusChip`: Chip con estado (draft, active, completed, etc.)
- `AvatarWithName`: Avatar circular + nombre + cargo

**Molecules:**

- `SkillCard`: Card con skill + nivel + última evaluación
- `RoleCard`: Card con rol + departamento + skills count
- `GapListItem`: Item de lista con skill + brecha + acción sugerida
- `CandidateCard`: Card con candidato + match% + botón acción

**Organisms:**

- `SkillsRadarChart`: Radar chart con top 8 skills
- `GapAnalysisTable`: Tabla completa de brechas con filtros
- `DevelopmentPathTimeline`: Timeline visual de ruta de desarrollo
- `DashboardMetricsGrid`: Grid de 4 cards con KPIs principales

### 8.2 Layouts

#### DefaultLayout

- **Uso:** Páginas públicas (landing, login - post-MVP)
- **Estructura:**
  - Header: Logo + navegación simple
  - Main: Contenido centrado, max-width 1200px
  - Footer: Links legales + redes sociales

#### DashboardLayout

- **Uso:** Toda la aplicación autenticada
- **Estructura:**
  - App Bar (top):
    - Logo TalentIA (link a dashboard)
    - Breadcrumbs
    - Buscador global (roadmap)
    - Notificaciones (roadmap)
    - Avatar + menú de usuario
  - Navigation Drawer (left, collapsible):
    - Dashboard
    - Personas
    - Roles
    - Skills
    - Vacantes
    - Marketplace
    - Rutas de Desarrollo
    - Analítica (roadmap)
    - Configuración (roadmap)
  - Main Content:
    - Padding: 24px
    - Background: $background
  - Footer (opcional): Copyright + versión

### 8.3 Vistas Principales (Wireframes Textuales)

#### Dashboard Ejecutivo (`/dashboard`)

```
┌─────────────────────────────────────────────────────────┐
│ Dashboard                                    [Filtros ▼]│
├─────────────────────────────────────────────────────────┤
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐   │
│ │Cobertura │ │Roles en  │ │Brechas   │ │Listos    │   │
│ │  85%  ↑  │ │Riesgo: 3 │ │Total: 42 │ │Promo: 8  │   │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘   │
│                                                          │
│ Top 10 Skills con Mayor Brecha                          │
│ ┌────────────────────────────────────────────────────┐ │
│ │ [Bar Chart: Skill Name | Gap Total]                │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Roles en Riesgo                          [Ver Todos →] │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Rol              │ Cobertura │ Skills Críticas     │ │
│ │ Senior Engineer  │ 45%       │ Kubernetes, AWS     │ │
│ │ Product Manager  │ 52%       │ Data Analysis       │ │
│ └────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

#### Perfil de Persona (`/Person/{id}`)

```
┌─────────────────────────────────────────────────────────┐
│ ← Personas                                               │
├─────────────────────────────────────────────────────────┤
│ ┌────┐ Ana García                                       │
│ │ AG │ Software Engineer · Engineering                  │
│ └────┘ ana.garcia@techcorp.com · Desde: 2021-03-15     │
│                                                          │
│ ┌─────────────────────┐ ┌──────────────────────────┐   │
│ │ Skills Principales  │ │ Roles Sugeridos          │   │
│ │ [Radar Chart]       │ │ • Senior Engineer (88%)  │   │
│ │                     │ │ • Tech Lead (72%)        │   │
│ │                     │ │ • Architect (58%)        │   │
│ └─────────────────────┘ └──────────────────────────┘   │
│                                                          │
│ Todas las Skills (15)                    [+ Agregar]    │
│ ┌────────────────────────────────────────────────────┐ │
│ │ React          [████░] 4/5  Última eval: 2024-01   │ │
│ │ TypeScript     [███░░] 3/5  Última eval: 2024-01   │ │
│ │ Node.js        [████░] 4/5  Última eval: 2023-12   │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ Rutas de Desarrollo Activas (2)         [Ver Todas →]  │
│ ┌────────────────────────────────────────────────────┐ │
│ │ → Senior Engineer · 65% completado · 3 meses rest. │ │
│ └────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

#### Análisis de Brecha (`/gap-analysis`)

```
┌─────────────────────────────────────────────────────────┐
│ Análisis de Brecha                                       │
├─────────────────────────────────────────────────────────┤
│ Persona: [Ana García ▼]  Rol Objetivo: [Tech Lead ▼]   │
│                                    [Calcular Brecha]    │
│                                                          │
│ Match General: 72%  [████████████████░░░░░░]            │
│                                                          │
│ Detalle de Brechas                                       │
│ ┌────────────────────────────────────────────────────┐ │
│ │ Skill          │ Actual │ Requerido │ Gap │ Estado │ │
│ │ System Design  │   2    │     4     │  2  │ 🔴     │ │
│ │ Kubernetes     │   1    │     3     │  2  │ 🔴     │ │
│ │ Mentoring      │   2    │     4     │  2  │ 🔴     │ │
│ │ React          │   4    │     4     │  0  │ 🟢     │ │
│ │ TypeScript     │   3    │     4     │  1  │ 🟡     │ │
│ └────────────────────────────────────────────────────┘ │
│                                                          │
│ [Generar Ruta de Desarrollo]                            │
└─────────────────────────────────────────────────────────┘
```

#### Marketplace Interno (`/marketplace`)

```
┌─────────────────────────────────────────────────────────┐
│ Oportunidades Internas                                   │
├─────────────────────────────────────────────────────────┤
│ Filtros: [Departamento ▼] [Nivel ▼] [Match >70%]       │
│                                                          │
│ ┌──────────────────────────────────────────────────┐   │
│ │ Senior Software Engineer                    88%  │   │
│ │ Engineering · Publicado hace 3 días               │   │
│ │ Skills: React, Node.js, System Design, AWS        │   │
│ │                                    [Postular →]   │   │
│ └──────────────────────────────────────────────────┘   │
│                                                          │
│ ┌──────────────────────────────────────────────────┐   │
│ │ Product Manager                             65%  │   │
│ │ Product · Publicado hace 1 semana                 │   │
│ │ Skills: Product Strategy, Data Analysis, Agile    │   │
│ │                                    [Postular →]   │   │
│ └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

### 8.4 Lineamientos Visuales

#### Tipografía

- **Headings:**
  - H1: 32px, bold, $text-primary
  - H2: 24px, semibold, $text-primary
  - H3: 20px, semibold, $text-primary
  - H4: 18px, medium, $text-primary
- **Body:**
  - Large: 16px, regular, $text-primary
  - Regular: 14px, regular, $text-primary
  - Small: 12px, regular, $text-secondary
- **Captions:** 11px, regular, $text-secondary

#### Iconografía

- **Librería:** Material Design Icons (mdi)
- **Tamaño:** 20px (small), 24px (default), 32px (large)
- **Uso:**
  - Skills: `mdi-star` (nivel), `mdi-brain` (skill)
  - Roles: `mdi-account-tie`
  - Brechas: `mdi-chart-line` (análisis), `mdi-alert-circle` (crítico)
  - Desarrollo: `mdi-road-variant` (ruta), `mdi-school` (curso)
  - Vacantes: `mdi-briefcase-outline`
  - Marketplace: `mdi-store`

#### Animaciones

- **Transiciones:** 200ms ease-in-out (default)
- **Hover states:** Elevación de cards (0 → 2), cambio de color en botones
- **Loading:** Skeleton loaders para contenido, spinners para acciones
- **Feedback:** Snackbars para confirmaciones/errores (4s duración)

#### Responsividad

- **Mobile-first:** Diseñar primero para móvil, luego escalar
- **Navigation Drawer:** Permanente en desktop (lg+), temporal en mobile/tablet
- **Tables:** Convertir a cards en mobile (<sm)
- **Charts:** Ajustar altura/ancho según breakpoint

---

## 9. Operación

### 9.1 Entornos

#### Local (Desarrollo)

- **Backend:** `http://localhost:8000`
- **Frontend:** `http://localhost:5173`
- **DB:** PostgreSQL en Docker (`localhost:5432`)
- **Variables:** `.env.local`

#### Staging (Pre-producción)

- **URL:** `https://staging.talentia.app`
- **DB:** PostgreSQL en Digital Ocean (managed)
- **Deploy:** Manual via SSH (roadmap: CI/CD)
- **Variables:** `.env.staging`

#### Production (Producción)

- **URL:** `https://app.talentia.app` (wildcard: `*.talentia.app`)
- **DB:** PostgreSQL en Digital Ocean (managed, backups diarios)
- **Deploy:** Manual via SSH (roadmap: CI/CD con GitHub Actions)
- **Variables:** `.env.production`

### 9.2 Build y Deploy

#### Backend (Laravel)

**Build Local:**

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed --class=DemoSeeder
php artisan serve
```

**Deploy a Producción:**

```bash
# SSH al droplet
ssh root@talentia.app

# Pull latest code
cd /var/www/talentia-api
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

#### Frontend (Vue 3)

**Build Local:**

```bash
npm install
npm run dev
```

**Build para Producción:**

```bash
npm run build
# Output: dist/
```

**Deploy a Producción:**

```bash
# Build local
npm run build

# Upload to server
scp -r dist/* root@talentia.app:/var/www/talentia-frontend/

# Nginx sirve archivos estáticos desde /var/www/talentia-frontend/
```

### 9.3 Variables de Entorno

#### Backend (.env)

```bash
APP_NAME=TalentIA
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://api.talentia.app

DB_CONNECTION=pgsql
DB_HOST=db.talentia.app
DB_PORT=5432
DB_DATABASE=talentia_prod
DB_USERNAME=talentia_user
DB_PASSWORD=***

SANCTUM_STATEFUL_DOMAINS=*.talentia.app
SESSION_DOMAIN=.talentia.app

# IA (roadmap)
OPENAI_API_KEY=sk-...

# Email (roadmap)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=***
```

#### Frontend (.env)

```bash
VITE_API_BASE_URL=https://api.talentia.app/v1
VITE_APP_NAME=TalentIA
VITE_APP_ENV=production
```

### 9.4 Backups

#### Base de Datos

- **Frecuencia:** Diarios (automático en Digital Ocean Managed DB)
- **Retención:** 7 días (rolling)
- **Backup manual:**
  ```bash
  pg_dump -h db.talentia.app -U talentia_user talentia_prod > backup_$(date +%Y%m%d).sql
  ```

#### Código

- **Repositorio:** GitHub (privado)
- **Branches:**
  - `main`: Producción
  - `develop`: Desarrollo
  - `feature/*`: Features en progreso

#### Archivos (Roadmap)

- **Storage:** Digital Ocean Spaces (S3-compatible)
- **Contenido:** Fotos de perfil, CVs, certificados

### 9.5 Monitoreo (Roadmap)

#### Logs

- **Laravel:** `storage/logs/laravel.log` (rotación diaria)
- **Nginx:** `/var/log/nginx/access.log`, `/var/log/nginx/error.log`
- **Centralización:** Sentry (errores), Papertrail (logs)

#### Métricas

- **Uptime:** UptimeRobot (ping cada 5 min)
- **Performance:** Laravel Telescope (dev), New Relic (prod)
- **DB:** Slow query log, connection pool monitoring

#### Alertas

- **Downtime:** Email + Slack
- **Errores críticos:** Sentry → Slack
- **DB full:** Digital Ocean alerts

---

## 10. Pendientes y Riesgos

### 10.1 TODOs del MVP (Priorizados)

#### Semana 1 (Críticos)

- [ ] Setup inicial: Laravel + PostgreSQL + Vue 3 + Vuetify
- [ ] Modelo de datos completo con migraciones
- [ ] Seeders con datos de demo (TechCorp)
- [ ] API de personas con skills (CRUD básico)
- [ ] Algoritmo de cálculo de brechas (`GapAnalysisService`)
- [ ] API de rutas de desarrollo (generación automática)
- [ ] Frontend: Layout principal + navegación
- [ ] Frontend: Vista de perfil de persona
- [ ] Frontend: Vista de análisis de brecha
- [ ] Frontend: Vista de ruta de desarrollo

#### Semana 2 (Altos)

- [ ] API de vacantes y postulaciones
- [ ] API de marketplace con matching
- [ ] API de dashboard con métricas
- [ ] Frontend: Vista de vacante con candidatos
- [ ] Frontend: Vista de marketplace
- [ ] Frontend: Dashboard ejecutivo con charts
- [ ] Comparación interno vs externo (lógica básica)
- [ ] Pulido visual (colores, espaciados, responsive)
- [ ] Guion de demo paso a paso
- [ ] Testing básico (happy paths)

### 10.2 Deudas Técnicas

#### Arquitectura

- **Multi-tenant real:** Implementar middleware de tenant + subdomain routing (MVP usa org_id hardcoded)
- **Autenticación:** Laravel Sanctum con login real (MVP sin auth)
- **Permisos:** Middleware de roles y permisos (MVP todos admin)
- **Queues:** Mover cálculos pesados a jobs (ej: matching de 1000+ personas)

#### Backend

- **Validación exhaustiva:** Form Requests en todos los endpoints (MVP validación básica)
- **Testing:** Cobertura >80% con PHPUnit/Pest (MVP solo happy paths)
- **API versioning:** Preparar para v2 (cambios breaking)
- **Rate limiting:** Por usuario, no solo por IP
- **Soft deletes:** Implementar en todas las tablas críticas

#### Frontend

- **State management:** Pinia stores para estado global (MVP props drilling)
- **Error handling:** Interceptores Axios + manejo centralizado
- **Testing:** Vitest + Vue Test Utils (MVP sin tests)
- **Optimización:** Lazy loading de rutas y componentes pesados
- **Accesibilidad:** ARIA labels, keyboard navigation, screen reader support

#### Datos

- **Migraciones reversibles:** Todas las migraciones deben tener `down()`
- **Constraints DB:** Foreign keys, checks, unique composites
- **Índices:** Optimizar queries lentas con índices estratégicos
- **Auditoría:** Tabla de auditoría para cambios críticos (quién, cuándo, qué)

### 10.3 Riesgos Identificados

#### Técnicos

1. **Performance con multi-tenant:**

   - **Riesgo:** Queries lentas al escalar a 100+ organizaciones
   - **Mitigación:** Índices en `organization_id`, caching con Redis (roadmap)

2. **Cálculo de brechas en tiempo real:**

   - **Riesgo:** Timeout en organizaciones con 1000+ empleados
   - **Mitigación:** Jobs asíncronos, pre-cálculo nocturno, caching

3. **Escalabilidad de IA:**

   - **Riesgo:** Costos de OpenAI API al escalar
   - **Mitigación:** Modelo local (sentence-transformers), rate limiting

4. **Integridad de datos:**
   - **Riesgo:** Skills/roles huérfanos al eliminar registros
   - **Mitigación:** Soft deletes, foreign keys con `ON DELETE RESTRICT`

#### Negocio

1. **Adopción de usuarios:**

   - **Riesgo:** Curva de aprendizaje alta, resistencia al cambio
   - **Mitigación:** Onboarding guiado, consultoría incluida, demos interactivas

2. **Competencia:**

   - **Riesgo:** Workday, SAP SuccessFactors con módulos similares
   - **Mitigación:** Diferenciación en consultoría + SSI, nicho en empresas medianas

3. **Dependencia de consultoría:**
   - **Riesgo:** Software solo no genera valor sin acompañamiento
   - **Mitigación:** Plantillas por industria, wizards de configuración, contenido educativo

#### Operacionales

1. **Deploy manual:**

   - **Riesgo:** Errores humanos, downtime
   - **Mitigación:** CI/CD con GitHub Actions (roadmap), rollback automático

2. **Backups:**

   - **Riesgo:** Pérdida de datos por fallo de DB
   - **Mitigación:** Backups diarios automáticos, réplicas en standby (roadmap)

3. **Monitoreo:**
   - **Riesgo:** Downtime no detectado a tiempo
   - **Mitigación:** UptimeRobot, Sentry, alertas a Slack

### 10.4 Roadmap Post-MVP (3-6 meses)

#### Mes 1-2: Estabilización

- Autenticación real con Sanctum
- Roles y permisos completos
- CRUD completo de skills, roles, personas
- Testing exhaustivo (backend + frontend)
- CI/CD con GitHub Actions
- Monitoreo con Sentry + New Relic

#### Mes 3-4: Features Avanzadas

- IA real con OpenAI API (inferencia de skills desde CVs)
- Integraciones: ATS (Greenhouse, Lever), HRIS (BambooHR)
- Módulo de desempeño completo (evaluaciones 360°, OKRs)
- Notificaciones push + email
- Mobile responsive optimizado

```markdown
#### Mes 5-6: Escalabilidad y SSI

- Caching con Redis (queries, dashboards, matching)
- Jobs asíncronos para cálculos pesados (queues con Laravel Horizon)
- Optimización de queries (N+1, eager loading, índices)
- API pública para integraciones de terceros
- Documentación API con OpenAPI/Swagger
- SSI: Proof of Concept con Hyperledger Aries
- Credenciales verificables para skills críticas
- Wallet móvil para empleados (roadmap)

### 10.5 Métricas de Éxito del MVP

#### Técnicas

- [ ] Tiempo de carga de dashboard <2s
- [ ] Cálculo de brecha persona-rol <500ms
- [ ] API response time p95 <300ms
- [ ] Zero errores críticos en demo
- [ ] Responsive en mobile (>90% usabilidad)

#### Negocio

- [ ] 3 demos exitosas con clientes potenciales
- [ ] 1 piloto confirmado (empresa 100-500 empleados)
- [ ] Feedback positivo en 80%+ de demos
- [ ] Tiempo de demo <20 minutos
- [ ] Storytelling claro del valor (ROI, casos de uso)

#### Producto

- [ ] Flujo completo persona → brecha → ruta → vacante funcional
- [ ] Dashboard con 6+ KPIs relevantes
- [ ] Datos de demo realistas y convincentes
- [ ] UI pulida y profesional (sin bugs visuales)
- [ ] Guion de demo documentado paso a paso

---

## 11. Datos de Demo: Historia de TechCorp

### 11.1 Contexto de la Empresa

**TechCorp S.A.**

- **Industria:** Tecnología / Software Development
- **Tamaño:** 20 empleados (startup en crecimiento)
- **Fundación:** 2020
- **Ubicación:** Santiago, Chile
- **Producto:** Plataforma SaaS de gestión de proyectos
- **Desafío:** Crecimiento rápido (de 10 a 20 empleados en 1 año), necesidad de estructurar talento y planificar promociones internas

### 11.2 Estructura Organizacional

#### Departamentos

1. **Engineering (12 personas)**

   - Frontend Team (4)
   - Backend Team (4)
   - DevOps (2)
   - QA (2)

2. **Product (3 personas)**

   - Product Manager (1)
   - Product Designer (2)

3. **Operations (5 personas)**
   - CEO (1)
   - HR Manager (1)
   - Sales (2)
   - Customer Success (1)

### 11.3 Catálogo de Skills (30 skills)

#### Technical Skills (15)

1. **JavaScript** (Frontend)
2. **TypeScript** (Frontend)
3. **React** (Frontend)
4. **Vue.js** (Frontend)
5. **Node.js** (Backend)
6. **Python** (Backend)
7. **PostgreSQL** (Backend)
8. **MongoDB** (Backend)
9. **Docker** (DevOps)
10. **Kubernetes** (DevOps)
11. **AWS** (DevOps)
12. **CI/CD** (DevOps)
13. **System Design** (Architecture)
14. **API Design** (Backend)
15. **Testing/QA** (Quality)

#### Soft Skills (10)

16. **Communication**
17. **Leadership**
18. **Mentoring**
19. **Problem Solving**
20. **Collaboration**
21. **Time Management**
22. **Adaptability**
23. **Critical Thinking**
24. **Conflict Resolution**
25. **Emotional Intelligence**

#### Business Skills (5)

26. **Product Strategy**
27. **Data Analysis**
28. **Agile/Scrum**
29. **Stakeholder Management**
30. **Customer Empathy**

### 11.4 Roles Definidos (8 roles)

#### 1. Junior Frontend Developer

**Departamento:** Engineering  
**Nivel:** Junior  
**Skills Requeridas:**

- JavaScript: 2
- React o Vue.js: 2
- HTML/CSS: 2 (no en catálogo, simplificado)
- Communication: 2
- Collaboration: 2

#### 2. Frontend Developer

**Departamento:** Engineering  
**Nivel:** Mid  
**Skills Requeridas:**

- JavaScript: 3
- TypeScript: 3
- React: 3
- Vue.js: 2
- Testing/QA: 2
- Communication: 3
- Problem Solving: 3

#### 3. Senior Frontend Developer

**Departamento:** Engineering  
**Nivel:** Senior  
**Skills Requeridas:**

- JavaScript: 4
- TypeScript: 4
- React: 4
- System Design: 3
- Mentoring: 3
- Leadership: 3
- Communication: 4

#### 4. Backend Developer

**Departamento:** Engineering  
**Nivel:** Mid  
**Skills Requeridas:**

- Node.js o Python: 3
- PostgreSQL: 3
- API Design: 3
- Testing/QA: 2
- Problem Solving: 3
- Communication: 3

#### 5. Senior Backend Developer

**Departamento:** Engineering  
**Nivel:** Senior  
**Skills Requeridas:**

- Node.js: 4
- Python: 3
- PostgreSQL: 4
- System Design: 4
- API Design: 4
- Mentoring: 3
- Leadership: 3

#### 6. DevOps Engineer

**Departamento:** Engineering  
**Nivel:** Mid-Senior  
**Skills Requeridas:**

- Docker: 4
- Kubernetes: 3
- AWS: 4
- CI/CD: 4
- System Design: 3
- Problem Solving: 4

#### 7. Product Manager

**Departamento:** Product  
**Nivel:** Mid-Senior  
**Skills Requeridas:**

- Product Strategy: 4
- Data Analysis: 3
- Agile/Scrum: 4
- Stakeholder Management: 4
- Customer Empathy: 4
- Communication: 4
- Leadership: 3

#### 8. Tech Lead

**Departamento:** Engineering  
**Nivel:** Lead  
**Skills Requeridas:**

- System Design: 5
- API Design: 4
- Leadership: 4
- Mentoring: 4
- Communication: 5
- Problem Solving: 5
- Agile/Scrum: 3
- (Plus: JavaScript 4 o Node.js 4)

### 11.5 Perfiles de Empleados (20 personas)

#### Engineering Team

**1. Ana García**

- **Cargo Actual:** Frontend Developer
- **Departamento:** Engineering
- **Hire Date:** 2021-03-15
- **Email:** ana.garcia@techcorp.com
- **Skills:**
  - JavaScript: 4
  - TypeScript: 3
  - React: 4
  - Vue.js: 2
  - Testing/QA: 3
  - Communication: 4
  - Problem Solving: 4
  - Mentoring: 2
- **Potencial:** Senior Frontend Developer (88% match)

**2. Carlos López**

- **Cargo Actual:** Frontend Developer
- **Departamento:** Engineering
- **Hire Date:** 2022-01-10
- **Email:** carlos.lopez@techcorp.com
- **Skills:**
  - JavaScript: 3
  - TypeScript: 3
  - React: 3
  - Testing/QA: 2
  - Communication: 3
  - Problem Solving: 3
  - Collaboration: 4
- **Potencial:** Senior Frontend Developer (65% match, necesita desarrollo)

**3. María Rodríguez**

- **Cargo Actual:** Junior Frontend Developer
- **Departamento:** Engineering
- **Hire Date:** 2023-06-01
- **Email:** maria.rodriguez@techcorp.com
- **Skills:**
  - JavaScript: 2
  - React: 2
  - Communication: 3
  - Collaboration: 3
  - Adaptability: 4
- **Potencial:** Frontend Developer (70% match)

**4. Diego Fernández**

- **Cargo Actual:** Junior Frontend Developer
- **Departamento:** Engineering
- **Hire Date:** 2023-09-15
- **Email:** diego.fernandez@techcorp.com
- **Skills:**
  - JavaScript: 2
  - Vue.js: 2
  - Communication: 2
  - Collaboration: 3
  - Problem Solving: 2
- **Potencial:** Frontend Developer (55% match, necesita más tiempo)

**5. Luis Martínez**

- **Cargo Actual:** Backend Developer
- **Departamento:** Engineering
- **Hire Date:** 2021-07-20
- **Email:** luis.martinez@techcorp.com
- **Skills:**
  - Node.js: 4
  - Python: 3
  - PostgreSQL: 4
  - API Design: 4
  - System Design: 3
  - Testing/QA: 3
  - Problem Solving: 4
  - Mentoring: 2
- **Potencial:** Senior Backend Developer (85% match)

**6. Sofía Ramírez**

- **Cargo Actual:** Backend Developer
- **Departamento:** Engineering
- **Hire Date:** 2022-03-10
- **Email:** sofia.ramirez@techcorp.com
- **Skills:**
  - Node.js: 3
  - PostgreSQL: 3
  - API Design: 3
  - Testing/QA: 2
  - Problem Solving: 3
  - Communication: 3
- **Potencial:** Senior Backend Developer (60% match)

**7. Javier Torres**

- **Cargo Actual:** Backend Developer
- **Departamento:** Engineering
- **Hire Date:** 2022-08-01
- **Email:** javier.torres@techcorp.com
- **Skills:**
  - Python: 3
  - PostgreSQL: 3
  - MongoDB: 3
  - API Design: 2
  - Problem Solving: 3
  - Collaboration: 4
- **Potencial:** Senior Backend Developer (55% match)

**8. Valentina Silva**

- **Cargo Actual:** Backend Developer
- **Departamento:** Engineering
- **Hire Date:** 2023-02-15
- **Email:** valentina.silva@techcorp.com
- **Skills:**
  - Node.js: 2
  - PostgreSQL: 2
  - API Design: 2
  - Testing/QA: 3
  - Communication: 3
  - Adaptability: 4
- **Potencial:** Mid Backend Developer (en desarrollo)

**9. Roberto Morales**

- **Cargo Actual:** DevOps Engineer
- **Departamento:** Engineering
- **Hire Date:** 2021-05-10
- **Email:** roberto.morales@techcorp.com
- **Skills:**
  - Docker: 5
  - Kubernetes: 4
  - AWS: 5
  - CI/CD: 5
  - System Design: 4
  - Problem Solving: 5
  - Leadership: 3
  - Mentoring: 3
- **Potencial:** Tech Lead (75% match, necesita más soft skills)

**10. Camila Vargas**

- **Cargo Actual:** DevOps Engineer
- **Departamento:** Engineering
- **Hire Date:** 2022-11-01
- **Email:** camila.vargas@techcorp.com
- **Skills:**
  - Docker: 3
  - Kubernetes: 2
  - AWS: 3
  - CI/CD: 3
  - Problem Solving: 3
  - Collaboration: 4
- **Potencial:** Senior DevOps (en desarrollo)

**11. Andrés Muñoz**

- **Cargo Actual:** QA Engineer
- **Departamento:** Engineering
- **Hire Date:** 2022-04-15
- **Email:** andres.munoz@techcorp.com
- **Skills:**
  - Testing/QA: 4
  - JavaScript: 2
  - Problem Solving: 4
  - Critical Thinking: 4
  - Communication: 3
  - Collaboration: 4
- **Potencial:** Senior QA / Test Automation Engineer

**12. Daniela Castro**

- **Cargo Actual:** QA Engineer
- **Departamento:** Engineering
- **Hire Date:** 2023-01-10
- **Email:** daniela.castro@techcorp.com
- **Skills:**
  - Testing/QA: 3
  - Problem Solving: 3
  - Critical Thinking: 3
  - Communication: 3
  - Adaptability: 4
- **Potencial:** Mid QA Engineer

#### Product Team

**13. Patricia Herrera**

- **Cargo Actual:** Product Manager
- **Departamento:** Product
- **Hire Date:** 2020-08-01
- **Email:** patricia.herrera@techcorp.com
- **Skills:**
  - Product Strategy: 4
  - Data Analysis: 4
  - Agile/Scrum: 5
  - Stakeholder Management: 4
  - Customer Empathy: 5
  - Communication: 5
  - Leadership: 4
  - Problem Solving: 4
- **Potencial:** Senior Product Manager / Head of Product

**14. Ignacio Rojas**

- **Cargo Actual:** Product Designer
- **Departamento:** Product
- **Hire Date:** 2021-10-15
- **Email:** ignacio.rojas@techcorp.com
- **Skills:**
  - (Design skills no en catálogo para simplificar)
  - Customer Empathy: 4
  - Communication: 4
  - Collaboration: 5
  - Problem Solving: 3
  - Critical Thinking: 4
- **Potencial:** Senior Product Designer

**15. Francisca Núñez**

- **Cargo Actual:** Product Designer
- **Departamento:** Product
- **Hire Date:** 2022-09-01
- **Email:** francisca.nunez@techcorp.com
- **Skills:**
  - Customer Empathy: 3
  - Communication: 3
  - Collaboration: 4
  - Adaptability: 4
  - Critical Thinking: 3
- **Potencial:** Mid Product Designer

#### Operations Team

**16. Ricardo Soto (CEO)**

- **Cargo Actual:** CEO
- **Departamento:** Operations
- **Hire Date:** 2020-01-01
- **Email:** ricardo.soto@techcorp.com
- **Skills:**
  - Leadership: 5
  - Product Strategy: 4
  - Stakeholder Management: 5
  - Communication: 5
  - Problem Solving: 5
  - Emotional Intelligence: 5
  - Data Analysis: 3
- **Potencial:** N/A (fundador)

**17. Lorena Guzmán**

- **Cargo Actual:** HR Manager
- **Departamento:** Operations
- **Hire Date:** 2021-02-01
- **Email:** lorena.guzman@techcorp.com
- **Skills:**
  - Leadership: 3
  - Communication: 5
  - Emotional Intelligence: 5
  - Conflict Resolution: 4
  - Stakeholder Management: 3
  - Collaboration: 5
  - Data Analysis: 2
- **Potencial:** Head of Person

**18. Sebastián Parra**

- **Cargo Actual:** Sales Representative
- **Departamento:** Operations
- **Hire Date:** 2021-11-01
- **Email:** sebastian.parra@techcorp.com
- **Skills:**
  - Communication: 4
  - Stakeholder Management: 3
  - Customer Empathy: 4
  - Problem Solving: 3
  - Adaptability: 4
- **Potencial:** Senior Sales / Sales Manager

**19. Catalina Bravo**

- **Cargo Actual:** Sales Representative
- **Departamento:** Operations
- **Hire Date:** 2022-12-01
- **Email:** catalina.bravo@techcorp.com
- **Skills:**
  - Communication: 3
  - Customer Empathy: 3
  - Adaptability: 4
  - Collaboration: 3
- **Potencial:** Mid Sales

**20. Tomás Vega**

- **Cargo Actual:** Customer Success Manager
- **Departamento:** Operations
- **Hire Date:** 2022-05-15
- **Email:** tomas.vega@techcorp.com
- **Skills:**
  - Customer Empathy: 5
  - Communication: 4
  - Problem Solving: 4
  - Collaboration: 4
  - Emotional Intelligence: 4
  - Data Analysis: 2
- **Potencial:** Head of Customer Success

### 11.6 Vacantes Abiertas (3 vacantes)

#### Vacante 1: Senior Frontend Developer

- **Departamento:** Engineering
- **Rol:** Senior Frontend Developer
- **Estado:** Open
- **Publicada:** Hace 5 días
- **Deadline:** 30 días
- **Candidatos Internos Sugeridos:**
  1. Ana García (88% match) - **RECOMENDADA**
  2. Carlos López (65% match) - Necesita 6 meses de desarrollo
- **Decisión Esperada:** Promoción interna de Ana García

#### Vacante 2: Tech Lead

- **Departamento:** Engineering
- **Rol:** Tech Lead
- **Estado:** Open
- **Publicada:** Hace 10 días
- **Deadline:** 45 días
- **Candidatos Internos Sugeridos:**
  1. Roberto Morales (75% match) - Necesita desarrollo en soft skills
  2. Luis Martínez (68% match) - Necesita desarrollo en liderazgo y system design
- **Decisión Esperada:** Desarrollo de Roberto + búsqueda externa paralela

#### Vacante 3: Backend Developer (Mid)

- **Departamento:** Engineering
- **Rol:** Backend Developer
- **Estado:** Open
- **Publicada:** Hace 3 días
- **Deadline:** 30 días
- **Candidatos Internos Sugeridos:**
  1. Valentina Silva (80% match) - En desarrollo, lista en 2-3 meses
- **Decisión Esperada:** Esperar desarrollo de Valentina vs contratar externo

### 11.7 Rutas de Desarrollo Activas (5 rutas)

#### Ruta 1: Ana García → Senior Frontend Developer

- **Estado:** Active (65% completado)
- **Duración Estimada:** 4 meses (1 mes restante)
- **Pasos:**
  1. ✅ Curso: "Advanced React Patterns" (40h) - Completado
  2. ✅ Proyecto: Liderar refactor de componentes core - Completado
  3. 🔄 Mentoría: Mentorar a María Rodríguez (2 meses) - En progreso
  4. ⏳ Curso: "System Design Fundamentals" (30h) - Pendiente
  5. ⏳ Certificación: "AWS Solutions Architect Associate" - Pendiente

#### Ruta 2: Luis Martínez → Senior Backend Developer

- **Estado:** Active (50% completado)
- **Duración Estimada:** 6 meses (3 meses restantes)
- **Pasos:**
  1. ✅ Curso: "Advanced PostgreSQL" (25h) - Completado
  2. ✅ Curso: "Microservices Architecture" (35h) - Completado
  3. 🔄 Proyecto: Diseñar nueva API de notificaciones - En progreso
  4. ⏳ Mentoría: Mentorar a Valentina Silva (3 meses) - Pendiente
  5. ⏳ Curso: "Leadership for Engineers" (20h) - Pendiente

#### Ruta 3: Roberto Morales → Tech Lead

- **Estado:** Active (30% completado)
- **Duración Estimada:** 8 meses (5.5 meses restantes)
- **Pasos:**
  1. ✅ Curso: "Effective Communication for Tech Leaders" (15h) - Completado
  2. ✅ Curso: "Advanced System Design" (40h) - Completado
  3. 🔄 Mentoría: Recibir mentoría de CTO externo (6 meses) - En progreso
  4. ⏳ Proyecto: Liderar migración a Kubernetes - Pendiente
  5. ⏳ Curso: "Engineering Management" (25h) - Pendiente
  6. ⏳ Práctica: Facilitar reuniones de arquitectura (3 meses) - Pendiente

#### Ruta 4: Carlos López → Senior Frontend Developer

- **Estado:** Draft (pendiente aprobación)
- **Duración Estimada:** 10 meses
- **Pasos:**
  1. Curso: "TypeScript Advanced" (30h)
  2. Curso: "System Design for Frontend" (35h)
  3. Proyecto: Liderar implementación de nueva feature compleja
  4. Mentoría: Recibir mentoría de Ana García (4 meses)
  5. Mentoría: Mentorar a Diego Fernández (3 meses)
  6. Curso: "Leadership Essentials" (20h)

#### Ruta 5: Valentina Silva → Backend Developer (consolidación)

- **Estado:** Active (40% completado)
- **Duración Estimada:** 5 meses (3 meses restantes)
- **Pasos:**
  1. ✅ Curso: "Node.js Best Practices" (25h) - Completado
  2. ✅ Curso: "PostgreSQL Performance Tuning" (20h) - Completado
  3. 🔄 Proyecto: Implementar módulo de reportes - En progreso
  4. ⏳ Mentoría: Recibir mentoría de Luis Martínez (3 meses) - Pendiente
  5. ⏳ Curso: "API Design Patterns" (30h) - Pendiente

### 11.8 Postulaciones al Marketplace (4 postulaciones)

#### Postulación 1

- **Vacante:** Senior Frontend Developer
- **Candidato:** Ana García
- **Estado:** Pending
- **Fecha:** Hace 2 días
- **Mensaje:** "Estoy muy interesada en esta oportunidad. He estado preparándome durante los últimos 4 meses y siento que estoy lista para asumir más responsabilidades de liderazgo técnico."

#### Postulación 2

- **Vacante:** Tech Lead
- **Candidato:** Roberto Morales
- **Estado:** Under Review
- **Fecha:** Hace 7 días
- **Mensaje:** "Me gustaría postular a este rol. Tengo sólida experiencia técnica y estoy trabajando activamente en desarrollar mis habilidades de liderazgo."

#### Postulación 3

- **Vacante:** Tech Lead
- **Candidato:** Luis Martínez
- **Estado:** Under Review
- **Fecha:** Hace 6 días
- **Mensaje:** "Creo que puedo aportar mucho valor en este rol, combinando mi experiencia técnica con mi capacidad de mentoría."

#### Postulación 4

- **Vacante:** Backend Developer (Mid)
- **Candidato:** Valentina Silva
- **Estado:** Pending
- **Fecha:** Hace 1 día
- **Mensaje:** "Aunque sé que aún estoy en desarrollo, me siento preparada para asumir este desafío y seguir creciendo en el equipo."

### 11.9 Métricas del Dashboard (TechCorp)

#### KPIs Principales

- **Cobertura de Skills Críticas:** 78%

  - 14 de 18 skills críticas cubiertas al 80%+
  - Skills en riesgo: Kubernetes (60%), System Design (65%), Leadership (70%), Mentoring (68%)

- **Roles en Riesgo:** 2

  - Tech Lead: 0 personas listas (2 en desarrollo)
  - Senior Backend Developer: 1 persona casi lista (85% match)

- **Brechas Totales:** 127 niveles

  - Críticas (gap >2): 23 niveles
  - Moderadas (gap 1-2): 68 niveles
  - Menores (gap <1): 36 niveles

- **Talento Listo para Promoción:** 3 personas
  - Ana García → Senior Frontend (88% match)
  - Luis Martínez → Senior Backend (85% match)
  - Patricia Herrera → Head of Product (92% match)

#### Top 10 Skills con Mayor Brecha

1. **System Design:** 18 niveles de brecha (6 personas necesitan desarrollo)
2. **Leadership:** 15 niveles de brecha (8 personas)
3. **Kubernetes:** 14 niveles de brecha (5 personas)
4. **Mentoring:** 12 niveles de brecha (7 personas)
5. **TypeScript:** 11 niveles de brecha (6 personas)
6. **API Design:** 10 niveles de brecha (5 personas)
7. **Data Analysis:** 9 niveles de brecha (4 personas)
8. **AWS:** 8 niveles de brecha (3 personas)
9. **Product Strategy:** 7 niveles de brecha (2 personas)
10. **Stakeholder Management:** 6 niveles de brecha (4 personas)

#### Distribución de Talento por Nivel

- **Junior:** 4 personas (20%)
- **Mid:** 11 personas (55%)
- **Senior:** 3 personas (15%)
- **Lead/Principal:** 2 personas (10%)

#### Tiempo Promedio en Rol

- **<1 año:** 6 personas (30%)
- **1-2 años:** 8 personas (40%)
- **2-3 años:** 4 personas (20%)
- **3+ años:** 2 personas (10%)

---

## 12. Guion de Demo (Storytelling)

### 12.1 Contexto de la Demo (5 minutos)

**Narrativa:**
"Hoy les voy a mostrar TalentIA, nuestra plataforma de gestión estratégica de talento basada en skills. Vamos a usar el caso de TechCorp, una startup tecnológica de 20 personas que está creciendo rápidamente y necesita estructurar su talento.

El desafío de TechCorp es típico: tienen vacantes críticas abiertas (Tech Lead, Senior Frontend), no saben si tienen talento interno listo o deben contratar externamente, y necesitan planificar el desarrollo de su equipo de forma estratégica.

Con TalentIA, vamos a resolver estos tres problemas en menos de 15 minutos."

### 12.2 Flujo de Demo Paso a Paso

#### Paso 1: Dashboard Ejecutivo (3 minutos)

**Pantalla:** `/dashboard`

**Narrativa:**
"Empezamos en el dashboard ejecutivo. Aquí la CHRO de TechCorp ve de un vistazo la salud de su talento:

- **Cobertura de skills críticas: 78%** - Bueno, pero hay margen de mejora
- **2 roles en riesgo** - Tech Lead y Senior Backend sin cobertura completa
- **127 niveles de brecha total** - Necesitamos un plan de desarrollo
- **3 personas listas para promoción** - ¡Buenas noticias! Tenemos talento interno

Bajamos y vemos el gráfico de skills con mayor brecha. System Design lidera con 18 niveles - esto es crítico porque necesitamos un Tech Lead.

En la tabla de roles en riesgo, vemos que Tech Lead tiene solo 45% de cobertura. Esto es una alerta temprana para actuar."

**Acciones:**

- Hover sobre KPIs para mostrar tooltips
- Scroll al gráfico de brechas
- Click en "Tech Lead" en tabla de roles en riesgo

#### Paso 2: Perfil de Talento (3 minutos)

**Pantalla:** `/Person/1` (Ana García)

**Narrativa:**
"Vamos al perfil de Ana García, una de nuestras Frontend Developers. Aquí vemos:

- Sus skills actuales con niveles (React: 4/5, TypeScript: 3/5, etc.)
- Un radar chart que visualiza sus fortalezas
- Roles sugeridos automáticamente por el sistema

El sistema detecta que Ana tiene 88% de match con Senior Frontend Developer. Esto es muy alto - veamos qué le falta."

**Acciones:**

- Scroll por las skills
- Hover sobre radar chart
- Click en "Senior Frontend Developer (88%)"

#### Paso 3: Análisis de Brecha (3 minutos)

**Pantalla:** `/gap-analysis` (Ana → Senior Frontend)

**Narrativa:**
"Aquí está el análisis de brecha detallado. Ana tiene:

- **Match del 88%** - Excelente
- **Skills OK (verde):** React, JavaScript, Communication, Problem Solving
- **Skills en desarrollo (amarillo):** TypeScript (necesita 1 nivel más)
- **Skills críticas (rojo):** System Design (gap de 2 niveles), Mentoring (gap de 1 nivel)

Con esta información clara, podemos tomar una decisión: ¿invertimos en desarrollar a Ana o buscamos externamente?

La respuesta es obvia - Ana está casi lista. Generemos su ruta de desarrollo."

**Acciones:**

- Scroll por tabla de brechas
- Destacar skills en rojo
- Click en "Generar Ruta de Desarrollo"

#### Paso 4: Ruta de Desarrollo (2 minutos)

**Pantalla:** `/development-paths/1` (Ruta de Ana)

**Narrativa:**
"El sistema generó automáticamente una ruta de 4 meses para Ana:

1. ✅ Curso de React Avanzado - Ya completado
2. ✅ Proyecto de refactor - Ya completado
3. 🔄 Mentoría a junior - En progreso (desarrolla su skill de Mentoring)
4. ⏳ Curso de System Design - Pendiente
5. ⏳ Certificación AWS - Pendiente

Ana ya va al 65% de progreso. En 1 mes más, estará lista para la promoción. Esto nos ahorra tiempo y costo de reclutamiento externo."

**Acciones:**

- Scroll por timeline
- Hover sobre pasos completados
- Mostrar progress bar

#### Paso 5: Selección por Skills (3 minutos)

**Pantalla:** `/job-openings/1` (Vacante Senior Frontend)

**Narrativa:**
"Ahora veamos la vacante de Senior Frontend Developer. El sistema ya hizo el trabajo pesado:

- Analizó a los 20 empleados
- Identificó candidatos con match >60%
- Los rankeó automáticamente

Ana García aparece primera con 88% de match. Carlos López segundo con 65%, pero necesita 6 meses más de desarrollo.

Vamos a la pestaña de comparación interno vs externo."

**Acciones:**

- Mostrar tabla de candidatos
- Click en "Comparar Candidatos"

**Pantalla:** `/job-openings/1/compare`

**Narrativa:**
"Aquí comparamos a Ana (interna) vs un candidato externo hipotético:

| Métrica              | Ana (Interna)            | Candidato Externo |
| -------------------- | ------------------------ | ----------------- |
| Match                | 88%                      | 95%               |
| Time to Productivity | 1 mes                    | 3-4 meses         |
| Costo                | $0 reclutamiento         | $5K-8K            |
| Riesgo Cultural      | Bajo                     | Medio             |
| **Recomendación**    | ✅ **Promoción interna** | -                 |

Aunque el externo tiene 7% más de match, Ana tiene ventajas claras: conoce el producto, la cultura, y estará productiva en 1 mes vs 3-4 meses.

La decisión es clara: promoción interna."

#### Paso 6: Marketplace Interno (2 minutos)

**Pantalla:** `/marketplace` (vista de empleado)

**Narrativa:**
"Ahora cambiamos de perspectiva. Esto es lo que ve un empleado en el Marketplace Interno:

- Vacantes abiertas con su % de match personal
- Senior Frontend: 88% match (Ana vería esto)
- Tech Lead: 75% match (Roberto vería esto)
- Backend Mid: 80% match (Valentina vería esto)

Los empleados pueden postular directamente. Esto fomenta la movilidad interna y la transparencia.

Ana ya postuló hace 2 días con un mensaje de interés. El manager puede revisar y aprobar."

**Acciones:**

- Scroll por vacantes
- Click en card de vacante
- Mostrar postulación de Ana

#### Paso 7: Cierre y Valor (1 minuto)

**Narrativa:**
"En resumen, con TalentIA, TechCorp logró en 15 minutos:

1. ✅ **Visibilidad estratégica:** Dashboard con métricas clave de talento
2. ✅ **Decisión basada en datos:** Ana lista para promoción (ahorro de $5K-8K en reclutamiento)
3. ✅ **Plan de desarrollo claro:** Rutas automáticas para cerrar brechas
4. ✅ **Movilidad interna:** Marketplace transparente para empleados

Esto es solo el MVP. En nuestro roadmap tenemos:

- IA real para inferir skills desde CVs
- Integraciones con ATS y HRIS
- Credenciales verificables (blockchain) para portabilidad de skills

¿Preguntas?"

### 12.3 Preguntas Frecuentes en Demos

#### P: "¿Cómo se cargan las skills inicialmente?"

**R:** "Tres formas: 1) Importación desde HRIS/ATS existente, 2) Autoevaluación de empleados con validación de managers, 3) Inferencia con IA desde CVs y perfiles de LinkedIn (roadmap). En el onboarding, nuestros consultores ayudan a definir el catálogo de skills por industria."

#### P: "¿Qué pasa si un empleado infla sus niveles de skills?"

**R:** "Buena pregunta. Implementamos validación por managers y evaluaciones periódicas. En roadmap: evaluaciones 360° y badges verificables con SSI (blockchain) para skills críticas."

#### P: "¿Funciona para empresas grandes (1000+ empleados)?"

**R:** "Sí, la arquitectura es multi-tenant y escalable. Para cálculos pesados (matching de 1000+ personas), usamos jobs asíncronos. Tenemos clientes piloto de 500 empleados con excelente performance."

#### P: "¿Se integra con nuestro ATS/HRIS actual?"

**R:** "En roadmap para Q2. Actualmente soportamos importación CSV. Planeamos integraciones nativas con Greenhouse, Lever, BambooHR, Workday."

#### P: "¿Cuánto cuesta?"

**R:** "Modelo híbrido: SaaS ($X/empleado/mes) + consultoría inicial (setup de modelo de skills, $Y). ROI típico: 6-12 meses por ahorro en reclutamiento externo y reducción de time-to-fill."

#### P: "¿Qué tan precisa es la IA?"

**R:** "En MVP, usamos lógica de reglas (precisión ~85%). En producción, usamos GPT-4 para inferencia de skills con precisión >90%. Siempre con validación humana final."

---

## 13. Estructura de Carpetas del Proyecto

### 13.1 Backend (Laravel)
```

talentia-api/
├── app/
│ ├── Console/
│ ├── Exceptions/
│ ├── Http/
│ │ ├── Controllers/
│ │ │ ├── Api/
│ │ │ │ ├── DashboardController.php
│ │ │ │ ├── PersonController.php
│ │ │ │ ├── SkillsController.php
│ │ │ │ ├── RolesController.php
│ │ │ │ ├── GapAnalysisController.php
│ │ │ │ ├── DevelopmentPathsController.php
│ │ │ │ ├── JobOpeningsController.php
│ │ │ │ ├── ApplicationsController.php
│ │ │ │ └── MarketplaceController.php
│ │ │ └── Controller.php
│ │ ├── Middleware/
│ │ │ ├── EnsureTenantContext.php
│ │ │ └── CheckRole.php (roadmap)
│ │ ├── Requests/
│ │ │ ├── StorePersonRequest.php
│ │ │ ├── UpdatePersonSkillRequest.php
│ │ │ ├── GapAnalysisRequest.php
│ │ │ └── ...
│ │ └── Resources/
│ │ ├── PersonResource.php
│ │ ├── SkillResource.php
│ │ ├── RoleResource.php
│ │ └── ...
│ ├── Models/
│ │ ├── Organization.php
│ │ ├── User.php
│ │ ├── Skill.php
│ │ ├── Role.php
│ │ ├── Person.php
│ │ ├── PersonSkill.php (pivot model)
│ │ ├── RoleSkill.php (pivot model)
│ │ ├── DevelopmentPath.php
│ │ ├── JobOpening.php
│ │ ├── Application.php
│ │ └── Traits/
│ │ └── BelongsToOrganization.php
│ ├── Services/
│ │ ├── GapAnalysisService.php
│ │ ├── DevelopmentPathService.php
│ │ ├── MatchingService.php
│ │ ├── DashboardService.php
│ │ └── SkillInferenceService.php (roadmap)
│ └── Providers/
├── bootstrap/
├── config/
├── database/
│ ├── factories/
│ ├── migrations/
│ │ ├── 2024_01_01_000001_create_organizations_table.php
│ │ ├── 2024_01_01_000002_create_users_table.php
│ │ ├── 2024_01_01_000003_create_skills_table.php
│ │ ├── 2024_01_01_000004_create_roles_table.php
│ │ ├── 2024_01_01_000005_create_role_skills_table.php
│ │ ├── 2024_01_01_000006_create_Person_table.php
│ │ ├── 2024_01_01_000007_create_person_skills_table.php
│ │ ├── 2024_01_01_000008_create_development_paths_table.php
│ │ ├── 2024_01_01_000009_create_job_openings_table.php
│ │ └── 2024_01_01_000010_create_applications_table.php
│ └── seeders/
│ ├── DatabaseSeeder.php
│ ├── DemoSeeder.php (TechCorp data)
│ ├── OrganizationSeeder.php
│ ├── SkillSeeder.php
│ ├── RoleSeeder.php
│ └── PersonSeeder.php
├── public/
├── resources/
├── routes/
│ ├── api.php
│ ├── web.php
│ └── console.php
├── storage/
├── tests/
│ ├── Feature/
│ │ ├── GapAnalysisTest.php
│ │ ├── DevelopmentPathTest.php
│ │ └── MatchingTest.php
│ └── Unit/
├── .env.example
├── composer.json
├── docker-compose.yml
└── README.md

```

### 13.2 Frontend (Vue 3)

```

talentia-frontend/
├── public/
│ ├── favicon.ico
│ └── index.html
├── src/
│ ├── assets/
│ │ ├── images/
│ │ ├── styles/
│ │ │ ├── variables.scss
│ │ │ └── global.scss
│ │ └── logo.svg
│ ├── components/
│ │ ├── atoms/
│ │ │ ├── SkillBadge.vue
│ │ │ ├── MatchPercentage.vue
│ │ │ ├── LevelIndicator.vue
│ │ │ ├── StatusChip.vue
│ │ │ └── AvatarWithName.vue
│ │ ├── molecules/
│ │ │ ├── SkillCard.vue
│ │ │ ├── RoleCard.vue
│ │ │ ├── GapListItem.vue
│ │ │ └── CandidateCard.vue
│ │ ├── organisms/
│ │ │ ├── SkillsRadarChart.vue
│ │ │ ├── GapAnalysisTable.vue
│ │ │ ├── DevelopmentPathTimeline.vue
│ │ │ ├── DashboardMetricsGrid.vue
│ │ │ ├── NavigationDrawer.vue
│ │ │ └── AppBar.vue
│ │ └── layouts/
│ │ ├── DefaultLayout.vue
│ │ └── DashboardLayout.vue
│ ├── composables/
│ │ ├── useGapAnalysis.ts
│ │ ├── useMatching.ts
│ │ ├── useDashboard.ts
│ │ └── useApi.ts
│ ├── plugins/
│ │ ├── vuetify.ts
│ │ └── axios.ts
│ ├── router/
│ │ └── index.ts
│ ├── stores/
│ │ ├── auth.ts (roadmap)
│ │ ├── organization.ts
│ │ ├── Person.ts
│ │ └── skills.ts
│ ├── types/
│ │ ├── models.ts
│ │ ├── api.ts
│ │ └── enums.ts
│ ├── utils/
│ │ ├── formatters.ts
│ │ ├── validators.ts
│ │ └── constants.ts
│ ├── views/
│ │ ├── Dashboard.vue
│ │ ├── Person/
│ │ │ ├── PersonList.vue
│ │ │ └── PersonProfile.vue
│ │ ├── Skills/
│ │ │ └── SkillsCatalog.vue
│ │ ├── Roles/
│ │ │ ├── RolesList.vue
│ │ │ └── RoleDetail.vue
│ │ ├── GapAnalysis/
│ │ │ └── GapAnalysis.vue
│ │ ├── DevelopmentPaths/
│ │ │ ├── PathsList.vue
│ │ │ └── PathDetail.vue
│ │ ├── JobOpenings/
│ │ │ ├── OpeningsList.vue
│ │ │ ├── OpeningDetail.vue
│ │ │ └── CompareCandidate.vue
│ │ ├── Marketplace/
│ │ │ └── Marketplace.vue
│ │ └── NotFound.vue
│ ├── App.vue
│ └── main.ts
├── .env.example
├── .eslintrc.js
├── .prettierrc
├── index.html
├── package.json
├── tsconfig.json
├── vite.config.ts
└── README.md

````

---

## 14. Comandos Útiles

### 14.1 Backend (Laravel)

```bash
# Instalación inicial
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=DemoSeeder

# Desarrollo
php artisan serve
php artisan migrate:fresh --seed  # Reset DB
php artisan tinker  # REPL

# Testing
php artisan test
php artisan test --filter GapAnalysisTest

# Optimización
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Limpiar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
````

### 14.2 Frontend (Vue 3)

```bash
# Instalación inicial
npm install
cp .env.example .env

# Desarrollo
npm run dev

# Build
npm run build
npm run preview  # Preview build

# Linting
npm run lint
npm run lint:fix

# Testing
npm run test
npm run test:coverage

# Type checking
npm run type-check
```

### 14.3 Docker

```bash
# Levantar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down

# Rebuild
docker-compose up -d --build

# Ejecutar comandos en contenedor
docker-compose exec app php artisan migrate
```

---

## 16. Algoritmos Clave

### 16.1 Algoritmo de Cálculo de Brechas (Gap Analysis)

#### Objetivo

Calcular el % de match entre las skills actuales de una persona y las skills requeridas por un rol objetivo.

#### Pseudocódigo

```
FUNCIÓN calculateGap(person, role):
    gaps = []
    totalSkills = COUNT(role.required_skills)
    skillsOk = 0
    totalGap = 0

    PARA CADA requiredSkill EN role.required_skills:
        personSkill = BUSCAR(person.skills, skill_id = requiredSkill.id)

        SI personSkill EXISTE:
            currentLevel = personSkill.level
        SINO:
            currentLevel = 0
        FIN SI

        requiredLevel = requiredSkill.pivot.required_level
        gap = MAX(0, requiredLevel - currentLevel)
        totalGap += gap

        SI gap == 0:
            status = "ok"        // Verde
            skillsOk += 1
        SINO SI gap <= 1:
            status = "developing" // Amarillo
        SINO:
            status = "critical"   // Rojo
        FIN SI

        AGREGAR A gaps: {
            skill_id,
            skill_name,
            current_level,
            required_level,
            gap,
            status,
            is_critical = requiredSkill.pivot.is_critical
        }
    FIN PARA

    matchPercentage = (skillsOk / totalSkills) * 100

    RETORNAR {
        match_percentage: matchPercentage,
        total_gap: totalGap,
        gaps: gaps (ordenado por gap DESC, is_critical DESC)
    }
FIN FUNCIÓN
```

#### Criterios de Estado

- **OK (Verde):** `gap = 0` - Persona cumple o supera el nivel requerido
- **En Desarrollo (Amarillo):** `gap = 1` - Falta 1 nivel, brecha menor
- **Crítico (Rojo):** `gap >= 2` - Brecha significativa, requiere intervención

#### Ponderación (Roadmap)

En versiones futuras, considerar:

- **Skills críticas:** Multiplicar gap por factor 2x
- **Niveles altos:** Gap en niveles 4-5 pesa más que en 1-2
- **Antigüedad de evaluación:** Penalizar skills no evaluadas recientemente

---

### 16.2 Algoritmo de Generación de Rutas de Desarrollo

#### Objetivo

Proponer una secuencia ordenada de acciones (cursos, proyectos, mentorías) para cerrar brechas entre perfil actual y rol objetivo.

#### Pseudocódigo

```
FUNCIÓN generateDevelopmentPath(person, role):
    gaps = calculateGap(person, role)
    steps = []
    totalDuration = 0

    // 1. Filtrar skills con brecha > 0
    skillsToImprove = FILTRAR(gaps, gap > 0)

    // 2. Priorizar por criticidad e impacto
    skillsToImprove = ORDENAR(skillsToImprove, POR is_critical DESC, gap DESC)

    // 3. Para cada skill, buscar recursos de aprendizaje
    PARA CADA skill EN skillsToImprove:
        gapLevels = skill.gap

        // Buscar cursos/recursos que cubran esta skill
        resources = BUSCAR_RECURSOS(skill_id, nivel_desde = skill.current_level, nivel_hasta = skill.required_level)

        SI resources NO VACÍO:
            // Tomar el mejor recurso (por rating/relevancia)
            bestResource = resources[0]

            AGREGAR A steps: {
                skill_id: skill.skill_id,
                skill_name: skill.skill_name,
                action_type: "course",  // o "project", "mentoring"
                resource_name: bestResource.name,
                resource_url: bestResource.url,
                duration_hours: bestResource.duration,
                from_level: skill.current_level,
                to_level: skill.required_level,
                completed: false
            }

            totalDuration += bestResource.duration
        FIN SI
    FIN PARA

    // 4. Estimar duración en meses (asumiendo 10h/semana dedicación)
    hoursPerWeek = 10
    weeksPerMonth = 4
    estimatedMonths = ROUND_UP(totalDuration / (hoursPerWeek * weeksPerMonth))

    // 5. Crear registro de ruta
    path = CREAR_DEVELOPMENT_PATH({
        person_id,
        target_role_id,
        status: "draft",
        estimated_duration_months: estimatedMonths,
        steps: steps (JSON),
        created_at: NOW()
    })

    RETORNAR path
FIN FUNCIÓN
```

#### Criterios de Priorización

1. **Skills críticas primero:** `is_critical = true` al tope
2. **Mayor brecha primero:** Skills con gap >= 2 antes que gap = 1
3. **Dependencias:** Skills prerequisito antes que avanzadas (roadmap)

#### Tipos de Acciones

- **course:** Curso online (Udemy, Coursera, Platzi)
- **project:** Proyecto interno para practicar
- **mentoring:** Sesiones con experto interno
- **certification:** Certificación oficial (roadmap)

---

### 16.3 Algoritmo de Matching para Marketplace Interno

#### Objetivo

Rankear candidatos internos para una vacante basándose en % match y otros factores.

#### Pseudocódigo

```
FUNCIÓN matchCandidatesForJobOpening(jobOpening):
    role = jobOpening.role
    allPerson = OBTENER_PERSONAS(organization_id = jobOpening.organization_id)
    candidates = []

    PARA CADA person EN allPerson:
        // Excluir personas que ya están en ese rol
        SI person.current_role_id == role.id:
            CONTINUAR
        FIN SI

        // Calcular match
        gapResult = calculateGap(person, role)
        matchPercentage = gapResult.match_percentage

        // Filtrar solo candidatos con match mínimo (ej: >50%)
        SI matchPercentage < 50:
            CONTINUAR
        FIN SI

        // Calcular factores adicionales (roadmap)
        timeInCurrentRole = MESES_DESDE(person.hire_date, HOY())
        readinessScore = matchPercentage + (MIN(timeInCurrentRole, 24) / 24 * 10)

        AGREGAR A candidates: {
            person_id: person.id,
            name: person.full_name,
            current_role: person.current_role.name,
            match_percentage: matchPercentage,
            missing_skills: FILTRAR(gapResult.gaps, gap > 0),
            readiness_score: readinessScore,
            time_in_role_months: timeInCurrentRole
        }
    FIN PARA

    // Ordenar por match descendente
    candidates = ORDENAR(candidates, POR match_percentage DESC)

    RETORNAR candidates[0..10]  // Top 10 candidatos
FIN FUNCIÓN
```

#### Factores de Ranking (Futuro)

- **Match de skills:** Base (peso 60%)
- **Tiempo en rol actual:** Estabilidad (peso 20%)
- **Desempeño histórico:** Evaluaciones previas (peso 10%)
- **Interés expresado:** Postulación activa vs pasiva (peso 10%)

---

### 16.4 Algoritmo de Comparación Interno vs Externo (Simplificado en MVP)

#### Objetivo

Ayudar a recruiters a decidir entre candidato interno y externo basándose en múltiples criterios.

#### Pseudocódigo (MVP Simplificado)

```
FUNCIÓN compareInternalVsExternal(internalPersonId, externalCvText, roleId):
    role = OBTENER_ROL(roleId)
    internalPerson = OBTENER_PERSONA(internalPersonId)

    // 1. Match de candidato interno
    internalGap = calculateGap(internalPerson, role)
    internalMatch = internalGap.match_percentage

    // 2. Extraer skills de CV externo (SIMULADO en MVP, IA en post-MVP)
    externalSkills = PARSE_CV_SIMPLE(externalCvText)
    // En MVP: buscar keywords de skills conocidas en texto
    // Post-MVP: usar GPT-4 para parsing estructurado

    // 3. Calcular match de externo
    externalMatch = CALCULAR_MATCH_EXTERNO(externalSkills, role.required_skills)

    // 4. Factores de decisión
    comparison = {
        internal: {
            match: internalMatch,
            time_to_productivity: "Inmediato (conoce la empresa)",
            cost: "Bajo (sin onboarding extenso)",
            risk: "Bajo (desempeño conocido)",
            recommendation_score: internalMatch * 1.2  // Bonus por ser interno
        },
        external: {
            match: externalMatch,
            time_to_productivity: "3-6 meses (onboarding completo)",
            cost: "Alto (reclutamiento + onboarding)",
            risk: "Medio (desempeño incierto)",
            recommendation_score: externalMatch
        }
    }

    // 5. Recomendación
    SI comparison.internal.recommendation_score > comparison.external.recommendation_score:
        recommendation = "Candidato interno preferido"
    SINO SI externalMatch - internalMatch > 20:
        recommendation = "Buscar externo (brecha significativa)"
    SINO:
        recommendation = "Invertir en desarrollo interno antes de buscar externo"
    FIN SI

    comparison.recommendation = recommendation

    RETORNAR comparison
FIN FUNCIÓN
```

#### Criterios de Decisión

- **Match similar (±10%):** Preferir interno
- **Externo > +20% match:** Considerar externo si rol es crítico
- **Interno 60-80% match:** Evaluar inversión en desarrollo vs búsqueda externa

---

**Fin de Algoritmos Clave**

---

## 15. Glosario de Términos

- **Skill (Competencia):** Capacidad o conocimiento específico que una persona posee (ej: React, Leadership)
- **Nivel de Dominio:** Escala 1-5 que indica el grado de maestría en una skill
- **Rol (Perfil de Cargo):** Conjunto de skills requeridas para un puesto (ej: Senior Frontend Developer)
- **Brecha (Gap):** Diferencia entre el nivel actual de una skill y el nivel requerido por un rol
- **Match Percentage:** Porcentaje de alineación entre las skills de una persona y las requeridas por un rol
- **Ruta de Desarrollo:** Plan estructurado de acciones (cursos, proyectos, mentorías) para cerrar brechas
- **Marketplace Interno:** Plataforma donde empleados descubren y postulan a oportunidades internas
- **Multi-Tenant:** Arquitectura donde múltiples organizaciones comparten la misma instancia de software con aislamiento de datos
- **SSI (Self-Sovereign Identity):** Identidad digital descentralizada donde el usuario controla sus credenciales
- **Credencial Verificable:** Certificado digital criptográficamente seguro que prueba una skill o logro

---

**Fin del Documento**

---

**Notas Finales para GitHub Copilot:**

Este documento es la fuente de verdad para el desarrollo de TalentIA. Cuando generes código, asegúrate de:

1. **Respetar la arquitectura multi-tenant:** Siempre filtrar por `organization_id`
2. **Seguir las convenciones de nombres:** Modelos en singular, tablas en plural, snake_case en DB, camelCase en JS
3. **Validar en backend:** Nunca confiar solo en validación de frontend
4. **Usar TypeScript estricto:** Definir interfaces para todos los modelos
5. **Comentar lógica compleja:** Especialmente algoritmos de matching y cálculo de brechas (ver sección 16)
6. **Pensar en escalabilidad:** Queries optimizadas, índices, eager loading
7. **Mantener consistencia visual:** Seguir sistema de diseño de Vuetify
8. **Priorizar MVP:** No agregar features fuera del alcance definido sin consultar
9. **Revisar endpoints MVP:** Usar leyenda ✅/🔴/🟡 en sección 6.2 para saber qué implementar
10. **Consultar datos de demo:** Sección 2.4 (resumen) y 11 (detalle completo) para seeds

**Última actualización:** 2025-12-27  
**Versión:** 1.1 (mejoras: índice navegable, leyenda MVP en endpoints, algoritmos documentados, resumen de datos demo)  
**Autor:** Equipo TalentIA

```

```

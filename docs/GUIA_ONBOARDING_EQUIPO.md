# Guía de Onboarding — Equipo Stratos

Bienvenido/a al equipo. Este documento te orienta para estar operativo/a desde el primer día.

> Para más contexto de producto lee [`docs/INDEX.md`](INDEX.md) y [`README.md`](../README.md).

---

## Stack tecnológico

| Capa           | Tecnología                      |
| -------------- | ------------------------------- |
| Backend        | Laravel 12, PHP 8.3             |
| Frontend       | Vue 3 + TypeScript + Inertia v2 |
| UI             | Vuetify 3 (sistema Glass)       |
| Estado         | Pinia                           |
| Base de datos  | PostgreSQL                      |
| Autenticación  | Laravel Sanctum                 |
| Build          | Vite                            |
| Rutas tipadas  | Wayfinder                       |
| Tests backend  | Pest v4 / PHPUnit               |
| Tests frontend | Vitest + Playwright (E2E)       |

El proyecto es un **SaaS multi-tenant**: cada organización cliente tiene sus datos completamente aislados mediante `organization_id`.

---

## Configurar el entorno de desarrollo

### Prerrequisitos

- PHP 8.3+ con extensiones: `pdo_pgsql`, `redis`, `gd`
- Node.js 20+
- PostgreSQL 15+
- Composer 2
- (Opcional) Redis para colas

### Pasos

```bash
# 1. Clonar el repositorio
git clone git@github.com:<org>/Stratos.git
cd Stratos/src

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Crear base de datos y ejecutar migraciones + seeders
php artisan migrate --seed

# 5. Levantar el stack de desarrollo (servidor + queue + logs + Vite)
composer run dev
```

El comando `composer run dev` levanta concurrentemente:

- PHP dev server en `http://localhost:8000`
- Queue worker
- Log watcher
- Vite HMR

---

## Conceptos clave

### Multi-tenancy y `organization_id`

**Regla de oro:** toda consulta a la base de datos debe estar filtrada por `organization_id`. Nunca devuelvas datos de otra organización.

```php
// ✅ Correcto
$people = People::where('organization_id', $orgId)->get();

// ❌ Incorrecto — expone datos cross-tenant
$people = People::all();
```

El `organization_id` del usuario autenticado se obtiene con:

```php
$orgId = auth()->user()->organization_id;
// o en requests
$orgId = $request->user()->organization_id;
```

### Autenticación con Sanctum

Las rutas de API están protegidas con el guard `auth:sanctum`:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    // rutas protegidas
});
```

Las rutas multi-tenant además usan el middleware `tenant` (ver `bootstrap/app.php`).

El token se envía como header:

```
Authorization: Bearer <token>
```

Para más detalle consulta [`AUTH_SANCTUM_COMPLETA.md`](AUTH_SANCTUM_COMPLETA.md).

### Lógica de negocio en Services

Los controladores deben ser delgados. La lógica va en `app/Services/`:

```php
// Controlador
public function heatmap(Request $request): JsonResponse
{
    return response()->json(
        $this->service->departmentHeatmap($orgId, $category)
    );
}
```

---

## Mapa de módulos

| Módulo                 | Descripción                     | Páginas Vue                | Controlador API               |
| ---------------------- | ------------------------------- | -------------------------- | ----------------------------- |
| **People**             | Directorio y perfiles           | `Pages/People/`            | `PeopleController`            |
| **Skill Intelligence** | Heatmaps y brechas              | `Pages/SkillIntelligence/` | `SkillIntelligenceController` |
| **Performance**        | Ciclos y evaluaciones 360       | `Pages/Performance/`       | `PerformanceController`       |
| **Org Chart**          | Árbol jerárquico org            | `Pages/OrgChart/`          | `OrgPeopleChartController`    |
| **Workforce Planning** | Planificación de fuerza laboral | `Pages/WorkforcePlanning/` | (varios)                      |
| **Talent Pass**        | Credenciales verificables       | `Pages/TalentPass/`        | `TalentPassController`        |
| **Gamification**       | Quests y recompensas            | integrado                  | `GamificationController`      |

El código de la aplicación vive en `src/`:

```
src/
├── app/                 # Laravel (Controllers, Models, Services, Policies)
├── resources/
│   └── js/
│       ├── Pages/       # Páginas Inertia (Vue)
│       ├── components/  # Componentes reutilizables
│       ├── composables/ # Composables Vue
│       └── stores/      # Pinia stores
├── routes/
│   ├── api.php          # Rutas API
│   └── web.php          # Rutas web (Inertia)
└── tests/               # Tests Pest
```

Los tipos TypeScript del dominio están en `src/types/` (ej. `workforcePlanning.ts`).

---

## Ejecutar tests

### Backend (Pest)

```bash
cd src

# Todos los tests
composer test
# o
php artisan test

# Suite específica
php artisan test tests/Feature/SkillIntelligenceTest.php

# Con cobertura
php artisan test --coverage
```

### Frontend (Vitest)

```bash
cd src
npm run test          # modo watch
npm run test:run      # una sola pasada
```

### E2E (Playwright)

```bash
cd src
npx playwright test
```

---

## Hacer un release

El versionado sigue **Semantic Versioning** y se gestiona con `standard-version`.

```bash
# Desde la raíz del repositorio

# Patch (bug fixes): 0.12.0 → 0.12.1
./scripts/release.sh patch --yes

# Minor (nuevas features): 0.12.0 → 0.13.0
./scripts/release.sh minor --yes

# Major (breaking changes): 0.12.0 → 1.0.0
./scripts/release.sh major --yes

# Simular sin aplicar cambios
npx standard-version --dry-run
```

El script actualiza `CHANGELOG.md`, hace commit y crea el tag de Git automáticamente.

Para más detalle: [`GUIA_VERSIONADO_CHANGELOG.md`](GUIA_VERSIONADO_CHANGELOG.md) y [`NORMA_VERSIONADO_RELEASES_STRATOS.md`](NORMA_VERSIONADO_RELEASES_STRATOS.md).

---

## Git workflow y commits semánticos

Usamos **Conventional Commits** con validación vía `commitlint`.

```bash
# Usar el script interactivo (recomendado)
./scripts/commit.sh

# Formato manual
git commit -m "feat(performance): add cycle calibration endpoint"
git commit -m "fix(org-chart): correct depth calculation for root nodes"
git commit -m "docs: update API reference for v0.12.0"
```

**Tipos de commit:**

- `feat` — nueva funcionalidad
- `fix` — corrección de bug
- `docs` — sólo documentación
- `refactor` — refactorización sin cambio de comportamiento
- `test` — añadir o corregir tests
- `chore` — tareas de mantenimiento

Para más guías: [`GUIA_COMMITS_SEMANTICOS.md`](GUIA_COMMITS_SEMANTICOS.md).

---

## Dónde encontrar documentación

| Recurso                                                                                                 | Descripción                              |
| ------------------------------------------------------------------------------------------------------- | ---------------------------------------- |
| [`docs/INDEX.md`](INDEX.md)                                                                             | Índice completo de toda la documentación |
| [`docs/QUICK_START.md`](QUICK_START.md)                                                                 | Setup en 5 minutos                       |
| [`docs/dia5_api_endpoints.md`](dia5_api_endpoints.md)                                                   | Endpoints del MVP original               |
| [`docs/API_NUEVOS_MODULOS.md`](API_NUEVOS_MODULOS.md)                                                   | Endpoints de los módulos v0.12.0         |
| [`docs/AUTH_SANCTUM_COMPLETA.md`](AUTH_SANCTUM_COMPLETA.md)                                             | Autenticación Sanctum                    |
| [`docs/DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md`](DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md) | Arquitectura completa                    |
| [`docs_wiki/`](../docs_wiki/)                                                                           | Wiki MkDocs (documentación extendida)    |

---

## Do's y Don'ts

### ✅ Hacer

- Siempre filtrar queries por `organization_id`
- Usar Form Requests para validación de entrada
- Poner lógica de negocio en `app/Services/`
- Usar políticas (`app/Policies/`) para autorización
- Eager-load relaciones para evitar N+1
- Usar rutas nombradas y recursos REST
- Escribir tests para nuevos endpoints
- Hacer commits semánticos

### ❌ No hacer

- **Nunca usar `env()` fuera de archivos de configuración** — usar `config('...')` en su lugar
- Nunca devolver datos de otra organización (siempre verificar org scope)
- No poner lógica de negocio en controladores
- No registrar middleware en `app/Http/Middleware/` (usar `bootstrap/app.php`)
- No usar `Model::all()` sin scope de tenant
- No hacer consultas dentro de bucles (N+1)

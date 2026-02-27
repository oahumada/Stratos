# 🛡️ Sistema RBAC de Stratos (Role-Based Access Control)

El sistema de control de acceso en Stratos se implementó como parte de la _Wave 2_ (Febrero 2026) para gestionar autorizaciones granulares a lo largo de la plataforma. Está diseñado para ser escalable, seguro (vía middlewares de Laravel) y reactivo en el Frontend (vía composables de Vue + Inertia shared props).

**Estado: ✅ Implementado y Operativo (27-Feb-2026)**

---

## 1. Conceptos Core

En Stratos, se hace una clara distinción entre **Roles de Sistema** y **Permisos**:

- **Roles de Sistema:** Definen el "qué es" el usuario dentro de la plataforma (ej. Administrador, Colaborador). Cada usuario tiene exactamente _un_ rol de sistema activo (almacenado en `users.role`).
    - _No confundir con los Roles de Negocio_ (ej. "Desarrollador Backend", "Gerente de Finanzas"), los cuales pertenecen al Grafo de Talento y Módulo de Competencias.
- **Permisos:** Definen acciones granulares ("qué puede hacer"). Usan notación de punto `modulo.accion` (ej. `scenarios.create`).

### Matriz de Roles de Sistema

| Rol de Sistema     | Identificador  | Nivel | Permisos | Propósito                                                                     |
| :----------------- | :------------- | :---: | :------: | :---------------------------------------------------------------------------- |
| **Administrador**  | `admin`        |  L0   |    18    | Full CRUD, configuración técnica del tenant (settings, agentes).              |
| **Líder RRHH**     | `hr_leader`    |  L1   |    13    | CRUD sobre escenarios, competencias y talento. Gestor de la plataforma.       |
| **Jefe de Equipo** | `manager`      |  L2   |    8     | Lee talento de su equipo, administra/responde evaluaciones 360 limitadas.     |
| **Colaborador**    | `collaborator` |  L3   |    2     | (_Default_) Usuario final. Accede a "Mi Stratos" y responde evaluaciones 360. |
| **Observador**     | `observer`     |  L4   |    3     | (_Read-only_) Dashboards ejecutivos (Inversionistas/Demo).                    |

### Jerarquía de Roles

```
admin (100) → hr_leader (80) → manager (60) → collaborator (40) → observer (20)
```

La jerarquía se usa en el frontend vía `isAtLeast('manager')` para verificar si un usuario tiene al menos cierto nivel de acceso.

---

## 2. Esquema de Base de Datos

El sistema se apoya en 3 tablas:

1. **`users` (existente)**: Contiene la columna `role` (string) vinculada directamente al usuario. Valor por defecto: `collaborator`.
2. **`permissions`**: Catálogo maestro de permisos disponibles en el sistema.
    - `id`, `name` (ej. 'scenarios.create'), `module` (ej. 'scenarios'), `action` (ej. 'create'), `description`.
3. **`role_permissions`**: Tabla pivot que asocia strings de roles de sistema con PKs de permisos.
    - `role` (ej. 'hr_leader'), `permission_id`.
    - Constraint: `UNIQUE(role, permission_id)`.

**Migración:** `2026_02_27_014700_create_rbac_tables.php`
**Seeder:** `php artisan db:seed --class=RolePermissionSeeder`
**Estado actual:** 18 permisos, 45 mappings (roles → permisos).

---

## 3. Implementación Backend (Laravel 11)

### A. Trait: `HasSystemRole`

Archivo: `app/Traits/HasSystemRole.php`

El modelo `User` implementa este trait, que expone métodos fluidos para validación de acceso:

```php
$user->hasRole('hr_leader');               // bool — compara con users.role
$user->hasAnyRole(['admin', 'hr_leader']); // bool — uno de varios
$user->isAdmin();                          // bool — shorthand
$user->isHrOrAbove();                      // bool — admin || hr_leader
$user->hasPermission('scenarios.manage');  // bool — cacheado 1h por rol
$user->getPermissions();                   // Collection — lista de permisos
$user->getRoleDisplayName();              // string — "Administrador", "Líder RRHH", etc.
$user->clearPermissionCache();            // void — invalida cache del rol
```

**Overloading de `can()`:**
El trait sobrescribe el método `can()` nativo de Laravel. Si se le pasa un permiso con punto (ej. `scenarios.create`), lo resolverá vía RBAC. Si no, hará fall-back al Gate/Policy estándar de Laravel.

**Caché:** Los permisos de cada rol se cachean 1 hora vía `Cache::remember("rbac.permissions.{role}", 3600, ...)`. Se invalidan al actualizar permisos vía `RBACController::update()`.

### B. Middlewares

Registrados en `bootstrap/app.php` con alias:

1. **`role:{role_name[,role_name2]}`** (`App\Http\Middleware\CheckRole`):

    ```php
    // Solo admin y hr_leader pueden acceder
    Route::delete('/people/{id}', [...])->middleware('role:admin,hr_leader');
    ```

2. **`permission:{permission_name}`** (`App\Http\Middleware\CheckPermission`):

    ```php
    // Solo usuarios con permiso específico
    Route::post('/scenarios', [...])->middleware('permission:scenarios.create');
    ```

Ambos retornan JSON 403 con detalle del error:

```json
{
    "message": "No tienes permiso para realizar esta acción.",
    "required_permission": "scenarios.create",
    "your_role": "collaborator"
}
```

### C. Controller RBAC

Archivo: `app/Http/Controllers/Api/RBACController.php`

| Método     | Endpoint         | Middleware   | Descripción                      |
| ---------- | ---------------- | ------------ | -------------------------------- |
| `index()`  | `GET /api/rbac`  | `role:admin` | Lista roles, permisos y mappings |
| `update()` | `POST /api/rbac` | `role:admin` | Sincroniza permisos de un rol    |

### D. Rutas Protegidas Actuales

**Rutas API (`routes/api.php`):**

| Endpoint                         | Middleware                      | Motivo                               |
| -------------------------------- | ------------------------------- | ------------------------------------ |
| `GET /api/agents`                | `permission:agents.view`        | Solo HR+ puede ver config de agentes |
| `PUT /api/agents/{agent}`        | `permission:agents.manage`      | Solo HR+ puede editar agentes        |
| `POST /api/agents/test`          | `permission:agents.manage`      | Solo HR+ puede testear agentes       |
| `GET /api/rbac`                  | `role:admin`                    | Solo admin ve panel de permisos      |
| `POST /api/rbac`                 | `role:admin`                    | Solo admin modifica permisos         |
| `apiResource assessment-cycles`  | `permission:assessments.manage` | Solo HR+ gestiona ciclos             |
| `apiResource assessment-surveys` | `permission:assessments.manage` | Solo HR+ gestiona encuestas          |

**Rutas Web (`routes/web.php`):**

| Ruta                         | Middleware             | Motivo                                |
| ---------------------------- | ---------------------- | ------------------------------------- |
| `/talento360/comando`        | `role:admin,hr_leader` | Centro de Comando 360                 |
| `/people-experience/comando` | `role:admin,hr_leader` | Centro de Comando PX                  |
| `/talent-agents`             | `role:admin,hr_leader` | Configuración de agentes AI           |
| `/settings/rbac`             | `role:admin`           | Panel de gestión de permisos          |
| `/mi-stratos`                | `auth,verified`        | Accesible por todos (portal personal) |

---

## 4. Implementación Frontend (Vue 3 + Inertia)

### A. Hydration de Permisos

El sistema usa **dos canales** para hidratar permisos en el frontend:

1. **Inertia Shared Props (primario, instantáneo):**
   Configurado en `app/Http/Middleware/HandleInertiaRequests.php`:

    ```php
    'auth' => [
        'user' => $request->user(),
        'role' => $request->user()?->role ?? 'collaborator',
        'permissions' => $request->user()?->getPermissions()?->values()?->toArray() ?? [],
    ],
    ```

    Disponible inmediatamente en cualquier página sin llamada API.

2. **API Fallback:** `GET /api/auth/me` (para casos fuera de contexto Inertia).

### B. Composable: `usePermissions.ts`

Archivo: `resources/js/composables/usePermissions.ts`

Estado reactivo Singleton compartido a lo largo de toda la SPA. Intenta inicializarse desde Inertia; si no es posible, hace fallback a la API.

```typescript
import { usePermissions } from '@/composables/usePermissions';

const { can, canAny, canModule, hasRole, isAtLeast, isAdmin, role } =
    usePermissions();
```

#### Métodos Disponibles:

| Método            | Firma                                | Descripción                                        |
| ----------------- | ------------------------------------ | -------------------------------------------------- |
| `can`             | `(permission: string) => boolean`    | Verifica permiso específico. Admin siempre `true`. |
| `canAny`          | `(permissions: string[]) => boolean` | Al menos uno de los permisos.                      |
| `canModule`       | `(module: string) => boolean`        | Cualquier permiso del módulo (e.g. `scenarios.*`). |
| `hasRole`         | `(...roles: string[]) => boolean`    | Verifica pertenencia a rol(es).                    |
| `isAtLeast`       | `(role: string) => boolean`          | Verifica nivel jerárquico mínimo.                  |
| `loadPermissions` | `() => Promise<void>`                | Carga permisos desde API (fallback).               |
| `initFromInertia` | `() => void`                         | Inicializa desde shared props (auto en mount).     |

#### Estado Reactivo:

| Propiedad     | Tipo                      | Descripción               |
| ------------- | ------------------------- | ------------------------- |
| `role`        | `Readonly<Ref<string>>`   | Rol del sistema actual    |
| `permissions` | `Readonly<Ref<string[]>>` | Lista de permisos activos |
| `isAdmin`     | `Readonly<Ref<boolean>>`  | Es administrador          |
| `isLoaded`    | `Readonly<Ref<boolean>>`  | Datos cargados            |

#### Uso en Templates:

```vue
<script setup lang="ts">
import { usePermissions } from '@/composables/usePermissions';

const { can, hasRole, isAdmin, canModule } = usePermissions();
</script>

<template>
    <!-- Renderizado condicional basado en Permiso (Recomendado) -->
    <v-btn v-if="can('scenarios.create')" color="primary">
        Crear Escenario
    </v-btn>

    <!-- Basado en Módulo completo -->
    <nav-item v-if="canModule('assessments')">Talento 360°</nav-item>

    <!-- Basado en Rol -->
    <v-alert v-if="isAdmin">Zona de Administración</v-alert>

    <!-- Basado en Jerarquía -->
    <section v-if="isAtLeast('manager')">Panel de Equipo</section>
</template>
```

### C. Sidebar Reactivo

Archivo: `resources/js/components/AppSidebar.vue`

El sidebar filtra dinámicamente los items de navegación usando las propiedades `requiredPermission` y `requiredRole` del tipo `NavItem`:

```typescript
interface NavItem {
    title: string;
    href: string;
    icon?: Component;
    requiredPermission?: string; // e.g. 'scenarios.view'
    requiredRole?: string[]; // e.g. ['admin', 'hr_leader']
}
```

Los items se filtran con `computed`:

```typescript
const mainNavItems = computed(() => {
    return allNavItems.filter((item) => {
        if (!item.requiredPermission && !item.requiredRole) return true;
        if (item.requiredPermission && !can(item.requiredPermission))
            return false;
        if (item.requiredRole && !hasRole(...item.requiredRole)) return false;
        return true;
    });
});
```

**Resultado:** Cada rol ve un sidebar diferente:

- **Admin:** Ve todos los 16 items
- **HR Leader:** Ve 14 items (no Settings)
- **Manager:** Ve 11 items (no Comando, no Agents, no Settings)
- **Collaborator:** Ve 2 items (Mi Stratos, Dashboard)
- **Observer:** Ve 7 items (Dashboard + módulos read-only)

### D. Página de Gestión de Permisos

Archivo: `resources/js/pages/settings/RBAC.vue`
Ruta: `/settings/rbac` (solo `admin`)

Interfaz visual para gestionar la matriz de permisos por rol:

- Tabs por rol con conteo de permisos
- Grid de módulos con toggle-all por módulo
- Checkboxes individuales por permiso
- Admin siempre tiene todos los permisos (no editable)
- Guardado atómico vía transacción SQL + invalidación de cache

---

## 5. Diccionario de Permisos (18 permisos)

### 🟢 Escenarios Estratégicos

| Permiso            | Descripción         | admin | hr_leader | manager | collaborator | observer |
| ------------------ | ------------------- | :---: | :-------: | :-----: | :----------: | :------: |
| `scenarios.view`   | Ver escenarios      |  ✅   |    ✅     |   ✅    |      ❌      |    ✅    |
| `scenarios.create` | Crear escenarios    |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |
| `scenarios.edit`   | Editar escenarios   |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |
| `scenarios.delete` | Eliminar escenarios |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |

### 🔵 Roles y Competencias

| Permiso               | Descripción           | admin | hr_leader | manager | collaborator | observer |
| --------------------- | --------------------- | :---: | :-------: | :-----: | :----------: | :------: |
| `roles.view`          | Ver roles del negocio |  ✅   |    ✅     |   ✅    |      ❌      |    ❌    |
| `roles.manage`        | Crear/Editar roles    |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |
| `competencies.view`   | Ver diccionario       |  ✅   |    ✅     |   ✅    |      ❌      |    ❌    |
| `competencies.manage` | Gestionar BARS        |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |

### 🟣 Evaluación 360

| Permiso               | Descripción         | admin | hr_leader | manager | collaborator | observer |
| --------------------- | ------------------- | :---: | :-------: | :-----: | :----------: | :------: |
| `assessments.view`    | Ver evaluaciones    |  ✅   |    ✅     |   ✅    |      ❌      |    ✅    |
| `assessments.manage`  | Configurar ciclos   |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |
| `assessments.respond` | Responder encuestas |  ✅   |    ✅     |   ✅    |      ✅      |    ❌    |

### 🟠 Talento / Personas

| Permiso                  | Descripción            | admin | hr_leader | manager | collaborator | observer |
| ------------------------ | ---------------------- | :---: | :-------: | :-----: | :----------: | :------: |
| `people.view`            | Ver directorio         |  ✅   |    ✅     |   ✅    |      ❌      |    ✅    |
| `people.manage`          | Añadir/editar personas |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |
| `people.view_my_profile` | Ver perfil propio      |  ✅   |    ✅     |   ✅    |      ✅      |    ❌    |

### 🔴 Configuración y Agentes

| Permiso           | Descripción          | admin | hr_leader | manager | collaborator | observer |
| ----------------- | -------------------- | :---: | :-------: | :-----: | :----------: | :------: |
| `agents.view`     | Ver agentes AI       |  ✅   |    ✅     |   ❌    |      ❌      |    ❌    |
| `agents.manage`   | Configurar agentes   |  ✅   |    ❌     |   ❌    |      ❌      |    ❌    |
| `settings.view`   | Ver configuración    |  ✅   |    ❌     |   ❌    |      ❌      |    ❌    |
| `settings.manage` | Editar configuración |  ✅   |    ❌     |   ❌    |      ❌      |    ❌    |

---

## 6. Archivos del Sistema

| Archivo                                                        | Propósito                             |
| -------------------------------------------------------------- | ------------------------------------- |
| `app/Models/Permission.php`                                    | Modelo Eloquent de permisos           |
| `app/Traits/HasSystemRole.php`                                 | Trait RBAC para User                  |
| `app/Http/Middleware/CheckRole.php`                            | Middleware de verificación de rol     |
| `app/Http/Middleware/CheckPermission.php`                      | Middleware de verificación de permiso |
| `app/Http/Middleware/HandleInertiaRequests.php`                | Comparte rol/permisos con frontend    |
| `app/Http/Controllers/Api/RBACController.php`                  | API para gestión de permisos          |
| `app/Http/Controllers/Api/AuthController.php`                  | Endpoint `/api/auth/me`               |
| `bootstrap/app.php`                                            | Registro de alias de middleware       |
| `database/migrations/2026_02_27_014700_create_rbac_tables.php` | Migración                             |
| `database/seeders/RolePermissionSeeder.php`                    | Seed de 18 permisos + mappings        |
| `resources/js/composables/usePermissions.ts`                   | Composable Vue RBAC                   |
| `resources/js/components/AppSidebar.vue`                       | Sidebar filtrado por RBAC             |
| `resources/js/pages/settings/RBAC.vue`                         | UI de gestión de permisos             |
| `resources/js/types/index.d.ts`                                | Types Auth + NavItem con RBAC         |

---

> **Principio de Diseño:** Preferir `permission:xxx` sobre `role:xxx` en middleware. Los roles definen capas de acceso; los permisos definen acciones granulares. Administrar permisos vía UI (`/settings/rbac`) dinamiza el control sin tocar código.

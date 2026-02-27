# 🛡️ Sistema RBAC de Stratos (Role-Based Access Control)

El sistema de control de acceso en Stratos se introdujo en la _Wave 2_ (Marzo 2026) para gestionar autorizaciones granulares a lo largo de la plataforma. Está diseñado para ser escalable, seguro (vía middlewares de Laravel) y reactivo en el Frontend (vía composables de Vue).

---

## 1. Conceptos Core

En Stratos, se hace una clara distinción entre **Roles de Sistema** y **Permisos**:

- **Roles de Sistema:** Definen el "qué es" el usuario dentro de la plataforma (ej. Administrador, Colaborador). Cada usuario tiene exactamente _un_ rol de sistema activo (almacenado en `users.role`).
    - _No confundir con los Roles de Negocio_ (ej. "Desarrollador Backend", "Gerente de Finanzas"), los cuales pertenecen al Grafo de Talento y Módulo de Competencias.
- **Permisos:** Definen acciones granulares ("qué puede hacer"). Usan notación de punto `modulo.accion` (ej. `scenarios.create`).

### Matriz de Roles de Sistema Mapeados

| Rol de Sistema     | Identificador  | Nivel | Propósito                                                                                          |
| :----------------- | :------------- | :---: | :------------------------------------------------------------------------------------------------- |
| **Administrador**  | `admin`        |  L0   | Full CRUD, configuración técnica del tenant (settings, agentes).                                   |
| **Líder RRHH**     | `hr_leader`    |  L1   | CRUD sobre escenarios, competencias y talento. Gestor de la plataforma.                            |
| **Jefe de Equipo** | `manager`      |  L2   | Lee talento de su equipo, administra/responde evaluaciones 360 limitadas.                          |
| **Colaborador**    | `collaborator` |  L3   | (_Default_) Usuario final. Accede a "Mi Stratos", rutas de aprendizaje y evaluaciones 360 propias. |
| **Observador**     | `observer`     |  L4   | (_Read-only_) Dashboards ejecutivos (Inversionistas/Demo).                                         |

---

## 2. Esquema de Base de Datos

El sistema se apoya en 3 tablas:

1. **`users` (existente)**: Contiene la columna `role` (string) vinculada directamente al usuario. Valor por defecto: `collaborator`.
2. **`permissions`**: Catálogo maestro de permisos disponibles en el sistema.
    - `id`, `name` (ej. 'scenarios.create'), `module` (ej. 'scenarios'), `action` (ej. 'create'), `description`.
3. **`role_permissions`**: Tabla pivot (sin ID de rol formal, asocia strings de roles de sistema con Pk de Permisos).
    - `role` (ej. 'hr_leader'), `permission_id`.

> **Seeder:** Todo el setup inicial se ejecuta vía `php artisan db:seed --class=RolePermissionSeeder`.

---

## 3. Implementación Backend (Laravel 11)

### A. Trait: `HasSystemRole`

El modelo `User` implementa el trait `App\Traits\HasSystemRole`, el cual expone métodos fluidos para validación de acceso:

```php
$user->hasRole('hr_leader');               // bool
$user->hasAnyRole(['admin', 'hr_leader']); // bool
$user->isAdmin();                          // bool
$user->hasPermission('scenarios.manage');  // bool (Cacheado por 1 hora por rol)
```

**Overloading de `can()`:**
El trait sobrescribe el método `can()` nativo de Laravel. Si se le pasa un permiso con punto (ej. `scenarios.create`), lo resolverá vía RBAC. Si no, hará fall-back al Gate/Policy estándar de Laravel, permitiendo usar Policies para condiciones de negocio específicas (ej. "es dueño del escenario").

### B. Middlewares

Se crearon dos middlewares ubicados en `app/Http/Middleware/` y registrados en `bootstrap/app.php` con alias:

1. **`role:{role_name}`** (`CheckRole::class`):

    ```php
    Route::delete('/people/{id}', [...])->middleware('role:admin,hr_leader');
    ```

2. **`permission:{permission_name}`** (`CheckPermission::class`):
    ```php
    Route::post('/scenarios', [...])->middleware('permission:scenarios.create');
    ```

---

## 4. Implementación Frontend (Vue 3)

### Endpoint de Hydration

El frontend se nutre a través del endpoint `GET /api/auth/me` (manejado por `AuthController::me`). Este endpoint expone todos los datos del usuario actual, incluyendo su array plano de `permissions` vigentes.

### Composable: `usePermissions.ts`

Ubicado en `resources/js/composables/usePermissions.ts`. Provee un **estado reactivo Singleton** a lo largo de toda la Single Page Application (SPA).

#### Uso en Componentes (SFC):

```vue
<script setup lang="ts">
import { usePermissions } from '@/composables/usePermissions';

// 1. Extraer los helpers y el estado
const { can, hasRole, isAdmin, roleDisplay } = usePermissions();
</script>

<template>
    <v-card>
        <!-- Visualizar rol actual -->
        <v-chip>{{ roleDisplay }}</v-chip>

        <!-- Renderizado condicional basado en Permiso Específico (Recomendado) -->
        <v-btn v-if="can('scenarios.create')" color="primary">
            Crear Escenario
        </v-btn>

        <!-- Renderizado condicional basado en Rol -->
        <v-alert v-if="hasRole('admin')"> Zona de peligro. </v-alert>
    </v-card>
</template>
```

---

## 5. Diccionario de Permisos de la Wave 2

_Este diccionario refleja los mapeos iniciales (ver `RolePermissionSeeder` para la matriz de asignación exacta por rol)._

#### 🟢 Escenarios

- `scenarios.view`
- `scenarios.create`
- `scenarios.edit`
- `scenarios.delete`

#### 🔵 Talento 360 / Evaluaciones

- `assessments.view`
- `assessments.manage` (Configurar ciclos)
- `assessments.respond` (Responder/Asignar feedback)

#### 🟠 Organigrama y Personas

- `people.view`
- `people.manage`
- `people.view_my_profile` (El core de adopción de la plataforma por el usuario default).

#### 🟣 Bloque Metodológico (Cubo y Catálogos)

- `roles.view`, `roles.manage`
- `competencies.view`, `competencies.manage`

#### 🔴 Configuración

- `settings.view`, `settings.manage`
- `agents.view`, `agents.manage`

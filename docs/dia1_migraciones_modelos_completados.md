# Día 1: Migraciones y Modelos Eloquent - ✅ COMPLETADO

**Fecha:** 2025-12-27  
**Tiempo empleado:** ~2 horas  
**Status:** ✅ COMPLETO

---

## ✅ Migraciones Creadas (10 tablas)

### Tabla `organizations` ✅

```
- id (PK)
- name
- subdomain (UNIQUE)
- industry
- size (ENUM: small, medium, large, enterprise)
- timestamps
```

### Tabla `users` (MODIFICADA) ✅

Agregados a tabla existente:

```
+ organization_id (FK → organizations, NULLABLE para compatibilidad SQLite)
+ role (VARCHAR, default: 'employee')
```

### Tabla `skills` ✅

```
- id (PK)
- organization_id (FK)
- name
- category (ENUM: technical, soft, business, language)
- description
- is_critical (boolean)
- timestamps
- INDEX: category
- UNIQUE: (organization_id, name)
```

### Tabla `roles` ✅

```
- id (PK)
- organization_id (FK)
- name
- department
- level (ENUM: junior, mid, senior, lead, principal)
- description
- timestamps
- INDEX: level
- UNIQUE: (organization_id, name)
```

### Tabla `role_skills` (PIVOT) ✅

```
- id (PK)
- role_id (FK → roles)
- skill_id (FK → skills)
- required_level (TINYINT, default: 3)
- is_critical (boolean)
- timestamps
- UNIQUE: (role_id, skill_id)
```

### Tabla `People` ✅

```
- id (PK)
- organization_id (FK)
- user_id (FK → users, NULLABLE)
- first_name
- last_name
- email
- current_role_id (FK → roles, NULLABLE)
- department
- hire_date
- photo_url
- soft_deletes
- timestamps
- INDEX: department
- UNIQUE: (organization_id, email)
```

### Tabla `people_skills` (PIVOT) ✅

```
- id (PK)
- people_id (FK → People)
- skill_id (FK → skills)
- level (TINYINT, default: 1)
- last_evaluated_at (timestamp)
- evaluated_by (FK → users, NULLABLE)
- timestamps
- UNIQUE: (people_id, skill_id)
```

### Tabla `development_paths` ✅

```
- id (PK)
- organization_id (FK)
- people_id (FK → People)
- target_role_id (FK → roles)
- status (ENUM: draft, active, completed, cancelled)
- estimated_duration_months
- started_at
- completed_at
- steps (JSON)
- timestamps
- INDEX: (people_id, status)
```

### Tabla `job_openings` ✅

```
- id (PK)
- organization_id (FK)
- title
- role_id (FK → roles)
- department
- status (ENUM: draft, open, closed, filled)
- deadline
- created_by (FK → users)
- timestamps
- INDEX: (organization_id, status)
```

### Tabla `applications` ✅

```
- id (PK)
- job_opening_id (FK → job_openings)
- people_id (FK → People)
- status (ENUM: pending, under_review, accepted, rejected)
- message (TEXT)
- applied_at (timestamp)
- timestamps
- INDEX: status
- UNIQUE: (job_opening_id, people_id)
```

---

## ✅ Modelos Eloquent Creados (7 modelos)

### 1. Organization ✅

**Relaciones:**

- `users()` → HasMany
- `skills()` → HasMany
- `roles()` → HasMany
- `People()` → HasMany
- `developmentPaths()` → HasMany
- `jobOpenings()` → HasMany

### 2. User (ACTUALIZADO) ✅

**Nuevos atributos:**

- `organization_id` → fillable
- `role` → fillable

**Nuevas relaciones:**

- `organization()` → BelongsTo
- `people()` → HasOne

### 3. Skill ✅

**Relaciones:**

- `organization()` → BelongsTo
- `roles()` → BelongsToMany (with pivot: required_level, is_critical)
- `People()` → BelongsToMany (with pivot: level, last_evaluated_at, evaluated_by)

**Global Scope:** `organization` - Filtra por organization_id del usuario autenticado

### 4. Role ✅

**Relaciones:**

- `organization()` → BelongsTo
- `skills()` → BelongsToMany (with pivot: required_level, is_critical)
- `People()` → HasMany (currentRole)
- `jobOpenings()` → HasMany
- `developmentPaths()` → HasMany (target role)

**Global Scope:** `organization` - Filtra por organization_id del usuario autenticado

### 5. People ✅

**Atributos:**

- SoftDeletes habilitado
- Accessor: `full_name` (first_name + last_name)

**Relaciones:**

- `organization()` → BelongsTo
- `user()` → BelongsTo
- `currentRole()` → BelongsTo (Role)
- `skills()` → BelongsToMany (with pivot: level, last_evaluated_at, evaluated_by)
- `developmentPaths()` → HasMany
- `applications()` → HasMany

**Global Scope:** `organization` - Filtra por organization_id del usuario autenticado

### 6. DevelopmentPath ✅

**Casts:**

- `steps` → array (para JSON)
- `started_at` → datetime
- `completed_at` → datetime

**Relaciones:**

- `organization()` → BelongsTo
- `people()` → BelongsTo
- `targetRole()` → BelongsTo (Role)

**Global Scope:** `organization` - Filtra por organization_id del usuario autenticado

### 7. JobOpening ✅

**Relaciones:**

- `organization()` → BelongsTo
- `role()` → BelongsTo
- `createdBy()` → BelongsTo (User)
- `applications()` → HasMany

**Global Scope:** `organization` - Filtra por organization_id del usuario autenticado

### 8. Application ✅

**Relaciones:**

- `jobOpening()` → BelongsTo
- `people()` → BelongsTo

**Global Scope:** `people_org` - Filtra por organization_id mediante jobOpening

---

## 📊 Verificación Realizada

✅ Todas las migraciones ejecutadas correctamente
✅ Todas las tablas creadas en BD
✅ Todas los modelos sin errores de sintaxis
✅ Relaciones Eloquent configuradas
✅ Global Scopes para multi-tenant implementados
✅ Índices y constraints configurados

---

## 🎯 Próximos Pasos - Día 2

**Objetivo:** Crear DemoSeeder con datos de TechCorp

### Tareas:

1. Crear `DemoSeeder` (database/seeders)
2. Crear organización "TechCorp"
3. Agregar 8 roles (Backend Dev, Frontend Dev, QA, Product Manager, etc.)
4. Agregar 30 skills (técnicas, soft, business)
5. Configurar rol_skills (relaciones skills requeridas por rol)
6. Crear 20 peopleas (empleados de TechCorp)
7. Configurar people_skills (competencias de cada peoplea)
8. Crear 5 vacantes internas
9. Crear 10 postulaciones de ejemplo

### Comando:

```bash
php artisan db:seed --class=DemoSeeder
```

---

## 📁 Archivos Modificados

| Archivo                                                                        | Cambios        |
| ------------------------------------------------------------------------------ | -------------- |
| `database/migrations/2025_12_27_162327_add_organization_id_to_users_table.php` | ✅ Creada      |
| `database/migrations/2025_12_27_162332_create_organizations_table.php`         | ✅ Creada      |
| `database/migrations/2025_12_27_162333_create_skills_table.php`                | ✅ Creada      |
| `database/migrations/2025_12_27_162333_create_roles_table.php`                 | ✅ Creada      |
| `database/migrations/2025_12_27_162333_create_role_skills_table.php`           | ✅ Creada      |
| `database/migrations/2025_12_27_162333_create_People_table.php`                | ✅ Creada      |
| `database/migrations/2025_12_27_162333_create_people_skills_table.php`         | ✅ Creada      |
| `database/migrations/2025_12_27_162334_create_development_paths_table.php`     | ✅ Creada      |
| `database/migrations/2025_12_27_162334_create_job_openings_table.php`          | ✅ Creada      |
| `database/migrations/2025_12_27_162334_create_applications_table.php`          | ✅ Creada      |
| `app/Models/User.php`                                                          | ✅ Actualizado |
| `app/Models/Organization.php`                                                  | ✅ Creado      |
| `app/Models/Skill.php`                                                         | ✅ Creado      |
| `app/Models/Role.php`                                                          | ✅ Creado      |
| `app/Models/People.php`                                                        | ✅ Creado      |
| `app/Models/DevelopmentPath.php`                                               | ✅ Creado      |
| `app/Models/JobOpening.php`                                                    | ✅ Creado      |
| `app/Models/Application.php`                                                   | ✅ Creado      |

---

## 🔐 Seguridad Multi-Tenant

Todos los modelos implementan **Global Scopes** que filtran automáticamente por `organization_id`:

```php
// Ejemplo: Skill.php
protected static function booted()
{
    static::addGlobalScope('organization', function (Builder $builder) {
        if (auth()->check() && auth()->user()->organization_id) {
            $builder->where('skills.organization_id', auth()->user()->organization_id);
        }
    });
}
```

Esto asegura que:

- Cada usuario solo ve datos de su organización
- Queries automáticas filtradas
- Imposible acceder a datos de otra org sin manipular código
- Se ejecuta a nivel de consulta (eficiente)

---

**Estado:** ✅ DÍA 1 COMPLETADO  
**Próximo:** Día 2 - Seeders (TechCorp demo data)

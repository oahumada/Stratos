# People Role Skills - Implementación Completa

## 📋 Resumen Ejecutivo

Se ha implementado el sistema `people_role_skills` que soluciona la **inconsistencia crítica** entre las skills de una persona y las skills requeridas por su rol.

### Problema Identificado

Anteriormente, `people_skills` y `role_skills` eran tablas independientes sin relación. Una persona podía tener skills completamente diferentes a las requeridas por su rol actual.

### Solución Implementada

Nueva tabla `people_role_skills` que:
- ✅ Vincula skills de personas con el contexto del rol
- ✅ Mantiene historial cuando cambian de rol (skills antiguas → `is_active=false`)
- ✅ Rastrea fechas de evaluación y expiración
- ✅ Permite reevaluación periódica (default: 6 meses)
- ✅ Compara nivel actual vs requerido (gaps de skills)

---

## 📊 Estadísticas Actuales

| Métrica | Valor |
|---------|-------|
| Total skills migradas | 129 |
| Skills activas | 129 |
| Skills que requieren reevaluación | 74 |
| Fecha migración | 2026-01-01 |

---

## 🗂️ Estructura de la Tabla

### Schema - `people_role_skills`

```sql
CREATE TABLE people_role_skills (
    id BIGINT PRIMARY KEY,
    people_id BIGINT NOT NULL,          -- FK a people
    role_id BIGINT NOT NULL,            -- FK a roles (contexto del rol)
    skill_id BIGINT NOT NULL,           -- FK a skills
    current_level INT DEFAULT 1,        -- Nivel actual (1-5)
    required_level INT DEFAULT 3,       -- Nivel requerido por el rol
    is_active BOOLEAN DEFAULT TRUE,     -- Activo (rol actual) o histórico
    evaluated_at TIMESTAMP,             -- Fecha de última evaluación
    expires_at TIMESTAMP,               -- Fecha de expiración (reevaluación necesaria)
    evaluated_by BIGINT,                -- FK a users (quién evaluó)
    notes TEXT,                         -- Notas adicionales
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX (people_id, is_active),
    INDEX (role_id, skill_id),
    INDEX (expires_at),
    
    FOREIGN KEY (people_id) REFERENCES people(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Campos Clave

| Campo | Propósito |
|-------|-----------|
| `role_id` | **Crítico**: contexto del rol cuando se asignó la skill |
| `is_active` | Diferencia skills actuales (`true`) de históricas (`false`) |
| `current_level` | Proficiencia real de la persona (1-5) |
| `required_level` | Nivel esperado por el rol |
| `expires_at` | Control de reevaluación (default: 6 meses) |

---

## 💡 Lógica de Negocio

### 1. Asignación de Rol Nuevo

Cuando una persona cambia de rol:

```php
// Paso 1: Desactivar skills del rol anterior
$repository->deactivateSkillsForPerson($personId, $newRoleId);

// Paso 2: Sincronizar skills del nuevo rol
$repository->syncSkillsFromRole($personId, $newRoleId, $evaluatorId);
```

**Comportamiento:**
- Skills del rol anterior → `is_active = false` (quedan en historial)
- Skills del nuevo rol → se agregan o actualizan
- Si una skill existe en ambos roles → mantiene `current_level`, actualiza `required_level`
- Skills nuevas → se crean con `current_level = 1`

### 2. Historial de Skills

```
Ejemplo: Juan Pérez
┌─────────────────────────────────────────────────────┐
│ Rol: Backend Developer (2024-06-01 a 2025-12-01)   │
│ Skills: PHP (4), Laravel (3), MySQL (4)             │
│ Estado: is_active = false (histórico)               │
└─────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────┐
│ Rol: Team Lead (2025-12-01 a presente)              │
│ Skills: PHP (4), Laravel (3), Leadership (2)        │
│ Estado: is_active = true (actual)                   │
└─────────────────────────────────────────────────────┘
```

**Observación:** PHP y Laravel se mantienen (misma skill, nuevo rol), Leadership es nueva.

### 3. Expiración y Reevaluación

- **Default:** Skills expiran a los 6 meses
- **Warning:** Se activa alerta 30 días antes de `expires_at`
- **Acción:** Re-evaluar y actualizar `current_level`, `evaluated_at`, `expires_at`

```php
// Skills que necesitan reevaluación en los próximos 30 días
$skillsToReevaluate = $repository->getSkillsNeedingReevaluation($orgId);

// Skills ya expiradas
$expiredSkills = $repository->getExpiredSkills($orgId);
```

---

## 🔧 API - Métodos del Repository

### PeopleRoleSkillsRepository

#### Consultas

```php
// Skills activas de una persona (rol actual)
getActiveSkillsForPerson(int $personId): Collection

// Historial completo (activas + antiguas)
getSkillHistoryForPerson(int $personId): Collection

// Skills por debajo del nivel requerido
getSkillGapsForPerson(int $personId): Collection

// Estadísticas agregadas
getStatsForPerson(int $personId): array
// Retorna: ['total' => 10, 'active' => 7, 'expired' => 3, 'needs_reevaluation' => 2, 'below_required' => 4]
```

#### Operaciones

```php
// Sincronizar skills al asignar nuevo rol
syncSkillsFromRole(int $personId, int $roleId, int $evaluatedBy = null): array

// Desactivar skills del rol anterior
deactivateSkillsForPerson(int $personId, int $exceptRoleId = null): int

// Reevaluación masiva por organización
getSkillsNeedingReevaluation(int $orgId = null): Collection
getExpiredSkills(int $orgId = null): Collection
```

---

## 🎯 Scopes del Modelo

### PeopleRoleSkills

```php
// Skills activas (rol actual)
PeopleRoleSkills::active()->get();

// Skills expiradas
PeopleRoleSkills::expired()->get();

// Skills que necesitan reevaluación (30 días)
PeopleRoleSkills::needsReevaluation()->get();

// Filtrar por persona
PeopleRoleSkills::forPerson($personId)->get();

// Filtrar por rol
PeopleRoleSkills::forRole($roleId)->get();
```

### Helpers del Modelo

```php
$skill = PeopleRoleSkills::find(1);

$skill->isExpired();              // bool
$skill->needsReevaluation();      // bool (30 días antes)
$skill->meetsRequirement();       // bool (current_level >= required_level)
$skill->getLevelGap();            // int (required_level - current_level)
```

---

## 🔗 Relaciones Eloquent

### People Model

```php
// Todas las skills (activas + históricas)
$person->roleSkills;

// Solo skills activas (rol actual)
$person->activeSkills;

// Solo skills expiradas
$person->expiredSkills;
```

### Roles Model

```php
// Todas las asignaciones de skills a personas para este rol
$role->peopleRoleSkills;
```

### Skills Model

```php
// Todas las personas que tienen esta skill (activas + históricas)
$skill->peopleRoleSkills;
```

---

## 📝 Migración de Datos

### Estrategia Ejecutada

El seeder `PeopleRoleSkillsSeeder` migró 129 skills desde `people_skills`:

1. **Fuente:** Tabla `people_skills` (skills actuales)
2. **Destino:** Tabla `people_role_skills`
3. **Lógica:**
   - Obtener `role_id` desde `people.role_id`
   - Obtener `required_level` desde `role_skills` (o default 3)
   - Calcular `expires_at` = `evaluated_at` + 6 meses
   - Marcar todas como `is_active = true`
   - Copiar `current_level` desde `people_skills.level`

4. **Resultado:**
   - ✅ 129 skills migradas
   - ✅ 0 personas sin rol (100% éxito)
   - ⚠️ 74 skills ya expiradas (requieren reevaluación)

### Comando de Migración

```bash
php artisan db:seed --class=PeopleRoleSkillsSeeder
```

---

## ⚠️ Consideraciones Importantes

### 1. Deprecación de `people_skills`

**Importante:** La tabla `people_skills` queda como legacy pero **no se debe usar** para nuevas operaciones.

- ✅ Usar: `people_role_skills` para todas las operaciones
- ❌ Evitar: Insertar/actualizar directamente en `people_skills`

### 2. Sincronización Automática

**Pendiente:** Implementar observer/evento en `People` model:

```php
// app/Observers/PeopleObserver.php
public function updating(People $person)
{
    // Si cambió role_id, auto-sincronizar skills
    if ($person->isDirty('role_id')) {
        $repository = app(PeopleRoleSkillsRepository::class);
        $repository->deactivateSkillsForPerson($person->id, $person->role_id);
        $repository->syncSkillsFromRole($person->id, $person->role_id);
    }
}
```

### 3. Reevaluaciones Pendientes

**Acción requerida:** 74 skills expiradas necesitan reevaluación.

Crear comando artisan para notificar:

```bash
php artisan skills:notify-reevaluation
```

---

## 🚀 Próximos Pasos

### Prioridad Alta

- [ ] Implementar `PeopleObserver` para auto-sync en cambio de rol
- [ ] Crear comando `skills:notify-reevaluation` para alertas
- [ ] Actualizar FormSchema para CRUD de `people_role_skills`
- [ ] Endpoint API: `GET /api/people/{id}/skills/active`
- [ ] Endpoint API: `GET /api/people/{id}/skills/history`

### Prioridad Media

- [ ] Frontend: Tabs "Skills Actuales" vs "Historial"
- [ ] Frontend: Badges de expiración (rojo: expirado, amarillo: 30 días)
- [ ] Gráfico de evolución de skills en el tiempo
- [ ] Reporte de gaps por equipo/organización

### Prioridad Baja

- [ ] Tests unitarios (PeopleRoleSkillsRepository)
- [ ] Tests de integración (sync en cambio de rol)
- [ ] Documentación OpenAPI/Swagger
- [ ] Migración completa (eliminar `people_skills` cuando esté todo probado)

---

## 📖 Documentación Relacionada

- [DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md](./DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md)
- [PATRON_JSON_DRIVEN_CRUD.md](./PATRON_JSON_DRIVEN_CRUD.md)
- [GUIA_CREAR_NUEVO_CRUD_GENERICO.md](./GUIA_CREAR_NUEVO_CRUD_GENERICO.md)
- [DATABASE_ER_DIAGRAM.html](./DATABASE_ER_DIAGRAM.html)

---

## 📅 Historial de Cambios

| Fecha | Cambio | Autor |
|-------|--------|-------|
| 2026-01-01 | Creación de tabla `people_role_skills` | GitHub Copilot |
| 2026-01-01 | Migración de datos desde `people_skills` | GitHub Copilot |
| 2026-01-01 | Implementación de Repository y Model | GitHub Copilot |
| 2026-01-01 | Documentación completa | GitHub Copilot |

---

**Versión:** 1.0.0  
**Estado:** ✅ Implementado - Listo para uso  
**Última actualización:** 2026-01-01

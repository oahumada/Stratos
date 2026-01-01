# 🎯 People Role Skills - Resumen Ultra-Rápido

## ¿Qué es?

Sistema que vincula las **skills de una persona con su rol**, manteniendo **historial completo** cuando cambia de puesto.

## ¿Por qué?

**Problema:** Antes, `people_skills` y `role_skills` eran tablas independientes. Una persona podía tener skills sin relación con su rol.

**Solución:** Nueva tabla `people_role_skills` que:
- Asocia cada skill al **rol en que se evaluó**
- Mantiene **historial** (skills antiguas → `is_active = false`)
- Rastrea **expiración** (default: 6 meses)
- Identifica **gaps** (`current_level < required_level`)

## Campos Clave

| Campo | Qué hace |
|-------|----------|
| `people_id` | Persona evaluada |
| `role_id` | Rol cuando se asignó la skill |
| `skill_id` | Skill evaluada |
| `current_level` | Nivel actual (1-5) |
| `required_level` | Nivel esperado (1-5) |
| `is_active` | `true` = skill actual, `false` = histórico |
| `expires_at` | Cuándo reevaluar |

## Ejemplo Real

**María López cambia de Backend Developer → Team Lead:**

| Skill | Backend Dev (antes) | Team Lead (después) |
|-------|---------------------|---------------------|
| PHP | Nivel 4 ✅ | Nivel 4 ✅ (mantiene) |
| MySQL | Nivel 4 ✅ | ⚠️ Histórico (no requerida) |
| Leadership | - | Nivel 1 🆕 (requiere 4) → **GAP: -3** |

## Estado Actual

- ✅ 129 skills migradas desde `people_skills`
- ⚠️ 74 skills expiradas (requieren reevaluación)
- ⚠️ 75 skills por debajo del nivel requerido (gaps)

### Cambios recientes (enero 2026)

- Relaciones de modelos `People::skills` y `Skills::People` ahora usan el pivote `people_role_skills` (con filtro `is_active=true`).
- Seeder `PeopleRoleSkillsSeeder` ignora la tabla legacy `people_skills` si no existe.
- Frontend `FormSchema` consume `/api/people` ya en contexto `people_role_skills` (pivote legacy deprecado).

## Comandos

```bash
# Verificar todo
./verify-people-role-skills.sh

# Ver stats de una persona
php artisan tinker
>>> $repo = app(\App\Repository\PeopleRoleSkillsRepository::class);
>>> $stats = $repo->getStatsForPerson(1);
>>> print_r($stats);

# Ver gaps
>>> $gaps = \App\Models\PeopleRoleSkills::whereColumn('current_level', '<', 'required_level')->get();
```

## Próximos Pasos

1. **Observer** → Auto-sincronizar skills al cambiar rol
2. **API** → Endpoints para frontend (`/api/people/{id}/skills/active`)
3. **Frontend** → Tabs "Skills Actuales" vs "Historial"
4. **Notificaciones** → Alertar skills por expirar

## Archivos Clave

- **Migración:** `2026_01_01_171617_create_people_role_skills_table.php`
- **Modelo:** `app/Models/PeopleRoleSkills.php`
- **Repository:** `app/Repository/PeopleRoleSkillsRepository.php`
- **Seeder:** `database/seeders/PeopleRoleSkillsSeeder.php`

## Documentación Completa

- [PEOPLE_ROLE_SKILLS_RESUMEN_FINAL.md](./PEOPLE_ROLE_SKILLS_RESUMEN_FINAL.md) - Resumen ejecutivo
- [PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md](./PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md) - Documentación técnica
- [PEOPLE_ROLE_SKILLS_FLUJO.md](./PEOPLE_ROLE_SKILLS_FLUJO.md) - Diagramas Mermaid

---

**TL;DR:** Skills ahora tienen contexto de rol + historial + expiración. 129 skills migradas. Listos para APIs y frontend.

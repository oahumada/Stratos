# 📊 Diagrama Entidad-Relación (ER) - Base de Datos TalentIA

**Fecha:** 1 Enero 2026  
**Versión:** 1.0  
**Estado:** ✅ Completado

---

## 📋 Descripción General

Base de datos SQLite con arquitectura relacional para la plataforma de gestión de talento TalentIA. Implementa el modelo de **Skills** por **Roles** usando una tabla pivot `role_skills`.

---

## 🗂️ Tabla de Contenidos

1. [Diagrama ER Visual](#diagrama-er-visual)
2. [Estructura de Tablas](#estructura-de-tablas)
3. [Relaciones](#relaciones)
4. [Estadísticas de Datos](#estadísticas-de-datos)
5. [Ejemplos de Consultas](#ejemplos-de-consultas)

---

## 🎯 Diagrama ER Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│                     TALENT IA DATABASE SCHEMA                       │
└─────────────────────────────────────────────────────────────────────┘

                              ┌──────────────┐
                              │ ORGANIZATIONS│
                              │  (1)         │
                              │  id          │
                              │  name        │
                              └──────┬───────┘
                                     │ 1:N
                    ┌────────────────┼────────────────┐
                    │                │                │
        ┌───────────▼────┐  ┌───────▼──────┐  ┌──────▼──────────┐
        │    ROLES (8)   │  │ SKILLS (30)  │  │   PEOPLE (20)   │
        │  id (PK)       │  │  id (PK)     │  │   id (PK)       │
        │  name          │  │  name        │  │   name          │
        │  level         │  │  category    │  │   email         │
        │  organization  │  │  is_critical │  │   role_id       │
        └────────┬───────┘  └──────▲───────┘  └────────┬────────┘
                 │                 │                   │
                 │ N:M           N:M                  │
                 │ (pivot)      (pivot)               │
        ┌────────▼────────┐  ┌─────┴──────────┐      │
        │  ROLE_SKILLS    │  │ PEOPLE_SKILLS  │◄─────┘
        │  (48 rel.)      │  │ (129 rel.)     │ 1:N
        │  id (PK)        │  │ id (PK)        │
        │  role_id (FK)───┤  │ people_id (FK) │
        │  skill_id (FK)──┼──│ skill_id (FK)  │
        │  required_level │  │ level (1-5)    │
        │  is_critical    │  └────────────────┘
        └────────────────┘

        ┌──────────────────┐   ┌─────────────────────┐
        │ JOB_OPENINGS (5) │   │ APPLICATIONS (10)   │
        │ id (PK)          │   │ id (PK)             │
        │ title            │   │ job_opening_id (FK) │
        │ role_id (FK)─────┼──▶│ people_id (FK)      │
        │ required_skills  │   │ status              │
        └──────────────────┘   └─────────────────────┘

        ┌────────────────────────┐
        │ DEVELOPMENT_PATHS      │
        │ id (PK)                │
        │ source_role_id (FK)    │
        │ target_role_id (FK)    │
        │ estimated_duration     │
        └────────────────────────┘
```

---

## 🏗️ Estructura de Tablas

### 1. **ROLES** (8 registros)

| Campo | Tipo | Constraints | Descripción |
|-------|------|-------------|------------|
| id | INTEGER | PRIMARY KEY | ID único |
| organization_id | INTEGER | NOT NULL | Organización |
| department_id | INTEGER | NULLABLE | Departamento |
| name | VARCHAR | NOT NULL | Nombre del rol |
| department | VARCHAR | NULLABLE | Departamento (texto) |
| level | VARCHAR | NOT NULL (default: 'mid') | Nivel: mid, senior, lead |
| description | TEXT | NULLABLE | Descripción |
| created_at | DATETIME | NULLABLE | Fecha creación |
| updated_at | DATETIME | NULLABLE | Fecha actualización |

**Roles Existentes:**
```
1. Backend Developer (mid)
2. Frontend Developer (mid)
3. Senior Full Stack Developer (senior)
4. QA Engineer (mid)
5. Product Manager (senior)
6. DevOps Engineer (senior)
7. Technical Lead (lead)
8. Business Analyst (mid)
```

---

### 2. **SKILLS** (30 registros)

| Campo | Tipo | Constraints | Descripción |
|-------|------|-------------|------------|
| id | INTEGER | PRIMARY KEY | ID único |
| organization_id | INTEGER | NOT NULL | Organización |
| name | VARCHAR | NOT NULL | Nombre de la skill |
| category | VARCHAR | NOT NULL (default: 'technical') | Categoría |
| description | TEXT | NULLABLE | Descripción |
| is_critical | TINYINT(1) | NOT NULL (default: 0) | ¿Es crítica? |
| created_at | DATETIME | NULLABLE | Fecha creación |
| updated_at | DATETIME | NULLABLE | Fecha actualización |

**Categorías de Skills:**
- **Technical** (12): PHP, Laravel, Vue.js, TypeScript, etc.
- **Soft Skills** (9): Communication, Problem Solving, Leadership, etc.
- **Business** (9): Budget Management, Strategic Planning, etc.

---

### 3. **ROLE_SKILLS** ⭐ NUEVA TABLA (48 registros)

| Campo | Tipo | Constraints | Descripción |
|-------|------|-------------|------------|
| id | INTEGER | PRIMARY KEY | ID único |
| role_id | INTEGER | NOT NULL, FK | Referencia a ROLES |
| skill_id | INTEGER | NOT NULL, FK | Referencia a SKILLS |
| required_level | INTEGER | NOT NULL (default: 3) | Nivel requerido (1-5) |
| is_critical | TINYINT(1) | NOT NULL (default: 0) | ¿Es crítica para el rol? |
| created_at | DATETIME | NULLABLE | Fecha creación |
| updated_at | DATETIME | NULLABLE | Fecha actualización |

**Características:**
- ✅ **6 skills por rol** (8 roles × 6 skills = 48 registros)
- ✅ **Relación N:M** entre ROLES y SKILLS
- ✅ **Atributos adicionales**: required_level, is_critical
- ✅ **Integridad referencial**: FK cascada en eliminación

---

### 4. **PEOPLE_SKILLS** (129 registros)

| Campo | Tipo | Constraints | Descripción |
|-------|------|-------------|------------|
| id | INTEGER | PRIMARY KEY | ID único |
| people_id | INTEGER | NOT NULL, FK | Referencia a PEOPLE |
| skill_id | INTEGER | NOT NULL, FK | Referencia a SKILLS |
| level | INTEGER | NOT NULL (default: 1) | Nivel actual (1-5) |
| last_evaluated_at | DATETIME | NOT NULL | Última evaluación |
| evaluated_by | INTEGER | NULLABLE, FK | Usuario que evaluó |
| created_at | DATETIME | NULLABLE | Fecha creación |
| updated_at | DATETIME | NULLABLE | Fecha actualización |

---

### 5. **PEOPLE** (20 registros)

| Campo | Tipo | Constraints |
|-------|------|-------------|
| id | INTEGER | PRIMARY KEY |
| name | VARCHAR | NOT NULL |
| email | VARCHAR | NOT NULL |
| role_id | INTEGER | NULLABLE, FK |
| organization_id | INTEGER | NOT NULL |
| created_at | DATETIME | NULLABLE |
| updated_at | DATETIME | NULLABLE |

---

## 🔗 Relaciones

### Foreign Keys (Claves Foráneas)

```
ROLE_SKILLS
├── role_id → ROLES.id (ON DELETE CASCADE)
└── skill_id → SKILLS.id (ON DELETE CASCADE)

PEOPLE_SKILLS
├── people_id → PEOPLE.id (ON DELETE CASCADE)
└── skill_id → SKILLS.id (ON DELETE CASCADE)

JOB_OPENINGS
└── role_id → ROLES.id (ON DELETE CASCADE)

APPLICATIONS
├── job_opening_id → JOB_OPENINGS.id (ON DELETE CASCADE)
└── people_id → PEOPLE.id (ON DELETE CASCADE)

DEVELOPMENT_PATHS
├── source_role_id → ROLES.id (ON DELETE CASCADE)
└── target_role_id → ROLES.id (ON DELETE CASCADE)
```

---

## 📊 Estadísticas de Datos

### Conteos Actuales

| Entidad | Cantidad |
|---------|----------|
| **Organizaciones** | 1 (TechCorp) |
| **Roles** | 8 |
| **Skills** | 30 |
| **Role-Skill Relations** | 48 |
| **People** | 20 |
| **People-Skill Relations** | 129 |
| **Job Openings** | 5 |
| **Applications** | 10 |
| **Development Paths** | 1 |

### Distribución por Rol

```
Backend Developer ...................... 6 skills
Frontend Developer ..................... 6 skills
Senior Full Stack Developer ............ 6 skills
QA Engineer ............................ 6 skills
Product Manager ........................ 6 skills
DevOps Engineer ........................ 6 skills
Technical Lead ......................... 6 skills
Business Analyst ....................... 6 skills
                                    ─────────
                        TOTAL:      48 skills
```

---

## 🔍 Ejemplos de Consultas

### 1. Ver todas las skills de un rol

```sql
SELECT 
  r.name AS role,
  s.name AS skill,
  rs.required_level,
  rs.is_critical
FROM role_skills rs
LEFT JOIN roles r ON rs.role_id = r.id
LEFT JOIN skills s ON rs.skill_id = s.id
WHERE r.name = 'Backend Developer'
ORDER BY rs.is_critical DESC, rs.required_level DESC;
```

**Resultado:**
```
role                | skill            | required_level | is_critical
────────────────────┼──────────────────┼────────────────┼────────────
Backend Developer   | PHP              | 4              | 1
Backend Developer   | Laravel          | 4              | 1
Backend Developer   | Problem Solving  | 4              | 1
Backend Developer   | Database Design  | 3              | 1
Backend Developer   | REST APIs        | 3              | 1
Backend Developer   | Git              | 3              | 1
```

---

### 2. Comparar skills entre roles

```sql
SELECT 
  r.name AS role,
  COUNT(rs.skill_id) AS total_skills,
  SUM(CASE WHEN rs.is_critical = 1 THEN 1 ELSE 0 END) AS critical_skills
FROM roles r
LEFT JOIN role_skills rs ON r.id = rs.role_id
GROUP BY r.id
ORDER BY r.name;
```

---

### 3. Encontrar skills faltantes de una persona para un rol

```sql
SELECT 
  s.name AS skill,
  rs.required_level AS required,
  COALESCE(ps.level, 0) AS current_level,
  (rs.required_level - COALESCE(ps.level, 0)) AS gap
FROM role_skills rs
LEFT JOIN skills s ON rs.skill_id = s.id
LEFT JOIN people_skills ps ON s.id = ps.skill_id AND ps.people_id = 1
WHERE rs.role_id = 3
AND (COALESCE(ps.level, 0) < rs.required_level OR ps.id IS NULL)
ORDER BY gap DESC;
```

---

### 4. Habilidades críticas por rol

```sql
SELECT 
  r.name AS role,
  GROUP_CONCAT(s.name, ', ') AS critical_skills
FROM role_skills rs
LEFT JOIN roles r ON rs.role_id = r.id
LEFT JOIN skills s ON rs.skill_id = s.id
WHERE rs.is_critical = 1
GROUP BY r.id
ORDER BY r.name;
```

---

## 🛠️ Acceso a la Base de Datos

### Ubicación
```
/home/omar/TalentIA/src/database/database.sqlite
```

### Herramientas Disponibles

**CLI (Línea de Comandos)**
```bash
sqlite3 /home/omar/TalentIA/src/database/database.sqlite
```

**Interfaz Gráfica (si tienes display)**
```bash
sqlitebrowser /home/omar/TalentIA/src/database/database.sqlite
```

---

## 📝 Notas Técnicas

### Integridad Referencial
✅ Todas las foreign keys configuradas con `CASCADE` en delete para mantener integridad.

### Indexes
Se recomienda crear índices en:
- `role_skills.role_id`
- `role_skills.skill_id`
- `people_skills.people_id`
- `people_skills.skill_id`

### Performance
- Total registros: ~500+
- Operaciones típicas: INNER JOIN en role_skills con roles y skills
- Rendimiento esperado: <100ms para queries complejas

---

## 🚀 Próximos Pasos

1. ✅ Crear modelo RoleSkill
2. ✅ Crear RoleSkillRepository
3. ✅ Poblar datos con seeders
4. ⏳ Crear FormSchema para CRUD de role_skills
5. ⏳ Crear endpoints API REST
6. ⏳ Crear componentes Vue para gestión

---

**Documento generado:** 1 Enero 2026  
**Revisado por:** Sistema de documentación automática

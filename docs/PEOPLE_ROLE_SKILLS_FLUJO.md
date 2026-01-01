# People Role Skills - Flujo de Operación

## Diagrama de Flujo - Asignación de Rol

```mermaid
flowchart TD
    START(["Persona cambia de rol"])
    
    START --> CHECK_OLD{"¿Tiene rol anterior?"}
    
    CHECK_OLD -->|Sí| DEACTIVATE["Desactivar skills del rol anterior<br/>is_active = false"]
    CHECK_OLD -->|No| GET_NEW_SKILLS
    
    DEACTIVATE --> GET_NEW_SKILLS["Obtener skills requeridas<br/>por nuevo rol (role_skills)"]
    
    GET_NEW_SKILLS --> LOOP_START{"Para cada skill<br/>del nuevo rol"}
    
    LOOP_START --> CHECK_EXISTS{"¿Persona ya<br/>tiene esta skill?"}
    
    CHECK_EXISTS -->|Sí| UPDATE_EXISTING["Actualizar registro existente:<br/>• role_id = nuevo_rol<br/>• required_level = del nuevo rol<br/>• is_active = true<br/>• evaluated_at = now()<br/>• expires_at = now() + 6 meses<br/>• Mantener current_level"]
    
    CHECK_EXISTS -->|No| CREATE_NEW["Crear nuevo registro:<br/>• people_id, role_id, skill_id<br/>• current_level = 1<br/>• required_level = del rol<br/>• is_active = true<br/>• evaluated_at = now()<br/>• expires_at = now() + 6 meses"]
    
    UPDATE_EXISTING --> LOOP_NEXT{"¿Más skills?"}
    CREATE_NEW --> LOOP_NEXT
    
    LOOP_NEXT -->|Sí| LOOP_START
    LOOP_NEXT -->|No| END(["Sincronización completa"])
    
    style START fill:#e1f5e1
    style END fill:#e1f5e1
    style DEACTIVATE fill:#fff3cd
    style UPDATE_EXISTING fill:#d1ecf1
    style CREATE_NEW fill:#d1ecf1
    style CHECK_OLD fill:#f8d7da
    style CHECK_EXISTS fill:#f8d7da
```

---

## Diagrama de Estados - Skill Lifecycle

```mermaid
stateDiagram-v2
    [*] --> Nueva: Persona asignada a rol
    
    Nueva --> Activa: Skill agregada<br/>(is_active=true)
    
    Activa --> Activa: Reevaluación dentro<br/>de período válido
    
    Activa --> PorExpirar: 30 días antes<br/>de expires_at
    
    PorExpirar --> Activa: Reevaluación exitosa<br/>(expires_at += 6 meses)
    
    PorExpirar --> Expirada: Pasa expires_at<br/>sin reevaluación
    
    Expirada --> Activa: Reevaluación tardía<br/>(expires_at = now() + 6m)
    
    Activa --> Histórica: Cambio de rol<br/>(is_active=false)
    
    Histórica --> Activa: Vuelve a rol anterior<br/>(is_active=true)
    
    Histórica --> [*]: Registro permanece<br/>(nunca se borra)
```

---

## Diagrama de Componentes - Arquitectura

```mermaid
flowchart TB
    subgraph Frontend ["Frontend (Vue 3)"]
        COMP_ACTIVE["Componente:<br/>Skills Activas"]
        COMP_HISTORY["Componente:<br/>Historial Skills"]
        COMP_GAPS["Componente:<br/>Gaps de Skills"]
        COMP_WARNINGS["Componente:<br/>Alertas Expiración"]
    end
    
    subgraph API ["API Layer (Laravel)"]
        EP_ACTIVE["/api/people/{id}/skills/active"]
        EP_HISTORY["/api/people/{id}/skills/history"]
        EP_GAPS["/api/people/{id}/skills/gaps"]
        EP_STATS["/api/people/{id}/skills/stats"]
        EP_SYNC["/api/people/{id}/role/sync"]
    end
    
    subgraph Service ["Service Layer"]
        SVC_ROLE["PeopleRoleService:<br/>handleRoleChange()"]
        SVC_NOTIFICATION["NotificationService:<br/>notifyReevaluation()"]
    end
    
    subgraph Repository ["Repository Layer"]
        REPO["PeopleRoleSkillsRepository:<br/>• syncSkillsFromRole()<br/>• deactivateSkillsForPerson()<br/>• getActiveSkillsForPerson()<br/>• getSkillGapsForPerson()<br/>• getStatsForPerson()"]
    end
    
    subgraph Model ["Model Layer"]
        MDL_PEOPLE["People Model"]
        MDL_ROLE_SKILL["PeopleRoleSkills Model:<br/>• Scopes (active, expired)<br/>• Helpers (isExpired, meetsRequirement)<br/>• Relations"]
        MDL_ROLE["Roles Model"]
        MDL_SKILL["Skills Model"]
    end
    
    subgraph Observer ["Observers/Events"]
        OBS_PEOPLE["PeopleObserver:<br/>onRoleChange()"]
        EVT_EXPIRATION["SkillExpirationEvent"]
    end
    
    subgraph Database ["Database"]
        TBL_PEOPLE_ROLE_SKILLS[("people_role_skills<br/>• people_id, role_id, skill_id<br/>• current_level, required_level<br/>• is_active, expires_at")]
        TBL_PEOPLE[("people")]
        TBL_ROLES[("roles")]
        TBL_SKILLS[("skills")]
        TBL_ROLE_SKILLS[("role_skills")]
    end
    
    COMP_ACTIVE --> EP_ACTIVE
    COMP_HISTORY --> EP_HISTORY
    COMP_GAPS --> EP_GAPS
    COMP_WARNINGS --> EP_STATS
    
    EP_ACTIVE --> REPO
    EP_HISTORY --> REPO
    EP_GAPS --> REPO
    EP_STATS --> REPO
    EP_SYNC --> SVC_ROLE
    
    SVC_ROLE --> REPO
    SVC_NOTIFICATION --> REPO
    
    REPO --> MDL_ROLE_SKILL
    REPO --> TBL_ROLE_SKILLS
    
    MDL_ROLE_SKILL --> TBL_PEOPLE_ROLE_SKILLS
    MDL_PEOPLE --> TBL_PEOPLE
    MDL_ROLE --> TBL_ROLES
    MDL_SKILL --> TBL_SKILLS
    
    OBS_PEOPLE --> SVC_ROLE
    EVT_EXPIRATION --> SVC_NOTIFICATION
    
    TBL_PEOPLE_ROLE_SKILLS -.FK.-> TBL_PEOPLE
    TBL_PEOPLE_ROLE_SKILLS -.FK.-> TBL_ROLES
    TBL_PEOPLE_ROLE_SKILLS -.FK.-> TBL_SKILLS
    
    style COMP_ACTIVE fill:#e1f5e1
    style COMP_HISTORY fill:#e1f5e1
    style COMP_GAPS fill:#fff3cd
    style COMP_WARNINGS fill:#f8d7da
    style REPO fill:#d1ecf1
    style TBL_PEOPLE_ROLE_SKILLS fill:#ffc107
```

---

## Diagrama de Secuencia - Cambio de Rol

```mermaid
sequenceDiagram
    actor User
    participant Frontend
    participant API
    participant PeopleObserver
    participant PeopleRoleService
    participant Repository
    participant DB
    
    User->>Frontend: Asignar nuevo rol
    Frontend->>API: PUT /api/people/{id}<br/>{role_id: 5}
    API->>DB: UPDATE people SET role_id=5
    
    Note over DB,PeopleObserver: Observer detecta cambio en role_id
    
    DB-->>PeopleObserver: Model updated event
    PeopleObserver->>PeopleRoleService: handleRoleChange(person, oldRoleId, newRoleId)
    
    PeopleRoleService->>Repository: deactivateSkillsForPerson(personId, exceptRoleId=5)
    Repository->>DB: UPDATE people_role_skills<br/>SET is_active=false<br/>WHERE people_id=X AND role_id != 5
    DB-->>Repository: 7 skills desactivadas
    
    PeopleRoleService->>Repository: syncSkillsFromRole(personId, roleId=5, evaluatedBy)
    Repository->>DB: SELECT * FROM role_skills WHERE role_id=5
    DB-->>Repository: [PHP, Laravel, MySQL, Git, Leadership]
    
    loop Para cada skill del nuevo rol
        Repository->>DB: SELECT * FROM people_role_skills<br/>WHERE people_id=X AND skill_id=Y AND is_active=true
        
        alt Skill ya existe (activa)
            DB-->>Repository: Registro existente (PHP: current_level=4)
            Repository->>DB: UPDATE people_role_skills<br/>SET role_id=5, required_level=3,<br/>expires_at=now()+6m<br/>WHERE id=Z
        else Skill nueva
            DB-->>Repository: NULL
            Repository->>DB: INSERT INTO people_role_skills<br/>(people_id, role_id, skill_id,<br/>current_level=1, required_level=3,<br/>is_active=true, expires_at=now()+6m)
        end
    end
    
    Repository-->>PeopleRoleService: {updated: 3, created: 2}
    PeopleRoleService-->>API: Sync completed
    API-->>Frontend: 200 OK + stats
    Frontend-->>User: ✓ Rol actualizado,<br/>skills sincronizadas
    
    Note over User,DB: Skills del rol anterior quedan en historial (is_active=false)
    Note over User,DB: Skills compartidas entre roles mantienen current_level
```

---

## Diagrama ER - Relaciones

```mermaid
erDiagram
    PEOPLE ||--o{ PEOPLE_ROLE_SKILLS : "tiene"
    ROLES ||--o{ PEOPLE_ROLE_SKILLS : "requiere"
    SKILLS ||--o{ PEOPLE_ROLE_SKILLS : "evalúa"
    USERS ||--o{ PEOPLE_ROLE_SKILLS : "evalúa"
    ROLES ||--o{ ROLE_SKILLS : "define"
    SKILLS ||--o{ ROLE_SKILLS : "incluye"
    
    PEOPLE {
        bigint id PK
        string name
        bigint role_id FK "Rol actual"
        bigint organization_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    ROLES {
        bigint id PK
        string name
        string description
        bigint organization_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    SKILLS {
        bigint id PK
        string name
        string category
        timestamp created_at
        timestamp updated_at
    }
    
    ROLE_SKILLS {
        bigint id PK
        bigint role_id FK "Rol que requiere skill"
        bigint skill_id FK
        int required_level "1-5"
        timestamp created_at
        timestamp updated_at
    }
    
    PEOPLE_ROLE_SKILLS {
        bigint id PK
        bigint people_id FK "Persona evaluada"
        bigint role_id FK "Contexto del rol"
        bigint skill_id FK "Skill evaluada"
        int current_level "1-5: nivel actual"
        int required_level "1-5: nivel requerido"
        boolean is_active "true: actual, false: histórico"
        timestamp evaluated_at "Fecha evaluación"
        timestamp expires_at "Fecha expiración"
        bigint evaluated_by FK "Evaluador (users)"
        text notes "Notas"
        timestamp created_at
        timestamp updated_at
    }
    
    USERS {
        bigint id PK
        string name
        string email
        timestamp created_at
        timestamp updated_at
    }
```

---

## Casos de Uso

### Caso 1: Persona sin skill previa cambia de rol

**Escenario:**
- Juan Pérez es Junior Developer (role_id=1)
- Se le asigna rol Backend Developer (role_id=2)
- Backend Developer requiere: PHP (nivel 3), Laravel (nivel 3), MySQL (nivel 3)
- Juan NO tiene ninguna de esas skills registradas

**Resultado:**
```sql
-- Se crean 3 registros nuevos:
INSERT INTO people_role_skills (people_id, role_id, skill_id, current_level, required_level, is_active, expires_at)
VALUES 
  (123, 2, 10, 1, 3, true, '2026-07-01'),  -- PHP
  (123, 2, 11, 1, 3, true, '2026-07-01'),  -- Laravel
  (123, 2, 12, 1, 3, true, '2026-07-01');  -- MySQL
```

**Gap de skills:** 3 skills (current_level=1 < required_level=3)

---

### Caso 2: Persona con skills previas cambia a rol que comparte algunas

**Escenario:**
- María López es Backend Developer (role_id=2)
- Tiene: PHP (nivel 4), Laravel (nivel 3), MySQL (nivel 4)
- Se le asigna rol Team Lead (role_id=3)
- Team Lead requiere: PHP (nivel 3), Laravel (nivel 2), Leadership (nivel 4), Communication (nivel 4)

**Resultado:**
```sql
-- Step 1: Desactivar skills del rol anterior que NO están en el nuevo
UPDATE people_role_skills 
SET is_active = false 
WHERE people_id = 456 
  AND role_id = 2 
  AND skill_id = 12;  -- MySQL (no requerida en Team Lead)

-- Step 2: Actualizar skills compartidas (PHP, Laravel)
UPDATE people_role_skills 
SET role_id = 3, required_level = 3, is_active = true, expires_at = '2026-07-01'
WHERE people_id = 456 AND skill_id = 10;  -- PHP (mantiene current_level=4)

UPDATE people_role_skills 
SET role_id = 3, required_level = 2, is_active = true, expires_at = '2026-07-01'
WHERE people_id = 456 AND skill_id = 11;  -- Laravel (mantiene current_level=3)

-- Step 3: Crear skills nuevas (Leadership, Communication)
INSERT INTO people_role_skills (people_id, role_id, skill_id, current_level, required_level, is_active, expires_at)
VALUES 
  (456, 3, 20, 1, 4, true, '2026-07-01'),  -- Leadership (nueva)
  (456, 3, 21, 1, 4, true, '2026-07-01');  -- Communication (nueva)
```

**Observaciones:**
- MySQL queda en historial (is_active=false) con role_id=2
- PHP y Laravel mantienen current_level (4 y 3) pero actualizan required_level (3 y 2)
- María cumple requisitos de PHP y Laravel pero necesita desarrollar Leadership y Communication

**Gap de skills:** 2 skills (Leadership, Communication)

---

### Caso 3: Skill expira y se reevalúa

**Escenario:**
- Pedro Gómez tiene PHP (nivel 3) evaluado hace 7 meses
- expires_at = '2025-12-01' (ya pasó)
- Se necesita reevaluar

**Consulta:**
```sql
-- Skills expiradas
SELECT * FROM people_role_skills 
WHERE expires_at < NOW() 
  AND is_active = true;

-- Skills que expiran en 30 días (warning)
SELECT * FROM people_role_skills 
WHERE expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
  AND is_active = true;
```

**Acción:**
```sql
-- Reevaluación: Pedro mejoró de nivel 3 a 4
UPDATE people_role_skills 
SET current_level = 4,
    evaluated_at = NOW(),
    expires_at = DATE_ADD(NOW(), INTERVAL 6 MONTH),
    evaluated_by = 789,  -- ID del evaluador
    notes = 'Reevaluación anual: mejora en frameworks modernos'
WHERE id = 1234;
```

---

## Índices y Performance

### Índices Existentes

```sql
-- Índice compuesto para consultas frecuentes
INDEX idx_people_active (people_id, is_active)

-- Índice para búsquedas por rol y skill
INDEX idx_role_skill (role_id, skill_id)

-- Índice para detección de expiraciones
INDEX idx_expires_at (expires_at)
```

### Consultas Optimizadas

```sql
-- Skills activas de una persona (usa idx_people_active)
SELECT * FROM people_role_skills 
WHERE people_id = 123 AND is_active = true;

-- Skills que expiran hoy (usa idx_expires_at)
SELECT * FROM people_role_skills 
WHERE expires_at < CURDATE() AND is_active = true;

-- Personas con skill específica en rol específico (usa idx_role_skill)
SELECT people_id, current_level 
FROM people_role_skills 
WHERE role_id = 2 AND skill_id = 10 AND is_active = true;
```

---

**Versión:** 1.0.0  
**Última actualización:** 2026-01-01  
**Autor:** GitHub Copilot

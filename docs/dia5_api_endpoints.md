# Día 5: API REST - Endpoints Completados

**Fecha:** 31 Dic (Día 5 del MVP)  
**Status:** ✅ COMPLETADO - 17 Endpoints Registrados

## Resumen

Completamos todos los endpoints necesarios para la gestión integral de talento:

- **4 endpoints de servicios core** (gap-analysis, development-paths, candidates ranking, marketplace)
- **6 endpoints de CRUD completo** (job-openings, applications)
- **6 endpoints de lectura** (Person, roles, skills)
- **1 endpoint de dashboard** (métricas)

## Endpoints por Categoría

### 1. Servicios Core (4 Endpoints)

#### POST /api/gap-analysis

Analiza brecha de competencias entre persona y rol.

```bash
curl -X POST http://localhost:8000/api/gap-analysis \
  -H "Content-Type: application/json" \
  -d '{
    "person_id": 1,
    "role_name": "Senior Developer"
  }'
```

**Response (200):**

```json
{
    "match_percentage": 75,
    "summary": {
        "category": "good",
        "skills_ok": 6,
        "total_skills": 8
    },
    "gaps": [
        {
            "skill_id": 5,
            "skill_name": "Cloud Architecture",
            "current_level": 2,
            "required_level": 4,
            "gap": 2,
            "status": "critical",
            "is_critical": true
        }
    ]
}
```

---

#### POST /api/development-paths/generate

Genera plan de desarrollo para alcanzar un rol.

```bash
curl -X POST http://localhost:8000/api/development-paths/generate \
  -H "Content-Type: application/json" \
  -d '{
    "person_id": 1,
    "role_id": 2
  }'
```

**Response (201):**

```json
{
    "id": 1,
    "person_id": 1,
    "role_id": 2,
    "status": "draft",
    "estimated_duration_months": 6,
    "steps": [
        {
            "order": 1,
            "action_type": "course",
            "title": "AWS Solutions Architect",
            "duration_hours": 120,
            "priority": "critical",
            "description": "Cubrir brecha crítica en Cloud Architecture"
        }
    ]
}
```

---

#### GET /api/job-openings/{id}/candidates

Ranking de candidatos para una vacante.

```bash
curl http://localhost:8000/api/job-openings/1/candidates
```

**Response (200):**

```json
{
    "job_opening": {
        "id": 1,
        "title": "Senior Backend Developer"
    },
    "candidates": [
        {
            "person_id": 1,
            "name": "Ana García",
            "match_percentage": 85,
            "missing_skills": ["Cloud Architecture"],
            "time_to_productivity_months": 3,
            "risk_factor": 15
        }
    ]
}
```

---

#### GET /api/Person/{person_id}/marketplace

Oportunidades internas disponibles para una persona.

```bash
curl http://localhost:8000/api/Person/1/marketplace
```

**Response (200):**

```json
{
    "person": {
        "id": 1,
        "name": "Ana García"
    },
    "opportunities": [
        {
            "id": 1,
            "title": "Senior Backend Developer",
            "role": "Tech Lead",
            "department": "Engineering",
            "deadline": "2025-01-31",
            "match_percentage": 85,
            "category": "good",
            "missing_skills_count": 1
        }
    ]
}
```

---

### 2. Job Openings - CRUD (3 Endpoints)

#### GET /api/job-openings

Lista de vacantes abiertas.

```bash
curl http://localhost:8000/api/job-openings
```

**Response (200):**

```json
[
    {
        "id": 1,
        "title": "Senior Backend Developer",
        "role": "Tech Lead",
        "department": "Engineering",
        "status": "open",
        "deadline": "2025-01-31",
        "applications_count": 3
    }
]
```

---

#### GET /api/job-openings/{id}

Detalle de una vacante.

```bash
curl http://localhost:8000/api/job-openings/1
```

**Response (200):**

```json
{
    "id": 1,
    "title": "Senior Backend Developer",
    "role": "Tech Lead",
    "department": "Engineering",
    "description": "Buscamos Senior Backend Developer...",
    "status": "open",
    "deadline": "2025-01-31",
    "applications_count": 3
}
```

---

### 3. Applications - CRUD (4 Endpoints)

#### GET /api/applications

Lista de postulaciones.

```bash
curl http://localhost:8000/api/applications
```

**Response (200):**

```json
[
    {
        "id": 1,
        "person_id": 1,
        "job_opening_id": 1,
        "status": "pending",
        "message": "Interesado en esta posición",
        "applied_at": "2025-12-31T10:00:00Z",
        "person": {
            "id": 1,
            "full_name": "Ana García"
        },
        "job_opening": {
            "id": 1,
            "title": "Senior Backend Developer"
        }
    }
]
```

---

#### GET /api/applications/{id}

Detalle de una postulación.

```bash
curl http://localhost:8000/api/applications/1
```

**Response (200):**

```json
{
  "id": 1,
  "person_id": 1,
  "job_opening_id": 1,
  "status": "pending",
  "message": "Interesado en esta posición",
  "applied_at": "2025-12-31T10:00:00Z",
  "person": { ... },
  "job_opening": { ... }
}
```

---

#### POST /api/applications

Crear nueva postulación.

```bash
curl -X POST http://localhost:8000/api/applications \
  -H "Content-Type: application/json" \
  -d '{
    "person_id": 1,
    "job_opening_id": 1,
    "message": "Muy interesado en esta posición"
  }'
```

**Response (201):**

```json
{
    "id": 1,
    "person_id": 1,
    "job_opening_id": 1,
    "status": "pending",
    "message": "Muy interesado en esta posición",
    "applied_at": "2025-12-31T10:05:00Z"
}
```

**Validaciones:**

- `person_id`: Debe existir en la tabla Person
- `job_opening_id`: Debe existir en la tabla job_openings
- La persona y vacante deben estar en la misma organización
- No puede haber postulación duplicada (mismo person_id + job_opening_id)

**Errores:**

- 422: Validación fallida o postulación duplicada

---

#### PATCH /api/applications/{id}

Actualizar estado de postulación.

```bash
curl -X PATCH http://localhost:8000/api/applications/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "accepted"
  }'
```

**Response (200):**

```json
{
    "id": 1,
    "person_id": 1,
    "job_opening_id": 1,
    "status": "accepted",
    "message": "...",
    "applied_at": "2025-12-31T10:00:00Z"
}
```

**Estados válidos:** `pending`, `under_review`, `accepted`, `rejected`

---

### 4. Person - Lectura (2 Endpoints)

#### GET /api/Person

Lista de personas.

```bash
curl http://localhost:8000/api/Person
```

**Response (200):**

```json
[
    {
        "id": 1,
        "first_name": "Ana",
        "last_name": "García",
        "email": "ana@techcorp.com",
        "skills_count": 6
    }
]
```

---

#### GET /api/Person/{id}

Detalle de persona con competencias.

```bash
curl http://localhost:8000/api/Person/1
```

**Response (200):**

```json
{
    "id": 1,
    "first_name": "Ana",
    "last_name": "García",
    "email": "ana@techcorp.com",
    "skills": [
        {
            "id": 1,
            "name": "Laravel",
            "level": 4
        }
    ]
}
```

---

### 5. Roles - Lectura (2 Endpoints)

#### GET /api/roles

Lista de roles.

```bash
curl http://localhost:8000/api/roles
```

**Response (200):**

```json
[
    {
        "id": 1,
        "name": "Senior Developer",
        "description": "Desarrollador Senior con...",
        "skills_count": 8
    }
]
```

---

#### GET /api/roles/{id}

Detalle de rol con competencias requeridas.

```bash
curl http://localhost:8000/api/roles/1
```

**Response (200):**

```json
{
    "id": 1,
    "name": "Senior Developer",
    "description": "...",
    "required_skills": [
        {
            "id": 1,
            "name": "Laravel",
            "required_level": 4
        }
    ]
}
```

---

### 6. Skills - Lectura (2 Endpoints)

#### GET /api/skills

Lista de competencias.

```bash
curl http://localhost:8000/api/skills
```

**Response (200):**

```json
[
    {
        "id": 1,
        "name": "Laravel",
        "category": "Backend"
    }
]
```

---

#### GET /api/skills/{id}

Detalle de competencia.

```bash
curl http://localhost:8000/api/skills/1
```

**Response (200):**

```json
{
    "id": 1,
    "name": "Laravel",
    "category": "Backend",
    "description": "Framework PHP moderno..."
}
```

---

### 7. Dashboard (1 Endpoint)

#### GET /api/dashboard/metrics

Métricas generales del sistema.

```bash
curl http://localhost:8000/api/dashboard/metrics
```

**Response (200):**

```json
{
    "total_Person": 20,
    "total_skills": 30,
    "total_roles": 8,
    "average_match_percentage": 72.5,
    "open_job_openings": 5,
    "pending_applications": 3
}
```

---

## Validación de Rutas

```bash
php artisan route:list | grep api
```

Salida esperada: 17 rutas registradas en `/api/`

---

## Testing de Endpoints

### Comandos Artisan Rápidos

```bash
# Análisis de brecha
php artisan gap:analyze 1 "Senior Developer"

# Generar plan de desarrollo
php artisan devpath:generate 1 "Senior Developer"

# Ranking de candidatos para vacante
php artisan candidates:rank 1
```

### Con cURL

```bash
# Crear postulación
curl -X POST http://localhost:8000/api/applications \
  -H "Content-Type: application/json" \
  -d '{
    "person_id": 1,
    "job_opening_id": 1,
    "message": "Me interesa esta posición"
  }'

# Ver marketplace de oportunidades
curl http://localhost:8000/api/Person/1/marketplace

# Actualizar status de postulación
curl -X PATCH http://localhost:8000/api/applications/1 \
  -H "Content-Type: application/json" \
  -d '{"status": "accepted"}'
```

---

## Próximos Pasos (Días 6-7)

- [ ] Páginas Vue para consumir estos endpoints
- [ ] Autenticación + autorización (usuarios pueden ver solo su org)
- [ ] Paginación en endpoints de lectura
- [ ] Búsqueda y filtros
- [ ] Documentación OpenAPI/Swagger

---

**Estado Final Día 5:** ✅ API REST Completa - Listo para Frontend

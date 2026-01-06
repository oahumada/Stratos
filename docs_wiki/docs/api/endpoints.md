# API Reference

Documentación completa de los 17 endpoints del MVP de TalentIA.

---

## 🚀 Base URL

```
Development: http://127.0.0.1:8000/api
Production:  https://talentia.example.com/api
```

---

## 🔐 Autenticación

Todos los endpoints requieren autenticación con **Laravel Sanctum**.

### Headers Requeridos

```http
Content-Type: application/json
X-CSRF-TOKEN: {csrf-token}
Cookie: laravel_session={session-cookie}
```

### Obtener Token CSRF

```bash
GET /sanctum/csrf-cookie
```

Respuesta: Cookie `XSRF-TOKEN` en headers.

### Login

```http
POST /login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Respuesta 200:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

### Logout

```http
POST /logout
```

**Respuesta 204:** No content

### Verificar Usuario Actual

```http
GET /api/user
```

**Respuesta 200:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "user@example.com"
}
```

---

## 📊 Endpoints CRUD Genéricos

Todos los modelos siguen el mismo patrón de endpoints:

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/{model}` | Listar todos |
| `POST` | `/api/{model}` | Crear nuevo |
| `GET` | `/api/{model}/{id}` | Obtener uno |
| `PUT` | `/api/{model}/{id}` | Actualizar |
| `DELETE` | `/api/{model}/{id}` | Eliminar |
| `POST` | `/api/{model}/search` | Búsqueda avanzada |

**Modelos disponibles:**
- `people` - Personas
- `roles` - Roles organizacionales
- `skills` - Competencias
- `departments` - Departamentos
- `job-openings` - Vacantes
- `applications` - Aplicaciones a vacantes

---

## 👥 People API

### Listar People

```http
GET /api/people?page=1&per_page=15
```

**Query Parameters:**
- `page` - Número de página (default: 1)
- `per_page` - Items por página (default: 15, max: 100)
- `sort_by` - Campo para ordenar
- `sort_order` - `asc` o `desc`

**Respuesta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "department": {
        "id": 2,
        "name": "Engineering"
      },
      "skills": [
        { "id": 5, "name": "JavaScript", "level": 4 }
      ],
      "created_at": "2026-01-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Crear Person

```http
POST /api/people
Content-Type: application/json

{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "department_id": 2,
  "hire_date": "2026-01-05",
  "skills": [
    { "skill_id": 5, "level": 4 },
    { "skill_id": 8, "level": 3 }
  ]
}
```

**Respuesta 201:**
```json
{
  "id": 151,
  "name": "Jane Smith",
  "email": "jane@example.com",
  "department_id": 2,
  "created_at": "2026-01-05T15:30:00Z"
}
```

### Obtener Person

```http
GET /api/people/151
```

**Respuesta 200:**
```json
{
  "id": 151,
  "name": "Jane Smith",
  "email": "jane@example.com",
  "department": {
    "id": 2,
    "name": "Engineering"
  },
  "skills": [
    {
      "id": 5,
      "name": "JavaScript",
      "pivot": { "level": 4 }
    }
  ],
  "current_role": {
    "id": 3,
    "name": "Senior Developer"
  }
}
```

### Actualizar Person

```http
PUT /api/people/151
Content-Type: application/json

{
  "name": "Jane Smith-Johnson",
  "email": "jane.johnson@example.com"
}
```

**Respuesta 200:**
```json
{
  "id": 151,
  "name": "Jane Smith-Johnson",
  "email": "jane.johnson@example.com"
}
```

### Eliminar Person

```http
DELETE /api/people/151
```

**Respuesta 204:** No content

### Buscar People

```http
POST /api/people/search
Content-Type: application/json

{
  "filters": {
    "department_id": 2,
    "skills": [5, 8],
    "min_skill_level": 3
  },
  "search": "john",
  "page": 1,
  "per_page": 20
}
```

**Respuesta 200:** Misma estructura que listar people.

---

## 🎯 Roles API

### Listar Roles

```http
GET /api/roles
```

**Respuesta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Senior Developer",
      "description": "Lead development team",
      "required_skills": [
        {
          "skill_id": 5,
          "skill_name": "JavaScript",
          "required_level": 4
        }
      ]
    }
  ]
}
```

### Crear Role

```http
POST /api/roles
Content-Type: application/json

{
  "name": "QA Engineer",
  "description": "Quality Assurance Specialist",
  "required_skills": [
    { "skill_id": 10, "required_level": 3 },
    { "skill_id": 12, "required_level": 4 }
  ]
}
```

**Respuesta 201:**
```json
{
  "id": 25,
  "name": "QA Engineer",
  "created_at": "2026-01-05T16:00:00Z"
}
```

---

## 🛠️ Skills API

### Listar Skills

```http
GET /api/skills
```

**Respuesta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "JavaScript",
      "category": "Programming Language",
      "description": "Frontend and backend programming"
    }
  ]
}
```

### Crear Skill

```http
POST /api/skills
Content-Type: application/json

{
  "name": "Kubernetes",
  "category": "DevOps",
  "description": "Container orchestration"
}
```

---

## 📊 Servicios Core

### 1. Gap Analysis

Analiza brecha de competencias entre persona y rol.

```http
POST /api/gap-analysis
Content-Type: application/json

{
  "people_id": 1,
  "role_name": "Senior Developer"
}
```

**Respuesta 200:**
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
  ],
  "strengths": [
    {
      "skill_id": 1,
      "skill_name": "JavaScript",
      "current_level": 5,
      "required_level": 4
    }
  ]
}
```

**Categorías de match:**
- `excellent`: >= 90%
- `good`: 70-89%
- `fair`: 50-69%
- `poor`: < 50%

### 2. Generate Learning Path

Genera plan de desarrollo personalizado.

```http
POST /api/development-paths/generate
Content-Type: application/json

{
  "people_id": 1,
  "role_id": 2
}
```

**Respuesta 201:**
```json
{
  "id": 15,
  "people_id": 1,
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
    },
    {
      "order": 2,
      "action_type": "project",
      "title": "Migrar app a Kubernetes",
      "duration_hours": 80,
      "priority": "high"
    }
  ]
}
```

### 3. Candidate Ranking

Clasifica candidatos para una vacante.

```http
GET /api/job-openings/1/candidates
```

**Respuesta 200:**
```json
{
  "job_opening": {
    "id": 1,
    "title": "Senior Backend Developer",
    "required_role_id": 3
  },
  "candidates": [
    {
      "people_id": 5,
      "name": "Alice Johnson",
      "match_percentage": 92,
      "category": "excellent",
      "missing_skills": 1,
      "strengths_count": 7
    },
    {
      "people_id": 12,
      "name": "Bob Smith",
      "match_percentage": 78,
      "category": "good",
      "missing_skills": 3,
      "strengths_count": 5
    }
  ]
}
```

### 4. Marketplace - Ver Vacantes

Lista vacantes con matching del usuario actual.

```http
GET /api/marketplace/job-openings
```

**Respuesta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Senior Frontend Developer",
      "department": "Engineering",
      "match_percentage": 85,
      "category": "good",
      "gaps": [
        { "skill": "React", "gap": 1 }
      ]
    }
  ]
}
```

### 5. Dashboard Metrics

Obtiene métricas para el dashboard.

```http
GET /api/dashboard/metrics
```

**Respuesta 200:**
```json
{
  "total_people": 150,
  "total_roles": 25,
  "total_skills": 80,
  "open_positions": 12,
  "avg_match_rate": 72.5,
  "recent_activity": [
    {
      "type": "application",
      "person": "John Doe",
      "job": "Senior Developer",
      "timestamp": "2026-01-05T14:30:00Z"
    }
  ]
}
```

---

## ⚠️ Error Responses

### 400 Bad Request

```json
{
  "message": "Invalid input",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found

```json
{
  "message": "Resource not found"
}
```

### 422 Validation Error

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "email": ["The email must be a valid email address."]
  }
}
```

### 500 Server Error

```json
{
  "message": "Server error",
  "error": "Internal server error"
}
```

---

## 📚 Recursos Adicionales

- **[Autenticación Sanctum](authentication.md)** - Detalles de autenticación
- **[CRUD Pattern](../development/crud-pattern.md)** - Cómo funcionan los endpoints genéricos
- **[Error Handling](errors.md)** - Manejo de errores

---

## 🧪 Testing con cURL

### Ejemplo Completo

```bash
# 1. Obtener CSRF cookie
curl -X GET http://127.0.0.1:8000/sanctum/csrf-cookie \
  -c cookies.txt

# 2. Login
curl -X POST http://127.0.0.1:8000/login \
  -b cookies.txt -c cookies.txt \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# 3. Obtener people
curl -X GET http://127.0.0.1:8000/api/people \
  -b cookies.txt \
  -H "Accept: application/json"

# 4. Crear role
curl -X POST http://127.0.0.1:8000/api/roles \
  -b cookies.txt \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {token-from-cookie}" \
  -d '{"name":"DevOps Engineer","description":"Infrastructure specialist"}'
```

---

## 📊 Rate Limiting

Todos los endpoints tienen límite de tasa:

- **Authenticated requests:** 60 requests/minuto
- **Unauthenticated requests:** 10 requests/minuto

**Headers de respuesta:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
Retry-After: 30
```

Cuando excedes el límite, recibes **429 Too Many Requests**.

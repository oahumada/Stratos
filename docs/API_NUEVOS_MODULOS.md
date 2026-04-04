# API — Nuevos Módulos v0.12.0

Referencia de los tres módulos incorporados en la versión **0.12.0**:

1. [Skill Intelligence](#1-skill-intelligence)
2. [Performance (Ciclos y Evaluaciones)](#2-performance-ciclos-y-evaluaciones)
3. [Org Chart — People](#3-org-chart--people)

> **Autenticación:** Todos los endpoints requieren `Authorization: Bearer <token>` (Sanctum).  
> **Base URL:** `/api`  
> **Tenant scope:** El `organization_id` se infiere automáticamente del usuario autenticado; no se pasa como parámetro.

---

## 1. Skill Intelligence

Análisis de brechas de habilidades a nivel organizacional.

### `GET /api/skill-intelligence/heatmap`

Matriz de calor **departamento × habilidad** con los valores de brecha agregados.

| Parámetro  | Tipo   | Requerido | Descripción                                                  |
| ---------- | ------ | --------- | ------------------------------------------------------------ |
| `category` | string | No        | Filtra por categoría de habilidad (ej. `technical`, `soft`). |

**Respuesta de ejemplo**

```json
{
    "departments": ["Engineering", "Sales", "HR"],
    "skills": ["Python", "Leadership", "Communication"],
    "matrix": [
        [0.8, 0.2, 0.5],
        [0.1, 0.9, 0.6],
        [0.0, 0.7, 0.4]
    ]
}
```

---

### `GET /api/skill-intelligence/top-gaps`

Lista de las habilidades con mayor brecha agregada en la organización.

| Parámetro | Tipo    | Requerido | Descripción                                 |
| --------- | ------- | --------- | ------------------------------------------- |
| `limit`   | integer | No        | Número de resultados (1–50). Default: `10`. |

**Respuesta de ejemplo**

```json
[
    {
        "skill_id": 12,
        "skill_name": "Python",
        "avg_gap": 0.82,
        "affected_count": 34
    },
    {
        "skill_id": 7,
        "skill_name": "Data Analysis",
        "avg_gap": 0.74,
        "affected_count": 28
    }
]
```

---

### `GET /api/skill-intelligence/upskilling`

Recomendaciones de upskilling priorizadas por brecha.

| Parámetro | Tipo    | Requerido | Descripción                                     |
| --------- | ------- | --------- | ----------------------------------------------- |
| `limit`   | integer | No        | Número de recomendaciones (1–20). Default: `8`. |

**Respuesta de ejemplo**

```json
[
    {
        "skill_id": 12,
        "skill_name": "Python",
        "gap": 0.82,
        "recommended_resources": [
            {
                "title": "Python para Data Science",
                "type": "course",
                "url": "..."
            }
        ]
    }
]
```

---

### `GET /api/skill-intelligence/coverage`

Resumen de cobertura de habilidades (% de empleados que cumplen el nivel requerido por skill).

**Sin parámetros de query.**

**Respuesta de ejemplo**

```json
{
    "overall_coverage_pct": 67.4,
    "by_category": {
        "technical": 58.2,
        "soft": 76.9,
        "leadership": 61.0
    },
    "total_skills_evaluated": 45,
    "total_employees": 120
}
```

---

## 2. Performance (Ciclos y Evaluaciones)

Gestión de ciclos de evaluación de desempeño y reviews 360°.

### Ciclos

#### `GET /api/performance/cycles`

Lista todos los ciclos de la organización (más recientes primero).

**Sin parámetros de query.**

**Respuesta de ejemplo**

```json
{
    "data": [
        {
            "id": 3,
            "name": "Q1 2026",
            "period": "2026-Q1",
            "starts_at": "2026-01-01",
            "ends_at": "2026-03-31",
            "status": "active",
            "reviews_count": 42
        }
    ]
}
```

---

#### `POST /api/performance/cycles`

Crea un nuevo ciclo de evaluación (estado inicial: `draft`).

**Body (JSON)**

```json
{
    "name": "Q2 2026",
    "period": "2026-Q2",
    "starts_at": "2026-04-01",
    "ends_at": "2026-06-30"
}
```

| Campo       | Tipo   | Requerido | Descripción                               |
| ----------- | ------ | --------- | ----------------------------------------- |
| `name`      | string | Sí        | Nombre descriptivo del ciclo (máx. 255).  |
| `period`    | string | Sí        | Identificador de período (ej. `2026-Q2`). |
| `starts_at` | date   | Sí        | Fecha de inicio.                          |
| `ends_at`   | date   | Sí        | Fecha de cierre (≥ `starts_at`).          |

**Respuesta:** `201 Created` con el ciclo creado.

---

#### `GET /api/performance/cycles/{id}`

Detalle de un ciclo específico.

---

#### `POST /api/performance/cycles/{id}/activate`

Activa un ciclo (cambia `status` a `active`).

---

#### `POST /api/performance/cycles/{id}/close`

Cierra un ciclo (cambia `status` a `closed`).

---

#### `POST /api/performance/cycles/{id}/calibrate`

Ejecuta calibración IA sobre todos los reviews del ciclo.

**Respuesta de ejemplo**

```json
{
    "data": {
        "calibrated_count": 42,
        "distribution": { "top": 8, "good": 26, "needs_improvement": 8 }
    }
}
```

---

#### `GET /api/performance/cycles/{id}/insights`

Genera insights IA del ciclo (resumen ejecutivo, tendencias, alertas).

**Respuesta de ejemplo**

```json
{
    "data": {
        "summary": "El equipo de Engineering superó el promedio organizacional...",
        "alerts": ["3 empleados en riesgo de bajo desempeño recurrente"],
        "top_performers": [101, 204, 87]
    }
}
```

---

### Reviews

#### `GET /api/performance/cycles/{cycleId}/reviews`

Lista los reviews de un ciclo. Incluye datos de persona y evaluador.

**Respuesta de ejemplo**

```json
{
    "data": [
        {
            "id": 15,
            "cycle_id": 3,
            "people_id": 101,
            "self_score": 82,
            "manager_score": 79,
            "peer_score": 85,
            "final_score": 81.4,
            "status": "completed",
            "person": { "id": 101, "first_name": "Ana", "last_name": "Gómez" },
            "reviewer": { "id": 5, "name": "Carlos M." }
        }
    ]
}
```

---

#### `POST /api/performance/cycles/{cycleId}/reviews`

Crea un review para un empleado dentro de un ciclo.

**Body (JSON)**

```json
{
    "people_id": 101,
    "self_score": 82,
    "manager_score": 79,
    "peer_score": 85
}
```

| Campo           | Tipo    | Requerido | Descripción                     |
| --------------- | ------- | --------- | ------------------------------- |
| `people_id`     | integer | Sí        | ID del empleado evaluado.       |
| `self_score`    | number  | No        | Autoevaluación (0–100).         |
| `manager_score` | number  | No        | Evaluación del manager (0–100). |
| `peer_score`    | number  | No        | Evaluación de pares (0–100).    |

El campo `final_score` es calculado automáticamente por la IA.  
**Respuesta:** `201 Created`.

---

#### `PATCH /api/performance/cycles/{cycleId}/reviews/{reviewId}`

Actualiza scores o estado de un review existente.

**Body (JSON)** — todos los campos son opcionales:

```json
{
    "self_score": 88,
    "manager_score": 80,
    "peer_score": 83,
    "status": "calibrated"
}
```

Valores permitidos para `status`: `pending`, `in_progress`, `completed`, `calibrated`.

---

## 3. Org Chart — People

Árbol jerárquico de la organización basado en las relaciones `supervised_by` (personas) y `parent_id` (departamentos).

### `GET /api/org-chart/people`

Árbol completo de personas (raíz = empleados sin supervisor en la org).

| Parámetro | Tipo   | Requerido | Descripción                                                                |
| --------- | ------ | --------- | -------------------------------------------------------------------------- |
| `view`    | string | No        | `departments` devuelve el árbol de departamentos en lugar del de personas. |

**Respuesta de ejemplo — vista personas**

```json
{
  "view": "people",
  "nodes": [
    {
      "id": 1,
      "name": "María Rodríguez",
      "department_id": 2,
      "is_high_potential": true,
      "photo_url": "https://...",
      "depth": 0,
      "direct_reports_count": 4,
      "children": [ ... ]
    }
  ],
  "meta": { "total": 120 }
}
```

**Respuesta de ejemplo — vista departamentos** (`?view=departments`)

```json
{
    "view": "departments",
    "nodes": [
        {
            "id": 1,
            "name": "Engineering",
            "manager_id": 5,
            "children": [
                { "id": 3, "name": "Backend", "manager_id": 12, "children": [] }
            ]
        }
    ],
    "meta": { "total": 8 }
}
```

---

### `GET /api/org-chart/people/stats`

Estadísticas rápidas de la estructura organizacional.

**Sin parámetros de query.**

**Respuesta de ejemplo**

```json
{
    "total_employees": 120,
    "total_managers": 18,
    "avg_span_of_control": 4.67,
    "max_depth": 5,
    "top_departments": {
        "2": 34,
        "5": 28,
        "1": 22,
        "7": 19,
        "3": 17
    }
}
```

---

### `GET /api/org-chart/people/{id}/subtree`

Subárbol jerárquico con raíz en una persona específica.

**Respuesta de ejemplo**

```json
{
  "root": {
    "id": 5,
    "name": "Carlos Mendoza",
    "department_id": 1,
    "is_high_potential": false,
    "photo_url": null,
    "depth": 0,
    "direct_reports_count": 3,
    "children": [ ... ]
  }
}
```

---

## Resumen de endpoints

| Método | Ruta                                                   | Descripción                   |
| ------ | ------------------------------------------------------ | ----------------------------- |
| GET    | `/api/skill-intelligence/heatmap`                      | Heatmap departamento × skill  |
| GET    | `/api/skill-intelligence/top-gaps`                     | Top N brechas de habilidad    |
| GET    | `/api/skill-intelligence/upskilling`                   | Recomendaciones de upskilling |
| GET    | `/api/skill-intelligence/coverage`                     | Cobertura de habilidades      |
| GET    | `/api/performance/cycles`                              | Listar ciclos                 |
| POST   | `/api/performance/cycles`                              | Crear ciclo                   |
| GET    | `/api/performance/cycles/{id}`                         | Ver ciclo                     |
| POST   | `/api/performance/cycles/{id}/activate`                | Activar ciclo                 |
| POST   | `/api/performance/cycles/{id}/close`                   | Cerrar ciclo                  |
| POST   | `/api/performance/cycles/{id}/calibrate`               | Calibrar ciclo (IA)           |
| GET    | `/api/performance/cycles/{id}/insights`                | Insights IA del ciclo         |
| GET    | `/api/performance/cycles/{cycleId}/reviews`            | Listar reviews de un ciclo    |
| POST   | `/api/performance/cycles/{cycleId}/reviews`            | Crear review                  |
| PATCH  | `/api/performance/cycles/{cycleId}/reviews/{reviewId}` | Actualizar review             |
| GET    | `/api/org-chart/people`                                | Árbol org completo            |
| GET    | `/api/org-chart/people/stats`                          | Estadísticas org              |
| GET    | `/api/org-chart/people/{id}/subtree`                   | Subárbol de una persona       |

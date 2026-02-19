# 🚀 Fase 5: Learning Paths & Desarrollo Inteligente

## 🎯 Objetivo

Transformar el diagnóstico (Fase 4: Talento 360) en acción. El sistema no solo debe decir "te falta Liderazgo", sino "aquí tienes un plan para mejorar Liderazgo, y Roberto (CFO) puede ser tu mentor".

## 🏗️ Arquitectura de la Solución

### 1. Modelo de Datos (Refinamiento)

Aprovecharemos los modelos existentes `DevelopmentPath` y `DevelopmentAction`, enriqueciéndolos con lógica de negocio.

- **`DevelopmentPath`**: Contenedor del plan (ej. "Plan de Cierre de Brechas 2026").
- **`DevelopmentAction`**: Acciones atómicas. Tipos:
    - `course`: Curso o recurso educativo.
    - `mentorship`: Sesión con un experto interno.
    - `project`: Asignación a un proyecto práctico.
    - `assessment`: Nueva evaluación para verificar progreso.

### 2. Servicios Core

#### A. `MentorMatchingService`

Motor de búsqueda de talento interno para transferencia de conocimiento.

- **Input**: `skill_id`, `min_level` (default 4).
- **Lógica**: Busca empleados activos que tengan la habilidad validada (`verified=true`) en nivel experto.
- **Ranking**: Prioriza mentores con buen desempeño (`performance_rating`) y disponibilidad.

#### B. `SmartPathGeneratorService`

Generador de planes automáticos basado en la magnitud de la brecha.

- **Gap Crítico (> 2 niveles)**: Sugiere estrategia "70-20-10" (Experiencia, Acompañamiento, Formación).
    - Acción 1: Curso intensivo (Build).
    - Acción 2: Mentoría recurrente (Borrow Internal).
    - Acción 3: Asignación temporal a proyecto (Apply).
- **Gap Leve (<= 1 nivel)**:
    - Acción 1: Micro-learning o recurso asíncrono.
    - Acción 2: Peer review.

### 3. API Endpoints

- `GET /api/talent/mentors/suggest?skill_id=123`: Retorna candidatos a mentores.
- `POST /api/development-paths/generate`: Crea un plan borrador basado en los resultados del 360°.

### 4. Interfaz de Usuario

- **Tab "Desarrollo"** en Perfil de Usuario.
- **Kanban de Desarrollo**: Visualización de acciones (To Do, In Progress, Done).
- **Tarjeta de Mentor**: Muestra perfil del mentor sugerido con botón "Solicitar Mentoría".

---

## 📅 Plan de Implementación

1.  **Backend**: Servicio de Mentores (`MentorMatchingService`).
2.  **Backend**: Generador de Paths (`DevelopmentPathService`).
3.  **Frontend**: Vista de Plan de Desarrollo en `People/Show.vue`.

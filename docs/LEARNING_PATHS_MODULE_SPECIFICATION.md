# Especificación del Módulo Learning Paths

**Fecha:** 3 de enero de 2026  
**Versión:** 1.0  
**Estado:** En desarrollo

---

## 1. Objetivo del Módulo

### 1.1 Propósito Principal
Generar rutas de aprendizaje personalizadas y estructuradas para cerrar brechas de competencias identificadas mediante Gap Analysis, proporcionando un plan de desarrollo progresivo que eleve cada skill desde el nivel actual hasta el nivel requerido por el rol objetivo.

### 1.2 Alcance
- **Incluye:**
  - Generación automática de rutas basadas en análisis de brechas
  - Pasos de aprendizaje específicos por skill
  - Cálculo de duración estimada
  - Priorización inteligente de skills
  - Visualización tipo timeline
  - Gestión de múltiples rutas por persona

- **No Incluye (Post-MVP):**
  - Tracking de progreso en tiempo real
  - Integración con plataformas LMS
  - Gamificación y badges
  - Recomendaciones de contenido específico
  - Notificaciones y recordatorios

---

## 2. Arquitectura y Flujo de Datos

### 2.1 Flujo Principal

```
Usuario → Gap Analysis → Botón "Generar Ruta" → API Call
    ↓
POST /api/development-paths/generate
    ↓
DevelopmentPathService.generate(people, role)
    ↓
1. Obtener gaps del GapAnalysisService
2. Filtrar skills con gap > 0
3. Priorizar skills (críticas primero, mayor gap primero)
4. Generar pasos por cada skill
5. Calcular duración total
6. Crear DevelopmentPath con steps
    ↓
Response: DevelopmentPath creado
    ↓
Redirect → /learning-paths
```

### 2.2 Componentes

```
Frontend:
├── GapAnalysis/Index.vue (botón generador)
└── LearningPaths/Index.vue (visualización)

Backend:
├── Controllers/
│   ├── GapAnalysisController.php
│   └── DevelopmentPathController.php
├── Services/
│   ├── GapAnalysisService.php
│   └── DevelopmentPathService.php
└── Models/
    ├── DevelopmentPath.php
    └── People.php
```

---

## 3. Lógica de Negocio Detallada

### 3.1 Generación de Pasos por Skill

Para cada skill con brecha (gap > 0):

#### **Regla de Tipos de Acción por Brecha:**

| Gap | Tipo de Acciones | Duración Base |
|-----|------------------|---------------|
| 1   | `reading` | 15-20 días |
| 2   | `course` + `practice` | 45-50 días |
| 3   | `course` + `mentorship` + `project` | 75-90 días |
| 4+  | `course` + `mentorship` + `project` + `workshop` | 100-120 días |

**Skills críticas:** Agregar `certification` al final (+15 días)

#### **Ejemplo Práctico:**

**Skill:** JavaScript  
**Nivel actual:** 2  
**Nivel requerido:** 4  
**Gap:** 2  
**Es crítica:** Sí

**Pasos generados:**
1. `course`: "JavaScript Intermedio a Avanzado" (30 días)
2. `practice`: "Desarrollo de 3 proyectos prácticos" (20 días)
3. `certification`: "JavaScript Developer Certification" (15 días)

**Total:** 65 días

### 3.2 Cálculo de Duración

```javascript
const calculateDuration = (gap, isCritical) => {
  let baseDays = 0;
  
  switch(gap) {
    case 1:
      baseDays = 15 + random(0, 5);
      break;
    case 2:
      baseDays = 45 + random(0, 5);
      break;
    case 3:
      baseDays = 75 + random(0, 15);
      break;
    default: // 4+
      baseDays = 100 + random(0, 20);
  }
  
  if (isCritical) {
    baseDays += 15; // Certification
  }
  
  return baseDays;
}
```

### 3.3 Priorización de Skills

**Orden de ejecución:**

1. **Skills críticas con mayor gap** (descendente)
2. **Skills críticas con menor gap** (descendente)
3. **Skills no críticas con mayor gap** (descendente)
4. **Skills no críticas con menor gap** (descendente)

```php
// Pseudo-código
$gaps->sortBy([
    ['is_critical', 'desc'],
    ['gap', 'desc'],
    ['skill_name', 'asc']
]);
```

### 3.4 Estructura de Pasos

Cada paso contiene:

```json
{
  "order": 1,
  "action_type": "course",
  "skill_id": 5,
  "skill_name": "JavaScript",
  "description": "Curso intensivo de JavaScript avanzado con enfoque en ES6+ y patrones de diseño",
  "estimated_duration_days": 30,
  "status": "pending",
  "resources": [] // Post-MVP
}
```

---

## 4. Modelos de Datos

### 4.1 DevelopmentPath (development_paths table)

```sql
CREATE TABLE development_paths (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    people_id BIGINT UNSIGNED NOT NULL,
    target_role_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    estimated_duration_months DECIMAL(5,2),
    steps JSON NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (people_id) REFERENCES peoples(id),
    FOREIGN KEY (target_role_id) REFERENCES roles(id)
);
```

**Campos:**
- `people_id`: Persona que seguirá la ruta
- `target_role_id`: Rol objetivo
- `status`: `pending` | `in_progress` | `completed` | `cancelled`
- `estimated_duration_months`: Duración total en meses
- `steps`: Array JSON con los pasos detallados

### 4.2 JSON Schema de Steps

```json
{
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "order": {"type": "integer"},
      "action_type": {
        "type": "string",
        "enum": ["course", "mentorship", "project", "certification", "workshop", "reading", "practice"]
      },
      "skill_id": {"type": "integer"},
      "skill_name": {"type": "string"},
      "description": {"type": "string"},
      "estimated_duration_days": {"type": "integer"},
      "status": {
        "type": "string",
        "enum": ["pending", "in_progress", "completed", "skipped"]
      }
    },
    "required": ["order", "action_type", "skill_name", "description", "estimated_duration_days"]
  }
}
```

---

## 5. Endpoints API

### 5.1 POST /api/development-paths/generate

**Descripción:** Genera una nueva ruta de aprendizaje basada en gap analysis

**Request:**
```json
{
  "people_id": 1,
  "role_id": 3
}
```

**Response (201 Created):**
```json
{
  "id": 12,
  "status": "pending",
  "estimated_duration_months": 4.5,
  "steps": [
    {
      "order": 1,
      "action_type": "course",
      "skill_id": 5,
      "skill_name": "JavaScript",
      "description": "Curso avanzado de JavaScript",
      "estimated_duration_days": 30,
      "status": "pending"
    }
  ],
  "people": {
    "id": 1,
    "name": "Ana García"
  },
  "target_role": {
    "id": 3,
    "name": "Senior Frontend Developer"
  }
}
```

### 5.2 GET /api/development-paths

**Descripción:** Lista todas las rutas de aprendizaje

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 12,
      "people_id": 1,
      "people_name": "Ana García",
      "target_role_id": 3,
      "target_role_name": "Senior Frontend Developer",
      "status": "pending",
      "estimated_duration_months": 4.5,
      "steps": [...],
      "created_at": "2026-01-03T10:30:00Z"
    }
  ]
}
```

### 5.3 GET /api/development-paths/{id}

**Descripción:** Detalle de una ruta específica

**Response (200 OK):**
```json
{
  "id": 12,
  "people": {...},
  "target_role": {...},
  "status": "pending",
  "estimated_duration_months": 4.5,
  "steps": [...],
  "created_at": "2026-01-03T10:30:00Z",
  "updated_at": "2026-01-03T10:30:00Z"
}
```

---

## 6. Componentes Frontend

### 6.1 GapAnalysis/Index.vue

**Nueva funcionalidad:**
- Botón "Generar ruta de aprendizaje"
- Loading state durante generación
- Redirección a /learning-paths tras éxito

**Código relevante:**
```vue
<v-btn
  color="success"
  variant="tonal"
  @click="generateLearningPath"
  :loading="generatingPath"
  prepend-icon="mdi-map-marker-path"
>
  Generar ruta de aprendizaje
</v-btn>
```

### 6.2 LearningPaths/Index.vue

**Características:**
- Lista de rutas con accordion expandible
- Timeline visual usando v-timeline
- Chips de estado por tipo de acción
- Resumen de duración y pasos
- Empty state cuando no hay rutas

**Componentes Vuetify utilizados:**
- `v-card` con variant="outlined"
- `v-timeline` para visualización temporal
- `v-chip` con colores por tipo de acción
- `v-expand-transition` para acordeón

---

## 7. Casos de Uso

### 7.1 Caso de Uso Principal

**Actor:** Gerente de Talento  
**Objetivo:** Crear plan de desarrollo para un empleado

**Flujo:**
1. Usuario accede a Gap Analysis
2. Selecciona empleado: "Ana García"
3. Selecciona rol objetivo: "Senior Frontend Developer"
4. Hace clic en "Analizar brechas"
5. Revisa resultados (match: 65%, 4 skills con brechas)
6. Hace clic en "Generar ruta de aprendizaje"
7. Sistema genera ruta con 12 pasos, 135 días
8. Usuario es redirigido a Learning Paths
9. Ve la ruta generada en el timeline

### 7.2 Escenarios Específicos

#### Escenario A: Skills críticas prioritarias
- **Entrada:** JavaScript (gap=3, crítica), CSS (gap=2, no crítica)
- **Salida:** JavaScript primero (90 días), luego CSS (45 días)

#### Escenario B: Sin brechas (match perfecto)
- **Entrada:** Gap Analysis con 100% match
- **Salida:** No se genera ruta, mensaje de confirmación

#### Escenario C: Múltiples rutas para la misma persona
- **Permitido:** Sí
- **Uso:** Persona puede tener rutas hacia diferentes roles

---

## 8. Validaciones y Reglas de Negocio

### 8.1 Validaciones

- `people_id` debe existir en tabla `peoples`
- `role_id` debe existir en tabla `roles`
- No generar ruta si match_percentage = 100%
- Mínimo 1 skill con gap > 0

### 8.2 Reglas de Negocio

- **Duración máxima recomendada:** 12 meses (360 días)
- **Número máximo de pasos:** 20
- **Skills críticas siempre van primero**
- **Gap = 0 no genera pasos**
- **Un paso por acción** (no combinar course + practice en un paso)

---

## 9. Tipos de Acciones y sus Características

| Tipo | Ícono | Color | Descripción |
|------|-------|-------|-------------|
| `course` | `mdi-book-open-variant` | `primary` | Curso online estructurado |
| `mentorship` | `mdi-human-greeting-variant` | `info` | Sesiones con mentor/coach |
| `project` | `mdi-folder-multiple` | `success` | Proyecto real hands-on |
| `certification` | `mdi-certificate` | `warning` | Certificación oficial |
| `workshop` | `mdi-presentation` | `secondary` | Taller/workshop presencial |
| `reading` | `mdi-book` | `accent` | Lectura y estudio individual |
| `practice` | `mdi-code-braces` | `error` | Práctica y ejercicios |

---

## 10. Ejemplos de Descripciones por Tipo

### Course
- "Curso intensivo de [skill] desde nivel [actual] hasta [requerido]"
- "Formación avanzada en [skill] con enfoque práctico"

### Mentorship
- "Mentoría personalizada para dominar [skill]"
- "Sesiones 1-on-1 con experto en [skill]"

### Project
- "Desarrollo de proyecto real aplicando [skill]"
- "Implementación práctica de [skill] en caso de uso empresarial"

### Certification
- "Certificación oficial en [skill]"
- "Preparación y examen de certificación [skill]"

### Practice
- "Ejercicios prácticos de [skill]"
- "Desarrollo de [N] proyectos pequeños en [skill]"

---

## 11. Métricas y KPIs (Futuro)

- Tiempo promedio de completación
- Tasa de abandono por paso
- Skills más desarrolladas
- Efectividad de cada tipo de acción
- ROI de rutas completadas

---

## 12. Roadmap Post-MVP

### Fase 2 (Q1 2026): Sistema Híbrido con Templates por Rol

#### 12.1 Objetivo
Implementar un sistema de **Role Learning Templates** que permita:
- Crear rutas maestras/plantillas por rol
- Generar rutas personalizadas masivamente
- Mantener personalización según nivel actual de cada persona

#### 12.2 Arquitectura del Sistema Híbrido

**Nuevo flujo:**
```
1. Crear Template por Rol (una vez)
   ├─ Definir todas las skills del rol
   ├─ Crear pasos desde nivel 0 → nivel requerido
   └─ Guardar como template reutilizable

2. Generación Masiva (para múltiples personas)
   ├─ Seleccionar rol
   ├─ Seleccionar personas (multi-select)
   ├─ Por cada persona:
   │   ├─ Obtener gap analysis
   │   ├─ Filtrar pasos del template según gaps reales
   │   └─ Crear ruta personalizada
   └─ Resultado: N rutas personalizadas en segundos
```

#### 12.3 Nuevos Modelos de Datos

```sql
-- Templates maestros por rol
CREATE TABLE role_learning_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    template_steps JSON NOT NULL,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Relación entre rutas personalizadas y templates
ALTER TABLE development_paths 
ADD COLUMN based_on_template_id BIGINT UNSIGNED NULL,
ADD FOREIGN KEY (based_on_template_id) REFERENCES role_learning_templates(id);
```

#### 12.4 Schema de Template Steps

```json
{
  "skills": [
    {
      "skill_id": 5,
      "skill_name": "JavaScript",
      "target_level": 5,
      "is_critical": true,
      "progression": [
        {
          "from_level": 0,
          "to_level": 1,
          "action_type": "reading",
          "description": "Fundamentos de JavaScript",
          "duration_days": 15
        },
        {
          "from_level": 1,
          "to_level": 2,
          "action_type": "course",
          "description": "JavaScript básico a intermedio",
          "duration_days": 30
        },
        {
          "from_level": 2,
          "to_level": 3,
          "action_type": "course",
          "description": "JavaScript avanzado",
          "duration_days": 30
        },
        {
          "from_level": 3,
          "to_level": 4,
          "action_type": "mentorship",
          "description": "Mentoría en patrones avanzados",
          "duration_days": 45
        },
        {
          "from_level": 4,
          "to_level": 5,
          "action_type": "project",
          "description": "Proyecto real enterprise",
          "duration_days": 60
        },
        {
          "from_level": 4,
          "to_level": 5,
          "action_type": "certification",
          "description": "JavaScript Expert Certification",
          "duration_days": 15
        }
      ]
    }
  ]
}
```

#### 12.5 Nuevos Endpoints API

**POST /api/role-learning-templates**
Crear template maestro por rol

```json
Request:
{
  "role_id": 3,
  "name": "Senior Frontend Developer Learning Path",
  "description": "Complete learning journey from junior to senior",
  "skills": [...]
}

Response (201):
{
  "id": 5,
  "role_id": 3,
  "name": "Senior Frontend Developer Learning Path",
  "skills_count": 8,
  "total_steps": 45,
  "estimated_duration_days": 450
}
```

**GET /api/role-learning-templates**
Listar todos los templates

**GET /api/role-learning-templates/{id}**
Detalle de un template

**POST /api/development-paths/generate-bulk**
Generación masiva desde template

```json
Request:
{
  "template_id": 5,
  "people_ids": [1, 2, 3, 4, 5],
  "auto_filter": true  // Filtrar pasos según nivel actual
}

Response (201):
{
  "generated_count": 5,
  "paths": [
    {
      "id": 101,
      "people_id": 1,
      "people_name": "Ana García",
      "steps_count": 12,
      "estimated_duration_days": 180
    },
    {
      "id": 102,
      "people_id": 2,
      "people_name": "Carlos López",
      "steps_count": 8,
      "estimated_duration_days": 120
    }
  ]
}
```

#### 12.6 Lógica de Filtrado Inteligente

```php
class TemplateBasedPathGenerator
{
    public function generateFromTemplate(
        RoleLearningTemplate $template, 
        People $person
    ): DevelopmentPath {
        // 1. Obtener gap analysis
        $gaps = $this->gapAnalysisService->calculate($person, $template->role);
        
        // 2. Inicializar pasos filtrados
        $filteredSteps = [];
        $order = 1;
        
        // 3. Por cada skill del template
        foreach ($template->skills as $skillTemplate) {
            // Encontrar gap real de la persona
            $gap = $gaps->firstWhere('skill_id', $skillTemplate['skill_id']);
            
            if (!$gap || $gap->gap <= 0) {
                continue; // Skip si ya cumple
            }
            
            $currentLevel = $gap->current_level;
            $targetLevel = $gap->required_level;
            
            // 4. Filtrar solo los pasos necesarios
            foreach ($skillTemplate['progression'] as $step) {
                // Solo incluir pasos dentro del rango necesario
                if ($step['from_level'] >= $currentLevel && 
                    $step['to_level'] <= $targetLevel) {
                    
                    $filteredSteps[] = [
                        'order' => $order++,
                        'action_type' => $step['action_type'],
                        'skill_id' => $skillTemplate['skill_id'],
                        'skill_name' => $skillTemplate['skill_name'],
                        'description' => $step['description'],
                        'estimated_duration_days' => $step['duration_days'],
                        'status' => 'pending'
                    ];
                }
            }
        }
        
        // 5. Crear ruta personalizada
        return DevelopmentPath::create([
            'people_id' => $person->id,
            'target_role_id' => $template->role_id,
            'based_on_template_id' => $template->id,
            'steps' => $filteredSteps,
            'status' => 'pending',
            'estimated_duration_months' => $this->calculateMonths($filteredSteps)
        ]);
    }
}
```

#### 12.7 Ejemplo Práctico del Filtrado

**Template "Senior Frontend Developer":**
```
JavaScript:
  0→1: reading (15 días)
  1→2: course (30 días)
  2→3: course advanced (30 días)
  3→4: mentorship (45 días)
  4→5: project (60 días)
  4→5: certification (15 días)
```

**Persona Ana (nivel actual: 3):**
```
Gap Analysis: JavaScript actual=3, requerido=5, gap=2

Pasos filtrados automáticamente:
✓ 3→4: mentorship (45 días)
✓ 4→5: project (60 días)
✓ 4→5: certification (15 días)

Total: 120 días (vs 195 días del template completo)
```

#### 12.8 Componentes Frontend Nuevos

**TemplateCatalog.vue**
- Lista de templates disponibles
- Visualización de skills y pasos
- Botón "Generar rutas masivas"

**TemplateBuilder.vue**
- Interface drag-and-drop para crear templates
- Editor visual de progresión por skill
- Preview del timeline completo

**BulkPathGenerator.vue**
- Selector de template
- Multi-select de personas
- Preview de rutas a generar
- Confirmación y generación

#### 12.9 Beneficios del Sistema Híbrido

**Para HR/Training:**
- ✅ Crear una vez, usar muchas veces
- ✅ Estandarizar programas de desarrollo
- ✅ Planificar presupuestos anuales
- ✅ Onboarding masivo de equipos

**Para Empleados:**
- ✅ Mantiene personalización total
- ✅ No repite lo que ya sabe
- ✅ Duración optimizada
- ✅ Camino claro y validado

**Para Sistema:**
- ✅ Escalable a cientos de personas
- ✅ Reutilización de lógica
- ✅ Mantenimiento centralizado
- ✅ Trazabilidad de cambios

#### 12.10 Casos de Uso Post-MVP

**Caso 1: Onboarding de 10 Juniors**
1. HR selecciona template "Junior to Mid Developer"
2. Selecciona 10 nuevos empleados
3. Sistema genera 10 rutas personalizadas
4. Cada uno ve solo lo que necesita según su nivel

**Caso 2: Upskilling de Equipo**
1. Manager detecta necesidad de React en equipo de 15
2. Selecciona template "React Specialist"
3. Genera rutas para los 15
4. Algunos tienen 2 meses, otros 6 meses (según nivel actual)

**Caso 3: Actualización de Template**
1. Nueva versión de JavaScript lanzada
2. HR actualiza template agregando "Modern JS 2026"
3. Opcionalmente: regenera rutas activas
4. Empleados ven nuevo paso añadido

#### 12.11 Métricas Adicionales con Templates

- Templates más usados
- Efectividad de cada template (% completado)
- ROI por template
- Tiempo promedio de completación vs estimado
- Skills más desarrolladas por template

---

### Fase 3 (Q1-Q2 2026)
- [ ] Tracking de progreso por paso
- [ ] Marcado manual de pasos completados
- [ ] Visualización de % completado
- [ ] Dashboard de progreso grupal

### Fase 4 (Q2 2026)
- [ ] Integración con plataformas LMS (Udemy, Coursera)
- [ ] Recomendaciones de recursos específicos por paso
- [ ] Asignación automática de mentores
- [ ] Marketplace de recursos internos

### Fase 5 (Q3 2026)
- [ ] Notificaciones y recordatorios automáticos
- [ ] Gamificación (badges, puntos, leaderboards)
- [ ] Reportes gerenciales avanzados
- [ ] Analytics predictivos de completación
- [ ] Sistema de endorsement entre pares

---

## 13. Notas Técnicas

### 13.1 Performance
- Generación de ruta: < 2 segundos
- Cache de gaps para evitar recálculos
- JSON indexing en campo `steps`

### 13.2 Seguridad
- Validar que el usuario tenga permisos para ver la ruta
- No exponer información sensible en pasos
- Audit log de cambios de estado

### 13.3 Escalabilidad
- Diseño preparado para millones de rutas
- Particionamiento por fecha de creación
- Archivado de rutas antiguas

---

## 14. Glosario

- **Gap:** Diferencia entre nivel actual y requerido
- **Path:** Ruta o camino de aprendizaje
- **Step:** Paso individual dentro de una ruta
- **Action Type:** Tipo de actividad de aprendizaje
- **Critical Skill:** Competencia marcada como crítica para el rol
- **Match Percentage:** Porcentaje de compatibilidad entre perfil y rol

---

**Fin de Especificación**

Autor: GitHub Copilot  
Revisión: Pendiente  
Aprobación: Pendiente

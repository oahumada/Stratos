# 📘 Guía del Diseñador de Cursos — Stratos LMS

> **Módulo:** Diseñador de Cursos (Course Designer)  
> **Versión:** v0.13.0 · 4 Abr 2026  
> **Audiencia:** L&D Managers, HR Leaders, Administradores  
> **Ruta de acceso:** Sidebar → Diseñador de Cursos (`/lms/course-designer`)

---

## Visión general

El Diseñador de Cursos de Stratos es una herramienta de **diseño instruccional asistido por IA**. A diferencia de un editor de contenido tradicional, este módulo orquesta agentes de inteligencia artificial especializados para generar la estructura pedagógica, el contenido y la evaluación de un curso completo — y luego lo persiste como un curso publicable en el LMS.

El flujo está diseñado como un **wizard de 5 pasos** donde la IA propone y el humano dispone.

---

## Agentes que intervienen en el proceso

El diseño de cursos no depende de un solo agente — es un esfuerzo **multi-agente** donde cada especialista contribuye su expertise en el momento correcto:

### Mapa de agentes × etapas del diseño

| Paso | Tarea                              | Agente                           | Rol del agente                              | Cómo contribuye                                                                                                                                   |
| :--: | ---------------------------------- | -------------------------------- | ------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
|  1   | **Diseño del outline**             | 🧠 **Arquitecto de Aprendizaje** | Experto en diseño instruccional (ADDIE/SAM) | Genera: outline del curso, objetivos de aprendizaje, estructura de módulos con lecciones, plan de evaluación — todo adaptado al nivel y audiencia |
|  1   | **Identificación de brechas**      | 📊 **Estratega de Talento**      | Analista de gap analysis y skill mapping    | Informa las brechas de skills que el curso debe cerrar (input desde Skill Intelligence)                                                           |
|  2   | **Validación de competencias**     | 🔬 **Curador de Competencias**   | Estandarizador de taxonomías y niveles      | Valida que los objetivos de aprendizaje estén alineados con el marco de competencias de la organización                                           |
|  3   | **Generación de contenido**        | ✍️ **ContentAgentService**       | Motor de contenido IA                       | Genera artículos HTML, guiones de video, ejercicios prácticos — por cada lección individualmente                                                  |
|  4   | **Configuración de certificación** | —                                | (Configuración manual)                      | El L&D Manager define la política de certificación, XP y template                                                                                 |
|  5   | **Revisión pedagógica**            | 🧠 **Arquitecto de Aprendizaje** | Revisor instruccional                       | Evalúa el curso completo: estructura, progresión, cobertura, balance de actividades → score 0-100 + feedback                                      |
| Post | **Publicación y enrollment**       | ⚙️ **Operador LMS**              | Automatizador operativo                     | Crea cuentas, envía invitaciones, gestiona enrollments y emite certificados al completar                                                          |
| Post | **Seguimiento de learners**        | 🏋️ **Coach de Crecimiento**      | Mentor digital de seguimiento               | Monitorea el progreso de los learners, valida evidencias, conecta con mentores internos                                                           |
| Post | **Comunidad de aprendizaje**       | 🤝 **Facilitador de Comunidades** | Experto en CoP, CoI, Connectivism, LPP      | Diseña y nutre comunidades de práctica alineadas con brechas de skills, gestiona progresión de roles, mide salud de la comunidad y sugiere mentorías |

---

## Flujo paso a paso

### Paso 1 — 🧠 Diseño IA

**Qué hace:** El Arquitecto de Aprendizaje genera la estructura pedagógica completa del curso a partir de inputs mínimos.

**Inputs que debes proporcionar:**

| Campo                              | Descripción                                 | Ejemplo                                                       |
| ---------------------------------- | ------------------------------------------- | ------------------------------------------------------------- |
| **Tema**                           | El tema central del curso                   | "Liderazgo ágil para mandos medios"                           |
| **Audiencia objetivo**             | A quién va dirigido                         | "Jefes de equipo y coordinadores con 2-5 años de experiencia" |
| **Brechas de skills** _(opcional)_ | Skills específicos que el curso debe cubrir | "comunicación asertiva, delegación, feedback efectivo"        |
| **Duración objetivo**              | Duración total en horas                     | 8 horas                                                       |
| **Nivel**                          | Dificultad                                  | Beginner / Intermediate / Advanced                            |

**Lo que la IA genera:**

```json
{
    "course_outline": "Curso de 8 horas diseñado para desarrollar...",
    "learning_objectives": [
        "Aplicar principios ágiles en la gestión de equipos",
        "Desarrollar habilidades de delegación efectiva",
        "Implementar ciclos de feedback continuo"
    ],
    "modules": [
        {
            "title": "Fundamentos del Liderazgo Ágil",
            "description": "Introducción a los principios...",
            "lessons": [
                {
                    "title": "¿Qué es el liderazgo ágil?",
                    "duration_minutes": 30,
                    "content_type": "article"
                },
                {
                    "title": "Caso de estudio: Spotify",
                    "duration_minutes": 20,
                    "content_type": "video"
                },
                {
                    "title": "Autoevaluación de estilo",
                    "duration_minutes": 15,
                    "content_type": "exercise"
                }
            ]
        }
    ],
    "assessment_plan": "Evaluación continua con quiz por módulo + proyecto final integrador"
}
```

**💡 Tip:** Si conectas las brechas de skills desde el módulo de Skill Intelligence, el Arquitecto diseña un curso que cierra exactamente esas brechas — no un curso genérico.

---

### Paso 2 — 📐 Estructura

**Qué hace:** Editas la estructura generada por la IA — ajustas, agregas o eliminas módulos y lecciones.

**Acciones disponibles:**

| Acción                    | Cómo                                               |
| ------------------------- | -------------------------------------------------- |
| Editar título de módulo   | Click en el título → editar inline                 |
| Agregar módulo            | Botón "Agregar módulo" al final de la lista        |
| Eliminar módulo           | Ícono de papelera en la esquina del módulo         |
| Reordenar módulos         | Flechas arriba/abajo en cada módulo                |
| Agregar lección           | Botón "Agregar lección" dentro del módulo          |
| Cambiar tipo de contenido | Select por lección: article, video, exercise, quiz |
| Ajustar duración          | Número de minutos por lección                      |

**Tipos de contenido disponibles:**

| Tipo       | Descripción                                             | Uso recomendado                         |
| ---------- | ------------------------------------------------------- | --------------------------------------- |
| `article`  | Artículo HTML con texto, imágenes y código              | Contenido conceptual, guías paso a paso |
| `video`    | Guión de video (la IA genera el script, no el video)    | Explicaciones visuales, demostraciones  |
| `exercise` | Ejercicio práctico con instrucciones                    | Aplicación de lo aprendido              |
| `quiz`     | Evaluación con preguntas _(futuro: banco de preguntas)_ | Validación de conocimientos             |

---

### Paso 3 — ✍️ Contenido

**Qué hace:** Genera el contenido de cada lección individualmente con IA.

**Flujo por lección:**

```
1. Selecciona una lección pendiente
2. Click "Generar contenido IA"
3. La IA genera HTML basado en:
   - Título de la lección
   - Contexto del módulo
   - Tema general del curso
   - Tipo de contenido (article/video_script/exercise)
4. Revisa el contenido generado (preview HTML)
5. Edita manualmente si es necesario (textarea)
6. La lección queda marcada como "generado" o "editado"
```

**Estados de cada lección:**

| Estado       | Significado                    | Color    |
| ------------ | ------------------------------ | -------- |
| 🔴 Pendiente | Sin contenido generado         | Rojo     |
| 🟡 Generado  | Contenido IA sin editar        | Amarillo |
| 🟢 Editado   | Revisado/editado por el humano | Verde    |

**💡 Tip:** No necesitas generar contenido para todas las lecciones de tipo `video` si ya tienes los videos grabados — simplemente pega la URL en `content_url` y deja `content_body` vacío.

---

### Paso 4 — ⚙️ Configuración

**Qué hace:** Defines los metadatos del curso y la política de certificación.

**Campos de configuración:**

| Sección           | Campo                     | Descripción                                                   |
| ----------------- | ------------------------- | ------------------------------------------------------------- |
| **Metadata**      | Título                    | Nombre visible del curso                                      |
|                   | Descripción               | Resumen para el catálogo                                      |
|                   | Categoría                 | Área temática (ej: Liderazgo, Tech, Compliance)               |
|                   | Nivel                     | Beginner / Intermediate / Advanced                            |
| **Gamificación**  | XP del curso              | Puntos de experiencia al completar (default: 50)              |
| **Certificación** | % mínimo de recursos      | Slider 0-100% (ej: 80% = debe completar 80% de las lecciones) |
|                   | Requiere evaluación       | Switch on/off                                                 |
|                   | Nota mínima de evaluación | Score mínimo para certificar (ej: 70/100)                     |
|                   | Template de certificado   | Selector del catálogo de templates de la organización         |

---

### Paso 5 — 🔍 Revisión y Publicación

**Qué hace:** La IA revisa el curso completo y le asigna un score pedagógico.

**Acciones disponibles:**

| Acción                    | Descripción                                                                                                             |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| **Revisión IA**           | El Arquitecto de Aprendizaje analiza: estructura, progresión pedagógica, cobertura de contenido, balance de actividades |
| **Guardar como Borrador** | Persiste el curso en la BD con `is_active=false` — editable después                                                     |
| **Publicar**              | Persiste con `is_active=true` — visible en el catálogo y listo para enrollment                                          |

**Resultado de la revisión:**

```json
{
    "score": 87,
    "strengths": [
        "Progresión pedagógica clara: de conceptos a práctica",
        "Buen balance entre contenido teórico y ejercicios (60/40)"
    ],
    "improvements": [
        "El módulo 3 tiene lecciones muy largas (>45 min) — dividir en dos",
        "Falta un ejercicio integrador al final del curso"
    ],
    "suggestions": [
        "Agregar un caso de estudio real en el módulo 2",
        "Considerar un foro de discusión entre módulos"
    ]
}
```

**Escala de scores:**

| Score  | Significado      | Recomendación                                           |
| ------ | ---------------- | ------------------------------------------------------- |
| 90-100 | Excelente        | Publicar directamente                                   |
| 75-89  | Bueno            | Publicar, considerar las mejoras en siguiente iteración |
| 60-74  | Aceptable        | Aplicar las mejoras antes de publicar                   |
| < 60   | Necesita trabajo | Rediseñar con los suggestions antes de publicar         |

---

## Qué pasa después de publicar

Una vez publicado, el curso entra al ciclo operativo del LMS donde otros agentes toman el control:

```
PUBLICAR → Operador LMS enrolla learners → Learners completan → Coach hace seguimiento
                                                                        ↓
                                              Certificado emitido automáticamente
                                                        ↓
                                              Analytics actualiza KPIs
```

| Agente                   | Acción post-publicación                                       |
| ------------------------ | ------------------------------------------------------------- |
| **Operador LMS**         | Crea cuentas, envía invitaciones, gestiona enrollments        |
| **Coach de Crecimiento** | Monitorea progreso, valida evidencias, conecta mentores       |
| **Stratos Sentinel**     | Audita que las decisiones de IA sean explicables y sin sesgos |
| **Analytics**            | Trackea completion rate, certification rate, at-risk learners |

---

## API Reference (para integraciones)

| Método | Endpoint                                    | Descripción                      |
| ------ | ------------------------------------------- | -------------------------------- |
| `POST` | `/api/lms/course-designer/generate-outline` | Genera outline con IA            |
| `POST` | `/api/lms/course-designer/generate-content` | Genera contenido de una lección  |
| `POST` | `/api/lms/course-designer/persist`          | Guarda el curso en la BD         |
| `POST` | `/api/lms/course-designer/{id}/review`      | Obtiene revisión IA del curso    |
| `GET`  | `/api/lms/course-designer/{id}/preview`     | Preview del curso con relaciones |

Todos requieren `Authorization: Bearer {token}` (Sanctum).

---

## Permisos requeridos

| Rol              | Acceso                                      |
| ---------------- | ------------------------------------------- |
| `admin`          | ✅ Acceso completo                          |
| `hr_leader`      | ✅ Acceso completo                          |
| `talent_planner` | ❌ No tiene acceso (futuro: se puede abrir) |
| `collaborator`   | ❌ No tiene acceso                          |

---

## Flujo completo visualizado

```
┌─────────────────────────────────────────────────────────────────┐
│                    DISEÑADOR DE CURSOS                          │
│                                                                 │
│  ┌─────────┐   ┌───────────┐   ┌──────────┐   ┌─────────────┐ │
│  │ PASO 1  │──▶│  PASO 2   │──▶│ PASO 3   │──▶│   PASO 4    │ │
│  │ Diseño  │   │ Estructura│   │ Contenido│   │   Config    │ │
│  │   IA    │   │  Manual   │   │    IA    │   │   Manual    │ │
│  └─────────┘   └───────────┘   └──────────┘   └─────────────┘ │
│       │                                              │         │
│  ┌────┴────┐                                   ┌─────┴──────┐  │
│  │Arquitecto                                   │  PASO 5    │  │
│  │   de    │                                   │  Revisión  │  │
│  │Aprendiz.│◄──────────────────────────────────│    IA +    │  │
│  └─────────┘                                   │  Publicar  │  │
│                                                └────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CICLO OPERATIVO LMS                          │
│                                                                 │
│  ┌───────────┐   ┌───────────┐   ┌────────────┐   ┌─────────┐ │
│  │ Operador  │──▶│  Learner  │──▶│   Coach    │──▶│Analytics│ │
│  │   LMS     │   │ completa  │   │ Crecimiento│   │  KPIs   │ │
│  │(enrollment│   │  curso    │   │(seguimiento│   │(9 KPIs) │ │
│  │ + invit.) │   │           │   │ + mentoring│   │         │ │
│  └───────────┘   └───────────┘   └────────────┘   └─────────┘ │
│                       │                                         │
│                       ▼                                         │
│              ┌─────────────────┐                                │
│              │  Certificado    │                                │
│              │  auto-emitido   │                                │
│              │  + Talent Pass  │                                │
│              └─────────────────┘                                │
└─────────────────────────────────────────────────────────────────┘
```

---

## Mejores prácticas

1. **Usa las brechas de Skill Intelligence como input** — el campo "Brechas de skills" acepta los mismos nombres que aparecen en el módulo de Skill Intelligence. Esto hace que el curso sea relevante, no genérico.

2. **No publiques sin revisión IA** — el score pedagógico detecta problemas comunes como módulos desbalanceados, lecciones demasiado largas, o falta de evaluación.

3. **Mezcla tipos de contenido** — un buen curso tiene mínimo 3 tipos: artículos (conceptos), videos (demos), ejercicios (práctica). La regla 60/40 (teoría/práctica) funciona bien.

4. **Edita siempre el contenido generado** — la IA produce un borrador de calidad, pero el experto en el tema (SME) debe validar la precisión técnica.

5. **Empieza con cursos cortos** (2-4 horas) — es más fácil iterar y el completion rate es significativamente más alto.

6. **Configura la certificación desde el inicio** — XP + certificado = motivación. Sin gamificación, el completion rate baja 40% según benchmarks del sector.

---

_Documento generado a partir del código fuente de Stratos v0.13.0 · 4 Abr 2026_

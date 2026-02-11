# Instrucción por defecto (Sistema) EL OTRO

Genera un escenario estratégico a partir de los datos proporcionados.

- Respeta el alcance organizacional y no expongas datos de otras organizaciones.
- Mantén el resultado en español si el parámetro `language` es `es`, en inglés si es `en`.
- Provee un resumen ejecutivo corto, objetivos estratégicos, iniciativas recomendadas y un plan de hitos.
- Si hay incertidumbre, indica supuestos y fuentes de datos sugeridas.

Además, incluye una sección detallada de: capacidades, competencias, skills y suggeted_roles. Para cada elemento proporciona una breve descripción y ejemplos de cómo se relaciona con las iniciativas recomendadas.

## Propósito y definiciones breves

Propósito: este escenario simula la gestión estratégica del talento para alcanzar el objetivo principal.

Definiciones:

- **Capabilities (Capacidades):** medios/funciones organizacionales que permiten cumplir el objetivo del escenario.
- **Competencies (Competencias):** conocimientos y habilidades necesarias para ejecutar una capability.
- **Skills (Habilidades):** unidad mínima (habilidades/conocimientos) que compone una competency; puede ser texto o un objeto `{ "name": "..." }`.
- **Suggested_ Roles (Suggested_Roles):** puestos propuestos con las competencias asignadas; el analista debe homologar estos roles con la estructura interna.
  Además, incluya una sección `roles` que sea un arreglo de objetos con `name`, `description` opcional y `competencies` (lista de nombres de competencias o objetos `{ "name": "..." }`).

Formato: JSON. Devuelve únicamente un objeto JSON válido que cumpla el esquema con claves de primer nivel: scenario_metadata, capabilities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions. No incluyas ningún texto, explicación o comentario fuera del objeto JSON.

Ejemplo mínimo de salida (JSON) — la estructura debe ser anidada: `capabilities[]` → `competencies[]` → `skills[]`:

```json
{
    "scenario_metadata": {
        "name": "Mi Escenario",
        "generated_at": "2026-01-01T00:00:00Z",
        "confidence_score": 0.85
    },
    "capabilities": [
        {
            "name": "Contratación",
            "description": "Atraer y seleccionar talento",
            "competencies": [
                {
                    "name": "Sourcing",
                    "skills": ["Boolean search", "LinkedIn outreach"]
                }
            ]
        }
    ],
    "competencies": [],
    "skills": [],
    "suggested_roles": [],
    "impact_analysis": [],
    "confidence_score": 0.85,
    "assumptions": []
}
```

## JSON Schema (resumen)

Incluye el siguiente esquema JSON simplificado para validar la estructura anidada requerida (capabilities → competencies → skills):

```json
{
    "$schema": "http://json-schema.org/draft-07/schema#",
    "type": "object",
    "required": ["scenario_metadata"],
    "properties": {
        "scenario_metadata": { "type": "object", "required": ["name"] },
        "capabilities": { "type": "array" },
        "competencies": { "type": "array" },
        "skills": { "type": "array" },
        "suggested_roles": {"type:"array}
    }
}
```

# INSTRUCCIÓN PARA EL MODELO (INGENIERÍA DE TALENTO ESTRATÉGICO):

Actúa como un Ingeniero de Talento de élite. Tu objetivo es diseñar un "Blueprint" (plano técnico) de capacidades organizacionales híbridas. Para cada rol sugerido en `suggested_roles`, debes realizar un análisis de descomposición de tareas y definir su composición de talento.

## REQUISITOS OBLIGATORIOS POR ROL:
1. `name`: Nombre del rol estratégico.
2. `description`: Descripción breve del rol.
3. `key_competencies`: Array de nombres de competencias asociadas (strings).
4. `estimated_fte`: Cantidad de personas equivalentes necesarias (número).
5. `talent_composition`: Objeto JSON que DEBE contener:
    - `human_percentage`: (0-100) % de carga de trabajo humana.
    - `synthetic_percentage`: (0-100) % de carga delegable a IA.
    - `strategy_suggestion`: ["Buy", "Build", "Borrow", "Synthetic", "Hybrid"].
    - `logic_justification`: Explicación técnica del mix.
6. `suggested_agent_type`: Tipo de IA necesaria si aplica.

## REGLAS DE NEGOCIO PARA EL MODELO:
- Si el rol es altamente transaccional o de procesamiento de datos, el `synthetic_percentage` debe ser alto (>60%).
- Si el rol es de alta gestión o cuidado de personas, el `human_percentage` debe ser dominante (>80%).
- `Estrategia`: Usa "Synthetic" solo si es 100% IA. Usa "Hybrid" para colaboración humano-máquina.
- La suma de `human_percentage` y `synthetic_percentage` debe ser siempre 100.
- La `strategy_suggestion` debe ser "Synthetic" solo si el rol es 100% ejecutable por IA, y "Hybrid" si hay un mix equilibrado.
- `Sesgo de Ingeniería`: Roles transaccionales/datos = synthetic_percentage > 60%. Roles de gestión/personas = human_percentage > 80%.
- `Catálogos completos`: Los arrays competencies y skills de primer nivel deben contener TODAS las competencias y skills mencionadas en capabilities, sin duplicados.

## Ejemplo de salida:

```json
{
  "escenario": {
    "id": "ESC-001",
    "nombre": "Transformación digital en una empresa mediana",
    "descripcion": "Marco de capacidades, competencias y habilidades para ejecutar un programa de transformación digital de extremo a extremo en una organización mediana.",
    "version": "1.",
    "idioma": "es-ES"
  },
  "capabilities": [
    {
      "id": "CAP-01",
      "nombre": "Liderazgo y Gestión del Cambio",
      "descripcion": "Alinear visión, personas y resultados para habilitar cambios sostenibles.",
      "competencies": [
        {
          "id": "CAP-01-C1",
          "nombre": "Gestión del Cambio Organizacional",
          "descripcion": "Preparar, acompañar y reforzar la adopción del cambio.",
          "skills": [
            {
              "id": "CAP-01-C1-S1",
              "nombre": "Análisis de impacto del cambio",
              "descripcion": "Identificar procesos, roles y sistemas afectados, y estimar el alcance del cambio."
            },
            {
              "id": "CAP-01-C1-S2",
              "nombre": "Plan de adopción y comunicación",
              "descripcion": "Diseñar mensajes, canales y calendario para informar y capacitar a los grupos objetivo."
            },
            {
              "id": "CAP-01-C1-S3",
              "nombre": "Gestión de la resistencia",
              "descripcion": "Detectar causas de resistencia y aplicar intervenciones para mitigarlas."
            }
          ]
        },
        {
          "id": "CAP-01-C2",
          "nombre": "Toma de Decisiones",
          "descripcion": "Seleccionar opciones óptimas equilibrando valor, riesgo y tiempo.",
          "skills": [
            {
              "id": "CAP-01-C2-S1",
              "nombre": "Priorización basada en valor",
              "descripcion": "Ordenar iniciativas por impacto en objetivos estratégicos y métricas de negocio."
            },
            {
              "id": "CAP-01-C2-S2",
              "nombre": "Evaluación de riesgos",
              "descripcion": "Identificar, analizar y mitigar riesgos con planes de contingencia."
            },
            {
              "id": "CAP-01-C2-S3",
              "nombre": "Uso de datos para decidir",
              "descripcion": "Aplicar evidencia cuantitativa y cualitativa para sustentar decisiones."
            }
          ]
        },
        {
          "id": "CAP-01-C3",
          "nombre": "Gestión de Stakeholders",
          "descripcion": "Involucrar a las personas clave y alinear expectativas.",
          "skills": [
            {
              "id": "CAP-01-C3-S1",
              "nombre": "Mapeo y análisis de interesados",
              "descripcion": "Identificar influencia, intereses y necesidades de cada actor."
            },
            {
              "id": "CAP-01-C3-S2",
              "nombre": "Comunicación ejecutiva",
              "descripcion": "Sintetizar información compleja en narrativas claras orientadas a la acción."
            },
            {
              "id": "CAP-01-C3-S3",
              "nombre": "Negociación y acuerdos",
              "descripcion": "Conducir conversaciones hacia compromisos viables y documentados."
            }
          ]
        }
      ]
    },
    {
      "id": "CAP-02",
      "nombre": "Entrega y Operaciones de Producto",
      "descripcion": "Definir, construir y operar productos con calidad y enfoque en el cliente.",
      "competencies": [
        {
          "id": "CAP-02-C1",
          "nombre": "Gestión de Proyectos y Métodos Ágiles",
          "descripcion": "Planificar y ejecutar incrementos de valor de forma iterativa.",
          "skills": [
            {
              "id": "CAP-02-C1-S1",
              "nombre": "Planificación iterativa",
              "descripcion": "Establecer objetivos por iteración, capacidad del equipo y roadmap adaptable."
            },
            {
              "id": "CAP-02-C1-S2",
              "nombre": "Gestión de backlog",
              "descripcion": "Definir épicas e historias con criterios de aceptación claros y priorización continua."
            },
            {
              "id": "CAP-02-C1-S3",
              "nombre": "Facilitación de ceremonias",
              "descripcion": "Conducir dailies, reviews, retrospectivas y refinamientos efectivos."
            }
          ]
        },
        {
          "id": "CAP-02-C2",
          "nombre": "Diseño y Experiencia de Usuario",
          "descripcion": "Entender necesidades y validar soluciones centradas en el usuario.",
          "skills": [
            {
              "id": "CAP-02-C2-S1",
              "nombre": "Investigación con usuarios",
              "descripcion": "Planear y ejecutar entrevistas, encuestas y pruebas de usabilidad."
            },
            {
              "id": "CAP-02-C2-S2",
              "nombre": "Prototipado y validación",
              "descripcion": "Crear prototipos de baja/alta fidelidad y validar hipótesis con usuarios."
            },
            {
              "id": "CAP-02-C2-S3",
              "nombre": "Escritura UX",
              "descripcion": "Redactar microcopys claros y consistentes que reduzcan fricción."
            }
          ]
        },
        {
          "id": "CAP-02-C3",
          "nombre": "Gestión de Calidad y Operación",
          "descripcion": "Asegurar confiabilidad, rendimiento y mejora continua en producción.",
          "skills": [
            {
              "id": "CAP-02-C3-S1",
              "nombre": "Definición de criterios de aceptación",
              "descripcion": "Establecer condiciones verificables que garanticen el valor entregado."
            },
            {
              "id": "CAP-02-C3-S2",
              "nombre": "Pruebas y automatización básica",
              "descripcion": "Diseñar pruebas funcionales y automatizarlas cuando sea viable."
            },
            {
              "id": "CAP-02-C3-S3",
              "nombre": "Monitoreo post-lanzamiento",
              "descripcion": "Configurar alertas, KPIs y bucles de feedback para detectar incidencias."
            }
          ]
        }
      ]
    },
    {
      "id": "CAP-03",
      "nombre": "Tecnología y Datos",
      "descripcion": "Seleccionar y operar plataformas tecnológicas seguras, escalables y orientadas a datos.",
      "competencies": [
        {
          "id": "CAP-03-C1",
          "nombre": "Arquitectura y Nube",
          "descripcion": "Diseñar soluciones modulares y operar cargas en la nube con buenas prácticas.",
          "skills": [
            {
              "id": "CAP-03-C1-S1",
              "nombre": "Modelado de arquitectura",
              "descripcion": "Definir componentes, integraciones y principios de diseño."
            },
            {
              "id": "CAP-03-C1-S2",
              "nombre": "Servicios en la nube",
              "descripcion": "Seleccionar y configurar servicios gestionados acorde a requisitos no funcionales."
            },
            {
              "id": "CAP-03-C1-S3",
              "nombre": "Automatización CI/CD",
              "descripcion": "Configurar pipelines para despliegues repetibles y seguros."
            }
          ]
        },
        {
          "id": "CAP-03-C2",
          "nombre": "Gobernanza y Seguridad",
          "descripcion": "Proteger activos y garantizar cumplimiento regulatorio.",
          "skills": [
            {
              "id": "CAP-03-C2-S1",
              "nombre": "Gestión de identidades y accesos",
              "descripcion": "Aplicar principios de mínimo privilegio y revisiones periódicas."
            },
            {
              "id": "CAP-03-C2-S2",
              "nombre": "Privacidad y cumplimiento",
              "descripcion": "Implementar controles para normativas de datos y retención."
            },
            {
              "id": "CAP-03-C2-S3",
              "nombre": "Respuesta a incidentes",
              "descripcion": "Detectar, contener y aprender de incidentes de seguridad y disponibilidad."
            }
          ]
        },
        {
          "id": "CAP-03-C3",
          "nombre": "Analítica y Ciencia de Datos",
          "descripcion": "Convertir datos en información accionable para el negocio.",
          "skills": [
            {
              "id": "CAP-03-C3-S1",
              "nombre": "Modelado y calidad de datos",
              "descripcion": "Diseñar modelos lógicos/físicos y asegurar integridad y linaje."
            },
            {
              "id": "CAP-03-C3-S2",
              "nombre": "Visualización y storytelling",
              "descripcion": "Crear paneles y narrativas que impulsen decisiones."
            },
            {
              "id": "CAP-03-C3-S3",
              "nombre": "Experimentación y A/B testing",
              "descripcion": "Diseñar y analizar experimentos para validar hipótesis."
            }
          ]
        }
      ]
    }
  ]
    "suggested_roles": [
        {
            "name": "Ingeniero de Talento",
            "description": "Diseña y optimiza sistemas de capacidades híbridas",
            "estimated_fte": 1.0,
            "key_competencies": ["CAP-02-C1", "CAP-03-C2"],
            "talent_composition": {
                "human_percentage": 40,
                "synthetic_percentage": 60,
                "strategy_suggestion": "Hybrid",
                "logic_justification": "La IA procesa modelos y genera dashboards mientras el humano diseña la estrategia organizacional y valida hipótesis."
            },
            "suggested_agent_type": "Orquestador de Datos y Analytics"
        }
    ],
    "impact_analysis": [
        {"area": "Productividad", "impact": "alto", "notes": "Reducción de ciclos de planificación en 40%"}
    ],
    "confidence_score": 0.92,
    "assumptions": ["Disponibilidad de APIs de datos históricos", "Acceso a plataformas de BI"]
}

```
# FORMATO DE SALIDA:
Devuelve exclusivamente el objeto JSON siguiendo el esquema validado, asegurando que la estructura de `suggested_roles` incluya estos nuevos campos de ingeniería.
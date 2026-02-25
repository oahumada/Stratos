# Estándar de Ingeniería de Talento (Blueprint Detalle)

Este documento define los moldes maestros para Roles y Competencias en Stratos, alineados con el estándar global **SFIA 8** y el enfoque de **Organización Basada en Skills**.

---

## 1. El Molde del Rol (Role Engineering Mold)

Cada rol diseñado en un escenario debe responder a este molde para asegurar consistencia arquitectónica.

| Campo                  | Definición                                      | Ejemplo (Product Manager)                           |
| :--------------------- | :---------------------------------------------- | :-------------------------------------------------- |
| **Identidad**          | Nombre técnico y código único por escenario.    | `PM-DIG-001`                                        |
| **Arquetipo**          | Clasificación estratégica (E, T, O).            | **Tactical (T)**                                    |
| **Misión**             | Propósito único y razón de existir.             | Definir y liderar la visión del producto digital... |
| **Outcomes (KPIs)**    | Resultados clave esperados (Estándar de éxito). | Time-to-market < 2 semanas; NPS > 70.               |
| **Contexto Operativo** | Procesos de negocio del Paso 1 donde participa. | Roadmap Planning, Sprint Review.                    |
| **Talent Composition** | % Humano vs % Sintético (Agentes IA).           | 70% Humano / 30% Sintético.                         |
| **Authority Level**    | Capacidad de toma de decisiones.                | Decisiones sobre backlog y priorización técnica.    |

---

## 2. El Molde de la Competencia (Competency Detail Mold)

Para cada competencia asignada a un rol en el Paso 2, se genera este detalle de ingeniería en el Paso 3.

### Estructura por Nivel (Escala 1-5 adaptada de SFIA)

| Nivel             | Descriptor (Marco) | BARS / Indicador Conductual (Contenido)                                             |
| :---------------- | :----------------- | :---------------------------------------------------------------------------------- |
| **1. Básico**     | **Ayuda**          | _Ejecuta bajo supervisión_: Realiza [Acción Específica] alineada a [Contexto].      |
| **2. Intermedio** | **Aplica**         | _Autónomo en estándar_: Resuelve problemas de [Tipo] sin escalamiento constante.    |
| **3. Avanzado**   | **Habilita**       | _Guía a otros_: Diseña y supervisa la ejecución de [Proceso/Tarea] compleja.        |
| **4. Experto**    | **Asegura**        | _Define el estándar_: Crea marcos de referencia para [Dominio] aplicables a la org. |
| **5. Maestro**    | **Inspira**        | _Innova el domino_: Lidera la transformación de [Campo] a nivel industria.          |

### El BARS en la Matriz de Transformación

Para evitar redundancia en el diseño, aplicamos esta lógica basada en el `change_type` definido en el Paso 2:

1. **Maintenance (Mantenimiento):** Se hereda el BARS existente en el catálogo global. No se genera contenido nuevo.
2. **Transformation (Transformación):** El "Blueprint de Detalle" **debe generar un nuevo BARS**. La transformación implica que, aunque el "Nivel 3" siga significando "Habilita", _lo que haces_ para habilitar ha cambiado (ej: de operar manualmente a supervisar un agente IA).
3. **Enrichment (Enriquecimiento):** Se genera un BARS inicial (v1) para la nueva competencia, anclado al contexto del rol.

---

## 3. Componentes de la Skill (El DNA)

- **Technical Skills:** Lista de sub-habilidades específicas (ej: Análisis de datos con SQL).
- **BARS Evidence (Validation):** El comportamiento observable que garantiza el nivel.
- **Evaluation Criterion (The Evidence):** El entregable físico o prueba técnica.
    - _Ejemplo Nivel 4:_ "Diseño documentado de una arquitectura escalable revisado por pares".
- **Learning Unit (Micro-Currículum):**
    - _Objetivo:_ Qué será capaz de hacer (Taxonomía de Bloom).
    - _Contenido Clave:_ Temas críticos para cerrar el gap.
    - _Esfuerzo Estimado:_ Horas/Semanas para dominar.

---

## 4. La Importancia Crítica del BARS

El **BARS (Behaviorally Anchored Rating Scales)** es el ancla de realidad del sistema. Sin un BARS, un "Nivel 3" es una interpretación subjetiva que genera fricción y desacuerdo. Con un BARS, el nivel es un **contrato de desempeño observable**.

### ¿Por qué es vital? (El Ejemplo Claro)

Imagina la competencia "Resolución de Problemas Técnicos" en Nivel 3 para un Desarrollador:

- **Sin BARS:** El líder cree que significa "arreglar bugs rápido"; el colaborador cree que significa "no pedir ayuda"; la IA no sabe qué evaluar.
- **Con BARS (Ejemplo de Ingeniería):** _"El colaborador identifica la causa raíz de fallos complejos en el entorno de producción, documenta la solución en el wiki técnico y supervisa que la corrección no genere regresiones en módulos adjuntos."_

**Utilidad:**

1.  **Evaluación Justa:** Elimina el sesgo del evaluador.
2.  **Claridad del Agente:** El Agente de IA sabe exactamente qué evidencia buscar para validar el nivel.
3.  **Mapa de Carrera:** El colaborador sabe exactamente qué conducta debe cambiar para ascender.

---

## 5. Política de BARS: Obligatoriedad y Fallback de IA

¿Qué ocurre si el usuario deja el BARS en blanco en la fase de diseño?

1.  **No Obligatoriedad Inicial (Fricción Cero):** En el Paso 2 (Matriz), no obligamos al usuario a escribir el BARS para no detener su flujo creativo.
2.  **Generación Mandatoria por IA (Paso 3):** Si una celda llega al Paso 3 (Ingeniería de Detalle) con el BARS vacío o es una `Transformation`, el **Agente de Ingeniería de Roles** tiene la orden de **generarlo automáticamente**.
3.  **Criterio de Generación:** El agente creará el BARS combinando:
    - El **Arquetipo** del rol (E, T o O).
    - El **Proceso de Negocio** al que pertenece.
    - El **Nivel SFIA** (1-5) seleccionado.

**En resumen:** En Stratos, un rol no puede ser finalizado ni "pasado a producción" sin un BARS técnicamente sólido. Si el humano no lo define, la IA propone el estándar basado en el blueprint estratégico.

---

## 3. Modelo de Ingeniería (Blueprint Model)

El sistema operará bajo este ciclo:

1. **Herencia (Paso 2):** El rol hereda competencias y niveles.
2. **Derivación (Paso 3):** El Agente Ingeniero rellena el molde usando el contexto de la organización.
3. **Validación:** El usuario ajusta los "Criterios de Evaluación" para que sean realistas en su cultura corporativa.

---

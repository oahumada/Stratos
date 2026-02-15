# Modelo del Cubo y Flujo de Conciliación Estratégica

Este documento detalla la metodología del **Cubo de Roles** y cómo se integra en el flujo de planificación de escenarios dentro de Stratos.

---

## 1. El Objetivo Estratégico: Validación de la Anatomía Futura

El objetivo principal del uso del Cubo en la fase de planificación es actuar como un **filtro de calidad de ingeniería organizacional**.

Antes de que las propuestas de la IA o las ideas del consultor pasen al Catálogo Maestro de la empresa, el Cubo permite validar que el diseño del futuro sea coherente en sus tres dimensiones. Esto evita la "contaminación" del catálogo con roles inconsistentes o mal definidos, asegurando que solo lo que tiene una estructura técnica sólida sea aprobado para la ejecución.

---

## 2. Posición en la Metodología de 7 Pasos

Dentro de la metodología de planificación dotacional de Stratos, el Cubo y su interfaz de revisión actúan como el **"Puente de Transición"** entre las dos primeras fases críticas:

1.  **FASE 1 - Definir contexto y diseño (Incubación):** Aquí es donde se crean escenarios y se "lanzan" ideas de capacidades y roles sin restricciones. El Cubo vive aquí en modo _laboratorio_.
2.  **CONCILIACIÓN DEL CUBO (El punto actual):** Es el hito de decisión donde se valida lo incubado contra la realidad organizacional.
3.  **FASE 2 - Modelar roles y skills (Ingeniería de Detalle):** Una vez que el "Cubo" ha sido aprobado, el rol entra a la matriz de ingeniería para precisar el pivote Rol-Competencia-Escenario.

---

## 3. El Concepto del Cubo (Visualización 3D)

El rol en Stratos no es una entidad plana; se define mediante tres ejes de coordenadas que aseguran su coherencia organizacional:

- **Eje X (Arquetipo de Accountability):** Define la complejidad del rol.
    - **(E) Estratégico:** Dirección, visión y toma de decisiones de alto impacto. (Usualmente >70% de leverage humano).
    - **(T) Táctico:** Gestión, coordinación y optimización de recursos.
    - **(O) Operacional:** Ejecución, transaccionalidad y cumplimiento de procesos.
- **Eje Y (Maestría y Competencias):** El "ADN" técnico y conductual. Define qué sabe hacer el rol y en qué nivel (1-5). Representado visualmente mediante la intensidad de las competencias asociadas.
- **Eje Z (Proceso de Negocio / Capacidad):** El anclaje estructural. Define en qué parte de la cadena de valor entrega resultados el rol. Los roles se agrupan por "Capability" para visualizar este eje.

---

## 4. Flujo de Trabajo: De la Incubación a la Ingeniería

El proceso se divide en dos grandes bloques dentro del sistema:

### A. Bloque de Revisión (Incubación / Laboratorio)

Es la "sala de espera" de la IA. Aquí se presentan las propuestas generadas.

- **Lógica de Decisión:**
    - **Aprobación:** El elemento cambia su estado de `incubating` a `active`. Esto gatilla su aparición automática en la **Matriz de Ingeniería** y permite que el sistema lo use para cálculos de brechas.
    - **No Aprobación / Rechazo:** El elemento permanece aislado. No afecta los promedios de la organización, no aparece en los selectores de roles y mantiene la matriz de ingeniería limpia.
- **Capacidad de Iteración:** Si la propuesta es rechazada, el consultor puede re-simular o ajustar los parámetros antes de intentar una nueva aprobación.

### C. Matriz de Compatibilidad (Guía para el Operador)

Para asegurar la coherencia entre el descubrimiento (Cubo) y la ejecución (Matriz), se establece la siguiente correspondencia recomendada:

| Resultado del Matching (Cubo) | Estado Sugerido (Matriz) | Acción de Ingeniería                                |
| :---------------------------- | :----------------------- | :-------------------------------------------------- |
| **Nuevo Rol (Match 0%)**      | **📈 Enriquecimiento**   | Creación de capacidad nueva e inexistente.          |
| **Existente (Match > 85%)**   | **✅ Mantención**        | Validación de que el catálogo actual es suficiente. |
| **Parcial (Match 40-84%)**    | **🔄 Transformación**    | Upskilling: El rol evoluciona su ADN actual.        |
| **No Propuesto por IA**       | **📉 Extinción**         | El rol actual no es necesario en el diseño futuro.  |

Esta lógica permite que el operador tome decisiones basadas en datos técnicos (Embeddings) y no solo en intuición.

---

## 6. Fundamentos Conceptuales y Literatura Organizacional

El modelo de conciliación de Stratos no es solo técnico, sino que se apoya en pilares clásicos del diseño de puestos y desarrollo de talento:

### A. Dimensión de Crecimiento del Rol

La literatura organizacional (Herzberg, 1968) distingue dos ejes de crecimiento que Stratos mapea directamente:

1.  **Crecimiento en Extensión (Job Enlargement):** Corresponde a nuestro estado de **Enriquecimiento**. Es una expansión horizontal donde el colaborador asume más competencias del mismo nivel de complejidad. Busca polifuncionalidad.
2.  **Crecimiento en Profundidad (Job Enrichment):** Corresponde a nuestro estado de **Transformación/Upskilling**. Es una expansión vertical que aumenta la autonomía y la complejidad técnica, exigiendo niveles superiores en los BARS (Hackman & Oldham, 1976).

### C. Resumen de Correspondencia Teórica

Esta tabla consolida el respaldo académico de la terminología utilizada en Stratos:

| Concepto Stratos    | Término Organizacional (Literatura) | Fundamento Teórico                                                                                                                                                        |
| :------------------ | :---------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Enriquecimiento** | **Job Enlargement (Extensión)**     | **Aumento Horizontal:** Se añaden nuevas tareas y competencias al mismo nivel de responsabilidad para diversificar el rol y reducir la monotonía (Hackman & Oldham).      |
| **Transformación**  | **Job Enrichment (Profundidad)**    | **Aumento Vertical:** Basado en la teoría de los dos factores de **Herzberg**. El rol crece en complejidad y autonomía, requiriendo un "salto de nivel" o **Upskilling**. |
| **Mantención**      | **Job Stabilization**               | El diseño del puesto es maduro y eficiente; el foco es la sostenibilidad del nivel de maestría actual.                                                                    |
| **Extinción**       | **Job Substitution / Obsolescence** | El rol es desplazado por la automatización o el cambio de modelo de negocio (Schumpeter - Destrucción Creativa).                                                          |

---

## 7. Ventajas del Modelo .md vs .docx

Para el desarrollo de Stratos, el formato **Markdown (.md)** es el preferido por las siguientes razones:

1.  **Versionamiento:** Permite ver cambios línea por línea en el historial (Git).
2.  **Accesibilidad IA:** El agente IA puede leerlo, editarlo y consultarlo instantáneamente para guiar sus decisiones de código.
3.  **Trazabilidad:** Facilita la vinculación entre decisiones metodológicas y archivos de código fuente.
4.  **Colaboración Ágil:** Evita bloqueos de archivos y facilita la edición simultánea en entornos de desarrollo.

---

_Documento actualizado en base a la metodología Stratos - Febrero 2026_

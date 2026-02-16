# Reglas de Arquitectura y Coherencia Stratos

Este documento define las validaciones de negocio que el sistema debe aplicar para asegurar la integridad de la ingeniería organizacional.

## 1. Coherencia Arquetipo - Nivel de Maestría

Se establece un rango de niveles de competencia sugeridos según el arquetipo de accountability del rol:

| Arquetipo           | Banda Sugerida | Descripción                                            |
| :------------------ | :------------- | :----------------------------------------------------- |
| **(E) Estratégico** | **4 - 5**      | Accountability de visión, diseño y mentoría técnica.   |
| **(T) Táctico**     | **3 - 4**      | Accountability de gestión, autonomía y coordinación.   |
| **(O) Operacional** | **1 - 2**      | Accountability de ejecución transaccional supervisada. |

**Regla de Coherencia:**

1.  **Sobrecarga Técnica (Warning):** Si un Rol Operacional (Banda 1-3) se asigna a una competencia con Nivel 4-5, se emite advertencia a menos que se marque como **Referente**.
2.  **Competencias de Apoyo (Válido):** Es perfectamente válido que un Rol Estratégico o Táctico tenga competencias en niveles bajos (1-3) si estas son habilidades de apoyo o no core para su accountability principal. No se emite advertencia por niveles bajos en arquetipos altos.

### 1.1 Dualidad de Niveles: Estructural (Rol) vs. Operativo (Competencia)

Es fundamental distinguir entre dos dimensiones de exigencia:

1.  **Nivel Estructural (Arquetipo del Rol):** Define la complejidad de la accountability (accountability span) y la naturaleza del puesto (E/T/O).
2.  **Nivel Objetivo de Maestría (Requerimiento de Competencia):** Define la profundidad de conocimiento o habilidad necesaria en una competencia específica para cumplir con el diseño del puesto (**Job Design**).

**Clarificación:** Un rol con Arquetipo Operacional (Estructuralmente Nivel 1-3) puede requerir un **Nivel Objetivo de Maestría** alto (Nivel 4-5) en competencias críticas. Esto es común en roles de **Referente Técnico** o mentores operativos. En estos casos, el sistema valida la coherencia a través del flag de **Referente**, reconociendo que la maestría operativa no altera la naturaleza estructural (operacional) del puesto.

### 1.2 Relatividad de Maestría (Detail Design)

El nivel de maestría (1-5) se interpreta de forma relativa al arquetipo:

- **Nivel 5 en Rol Estratégico:** Maestría en visión global y diseño sistémico.
- **Nivel 5 en Rol Operacional:** Maestría técnica extrema en la ejecución y capacidad de actuar como **Referente Interno** o mentor para sus pares.

Cuando un rol operacional supera el nivel 3, el sistema debe permitir "consignar" este rol como un **Referente de Especialidad**, lo cual valida la coherencia arquitectónica a pesar de la diferencia de niveles.

## 2. Lógica de Versionamiento (Cambio de ADN)

- **Transformación de ADN:** Ocurre cuando se añade, elimina o modifica sustancialmente una Skill dentro de una Competencia. Esto **OBLIGA** a generar una nueva versión (v2.0, v3.0) y afecta al catálogo global.
- **Transformación de Exigencia:** Ocurre cuando solo se cambia el nivel requerido para un escenario específico. No requiere nueva versión de la competencia, pero sí un registro de cambio en el mapeo del rol.

## 3. Racionales de Cambio de Nivel

Toda disminución de nivel de maestría debe clasificarse como:

- **Efficiency Gain:** El trabajo es absorbido por IA o herramientas (Leverage Humano ↓, Productividad ↑).
- **Reduced Scope:** El rol deja de ser responsable de esa profundidad de tarea (Job Simplification).
- **Capacity Loss:** Degradación de la calidad del servicio por falta de talento (Riesgo Crítico).

## 4. Competencias Base vs. Competencias de Escenario

### 4.1 Definiciones

**Competencias Base del Rol:**

- Son competencias **permanentes** que definen la naturaleza del rol, independiente del escenario de transformación.
- Incluyen competencias **transversales** (ej: Trabajo en Equipo, Comunicación), **corporativas** (ej: Cultura Organizacional, Compliance), y **técnicas core** (ej: Gestión de Inventarios para un Jefe de Bodega).
- Se heredan del **catálogo de roles** o de **arquetipos/plantillas** predefinidas.
- **No se modifican** en el contexto de un escenario específico.

**Competencias de Escenario:**

- Son competencias que **responden a la transformación estratégica** planteada en el escenario.
- Representan **nuevas exigencias**, **cambios de nivel**, o **competencias emergentes** debido a factores como automatización, cambio de modelo de negocio, o evolución tecnológica.
- Se mapean en **Step 2** del Scenario Planning.
- Tienen estados de cambio: Mantenimiento, Transformación, Enriquecimiento, Extinción.

### 4.2 Regla de Composición del Perfil Completo

El **perfil completo** de un rol en un escenario se compone de:

```
Perfil Completo = Competencias Base + Competencias de Escenario
```

**Ejemplo: Jefe de Bodega en Escenario de Automatización**

| Competencia                 | Tipo      | Nivel | Origen                          |
| :-------------------------- | :-------- | :---- | :------------------------------ |
| Gestión de Inventarios      | Base      | 3     | Catálogo de Roles               |
| Seguridad Laboral           | Base      | 2     | Catálogo de Roles               |
| Liderazgo de Equipos        | Base      | 3     | Arquetipo "Supervisor"          |
| Comunicación Efectiva       | Base      | 2     | Competencias Transversales Corp |
| **Gestión de Sistemas WMS** | Escenario | 4     | Transformación (nuevo req)      |
| **Análisis de Datos**       | Escenario | 3     | Enriquecimiento (nueva skill)   |
| Manejo Manual de Cargas     | Escenario | 1     | Extinción (automatizado)        |

### 4.3 Implementación en Stratos (Enfoque Pragmático)

**Durante la Exploración de Escenarios (Step 2):**

- El operador mapea **todas las competencias relevantes** sin distinción entre base y escenario.
- El sistema asume que **todo es competencia de escenario** (enfoque conservador).
- El foco está en explorar la transformación, no en formalizar arquetipos.

**Al Formalizar el Escenario (Post-Aprobación):**

1. **Consolidación del Perfil:** El sistema identifica qué competencias son recurrentes en roles similares y sugiere convertirlas en "Competencias Base" del catálogo.
2. **Creación de Arquetipos:** A partir de múltiples escenarios formalizados, se pueden extraer patrones y crear plantillas de roles (ej: "Supervisor Operacional Tipo A", "Especialista Técnico Senior").
3. **Herencia Automática:** Los nuevos escenarios pueden heredar competencias base de roles ya formalizados en el catálogo.

**Criterios para Definir Competencias Base (Post-Escenario):**

- **Cultura Corporativa:** Competencias transversales alineadas con valores organizacionales (ej: Innovación, Colaboración).
- **Estrategia Corporativa:** Competencias core del negocio (ej: Excelencia Operacional, Customer Centricity).
- **Estrategia Funcional:** Competencias específicas por área (ej: Gestión de Proyectos para PMO, Análisis Financiero para Finanzas).
- **Especificidades del Puesto:** Competencias técnicas inherentes al rol (ej: Gestión de Inventarios para Jefe de Bodega).

**Regla de Oro:**

> "El 80% del rol debe ser definido por su arquetipo/plantilla (competencias base), y el 20% restante por el contexto del escenario (competencias de escenario)."

Esta regla solo se aplica **después** de que el escenario se formaliza y se incorpora al catálogo organizacional.

### 4.4 Beneficios de este Enfoque

- **Flexibilidad en Exploración:** Durante Step 2, el operador no está limitado por arquetipos rígidos.
- **Aprendizaje Organizacional:** Los arquetipos emergen de la práctica, no de supuestos a priori.
- **Eficiencia Post-Formalización:** Una vez que un escenario se aprueba, las competencias base se reutilizan en futuros escenarios.
- **Trazabilidad:** Se puede distinguir qué cambios son estructurales (base) vs. coyunturales (escenario) solo cuando es relevante.

## 5. Niveles de Diseño vs. Niveles Medidos

Es imperativo diferenciar el propósito de los niveles según la fase del proceso:

- **Step 2 (Scenario Planning):** Los niveles 1-5 representan **Niveles Objetivo de Requerimiento**. Son definiciones de **Job Design** (expectativas de lo que el puesto _debería_ ser).
- **Fase de Evaluación (Post-Mapeo):** Los **Niveles Medidos** se calculan en base a la sumatoria de Skills demostradas. Un nivel de competencia real es una métrica de desempeño, mientras que el nivel en Step 2 es una métrica de exigencia organizacional.

> **Regla de Oro:** "En el diseño de escenarios, definimos la **exigencia** (diseño); en el diagnóstico de talento, medimos la **capacidad** (skills)."

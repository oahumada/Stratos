# 🧠 Guía de Verificación: Paso 2 - Diseño del Cubo de Roles (Role Blueprinting)

Este documento establece el flujo sistemático y ordenado para validar el segundo paso del Scenario Planning: la transformación de Capacidades/Competencias teóricas (obtenidas en el Paso 1) en perfiles de roles tangibles utilizando el **Role Cube Wizard** impulsado por IA.

---

## 🎯 Objetivo de la Fase

Asegurar que el sistema es capaz de generar perfiles de roles tridimensionales (Nivel Técnico 'Y' vs. Arquetipo de Ejecución 'X') basándose estrictamente en el blueprint de capacidades del escenario, y que la interfaz permita al usuario iterar, afinar y aprobar estos diseños.

---

## La Metodología de los 4 Momentos (Arquitectura de Clusters)

El diseño de talento se basa en una jerarquía de agrupaciones funcionales para asegurar la coherencia operativa:

1.  **Skill (Ladrillo / Unidad Atómica)**: El punto mínimo de ejecución técnica o conductual con su propio DNA (H/S). Son los bloques básicos de construcción.
2.  **Competencia (Piso / Skill Wrap)**: Agrupación de skills relacionadas que comparten un resultado operativo afín. Son los _Medios Estratégicos_ y los niveles funcionales del rol.
3.  **Rol (Edificio / Bridge Cluster)**: La estructura completa que agrupa competencias bajo un **Marco Estratégico** (Propósito y Resultados). Es el "Edificio" que da sentido al conjunto.

A continuación, los 4 momentos del flujo:

### Momento 1: Propuesta y Agrupación Lógica (Role Bundling)

AI analiza el Blueprint del Paso 1 para proponer clusters de roles, utilizando como **punto de partida** obligatorio:

- **Marco Estratégico (The Frame)**: Cada rol debe nacer con un **Propósito** claro y **Resultados Esperados** cuantificables. Este marco actúa como el anclaje inicial y aglutinador; las competencias son los medios que el rol utiliza para alcanzar dicho propósito.
- **Diseño de Clusters**: El agente agrupa competencias y skills bajo una lógica de "Bridge Cluster" para maximizar la eficiencia y alineación con el propósito.
- **DNA Inicial**: Propuesta de mix Humano/IA (% Sintético vs % Humano) para la composición global del rol.
- **Justificación de Ingeniería**: Razonamiento de por qué esa agrupación y ese mix de talento son ideales para los retos estratégicos.

### Momento 2: Reconciliación Semántica (Análisis de Catálogo)

- **Acción**: Búsqueda semántica cruzada contra el catálogo oficial utilizando **Vectores de Embedding**.
- **Verificación Técnica**:
    - **Grado de Concordancia de Roles**: Para cada rol propuesto, el sistema utiliza el `EmbeddingService` para indicar si existe uno similar en el catálogo (ej: "85% match con Business Analyst").
    - **Detección de Duplicados**: La UI advierte mediante un badge de "Posible duplicado" si la similitud supera el 85%.
    - **Agente de Competencias**: Realiza el mismo análisis vectorial para determinar si una competencia es:
        - **Existente**: Reutiliza ID.
        - **Modificada**: Propone cambio sobre una existente.
        - **Nueva (ADD)**: Incorporación al catálogo.
    - **Feedback al Usuario**: La UI muestra claramente el porcentaje de similitud y el nombre del rol/competencia original.

### Momento 3: Persistencia Estructural y Mapeo Inicial

- **Acción**: Tras la validación del usuario, se procede a la persistencia de las relaciones.
- **Verificación Funcional**:
    - Creación de registros en `scenario_roles` y vinculación en `scenario_role_competencies`.
    - Registro de los FTEs sugeridos y la justificación de la hibridez (Mix Humano-IA).
    - La matriz queda poblada con los mappings iniciales (source='agent').

### Momento 4: Blueprinting Operacional (X, Y, Z & Atomic BARS)

En esta fase, el asistente finaliza la definición técnica del rol basándose en las coordenadas del Cubo:

- **Eje X (Arquetipo)**: Estratégico, Táctico u Operacional.
- **Eje Y (Maestría)**: Definición del nivel de dominio (1-5) para cada mapping.
- **Eje Z (Proceso)**: Alineación del rol con el flujo de valor o proceso de negocio.
- **Atomic DNA consistency**: Verificación de que el mix de talento de la competencia sea consistente con el ADN de sus skills atómicas (70% H / 30% S, etc.).
- **Generación de BARS**: Creación de conductas, actitudes y responsabilidades a medida.
    - **Eje Y (Maestría)**: Ajuste fino de los niveles 1-5 para cada competencia core.
    - **Eje Z (Proceso)**: Vinculación del rol al proceso de negocio (Z) y flujo de valor.
    - **Generación de BARS (Engineering Blueprints)**: El sistema debe generar las conductas, actitudes y responsabilidades esperadas para el nivel de maestría definido.
    - **Confirmación del Contexto**: Asegurar que el rol responde a las necesidades específicas del escenario futuro.

---

## 🛠️ Validation checks (Pruebas Negativas y Rainy Days)

Para garantizar la robustez del componente interactivo de la arquitectura, se deben forzar los siguientes fallos:

1. **Alucinación de Mapeo**:
    - _Intento_: Pedir a la IA un rol inconexo (ej: Maestro Pastelero en un escenario de Fintech IA).
    - _Expectativa_: La IA debe vincularse estrictamente a las _Capacidades_ importadas en el paso 1, y advertir/rechazar roles fuera del scope del blueprint.

2. **Validación de Persistencia**:
    - _Intento_: Generar roles, salir abruptamente de la página, y regresar.
    - _Expectativa_: Los roles en incubación del escenario activo deben persistir en base de datos sin perder el estado de relación con las competencias.

3. **Restricción de Privilegios**:
    - _Intento_: Intentar avanzar al Paso 3 sin haber aprobado al menos un rol esencial que el sistema requiera para justificar la existencia del escenario (dependiendo de la regla de validación).
    - _Expectativa_: Botón deshabilitado o alerta de validación informando la falta de "Capital Humano Diseñado".

---

## 🔍 Métricas y Telemetría Oculta (Glass Box)

Durante la validación, el equipo técnico debe revisar en paralelo:

- La carga de la API `/api/role-cube/analyze` (o end-points equivalentes).
- El Payload del modelo a nivel `JSON`: Asegurar que el contrato de datos del rol cumple con el Standard Stratos (Títulos de rol limpios, puntuaciones de maestría de 1 a 5 numéricas).
- Logs de Laravel de las transacciones SQL: Garantizar que no hay "Deadlocks" al crear los Pivots `role_capability_competency`.

---

Al finalizar con check verde esta guía, el módulo de **Diseño Estratégico** del Scenario Planning estará listo para la integración final con análisis predictivo (RAG/Gaps).

---

## 🛡️ Guía de Excelencia UX/UI

Para asegurar que la implementación física de estos roles y competencias mantenga el estándar de profesionalismo y estética de Stratos, consulta los:
[Principios de Vanguardia UX/UI](file:///home/omar/Stratos/docs/DesignSystem/UX_UI_VANGUARD_PRINCIPLES.md)

Este documento detalla los 5 pilares (Jerarquía, Micro-interacciones, Tipografía, ADN Visual y Narrativa) que deben guiar la construcción de cada interfaz en las siguientes fases.

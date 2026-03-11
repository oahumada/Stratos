# 🧠 Guía de Verificación: Paso 2 - Diseño del Cubo de Roles (Role Blueprinting)

Este documento establece el flujo sistemático y ordenado para validar el segundo paso del Scenario Planning: la transformación de Capacidades/Competencias teóricas (obtenidas en el Paso 1) en perfiles de roles tangibles utilizando el **Role Cube Wizard** impulsado por IA.

---

## 🎯 Objetivo de la Fase

Asegurar que el sistema es capaz de generar perfiles de roles tridimensionales (Nivel Técnico 'Y' vs. Arquetipo de Ejecución 'X') basándose estrictamente en el blueprint de capacidades del escenario, y que la interfaz permita al usuario iterar, afinar y aprobar estos diseños.

---

## 🚀 Flujo de Pruebas (Happy Path)

### 1. Transición e Inicialización

- **Acción**: Tras finalizar el Paso 1 (Importación y guardado del organigrama de capacidades), avanzar al "Paso 2: Diseño de Roles".
- **Verificación Técnica**:
    - Asegurar que la vista (ej. `Step2Design.vue` o la pestaña correspondiente en `ScenarioDetail`) carga correctamente.
    - El listado de capacidades inyectadas en el escenario debe estar disponible como contexto principal.

### 2. Ejecución del "Role Cube Wizard"

- **Acción**: Lanzar el asistente de IA para proponer nuevos roles.
- **Interacción**:
    - Proporcionar las instrucciones (o dejar que la IA infiera basados en el escenario actual): "Define 3 roles clave para ejecutar la capacidad de Ciberseguridad IA".
    - Configurar parámetros (ejes de madurez, enfoque operativo vs estratégico).
- **Verificación Técnica**:
    - Verificar que el spinner/indicador de progreso comunique al usuario la espera.
    - Asegurar que la respuesta del LLM aísla la creación de roles (y no mezcle datos del Paso 1).

### 3. Revisión de la Matriz (Role/Competency Matrix)

- **Acción**: Visualizar los roles generados dentro de la `RoleCompetencyMatrix.vue` o vista de revisión equivalente.
- **Verificación Funcional**:
    - **Mapeo Dimensional**: ¿Aparece el rol mapeado correctamente en los cruces de Competencias?
    - **Deduplicación**: Si el rol sugiere una habilidad que ya existe, ¿se reutiliza correctamente el UUID?
    - **Estatus Incubado**: Confirmar que los roles recién nacidos están marcados en estado `incubating` (borrador/no oficiales todavía).

### 4. Edición de Casos de Borde e Interfaz de Roles

- **Acción**: Intentar modificar el nivel de seniority de un rol generado (por ejemplo, subir la exigencia de Python de nivel 3 a nivel 5).
- **Verificación Funcional**:
    - Comportamiento de sliders e inputs en la configuración del rol.
    - Si el escenario se configuró finalmente como cerrado/deployado, asegurar que rige el **Read-Only Protocol** y no se puede modificar.

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

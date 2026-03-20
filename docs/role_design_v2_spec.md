# Especificación Técnica: AI Role Wizard V2 & Materialización de Skills

Este documento describe la arquitectura de guardado y la jerarquía de datos implementada en la segunda versión del Wizard de Roles de Stratos.

## 1. Estructura del Wizard (5 Pasos)

El wizard se ha consolidado para mejorar la UX, eliminando la etapa manual de "Skill Blueprint" y reemplazándola por una generación automatizada profunda.

1.  **Contexto Organizacional:** Nombre, descripción y propósito (Capa de Negocio).
2.  **Ingeniería de Cubo:** Definición de arquetipo y coordenadas (Capa Estratégica).
3.  **Sugerencias de Diseño IA:** Selección de competencias base sugeridas por el agente.
4.  **Matriz de Competencias SFIA:** Definición de niveles requeridos y racional (Capa Técnica).
    *   *Trigger:* Al avanzar, la IA genera el Blueprint completo (BARS, Unidades y Criterios).
5.  **Estándares BARS del Rol:** Consolidación de comportamiento, actitud y responsabilidad global.
    *   *Acción:* Guardado final y seteo del estado `pending_approval`.

---

## 2. Jerarquía de Persistencia (Data Mapping)

> [!IMPORTANT]
> **Nota conceptual crítica:** Los **BARS** (Behavior, Attitude, Responsibility, Skill) por definición **no poseen niveles**; representan el estándar cualitativo básico y transversal esperado para el rol. Los niveles numerados (1-5) corresponden exclusivamente al estándar **SFIA** (o equivalente) aplicado a habilidades técnicas específicas.

Al guardar el rol desde el Wizard, `RoleController::syncRoleSkills` ejecuta una orquestación en tres niveles, separando los estándares globales del rol de las definiciones técnicas de habilidades.

### A. Capa Global del Rol (BARS)
- **Atributos:** `behavior`, `attitude`, `responsibility`, `skill`.
- **Almacenamiento:** Se guardan en el campo JSON `ai_archetype_config.bars` de la tabla `roles`.
- **Propósito:** Definir el estándar esperado para la persona que ocupa el puesto, independientemente de la competencia técnica específica. Es la "cultura en acción" del rol.

### B. Capa de Competencia (Agrupador SFIA)
- **Modelo:** `App\Models\Competency`
- **Atributos:** `name`, `description`, `status`.
- **Mapeo:** Corresponde a las categorías de alto nivel (ej. Gestión de Proyectos).

### C. Capa de Skill (Unidad Técnica SFIA 1-5)
- **Modelo:** `App\Models\Skill`
- **Almacenamiento de Niveles:** Tabla `bars_levels` (utilizada internamente para almacenar los **Niveles SFIA**).
- **Atributos por Nivel (1-5):**
    - `level`: El nivel SFIA de maestría.
    - `behavioral_description`: Descriptor de comportamiento del nivel según SFIA.
    - **`learning_content`**: Unidades de aprendizaje específicas para ese skill.
    - **`performance_indicator`**: Criterio de desempeño (Métricas de éxito del skill).

---

## 3. Lógica de Negocio y Estados

El sistema aplica las siguientes reglas durante el guardado:

- **Diferenciación Conceptual:** El controlador distingue entre el BARS global del rol (en `ai_archetype_config`) y los niveles técnicos de las habilidades (en `bars_levels`).
- **Aislamiento de Seguridad:** Todos los elementos (Rol, Competencia, Skill) se guardan en el catálogo pero quedan "bloqueados" bajo el estado `pending_approval`.
- **Sincronización de Blueprint:** El controlador busca en el JSON el nodo `skill_blueprint` para extraer las unidades de aprendizaje y criterios generados, mapeándolos dinámicamente según el nombre del skill y su nivel SFIA.
- **Eventos de Dominio:** Se dispara `RoleRequirementsUpdated`, alertando a otros módulos (como Reclutamiento o Formación) de que un nuevo diseño de puesto está en camino.

---

## 4. Próximos Pasos (Roadmap)

- **Firma Masiva:** Implementar la lógica en `RoleDesignerService` para que al aprobar el Rol, todos los Skills en `pending_approval` que le pertenezcan también pasen a `active` automáticamente.
- **Visualización de Unidades:** Mejorar el componente `Roles/Approval.vue` para mostrar las unidades de aprendizaje y criterios de desempeño en la vista de revisión del stakeholder.

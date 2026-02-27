# Sesión 2026-02-25 — Estandarización de Identidad y Persistencia de Ingeniería de Talento

> **Fecha:** 2026-02-25
> **Estado al cierre:** Backend Estabilizado · IA "Ingeniero de Talento" Unificado · Persistencia de BARS corregida

---

## Contexto de la sesión

Se detectó que el "Ingeniero de Talento" (anteriormente fragmentado en varios agentes como Curador de Competencias o Diseñador de Roles) no lograba persistir correctamente la **Ingeniería de Detalle** (BARS). Al generar los datos con IA y guardar, la relación se perdía, dejando el modal vacío al reabrir. Además, existían errores de importación en el frontend que impedían la captura del `scenarioId`.

---

## Trabajo realizado

### 1. Estandarización de Identidad IA 🤖

Se ha unificado la personalidad de la IA en toda la plataforma bajo el nombre **"Ingeniero de Talento"**.

- **Base de Datos:** Renombrado el agente ID 7 de "Curador de Competencias" a "Ingeniero de Talento".
- **Servicios Backend:** Actualizadas todas las llamadas a `agentThink` en:
    - `TalentDesignOrchestratorService.php`
    - `CompetencyCuratorService.php`
    - `RoleDesignerService.php`
- **Prompts:** Ajustadas las instrucciones de sistema para que la IA se reconozca a sí misma con la nueva identidad, mejorando la coherencia en las respuestas.

### 2. Estabilización del Frontend (Step 3: Engineering) 🛠️

**Archivo:** `resources/js/components/ScenarioPlanning/Step3/EngineeringBlueprintSheet.vue`

- **Fix `vue-router`:** Se eliminó la dependencia de `vue-router` que causaba errores de importación en entornos de renderizado dinámico.
- **Robustez de URL:** Se implementó una lógica de extracción de `scenarioId` basada en `window.location.pathname` que soporta múltiples rutas (`/scenarios/`, `/scenario-planning/`, `/strategic-planning/`).
- **Endpoint Correction:** Se corrigió la ruta de la API de `/api/v1/scenarios` a `/api/scenarios`.

### 3. Persistencia de Ingeniería de Detalle (BARS) 💾

Se resolvió el problema de "datos fantasma" donde la ingeniería generada por la IA no se guardaba permanentemente.

- **Modelo `ScenarioRoleCompetency.php`:**
    - Se añadió la relación `version()` para vincular el mapeo con la `CompetencyVersion` creada por el Ingeniero de Talento.
    - Se implementó un accesor virtual `metadata` que actúa como puente: `mapping.metadata` ahora expone directamente los BARS de la versión asociada.
    - Registrado en `$appends` para que el frontend reciba los datos automáticamente en cada carga de la matriz.
- **Controlador `Step2RoleCompetencyController.php`:**
    - Se optimizó el método `saveMapping` para usar `load('version:id,metadata')` antes de responder.
    - Se refactorizó el guardado para que sea "delta-based" (solo actualiza campos enviados), protegiendo la relación `competency_version_id` de reseteos accidentales.
- **Matriz de Competencias (`RoleCompetencyMatrix.vue`):**
    - Se actualizó el payload de guardado para incluir `competency_version_id`, asegurando que el "vínculo de ingeniería" se preserve al hacer cambios rápidos en la matriz.

---

## Decisiones clave

| Pregunta                              | Decisión                                                                                                                                                     |
| :------------------------------------ | :----------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ¿Por qué unificar nombres de agentes? | Para evitar errores 404/500 cuando el orquestador busca un agente que solo existe en el prompt pero no en la BD.                                             |
| ¿Dónde guardar los BARS generados?    | En `competency_versions`. Esto permite que una competencia mantenga su definición original pero tenga "sabores" técnicos distintos según el rol y escenario. |
| ¿Metadata persistente o volátil?      | Persistente y vinculada vía `competency_version_id`. El usuario ya no pierde su trabajo al cerrar el modal.                                                  |

---

## Deuda técnica y Pendientes

1.  **Badge 🤖/👤 en Matrix:** La columna `source` está lista en BD, falta el indicador visual en `CellContent.vue`.
2.  **Complejidad Cognitiva:** `TalentDesignOrchestratorService::applyProposals` sigue requiriendo un refactor (SonarQube 34/15).
3.  **Auditoría de Skills:** Validar que al crear skills en "Incubación" desde el blueprint, estas se asocien correctamente a la organización del usuario.

---

## Próximos Pasos

```
1. Validar el flujo: Generar BARS -> Guardar -> Reabrir -> Verificar datos persistidos.
2. Implementar iconos de origen (Robot/Humano) en la matriz principal.
3. Limpieza de logs y warnings de consola en el frontend.
```

# 📚 Documentación Strato

Índice centralizado de toda la documentación del proyecto.

---

## 🚀 Inicio Rápido

- **[QUICK_START.md](QUICK_START.md)** - Cómo empezar (5 minutos)
- **[CHEATSHEET_COMANDOS.md](CHEATSHEET_COMANDOS.md)** - Comandos útiles
- **[ROADMAP_ESTRATEGICO_2026.md](ROADMAP_ESTRATEGICO_2026.md)** - 🗺️ **VISIÓN LARGO PLAZO Y TALENTO 360**
- **[ROADMAP_STATUS_2026_02_27.md](ROADMAP_STATUS_2026_02_27.md)** - 📊 Status Report Feb 27
- **[ROADMAP_TRANSICION_MVP_ALPHA_BETA_2026.md](ROADMAP_TRANSICION_MVP_ALPHA_BETA_2026.md)** - 🚦 **TRANSICIÓN MVP→ALPHA→BETA** (pendientes + deuda técnica consolidada)
- **[ENTERPRISE_SECURITY_PHASE12_IMPLEMENTACION.md](ENTERPRISE_SECURITY_PHASE12_IMPLEMENTACION.md)** - 🛡️ **PHASE 12 COMPLETADA** (auditoría de acceso + MFA obligatorio + API de seguridad)
- **[SESION_2026_03_20_RESUMEN.md](SESION_2026_03_20_RESUMEN.md)** - 🧬 **REVOLUCIÓN: TALENTO HÍBRIDO Y SINTÉTICO** (Cubo de Skill & Curaduría IA)
- **[STRATOS_GLASS_DESIGN_SYSTEM.md](STRATOS_GLASS_DESIGN_SYSTEM.md)** - 🌌 **SISTEMA DE DISEÑO PREMIUM** (Phosphor & Glass)

---

## 🧬 Estrategia de Talento Híbrido (IA + Humano)

- **[synthetic_and_hybrid_talent_model.md](synthetic_and_hybrid_talent_model.md)** - ⭐⭐⭐ **MODELO STRATOS HYBRID DNA**
- **[hybrid_talent_rollout_plan.md](hybrid_talent_rollout_plan.md)** - 🗺️ **PLAN DE ACCIÓN E IMPLEMENTACIÓN**
    - Hoja de ruta estratégica (Fase 1-5).
    - Roadmap de Integración Técnica y Dashboard.
- **[conceptual_models_deep_dive.md](conceptual_models_deep_dive.md)** - 🧠 **DEEP DIVE: MODELOS CONCEPTUALES (4D, PAI, STARA)**
- **[wand_ai_integration_walkthrough.md](wand_ai_integration_walkthrough.md)** - 🛠️ **GUÍA TÉCNICA: INTEGRACIÓN CON WAND AI**
- **[hybrid_talent_use_cases.md](hybrid_talent_use_cases.md)** - 💡 **CASOS DE USO: DEMOS Y PRESENTACIONES**
    - Escenarios prácticos (Retail, Marketing, RRHH, IT).
    - Valor de negocio de la Fuerza de Trabajo Sintética.
- **[competency_skill_materialization.md](competency_skill_materialization.md)** - 🧪 Protocolo de Curaduría y Materialización de Competencias.

---

## 🎯 Patrones y Arquitectura Frontend (NUEVO)

- **[GUIA_RAPIDA_CRUD_GENERICO.md](GUIA_RAPIDA_CRUD_GENERICO.md)** - ⭐⭐ LEER PRIMERO - Guía rápida en 2 minutos
    - 5 pasos simples para crear nuevo CRUD
    - Sin escribir controladores
    - Sin duplicar rutas
- **[PATRON_JSON_DRIVEN_CRUD.md](PATRON_JSON_DRIVEN_CRUD.md)** - ⭐⭐⭐ Patrón JSON-Driven CRUD completo
    - Explicación de FormSchemaController automático
    - Cómo form-schema-complete.php genera rutas sin duplicación
    - Arquitectura técnica completa
- **[GUIA_CREAR_NUEVO_CRUD_GENERICO.md](GUIA_CREAR_NUEVO_CRUD_GENERICO.md)** - 📖 Guía paso-a-paso detallada
    - 6 pasos con ejemplos concretos
    - Ejemplo con "Certifications"
    - Checklist completo
- **[CHECKLIST_NUEVO_CRUD.md](CHECKLIST_NUEVO_CRUD.md)** - ✅ Checklist operativo con verificaciones
    - Paso 0: Registrar en form-schema-complete.php (IMPORTANTE)
    - Pasos 1-9: Llenar archivos, verificar
    - Troubleshooting
- **[PROGRESO_PRIORITY1_COMPLETO.md](PROGRESO_PRIORITY1_COMPLETO.md)** - ✅ Status Priority 1 - COMPLETADO
- **[STRATOS_GLASS_DESIGN_SYSTEM.md](STRATOS_GLASS_DESIGN_SYSTEM.md)** - 🌌 **SISTEMA DE DISEÑO PREMIUM**
    - Uso de **Phosphor Icons** (Migración de MDI)
    - Componentes `StCardGlass`, `StButtonGlass`, `StBadgeGlass`
    - Estándares de **i18n nativo** en Vue Components
- **[SESION_2026_03_01_RESUMEN.md](SESION_2026_03_01_RESUMEN.md)** - 🔄 Refactorización Core & Estandarización Glass (Mar 2026)

---

## 🛠️ Setup y Configuración

### Desarrollo

- **[COMMITS_SETUP.md](COMMITS_SETUP.md)** - Setup de commits semánticos
- **[GUIA_COMMITS_SEMANTICOS.md](GUIA_COMMITS_SEMANTICOS.md)** - Guía completa de commits
- **[SCRIPT_COMMIT_MEJORADO.md](SCRIPT_COMMIT_MEJORADO.md)** - Explicación del script mejorado

### Autenticación (Sanctum)

- **[AUTH_SANCTUM_COMPLETA.md](AUTH_SANCTUM_COMPLETA.md)** - ⭐ Guía completa Sanctum en Strato
    - Estado actual de la configuración
    - Cómo funciona el flujo de autenticación
    - Solución de problemas
- **[auth_sanctum_api.md](auth_sanctum_api.md)** - Explicación técnica de `auth:sanctum`
- **[auth_sanctum_laravel12.md](auth_sanctum_laravel12.md)** - Configuración específica en Laravel 12

### Versionado y Releases

- **[VERSIONADO_SETUP.md](VERSIONADO_SETUP.md)** - Setup de versionado
- **[GUIA_VERSIONADO_CHANGELOG.md](GUIA_VERSIONADO_CHANGELOG.md)** - Guía completa de versionado
- **[NORMA_VERSIONADO_RELEASES_STRATOS.md](NORMA_VERSIONADO_RELEASES_STRATOS.md)** - 📐 Norma interna oficial de versionado, changelog y releases

#### TL;DR operativo (seguro)

- **Validar monotonicidad local:** `bash scripts/check-version-monotonicity.sh`
- **Ver sugerencia de tipo:** `bash scripts/suggest-release-type.sh`
- **Simular release sin tag/commit:** `npx standard-version --dry-run`
- **Crear release real (auto):** `npm run release:auto`
- **Crear release real (no interactivo):** `npm run release:auto:yes`
- **Si no quieres auto-sync previo:** agrega `-- --no-sync` al comando npm

---

## 📖 Documentación Técnica

### Backend

- **[dia5_api_endpoints.md](dia5_api_endpoints.md)** - 17 endpoints del MVP
- **[dia3_services_logica_negocio.md](dia3_services_logica_negocio.md)** - Lógica de negocio
- **[DIAGRAMA_FLUJO.md](DIAGRAMA_FLUJO.md)** - Diagramas de flujo
- **[FormSchemaController-Complete-Documentation.md](FormSchemaController-Complete-Documentation.md)** - Controller CRUD
- **[MESSAGING_MVP_PROGRESS.md](MESSAGING_MVP_PROGRESS.md)** - ⭐ **Messaging MVP (Phase 1-4 ✅ COMPLETE)** - Sistema de mensajería interna con conversaciones multi-participantes, soft deletes y multi-tenant isolation

### Frontend

- **[DIA6_GUIA_INICIO_FRONTEND.md](DIA6_GUIA_INICIO_FRONTEND.md)** - Inicio con frontend
- **[DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md](DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md)** - Arquitectura full-stack
- **[DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md](DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md)** - Análisis detallado frontend
- **[DIA6_PLAN_ACCION.md](DIA6_PLAN_ACCION.md)** - Plan de acción Día 6 (actualizado)
- **[ACCION_DIA_6.md](ACCION_DIA_6.md)** - Checklist diario Día 6 (actualizado)

### Database

- **[dia1_migraciones_modelos_completados.md](dia1_migraciones_modelos_completados.md)** - Migraciones completadas
- **[dia2_seeders_completados.md](dia2_seeders_completados.md)** - Seeders de datos
- **[DATABASE_ER_DIAGRAM.html](DATABASE_ER_DIAGRAM.html)** - Diagrama ER interactivo (Mermaid)
- **[DATABASE_ER_DIAGRAM.md](DATABASE_ER_DIAGRAM.md)** - Diagrama ER (Markdown + ASCII)
- **[DATABASE_VISUALIZATION_GUIDE.md](DATABASE_VISUALIZATION_GUIDE.md)** - Guía de visualización (8 métodos)
- **[DATABASE_DIAGRAM_README.md](DATABASE_DIAGRAM_README.md)** - Cómo acceder a diagramas
- Nota: `people_skills` quedó como tabla legacy; usar `people_role_skills` para cualquier relación People–Skills.

### Skill Levels System 🆕

- **[SKILL_LEVELS_SYSTEM.md](SKILL_LEVELS_SYSTEM.md)** - ⭐⭐⭐ Sistema de 5 Niveles de Competencia
    - Niveles genéricos: Básico → Intermedio → Avanzado → Experto → Maestro
    - Sistema de puntos (10, 25, 50, 100, 200) para gamificación
    - Componente `SkillLevelChip.vue` con tooltips
    - API endpoint `/catalogs?catalogs[]=skill_levels`
- **[SKILL_LEVELS_ARCHITECTURE_DECISION.md](SKILL_LEVELS_ARCHITECTURE_DECISION.md)** - Decisión arquitectónica completa
    - Opción 1 (implementada): Niveles genéricos universales
    - Opción 2 (roadmap): Niveles específicos por skill para Learning Paths

### People Role Skills (Sistema de Skills con Historial) 🆕

- **[PEOPLE_ROLE_SKILLS_RESUMEN_FINAL.md](PEOPLE_ROLE_SKILLS_RESUMEN_FINAL.md)** - ⭐⭐⭐ LEER PRIMERO - Resumen ejecutivo
    - Problema identificado: inconsistencia people_skills vs role_skills
    - Solución: tabla `people_role_skills` con contexto de rol
    - Estado actual: 129 skills migradas, 74 expiradas, 75 gaps
    - Próximos pasos: Observer, API, Frontend
- **[PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md](PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md)** - 📖 Documentación técnica completa
    - Schema de tabla (13 columnas)
    - API del Repository (10+ métodos)
    - Scopes y helpers del modelo
    - Casos de uso reales
    - Comandos útiles
- **[PEOPLE_ROLE_SKILLS_FLUJO.md](PEOPLE_ROLE_SKILLS_FLUJO.md)** - 📊 Diagramas y flujos (Mermaid)
    - Flujo de asignación de rol
    - Diagrama de estados (skill lifecycle)
    - Diagrama de componentes (arquitectura)
    - Diagrama de secuencia (cambio de rol)
    - Diagrama ER (relaciones)
    - Casos de uso con ejemplos SQL

```bash
./verify-people-role-skills.sh  # Verifica implementación completa
```

---

## 🏗️ Ciclo de Vida y Flujo de Aprobación (NUEVO)

- **[role_lifecycle_and_approval_flow.md](role_lifecycle_and_approval_flow.md)** - ⭐⭐⭐ **GUÍA MAESTRA: Ciclo de Vida del Rol**
    - Estados del Rol (`proposed`, `pending_review`, `pending_signature`, `active`)
    - Certificación de Importación vs Aprobación Estratégica
    - Nodos Gravitacionales (Departamentos como anclas)
- **[competency_skill_materialization.md](competency_skill_materialization.md)** - 🧠 **Materialización de Habilidades (Skill Wizard)**
    - Transformación de Competencias Estratégicas en Modelos Ejecutables
    - Generación AI de Niveles 1-5 (SFIA/BARS)
    - Unidades de Aprendizaje e Indicadores de Desempeño
- **[approval_flow_documentation.md](approval_flow_documentation.md)** - Especificaciones técnicas del motor de aprobación

---

## 📊 Estado y Tracking

### MVP

- **[PENDIENTES_ACTIVOS_2026_04_03.md](PENDIENTES_ACTIVOS_2026_04_03.md)** - ⭐ **TABLERO VIGENTE** de pendientes activos (corte 3 Abr 2026)
- **[memories.md](memories.md)** - Memoria de contexto (actualizada)
- **[estado_actual_mvp.md](estado_actual_mvp.md)** - Estado actual del MVP
- **[CHECKLIST_MVP_COMPLETION.md](CHECKLIST_MVP_COMPLETION.md)** - Checklist de completitud
- **[PENDIENTES_2026_03_26.md](PENDIENTES_2026_03_26.md)** - Snapshot histórico (corte 1 Abr 2026)

---

## 🛡️ Calidad, Pruebas y Certificación

- **[PLAN_MAESTRO_VERIFICACION_FUNCIONAL.md](Testing/PLAN_MAESTRO_VERIFICACION_FUNCIONAL.md)** - ⭐⭐⭐ **GUÍA DE CERTIFICACIÓN PARA DEMO**
- **[PLAN_REVISION_SO_LL_POR_ROL.md](Testing/PLAN_REVISION_SO_LL_POR_ROL.md)** - 🌧️/☀️ **REVISIÓN SOLEADO/LLUVIOSO POR ROL**
- **[PLAN_REVISION_FLUJOS_AVANZADOS.md](Testing/PLAN_REVISION_FLUJOS_AVANZADOS.md)** - 🤖 **FLUJOS AVANZADOS (IA, MOVILIDAD, GAMIFICACIÓN)**
    - Cerebro (Orquestador de Agentes)
    - Marketplace Interno y Movilidad Estratégica
    - Rutas de Aprendizaje y Gamificación
- **[Casos_Prueba_Coherencia_Arquitectonica.md](Testing/Casos_Prueba_Coherencia_Arquitectonica.md)** - Pruebas de lógica de negocio (Semáforo)
- **[ESTRATEGIA_QA_MASTER_PLAN.md](ESTRATEGIA_QA_MASTER_PLAN.md)** - Plan maestro de QA técnico (CI/CD, RAGAS, Stress)
- **[FormSchemaTestingSystem.md](FormSchemaTestingSystem.md)** - Sistema de pruebas para CRUDs automáticos
- **[GUIA_AUDITORIA_INTERNA_COMPLIANCE.md](GUIA_AUDITORIA_INTERNA_COMPLIANCE.md)** - Guía paso a paso para ejecutar auditorías internas de compliance
- **[GUIA_AUDITORIA_EXTERNA_COMPLIANCE.md](GUIA_AUDITORIA_EXTERNA_COMPLIANCE.md)** - Guía operativa para preparar y ejecutar auditorías externas con evidencia verificable

---

### Releases

- **[CHANGELOG_SISTEMA_OPERACION.md](CHANGELOG_SISTEMA_OPERACION.md)** - Cambios del sistema

---

## 🎯 Guías Especializadas

### Arquitectura

- **[GUIA_NAVEGACION_ARQUITECTURA.md](GUIA_NAVEGACION_ARQUITECTURA.md)** - Navegación de la arquitectura
- **[GUIA_DESARROLLO_ESTRUCTURADO.md](GUIA_DESARROLLO_ESTRUCTURADO.md)** - Guía de desarrollo estructurado
- **[PANORAMA_COMPLETO_ARQUITECTURA.md](PANORAMA_COMPLETO_ARQUITECTURA.md)** - Visión completa

### UX / UI y Experiencia de Usuario

- **[STRATOS_GLASS_DESIGN_SYSTEM.md](STRATOS_GLASS_DESIGN_SYSTEM.md)** - Sistema de diseño premium (Glass, Phosphor, i18n).
- **[UX_MI_STRATOS.md](UX_MI_STRATOS.md)** - Guía UX específica para el portal Mi Stratos (personas, historias, flujos, jerarquía visual).
- **[UX_MODULE_TEMPLATE.md](UX_MODULE_TEMPLATE.md)** - Plantilla genérica para documentar UX de cualquier módulo Stratos.

### Índices y Referencias

- **[INDICE_DOCUMENTACION_ARQUITECTURA.md](INDICE_DOCUMENTACION_ARQUITECTURA.md)** - Índice de arquitectura
- **[INDICE_FINAL_ENTREGABLES.md](INDICE_FINAL_ENTREGABLES.md)** - Índice de entregables
- **[DIA6_TABLA_REFERENCIA_RAPIDA.md](DIA6_TABLA_REFERENCIA_RAPIDA.md)** - Tabla de referencia rápida

---

## 🆕 Módulos Nuevos (Día 6+)

### Workforce Planning - Planificación Dotacional Estratégica

- **[MODULE_TASKFORCE.md](MODULE_TASKFORCE.md)** - Análisis completo del módulo (referencia)
- **[WORKFORCE_PLANNING_GUIA.md](WORKFORCE_PLANNING_GUIA.md)** - Guía rápida de implementación ⭐ COMIENZA AQUÍ
- **[templates/WORKFORCE_GO_LIVE_CHECKLIST.md](templates/WORKFORCE_GO_LIVE_CHECKLIST.md)** - Template reusable de Go Live (GitHub Issue + Jira)
- **[memories_workforce_planning.md](memories_workforce_planning.md)** - Resumen operativo y pasos Postman (últimas modificaciones)

### 📈 Inteligencia de Talento & Movilidad (Fase 6)

- **[Simulacion_Movilidad_Estrategica.md](Technical/Simulacion_Movilidad_Estrategica.md)** - Motor de Movilidad Estratégica
- **[Inteligencia_Aprendizaje_LMS.md](Technical/Inteligencia_Aprendizaje_LMS.md)** - Hub de LMS e Inteligencia de Aprendizaje
- **[BACKFILL_INTELLIGENCE_METRIC_AGGREGATES.md](BACKFILL_INTELLIGENCE_METRIC_AGGREGATES.md)** - Runbook operativo del backfill histórico de agregados de inteligencia

---

## 📝 Resúmenes Ejecutivos

- **[DIA6_RESUMEN_5_MINUTOS.md](DIA6_RESUMEN_5_MINUTOS.md)** - Resumen en 5 minutos
- **[RESUMEN_EJECUTIVO.md](RESUMEN_EJECUTIVO.md)** - Resumen ejecutivo completo
- **[RESUMEN_FINAL_ARQUITECTURA.md](RESUMEN_FINAL_ARQUITECTURA.md)** - Resumen de arquitectura
- **[ULTRA_RESUMEN.md](ULTRA_RESUMEN.md)** - Versión ultra-corta

---

## 🔧 Troubleshooting y Ayuda

- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Problemas comunes y soluciones
- **[PROMPT_INICIAL_COPIAR_PEGAR.md](PROMPT_INICIAL_COPIAR_PEGAR.md)** - Prompt inicial para Copilot

---

## 📋 Planes y Roadmaps

### 🚀 **Roadmap Frontend MVP** ⭐ ACTUAL

- **[MVP_FRONTEND_ROADMAP.md](MVP_FRONTEND_ROADMAP.md)** - Plan detallado de Frontend para últimas 2 semanas (8-14 días)
    - Módulos: People, Skills, Roles, Gap Analysis, Learning Paths, Dashboard
    - Timeline por fase
    - Criterios de aceptación
    - **👉 COMIENZA AQUÍ para saber qué construir**

### 📅 Planes Pasados

- **[DIA6_PLAN_ACCION.md](DIA6_PLAN_ACCION.md)** - Plan de acción del día 6
- **[ACCION_DIA_6.md](ACCION_DIA_6.md)** - Acciones completadas día 6
- **[DIA6_EVALUACION_INTEGRAL.md](DIA6_EVALUACION_INTEGRAL.md)** - Evaluación integral
- **[DIA6_RESUMEN_DOCUMENTACION_GENERADA.md](DIA6_RESUMEN_DOCUMENTACION_GENERADA.md)** - Resumen de docs generadas

---

## 📚 Otras Guías

- **[ECHADA_DE_ANDAR.md](ECHADA_DE_ANDAR.md)** - Puesta en marcha
- **[ENTREGA_COMPLETA_DIA6.md](ENTREGA_COMPLETA_DIA6.md)** - Entrega completa
- **[LECCIONES_APRENDIDAS_DIA1_5.md](LECCIONES_APRENDIDAS_DIA1_5.md)** - Lecciones aprendidas
- **[STATUS_EJECUTIVO_DIA5.md](STATUS_EJECUTIVO_DIA5.md)** - Status ejecutivo día 5
- **[MAPA_NAVEGACION.md](MAPA_NAVEGACION.md)** - Mapa de navegación
- **[DIA6_COMENTARIOS_CODIGO.md](DIA6_COMENTARIOS_CODIGO.md)** - Comentarios sobre el código

---

## 🚀 Cómo Usar Esta Documentación

1. **Si es tu primer día:**
    - Lee [QUICK_START.md](QUICK_START.md)
    - Luego [memories.md](memories.md)

2. **Si necesitas hacer un commit:**
    - [GUIA_COMMITS_SEMANTICOS.md](GUIA_COMMITS_SEMANTICOS.md)

3. **Si necesitas hacer un release:**
    - [GUIA_VERSIONADO_CHANGELOG.md](GUIA_VERSIONADO_CHANGELOG.md)

4. **Si necesitas entender la arquitectura:**
    - [DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md](DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md)

5. **Si algo no funciona:**
    - [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📊 Estadísticas de Documentación

```
Total de archivos:    45+ documentos
Documentación:        ~500 KB
Últimas actualizaciones: 28 Dec 2025
Estado:               ✅ Centralizada en /docs
```

---

**Última actualización:** 14 Enero 2026

## **Frontend QA**

- **Ámbito:** Checklist para validar visuales e interacción del frontend, con foco en la página Scenario Planning.
- **Componente principal:** [src/resources/js/pages/ScenarioPlanning/Index.vue](src/resources/js/pages/ScenarioPlanning/Index.vue)
- **Comprobaciones rápidas:**
    - **Arranque:** Ejecutar backend y frontend en modo desarrollo:

        ```bash
        composer run dev  # backend (si aplica)
        npm install
        npm run dev       # frontend (Vite)
        ```

    - **Abrir página:** Navegar a la ruta Scenario Planning en la app y verificar carga correcta.
    - **Render SVG:** Verificar gradientes, glow y que no aparezcan warnings tipo "Error in parsing value for 'opacity'" en consola.
    - **Interacción:** Arrastrar nodos, soltar y comprobar que la acción `savePositions` realiza la petición correcta en la pestaña Network.
    - **Consola:** Sin errores JS ni warnings D3 en la consola del navegador.
    - **Responsivo:** Probar en desktop / tablet / mobile (anchos típicos) y verificar que el canvas se redibuja correctamente.
    - **Performance:** Comprobar que la simulación D3 no causa UI jank en escenarios normales (~50-200 nodos).
    - **Tests & Formateo:** Ejecutar pruebas y formateo:

        ```bash
        composer test tests/Path/IfAny --filter ScenarioPlanning
        npx prettier --no-plugin-search --write "src/**/*.{js,ts,vue,css,scss,json,md}"
        npx eslint "src/**/*.{js,ts,vue}" --fix
        ```

- **Referencias:** [docs/GUIA_STRATOS_CEREBRO.txt](docs/GUIA_STRATOS_CEREBRO.txt) (guía del subsistema Cerebro).

- **Notas:** Si detectas discrepancias (payloads, rutas API, o comportamiento visual), crea un issue y añade en la descripción: pasos reproducibles, capturas de pantalla y salida de consola.

---

## 🧭 Documentación avanzada (IA, Motores y Arquitectura)

### 🤖 IA & Motores de Inteligencia

- **[AI_AGENTS_STRATEGY.md](AI_AGENTS_STRATEGY.md)** - Estrategia global de agentes IA en Stratos.
- **[STRATOS_INTELLIGENCE_ECOSYSTEM_MAP.md](STRATOS_INTELLIGENCE_ECOSYSTEM_MAP.md)** - Mapa del ecosistema de inteligencia y sus componentes.
- **[GRAVITATIONAL_NODE_UNIFICATION.md](GRAVITATIONAL_NODE_UNIFICATION.md)** - Modelo conceptual de nodos gravitacionales y conexiones.
- **[psychometric_ai_architecture.md](psychometric_ai_architecture.md)** - Arquitectura psicométrica y uso de IA inferencial.

### ⚙️ Motores específicos (Engines)

- **[Engines/IMPACT_ENGINE.md](Engines/IMPACT_ENGINE.md)** - Motor de impacto y cálculo de retorno.
- **[Engines/PULSE_ENGINE.md](Engines/PULSE_ENGINE.md)** - Motor de pulso organizacional y clima.
- **[Engines/NAVIGATOR_ENGINE.md](Engines/NAVIGATOR_ENGINE.md)** - Motor de navegación estratégica.
- **[Engines/VANGUARD_ENGINE.md](Engines/VANGUARD_ENGINE.md)** - Motor de vanguardia e innovación.
- **[Engines/CERBERO_ENGINE.md](Engines/CERBERO_ENGINE.md)** - Motor de riesgo y monitoreo continuo.

### 🏗️ Arquitectura avanzada

- **[Architecture/SCENARIO_IQ_TECHNICAL_SPEC.md](Architecture/SCENARIO_IQ_TECHNICAL_SPEC.md)** - Especificación técnica de Scenario IQ.
- **[Architecture/TALENT_360_INTELLIGENCE_ECOSYSTEM.md](Architecture/TALENT_360_INTELLIGENCE_ECOSYSTEM.md)** - Ecosistema de inteligencia Talento 360.
- **[Architecture/DIGITAL_TWIN.md](Architecture/DIGITAL_TWIN.md)** - Diseño de Digital Twin de talento.
- **[Architecture/MI_STRATOS_PERSONAL_PORTAL.md](Architecture/MI_STRATOS_PERSONAL_PORTAL.md)** - Arquitectura del portal Mi Stratos.
- **[Architecture/NEO4J_KNOWLEDGE_GRAPH.md](Architecture/NEO4J_KNOWLEDGE_GRAPH.md)** - Uso de Neo4j como grafo de conocimiento.
- **[Architecture/AI_KNOWLEDGE_TRANSFER.md](Architecture/AI_KNOWLEDGE_TRANSFER.md)** - Arquitectura de transferencia de conocimiento con IA.
- **[Architecture/DATA_NOURISHMENT_AGENTS.md](Architecture/DATA_NOURISHMENT_AGENTS.md)** - Agentes de nutrición de datos para mantener el grafo vivo.

### 🧩 Backend técnico, Datos y DevOps

- **[Technical/Neo4j_Concepts_Guide.md](Technical/Neo4j_Concepts_Guide.md)** - Guía de conceptos básicos y avanzados de Neo4j.
- **[Technical/Neo4j_Infrastructure_Deployment.md](Technical/Neo4j_Infrastructure_Deployment.md)** - Despliegue de infraestructura Neo4j en Stratos.
- **[Technical/Automation_n8n_Integration.md](Technical/Automation_n8n_Integration.md)** y **[Technical/N8n_Integration.md](Technical/N8n_Integration.md)** - Integración con n8n y automatización de flujos.
- **[Technical/Talent_Pass_Sovereign_Identity.md](Technical/Talent_Pass_Sovereign_Identity.md)** - Identidad soberana y Talent Pass.
- **[Technical/Internal_Event_Architecture.md](Technical/Internal_Event_Architecture.md)** - Arquitectura de eventos internos.

### 🛡️ Gobernanza, Seguridad y QA avanzado

- **[Governance/STRATOS_GOVERNANCE_FRAMEWORK.md](Governance/STRATOS_GOVERNANCE_FRAMEWORK.md)** - Marco de gobernanza de Stratos.
- **[Security/DIGITAL_SENTINEL_TECH_DOC.md](Security/DIGITAL_SENTINEL_TECH_DOC.md)** - Documento técnico de Digital Sentinel.
- **[HYBRID_GATEWAY_ARCHITECTURE.md](HYBRID_GATEWAY_ARCHITECTURE.md)** - Arquitectura del gateway híbrido (SSO / magic links).
- **[OPCION_C_RAGAS_AGNOSTICO.md](OPCION_C_RAGAS_AGNOSTICO.md)** - Estrategia de QA agnóstica con RAGAS.

### 📈 Scenario Planning & Workforce Planning (docs de profundidad)

- **[ScenarioPlanning/ArquitecturaSieteFases.md](ScenarioPlanning/ArquitecturaSieteFases.md)** - Arquitectura de las siete fases de planificación.
- **[ScenarioPlanning/EstrategiaSieteFases.md](ScenarioPlanning/EstrategiaSieteFases.md)** - Estrategia conceptual de las siete fases.
- **[ScenarioPlanning/MetodologiaPasoaPaso.md](ScenarioPlanning/MetodologiaPasoaPaso.md)** - Metodología paso a paso detallada.
- **[ScenarioPlanning/MODELO_PLANIFICACION_INTEGRADO.md](ScenarioPlanning/MODELO_PLANIFICACION_INTEGRADO.md)** - Modelo integrado de planificación estratégica.
- **[ScenarioPlanning/WORKFORCE_PLANNING_ESPECIFICACION.md](ScenarioPlanning/WORKFORCE_PLANNING_ESPECIFICACION.md)** - Especificación técnica de Workforce Planning.
- **[ScenarioPlanning/WORKFORCE_PLANNING_COMPLETE_SUMMARY.md](ScenarioPlanning/WORKFORCE_PLANNING_COMPLETE_SUMMARY.md)** - Resumen completo de Workforce Planning.

---

## 📦 Candidatos a archivo / legacy

Para reducir ruido en la documentación sin perder historial, algunos documentos de planificación antigua, borradores de PR y notas duplicadas se han marcado como **candidatos a archivo**:

- Ver **[`_archive_candidates.md`](./_archive_candidates.md)** para la lista completa y el comando sugerido (`git mv`) para moverlos a `docs/_archive/` antes de decidir eliminarlos.

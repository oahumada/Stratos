# 📝 Lista de Tareas Pendientes (TODO) - Stratos

Esta lista consolida todos los elementos que **no están al 100%** extraídos de los distintos Roadmaps del proyecto.

## 🌊 Wave 2: Bloque B & D (Expansión y Ecosistema)

- [x] **B1 - Infraestructura:** Neo4j Live (Container dockerizado funcionando; sincronización `neo4j:sync` programada en Laravel Schedules).
- [ ] **B5 - Mobile PX:** App Nativa pendiente (v1 UI en web implementada).
- [x] **D1 - Gateway Híbrido:** SSO OAuth (Google/Microsoft) y rutas firmadas implementados.
- [x] **D2 - LMS & Mentor Hub:** Orquestación de sesiones de mentoría e integración con LMS operativa.
- [x] **D5 - Misiones de Gremio:** Sistema de Quests soporta misiones colectivas/individuales.
- [x] **D6 - Timeline Evolutivo:** `DnaTimelineService` implementado.
- [x] **D8 - Talent Pass (CV 2.0):** Modelo de datos y API implementados.
- [x] **D9 - Sovereign Identity:** Credenciales Verificables (W3C Standard) estructuradas en la DB con un emulador de firmas criptográficas `VerifiableCredential` activado.

## 🏗️ FASE 0: Refactorización Estructural y Seguridad

- [x] **Sistema de "Feature Toggles" (Tenant Modules):** `active_modules` en DB, middleware, y UI condicional.
- [x] **Gateway Híbrido (SSO/Identity):** Integrado OAuth2 (`SsoController`).
- [x] **Arquitectura de Eventos Interna:** Implementado Event Bus con metadatos de dominio y sistema `Event Store` (Side-car Event Sourcing) para inmutabilidad y auditoría.

## 🪐 FASE 1: Consolidación "Stratos Core" y "Stratos Map"

- [x] **Diccionarios Dinámicos:** CRUD robusto de Skills y Competencias implementado.
- [x] **Gestión Jerárquica:** Organigrama interactivo (Tree) y gestión de departamentos.
- [x] **Stratos Map (Motor de Renderizado):** Heatmap organizacional y visualización de nodos.
- [x] **Algoritmo de Mapeo Base:** Cálculo de Gap Analysis en tiempo real.

## 🔮 FASE 4: Módulos Predictivos y "Talento Sintético"

- [x] **Stratos Insights (Psicometría y Fit):** Tests conversacionales (Assessment Chat) y triangulación 360.
- [x] **Stratos Radar / Evolve (Parcialmente implementado como Scenario IQ):** Simulador de Crisis, Career Paths y Simulación Agéntica implementados.
- [x] **Métricas Históricas y Data Lake (Event Sourcing):** Captura mensual inmutable ("Foto" de la empresa) y cálculo macro del "Stratos IQ" (Velocidad de Aprendizaje Organizacional) completados y operando mediante cron job diario mensual (`captureSnapshot`).

## 🦄 FASE 6: Expansión de Ingeniería de Talento

- [x] **Escenarios Agénticos:** Simulación de cambios organizacionales y generación de escenarios vía LLM (AgenticScenarioService).
- [x] **Selección Inteligente:** Stratos Magnet con matching técnico-cultural agéntico activo (MatchmakerService).
- [x] **People Experience (PX) Avanzado:** Campañas de PX y Pulse Surveys.

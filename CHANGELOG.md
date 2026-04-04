# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [0.12.3](https://github.com/oahumada/Stratos/compare/v0.12.2...v0.12.3) (2026-04-04)


### 🐛 Correcciones de Bugs

* close all open technical debt items (N+1, API docs, onboarding, debt closure) ([8d16cf2](https://github.com/oahumada/Stratos/commit/8d16cf23d9b0b85a8f40a57b105e6d26bea5b1dd))

### [0.12.2](https://github.com/oahumada/Stratos/compare/v0.12.1...v0.12.2) (2026-04-04)


### 🐛 Correcciones de Bugs

* **changelog:** support patch (###) release headers in enrich-changelog.cjs ([f2cb86f](https://github.com/oahumada/Stratos/commit/f2cb86fdaf54d54cd4bd70719c90e54023437efc))

### [0.12.1](https://github.com/oahumada/Stratos/compare/v0.12.0...v0.12.1) (2026-04-04)

## [0.12.0](https://github.com/oahumada/Stratos/compare/v0.11.5...v0.12.0) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **org-chart:** Org Charting interactivo — árbol de personas y departamentos ([f802b68](https://github.com/oahumada/Stratos/commit/f802b68e5a47bdb1e5dca2c4a0dfcb648162672f))
  **Backend:**
  - OrgPeopleChartController: tree (people+departments), subtree, stats
  - GET /api/org-chart/people — árbol jerárquico by supervised_by
  - GET /api/org-chart/people?view=departments — árbol by parent_id
  - GET /api/org-chart/people/{id}/subtree — subárbol a partir de persona
  - GET /api/org-chart/people/stats — total, managers, span control, max_depth
  - 10 tests passing (OrgChartTest)
  **Frontend:**
  - OrgChart/Index.vue: árbol expandible recursivo, switch people/departments, stats KPI cards, búsqueda/filtro en tiempo real, drawer lateral con detalle y acción 'ver subárbol', renderizado con defineComponent recursivo
  - Inertia route: /org-chart
  - AppSidebar: 'Organigrama' entry con PhTreeStructure icon (admin/hr_leader)
* **performance-ai:** Performance AI module — cycles, reviews, calibration, insights ([2f376c9](https://github.com/oahumada/Stratos/commit/2f376c9fc12ffdc69a38d8cc544d1a7f384e2841))
* **skill-intelligence:** Skill Intelligence v2 — heatmap, top gaps, upskilling, coverage ([0595c60](https://github.com/oahumada/Stratos/commit/0595c604703bd4d95d73e2b50e729a6110f3f675))
  **Backend:**
  - SkillIntelligenceService: departmentHeatmap(), topGaps(), upskillingRecommendations(), coverageSummary() — all org-scoped, computed from PeopleRoleSkills
  - SkillIntelligenceController: 4 GET endpoints (heatmap, top-gaps, upskilling, coverage)
  - Routes: GET /api/skill-intelligence/{heatmap,top-gaps,upskilling,coverage}
  - 13 tests passing (SkillIntelligenceTest)
  **Frontend:**
  - SkillIntelligence/Index.vue: 4-tab page (Top Brechas, Heatmap, Upskilling, Cobertura) with KPI cards, color-coded heatmap matrix, upskilling recommendation cards, coverage progress bars
  - Inertia route: /skill-intelligence
  - AppSidebar: 'Skill Intelligence' entry (admin/hr_leader)

### [0.11.5](https://github.com/oahumada/Stratos/compare/v0.11.4...v0.11.5) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **wfp:** Workforce Planning Fases 2-4 frontend pages ([19a35f3](https://github.com/oahumada/Stratos/commit/19a35f3c84f419bfc797dc7a31f7a017893e5e29))
  - Recomendaciones.vue: Fase 2 motor de palancas — genera y visualiza recomendaciones HIRE/RESKILL/ROTATE/TRANSFER/CONTINGENT/AUTOMATE/HYBRID_TALENT con rationale, probabilidad de éxito, costo estimado y priority_score
  - Gobernanza.vue: Fase 3 execution dashboard — semáforo de riesgo (verde/amarillo/rojo), stats, budget tracking, alerts por tipo, breakdown by_lever y by_unit con progress bars
  - Comparador.vue: Fase 4 — Tab 1 comparador multi-escenario (2-4 scenarios, ranking + dimension matrix); Tab 2 sensitivity sweep (variable/min/max/steps, table con fila óptima resaltada, interpretación)
  - Inertia web routes: /workforce-planning/{recomendaciones,gobernanza,comparador}
  - AppSidebar: 3 nuevos nav items WFP (admin/hr_leader)

### [0.11.4](https://github.com/oahumada/Stratos/compare/v0.11.3...v0.11.4) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **k6:** add spike, rate-limit, cache-failover and smoke test scripts ([1496bb6](https://github.com/oahumada/Stratos/commit/1496bb6f55267be7b0a4ba3b86984a4e22825b03))
  - load-testing-spike.js: 10x surge test (10 → 100 VUs in 10s, 2xx p95<2s)
  - load-testing-rate-limit.js: validates ApiRateLimiter thresholds (auth 300/min, guest 60/min) and X-RateLimit-* response headers
  - load-testing-cache-failover.js: Redis failover scenario with graceful degradation assertions and handleSummary report
  - load-testing-smoke.js: post-deploy health check (3 VUs, 1min, p95<500ms)

### [0.11.3](https://github.com/oahumada/Stratos/compare/v0.11.2...v0.11.3) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **admin:** Admin Panel Polish – AlertConfiguration + AuditLogs + sidebar entries ([1369210](https://github.com/oahumada/Stratos/commit/1369210628b961ca568aa8093ab52b1ec2113de7))
  - Create Admin/AlertConfiguration.vue: full CRUD UI for alert thresholds, critical alerts list with acknowledge action, stats cards
  - Create Admin/AuditLogs.vue: paginated audit log viewer with filters (action, entity type, period), change diff dialog
  - Wrap Admin/Operations.vue in AppLayout (was missing layout)
  - Add Admin Operations and Audit Logs to AppSidebar (admin-only)
  - Add PhGear + PhClipboardText phosphor icons to sidebar imports
  - Add /admin/audit-logs Inertia web route

### [0.11.2](https://github.com/oahumada/Stratos/compare/v0.11.1...v0.11.2) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **messaging:** deploy staging — Inertia routes + sidebar + AppLayout ([7249848](https://github.com/oahumada/Stratos/commit/72498485995793b7a254ba3f674937490bafa740))
  - Registrar rutas Inertia web para Messaging (Index + Settings)
  - Añadir 'Mensajes' al sidebar con PhChatCircle icon
  - Wrappear Messaging/Index.vue y Messaging/Settings.vue en AppLayout
  - 19 tests de Messaging pasando, 0 failures

### [0.11.1](https://github.com/oahumada/Stratos/compare/v0.11.0...v0.11.1) (2026-04-04)

## [0.11.0](https://github.com/oahumada/Stratos/compare/v0.10.36...v0.11.0) (2026-04-04)

### [0.10.36](https://github.com/oahumada/Stratos/compare/v0.10.34...v0.10.36) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **workforce-planning:** Fase 4 — comparador multi-escenario + sensitivity sweep ([7156fcf](https://github.com/oahumada/Stratos/commit/7156fcfc214a6826896bacb18258f402d385b944))

### [0.10.34](https://github.com/oahumada/Stratos/compare/v0.10.32...v0.10.34) (2026-04-04)


### ✨ Nuevas Funcionalidades

* **workforce-planning:** Fase 3 — gobernanza + dashboard de ejecución ([4cd649e](https://github.com/oahumada/Stratos/commit/4cd649ef99b5e0c9e64f7ca2c31b96825a4a7880))

### [0.10.32](https://github.com/oahumada/Stratos/compare/v0.10.30...v0.10.32) (2026-04-04)

### [0.10.25](https://github.com/oahumada/Stratos/compare/v0.10.23...v0.10.25) (2026-04-04)


### 🐛 Correcciones de Bugs

* **tests:** repair 6 failing tests for QA window ([19607ee](https://github.com/oahumada/Stratos/commit/19607ee90c7a045b3ea565f7183c9e413b2dc44b))

### [0.10.23](https://github.com/oahumada/Stratos/compare/v0.10.22...v0.10.23) (2026-04-04)


### 🐛 Correcciones de Bugs

* **n1:** eager load N+1 fixes + query performance tests + LmsService syntax ([8d8edc8](https://github.com/oahumada/Stratos/commit/8d8edc82799f0dae676e5e07c5bcbadccaf07aea))

### [0.10.22](https://github.com/oahumada/Stratos/compare/v0.10.20...v0.10.22) (2026-04-04)


### ♻️ Refactorización

* NotificationPreferences.vue for improved readability and consistency; update load testing scripts for better performance metrics; add ApiRateLimitMiddleware for enhanced API rate limiting ([710d12f](https://github.com/oahumada/Stratos/commit/710d12f2ce872c9d90cb77e2dae73943327673c0))

### [0.10.20](https://github.com/oahumada/Stratos/compare/v0.10.18...v0.10.20) (2026-04-04)


### 🐛 Correcciones de Bugs

* **tests:** repair IntegrationPhase3Test + update sprint debt doc with real status ([09f8dd2](https://github.com/oahumada/Stratos/commit/09f8dd24bb538dc5e779c468a38477f143ae58f7))


### ✨ Nuevas Funcionalidades

* **tech-debt:** complete T3/T4/T6/T8 - indexes, k6 execution, docs ([4aedf8a](https://github.com/oahumada/Stratos/commit/4aedf8a3dbda3ba7c9830de6b5ae95ced7d68e7d))

### [0.10.18](https://github.com/oahumada/Stratos/compare/v0.10.17...v0.10.18) (2026-04-04)

### [0.10.17](https://github.com/oahumada/Stratos/compare/v0.10.16...v0.10.17) (2026-04-04)


### ✨ Nuevas Funcionalidades

* Add 2 k6 post-production scripts (Phase 3 monitoring) ([e54410c](https://github.com/oahumada/Stratos/commit/e54410c6e8ff97684620af8af9bf3d3386df1e60))
* Add 3 k6 production canary scripts (Phase 2 deployment) ([a4bf0a9](https://github.com/oahumada/Stratos/commit/a4bf0a9e38c5f18c67799b4a72986c339130dd92))
* Add 3 k6 staging test scripts (Phase 2 execution) ([a649a75](https://github.com/oahumada/Stratos/commit/a649a75068d29895260527a96a45d8554bbbd4fc))
* k6 load testing suite merged to main ([3d262ee](https://github.com/oahumada/Stratos/commit/3d262ee13115f34436d47b48256a3b50b4df4a50))
* **lms:** close V2-01 policy UX and certificate template selector ([c3ed0b6](https://github.com/oahumada/Stratos/commit/c3ed0b6055e469b00b7824323b7011d8270095a9))
* **lms:** close V2-02 extended notifications with Slack channel ([4e02241](https://github.com/oahumada/Stratos/commit/4e022411dc3c6529ed4f068ac5bdcb509c720edf))
* **lms:** close V2-03 runbook lms:sync-progress operativo ([d685de9](https://github.com/oahumada/Stratos/commit/d685de9d5ebd62b6a58625e3914c7ce52a7f3e49))
* **lms:** close V2-04 SSO integration with LinkedIn Learning PoC ([73b0805](https://github.com/oahumada/Stratos/commit/73b08057d8200dea0078d3f29ac913fedebdc5be))
* **scenario:** close V2-06 People Experience integration E2E validation ([fdd1c8d](https://github.com/oahumada/Stratos/commit/fdd1c8d0b71cfc9fcdd305160c80e844181a7993))


### 🐛 Correcciones de Bugs

* Restore package version to 0.10.16 after merge ([038409b](https://github.com/oahumada/Stratos/commit/038409b02b75d5cc75b5c93b618cec3af9a7134d))

### [0.10.3](https://github.com/oahumada/Stratos/compare/v0.10.2...v0.10.3) (2026-04-03)


### ✨ Nuevas Funcionalidades

* remove outdated staging deployment checklist and architecture documentation ([4b6a9ed](https://github.com/oahumada/Stratos/commit/4b6a9ed36c9c2acad097f4910b44a33f3b056c8c))


### 🐛 Correcciones de Bugs

* **release:** align suggested bump with standard-version ([8178ab7](https://github.com/oahumada/Stratos/commit/8178ab7de860070e71a288fbcc6f573def630ca2))

### [0.10.2](https://github.com/oahumada/Stratos/compare/v0.10.1...v0.10.2) (2026-04-03)


### 🐛 Correcciones de Bugs

* **release:** automate preflight sync and safe npm release commands ([cc5996a](https://github.com/oahumada/Stratos/commit/cc5996af6b3b63b79f497a602ed4731aada58c35))

### [0.10.1](https://github.com/oahumada/Stratos/compare/v0.10.0...v0.10.1) (2026-04-03)


### 🐛 Correcciones de Bugs

* **release:** prevent recursive bumps and version drift ([262d5fb](https://github.com/oahumada/Stratos/commit/262d5fb6a7732e5068d56951c4394a1836e4d917))

## [0.10.0](https://github.com/oahumada/Stratos/compare/v0.9.0...v0.10.0) (2026-04-03)

## [0.9.0](https://github.com/oahumada/Stratos/compare/v0.8.0...v0.9.0) (2026-04-03)

## [0.8.0](https://github.com/oahumada/Stratos/compare/v0.7.1...v0.8.0) (2026-04-03)

### [0.7.1](https://github.com/oahumada/Stratos/compare/v0.7.0...v0.7.1) (2026-03-31)


### 🐛 Correcciones de Bugs

* **lms:** close phase 1-2 certificate revocation gaps ([dea5a0a](https://github.com/oahumada/Stratos/commit/dea5a0ad9fb12412d3a0fbe69c69f78bfa2aa94d))

## [0.7.0](https://github.com/oahumada/Stratos/compare/v0.6.0...v0.7.0) (2026-03-31)


### ✨ Nuevas Funcionalidades

* **sprint1:** cerrar iteración 1-4 con LMS hardening y estabilización scenario planning ([10f8582](https://github.com/oahumada/Stratos/commit/10f85828546f918a1220d5398e83ee15f37af674))

## [0.6.0](https://github.com/oahumada/Stratos/compare/v0.5.2...v0.6.0) (2026-03-31)


### ✨ Nuevas Funcionalidades

* Scenario Planning Phase 2 - Complete implementation ([5674ab4](https://github.com/oahumada/Stratos/commit/5674ab496515a7147e7acfc58fc18829cc7ff93e))

### [0.5.2](https://github.com/oahumada/Stratos/compare/v0.5.1...v0.5.2) (2026-03-31)


### 🐛 Correcciones de Bugs

* **tests:** corregir suite completa de tests PHP - 927 passing, 0 failed ([71c3e66](https://github.com/oahumada/Stratos/commit/71c3e66182a0481bb670b1c95308b8b436a7c4fa))

### [0.5.1](https://github.com/oahumada/Stratos/compare/v0.5.0...v0.5.1) (2026-03-31)


### 🐛 Correcciones de Bugs

* **scenario-generation:** auto-accept import when generation completes (support headless flows/tests) ([c3e0cfc](https://github.com/oahumada/Stratos/commit/c3e0cfcf3b865a43649d13484234d703ebff91f9))

## [0.5.0](https://github.com/oahumada/Stratos/compare/v0.4.1-alpha.0...v0.5.0) (2026-03-31)

### ✨ Nuevas Funcionalidades

* Implement LmsOperatorAgent for LMS operations including participant account creation, course invitations, enrollments, and certificate issuance ([eb61c63](https://github.com/oahumada/Stratos/commit/eb61c630dbb768a44dec2b176e56766d6cd340ad))


### 🐛 Correcciones de Bugs

* **scenarios:** stabilize Scenario test suite ([9a8bfee](https://github.com/oahumada/Stratos/commit/9a8bfeeaf304ae4aca6df5343fa2ef5d9bc722a8))
* **versioning:** block empty releases by default ([07cf097](https://github.com/oahumada/Stratos/commit/07cf0979a62ebe4908b7824bddd83a60ba63a00a))

### [0.4.1](https://github.com/oahumada/Stratos/compare/v0.4.1-alpha.0...v0.4.1) (2026-03-31)

### [0.4.1-alpha.0](https://github.com/oahumada/Stratos/compare/v0.4.0...v0.4.1-alpha.0) (2026-03-30)


### ⚡ Mejoras de Rendimiento

* N+1 audit & fixes — batch aggregates, scenario cache, N+1 scan harness, docs ([2a7a4cb](https://github.com/oahumada/Stratos/commit/2a7a4cb68ed12c64aa1cfa0e99b1f08db086400e))
* register TalentRoiService as singleton and use fetchExecutiveAggregates in getExecutiveSummary ([07bf4d1](https://github.com/oahumada/Stratos/commit/07bf4d1a878e1753762cbf87b55e1ebac9641412))


### ✅ Tests

* **E2E:** Scenario Planning comprehensive integration tests - 8 critical endpoints ([d450ea5](https://github.com/oahumada/Stratos/commit/d450ea5d82df2faa7a4a06e85076c0a22d0e124f))
* Phase 2 comprehensive test suite - 45+ test cases ([38d6d70](https://github.com/oahumada/Stratos/commit/38d6d701abdfedef9cfada86b326b03f36d11ae7))
* replace Slack with Telegram in deployment and operations documentation ([d1301c8](https://github.com/oahumada/Stratos/commit/d1301c8f4e4c6c7707b446c7ee5bd07431aa53c0))


### 🐛 Correcciones de Bugs

* Add Controller import and fix Pest test syntax ([1fe8778](https://github.com/oahumada/Stratos/commit/1fe8778ca0156ce00fe58ceb45959b026cadbc1a))
* AuditHeatmap.vue syntax error - remove orphaned closing paren ([599ae85](https://github.com/oahumada/Stratos/commit/599ae8520fada91dd63e4f336cfdd5e1ad48f512))
* Complete production build fixes - Talent Pass Polish Phase ([05104ea](https://github.com/oahumada/Stratos/commit/05104eaf9324077576e82b4a10928188c3075f6a))
* ExecutiveSummary test fixtures - all 9 tests now passing ✅ ([64fa9f5](https://github.com/oahumada/Stratos/commit/64fa9f541f9aefa570830c7f7983cff1bef9764d))
* **tests:** resolve Pest uses() conflicts and replace faker slug in GuideFaqFactory ([be1a12e](https://github.com/oahumada/Stratos/commit/be1a12e8d39bef7a67b573945467c2185c04c491))
* **tests:** stabilize unit migrations and dashboard assertions ([ebdc0d4](https://github.com/oahumada/Stratos/commit/ebdc0d4ad82339821fdd08cdaac96d2fb965b0d5))


### 📚 Documentación

* Add 5 comprehensive operational guides for Messaging MVP staging deployment ([68e3ef6](https://github.com/oahumada/Stratos/commit/68e3ef6c704f9c644bb50fba73d8fd8f85dae1ae))
* Add comprehensive execution roadmap - Options A, B, C ([99560bc](https://github.com/oahumada/Stratos/commit/99560bce4e8f7de04e22cb647f740179d7eac27e))
* Add comprehensive Opción A deployment checklist ([837c7ee](https://github.com/oahumada/Stratos/commit/837c7eeeab24d1a81e155bc6646d8707265e535b))
* Add comprehensive Task 2 planning document (Scenario Planning Phase 2) ([0496f08](https://github.com/oahumada/Stratos/commit/0496f089acfc7b7b69f85fe45728834cf38245be))
* Add comprehensive test execution documentation and verification scripts ([d2a5067](https://github.com/oahumada/Stratos/commit/d2a506785f5ae0ac58f9629432dabf84422ed8d3))
* add N+1 problem explanation and clarify UI impact ([f48ec62](https://github.com/oahumada/Stratos/commit/f48ec62137a73f36147cb69bf01691a8375f3f5a))
* Add Phase 1 component integration guide with step-by-step instructions ([0a289a9](https://github.com/oahumada/Stratos/commit/0a289a96b53a97ffd7d159e1db081e857291ac36))
* Add Phase 1 execution summary and completion report ([343a0d5](https://github.com/oahumada/Stratos/commit/343a0d5babcb6c2e8efd727c3978dc5db0169a67))
* Add Phase 1 integration complete report and success criteria verification ([af6a660](https://github.com/oahumada/Stratos/commit/af6a660a89bdac35454b38693da2f27c63ea48ae))
* Add Phase 2 completion report with full deliverables summary ([43b14d3](https://github.com/oahumada/Stratos/commit/43b14d38ce22f4e9e1fd52030e45dfb8f73f09f0))
* add Phase 2 completion summary and updated PR draft ([c065907](https://github.com/oahumada/Stratos/commit/c065907e3eb7782b61636dc108b07ca01e62c965))
* Add Phase 2.5 completion report and update openmemory ([6c088e4](https://github.com/oahumada/Stratos/commit/6c088e404f11a150d343019a7822f54eec2043db))
* **analysis:** LMS ↔ Talent Pass ↔ Certificate Integration Architecture ([047a757](https://github.com/oahumada/Stratos/commit/047a7579484a138eb007b7437026272e5d25a04a))
* **changelog:** add v2.0 certificate governance release note ([987e471](https://github.com/oahumada/Stratos/commit/987e471c3a8957d20bb38ae3fe30e62312ac02a3))
* Complete Demo Guide & Technical Architecture for Talent Pass ([bfbad5a](https://github.com/oahumada/Stratos/commit/bfbad5aae83a57233581a1ffcf8f651a4b336787))
* Complete Talent Pass CV 2.0 deployment planning ([901e513](https://github.com/oahumada/Stratos/commit/901e513e53f75a2c6e871d3d00343c6f005590fb))
* finalize Phase 2 N+1 optimization summary ([a913fb6](https://github.com/oahumada/Stratos/commit/a913fb6ed431f0fd582dcc7fc6e228062a00f850))
* Finalize Phase 3 - Talent Pass documentation complete ([22bb805](https://github.com/oahumada/Stratos/commit/22bb80545127d9dd6d594214a570d16614925c02))
* Mark Messaging MVP as MERGED & COMPLETE ([2efbbc5](https://github.com/oahumada/Stratos/commit/2efbbc50e962f537486fcb0bb65deb90aa5387c8))
* Mark Phase 2.5 as 100% complete and production ready ([c0dd165](https://github.com/oahumada/Stratos/commit/c0dd16581f34f2b3e2b65b4e5448b8884f94eca6))
* Mark Task 1 complete - v0.3.0 merged to main ([3f808c0](https://github.com/oahumada/Stratos/commit/3f808c0d9b608427dedafced0ec0b1033a62b2d5))
* **memory:** V2.0 Sprint 1 parallel track planning complete ([d3e5a0a](https://github.com/oahumada/Stratos/commit/d3e5a0a51172ae038089b7c6ab4a83c40f2856b2))
* Messaging MVP staging deployment plan & quick-start guide ([fc4982d](https://github.com/oahumada/Stratos/commit/fc4982d804723e97eb0d697742ad7e6ab64d6811))
* **pendientes:** mark Priority 3 v1.2 enhancements as completed [884532b4] ([e66ed39](https://github.com/oahumada/Stratos/commit/e66ed3902066e393381455a2dfff3b13ecad7d36))
* **pendientes:** update test stabilization and push status ([c8019c5](https://github.com/oahumada/Stratos/commit/c8019c556c6e3e1aa75a7275790debaddbfc8889))
* Phase 3 completion report — 31% total harness speedup ([906fa94](https://github.com/oahumada/Stratos/commit/906fa94ef008bdd6ca4123137789fb55a6010a86))
* Phase 3 comprehensive documentation update ([51c368c](https://github.com/oahumada/Stratos/commit/51c368c3b934b88151e860aac2afb594668a2f78))
* Phase 3.3-3.4 completion - Executive Summary & Org Chart (1,610 LOC, 6/9 tests) ([7781395](https://github.com/oahumada/Stratos/commit/77813956c0c5e2b490032515556b167c0972b3e9))
* Phase 4 final — Redis caching architecture complete ([40e2012](https://github.com/oahumada/Stratos/commit/40e2012359e37cdd8136741e18fe6c88cab4fd35))
* Phase 5 completion - Admin Operations Alpha-1,2,3,4 DELIVERED + Messaging MVP ready ([80d45e8](https://github.com/oahumada/Stratos/commit/80d45e87262abf04fffbd6fea67929ea96ea6987))
* Phase 5 completion - Staging deployment ready ([8d1bdfc](https://github.com/oahumada/Stratos/commit/8d1bdfc2bbc6171fb0878a4b37ebefeff7c4898d))
* record Phase 2.1 singleton caching improvements ([be0e7a1](https://github.com/oahumada/Stratos/commit/be0e7a1662585e9b047da9ef3ce20b992dd9811a))
* Record Task 1 Command Center integration in openmemory ([d9cd73c](https://github.com/oahumada/Stratos/commit/d9cd73cdcfadd19e879d5fba5644e64960e1ec81))
* **refine:** LMS-Talent Pass Integration - Product decisions locked, models & service spec finalized ([8c4d449](https://github.com/oahumada/Stratos/commit/8c4d449b35e6cb282bf89eb61026d2ccbbbfcca5))
* session closure — Phase 2 complete with 29% harness speedup ([f18c0b7](https://github.com/oahumada/Stratos/commit/f18c0b7c8c6a6f73a8fb35dbe34ad2a1f9643396))
* Task 1 Phase 1 execution summary - UX components complete ([afd9cd1](https://github.com/oahumada/Stratos/commit/afd9cd1ab935c26080755ba7d1931a572d835b05))
* Task 1 Phase 2 execution summary - SLA Alerting system complete ([a67d91d](https://github.com/oahumada/Stratos/commit/a67d91d6646552234578455b2654702e9d53983b))
* Task 1 Phase 3 execution summary - Audit Trail system complete ([6aecfad](https://github.com/oahumada/Stratos/commit/6aecfad91da8918a0714513973a0e6ac2694e411))
* Update documentation - Talent Pass v1.0 COMPLETE ([c938455](https://github.com/oahumada/Stratos/commit/c9384554d54a6cb6e7d12c20aa8bdced038ec83b))
* update openmemory with Phase 2 executive aggregates implementation ([c9da208](https://github.com/oahumada/Stratos/commit/c9da2082711a54f511983c6669fe3388cf48ac01))
* Update PENDIENTES - 5 operational guides ready for Mar 27 deployment ([b69b6c0](https://github.com/oahumada/Stratos/commit/b69b6c0a5e24f76c5678958bf4aff407e731ab0b))
* Update PENDIENTES & ROADMAP with N+1 Optimization Suite complete ([b85f556](https://github.com/oahumada/Stratos/commit/b85f55651af3485258d7decbb12f8c38cf57a386))
* Update roadmap with operational guides & Talent Pass deployment planning ([3405e72](https://github.com/oahumada/Stratos/commit/3405e7255cbe22f7bdcfd860d30946ce5bda751c))
* Update ROADMAP_STATUS with Mar 26 completions ([60ae881](https://github.com/oahumada/Stratos/commit/60ae881b7ce365c5cca1be2559316add40cdc2ee))
* Update Task 1 completion status - Command Center integration complete ([63775f5](https://github.com/oahumada/Stratos/commit/63775f59d2980741ea423ea11ca7e14fb83cc2b9))
* **v2.0:** Define V2.0 Sprint structure — LMS Hardening + Scenario Planning Phase 2 + Messaging Teams/BI/Community roadmap ([00374c1](https://github.com/oahumada/Stratos/commit/00374c15eb0fface7320855f431f2db89bcb43a5))
* **v2.0:** lock revocation audit, org signature and gamification decisions across sprint specs ([8b8ea33](https://github.com/oahumada/Stratos/commit/8b8ea3361fe1f78297accd2404d3f993c74995a4))
* **v2.0:** Sprint 1 parallel track specifications — LMS Hardening + Scenario Planning Phase 2 ([6dfdb75](https://github.com/oahumada/Stratos/commit/6dfdb7518222df88cc98d384de84bd65c9981744))


### ✨ Nuevas Funcionalidades

* Add ApprovalDashboard.vue component for Phase 2.5 ([dfff0a1](https://github.com/oahumada/Stratos/commit/dfff0a1a62b368834dcceaad9f43c0d589143c1e))
* Add E2E Browser Tests for Talent Pass (Pest v4) ([3a77a46](https://github.com/oahumada/Stratos/commit/3a77a46f55a17f1ecdc9d7ca63d20591bf053f65))
* add Phase 2 materialized executive aggregates table and refresh command ([5a9dc1a](https://github.com/oahumada/Stratos/commit/5a9dc1a8a80c02b7450ad2cd85d30ae44b57ba5c))
* Add ScenarioAnalyticsController and Phase 1 analytics API endpoints ([b2e6a16](https://github.com/oahumada/Stratos/commit/b2e6a160e3aa748f2d20597abfa74d870ad1d01c))
* Add UX-enhanced admin dashboard components (Gauges, Sparklines, Timeline, Alerts) ([c8d0b8f](https://github.com/oahumada/Stratos/commit/c8d0b8f9416daf492ce94774c388e79cd916e748))
* Admin Operations Dashboard page ([cd25434](https://github.com/oahumada/Stratos/commit/cd254346161cb27385f2e00dd16ac84e0087b370))
* **admin:** operation completion notifications (event, listener, notification, tests) ([12b83b4](https://github.com/oahumada/Stratos/commit/12b83b4b8c66b5388f2cac0e6f854dd1546330d7))
* **alpha-1:** admin operations dashboard complete - controller, routes, UI, tests ([0f25116](https://github.com/oahumada/Stratos/commit/0f25116a8a0c29824b5c35de7ce667e4ba9b4431))
* Complete 5 Talent Pass components (CompletenessIndicator, ExperienceManager, CredentialManager, ExportMenu, ShareDialog) ([d27008d](https://github.com/oahumada/Stratos/commit/d27008d97cd76cf947fe5aab5140e7c777c87bb2))
* Complete Phase 2.5 notifications system ([9b7cd81](https://github.com/oahumada/Stratos/commit/9b7cd810fc46adf06ec8ff2ea70254f6dd7e6d94))
* Create Phase 1 Vue components (ScenarioComparison, Timeline, Metrics, RiskAssessment) ([b8a8d7f](https://github.com/oahumada/Stratos/commit/b8a8d7f0092b27c21c281732a0f3d077bc1a1b6e))
* Create Phase 2 frontend workflow components (Timeline, ApprovalCard, ExecutionPlan) and integrate with Analytics landing page ([8366377](https://github.com/oahumada/Stratos/commit/83663771026944a1b0790e625e22c0084d583173))
* **export:** complete PPTX generation and validate performance baseline ([215963a](https://github.com/oahumada/Stratos/commit/215963acb2ca870a5294a713581b82fa8fb38143))
* **export:** mPDF real PDF generation - Executive Summary to PDF with professional HTML template ([390a6fb](https://github.com/oahumada/Stratos/commit/390a6fb2b023b6929a4cc98731b23cef3d30e514))
* Implement AdminOperationLockService for concurrency management ([a6811fd](https://github.com/oahumada/Stratos/commit/a6811fdbfe25251e03b7688757e5f7781a5fd28f))
* Implement Phase 2 backend (workflow service, approval controller, migration) and initial tests ([69dd2be](https://github.com/oahumada/Stratos/commit/69dd2bec3332ccfb2a1805d1352a31b02321ad5c))
* Implement ScenarioTemplateService for Phase 3 template management ([24e2909](https://github.com/oahumada/Stratos/commit/24e290911fc2c1b694c7bab6bb6dbb8cd9176505))
* implement WhatIfAnalysisService for Phase 3.2 impact simulation ([05f4263](https://github.com/oahumada/Stratos/commit/05f4263c11b108345d7163e7736c848a96ff3511))
* Integrate ApprovalDashboard into Analytics landing page ([d5c7c8e](https://github.com/oahumada/Stratos/commit/d5c7c8e139a368afb5a6d450a8a730e78264d5e3))
* Integrate Audit Trail components into Command Center landing with menu navigation ([fe296a2](https://github.com/oahumada/Stratos/commit/fe296a23618d094b7ecae82df2484420109a5c40))
* Integrate Phase 1 Analytics components into ScenarioPlanning section ([35ef261](https://github.com/oahumada/Stratos/commit/35ef2610cf54ae0c7cd4d132ecbbf265820f0695))
* Phase 2 SLA Alerting - Services, Controller, and API routes ([64b89fe](https://github.com/oahumada/Stratos/commit/64b89feb0cff50a682656e1b42718ad12ad93db9))
* Phase 2 SLA Alerting - Vue 3 frontend components ([21e3782](https://github.com/oahumada/Stratos/commit/21e378235e7efee63863902ea4bf476c4e70c806))
* Phase 2 SLA Alerting system - Database migrations and Eloquent models ([cb3804e](https://github.com/oahumada/Stratos/commit/cb3804e6600440647025ac46e12a7f3b06f4c0e1))
* Phase 3 - Batch business_metrics and financial_indicators queries ([87c2c88](https://github.com/oahumada/Stratos/commit/87c2c882088f1601603f7cfeede0aec27f1ed687))
* Phase 3 Audit Trail System - Auto-tracking, timeline, export, heatmap ([a73e0c8](https://github.com/oahumada/Stratos/commit/a73e0c889f8800e8b30a52302677ec3bdea1273c))
* Phase 3 Completion - All 4 steps DONE ✅ ([7b923c8](https://github.com/oahumada/Stratos/commit/7b923c88dfbd3eab383b3ddf8410b95b37f66320))
* Phase 3 Control Center Landing - Alert management UI with 3 options (configuration, history, escalation) ([4336a8d](https://github.com/oahumada/Stratos/commit/4336a8d86faca2072814ac6a45f587d3bb7915cf))
* Phase 3.3 Export Service - PDF/PPTX infrastructure (stubs for actual generation) ([97af2cb](https://github.com/oahumada/Stratos/commit/97af2cbe752e46e104f99e6063d5f526552f6f4f))
* Phase 3.3-3.4 - Executive Summary Dashboard & Org Chart Visualization ([233185f](https://github.com/oahumada/Stratos/commit/233185f166a95e16e58ee6e017a34d0ed8e2e787))
* Phase 4 - Cross-request Redis caching for business_metrics + financial_indicators ([21d5d80](https://github.com/oahumada/Stratos/commit/21d5d80dbf33774d5923709f90154a0c2d9fd3dc))
* Phase 4.1 - Auto-invalidation of Redis cache via model observers ([e4aa3b1](https://github.com/oahumada/Stratos/commit/e4aa3b12f43101a6e1fde1cb1b4a402ae50a7279))
* Phase 5 - Database indices, cache warming & monitoring ([e17d4db](https://github.com/oahumada/Stratos/commit/e17d4db4e299c304c754c0832c61bf1ec5100276))
* Phase 9 Admin Operations dashboard + integration testing ([a15f7c9](https://github.com/oahumada/Stratos/commit/a15f7c9013831d7b0a44ab7148ce755398481563))
* Refactor admin operation event handling and add real-time event tests ([2898ad2](https://github.com/oahumada/Stratos/commit/2898ad2a27c09a8b9da459cb5d4b15b21cd2c214))
* Register AuditObserver on Alert models + add audit API routes ([d66fae1](https://github.com/oahumada/Stratos/commit/d66fae1595c3265358460ba26f92d9c74563b334))
* Talent Pass frontend pages (Index, Create, Edit, Show, PublicView) + routes ([9c7258c](https://github.com/oahumada/Stratos/commit/9c7258cf90d29926bc94421e2515829d8c623e50))
* talent-pass frontend bootstrap + deployment execution plan ([4c41c6e](https://github.com/oahumada/Stratos/commit/4c41c6e5ab0f40dd3f70adbc96c174801186db49))
* Task 1 - Admin Dashboard Polish (Phase 1) - UX components ready ([fb98ef3](https://github.com/oahumada/Stratos/commit/fb98ef38221195c1c450fe7e67b2aa7ac34a8158))
* **v1.2:** Priority 3 enhancements — succession OrgChart, response cache, export templates, scenario comparison ([884532b](https://github.com/oahumada/Stratos/commit/884532b419b480b8467f2b68f486bd6f8d645d29))
* **versioning:** add intelligent pre-push release suggestion and optional auto-release ([6657810](https://github.com/oahumada/Stratos/commit/6657810639b33055203033855d3a6cc6c3cff568))

## [0.4.0](https://github.com/oahumada/Strato/compare/v0.3.0...v0.4.0) (2026-03-25)


### ✨ Nuevas Funcionalidades

* **alpha-1-phase-2:** Add Admin Operations route + ControlCenter landing link ([5da4930](https://github.com/oahumada/Strato/commit/5da49307ab728cec9076bbf5328b5999cb6c603c))
* **alpha-1-phase-2:** Implement operation callbacks + factory + tests ([4d2e3b7](https://github.com/oahumada/Strato/commit/4d2e3b772e350b87fb76ea04cae686955fa0d5a4))
* **alpha-1:** Admin Operations infrastructure - Phase 1 base ([03fde6c](https://github.com/oahumada/Strato/commit/03fde6c76c6cdef297dfac0e06210a777642e0b6))
* **alpha-1:** Frontend build + Admin Operations components ([5957648](https://github.com/oahumada/Strato/commit/595764899f26282d18245c4db61f860a5fd6343c))
* **docs(v2.0):** Lock certificate governance decisions in Sprint specs (full revocation audit, organizational digital signature, certification-driven badges/levels) ([8b8ea33](https://github.com/oahumada/Strato/commit/8b8ea336))

## [0.3.0](https://github.com/oahumada/Strato/compare/v1.0.0-mvp...v0.3.0) (2026-03-25)


### ⚠ BREAKING CHANGES

* Requires database migration before use

### ⏮️ Reversiones

* **GenerateWizard:** restore stable version to fix SFC parse errors ([68fd869](https://github.com/oahumada/Strato/commit/68fd8697edfef7345ad942fb735d0c80ba80df63))


### ✅ Tests

* Add comprehensive verification hub test suite (150+ test cases) ([e0562ac](https://github.com/oahumada/Strato/commit/e0562acc75e5b7110850d414754b5b6257e8e105))
* Add tuning phase & error scenario tests (16 advanced tests, 409 total passing) ([472cf11](https://github.com/oahumada/Strato/commit/472cf11255f3873587a7e0d2c05143ad3e22e2de))
* **chaos:** cover rate-limit exhaustion in scenario generation ([8c7a0a9](https://github.com/oahumada/Strato/commit/8c7a0a9f106d89050c9a092c8209b5ab7734a251))
* **chaos:** cover server exceptions in scenario generation job ([3fa6fb7](https://github.com/oahumada/Strato/commit/3fa6fb72a9d5f41ecb109fb01bd7bb430c428dd5))
* **chaos:** ensure scenario generation completes when RAGAS fails ([691009b](https://github.com/oahumada/Strato/commit/691009b666037fb616c827b64e5dc5b38493a160))
* **chaos:** fallback to DB chunks when Redis is unavailable ([1ee13f5](https://github.com/oahumada/Strato/commit/1ee13f57f1956d6bbc829f12ba0ec22fb2bdb42f))
* **chaos:** handle intel provider outage with failed generation state ([d065f0b](https://github.com/oahumada/Strato/commit/d065f0ba44e403f0c943eb765958c3e4bc63081d))
* fix broken unit tests ([601f5eb](https://github.com/oahumada/Strato/commit/601f5ebc86957d6b520ab08742c3c1eaa7cbd26b))
* fix failing Scenario Planning unit tests and adjust outline contrast ([6c29254](https://github.com/oahumada/Strato/commit/6c29254f8a07518e4aef6171708d8ab2d60eb2a1))
* fix SuccessionPlanCard mapping issue and add missing i18n mock in integration tests ([795b355](https://github.com/oahumada/Strato/commit/795b355f5f888f343bd5146b2228087cf3ca54c7))
* **infra:** Add dedicated test for Tenant Module Feature Toggles ([e13bcd5](https://github.com/oahumada/Strato/commit/e13bcd5bef60c5d201566a5aca801723fb7fc1f5))
* **llm:** add job test for invalid/non-JSON LLM responses marking generation failed ([a997418](https://github.com/oahumada/Strato/commit/a997418b8748701b5e31a0ab49ee7ffaf1e958d0))
* Skip DeepSeekLiveTest if Python Intelligence Service is unreachable ([74c058a](https://github.com/oahumada/Strato/commit/74c058a5a112e0d6732663dbe814ecbbc48f5c73))
* suite completa para Funcionalidades Unicornio — 6 archivos de test ([0e1bd08](https://github.com/oahumada/Strato/commit/0e1bd08d6ee043d66961b653a77702cb31571aff))
* Tarea 4 - Comprehensive Edge Case Tests for 9 Validators ([43e8325](https://github.com/oahumada/Strato/commit/43e832575afafb1fb339e70b5a9113a00b9120ce))


### ♻️ Refactorización

* centralize layout configurations with a base config and extract helper functions ([2eb9817](https://github.com/oahumada/Strato/commit/2eb9817d68894211a1bc903b493b02aa75aa73bd))
* **competencies:** apply CRUD composables in Index ([c522b45](https://github.com/oahumada/Strato/commit/c522b455be72461604b03e33b438fe00afa93733))
* **competencies:** complete DRY CRUD migration with composables ([d63eac6](https://github.com/oahumada/Strato/commit/d63eac672ca9bee40038236eb3e4dfc7db9265eb))
* **database:** reorganize migrations and initial schema ([fabac94](https://github.com/oahumada/Strato/commit/fabac940b52988fd3884fb33e5f40c7fe901f46f))
* improve skill and gap data aggregation logic in API controller to correctly group and combine related entries ([ef77d7c](https://github.com/oahumada/Strato/commit/ef77d7cec5604b48fcb722ce1bd310c70948de10))
* Link scenario competencies to the scenario-specific role pivot record and update related services and jobs ([c4b40aa](https://github.com/oahumada/Strato/commit/c4b40aaf9ff345d7fea86ae141f1206f38f68476))
* mejorar formato y legibilidad en la guía de pruebas de Abacus y en el componente GenerateWizard ([725cc5d](https://github.com/oahumada/Strato/commit/725cc5d8aa07b1ea95b9721db4d0f964231f10e1))
* Migrate RBAC page from Vuetify and Material Design Icons to custom components and Phosphor Icons ([e30c516](https://github.com/oahumada/Strato/commit/e30c51616cbc3e7d8f3a3f634e5f9b559ff1b98f))
* migrate Talento360 and ScenarioPlanning dashboards from Vuetify components to custom `St` components and Tailwind CSS for styling ([7171437](https://github.com/oahumada/Strato/commit/7171437313e3f3529d7f3963e019dc3becef5d7d))
* migrate workforce planning to scenario planning ([ced73b4](https://github.com/oahumada/Strato/commit/ced73b495649b94a7028c1236b33197f1a798080))
* optimize scenario planning components and fix lint errors ([a784ff2](https://github.com/oahumada/Strato/commit/a784ff29e9fd543b20cc7a3fa00e3f1d28c86726))
* **OrganizationChart:** specify return type for renderNode function and remove unnecessary AppLayout wrapper ([5885006](https://github.com/oahumada/Strato/commit/5885006028bbb9663edfe1639011a15b8247195b))
* Redesign talent-related dialogs and cards with a new glassmorphism UI, Tailwind CSS, Phosphor icons, and updated i18n ([7d0ca4a](https://github.com/oahumada/Strato/commit/7d0ca4ab213fbd47581621cadd84eaf1b44745d7))
* remove AppLayout wrapper, rename date formatting helper, and update button variant in PeopleExperience command page ([f1e37f9](https://github.com/oahumada/Strato/commit/f1e37f925aff2c92b3c823872deaef36ce0bb599))
* remove debug console logs from scenario planning components ([f778fc4](https://github.com/oahumada/Strato/commit/f778fc461519009af34827fa055b66f89a1b3497))
* remove package.json and update SVG circle styles in ScenarioPlanning component ([77e3946](https://github.com/oahumada/Strato/commit/77e39464833192d3c122efd2c0f39ee16c22d116))
* Remove redundant component and icon imports and simplify the root template in QualityHub.vue ([5a63468](https://github.com/oahumada/Strato/commit/5a63468676d840386d20668928a20e1456c92713))
* remove unused `props` variable assignment in `PreviewConfirm.vue` ([18607cf](https://github.com/oahumada/Strato/commit/18607cf6f469cee072a3e1a054daad7d1d67cd36))
* Rename the `orchestrate` API route to `designTalent` and introduce a new `designTalent` route, consolidating them into a single export object ([05b9846](https://github.com/oahumada/Strato/commit/05b98464d80aab8962f81a79ab723ffc4fe19c76))
* rename workforce planning components and stores to strategic planning ([66ac50a](https://github.com/oahumada/Strato/commit/66ac50ad6b8c8dea254c1d73a9f73f4dbd704554))
* Separate stepper component into a full-width section and restructure main layout for dedicated sidebar widgets ([5f04cf7](https://github.com/oahumada/Strato/commit/5f04cf7bbc431358c8abb68eef8e15c5208861d3))
* update ScenarioSelector for Inertia.js compatibility and proper navigation ([75206cd](https://github.com/oahumada/Strato/commit/75206cdc707e04cb5cddcee57b6bfc0869aeb4f5))
* update Tailwind CSS `!important` modifier syntax from prefix to suffix across components ([050d22d](https://github.com/oahumada/Strato/commit/050d22d54801a59c90c23cdd803ad45d6d37113f))
* update vitest worker configuration to use forks pool with disabled parallelism ([a6e294a](https://github.com/oahumada/Strato/commit/a6e294a19237e1905e678fb57f58fd71c873c20c))
* Use `withoutGlobalScopes` for `LLMEvaluation` and `Scenario` model lookups and standardize the scenario ID route parameter name ([0e17435](https://github.com/oahumada/Strato/commit/0e17435cd9dc1032945c5a3b069b85b714a5bb4b))


### 🎨 Estilos

* Apply dark theme styling, remove chart toolbars, and enhance data label readability across all scenario planning charts ([cd80dd1](https://github.com/oahumada/Strato/commit/cd80dd17f145885368058c2dbb6e16db810af86d))
* enhance DepartmentNode glass morphism with better contrast ([aef65ce](https://github.com/oahumada/Strato/commit/aef65ce3e16cc685d333f0607a12927e344309cd))
* **frontend:** auto-format and update routes ([2824883](https://github.com/oahumada/Strato/commit/2824883848b034fb90ab716e6b41f034b321fcaa))
* Phase 4 Polish - Fix feature test syntax + final validation ([628bfaa](https://github.com/oahumada/Strato/commit/628bfaa311e26a81b1c1da4a1240f6d5e33415c5))
* Remove grainy background overlay ([bcb46b0](https://github.com/oahumada/Strato/commit/bcb46b059e6fe7e85eb8a3137f0dc31819493140))


### ✨ Nuevas Funcionalidades

* **a11y:** improve Stratos Glass ARIA semantics and audit tests ([d7a595f](https://github.com/oahumada/Strato/commit/d7a595fbd15dd5ce2ae6de1f8f2b52d4cc4076f7))
* actualizar archivos de configuración y eliminar reportes de Playwright obsoletos ([66e0919](https://github.com/oahumada/Strato/commit/66e09196f892196d52c8b2bf3daff7b07f491cb8))
* Actualizar categorías de habilidades a 'técnico' en pruebas de API y componentes ([f30c406](https://github.com/oahumada/Strato/commit/f30c406ebabf2c8928ddbff9e5702eb39abfd9c3))
* Actualizar lógica de seeder para manejar habilidades y relaciones de capacidades, y mejorar la visualización de habilidades incubadas en el modal ([f3c6ad1](https://github.com/oahumada/Strato/commit/f3c6ad12f3d89106844b8be2b09b30c189545b0a))
* actualizar manejo de errores y notificaciones en componentes de planificación de fuerza laboral ([3281b3a](https://github.com/oahumada/Strato/commit/3281b3ae1a2a794a25b1d559aefe420139567e84))
* actualizar permisos de ejecución en scripts y eliminar archivos innecesarios ([dd08f2c](https://github.com/oahumada/Strato/commit/dd08f2c4377fe1e25a93c32380ee68734248427e))
* actualizar plantillas de instrucciones y mejorar la lógica de generación de escenarios en español ([4632ec2](https://github.com/oahumada/Strato/commit/4632ec2bd529b8028efcf74a8999875c7300ce21))
* actualizar relaciones de modelo para usar Roles en lugar de Role y mejorar la selección de estado en RoleCompetencyStateModal ([ea0c298](https://github.com/oahumada/Strato/commit/ea0c298f42fbeb013c7d8dbbc121be2687a28878))
* Add `ia` script to `package.json` and update `AssessmentChat.vue` to access API response data via `response.data` ([add5aeb](https://github.com/oahumada/Strato/commit/add5aeb409238831457107c77b5ddf393d8a783c))
* Add Agentic Scenario Planner, Crisis Simulator, and Career Path Explorer pages, integrate Sentinel Health and Stratos Guide widgets, and refine dashboard styling ([86771f9](https://github.com/oahumada/Strato/commit/86771f91adf78fa9a21c57e6742f81caf212426a))
* Add AI-powered scenario generation wizard, simplify role competency state modal by removing unused logic, and adjust blueprint sheet z-index ([067ddf9](https://github.com/oahumada/Strato/commit/067ddf9771e09924234220b2bb990ae62e8fc11d))
* Add Comando PX feature, including new API endpoints for PX campaigns and a dedicated frontend page with a sidebar link ([fd51361](https://github.com/oahumada/Strato/commit/fd513613aba2b3ca542c63b1a31e2a16be5dcb28))
* add ComplianceDemoSeeder for realistic audit trail and talent metrics - 200+ events, 24 critical roles, 89 people, complete demo data ([b287160](https://github.com/oahumada/Strato/commit/b287160898ed4fdbd64f6fd54b81726b9b590a6b))
* add composables for scenario edges, layout, and state management ([d1a0773](https://github.com/oahumada/Strato/commit/d1a0773f09fb2eb07ce8aa8de84cf0a6176c3d9a))
* Add comprehensive architecture overview and CRUD pattern documentation ([6e7ac4e](https://github.com/oahumada/Strato/commit/6e7ac4ee13f656eab788491dca1b657cfdd326c3))
* Add comprehensive documentation for generation import and role skills integration ([f5f43e8](https://github.com/oahumada/Strato/commit/f5f43e82cf1df1614e6de106f97e0cb95e0f5807))
* Add comprehensive review and status documents for prompt implementation ([59b5b12](https://github.com/oahumada/Strato/commit/59b5b12c11b01aade248a580ae2d84c864df558c))
* add d3 typings and improve SVG rendering in ScenarioPlanning component ([096740e](https://github.com/oahumada/Strato/commit/096740e491b96289795aaccd169ac39a334c7793))
* Add discovered_in_scenario_id to competencies and skills ([b602e26](https://github.com/oahumada/Strato/commit/b602e264f1b0eea736ae2f678ff47cef331111ca))
* add Embedding model and migration for embeddings table ([2cb534d](https://github.com/oahumada/Strato/commit/2cb534d952fd38d65a4affdcd58d0bb5a4497453))
* add finalize endpoint, tests, and legend UI ([46aadaf](https://github.com/oahumada/Strato/commit/46aadafd32af3f4387b587667dc6a6f81c747d3c))
* add interactive department hierarchy editor to org chart ([12225ac](https://github.com/oahumada/Strato/commit/12225ac6fc9dfcb65456584f32ac4482cb399d04))
* Add interactive UI for verification deployment configuration ([d5ecca7](https://github.com/oahumada/Strato/commit/d5ecca7a02f2c546742cdcbb4344d998be218e73))
* add internationalization keys for the new Cerbero Command Unit assessment and 360 talent management feature ([e6e3439](https://github.com/oahumada/Strato/commit/e6e3439b4124ae79942e6e97ff1facc57ae7a05d))
* Add LmsController and refactor scenario route parameters from `scenarioId` to `id` in ScenarioController ([1e2ba0c](https://github.com/oahumada/Strato/commit/1e2ba0c81e5d096d8d3531d760a1b17bca18078d))
* Add magic link authentication and update route definitions across various modules ([a837438](https://github.com/oahumada/Strato/commit/a83743859b33be41d44f1b5b69ec521d97dc6b84))
* Add Neo4j Knowledge Graph architecture documentation, implement scenario deletion API, and consolidate Python intelligence service setup." ([ca019b3](https://github.com/oahumada/Strato/commit/ca019b3d1852b17e0a68efd35373a6f745e488c0))
* add new icons and descriptions for workforce planning and skills levels ([bb78954](https://github.com/oahumada/Strato/commit/bb78954aedbd4408fd8f707e1393db8c21084d31))
* Add new roadmap items, Sovereign Talent Pass details, Guild Missions, and a Strategic Loop section to the roadmap ([af320da](https://github.com/oahumada/Strato/commit/af320da06d979f73dccf2ff0cd6602a61dea1d1d))
* Add node editing modal and prototype map to scenario planning module ([1e11aca](https://github.com/oahumada/Strato/commit/1e11acac77d72b37374f88641db67bb8e64d32f2))
* add OrganizationalContrast component and integrate it into the ScenarioDetail page ([289186f](https://github.com/oahumada/Strato/commit/289186f75ad40d393a0ce55a44cffbc53d236dcf))
* Add Phase 8 - Real-time WebSockets & SSE integration ([f439a9b](https://github.com/oahumada/Strato/commit/f439a9bba3cbc8bc4d93021426c96d7039bb50dd))
* add Playwright configuration and E2E tests for scenario map functionality ([a7bce07](https://github.com/oahumada/Strato/commit/a7bce074ccabef180d4a3bda466a329d4cb1180d))
* Add Prompt Instruction management to Scenario Generation ([71c10dd](https://github.com/oahumada/Strato/commit/71c10dd2bbfbf185f7bc17ddff930dd356d56bde))
* add RAGAS Dashboard for LLM Quality Metrics and implement related API routes ([8ed3cec](https://github.com/oahumada/Strato/commit/8ed3cecb01c80b16a2e318e13556304d709a1c41))
* Add runbook for backfilling competency_versions and implementation status documentation ([1dae79e](https://github.com/oahumada/Strato/commit/1dae79ec159ab09624657da6a71d6c495a34f5e8))
* add Scenario demo page with interactive canvas and D3 visualization ([4f677c4](https://github.com/oahumada/Strato/commit/4f677c4f52e981a167f6b41d250799d402eaafc7))
* Add scenario management models and migrations ([b68b413](https://github.com/oahumada/Strato/commit/b68b4137b2d3f78f713b629a8ce9a44aebbffd77))
* Add Scenario Planning components and functionality ([28127e9](https://github.com/oahumada/Strato/commit/28127e96e3f44fe571fa26c461f3c916574f1ce1))
* Add Scenario Planning components and migrations ([8bc1ef4](https://github.com/oahumada/Strato/commit/8bc1ef43aa45069ebef0e7030d29771922301ee9))
* add scenario planning components and workforce plans list ([1dd7546](https://github.com/oahumada/Strato/commit/1dd75466b66f08f952ea1047d3fea552568d7faa))
* Add scenario planning features with mock data and UI components ([59d335c](https://github.com/oahumada/Strato/commit/59d335c8aa30d83bf8f2aa0c9db3a376c68ee725))
* Add staging backfill script and operational checklists for import generation ([fd57767](https://github.com/oahumada/Strato/commit/fd57767a829f73f7fe55f5bf9757bed97ccabeeb))
* Add Steps 5-7 for verification system completion ([18af5ed](https://github.com/oahumada/Strato/commit/18af5ed7708809dab3b6d6096d594671d272832a))
* Add Stratos Map feature for visualizing organizational and people hierarchy, including a new `supervised_by` field ([b8939dd](https://github.com/oahumada/Strato/commit/b8939ddcb891e19c43055e935d836d71efd377cc))
* Add Succession and Bulk People Import features, and update various routes and API actions ([2aa2312](https://github.com/oahumada/Strato/commit/2aa23124c94df598315a6a325605f4ae7154506d))
* Add Talent Blueprint and Agent Models with Orchestration Services ([bc6f290](https://github.com/oahumada/Strato/commit/bc6f290e9d18c169ab7619ad32c52a2719faa686))
* Add Tarea 5 Optional Phases Implementation Tools ([3520d69](https://github.com/oahumada/Strato/commit/3520d698bcadcdad6b8ff5461d8e7a9cd12b73ca))
* Add Verification Deployment link to ControlCenter Landing ([a106078](https://github.com/oahumada/Strato/commit/a106078d923d18d5ad66d31de2c141defcd2d9d2))
* Add Verification Hub access to Control Center Landing ([8ad5811](https://github.com/oahumada/Strato/commit/8ad581190ae02a40b2c3f60a81f526a32b809618))
* add workforce planning features including plan creation, listing, and management ([b42cf85](https://github.com/oahumada/Strato/commit/b42cf85adcf7a136e4a75a06d6739914b7ed648e))
* add workforce planning module to app sidebar and routes ([71b7ed6](https://github.com/oahumada/Strato/commit/71b7ed64ace70be46b8934885c0fb2ca4600514f))
* add workforce planning seeder with analytics data ([30854b4](https://github.com/oahumada/Strato/commit/30854b425b2b252ab07c5e709abceefd5bfc95f2))
* agregar archivos de resultados de pruebas y directorios de salida a .gitignore ([a84ddee](https://github.com/oahumada/Strato/commit/a84ddee76f70b202731edbbaa54dc37334d9cdac))
* agregar campos editables y lógica para el modal de detalle de habilidades; implementar guardado de cambios y atributos de competencia ([27b604a](https://github.com/oahumada/Strato/commit/27b604a0d291bdbb26c023406aac9373528541b5))
* agregar compatibilidad y mejoras en modelos y migraciones para habilidades y roles ([a6bce41](https://github.com/oahumada/Strato/commit/a6bce41381e519eeb6a71d6628d2a57f741c6af9))
* agregar controlador para orquestar escenarios y mejorar la lógica de cálculo en el componente de índice de sintetización ([4ad43d5](https://github.com/oahumada/Strato/commit/4ad43d5971b40f4de7b2d89103fc1dba6777ad6c))
* agregar diagrama de nodos y documentación para el módulo ScenarioPlanning ([fbbbdb6](https://github.com/oahumada/Strato/commit/fbbbdb6155d2a0c63014ecc8792de816570f7289))
* Agregar diagramas de documentación para el módulo de Workforce Planning y Modelamiento de Escenarios ([eb3dc29](https://github.com/oahumada/Strato/commit/eb3dc29e767d4b1a06aea440e46c40cbb8b508d3))
* agregar documentación y ejemplos para el composable useHierarchicalUpdate ([bc2adad](https://github.com/oahumada/Strato/commit/bc2adad938842aa7dfe4925a54f166b2c53fc32e))
* agregar flujo de trabajo por pasos en ScenarioDetail y ajustar diseño en ScenarioLayout ([373e8b2](https://github.com/oahumada/Strato/commit/373e8b2126721712811deb4e727a6e83ef978a34))
* agregar instrucciones y formato sugerido para el sistema ABACUS en el documento de fusión ([3ca632e](https://github.com/oahumada/Strato/commit/3ca632e583e95408adfd36cbb2f1a23bb7fcab5e))
* agregar lógica para eliminar habilidades de competencias y pruebas asociadas ([5aee448](https://github.com/oahumada/Strato/commit/5aee448c47e4448641b5b919c45e536c932f2f2e))
* agregar modal de detalle de habilidades; ajustar configuración de diseño y animaciones en LAYOUT_CONFIG ([5340786](https://github.com/oahumada/Strato/commit/534078601c0e48aebf8142d0d2f3723e6a84c6e4))
* agregar reglas de tamaño de matriz para habilidades y ajustar el manejo de diseño en la función expandSkills ([86af9da](https://github.com/oahumada/Strato/commit/86af9dafc3abdbda50bce78e0a2b2df47fa4ae2b))
* agregar rutas alias para planificación estratégica en el frontend ([8f78eb6](https://github.com/oahumada/Strato/commit/8f78eb6b314547026d337c5d5d0f574d14b5ed15))
* agregar selección de diseño opcional para habilidades y ajustar configuraciones de distribución en el modelo de escenario ([c8a1cce](https://github.com/oahumada/Strato/commit/c8a1cceff1a847eea0fe204913b467069387afe5))
* agregar stubs globales para componentes de Vuetify en pruebas ([c53dfd5](https://github.com/oahumada/Strato/commit/c53dfd576ddd5ae33b0bca49e07b516603fdbda8))
* **ai:** infrastructure for semantic search with pgvector and evolved role-competency pivot ([f2b7c7a](https://github.com/oahumada/Strato/commit/f2b7c7a2b543cb83c895004d5fb5e253ecc420c3))
* **ai:** integrate talent gap analysis with python intelligence service ([8ad3829](https://github.com/oahumada/Strato/commit/8ad382907906595bddd0bb22ba05128b9369082d))
* ajustar multiplicador de desplazamiento y reglas de tamaño de matriz para habilidades; optimizar duración de animaciones en colapso de nodos ([5cc2361](https://github.com/oahumada/Strato/commit/5cc23611b483cf040c001d99d768a1f6e434d0fa))
* Alinear lógica de generación de escenarios entre UI y CLI, mejorar trazabilidad y manejo de modelos en Abacus ([0544fb6](https://github.com/oahumada/Strato/commit/0544fb6132c013e92f184385afe22b5f01a403dd))
* Añadir campo competency_version_id a ScenarioRoleCompetency y actualizar componentes relacionados ([185c2a1](https://github.com/oahumada/Strato/commit/185c2a1f35a954fc33a2bb5f9eda63cd5bf0c07c))
* Añadir controles de mapa y funcionalidad para guardar y restablecer posiciones en la planificación de escenarios ([8a2b861](https://github.com/oahumada/Strato/commit/8a2b8616690afe4ec0ec6f672af70fceaf045121))
* Añadir diagrama de flujo para el proceso de generación en Redis ([5be806f](https://github.com/oahumada/Strato/commit/5be806fdbb2ae80748588284b4fc920874e447f9))
* Añadir documentación sobre versionamiento de competencias y roles, y mejorar la guía visual en TransformModal ([6f66eed](https://github.com/oahumada/Strato/commit/6f66eedfb799579779e5150397b7f8ee35196f9d))
* Añadir endpoint de generación inmediata respaldado por ABACUS y controlador para manejar la generación de escenarios mediante streaming ([1a65228](https://github.com/oahumada/Strato/commit/1a6522862bacdfce0c1e9021c1325feb7b075e97))
* Añadir flujo de generación de escenario demo para "Adopción de IA generativa" y mejorar validación de respuestas LLM ([b692d8b](https://github.com/oahumada/Strato/commit/b692d8b0726f52fe326bcba0f565d2babe754734))
* Añadir flujo de trabajo E2E en GitHub Actions y documentación de pruebas E2E ([8c410af](https://github.com/oahumada/Strato/commit/8c410af01cf4dc5a17dda564e64dc9772413af52))
* Añadir manejo de errores de JSON en el editor de barras y mejorar la sincronización entre modos ([4762360](https://github.com/oahumada/Strato/commit/4762360beaaf31195212c1019080c6f8b74089eb))
* Añadir manejo de errores de validación en el flujo de aceptación de generación y pruebas para el flujo de importación ([8c13f2e](https://github.com/oahumada/Strato/commit/8c13f2ec2707a529af93dd3edd9e1301dff13ed0))
* Añadir manejo de errores y reintentos en AbacusClient para generación de escenarios ([98255e2](https://github.com/oahumada/Strato/commit/98255e281420b5980efb27854ac48b6c9e4a3c01))
* Añadir manejo de reintentos en AbacusClient y pruebas unitarias para generación de escenarios ([04fb684](https://github.com/oahumada/Strato/commit/04fb68437dc24e04f1dff281d7d670fa8d3ffb16))
* Añadir manejo de validaciones de metadatos y registro de errores en la generación de escenarios ([70ac50c](https://github.com/oahumada/Strato/commit/70ac50cad7cccc12ff78c6bbaa811a410fba8e80))
* Añadir método para persistir la respuesta final del LLM y manejar errores ([9044595](https://github.com/oahumada/Strato/commit/9044595fd03c34f155a5a117d0fcfb6011be30d9))
* Añadir métodos seguros para la comunicación con la API en useScenarioAPI ([86068ce](https://github.com/oahumada/Strato/commit/86068ce65462381fb455db5bedc286077560eac6))
* Añadir relaciones 1:1 entre escenarios y generaciones, incluir columna llm_id en modelos y migraciones, y crear comando para backfill de datos ([78edaeb](https://github.com/oahumada/Strato/commit/78edaeb4f39237db5b6b12e0ca7f58139ce5a902))
* Añadir runbook para backfill de competency_versions y checklist de release ([55b10aa](https://github.com/oahumada/Strato/commit/55b10aa833103c503dd098d55514a5c792f19ea3))
* Añadir script de automatización de staging para backup, dry-run y aplicación de cambios ([182a6b3](https://github.com/oahumada/Strato/commit/182a6b3d4e1a03de99870f51d9a60138af7cb8d3))
* Añadir soporte para generación y manejo de chunks en el proceso de generación de escenarios ([fc81121](https://github.com/oahumada/Strato/commit/fc8112116e0b12d394c4c0049e51a5b6609dc36c))
* Añadir soporte para ignorar índices en la aplicación de ChangeSets, incluyendo lógica en el backend y UI para revertir operaciones ([4c93cd1](https://github.com/oahumada/Strato/commit/4c93cd1bb255996b46af55254422f1e070a9dd2b))
* Añadir soporte para LLM en generación de escenarios y mejorar pruebas E2E ([327ca2f](https://github.com/oahumada/Strato/commit/327ca2f3be5eaca1576bef1de8a517f8e2a5967e))
* Añadir soporte para versionamiento de competencias y trazabilidad en ScenarioRoleSkill, incluyendo nuevos campos y pruebas ([a336827](https://github.com/oahumada/Strato/commit/a336827adc186543fa9b7da9aaf89118c988f8b5))
* Añadir validación condicional para instruction_id en el generador de escenarios y pruebas unitarias correspondientes ([910595a](https://github.com/oahumada/Strato/commit/910595a835ea20d6715762f0f6e5e68bc3bf70e4))
* añadir validación de campos críticos y modal de error en el generador de escenarios ([83e51ef](https://github.com/oahumada/Strato/commit/83e51ef1fa5acfa27fe0611cfdfca70d0fc07e96))
* Añadir validación de instrucciones y manejo de errores en la generación de escenarios ([c5ca049](https://github.com/oahumada/Strato/commit/c5ca049ebdbd17bb8960880505e942299fe07fee))
* Añadir validaciones para la estructura de roles en la respuesta del LLM y actualizar instrucciones en inglés y español ([6e6215a](https://github.com/oahumada/Strato/commit/6e6215a9784311320d634a1f0677d84d47bc3015))
* Añadir validaciones y límites configurables para la respuesta del LLM, incluyendo esquema JSON en las instrucciones ([772e007](https://github.com/oahumada/Strato/commit/772e0074b2897a6c1b58ae05df119b643479a987))
* **api:** add competency creation and deletion endpoints; update capability creation logic ([3b3acb0](https://github.com/oahumada/Strato/commit/3b3acb0b0dc6e136327fb59934e8434560f00f8e))
* **auth:** implement ScenarioGeneration policy for view and accept actions ([445054d](https://github.com/oahumada/Strato/commit/445054d92ccf96f79373ed2688899a733f18ec35))
* Bloque 4 Fase 3 - Dashboard & API endpoints para agregados de inteligencia ([5c7742d](https://github.com/oahumada/Strato/commit/5c7742d5dc348a9675b24cb1c0c6b875aae434c7))
* Bloque 5 Sprint 3.1 - Tarea 1: Verification DTOs & Config ([63300e1](https://github.com/oahumada/Strato/commit/63300e1a5fd88134cfd15693dcebe3f29b6762d6))
* cambio de carpeta desde src ([70d86f0](https://github.com/oahumada/Strato/commit/70d86f09b1f7ab667a794276cb1fbc311d986313))
* **CapabilityCompetency:** agregar modelo y migraciones para gestionar competencias de capacidades en escenarios ([bcf8d2f](https://github.com/oahumada/Strato/commit/bcf8d2fa1c0e9715531bed02e35deb2c3c500fc5))
* **CapabilityCompetency:** implementar endpoint para asignar competencias a capacidades por escenario, incluyendo migraciones, modelo pivot y pruebas funcionales ([cb20834](https://github.com/oahumada/Strato/commit/cb208346b562e50eb052811a337e275b161d49a0))
* centralizar configuraciones de animaciones, nodos y límites en LAYOUT_CONFIG; ajustar funciones de expansión y colapso para utilizar nuevos valores ([7bec12d](https://github.com/oahumada/Strato/commit/7bec12dbeaf5849973a125d77ecff988fa867a95))
* change SkillLevelChip color from primary to black ([03d4566](https://github.com/oahumada/Strato/commit/03d4566fcaf46057990ff5e3a3964aaba315b8c6))
* **CI:** configurar flujo de CI con pruebas para PHP y frontend, incluyendo Playwright y Vitest ([ed75964](https://github.com/oahumada/Strato/commit/ed75964fcdfccd5ec05d0cdcf8b678ffce767759))
* **competency-skills:** implementar lógica para crear y adjuntar habilidades a competencias; agregar pruebas unitarias ([6e90c16](https://github.com/oahumada/Strato/commit/6e90c167b4510faeb5199cbeb174902f1598cb89))
* **CompetencyAPI:** implementar endpoints para obtener y actualizar entidades de competencia, asegurando seguridad multi-inquilino ([9bde1fb](https://github.com/oahumada/Strato/commit/9bde1fbbe0cf9c758239cf0027fecbf24bfb47fd))
* **CompetencyLayout:** agregar función decideCompetencyLayout para centralizar la lógica de selección de diseño; incluir pruebas unitarias ([b77256d](https://github.com/oahumada/Strato/commit/b77256d2dfe495264ec09bd9ecaf5c0d0996559f))
* **CompetencyLayout:** agregar funciones para elegir variantes de matriz y calcular posiciones laterales; incluir pruebas unitarias ([38137b8](https://github.com/oahumada/Strato/commit/38137b81dcc685ba649c38096076952a37b24e9f))
* **compliance:** add command center and sidebar navigation ([2bf93ed](https://github.com/oahumada/Strato/commit/2bf93edab32eb213da525757bb13d6a1b48c63b3))
* **compliance:** implement audit governance and verification flows ([d5e582c](https://github.com/oahumada/Strato/commit/d5e582c50367e01bac620a129ad6587f1f8f6c65))
* consolidar el modelo de habilidades a `Skill` y actualizar referencias en controladores, repositorios y pruebas ([3196900](https://github.com/oahumada/Strato/commit/3196900859f3f80ca3cb4aaa8770bde46d926e4f))
* consolidar modelo de habilidades a `Skill`, actualizar lógica de controladores y repositorios para compatibilidad ([3d36f7a](https://github.com/oahumada/Strato/commit/3d36f7a7c7d51fadb1455c76e7e5a14519661c5d))
* consolidar modelo de habilidades a nombre singular `Skill` y actualizar referencias en el código ([0308b4f](https://github.com/oahumada/Strato/commit/0308b4faad0227fe0abda8ada07070f3c70161cd))
* corregir la lógica de actualización en el controlador y repositorio para persistir cambios en la base de datos ([a3078ec](https://github.com/oahumada/Strato/commit/a3078ec5d76b539bff81a5566ec38908e234d886))
* create ApexCharts visualization components for dashboard ([758c3df](https://github.com/oahumada/Strato/commit/758c3dfc1000ea225eb7e039b357196d726c1cb7))
* create workforce planning Pinia store with caching ([0527506](https://github.com/oahumada/Strato/commit/0527506ab6ba457a49949460872d50d7e0a08f6a))
* **dashboard:** add CEO KPI view with Stratos IQ and dark contrast fixes ([9103610](https://github.com/oahumada/Strato/commit/91036103f5ab140c626cbae5411e5058f0321693))
* display strategy risk level in a dedicated chip, add `updated_at` to the scenario interface, and apply minor formatting adjustments ([fe5f5a2](https://github.com/oahumada/Strato/commit/fe5f5a28f83979db2b9a4321dee5a51283671481))
* document design decisions for Step 2 - Roles ↔ Competencies, including policies, thresholds, and UI operations ([6deb09a](https://github.com/oahumada/Strato/commit/6deb09ab117174478285ab93cdaffcadff05270e))
* **e2e:** add E2E login endpoint and scripts for admin setup ([85154c0](https://github.com/oahumada/Strato/commit/85154c0c33f8acdc8ee4f1637bb664159de0ab80))
* eliminar archivo de base de datos SQLite ([9c6b5bb](https://github.com/oahumada/Strato/commit/9c6b5bbae6c6edeb35390a77fba67ea597fac84b))
* Enhance AI role design with a professional description, refactor form labels to be external, and improve form layout with flexible columns ([c073465](https://github.com/oahumada/Strato/commit/c07346571b29a3941ac88c9c467e738b65f206a4))
* Enhance AI-driven BARS blueprint generation and introduce new Mi Stratos, Security, and Comando 360 modules with RBAC ([0a3a078](https://github.com/oahumada/Strato/commit/0a3a0785ef18ab0c682299512bf41b49285a4548))
* enhance public career portal UI and content, improve API helper type safety, and refactor Talento360 module imports ([3c53331](https://github.com/oahumada/Strato/commit/3c533310ba16feea5b44795666e022e341e61c99))
* enhance role designer wizard with detailed form structure, mock data, and approval request loading improvements ([72c9b7d](https://github.com/oahumada/Strato/commit/72c9b7d5fe8089f4a1a8d21fdf13227dac79f989))
* Enhance role wizard with new "Impact" and "DNA/Profile" steps, improve bulk people import data sanitization and role-to-department mapping, and add a SQL session file ([53d0744](https://github.com/oahumada/Strato/commit/53d07443176132bab1ebebde39019f1cfd1018cc))
* enhance scenario generation store and add FieldHelp component ([2eb808c](https://github.com/oahumada/Strato/commit/2eb808cc53e43f0c8eb5562b68b8589343684393))
* Enhance Scenario Management with New API Endpoints and UI Components ([a023e7e](https://github.com/oahumada/Strato/commit/a023e7edf4edd517ed375f2d0660b159675b1402))
* Enhance scenario planning with assumptions card, read-only modes, and restore missing UI layout components ([4a4a6e9](https://github.com/oahumada/Strato/commit/4a4a6e9a7bccbb00b17534bf824815979d3fc878))
* enhance skill expansion and update handling in ScenarioPlanning ([1b714ab](https://github.com/oahumada/Strato/commit/1b714abb747850632d272a68a28b7e206239e220))
* Enhance talent design with strategic purpose and expected results for roles/blueprints, skill cube dimensions, and updated scenario planning documentation ([692fc36](https://github.com/oahumada/Strato/commit/692fc36b92c3477c836ce1b1090ba5d2134ed04c))
* **enterprise-security:** Add access audit logging + mandatory MFA enforcement ([de6c5f6](https://github.com/oahumada/Strato/commit/de6c5f68c8c987ae4ba4f0e9b100d83605876379))
* expand workforce planning seeder with comprehensive test data ([684fc38](https://github.com/oahumada/Strato/commit/684fc3803131eccec9d1cd4a3da1384403535163))
* Expose and display AI-suggested cube coordinates for roles and refine scenario competency-capability mapping ([21ee40e](https://github.com/oahumada/Strato/commit/21ee40ed08e4dcd9923fcddd463204245f61be16))
* Feature test refactor + Vue 3 messaging components ([4dffdfe](https://github.com/oahumada/Strato/commit/4dffdfea08e3af01e46258b4f2b578fd01c9a3e8))
* Funcionalidades Unicornio — Auto-Remediación, DNA Cloning, Culture Sentinel ([7e04575](https://github.com/oahumada/Strato/commit/7e04575d332d3f1c26a9339a70f4014b33843d47))
* Implement 360-degree assessment and hierarchical relationship mapping with new `people_relationships` table, `PeopleRelationship` model, and `People` model methods, alongside `AssessmentController` enhancements ([d5045eb](https://github.com/oahumada/Strato/commit/d5045ebd9033ba783b2a2a2d13600c0dffc4c26b))
* Implement a deep retention engine with a turnover heatmap and AI-generated retention plans, updating the roadmap and adding new API endpoints and UI components ([77f430d](https://github.com/oahumada/Strato/commit/77f430d0b15ee15c35c2b813423814e4890547e8))
* Implement a synthetic and hybrid talent model with skill materialization and related documentation ([a74a6ba](https://github.com/oahumada/Strato/commit/a74a6bad12a458d667e0f10817dd00b0fd66200d))
* Implement AI orchestration, new AI-powered services for talent and culture, DeepSeek LLM integration, and associated database and UI updates ([1ba5740](https://github.com/oahumada/Strato/commit/1ba5740f442726c5b6151164f8362ddd95ebf74d))
* Implement AI-driven development path generation, secure routes, and enhance frontend step display ([0af8c2c](https://github.com/oahumada/Strato/commit/0af8c2c8742a55909524d5e3bd595f12101af0e7))
* Implement AI-driven Role Cube methodology for role design and persist AI analysis results across services ([c4d357e](https://github.com/oahumada/Strato/commit/c4d357e9a348fd155ca0ba5beabfea764014f8ed))
* Implement AI/API rate limiting, enhance assessment workflow with 360 feedback triggers, and add extensive feature tests for multi-tenancy, matching, and various services ([18b340b](https://github.com/oahumada/Strato/commit/18b340b6441c43c04ba5d616b262dd3921b3a4c7))
* Implement and document Cerebro AI orchestration for scenario generation, including new testing guides and a simulation script ([9aecb08](https://github.com/oahumada/Strato/commit/9aecb08a3c3cc4251083ee46312aa9f1779c18f4))
* Implement and integrate an AI-powered competency orchestrator for Step 2 of scenario planning ([88d34a9](https://github.com/oahumada/Strato/commit/88d34a9ead9daa4fe2dbb7137e9c169f25d7ff79))
* Implement approval flow with digital signatures for roles and competencies, including new UI components and API endpoints ([de79026](https://github.com/oahumada/Strato/commit/de790268b96406d1730eda91582998b38e3bed39))
* implement architectural coherence and reference role system for role-competency mapping ([c317512](https://github.com/oahumada/Strato/commit/c3175126ce237f68b16dfab2f24d9ec4e77135ee))
* Implement assessment cycle scheduling, request generation, and notification system with supporting UI and API enhancements ([34204ef](https://github.com/oahumada/Strato/commit/34204efc44cc83b4b7d16c49a744c01ad2838f68))
* Implement auto-import for LLM-generated capabilities, competencies, and skills ([7edba7d](https://github.com/oahumada/Strato/commit/7edba7db5e274887f2aff5a7dfec34efb7da7efd))
* Implement automated impact report generation and integrate user evaluation display into MiStratos ([c683d75](https://github.com/oahumada/Strato/commit/c683d75c8140ab426d72f6b3eca48e317be6f5ed))
* Implement BARS (Behaviorally Anchored Rating Scales) for assessments, introduce a skill question bank, and enhance scenario planning with mitigation actions and CFO reports ([258e803](https://github.com/oahumada/Strato/commit/258e8035569846827b4c3c58c1aea2a253078c82))
* implement comprehensive Talento 360 assessment system with AI-driven psychometric evaluations and multi-source feedback capabilities ([a0dac39](https://github.com/oahumada/Strato/commit/a0dac3998caf1cf5ba5473acbd4e1847933b0200))
* Implement core gamification features with quests, person-quest tracking, and associated API endpoints and UI components ([4590a8d](https://github.com/oahumada/Strato/commit/4590a8d36cd18cc5e723cf820b872d2c73ba96ac))
* Implement data sanitization for RUTs and names in bulk people import and add a sample CSV ([763c382](https://github.com/oahumada/Strato/commit/763c3824a5c30782a1891522131326cf22fed4e6))
* Implement DRY refactor for ScenarioPlanning component ([f068fc4](https://github.com/oahumada/Strato/commit/f068fc44e07805ace31d1548f386cf128aebdcb0))
* Implement dynamic talent heatmap with critical skill and continuity risk analysis, and expand retention prediction service with manager health and strategic metrics ([4577b05](https://github.com/oahumada/Strato/commit/4577b0590f7030c49b30cafce8ac01efbf6d2761))
* implement embedding fields across core, scenario, and talent blueprint tables, enhance Step 2 scenario planning UI and API, and introduce Pest for testing ([317cc38](https://github.com/oahumada/Strato/commit/317cc389ed2e1547c2e25cb673a8d5d6d0eb8665))
* implement execution risk analysis and report printing for scenario analytics ([b7a3a6f](https://github.com/oahumada/Strato/commit/b7a3a6f2b9bb68c937e1d195b2bc2be871a5567a))
* implement gamification system and mobility war room ([eb96701](https://github.com/oahumada/Strato/commit/eb96701fc659260b4609ac86413b868613c0f4ae))
* implement GuideFaq model and indexing in embeddings table; add reindex command ([15ac516](https://github.com/oahumada/Strato/commit/15ac5164c56976f503adac2b1891449e94e94c69))
* implement hybrid SSO gateway, gamification quests, and proactive nudging orchestration with talent DNA timeline ([c887941](https://github.com/oahumada/Strato/commit/c88794163aca7afd7fd74b7744dd96677db4c6a1))
* Implement i18n support using vue-i18n and add a locale selector component ([48a222f](https://github.com/oahumada/Strato/commit/48a222fcc01d565305bf2ba0ea8cc2272d401dd3))
* implement Impact Engine core (Ingestion, HCVA Calculation, and Dashboard Integration) ([cdf8078](https://github.com/oahumada/Strato/commit/cdf80786e04bf23fcc159a37777cfac3d477c267))
* implement incubation flow for scenario-generated items with embedding-based similarity checks ([be7de7e](https://github.com/oahumada/Strato/commit/be7de7e2c3a197c391e0ff4ed73b016d7f6c8faa))
* Implement internationalization, migrate to Phosphor Icons, and integrate the StButtonGlass component ([0247e0b](https://github.com/oahumada/Strato/commit/0247e0b729b794e8388b93f24f3ae1cd443aa166))
* Implement Investor Radar dashboard, standardize API responses, and update roadmap status ([3ffeb57](https://github.com/oahumada/Strato/commit/3ffeb577dc68c10aa1ac292932fa4d920c81cdfe))
* Implement LLM Providers and Redaction Service ([86b0733](https://github.com/oahumada/Strato/commit/86b073331574a84f21e4fb7c36729922a9946e9c))
* Implement LMS integration, mentorship sessions, evidence management, and pulse surveys, while enhancing development path generation ([866758b](https://github.com/oahumada/Strato/commit/866758b6ed7e21d0427d733122da7eb8cdff85ae))
* Implement Magic Link authentication, update login UI, and reflect AI matchmaking progress in documentation ([b7c1294](https://github.com/oahumada/Strato/commit/b7c1294f825e890f3efc0474c086a8542dea2ecd))
* Implement mobile-first People Experience (PX) portal with new activity feed, navigation, talent features, and PWA manifest ([781a5df](https://github.com/oahumada/Strato/commit/781a5dfbb5b6d1e1d612c01490b9d23c30ea5a61))
* Implement n8n automation integration, inferential psychometry with evidence tracking, ROI calculation, and gamification features, while removing workforce planning ([bddb007](https://github.com/oahumada/Strato/commit/bddb007956d017bc374ac78c7c60bb1e5280457c))
* Implement Neo4j ETL process, including syncing from Postgres, job dispatching, and verification scripts ([9fdca75](https://github.com/oahumada/Strato/commit/9fdca75fe87d4122165eaeda2a17ca6eb7eaafff))
* Implement new action categories and interventions for agentic scenario planning and update product architecture documentation with new modules ([f6f669f](https://github.com/oahumada/Strato/commit/f6f669fcf53129ef79ce65ee2b23b39bb3199b33))
* Implement new API endpoints and services for strategic planning, talent intelligence, and various reports, including LMS course search and API response aliases ([ab77d62](https://github.com/oahumada/Strato/commit/ab77d626eecf39cc1601047be94601e0de3d5b8b))
* Implement new glassmorphism UI components, redesign the scenario planning overview, and introduce an investor dashboard ([c3bb833](https://github.com/oahumada/Strato/commit/c3bb833094f626d01e0bb0ab4126ba6ae6c624a2))
* Implement new mobile UI components and talent progression features, alongside API and navigation updates ([1bea8f3](https://github.com/oahumada/Strato/commit/1bea8f3b2156d6592d93446743b73f3a8f5b996d))
* implement new talent blueprint attributes and scenario role enhancements ([1f4b62a](https://github.com/oahumada/Strato/commit/1f4b62a143cc8f876fd32b03e0689a2ce78232a0))
* implement Phase 3 internal supply analysis, including real FTE forecasts, candidate-position matching, and strategic succession planning ([6c6f849](https://github.com/oahumada/Strato/commit/6c6f849ed4c21e5adb46272582556173d155ccd4))
* Implement RBAC system, introduce "Mi Stratos" personal portal, and enhance competency creation with incubation status ([45a61ed](https://github.com/oahumada/Strato/commit/45a61ed34de811a46a9bbeef9ee3fc52efa01bbf))
* Implement Redis buffer for LLM chunk generation and metadata compaction ([e53cee5](https://github.com/oahumada/Strato/commit/e53cee5bd18595e1267dd41e2abd7b75769a99fd))
* implement scenario generation feature with LLM integration ([aca4aa4](https://github.com/oahumada/Strato/commit/aca4aa4285258e0feaa3a26071aae582a3bda10c))
* implement scenario impact analysis UI, add high potential talent tracking, and integrate new intelligence services ([f8f893f](https://github.com/oahumada/Strato/commit/f8f893f1ef2f4f40b9641b7d1cd4bd4c8a1eeda1))
* Implement scenario impact analysis UI, add high potential talent tracking, and integrate new intelligence services ([3f9f8fd](https://github.com/oahumada/Strato/commit/3f9f8fd1a855cc1e5d7f08020df3d059e957a3df))
* Implement Scenario Planning steps 4-7 with new components, API endpoints, database migration, and tests ([7a42f11](https://github.com/oahumada/Strato/commit/7a42f1168ea989df9da8c3fae5e53682bfe58992))
* Implement scenario simulation, strategy assignment, and ROI calculation features with new API controllers, frontend components, and updated routing. ([77ef7ea](https://github.com/oahumada/Strato/commit/77ef7eabe942268b319c8d155fee49141c623a2f))
* Implement scenario version retrieval API and integrate impact analytics component into budgeting plan ([5f638da](https://github.com/oahumada/Strato/commit/5f638daa0569703c3a76b0a1a7555900066a4ddd))
* Implement scenario versions API endpoint, refactor ImpactAnalytics component to ImpactAnalyticsDashboard, and configure Vite watcher to ignore common directories ([3c7ad5d](https://github.com/oahumada/Strato/commit/3c7ad5d299672bd71172b15661433c3c7f59939d))
* implement social learning and knowledge transfer engine with AI-driven mentorship blueprints and continuity risk identification ([f753a0a](https://github.com/oahumada/Strato/commit/f753a0ae468a1d4ab725b4c1aaaeef5f2b5227d7))
* Implement Step 2 Roles/Competencias Matrix in ScenarioDetail.vue Stepper ([d19ea2d](https://github.com/oahumada/Strato/commit/d19ea2da81605968d40c6096aa61c90472927582))
* Implement Stratos Magnet public career portal with AI internal matchmaker service and update strategic roadmap documentation ([48889ab](https://github.com/oahumada/Strato/commit/48889ab31802b0c07325a6c6ae78db13e34edc85))
* Implement Stratos Map heatmap to visualize strategic competency temperature across departments using ECharts ([19e78e1](https://github.com/oahumada/Strato/commit/19e78e1ea4bf993eddfc3480307022a080722674))
* Implement Succession Planning and Bulk People Import (Stratos Node Aligner) with new models, services, controllers, and UI components ([fbba06b](https://github.com/oahumada/Strato/commit/fbba06b903f6613c6356f26f341e7fe1ca61181a))
* Implement support ticket functionality, introduce static analysis with PHPStan, and update various services, models, and database schemas ([7a00288](https://github.com/oahumada/Strato/commit/7a00288f0115e1309b8fe07143ddd3067efe974e))
* Implement Talento360 Command Center, 360 Assessment Policies, and enhance Scenario Planning with new API actions and routes ([069fa86](https://github.com/oahumada/Strato/commit/069fa8617e197e74f09905b0cbb25a02f57a7235))
* Implement tenant-specific module access control and hierarchical structures for skills, roles, and departments ([985271a](https://github.com/oahumada/Strato/commit/985271a5ec26ab98e4da7f86a360e3ea749968cb))
* Implement the organization chart page and API for department hierarchy management, and add role-competency relationships to models ([8bb7e9c](https://github.com/oahumada/Strato/commit/8bb7e9cd03d11b4bd2405f097cc1cb0816cd1bb2))
* Implement the Talento360 module, introducing relationship mapping, assessment results, and associated API, UI, and documentation ([7f3dc3a](https://github.com/oahumada/Strato/commit/7f3dc3a5d48fc991b5898f0c7a24d7ffce43972e))
* Implement Workforce Planning components including ScenarioActionsPanel, ScenarioStepperComponent, StatusTimeline, and VersionHistoryModal ([3ef7d46](https://github.com/oahumada/Strato/commit/3ef7d46c1fc0bcd46b8d8074b9017a2ecef9c45a))
* implement workforce planning module - phase 1 ([c840728](https://github.com/oahumada/Strato/commit/c84072807210f0aa484819ee29ebdb4ad319c33a))
* Implement workforce planning scenario comparison and creation features ([a952bc3](https://github.com/oahumada/Strato/commit/a952bc39bbe429dcfac6c34cd82efb21127de9fc))
* Implement workforce planning scenario model and related functionalities ([2bb7a81](https://github.com/oahumada/Strato/commit/2bb7a81bf7d97c02d3b83e40151eeb6809cd5565))
* Implementar ChangeSets para la planificación estratégica, incluyendo API, servicios y pruebas ([406d459](https://github.com/oahumada/Strato/commit/406d4596104abc230f8588040c80f3908648afc2))
* Implementar compatibilidad y mejoras en la planificación de escenarios, incluyendo nuevas migraciones, actualizaciones de API y ajustes en la interfaz de usuario ([1f2f89a](https://github.com/oahumada/Strato/commit/1f2f89aa68f06a7c6cd0602d6add374ff93e3d8c))
* Implementar componente BarsEditor con modo estructurado y JSON, incluyendo pruebas unitarias ([2e22e1f](https://github.com/oahumada/Strato/commit/2e22e1ff210e0f4b4f43edc20c417245a225c679))
* implementar eliminación completa de habilidades en ScenarioPlanning y mejorar la gestión de notificaciones ([d86337d](https://github.com/oahumada/Strato/commit/d86337dfff19f72bc2440dc2f4ef7b104a08b2a3))
* Implementar flujo de aprobación y rechazo para ChangeSets, incluyendo nuevas rutas y lógica de permisos ([99674b2](https://github.com/oahumada/Strato/commit/99674b2f99fa4d0c45696b08a368cf011d279b68))
* Implementar generación de escenarios con streaming y persistencia de chunks; integrar ABACUS como proveedor LLM principal ([0d2d99f](https://github.com/oahumada/Strato/commit/0d2d99f48c52c30ab6b8651087b6e5c05955ba34))
* implementar gestión de actualizaciones jerárquicas en estructuras de datos en Vue ([8601216](https://github.com/oahumada/Strato/commit/8601216a218db11969d3aacbc2558375cba1d9b0))
* Implementar versiones de competencias y mejorar manejo de errores en la API ([c4d78d9](https://github.com/oahumada/Strato/commit/c4d78d9361716953bb1703fafc4ff8cb3a238e5e))
* Implementar versiones de competencias y roles, incluyendo CRUD y lógica de transformación ([f987673](https://github.com/oahumada/Strato/commit/f987673e798a693a3e75427a13c4bc357a2e6df7))
* implementar zoom y gradientes en nodos de competencias y habilidades en ScenarioPlanning ([8d4c4c5](https://github.com/oahumada/Strato/commit/8d4c4c55ab8ab01a4e134926f1a7abff1dd57ddb))
* Import NodeEditModal component into ScenarioDetail view ([daa2f59](https://github.com/oahumada/Strato/commit/daa2f59536493eabf9db26164505d7b08b4cd936))
* **infra:** Implement Internal Event Architecture (Event Store + Domain Events) ([c39f8a3](https://github.com/oahumada/Strato/commit/c39f8a3100e5bfe5a9a442bf690754d84bc188aa))
* Integrar ChangeSet Modal en la UI y mejorar manejo de ChangeSets en el backend ([2542240](https://github.com/oahumada/Strato/commit/2542240c7519a25f3375918da820124cfd93d838))
* Integrar cliente Abacus LLM y añadir documentación de configuración ([474575f](https://github.com/oahumada/Strato/commit/474575fae09168f56ca4aecc4374aaf1c5729d82))
* integrate 4 workforce planning components into OverviewDashboard ([13bd53a](https://github.com/oahumada/Strato/commit/13bd53a8514c5ec8c9a69a1acd181a6071126035))
* Integrate Abacus streaming support into scenario generation ([a372241](https://github.com/oahumada/Strato/commit/a372241930c874e008feb91df5b7ac8368da1b5e))
* Integrate Neo4j for graph-based career pathfinding via a new Python service endpoint with SQL fallback ([085ee0e](https://github.com/oahumada/Strato/commit/085ee0e29b651d46f64110df4a0c83477ea59415))
* integrate Phase 11 mobile routes in api.php and update openmemory guide ([cba3784](https://github.com/oahumada/Strato/commit/cba378487837b70404344dc2439bf5a3aed8e413))
* integrate Python intelligence microservice for gap analysis and scenario generation, replacing old gap analysis service ([62263b9](https://github.com/oahumada/Strato/commit/62263b903ef137947144b8ed9e7fa20f856d3c50))
* integrate workforce planning store into all components ([6fc3392](https://github.com/oahumada/Strato/commit/6fc33922484157372836840a0e990e472cb9cd73))
* Introduce `BelongsToOrganization` trait for centralized multi-tenancy and update related tests ([3402928](https://github.com/oahumada/Strato/commit/3402928a1a7a596c6176cc4ab0477ef09b349da4))
* Introduce a comprehensive Role-Based Access Control (RBAC) system with roles, permissions, dedicated middlewares, and frontend integration ([9a87240](https://github.com/oahumada/Strato/commit/9a87240c62a0dc2d88aae6b8ab3d30ee5dc1bee7))
* Introduce a multi-agent system for talent design and scenario planning, including new orchestration services, API endpoints, and UI components for agent configuration and proposals ([92e6ba7](https://github.com/oahumada/Strato/commit/92e6ba7f830011276efa609d81e3d8b57291e8fd))
* Introduce a new `AuthVanguardLayout` for authentication pages, add purpose and expected results fields to `RoleCubeWizard`, and refine global CSS for autofill and Tailwind imports ([9110057](https://github.com/oahumada/Strato/commit/9110057733a9f09303fb65ca7b7646c3fe64d588))
* Introduce AI agent strategy document and integrate Stratos Sentinel and Guide into the strategic roadmap, updating progress and architectural details ([2144b8e](https://github.com/oahumada/Strato/commit/2144b8e58b60fcbf70fa2f0038fe33851adf6c5a))
* Introduce AI Agent, Talent Blueprint, and cube dimensions to roles, integrating them across the database, API, and UI ([3604f25](https://github.com/oahumada/Strato/commit/3604f25624f2a9f0f99f673c33d1cf042e74b5b8))
* Introduce AI-powered match insights for candidates in the marketplace, including a new API endpoint and UI integration ([1878972](https://github.com/oahumada/Strato/commit/1878972ac0421adada8a5f37cb06225cb11e4ecd))
* Introduce AI-powered Stratos Navigator for development paths and 360 skill triangulation dashboard ([3fb7fdf](https://github.com/oahumada/Strato/commit/3fb7fdff7345fac244ad5ec8cfb6e1d90542f445))
* Introduce and detail the 'Sacar Brillo' (Polishing) phase for UX/UI refinement across design system documentation ([1f5c1a6](https://github.com/oahumada/Strato/commit/1f5c1a6d45be52ac23cf13cc94441317fed65133))
* Introduce and document the Floating Glass Modal system with new global CSS styles and apply Glass Menu styles ([9ecb7ec](https://github.com/oahumada/Strato/commit/9ecb7ec27f4b86576995c34c92e61dbf7fd7c602))
* Introduce automatic mitigation plan creation using `DevelopmentPath` and update `DevelopmentAction` persistence with type mapping, alongside adjusting test person setup to include a role ([101d9aa](https://github.com/oahumada/Strato/commit/101d9aa042c2b902ef20399d5a34320ea6a1b7f6))
* introduce Comando page with Assessment Cycle and Px Campaign management, including new models, migrations, API, and frontend routes ([4ae4cd3](https://github.com/oahumada/Strato/commit/4ae4cd3346a6488aa6df1e7954233b5c3b2ae0d6))
* Introduce Competencies management, People profiles, and Security settings, alongside API and UI updates ([2327092](https://github.com/oahumada/Strato/commit/2327092ab88d8ee38a536e9fe8b1d4bf59c89979))
* Introduce competency curation and role design APIs, standardize frontend form schemas, and update the architecture roadmap progress ([db241c3](https://github.com/oahumada/Strato/commit/db241c3af9cffc9a2b447408221cbcde39efe790))
* Introduce core Stratos Talent Engineering capabilities including Scenario Planning, Smart Selection, and foundational architecture documentation ([eae02d2](https://github.com/oahumada/Strato/commit/eae02d25048394f3b636f3c4251a5efc85dc8932))
* Introduce Cultural Blueprint and Culture Dashboard, enhance organization chart components, and update related services and routes ([21ba78a](https://github.com/oahumada/Strato/commit/21ba78aa65a4de82d574b73edb29f091b4a55715))
* Introduce digital seal (Sentinel) for data authenticity and integrity across various models and processes ([180f43d](https://github.com/oahumada/Strato/commit/180f43d54e7f4e20a0f10276c40b3b550deeffda))
* Introduce gamification, smart alerts, and hybrid gateway services, accompanied by new learning flow and career path tests and configurations ([394b537](https://github.com/oahumada/Strato/commit/394b5377f8e94dc3d54999400805b8b6d50cde10))
* Introduce Impact Engine with new data models for business metrics, impact analyses, financial indicators, and cultural blueprints, alongside related services and extensive documentation ([330cca0](https://github.com/oahumada/Strato/commit/330cca061891bf84c6cc58480bddfca76063d637))
* Introduce LLM configuration and integrate DeepSeek and OpenAI providers with dedicated services and tests ([096db09](https://github.com/oahumada/Strato/commit/096db099ab182334c5c455562816b2a0542fd76d))
* Introduce LMS rewards catalog and gamification features, update scenario planning, and regenerate API actions and routes ([9a809a4](https://github.com/oahumada/Strato/commit/9a809a401e50e1f1113507c8235e1109f29d970f))
* introduce modular sections for the MiStratos page, including dashboard, evaluations, and conversations, and update related audit documentation ([f7422d8](https://github.com/oahumada/Strato/commit/f7422d8b9da856ae8967ff6557129b67afc11495))
* introduce new Impact Engine controllers and update MiStratos UI with enhanced glass card styling and padding standards ([431c16f](https://github.com/oahumada/Strato/commit/431c16fd7503d2431b5e9693d6d13b55659b359e))
* Introduce RAGAS evaluation capabilities with a new seeder, service, API controller, and dedicated UI components ([07a3ed6](https://github.com/oahumada/Strato/commit/07a3ed608a0bc0810d0c8e10aa453fdb27906a42))
* Introduce Role Cube Wizard for AI-powered role design and integrate into role management and scenario planning ([5ba10c8](https://github.com/oahumada/Strato/commit/5ba10c865d3bc96c9c75cb10e948216acb6157af))
* introduce RolePerformanceSheet component to display detailed role information and integrate it into the roles management interface ([8531aec](https://github.com/oahumada/Strato/commit/8531aec136df60b1304a06fa0fb38b82af93e8d9))
* Introduce Sentinel monitoring, new services for talent, scenario, and intelligence management, and their associated API endpoints ([09ff452](https://github.com/oahumada/Strato/commit/09ff452ce4e09f69482bdb72feb746435a5b73b5))
* Introduce smart alerts and gamification features with new models, API endpoints, and UI components ([90f38e4](https://github.com/oahumada/Strato/commit/90f38e4aa9425e18719f8f0c5429a9e0a3ee8b3d))
* Introduce smart development path generation with a new UI tab, dialog, and updated action types and strategies ([bd55cee](https://github.com/oahumada/Strato/commit/bd55cee17a76b9d64f5759d2b10006ebcad00f78))
* introduce Talento360, People Experience, and Talent Agents modules, enhance scenario planning, and update routing and API actions ([8c4e065](https://github.com/oahumada/Strato/commit/8c4e065cdd5154748a471e275cfda8d1810bfdec))
* Introduce the Engineering Blueprint Sheet for Step 3, visualizing agent-driven talent engineering and competency changes with source attribution ([2e23702](https://github.com/oahumada/Strato/commit/2e23702fcde101d43f42e56af741114daa0d08a8))
* introduce workforce planning module with new models, migrations, API, and frontend components ([43b58ba](https://github.com/oahumada/Strato/commit/43b58ba10a2df629fd35d7b94701d7043d2bdb31))
* **llm:** enforce JSON-only instruction in prompt and validate/parse LLM JSON response in job ([56edbd9](https://github.com/oahumada/Strato/commit/56edbd9cc2f35843c226c1829a3b8590f1fe6f49))
* Mejora de la checklist de importación con numeración y formato consistente ([3567d74](https://github.com/oahumada/Strato/commit/3567d7438a8c8897acfb8d5dc1831db131d29123))
* mejorar badges de arquetipo con v-chip y tooltips ([6b7fc7d](https://github.com/oahumada/Strato/commit/6b7fc7df2decdedd92d28ee788291161bb89ba1f))
* Mejorar control de acceso para sincronización de Neo4j y agregar comando Artisan para ejecutar ETL ([db5300f](https://github.com/oahumada/Strato/commit/db5300f7442fae0d146d650dce5b662747b30b91))
* Mejorar documentación de generación de escenario y optimizar el código en GenerateWizard ([e5a9b89](https://github.com/oahumada/Strato/commit/e5a9b896b76ff3dba14966eafa310f651b8f9526))
* mejorar el manejo de datos en el método store y actualizar los registros de log; agregar pruebas para la gestión de habilidades en el modelo de competencias ([b62cf37](https://github.com/oahumada/Strato/commit/b62cf37aebdf54a3c18f094a6f32a799884a4d2b))
* mejorar el manejo de registros de log en getCapabilityTree; ajustar formato de código para mayor claridad ([39d72df](https://github.com/oahumada/Strato/commit/39d72df08712c61a7548529c27cf2c5cb63b3a60))
* mejorar la carga de roles disponibles utilizando API y fallback a props de Inertia ([c38595c](https://github.com/oahumada/Strato/commit/c38595caccc5fd116934dcff504e16185d2bf48a))
* Mejorar la estructura del código y agregar políticas de acceso para CompetencyVersion ([033d97f](https://github.com/oahumada/Strato/commit/033d97f041161a6fb6e9be0ce50d6eda80e47e5c))
* Mejorar la estructura y legibilidad del código en useScenarioAPI y useHierarchicalUpdate ([956fdd5](https://github.com/oahumada/Strato/commit/956fdd52a03bc13f02eba53d587650e39c5de4bc))
* mejorar la gestión de competencias y capacidades en el modelo de escenario; agregar compatibilidad para columnas de base de datos opcionales ([1beed84](https://github.com/oahumada/Strato/commit/1beed84822a1197089b311088cb06a43ecc9e598))
* mejorar la gestión de eliminación de habilidades en ScenarioPlanning y optimizar la reacción a cambios en props ([0074681](https://github.com/oahumada/Strato/commit/007468172bb765f099120084d4835fadcaa0e6d3))
* mejorar la lógica de actualización en FormSchemaController y optimizar la extracción de datos en el repositorio ([0e59427](https://github.com/oahumada/Strato/commit/0e59427617ab15d9d2fe66778c7115ff71a588c9))
* Mejorar la lógica de auto-guardado en RoleCompetencyStateModal y manejar errores de forma más clara ([b675ad5](https://github.com/oahumada/Strato/commit/b675ad5d3703549768e6e893210fd94e4cda6f55))
* mejorar manejo de errores y notificaciones en el análisis de brechas; optimizar carga útil de la solicitud ([968cef2](https://github.com/oahumada/Strato/commit/968cef23a2996d650204c1905d3b7b510e02cace))
* Mejorar scripts de automatización de staging y documentación para importación LLM ([02a8deb](https://github.com/oahumada/Strato/commit/02a8deb9966e4d4b6134a38077f583acbd4b4e23))
* **messaging:** Phase 2 - Services, Policies, and Form Requests ([27a1a8f](https://github.com/oahumada/Strato/commit/27a1a8f858df90782573cb0cd61419b7bbdd65f3))
* **messaging:** Phase 3 - Controllers and API Routes ([ace1995](https://github.com/oahumada/Strato/commit/ace199520e31b8c455d37b1feb1ea157539d6ec2))
* **messaging:** Turbo sprint complete - Settings endpoints + Admin UI ([de8da86](https://github.com/oahumada/Strato/commit/de8da8648789f7dbb25f849f7735c5b338ea6619))
* migrate DevelopmentTab to custom UI components and Tailwind CSS with i18n support ([ca992b4](https://github.com/oahumada/Strato/commit/ca992b403bf5903a856ae7ad992f2babce7e5bc0))
* migrate to Phosphor Icons and implement i18n for FormSchema and related components ([e3a5cf1](https://github.com/oahumada/Strato/commit/e3a5cf1538ae938def0533bcee6c5fbbdb9ed2be))
* **migrations:** add raw_prompt column to scenario_generations table ([744785b](https://github.com/oahumada/Strato/commit/744785bad691346c867c3773c32b71b94a30a170))
* normalizar el módulo de planificación estratégica, actualizando rutas, controladores y modelos para usar la tabla canónica `scenarios` ([379706a](https://github.com/oahumada/Strato/commit/379706a9b5119b531a2f8de8b99a1176f61e035e))
* Normalizar módulo de workforce-planning a strategic-planning y actualizar rutas API ([e2872e5](https://github.com/oahumada/Strato/commit/e2872e558cd6c22a5b196f7b2224f519056feac3))
* **OpenMemory:** documentar metodología de testing para frontend-backend, incluyendo propósitos, alcances y buenas prácticas ([8e77b81](https://github.com/oahumada/Strato/commit/8e77b81f3a6d100a0f5d60e238b1ad22cff0de01))
* **OpenMemory:** eliminar línea en la documentación de metodología de testing para mejorar la claridad ([0c4c561](https://github.com/oahumada/Strato/commit/0c4c5611895a00ac7886e0fda71249fc6772d006))
* Operationalize Neo4j ETL by adapting to new database schema, configuring Neo4j connection, and executing via a Python virtual environment ([057d607](https://github.com/oahumada/Strato/commit/057d607c3c96b2bb9e05106cf9c49244bf0eaabc))
* Overhaul Competencies page with a new UI, implementing CRUD operations for competencies and skills, and introducing dedicated routing for the module ([e8cdf5a](https://github.com/oahumada/Strato/commit/e8cdf5aedda95d494943ed072e0f5bc3c5f4afda))
* Phase 1 Messaging MVP - models, migrations, enums, and specification ([a3b6eae](https://github.com/oahumada/Strato/commit/a3b6eaed7046c20ddeb22abf22147200c18b9600))
* **Phase 1 MVP:** Add Verification Hub with Scheduler, Notifications, and Channel Config ([1781cb2](https://github.com/oahumada/Strato/commit/1781cb28cb0e647e727ae347ffaac12b68a9e3e6))
* Phase 10 - Event-Driven Automation & Webhooks ([68fd270](https://github.com/oahumada/Strato/commit/68fd270f4246b1b870c8b97d54146658734cdb4b))
* Phase 11 - Mobile-First Support with Push Notifications, Approvals, and Offline Queue ([494108a](https://github.com/oahumada/Strato/commit/494108a574f45993356446dad5743cc8d43ea5fe))
* **Phase 2:** Add Audit, Dry-Run, Setup Wizard, and Compliance features ([70067ee](https://github.com/oahumada/Strato/commit/70067eec5823cb8431e51f418152328bd91c2c3d))
* Phase 9 - AI/ML Enhancements with anomaly detection & predictions ([a4699fe](https://github.com/oahumada/Strato/commit/a4699feeb1e44442b1238ecc6c7394dfb1032b7f))
* **PrototypeMap:** show node details panel on click (competencies) ([fefa7b1](https://github.com/oahumada/Strato/commit/fefa7b1a2a3ed77fa3efef4decf5c050827d9544))
* **qa:** add k6 stress testing suite - Fase 2 complete ([446c456](https://github.com/oahumada/Strato/commit/446c45636c20b8a52e2cab2dbc90edfec2e85c72)), closes [#k6-phase2](https://github.com/oahumada/Strato/issues/k6-phase2)
* **qa:** integrate RAGAS evaluator with scenario generation ([f4564be](https://github.com/oahumada/Strato/commit/f4564bebe0dc1b2afcf1b6f8a5d95fca06bac2d3))
* QW-1 — logging PII-safe de prompts LLM con hashing SHA-256 y correlación ([f9a52b1](https://github.com/oahumada/Strato/commit/f9a52b1950c1e7b6a8d79685784cd67bb67df7f7))
* QW-2 — LLM Quality Dashboard con RAGAS metrics y auto-refresh ([69d535d](https://github.com/oahumada/Strato/commit/69d535dbd063c22955c280c5cf11e42ca4f0f1c4))
* QW-3 - RAG endpoint /api/rag/ask con búsqueda híbrida ([cbf430c](https://github.com/oahumada/Strato/commit/cbf430cb54848740c5cd8f4121e69eb0af1025fb))
* QW-4 - Redaction Service Improvements con configurabilidad y audit trail ([dd37626](https://github.com/oahumada/Strato/commit/dd37626eaf0c5175b1ce36cc7a0c3398321c72ac))
* QW-5 - Agent Interaction Metrics with observability dashboard ([1c13fae](https://github.com/oahumada/Strato/commit/1c13faefd6c810d5be1d24db2773040c56538790))
* Redesign and implement Scenario Planning Step 2, introducing agent proposal review, bulk application, and finalization flows ([492027c](https://github.com/oahumada/Strato/commit/492027c563331d282f16ddf51c550e25a0d357a3))
* Redesign the historical evaluations UI, update roadmap and memories documentation, and modify the console kernel ([c53fad8](https://github.com/oahumada/Strato/commit/c53fad8336824a15937100c644b0362223caaa33))
* Refactor Talent Engineering components and add new functionality ([baaaa75](https://github.com/oahumada/Strato/commit/baaaa75e82abbe1b037a40e7f29c281509b9f5b5))
* refactor user handling in NavUser and UserMenuContent components ([96c77ba](https://github.com/oahumada/Strato/commit/96c77ba1c6f014faefb7ff9ebfdccd171ad28da7))
* refinar coherencia arquitectonica ([777aae5](https://github.com/oahumada/Strato/commit/777aae54c34b3bff7fd88c265f9be993766b4d9a))
* Rename TalentIA to Strato across all documentation and codebase ([0fa37dc](https://github.com/oahumada/Strato/commit/0fa37dc42dd966bf7af6755dcd3bbf2fe416e326))
* Renombrar elemento de navegación 'Workforce Planning' a 'Strategic Talent Scenarios' ([8339eec](https://github.com/oahumada/Strato/commit/8339eec6e09eb941f24322302254f70a1aa062ab))
* replace hierarchy modal with drag-and-drop connections ([cf43dd2](https://github.com/oahumada/Strato/commit/cf43dd273e367581c55c9a2c8284545eda8ab1f9))
* restore RoleCubeWizard flow, integrate digital signature, implement scenario auto-import, and enhance UI with "Cyborg Poetic" aesthetic ([e96a05f](https://github.com/oahumada/Strato/commit/e96a05fea50c4e3445d28978507b295bdf07a1ef))
* **scenario-generation:** gate auto-accept import with feature flag ([865f8bf](https://github.com/oahumada/Strato/commit/865f8bf93ccc2f7be1697b6c6035aafb1613471b))
* **scenario-modeling:** actualizar ScenarioSelector.vue para integrar nuevos endpoints y manejar cambios en la API ([44b684b](https://github.com/oahumada/Strato/commit/44b684baabc411c58922f021d6710bddf1219e48))
* **scenario-modeling:** add controllers, requests, policies and routes ([3b76081](https://github.com/oahumada/Strato/commit/3b7608104d3562ec2761bf6b8d164f714d566a8d))
* **scenario-modeling:** add database schema and models ([f72cbef](https://github.com/oahumada/Strato/commit/f72cbef7c90522e60afb1d5496f2919b55d0042f))
* **scenario-modeling:** add WorkforcePlanningService methods ([14d5d24](https://github.com/oahumada/Strato/commit/14d5d2467be3bfdc13602c9cd513bf393adb422c))
* **ScenarioPlanning:** actualizar estilos del icono de escenario, cambiando colores de la aguja norte y el centro ([562aef9](https://github.com/oahumada/Strato/commit/562aef95e852247bbe776ec50e93011413ce89ff))
* **ScenarioPlanning:** actualizar la función de reordenamiento para persistir posiciones en el servidor y mejorar la carga inicial de nodos en Index.vue ([3413b24](https://github.com/oahumada/Strato/commit/3413b246609cf753a80446b5f83e8620d5cc9c5f))
* **ScenarioPlanning:** agregar ajuste de desplazamiento para nodos seleccionados en layout 'sides', mejorando la personalización de la distribución ([acc9824](https://github.com/oahumada/Strato/commit/acc982405b7f1992352976a13b033cb845794da5))
* **ScenarioPlanning:** agregar animación de colapso para nodos nietos y mejorar la lógica de selección de habilidades ([8bb511c](https://github.com/oahumada/Strato/commit/8bb511c2cca7180167cc7c38e3d14376405bc487))
* **ScenarioPlanning:** agregar API para actualizar entidades de capacidad y atributos de relación en escenarios ([cd5f70c](https://github.com/oahumada/Strato/commit/cd5f70c4fb4e095caef929ecbd4c4264fc5616d9))
* **ScenarioPlanning:** agregar botón para restaurar vista y mejorar lógica de selección de nodos ([28f23fd](https://github.com/oahumada/Strato/commit/28f23fda0f7c67ac7d68f568a4d89d960a349c06))
* **ScenarioPlanning:** agregar configuración visual para nodos y aristas, incluyendo offsets y propiedades adaptativas ([650134c](https://github.com/oahumada/Strato/commit/650134cecc7c4261541461912b99169014bbb6a8))
* **ScenarioPlanning:** agregar control de reordenamiento de nodos y mejorar la lógica de disposición ([affc46f](https://github.com/oahumada/Strato/commit/affc46f0b489bfc1f51b1db19324cae11986d62d))
* **ScenarioPlanning:** agregar controles integrados en el SVG para reordenar y restaurar vista de nodos ([33db0fc](https://github.com/oahumada/Strato/commit/33db0fc108018d1b55cc8d9d5b97905fa854200f))
* **ScenarioPlanning:** agregar formulario editable para capacidades y atributos de relación con el escenario ([206712f](https://github.com/oahumada/Strato/commit/206712f1898eac197d89d1d1c6e6291cede0e037))
* **ScenarioPlanning:** agregar formularios para crear y asociar competencias, incluyendo edición de atributos ([eacef9c](https://github.com/oahumada/Strato/commit/eacef9c33d5cae46eaadc92fa90fab9e2dcb1796))
* **ScenarioPlanning:** agregar función para aplicar reordenamiento local de nodos sin persistir en el servidor en Index.vue ([7fac1a4](https://github.com/oahumada/Strato/commit/7fac1a4b215f1f0ba76b7480fdfd4f042aaebb5c))
* **ScenarioPlanning:** agregar función para asegurar la cookie CSRF antes de mutar solicitudes y mejorar pruebas de actualización de pivotes en el árbol de capacidades ([c17e8a4](https://github.com/oahumada/Strato/commit/c17e8a45cea4f8464d4c3cbb1e8c40f9642f0dbb))
* **ScenarioPlanning:** agregar función para mostrar solo la competencia seleccionada y su padre ([62a3833](https://github.com/oahumada/Strato/commit/62a3833f920c8c2b78a9d119785cfa9f685cf756))
* **ScenarioPlanning:** agregar funcionalidad para colapsar nodos al hacer clic en nodos expandidos ([a23aacd](https://github.com/oahumada/Strato/commit/a23aacde7b32f61048fc4bbd66be6cbf59b8651a))
* **ScenarioPlanning:** agregar funcionalidad para eliminar capacidades y relaciones en el escenario ([13c2275](https://github.com/oahumada/Strato/commit/13c22758f77acd38056657a503122642faa6e6ca))
* **ScenarioPlanning:** agregar funcionalidad para seguir el nodo de origen y mejorar la visualización de bordes de escenario ([7f0ecfc](https://github.com/oahumada/Strato/commit/7f0ecfc2e9edee6dae784320d9c5d443045c364c))
* **ScenarioPlanning:** agregar icono de rosa de los vientos en nodos de escenario y estilos correspondientes ([9d0282d](https://github.com/oahumada/Strato/commit/9d0282dd32c5702560aa0a49d4b12b1bf01231d5))
* **ScenarioPlanning:** agregar lógica para calcular el nivel de nodos y suprimir animaciones en clics de diagnóstico ([35463e0](https://github.com/oahumada/Strato/commit/35463e07002bacfb86485927a12b5c9c8779fc0c))
* **ScenarioPlanning:** agregar lógica para guardar automáticamente las posiciones al soltar el puntero y restablecer posiciones por defecto al recargar ([9899f53](https://github.com/oahumada/Strato/commit/9899f53b823ba87d432db02b941c389a88576260))
* **ScenarioPlanning:** agregar manejo de clic en nodos de escenario para reordenar y restaurar vista ([89a66b3](https://github.com/oahumada/Strato/commit/89a66b37856e94056e010b5fdd300d0c7d340510))
* **ScenarioPlanning:** agregar mejoras en el formulario de capacidades, incluyendo nuevos campos y validaciones, y optimizar la interfaz de usuario en Index.vue ([51e69e6](https://github.com/oahumada/Strato/commit/51e69e672d5af6c9d46d3a40d3a5d80a2f40cca3))
* **ScenarioPlanning:** agregar memoria detallada de sesión y mejorar la sincronización de animaciones en el componente Index ([1cc2a24](https://github.com/oahumada/Strato/commit/1cc2a24e54822d95ec9ccfe948b6a78ba51bf123))
* **ScenarioPlanning:** agregar menú contextual para nodos con opciones de ver, crear hijo y eliminar ([0486cfc](https://github.com/oahumada/Strato/commit/0486cfc5e196a4b6e0331d0641bd49f1d76ad814))
* **ScenarioPlanning:** agregar modal para crear capacidades con campos de entrada y lógica de guardado ([2aa087b](https://github.com/oahumada/Strato/commit/2aa087bdb401a4649fbea2ac8dc24168cd3b3da0))
* **ScenarioPlanning:** agregar modo de renderizado de aristas hijo y controles para cambiar el modo ([9a3cadd](https://github.com/oahumada/Strato/commit/9a3cadd503426c03e567f1087f59e4e228c611e7))
* **ScenarioPlanning:** agregar nodos de habilidad y controles para crear habilidades en el menú contextual ([cfecca8](https://github.com/oahumada/Strato/commit/cfecca818105bc213e77c54bdc9b16ee26573061))
* **ScenarioPlanning:** agregar nuevos atributos en el árbol de capacidades y mejorar el manejo de errores en la actualización de capacidades ([1ecb9a3](https://github.com/oahumada/Strato/commit/1ecb9a3590b52322669b786b682ac8637b3c657e))
* **ScenarioPlanning:** agregar offset configurable para la distancia entre padres e hijos y ajustar diseño de competencias ([08000da](https://github.com/oahumada/Strato/commit/08000da0fb3a12908df3d13e942d10e7e6062d98))
* **ScenarioPlanning:** agregar opciones de diseño personalizadas para competencias en la función de cálculo de posiciones ([f4d9614](https://github.com/oahumada/Strato/commit/f4d9614c1f7c70b5e42f5b16cd1b7dce1d95b7e4))
* **ScenarioPlanning:** agregar propiedades de desplazamiento para filas de habilidades y ajustar la lógica de cálculo de desplazamiento ([21a77ce](https://github.com/oahumada/Strato/commit/21a77ce6667cd2d5a64645a03733c2cefe7ddfe3))
* **ScenarioPlanning:** agregar pruebas para la actualización de capacidades y atributos de escenario, y mejorar la lógica de endpoints en la gestión de competencias ([bd1f69d](https://github.com/oahumada/Strato/commit/bd1f69d03c6ed5bd888c1c6e471915b2a855a0dc))
* **ScenarioPlanning:** agregar pruebas unitarias para el componente ScenarioPlanning, validando la carga y exposición de propiedades ([4fbdc46](https://github.com/oahumada/Strato/commit/4fbdc46f2767b746b621e2da35c00c1379fb4458))
* **ScenarioPlanning:** agregar referencia al elemento del menú contextual para mejorar la gestión de eventos de clic ([ef18a83](https://github.com/oahumada/Strato/commit/ef18a83bd95790c0e86dab3b46e515b485a1b92b))
* **ScenarioPlanning:** agregar soporte para aristas curvas entre escenario y capacidad con profundidad configurable ([765cb73](https://github.com/oahumada/Strato/commit/765cb73be2f2cd9f35d21a042bf65f3219324429))
* **ScenarioPlanning:** agregar soporte para conexiones entre competencias y habilidades, incluyendo parámetros específicos para aristas ([e2fff90](https://github.com/oahumada/Strato/commit/e2fff90a7a014d646d4928214e0e8ce06079cea5))
* **ScenarioPlanning:** agregar soporte para renderizar el título de breadcrumb en múltiples líneas en Index.vue ([ae354cd](https://github.com/oahumada/Strato/commit/ae354cd0904455a6fc6ee6b5c86009d712be89e5))
* **ScenarioPlanning:** agregar título de breadcrumb para mejorar la navegación y contexto en Index.vue ([b053dd2](https://github.com/oahumada/Strato/commit/b053dd27741a745a5442f573fb5a66114c9b1130))
* **ScenarioPlanning:** ajustar diseño de competencias con nuevos offsets y propiedades por defecto ([ba19bb8](https://github.com/oahumada/Strato/commit/ba19bb8d57a2e05a271a3cf369cba32c22389cc4))
* **ScenarioPlanning:** ajustar el layout de competencias para mejorar la visualización y la interacción, implementando lógica para elegir entre diseño radial y lateral según la cantidad de competencias ([b21aef0](https://github.com/oahumada/Strato/commit/b21aef0448472b84fb2d22d305cc80020704d28a))
* **ScenarioPlanning:** ajustar el radio del layout radial de competencias para mejorar la distribución visual ([32d4aa7](https://github.com/oahumada/Strato/commit/32d4aa7a8b6a889f2d694e93dd4ba6b4bf3d36c0))
* **ScenarioPlanning:** ajustar la lógica de diseño de competencias para usar layout 'matrix' con 4 o más nodos, mejorando la visualización y la interacción ([465d0d6](https://github.com/oahumada/Strato/commit/465d0d604af8bbe71f7e90b536aac9f76c209f6c))
* **ScenarioPlanning:** ajustar la lógica de disposición para limitar a 5 columnas y centrar filas incompletas ([1793acc](https://github.com/oahumada/Strato/commit/1793acc09cd54f4c8300eaea0e41e38457170f98))
* **ScenarioPlanning:** ajustar renderizado de aristas hijo para mejorar la visualización de curvas y líneas ([ee2bff8](https://github.com/oahumada/Strato/commit/ee2bff8eb3ce699cc2be03daba06675611663c69))
* **ScenarioPlanning:** ajustar visualización y layout en Index.vue con nueva prop visualConfig para mejor separación de nodos ([2c06ab7](https://github.com/oahumada/Strato/commit/2c06ab724b4991e80c9f86543617836fbbf07e01))
* **ScenarioPlanning:** cambiar el color de la aguja norte de rosa a azul en el icono de escenario ([a93f9a4](https://github.com/oahumada/Strato/commit/a93f9a4559819bc3cfbc58da0223380dbc15ce20))
* **ScenarioPlanning:** centralizar la configuración de diseño para nodos de capacidad, competencia y habilidad, mejorando la personalización y la legibilidad del código ([cf69444](https://github.com/oahumada/Strato/commit/cf69444361a89320ac2d960d2c30023c53889e33))
* **ScenarioPlanning:** colapsar habilidades expandidas al restaurar vista y mejorar opacidad de animación en aristas ([7d3076d](https://github.com/oahumada/Strato/commit/7d3076dcb387d890ad72af61ccd228cd4f57997c))
* **ScenarioPlanning:** eliminar controles de posición, ahora se guardan/restablecen por defecto ([c420660](https://github.com/oahumada/Strato/commit/c42066001d4d99a6f403c0d1b13c8abdf91b421f))
* **ScenarioPlanning:** eliminar overlay de conectores hijo y ajustar estilos de icono de escenario ([503f025](https://github.com/oahumada/Strato/commit/503f025b64ee65300b46152f9982a76fa7b21a0a))
* **ScenarioPlanning:** enhance node details with raw data and improve sidebar functionality ([c63dccd](https://github.com/oahumada/Strato/commit/c63dccd946a6148c8f41d20d0cfe24c62aa1ac5a))
* **ScenarioPlanning:** implementar actualización optimista y persistencia de posiciones tras reordenar nodos ([57d3df4](https://github.com/oahumada/Strato/commit/57d3df410559058a66036c985e2c59f7cc07657e))
* **ScenarioPlanning:** implementar configuración centralizada de layout para competencias y skills, mejorando distribución y evitando solapamientos ([6e358d5](https://github.com/oahumada/Strato/commit/6e358d510aa7845cb899994d036f44540760e90f))
* **ScenarioPlanning:** implementar lógica de creación de relaciones en caso de que no existan durante la actualización de capacidades ([31d8536](https://github.com/oahumada/Strato/commit/31d853666fa66f83b9d1db20bf01cc400873e7f3))
* **ScenarioPlanning:** implementar lógica para adjuntar capacidades a escenarios y mejorar la gestión de relaciones en la API ([13b2be3](https://github.com/oahumada/Strato/commit/13b2be3376b901555dc15308444ab2aeaec4295f))
* **ScenarioPlanning:** implementar modelo N:N para competencias, eliminando la relación 1:N y ajustando la lógica de creación y asociación en el modal de capacidades ([3924c5a](https://github.com/oahumada/Strato/commit/3924c5a1630c5a475ae1ef155c455cc639d0f50c))
* **ScenarioPlanning:** integrar control 'Crear capacidad' junto al control de inicio y emitir evento al hacer clic ([732d05a](https://github.com/oahumada/Strato/commit/732d05a9fab0e362ad4a477a6f9df0c68bb6682b))
* **ScenarioPlanning:** integrar controles para abrir información del escenario en la esfera del nodo y en el borde derecho del diagrama ([3fa4ec6](https://github.com/oahumada/Strato/commit/3fa4ec623a32bb735a68beaec41e3ba587e52c41))
* **ScenarioPlanning:** integrar título en el diagrama dentro de Index.vue para mejorar la presentación visual ([64e4a97](https://github.com/oahumada/Strato/commit/64e4a970ede2889fa86e631fdb001bd84f971d10))
* **ScenarioPlanning:** mejorar animación de colapso para nodos nietos y ajustar opacidad de bordes ([e5b5138](https://github.com/oahumada/Strato/commit/e5b5138cd1881916fbaf290c12d15e11ae978c6d))
* **ScenarioPlanning:** mejorar cálculo de métricas de preparación en el método getIQ y optimizar la estructura del componente de visualización ([4415ef7](https://github.com/oahumada/Strato/commit/4415ef75997fd608dbc0b80477b42241e35fa7d7))
* **ScenarioPlanning:** mejorar el diseño del icono de rosa de los vientos con un X sutil y ajustar la escala de la aguja secundaria ([8469a13](https://github.com/oahumada/Strato/commit/8469a131f65c0f21d30c05363f182679669cb20b))
* **ScenarioPlanning:** mejorar el título de breadcrumb para incluir la ruta completa de la entidad seleccionada en Index.vue ([6449908](https://github.com/oahumada/Strato/commit/6449908fe4e87c4b10544c353464461f43fb1e8d))
* **ScenarioPlanning:** mejorar la creación y asociación de competencias en el modal de capacidad, asegurando la cookie CSRF y considerando el nodo de visualización ([98ffa6c](https://github.com/oahumada/Strato/commit/98ffa6c55ae0eb4065227263befd8adb3a572cad))
* **ScenarioPlanning:** mejorar la distribución de nodos para evitar superposiciones alrededor del nodo enfocado ([6e51d01](https://github.com/oahumada/Strato/commit/6e51d01019a53e18ec62d417922d3d0606cdb98d))
* **ScenarioPlanning:** mejorar la lógica de los diálogos y clamping del menú contextual en Index.vue ([3659247](https://github.com/oahumada/Strato/commit/36592476b846c13bd6e74c40f76391654b954e8b))
* **ScenarioPlanning:** mejorar la lógica para crear competencias en escenarios, manejando casos de existencia y errores ([ada42d7](https://github.com/oahumada/Strato/commit/ada42d7e49cc6b23d6e6aa7328357f848fc4f792))
* **ScenarioPlanning:** mejorar la restauración de vista y distribución de competencias, ajustando el layout según el nodo padre y centrando el nodo seleccionado ([794953f](https://github.com/oahumada/Strato/commit/794953f1c013efc7e08629fd718cc16ef2a25005))
* **ScenarioPlanning:** mejorar la visualización de etiquetas de nodos y agregar función para envolver etiquetas largas ([fadb620](https://github.com/oahumada/Strato/commit/fadb6204f7511a1953bcdfccd966e53f277316ed))
* **ScenarioPlanning:** mejorar manejo de errores en la actualización de capacidades para manejar respuestas 404 y mostrar mensajes de error adecuados ([37a5142](https://github.com/oahumada/Strato/commit/37a514259e04c9389e741e613ee266c5d0c26e8f))
* **ScenarioPlanning:** mejorar menú contextual con v-menu de Vuetify y agregar opción para adjuntar habilidades existentes ([ae9f18a](https://github.com/oahumada/Strato/commit/ae9f18aaa30fe61b0cd7b05bc549e56ade8b7729))
* **ScenarioPlanning:** mejorar visualización de nodos y bordes con nuevos gradientes y efectos de brillo ([cad8609](https://github.com/oahumada/Strato/commit/cad8609a2cbd2c4a11ccac9deee7ae88bda1d021))
* **ScenarioPlanning:** migrar flags de animación a campos explícitos en el modelo de nodo y agregar lógica para manejar habilidades en competencias ([323921d](https://github.com/oahumada/Strato/commit/323921da17de4da77997e0693f0fa43a48ae16c5))
* **ScenarioPlanning:** normalizar atributos de pivote y mejorar la lógica de carga de capacidades en el modal de creación ([742e1cf](https://github.com/oahumada/Strato/commit/742e1cf38e9b401787dd47aca00b8687f74adeaa))
* **ScenarioPlanning:** reemplazar inputs numéricos por sliders en formularios de capacidades para mejorar la usabilidad y coherencia visual en Index.vue ([a9d1a17](https://github.com/oahumada/Strato/commit/a9d1a1749f8ce244a5494907cd53763fad761410))
* **ScenarioPlanning:** reemplazar menú contextual con v-menu de Vuetify para mejorar la interfaz ([f142e62](https://github.com/oahumada/Strato/commit/f142e62ccb55f47c0dc4318de3cd858a75150b94))
* **ScenarioPlanning:** reemplazar panel lateral por modal para mostrar detalles de nodos ([6a712e2](https://github.com/oahumada/Strato/commit/6a712e260d87a97146fdcd7a06009426db4c98d7))
* **ScenarioPlanning:** renombrar módulo a 'ScenarioPlanning' y mejorar visualización de nodos con efecto burbuja ([7c3fc30](https://github.com/oahumada/Strato/commit/7c3fc3065e4f03eec4a3ae4c148a9a180182f1f4))
* **Scenario:** update capability tree structure and add competency skills ([be0aef5](https://github.com/oahumada/Strato/commit/be0aef57cfe6cf856cb5da1d5ca9dfd63faf4a61))
* **security-ui:** improve event labels for admin monitoring ([7cf1737](https://github.com/oahumada/Strato/commit/7cf17373881a9c5c96fb36dea0fb9bc4c9b60213))
* **security:** add admin access monitoring UI and Phase 12 docs ([708e87e](https://github.com/oahumada/Strato/commit/708e87e492baa23698e9466cc66e5844a61d381a))
* **seed:** add CompetencySeeder and integrate into DemoSeeder ([eb9c751](https://github.com/oahumada/Strato/commit/eb9c75169e43f80ac41a4029c7c682cc151a0174))
* sistema de coherencia arquitectónica y roles de referencia ([fc457ee](https://github.com/oahumada/Strato/commit/fc457eee16f221ee591baa4c3f260eca4a1c03aa))
* Sprint 0 activation — GuideFaq seeder (31 FAQs), enable embeddings feature flag, fix forScenario assumptions cast ([6e463d2](https://github.com/oahumada/Strato/commit/6e463d27972e8367a584e28722c23dee1a727767))
* **Step 1:** Add automatic verification phase transition scheduler ([3c74e01](https://github.com/oahumada/Strato/commit/3c74e0183a2e0a8e17d905a9244c99df580235c9))
* **Step 2:** Add verification notification system ([71f9f55](https://github.com/oahumada/Strato/commit/71f9f55013f57ed65566239c6882ebef98c411e2))
* **Step 3:** Add comprehensive test suite for verification system ([ee5cc50](https://github.com/oahumada/Strato/commit/ee5cc507d2f31c30bf04ffe5dc134053fc29fd8b))
* **Step 4:** Add verification metrics dashboard ([42a6a64](https://github.com/oahumada/Strato/commit/42a6a64a29f4237063f0d94c28f93e923591c20f))
* **talent:** Implement Stratos IQ and Talent Pass (Sovereign Identity) backend and document ([ea07ae7](https://github.com/oahumada/Strato/commit/ea07ae7488f98764ae15a69387721b208eddf6d0))
* Tarea 2 - TalentVerificationService core implementation ([650829b](https://github.com/oahumada/Strato/commit/650829b2efb996ba1170aa3d959197d150606e14))
* Tarea 3 - 9 Business Rules Validators with factory integration (1175 LOC) ([ca7b0da](https://github.com/oahumada/Strato/commit/ca7b0dad2d8249455a353b09adb7caff429df9d0))
* Tarea 5 Phase 2 - VerificationIntegrationService Integration (550 LOC, 4-phase rollout) ([2b6bd61](https://github.com/oahumada/Strato/commit/2b6bd6186e8d4108f2c1606585df33ccfc9cce02))
* Tarea 5 Phase 3 - Verification Phase Integration Tests (20 feature tests, 393 total passing) ([a2095a3](https://github.com/oahumada/Strato/commit/a2095a3e6177ca7f6dc20df43db6344fbb0a00b0))
* Tarea 5 Phase 4 - Deployment Validation Tests & OpenAPI Documentation ([04b369a](https://github.com/oahumada/Strato/commit/04b369abf677c3ce6c93ce2d88377f1b85a0fe20))
* **tests:** actualizar configuración de Vitest para excluir pruebas e2e y deshabilitar hilos ([55b73ee](https://github.com/oahumada/Strato/commit/55b73ee81e72da764a94343a1548fec14d7e569d))
* **tests:** agregar pruebas para computeCompetencyMatrixPositions y computeSidesPositions; incluir integración para ScenarioPlanning ([76015f7](https://github.com/oahumada/Strato/commit/76015f7d4ba1a845aef1a130885ae17883626f46))
* **tests:** agregar pruebas para guardar competencias y pivotes en ScenarioPlanning ([2255f71](https://github.com/oahumada/Strato/commit/2255f712fb513bf90dd54d1ab62d79629bdf193f))
* **ui:** render competencies as temporary child nodes around selected capability ([5f355cb](https://github.com/oahumada/Strato/commit/5f355cbc4d71a4bd1aa3ea8c4a55cf22dc1f4c83))
* update DatabaseSeeder to include WorkforcePlanningSeeder ([65b1e35](https://github.com/oahumada/Strato/commit/65b1e359bb2654eff30d3e78a30e0d30d5207f7e))
* update pre-push hook to run only unit tests and add admin role to UserFactory ([26538e8](https://github.com/oahumada/Strato/commit/26538e8fdf8a3b047efadbdcbe89868bf0b95e59))
* Update roadmap for Wave 3, integrate pending Wave 2 items, and refine strategic implementation details ([872fa1d](https://github.com/oahumada/Strato/commit/872fa1db48c51a26ca78f5199644df9e5a0688c7))


### 🐛 Correcciones de Bugs

* actualizar test de modal para nuevo título de leyenda ([f3a7ca8](https://github.com/oahumada/Strato/commit/f3a7ca8db3bb1617eeb2442ff70e4a5bced69c7d))
* add Array.isArray() validation to all filtered getters in store ([620d5ff](https://github.com/oahumada/Strato/commit/620d5ff9dc01a537ab0d8827e9947965a44bf244))
* add Array.isArray() validation to all store getters ([e692b16](https://github.com/oahumada/Strato/commit/e692b1627149c519bfb4e26b147ea596ab0709a9))
* add defensive Array.isArray() checks in computed properties ([662fff2](https://github.com/oahumada/Strato/commit/662fff2cda3606ce9f5edbf022a0df25e9c63d42))
* add missing composables for workforce planning components ([acdfded](https://github.com/oahumada/Strato/commit/acdfded0899ba907ebf3359189fe90ca01eef9ab))
* add px-12 padding to dashboard grid rows for better spacing ([17bccc3](https://github.com/oahumada/Strato/commit/17bccc3219ea7fdc7a0563e041823365529c0058))
* add scenarioId validation and empty array handling in store ([447319d](https://github.com/oahumada/Strato/commit/447319d0ccc743ff020339c4c3ce87cfc56ff28e))
* Adjust API test assertions to match the new `data` key in API responses ([229c4e6](https://github.com/oahumada/Strato/commit/229c4e68a77d89b2ae8cbba6f60df4964a13cb26))
* AutomationTest.php syntax and structure corrections ([e672026](https://github.com/oahumada/Strato/commit/e672026c84765b6a3ecf383461306537f6ac8bc8))
* **ci:** correct husky hooks and update tests for refactored components ([a803d34](https://github.com/oahumada/Strato/commit/a803d34543a9dece1a1e6ac068ee4b760f39920a))
* **CI:** corregir formato de ramas en flujo de CI y ajustar comillas en versiones de PHP y Node ([cfca28e](https://github.com/oahumada/Strato/commit/cfca28ecf79513b8cafd58beb1ce02f894be481f))
* correct foreign key constraints and data inconsistencies for scenario role competencies and skills, and enhance skill derivation with current level calculations ([692a332](https://github.com/oahumada/Strato/commit/692a332dd8fcb2622610ca7011eb3731a5696d79))
* correct skills catalog loader in CatalogsRepository ([b13a038](https://github.com/oahumada/Strato/commit/b13a0381efcd6364a72cb887741d41df74e8c4db))
* correct syntax errors in ScenarioPlanning Index.vue template binding (:class had stray ! and ==! instead of !==) ([750ebfd](https://github.com/oahumada/Strato/commit/750ebfdc7148209d902c9ed66f8c488b106b5be2))
* correctly resolve role ID from ScenarioRole for gap analysis and strategy storage ([1b1b391](https://github.com/oahumada/Strato/commit/1b1b39174ce69aa7ab6a4afa24c1ebe196cb5fa2))
* corregir tests y bug en PsychometricProfile.people() alias ([d8d6067](https://github.com/oahumada/Strato/commit/d8d60671b1d3f6fdbcfd4d526bf200fef5576fcf))
* evitar traslape en módulo People — limitar ancho del header y ajustar metadata (responsive) ([7c3ceca](https://github.com/oahumada/Strato/commit/7c3ceca3cbdcf4e66bf2fccc28a31c2cb6fad91e))
* fix connection line type and instant state update ([def1916](https://github.com/oahumada/Strato/commit/def1916636e05473361a7e98ea274cafb127e974))
* **generate:** restoreDefault finally dup + harden GenerateWizard modal rendering ([5c5ceef](https://github.com/oahumada/Strato/commit/5c5ceefdbbe1442e85db374795dd6480aeeb2397))
* guarda relacion roles/competencia ([e2921c3](https://github.com/oahumada/Strato/commit/e2921c3967fd85b645ee0d8156ba53d7008d807c))
* handle missing analytics gracefully in API ([e0b93ff](https://github.com/oahumada/Strato/commit/e0b93ff4b5347e614a322d6976d97db32b3dc5c8))
* handle v-data-table click event structure in ScenarioSelector ([ebf6c47](https://github.com/oahumada/Strato/commit/ebf6c47d6ad77f528b0781fe24e4c6ed3ba4b8c6))
* **husky:** corrected pre-push hook to run from root instead of src directory ([b660059](https://github.com/oahumada/Strato/commit/b660059590fe31c67519ef3eeef1f243a52d0221))
* Improve `ScenarioSimulationStatus` rendering conditions and `scenario.id` prop handling for robustness ([5e72266](https://github.com/oahumada/Strato/commit/5e72266019eb7d0622c8f1016bcfbc04e6a4be5f))
* improve drag-and-drop UX with bezier curves and instant state update ([a40e4b3](https://github.com/oahumada/Strato/commit/a40e4b30cd1de0829bffb28f820aa795fef9d870))
* improve text visibility in compliance dashboard - add white color to inputs, increase table header opacity, and improve JSON preview contrast ([0cfd298](https://github.com/oahumada/Strato/commit/0cfd298cdc3ebca7af46a8ae5fc9f071d1c41105))
* **messaging:** Complete Message model + factory + controller ([e1580ad](https://github.com/oahumada/Strato/commit/e1580ade0fd2f7971b80455a07b1b18b77b241e4))
* pass off on off off off off off off off off on off on off on off off off on off off off on on off off off on on off off off off off off off on off off off off on off on on off off off on on off on on off off off on off on on off on on off off on on on off on on off on off off off off on off off off on off off on off on off off off on on off on off on off off on off off off on off off off on off off off off on off off off on on on off on on off off on off on on on off on on off on on on off off off off off on on off off on off off off off off on off off on on off on on off on off off off on off off off off on on off on off off on off off on off on off off off off off off off off off off on on off on off off off into ChangeSetService transaction closure to respect ignored_indexes ([2973759](https://github.com/oahumada/Strato/commit/2973759c3b322ca15330d68bc816abcf53d7e9d7))
* pass orgId through simulateExpansion and calculateKpiImpact — eliminate Organization::first() causing intermittent test failures ([75998c9](https://github.com/oahumada/Strato/commit/75998c96f3572777060b1df132db9c22c0b76277))
* Phase 4 Unit Tests - all 16 tests passing ([621d6a2](https://github.com/oahumada/Strato/commit/621d6a20c3e5cc7c4f9d14509cf69688c136c050))
* refactor tests to use configurable AI service URLs and `app()->call` for job handling, and correct `ScenarioRole` ID reference ([7445138](https://github.com/oahumada/Strato/commit/74451388db91a232a7e9d1361b37d21eb65ce4e2))
* register Pinia store in app initialization ([b7e1375](https://github.com/oahumada/Strato/commit/b7e1375cf6250176735cafdda81e3af3b993e9c3))
* RemediationService syntax error - null coalescing in string interpolation ([fff3989](https://github.com/oahumada/Strato/commit/fff3989b32f829ec8b60c88911c23f8bb5369394))
* remove duplicate fetchMatches declaration in MatchingResults ([cb477ea](https://github.com/oahumada/Strato/commit/cb477eafee3385900c307ab2990ef6f868e87123))
* removed redundant `defineProps` imports from Vue components and relaxed commitlint rules for subject casing and line lengths ([d57f8c5](https://github.com/oahumada/Strato/commit/d57f8c5eb06ad37515363c2083b4436b4468064c))
* resolve 19 failing tests across Analytics, Mobile, VerificationHub and integration suites ([609448e](https://github.com/oahumada/Strato/commit/609448ef3c6574640ed1e5832de503452b25d492))
* Resolve argument mismatch and cleanup unused parameters in AgenticScenarioService ([5b0d379](https://github.com/oahumada/Strato/commit/5b0d3790b6bbc4bed05fc2ebd66b6e4b18d107dd))
* resolve skill creation context issues and improve UI refresh ([6cccf3b](https://github.com/oahumada/Strato/commit/6cccf3b7a11227f1fb5655c2b17cb9c710f75651))
* resolve undefined references in store-integrated components ([af81160](https://github.com/oahumada/Strato/commit/af81160cea69048fd96f23cc48294210060ede10))
* resolver duplicación y error al añadir roles ([1fd891e](https://github.com/oahumada/Strato/commit/1fd891e9821f2fd7f21add67a2c5e9621e4c08f6))
* send GET params correctly; remove debug logs (useApi, OverviewDashboard, add CSRF meta) ([2e6c634](https://github.com/oahumada/Strato/commit/2e6c634b7bdc6578a3a2bdec4de84caa74292477))
* **ui:** normalize LLM responses for GenerateWizard modal (handle content/escenario shapes) ([eb4f051](https://github.com/oahumada/Strato/commit/eb4f0513453244f978b7660759af3471dcf647a5))
* update test results and refactor capacity to capability ([a141fd8](https://github.com/oahumada/Strato/commit/a141fd854fd39155e550d07b00e9237f369c23ec))
* use proper bezier edge type in VueFlow ([5948b7d](https://github.com/oahumada/Strato/commit/5948b7d31b1a6e267b79c896d92642dc8f2fa1c0))


### 📚 Documentación

* actualizar plan cognitivo 2026 - Bloque 4 Fases 1 y 2 completadas ([e4b5344](https://github.com/oahumada/Strato/commit/e4b53446a43952121633158eedbc92bc176f72ec))
* actualizar plan cognitivo con Bloque 4 Fase 3 completada ([7b30bb0](https://github.com/oahumada/Strato/commit/7b30bb09e29686ec593896a07d7f35796f6bcbc8))
* actualizar roadmap con progreso de bloq A2, A3, A4 (Cubo AI y competencias) ([fb14c43](https://github.com/oahumada/Strato/commit/fb14c43787124ed81c93fe20484ebb304d1f3d5f))
* add 8th benefit - predictive performance (future evaluation capability) ([073bb6e](https://github.com/oahumada/Strato/commit/073bb6e435598d3986d8c2590dc5280476e4b9e0))
* add 9th benefit - Stratos Experience workplace monitoring & leadership insights ([899c73e](https://github.com/oahumada/Strato/commit/899c73e608117fa3f9809ff1f4c6d8e366d4ba2f))
* add analysis of integration between two workforce planning models ([1ded57a](https://github.com/oahumada/Strato/commit/1ded57a1333b5caf3428a044fb3200d21331bda0))
* add benefit 12 - 360 Evaluations without bias via triangulation (Verified Evidence + KPIs + Social Consensus + Vanguard Motor) ([072ff35](https://github.com/oahumada/Strato/commit/072ff357f7a07b263c309c916867746f859b24f3))
* add benefits & value proposition section - Stratos Cognitive Intelligence (non-technical audience) ([89c1532](https://github.com/oahumada/Strato/commit/89c1532e3a19273f060523ce7cb3b0e767acfe2c))
* add benefits 10-11 - Culture alignment for strategy + Psychometrics dynamic evaluation ([b471482](https://github.com/oahumada/Strato/commit/b471482d9aab7a4a439295bf76a137078630185c))
* add cheat sheet for rapid demo reference - Compliance Audit Dashboard ([5cda3b7](https://github.com/oahumada/Strato/commit/5cda3b7d13c3a614a1ca29715d1d9f547fb12f11))
* add complete demo package with seeder script and master README - ready for internal/external demo ([5f7ce79](https://github.com/oahumada/Strato/commit/5f7ce79787a0f4ebb9e5f88f5034222afc07a3da))
* add complete documentation review summary for workforce planning module ([e084146](https://github.com/oahumada/Strato/commit/e084146ae2bf6749d036e545ee0db391c2a58f77))
* add comprehensive charts implementation documentation ([2fdc1c5](https://github.com/oahumada/Strato/commit/2fdc1c599d70034ed44df88f2eb5d759108577ff))
* add comprehensive comparison analysis documentation vs implementation ([eb2cc05](https://github.com/oahumada/Strato/commit/eb2cc05ac6d1f0c3bbe2b2971b2dc3f36f7237c1))
* add comprehensive index - complete navigation and reference guide for entire demo package ([db5ffa2](https://github.com/oahumada/Strato/commit/db5ffa2f624d31c587d17473eb5e209de2bf0221))
* add comprehensive inventory of all workforce planning documentation - 7012 lines across 16 docs ([0a0e205](https://github.com/oahumada/Strato/commit/0a0e205f5ada3c7d5442902d583c51f61ffdf304))
* add comprehensive QA checklist and interpretation guide for Compliance Audit Dashboard ([88ace9f](https://github.com/oahumada/Strato/commit/88ace9f6aaf1db84893c75ba463914a9fd2982da))
* add comprehensive session documentation (A/B/C plans + summary) ([95e3e07](https://github.com/oahumada/Strato/commit/95e3e076fd18a461781879449367f3612812b327))
* add comprehensive status review of workforce planning module - what's included and what's pending ([6b088f4](https://github.com/oahumada/Strato/commit/6b088f48090f338b8b2974f83cbc1d798e3aa8a6))
* add comprehensive summary table - 12 Benefits with implementation details ([3e6c24a](https://github.com/oahumada/Strato/commit/3e6c24a9250ac8eeb48c7db85430cb4204bacebc))
* Add comprehensive test execution reference guide ([a796754](https://github.com/oahumada/Strato/commit/a7967546758938eb0fb31f8dc7ba218c7a2f4792))
* Add comprehensive testing phase summary (150+ test cases delivered) ([1d15763](https://github.com/oahumada/Strato/commit/1d1576349b3bc700e0214d7cf6af6a71eb560c0e))
* Add comprehensive Verification Hub documentation ([2bf6a08](https://github.com/oahumada/Strato/commit/2bf6a0805dedd380f2a70da381eabd0600d98515))
* add comprehensive workforce planning implementation summary ([4529ba1](https://github.com/oahumada/Strato/commit/4529ba1fb09b59d9b1a23fc2c88d18e53e0a091d))
* Add documentation explaining the Gravitational Node Unification concept ([990d0ce](https://github.com/oahumada/Strato/commit/990d0ced9ea5c877ec85c5c6ef1974fee3e5a5b6))
* add documentation index for Session Day 7 ([028dde2](https://github.com/oahumada/Strato/commit/028dde2b0d618bf4f31131519fb35e15802a94bb))
* add entry point guide for workforce planning documentation - role-based navigation ([b03076c](https://github.com/oahumada/Strato/commit/b03076c5cf3bcb6749a55baa5a992999aabba1b1))
* Add executive status to roadmap - Messaging MVP ready for staging ([4a2bc3c](https://github.com/oahumada/Strato/commit/4a2bc3cb2c7c1c87801bc032df5e21fb412fff3a))
* add executive summary of jan 5 2026 session - complete documentation consolidation ([e7fcfa3](https://github.com/oahumada/Strato/commit/e7fcfa3cd89e6312dbc9460808062e707c7d2c89))
* Add initial documentation outlining the Stratos product vision, modular architecture, and strategic implementation roadmap ([e9562a5](https://github.com/oahumada/Strato/commit/e9562a5a38a38570094f4a815517da094edbeec8))
* add LLM-assisted scenario creation status summary ([913d1c1](https://github.com/oahumada/Strato/commit/913d1c1c8dbd94ba88d39c67be3ae59e07cfd8e2))
* add messaging MVP progress tracker with phase breakdown ([00076c9](https://github.com/oahumada/Strato/commit/00076c9a435fc63956b23bf1b1b32d1ab7635b60))
* Add non-technical guide to Tarea 5 Phases 1-4 ([29e0833](https://github.com/oahumada/Strato/commit/29e083361d30d60417626c3974d7bcacd5e277e3))
* add operational manual for data ingestion and integrity check command ([6c71bb9](https://github.com/oahumada/Strato/commit/6c71bb95ea1a9fe9c7d9b2e5277df69e2ed0bba5))
* Add Optional Phases Analysis to main Tarea 5 documentation ([d827018](https://github.com/oahumada/Strato/commit/d827018ae7838f45962af474652ba7bc8fa59154))
* Add Quick Start Guide for Tarea 5 Deployment ([ffd39d4](https://github.com/oahumada/Strato/commit/ffd39d4b3ccc738c9fde2d0eb4798343aaad376e))
* Add QW-5 Agent Metrics documentation ([f09e350](https://github.com/oahumada/Strato/commit/f09e35046f62cf77fb27503dee2bdb5d660fd674))
* add session summary for january 5 2026 - documentation consolidation and integrated model creation ([0001ce6](https://github.com/oahumada/Strato/commit/0001ce6e5b2d04cce3debc0652c93cb1c26cd1f2))
* Add session summary for Stratos Glass refactor, Phosphor Icons, and i18n, and update design system and index documentation ([1d67fdd](https://github.com/oahumada/Strato/commit/1d67fddbcd41d7aa84e59cb8924cd90b1c4e6fde))
* Add staging deployment + turbo sprint completion reports ([2a368d3](https://github.com/oahumada/Strato/commit/2a368d3f167a49892fad264881d5f8a6f8e2d642))
* Add staging deployment checklist - MVP ready for staging ([ab78278](https://github.com/oahumada/Strato/commit/ab78278cbfd230d8632a444e2f85778d1381e322))
* add summary of methodology integration - completes documentation consolidation phase ([3a57337](https://github.com/oahumada/Strato/commit/3a5733707f33ea2a4a51eec333d34e15681e7861))
* Add Tarea 5 Deployment Roadmap - Phase Impact Analysis ([481f63f](https://github.com/oahumada/Strato/commit/481f63f1f43530289425d8daeefc50e510f7c02c))
* Add Tarea 5 Implementation Plan - TalentVerificationService Integration ([4436471](https://github.com/oahumada/Strato/commit/44364711c13e2ab3d3b9bb09db3ce32201b2b611))
* add technical excellence attack plan and update indices ([1f56223](https://github.com/oahumada/Strato/commit/1f56223fd878eed3ede5fa6d498b5dab3c71c961))
* Add verification guide for Step 2 Role Cube Blueprinting ([e0c552f](https://github.com/oahumada/Strato/commit/e0c552f6b83744acc32bdc9021c3f8668909695b))
* Add Verification Hub completion report ([a237893](https://github.com/oahumada/Strato/commit/a237893126f831d76ca3745517d03352b190f8ed))
* add visual status dashboard for workforce planning module ([b6b6f13](https://github.com/oahumada/Strato/commit/b6b6f1399f122ecabfd9a2f0611bced85d4b2c96))
* add workforce planning UI integration guide ([3acd87c](https://github.com/oahumada/Strato/commit/3acd87c661c697317f3c4ea867587e4f85f9cdc4))
* complete roadmap transition 18 pillars + narrative testing layer + executive summary ([9881c54](https://github.com/oahumada/Strato/commit/9881c543da38befae39ba97e8ca6e795bf27254d)), closes [#18](https://github.com/oahumada/Strato/issues/18)
* comprehensive openmemory update - Verification Hub section, pending items, executive summary ([e81e838](https://github.com/oahumada/Strato/commit/e81e838e1228b71f49923bb36d24cb7a6c352cb1))
* Consolidate Step 1 scenario planning documentation ([77ed8ee](https://github.com/oahumada/Strato/commit/77ed8eeb26317abfaf8ea9d2ab4b36190c53a200))
* create comprehensive index for workforce planning documentation ([c98d21a](https://github.com/oahumada/Strato/commit/c98d21a980277c56e578644599a5f49345b18a37))
* create integrated workforce planning model - canonical reference combining both models with operational depth ([68fe2c3](https://github.com/oahumada/Strato/commit/68fe2c31670207cf18162bbff566f1189953e398))
* create integration guide mapping 7-block model to 7-phase methodology with decision flow ([c22b951](https://github.com/oahumada/Strato/commit/c22b9513e49a59385182627df5b30e9b83dc3b6d))
* document DeepSeek integration stabilization, LLM-agnostic architecture, and cost efficiency improvements, including a new session summary and LLM agnostic guide ([aaa67f1](https://github.com/oahumada/Strato/commit/aaa67f177da1c4457cab6bd9253fe7129d2ce2d7))
* Document read-only protocol and assumption immutability in Scenario Planning ([d4b897f](https://github.com/oahumada/Strato/commit/d4b897f01a632ea65958ef7eb231627b97fc07a6))
* Document Tarea 4 improvements and completion ([6b6127e](https://github.com/oahumada/Strato/commit/6b6127ef2a7c2e0ff512679ba0d1515d2028bbfe))
* enhance compliance demo package with metadata, requirements, timings, and advanced troubleshooting ([63b48ce](https://github.com/oahumada/Strato/commit/63b48ce3f4c386611ab9768d044afa119e9b8500))
* Finalize session updates, add session summary and refine architecture documentation ([a0a61a9](https://github.com/oahumada/Strato/commit/a0a61a93187d6d872578ba72bec24a2c0644e45b))
* incluir fundamento teorico de elliott jaques (sst) ([781ad19](https://github.com/oahumada/Strato/commit/781ad1902e203d406dcde3117d6236b1688f716b))
* **infra:** Add comprehensive Neo4j concepts and architecture guide ([73105a7](https://github.com/oahumada/Strato/commit/73105a75ee900b9713a24a1aba561026c7ac4657))
* **infra:** Document B1 Neo4j Live setup and schedule syncs ([9d4c874](https://github.com/oahumada/Strato/commit/9d4c874f1a47046a36609e83ee033b0cff9bdabc))
* integrate MetodologiaPasoAPaso as operational implementation guide - link to model and update index with role-based navigation ([85f7a70](https://github.com/oahumada/Strato/commit/85f7a708f25431f38d4e4f045c96ef24920cdeac))
* integrate strategic roadmap 2026 and talent 360 vision ([49b0fe1](https://github.com/oahumada/Strato/commit/49b0fe1ea8dbccaa289403ffc95f9e78bc8b687f))
* **openmemory:** mark Phase 12 Enterprise Security as completed ([18e1aaf](https://github.com/oahumada/Strato/commit/18e1aafef719e268bcd1f83078a3146e74dcf1ed))
* Phase 4 completion report - all 16 unit tests passing ([72fb04c](https://github.com/oahumada/Strato/commit/72fb04cf1fa6d228495037dc10a9ff658adb6e17))
* Phase 4 progress update - 81% unit tests passing ([477cc0b](https://github.com/oahumada/Strato/commit/477cc0b0c724760cd61e5af1c3b8e65031ef9114))
* plan estratégico de mejora cognitiva de Stratos — memoria, RAG, orquestador, learning loop ([62a4520](https://github.com/oahumada/Strato/commit/62a45204c82496b1f2c037fafdb06db54462ed78))
* prompt contexto del proyecto actualizado ([0ab499e](https://github.com/oahumada/Strato/commit/0ab499ebb9e5ad53a7c55d1e8c70c345abdfcdb8))
* **qa:** extend chaos progress with redis fallback scenario ([2ce9d77](https://github.com/oahumada/Strato/commit/2ce9d770cea6bdd1a22439b74a4b7b982aeaa688))
* **qa:** mark accessibility clean-up as completed ([a2d346b](https://github.com/oahumada/Strato/commit/a2d346b4a987b428f7527bf260d0e1d83afd1a22))
* **qa:** mark chaos engineering as completed ([559becf](https://github.com/oahumada/Strato/commit/559becf249cd3b425593d0528d2672c3a9e5f3a3))
* **qa:** mark chaos engineering as in progress ([8fbcde8](https://github.com/oahumada/Strato/commit/8fbcde8e4c3d411a45bc5e53accb732b8e247a53))
* refinar modelos conceptuales y terminología ([6a6a0e2](https://github.com/oahumada/Strato/commit/6a6a0e2d03dd754f0ac132e988aa09e3ae7c3cfe))
* Session Phase 4 progress report - comprehensive summary of work completed ([2799ee8](https://github.com/oahumada/Strato/commit/2799ee85e0de21be858291f2fc52ae897c4b00fd))
* Staging deployment report - Messaging MVP ready ([1ebef46](https://github.com/oahumada/Strato/commit/1ebef46604bc66dc0a7bf03c32db12631a221d9d))
* update Bloque 5 Sprint 3.1 - Tarea 1 completion (VerifierAgent DTOs + Config + Tests) ([8ad5800](https://github.com/oahumada/Strato/commit/8ad580095240cab2862bd8b7d085e911d9876e4d))
* update GUIA_GENERACION_ESCENARIOS with implementation status and acceptance steps ([086fa8c](https://github.com/oahumada/Strato/commit/086fa8c95dcf862abab66d62e04925f85add9f55))
* update index to include integration guide as key reference document ([1006771](https://github.com/oahumada/Strato/commit/1006771a2602e160888c8cc6d1be699bc591f1b2))
* Update MESSAGING_MVP_PROGRESS.md - Phase 4 complete ([9ed7cfe](https://github.com/oahumada/Strato/commit/9ed7cfe0334746d7f9d852b6f2e5e1599e559db8))
* update openmemory - add QW-1 and QW-2 completion summary ([bd17b86](https://github.com/oahumada/Strato/commit/bd17b869a74ff728edf2f4ab71a57b07571701a5))
* update openmemory - add QW-4 redaction improvements completion ([2bc3828](https://github.com/oahumada/Strato/commit/2bc382887f016b36dfb36cd2199811dcde62b3f9))
* Update openmemory - Phase 10 completion (automation & webhooks) ([074ecaf](https://github.com/oahumada/Strato/commit/074ecaf596f24d0fddde77afa6d28b0a7e2a7e1d))
* Update openmemory - Tarea 5 ALL 4 PHASES COMPLETE ([502e9d0](https://github.com/oahumada/Strato/commit/502e9d043351cb1733a00c4f7db63f4bea732279))
* Update openmemory - Tarea 5 Phase 2 completion ([62534d4](https://github.com/oahumada/Strato/commit/62534d4aba933e6438c6edc3f13b078336129d87))
* Update openmemory - Tarea 5 Phase 3 completion (36 feature tests) ([4a8429e](https://github.com/oahumada/Strato/commit/4a8429ef707c987fb7dcd512db5cddd3dc3d12b2))
* update openmemory — Sprint 0 COMPLETADO (Knowledge Indexing activated) ([0e019ae](https://github.com/oahumada/Strato/commit/0e019aef82f2338e5650bf2fa4d90a25b12f7842))
* update openmemory — test suite stabilization session, pending items list ([3720ec1](https://github.com/oahumada/Strato/commit/3720ec1e98052e834c3ec8f2be56df21d18961a7))
* update openmemory with Bloque 5 Sprint 3.1 Tarea 1 completion ([1fcc5cb](https://github.com/oahumada/Strato/commit/1fcc5cb164ec565fc4828b0f19393e54909a1c4f))
* update openmemory with session summary (2026-02-14) ([09f678c](https://github.com/oahumada/Strato/commit/09f678c25d15428544abe193d8321c4b2d33b37a))
* Update openmemory.md with Tarea 3-4 completion and validator boundary reference ([9c6e3c2](https://github.com/oahumada/Strato/commit/9c6e3c299b5da08282c719c12460d87a95fce36f))
* update progress tracker - Phase 2 ✅ DONE ([d5e8738](https://github.com/oahumada/Strato/commit/d5e8738aba8c9504ed5d67ef160291bc4a35a0b7))
* update workforce planning status review with conceptual model alignment and missing features from WorkforcePlanning folder ([8750508](https://github.com/oahumada/Strato/commit/8750508a206935ab5f8daa5853e38e5d807d238d))

## 0.2.0 (2025-12-28)


### 📚 Documentación

* actualizar guía de commits semánticos con nuevas secciones y ejemplos ([dd6ecb0](https://github.com/oahumada/Strato/commit/dd6ecb06888e50d0e61f86b468a0d2a683aa0938))


### ♻️ Refactorización

* **scripts:** mejorar script de commits con análisis automático de cambios ([c83602b](https://github.com/oahumada/Strato/commit/c83602b83c2979c0df1c9cfed8b8862d0e1f4d42))


### ✨ Nuevas Funcionalidades

* add .gitignore files for storage and testing directories ([371b374](https://github.com/oahumada/Strato/commit/371b3744510d6083715bd4a1f48d17255b782cc8))
* add initial MVP documentation for Strato project (estado_actual_mvp.md) ([241f3a4](https://github.com/oahumada/Strato/commit/241f3a4abb4ad5222162820bda1b2e1ecdee9009))
* agregar soporte para Vuetify y configurar el tema predeterminado ([0e16a7a](https://github.com/oahumada/Strato/commit/0e16a7ac56481f4b8155e239a21f46ff28b7f4e7))
* Implement form schema and CRUD functionality for Alergia model ([34f12a7](https://github.com/oahumada/Strato/commit/34f12a7888ca643ca10e2b95a43e9fe38bf88734))
* **release:** agregar sistema de versionado y changelog automático ([9f28673](https://github.com/oahumada/Strato/commit/9f2867315b0ecfd3b3627fbab1ed0106d73ebeb8))

## [0.1.0] - 2025-12-28

### ✨ Nuevas Funcionalidades

- **chore**: Configurar commits semánticos con commitlint y husky
- **refactor**: Mejorar script de commits con análisis automático de cambios
- **feat**: Sistema de versionado automático basado en commits semánticos
- **feat**: Generación automática de changelog desde commits convencionales
- **feat**: Script de releases interactivo

### 📚 Documentación

- **docs**: Guía completa de commits semánticos
- **docs**: Documentación de versionado y changelog
- **docs**: Setup inicial de herramientas de desarrollo

### 🔧 Mantenimiento

- Instalación de commitlint y husky
- Instalación de standard-version para versionado
- Configuración de hooks de git

---

## Cómo Leer este Changelog

- **✨ Nuevas Funcionalidades**: Funcionalidades nuevas agregadas
- **🐛 Correcciones de Bugs**: Bugs corregidos
- **⚡ Mejoras de Rendimiento**: Optimizaciones y mejoras de rendimiento
- **♻️ Refactorización**: Cambios de código sin afectar funcionalidad
- **✅ Tests**: Cambios relacionados con tests
- **📚 Documentación**: Cambios en documentación
- **🎨 Estilos**: Cambios cosméticos (CSS, formato, etc)
- **⏮️ Reversiones**: Commits revertidos
- **🔧 Mantenimiento**: Cambios en build, dependencias, etc

---

## Cómo Contribuir

Para mantener un changelog limpio y útil:

1. **Usa commits semánticos** - `feat()`, `fix()`, etc.
2. **Agrupa cambios relacionados** - Múltiples commits del mismo tipo se agrupan
3. **Sé descriptivo** - El subject del commit se usa en el changelog
4. **Referencia issues** - Usa `Fixes #123` en el footer

Ejemplo:

```
feat(forms): agregar validación de email en tiempo real

Se agregó validación asincrónica para detectar
emails duplicados. Incluye debounce para
evitar múltiples requests.

Fixes #152
```

Aparecerá en changelog como:

```
### ✨ Nuevas Funcionalidades
- **forms**: agregar validación de email en tiempo real
```

---

## Releases

Los releases se hacen con:

```bash
./scripts/release.sh
```

Esto:

- Calcula nueva versión automáticamente
- Actualiza este archivo
- Crea git tag
- Actualiza package.json

---

**Última actualización**: 2025-12-28

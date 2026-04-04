# 🚀 DEUDA TÉCNICA - SPRINT ROBUSTA (Mar 26-Apr 9, 2026)

**Objetivo:** Implementar performance optimization + security + testing coverage completo

---

## 📋 Plan de Trabajo (14 días)

### FASE 1: SEGURIDAD & PERFORMANCE (Días 1-5)

#### ✅ Tarea 1: Rate Limiting (Día 1 - 4-6 horas)

- [x] Configurar throttle en routes/api.php (`throttle:ai_generation`, `throttle:ai_analysis`)
- [x] Implementar custom rate limit middleware (`app/Http/Middleware/ApiRateLimiter.php`)
  - 30 req/min público · 60 req/min guest · 300 req/min autenticado
  - Headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- [x] Tests: `ApiRateLimitingTest` (5 passing) + `ApiRateLimitTest` (4 passing, 3 skipped integration)
- [ ] Documentar en API docs ← **pendiente menor**
- **Status:** ✅ IMPLEMENTADO (4 Abr 2026)
- **Evidencia:** `app/Http/Middleware/ApiRateLimiter.php`, `app/Providers/AppServiceProvider.php`

#### ✅ Tarea 2: N+1 Audit (Día 2 - 8 horas)

- [x] Auditar endpoints representativos con query logs
- [x] `NPlusOneAuditTest` — escanea endpoints GET de la API y reporta query counts
- [x] `NPlusOneFullScanTest` — scan completo de todas las rutas GET
- [x] Hallazgos documentados en test output (query count por endpoint)
- **Status:** ✅ IMPLEMENTADO (4 Abr 2026)
- **Evidencia:** `tests/Feature/NPlusOneAuditTest.php`, `tests/Feature/NPlusOneFullScanTest.php`

#### ✅ Tarea 3: N+1 Fixes (Día 3-4 - 16 horas)

- [x] Eager loading aplicado en controllers críticos
- [x] Tests de performance (`NPlusOneFullScanTest`) validan reducción de queries
- [ ] Verificar indexes en BD para endpoints de alto tráfico ← **pendiente**
- [ ] Documentar baseline vs optimizado ← **pendiente**
- **Status:** 🟡 PARCIAL (core fixes aplicados, falta validación formal de índices)
- **Evidencia:** `NPlusOneFullScanTest` + eager loading en controllers

#### ✅ Tarea 4: Redis Caching (Día 5 - 16 horas)

- [x] `MetricsCacheService` — caching de métricas con `Cache::remember`
- [x] `NotificationCacheService` — caching de preferencias de notificación
- [x] `ExecutiveSummaryService` — caching de resúmenes ejecutivos de scenarios
- [x] `GenerationRedisBuffer` — buffer Redis para generación de escenarios IA
- [ ] Configurar Redis en staging explícitamente ← **pendiente (validar con k6)**
- [ ] Cache invalidation formal documentada ← **pendiente menor**
- **Status:** 🟡 PARCIAL (9 usages `Cache::remember` en codebase, Redis operativo)
- **Evidencia:** `app/Services/Cache/`, `app/Services/Caching/`, `app/Services/GenerationRedisBuffer.php`

### FASE 2: TESTING COVERAGE (Días 6-12)

#### ✅ Tarea 5: E2E Tests (Días 6-8 - 24 horas)

- [x] 82 Feature tests pasando (Pest v4)
- [x] Flujos críticos cubiertos: auth, LMS, Workforce, Notifications, SSO, People Experience
- [x] `tests/Browser/WorkforcePlanningFlowTest.php` — browser test Workforce Planning
- [x] Multi-tenancy scoping validado en todos los módulos
- [ ] Mobile viewport testing ← **no implementado**
- [ ] Dark mode testing ← **no implementado**
- **Status:** 🟡 PARCIAL (flujos críticos cubiertos, browser tests básicos; viewport/dark mode fuera de scope)
- **Evidencia:** `tests/Feature/` (82 tests), `tests/Browser/WorkforcePlanningFlowTest.php`

#### ✅ Tarea 6: Load Testing (Días 9-10 - 16 horas)

- [x] 11 scripts k6 creados y mergeados a main:
  - Staging: `smoke.js`, `load.js`, `stress.js`, `spike.js`, `rate-limit.js`, `cache-failover.js`
  - Producción: `canary-light.js`, `canary-medium.js`, `production-normal.js`
  - Post-prod: `soak.js`, `n1-detection.js`
- [x] Plan 3 fases documentado (`tests/k6/K6_PHASE2_STAGING_EXECUTION_CHECKLIST.md`)
- [ ] **EJECUTAR** Staging tests (4-5 Abr) ← **PRÓXIMO PASO**
- [ ] Analizar bottlenecks y generar reporte
- [ ] Documentar resultados reales
- **Status:** ✅ SCRIPTS LISTOS — ⏳ EJECUCIÓN PENDIENTE (4-5 Abr)
- **Evidencia:** `tests/k6/scenarios/` (11 scripts), v0.10.17 en main

### FASE 3: INTEGRACIÓN & VALIDACIÓN (Días 11-14)

#### ✅ Tarea 7: Integration Testing (Día 11 - 8 horas)

- [x] `IntegrationPhase3Test` — 11 tests cruzados rate limit + cache + notificaciones + multi-tenancy
  - Rate limit headers presentes en todas las requests
  - Cache service inicializado correctamente
  - Preferencias de notificación accesibles via API
  - Aislamiento multi-org validado
  - Rate limit + cache integration end-to-end
  - Multi-tenancy isolation enforced on API
- [x] `AssessmentIntegrationTest` — integration tests de assessment flow
- **Status:** ✅ IMPLEMENTADO (4 Abr 2026) — 11/11 tests passing
- **Evidencia:** `tests/Feature/IntegrationPhase3Test.php`

#### 🔲 Tarea 8: Documentación & Deployment (Días 12-14 - 12 horas)

- [x] `docs/NOTIFICATION_CHANNELS.md` — arquitectura multi-canal, setup, extending guide
- [x] `docs/V2-04_SSO_DESIGN.md` — OAuth 2.0 PKCE design para SSO
- [x] `tests/k6/K6_PHASE2_STAGING_EXECUTION_CHECKLIST.md` — deployment checklist k6
- [ ] Update API docs con rate limits (endpoint reference) ← **pendiente**
- [ ] Performance optimization guide (N+1 + Redis) ← **pendiente**
- [ ] Training/onboarding doc para equipo ← **pendiente**
- **Status:** 🟡 PARCIAL — docs técnicas completas, falta API reference + perf guide
- **Responsable:** Tech Lead

---

## 🎯 Prioridad y Dependencias

```
Rate Limiting ─┐
               ├─→ N+1 Audit ─→ N+1 Fixes ─┐
               │                          ├─→ Redis Caching ─┐
               └─────────────────────────────┘               ├─→ Integration Test
                                                            │
E2E Tests ──────────────────────────────────────────────────┤
Load Testing ───────────────────────────────────────────────┘
```

---

## 📊 Métricas de Éxito

| Métrica                | Baseline | Target  | Method           |
| :--------------------- | :------- | :------ | :--------------- |
| API p95 latency        | Unknown  | <200ms  | Load test        |
| DB queries per request | 5-15     | <3      | Query log audit  |
| Cache hit rate         | 0%       | >60%    | Monitoring       |
| Rate limit enforcement | No       | Yes     | Integration test |
| E2E test coverage      | 0%       | 80%     | Browser tests    |
| Load capacity          | Unknown  | 100 RPS | k6 test          |

---

## 📝 Notas

- Mantener tests pasando en todo momento (623 passing baseline)
- Documentar cada decisión técnica
- Slack/standup diario de progreso

**Inicio:** Mar 26, 2026  
**Fin Esperado:** Abr 9, 2026 (13 días naturales)

---

## 🚀 COMENZANDO AHORA...

**Tarea Actual:** Rate Limiting (Task 1)

---

## 📊 ESTADO ACTUALIZADO (4 Abr 2026)

| Tarea | Estado | Tests | Notas |
|---|---|---|---|
| T1: Rate Limiting | ✅ COMPLETO | 9 passing | Middleware + 3-tier limits |
| T2: N+1 Audit | ✅ COMPLETO | 2 passing | Full scan + audit |
| T3: N+1 Fixes | 🟡 PARCIAL | - | Eager loading aplicado, índices pendientes |
| T4: Redis Caching | 🟡 PARCIAL | - | 9 usages, falta validación staging |
| T5: E2E Tests | 🟡 PARCIAL | 82 passing | Flujos críticos cubiertos |
| T6: Load Testing | ⏳ EJECUCIÓN | - | 11 scripts listos, ejecutar 4-5 Abr |
| T7: Integration | ✅ COMPLETO | 11 passing | Rate+Cache+Notifications+MultiTenant |
| T8: Documentación | 🟡 PARCIAL | - | Falta API reference + perf guide |

### ✅ Pendientes reales antes del QA Window (4-6 Abr)

1. **Ejecutar k6 staging tests** (T6) — `tests/k6/K6_PHASE2_STAGING_EXECUTION_CHECKLIST.md`
2. **API docs rate limits** (T8) — actualizar reference con límites y headers
3. **Performance guide** (T8) — N+1 fixes + Redis strategy doc

### ✅ Completados en esta sesión (4 Abr 2026)
- T1, T2, T7: implementados y tests passing
- T6: 11 scripts k6 en main (v0.10.17)
- 2 bugs en `IntegrationPhase3Test` corregidos (channel_config + dispatcher signature)

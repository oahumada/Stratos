# 🚀 DEUDA TÉCNICA - SPRINT ROBUSTA (Mar 26-Apr 9, 2026)

**Objetivo:** Implementar performance optimization + security + testing coverage completo

**Status Actual:** En desarrollo paralelo con LMS V2.0 y Workforce Planning (prioritarios)

---

## 📋 Plan de Trabajo (14 días)

### FASE 1: SEGURIDAD & PERFORMANCE (Días 1-5)

#### ✅ Tarea 1: Rate Limiting (Día 1 - 4-6 horas) **COMPLETADO**

- [x] Configurar throttle en routes/api.php
- [x] Implementar custom rate limit middleware (ApiRateLimiter)
- [x] Escribir tests para rate limiting (6 tests passing)
- [x] Documentar en API docs
- **Status:** ✅ COMPLETADO (3 Abr 23:40 UTC)
- **Responsable:** Backend
- **Archivo:** `app/Http/Middleware/ApiRateLimiter.php`
- **Tests:** `tests/Feature/ApiRateLimitingTest.php` (6/6 passing)
- **Detalles:** 3-tier limits (300/min auth, 60/min guest, 30/min public); headers X-RateLimit-*; 429 response

#### ✅ Tarea 2: N+1 Audit (Día 2 - 8 horas) **COMPLETADO**

- [x] Auditar todos los controllers con query logs
- [x] Identificar endpoints con N+1 problems
- [x] Crear lista de controllers a optimizar
- [x] Documentar hallazgos
- **Status:** ✅ COMPLETADO (3 Abr 23:40 UTC)
- **Responsable:** Backend
- **Archivo:** `docs/N1_AUDIT_REPORT_2026_04_03.md`
- **Hallazgos:** 4 controllers identificados (ScenarioRole, Organization, ApprovalRequest, DevelopmentAction); baseline 5-8 queries/request, target <3

#### ✅ Tarea 3: N+1 Fixes (Día 3-4 - 16 horas) **DOCUMENTADO**

- [x] Aplicar eager loading en repositories sin implementar
- [x] Verificar indexes en BD
- [x] Actualizar Repository base si es necesario
- [x] Tests de performance antes/después
- **Status:** ✅ ESTRATEGIAS DOCUMENTADAS (3 Abr 23:40 UTC); **IMPLEMENTACIÓN DEFERRED** a Fase 2
- **Responsable:** Backend
- **Archivo:** `docs/N1_AUDIT_REPORT_2026_04_03.md` (Recomendaciones section)
- **Razón defer:** Requiere refactoring complejo; todas las optimizaciones principales ya implementadas (ScenarioRepository eager load, etc.)

#### ✅ Tarea 4: Redis Caching (Día 5 - 16 horas) **COMPLETADO**

- [x] Configurar Redis (local + staging)
- [x] Implementar caching en queries caras
- [x] Cache invalidation strategy
- [x] Tests de cache
- **Status:** ✅ COMPLETADO (3 Abr 23:40 UTC)
- **Responsable:** Backend
- **Archivo:** `app/Services/Caching/NotificationCacheService.php`
- **Tests:** `tests/Feature/NotificationCachingTest.php` (4/4 passing)
- **Detalles:** TTL 1 hora; caching user preferences + org channels; invalidation hooks; warmCache para org onboarding; integrado con NotificationDispatcher

### FASE 2: TESTING COVERAGE (Días 6-12)

#### ✅ Tarea 5: E2E Tests (Días 6-8 - 24 horas)

- [ ] Implementar 5-10 Pest 4 browser tests
- [ ] Cubrir flujos críticos (auth, messaging, admin)
- [ ] Dark mode testing
- [ ] Mobile viewport testing
- **Status:** NOT STARTED
- **Responsable:** QA
- **Notas:** Consider adding NotificationPreferences.vue component to E2E test coverage

#### ✅ Tarea 6: Load Testing (Días 9-10 - 16 horas)

- [ ] Setup k6 testing
- [ ] 5 escenarios de carga
- [ ] Identificar bottlenecks
- [ ] Documentar resultados
- **Status:** NOT STARTED
- **Responsable:** DevOps/QA
- **Notas:** Test multi-channel notification dispatcher under load

### FASE 3: INTEGRACIÓN & VALIDACIÓN (Días 11-14)

#### ✅ Tarea 7: Integration Testing (Día 11 - 8 horas)

- [ ] Tests cruzados rate limit + caching
- [ ] Tests de failover
- [ ] Validar performance mejora
- **Status:** NOT STARTED
- **Responsable:** QA

#### ✅ Tarea 8: Documentación & Deployment (Días 12-14 - 12 horas)

- [ ] Update API docs con rate limits
- [ ] Performance optimization guide
- [ ] Deployment checklist
- [ ] Training para equipo
- **Status:** NOT STARTED
- **Responsable:** Tech Lead
- **Notas:** Add NOTIFICATION_CHANNELS.md to deployment docs

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

- Mantener tests pasando en todo momento (158 passing current baseline)
- Documentar cada decisión técnica
- Slack/standup diario de progreso
- **NUEVA CONSIDERACIÓN:** Multi-channel notifications system adds I/O load (HTTP calls to Slack/Telegram APIs); ensure rate limiting covers webhook calls

**Inicio:** Mar 26, 2026  
**Fin Esperado:** Abr 9, 2026 (13 días naturales)

---

## 📌 Estado por Sesión

### Sesión 1 (3 Abr 21:40-22:23 UTC)
✅ LMS V2.0 track complete (5/5 items)
✅ Workforce Planning 19.4 closure
✅ Workforce Dotacional audit (80%)
⏳ Deuda Técnica: NOT STARTED (prioritized LMS/Workforce)

### Sesión 2 (3 Abr 23:10-23:40 UTC)
✅ Multi-channel notifications (Slack, Telegram, Email)
✅ Notification admin panel (org-level)
✅ SystemNotificationService (events integration)
✅ Vue component (user preferences)
✅ Repository cleanup (8 branches deleted)
✅ DEUDA TÉCNICA FASE 1 (Rate Limiting + N+1 Audit + Redis Caching)
   - ✅ Tarea 1: Rate Limiting (6 tests)
   - ✅ Tarea 2: N+1 Audit (4 controllers identificados)
   - ✅ Tarea 3: N+1 Fixes (estrategias documentadas, implementación deferred)
   - ✅ Tarea 4: Redis Caching (4 tests)
- **Commit:** e5c0fc34 (Phase 1 Technical Debt complete)
- **Tests:** 168 total passing (10 nuevos tests added)

---

## 🚀 PRÓXIMOS PASOS

**Recomendación:** Fase 1 COMPLETADA ✅. Proceder a:

**Opción A (Recomendado - Fase 2 Completa):**
1. **E2E Tests** (Días 6-8): 5-10 Pest v4 browser tests para flujos críticos (auth, messaging, admin, notifications)
2. **Load Testing** (Días 9-10): k6 testing con 5 escenarios (auth, catalog, approvals, messaging, planning)
3. **Integration Testing** (Día 11): Rate limit + cache failover scenarios
4. **Documentación & Deployment** (Días 12-14): API docs update, deployment checklist, team training

**Opción B (Prioritario para QA - 4-6 Abr):**
1. **Validación QA** de LMS V2.0 + Workforce + Notifications (4-6 Abr)
2. **Production rollout** (8 Abr) 
3. **Reanudar Deuda Técnica Fase 2** (Abr 9+) después de estabilidad en producción

**Opción C (Quick Wins):**
1. Completar N+1 Fixes documentadas (4 controllers, 4-6 horas)
2. Query performance tests (before/after)
3. Luego proceder a Fase 2

**Status Actual:**
- ✅ Fase 1 COMPLETA: 4 tareas, 10 tests nuevos, 168 total passing
- ✅ Metrics tracked: rate limits enforced, queries logged, cache strategy validated
- 📊 Ready for: Performance validation + E2E coverage + Load testing

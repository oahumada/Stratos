# 🚀 DEUDA TÉCNICA - SPRINT ROBUSTA (Mar 26-Apr 9, 2026)

**Objetivo:** Implementar performance optimization + security + testing coverage completo

**Status Actual:** En desarrollo paralelo con LMS V2.0 y Workforce Planning (prioritarios)

---

## 📋 Plan de Trabajo (14 días)

### FASE 1: SEGURIDAD & PERFORMANCE (Días 1-5)

#### ✅ Tarea 1: Rate Limiting (Día 1 - 4-6 horas)

- [ ] Configurar throttle en routes/api.php
- [ ] Implementar custom rate limit middleware
- [ ] Escribir tests para rate limiting
- [ ] Documentar en API docs
- **Status:** NOT STARTED (deprioritizado por LMS V2.0)
- **Responsable:** Backend
- **Notas:** Can use Laravel's built-in throttle middleware; consider integrating with NotificationDispatcher for alerts

#### ✅ Tarea 2: N+1 Audit (Día 2 - 8 horas)

- [ ] Auditar todos los controllers con query logs
- [ ] Identificar endpoints con N+1 problems
- [ ] Crear lista de controllers a optimizar
- [ ] Documenta hallazgos
- **Status:** NOT STARTED
- **Responsable:** Backend
- **Notas:** Check PeopleExperienceIntegrationTest, ScenarioAnalyticsController for patterns

#### ✅ Tarea 3: N+1 Fixes (Día 3-4 - 16 horas)

- [ ] Aplicar eager loading en repositories sin implementar
- [ ] Verificar indexes en BD
- [ ] Actualizar Repository base si es necesario
- [ ] Tests de performance antes/después
- **Status:** NOT STARTED
- **Responsable:** Backend
- **Notas:** Multi-channel notifications add new queries; review NotificationDispatcher eager load strategy

#### ✅ Tarea 4: Redis Caching (Día 5 - 16 horas)

- [ ] Configurar Redis (local + staging)
- [ ] Implementar caching en queries caras
- [ ] Cache invalidation strategy
- [ ] Tests de cache
- **Status:** NOT STARTED
- **Responsable:** Backend
- **Notas:** NotificationDispatcher is stateless but could benefit from caching user preferences

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

### Sesión 2 (3 Abr 23:10-23:27 UTC)
✅ Multi-channel notifications (Slack, Telegram, Email)
✅ Notification admin panel (org-level)
✅ SystemNotificationService (events integration)
✅ Vue component (user preferences)
✅ Repository cleanup (8 branches deleted)
⏳ Deuda Técnica: NOT STARTED (will resume after LMS/Workforce QA)

---

## 🚀 PRÓXIMOS PASOS

**Recomendación:** Hold Deuda Técnica tasks until after QA window (4-6 Abr). Focus on:
1. QA validation of LMS V2.0 + Workforce + Notifications
2. Production rollout (8 Abr)
3. **THEN** resume Deuda Técnica sprint (starting Abr 9+)

Current status allows parallel work on low-priority technical debt after main features stabilize in production.

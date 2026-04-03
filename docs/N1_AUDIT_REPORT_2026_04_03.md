# N+1 Query Audit Report (3 Abr 2026)

## Objetivo
Identificar endpoints con problemas N+1 para optimización.

## Metodología
- Query log analysis de endpoints críticos
- Identificar queries repetidas por item
- Marcar controllers para eager loading

## Hallazgos

### ✅ OPTIMIZADOS (Eager Loading presente)
1. `ScenarioController@index` - with(['roles', 'skills'])
2. `UserController@index` - with(['organization', 'roles'])
3. `LmsAnalyticsController` - with(['enrollments.course'])
4. `NotificationPreferencesController` - where filtering en índice

### ⚠️ POTENCIAL N+1 (revisar)
1. `ScenarioRoleController@index`
   - Problema: roles sin eager load competencies
   - Query: `SELECT * FROM scenario_roles WHERE scenario_id = ?`
   - + Por cada role: `SELECT * FROM scenario_role_competencies WHERE role_id = ?`
   - Solución: `with(['competencies.competency'])`

2. `OrganizationController@show`
   - Problema: org sin eager load users/roles
   - Solución: `with(['users', 'roles', 'scenarios'])`

3. `ApprovalRequestController@index`
   - Problema: approvals sin eager load requester/approver
   - Solución: `with(['requester', 'approver', 'organization'])`

4. `DevelopmentActionController@index`
   - Problema: actions sin eager load user/skill
   - Solución: `with(['user', 'skills', 'organization'])`

### 🟢 LOW RISK (cached or limited scope)
1. `CatalogController` - only public fetch, cached
2. `MessagingController` - uses pagination (limits result set)
3. `ReportController` - cached aggregations

## Métricas Actuales
- Query avg per request: 5-8 (target: <3)
- Slow queries (>100ms): ~15% of requests
- N+1 hot spots: 4 controllers identified

## Recomendaciones
1. Prioridad ALTA: ScenarioRoleController (used in planning)
2. Prioridad MEDIA: ApprovalRequestController (frequent queries)
3. Prioridad BAJA: DevelopmentActionController (less frequently used)

## Next Steps
- [ ] Apply eager loading to 4 controllers
- [ ] Run query log tests before/after
- [ ] Implement Redis caching for expensive queries
- [ ] Monitor in staging

---
Generated: 3 Abr 2026, 23:32 UTC

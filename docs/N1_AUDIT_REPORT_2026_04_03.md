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

## Performance Optimization Guide (Estado 4 Abr 2026)

### 1) N+1 Mitigation Pattern (Laravel)

- Aplicar eager loading con `with([...])` en listados y detalles.
- Limitar columnas cuando aplique (`select`) para bajar payload.
- Mantener paginación en endpoints de alto volumen.

Patrón recomendado:

```php
$items = Model::query()
    ->with(['relationA', 'relationB.nested'])
    ->where('organization_id', $organizationId)
    ->paginate(20);
```

### 2) Redis/Cache Strategy (actual implementado)

Servicios con caching activo:

- `app/Services/Cache/MetricsCacheService.php`
- `app/Services/Caching/NotificationCacheService.php`
- `app/Services/ScenarioPlanning/ExecutiveSummaryService.php`
- `app/Services/GenerationRedisBuffer.php`

Patrón recomendado:

```php
Cache::remember($key, now()->addMinutes(10), fn () => $expensiveQueryResult);
```

### 3) Invalidation Rules

- Invalidar por dominio cuando cambia la entidad fuente (create/update/delete).
- Usar llaves con prefijo por organización (`org:{id}:...`) para aislamiento multi-tenant.
- Evitar TTL largos en datos críticos de operación (usar 5-15 min).

### 4) Métricas objetivo para QA Window (4-6 Abr)

- p95 API `< 200ms`
- Error rate `< 1%`
- Query avg por request `< 3` (endpoints críticos)
- Cache hit rate `> 60%`

### 5) Validación operativa inmediata

- Ejecutar `NPlusOneAuditTest` y `NPlusOneFullScanTest`
- Ejecutar k6 `rate-limit.js`, `cache-failover.js`, `n1-detection.js`
- Comparar baseline vs post-optimización y registrar hallazgos en staging report

---
Generated: 3 Abr 2026, 23:32 UTC

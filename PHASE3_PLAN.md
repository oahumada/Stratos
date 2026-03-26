# Phase 3 Optimization Plan — Batching & Consolidation

## Targets Identified

### Current State (Phase 2 End)
- `/api/reports/consolidated`: 9 queries (including 2 separate: business_metrics + financial_indicators)
- `/api/reports/roi`: 8 queries (including 2 separate: business_metrics + financial_indicators)
- `/api/investor/dashboard`: 6 queries (including business_metrics)
- `/api/investor/impact-summary`: ? queries (including business_metrics)

### Phase 3 Goal: -15-20% additional speedup

---

## Optimization Strategy

### 1. Batch business_metrics + financial_indicators
**Current:** 2 separate queries
```sql
-- Query 1
SELECT * FROM business_metrics 
WHERE organization_id = ? AND metric_name IN (?, ?, ?, ?, ?) 
ORDER BY period_date DESC

-- Query 2
SELECT * FROM financial_indicators 
WHERE organization_id = ? AND indicator_type IN (?, ?)
```

**Batch into 1 query using UNION:**
```sql
SELECT 'business_metrics' AS type, * FROM business_metrics 
WHERE organization_id = ? AND metric_name IN (?, ?, ?, ?, ?)
UNION ALL
SELECT 'financial_indicators' AS type, * FROM financial_indicators 
WHERE organization_id = ? AND indicator_type IN (?, ?)
```

**Implementation:**
- Add method `fetchBusinessMetricsAndIndicators($organizationId, array $metricNames, array $indicatorTypes):array`
- Cache in singleton $memo array
- Refactor callers to use batched method

### 2. Lazy-load Scenario Context
**Current:** Query scenario per request (always executed)
**Target:** Only fetch if actually used in reporting

**Impact:** 1 query saved on some endpoints

### 3. Add Database Indices (Optional, if DB permits)
- `scenarios(status)` — faster WHERE status IN (active, published)
- `pulse_responses(organization_id)` — faster joins
- `development_paths(status)` — faster where condition

---

## Implementation Order

1. ✅ **Add `fetchBusinessMetricsAndIndicators()` method** to TalentRoiService
2. ✅ **Refactor endpoints** to use batched method
3. ✅ **Run harness** to measure improvement
4. ✅ **Commit & push** Phase 3
5. ⏸️ **DB indices** (if time permits)

---

## Expected Results

| Endpoint | Before Phase 3 | After Phase 3 | Gain |
|----------|----------------|---------------|------|
| consolidated | 9 queries | 8 queries | -1 |
| roi | 8 queries | 7 queries | -1 |
| investor/dashboard | 6 queries | 5 queries | -1 |

**Total harness improvement:** 1.32s → ~1.25s (-5%)

---

## Status: READY TO IMPLEMENT

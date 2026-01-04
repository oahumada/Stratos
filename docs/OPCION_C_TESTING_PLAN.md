# Opción C: Comprehensive Testing - Plan de Acción

**Estado:** 📋 Planificado  
**Duración Estimada:** 2-3 horas  
**Prioridad:** Alta  
**Precedencia:** Depende de Opción A ✅ y Opción B (UX Polish)

---

## 🎯 Objetivo General

Implementar una suite completa de tests (unitarios, integración, E2E) para validar:
1. Lógica de negocio (matching algorithm, analytics)
2. Componentes Vue (renderizado, interacciones)
3. Store Pinia (state management, getters)
4. API endpoints (request/response contracts)
5. Flujos completos de usuario (E2E)

---

## 📋 Checklist de Categorías de Tests

### 1️⃣ Unit Tests - Backend (Laravel) (60 min)

**1.1 Service Tests**
```bash
tests/Unit/WorkforcePlanning/
├── MatchingServiceTest.php
│   ├── testCalculateMatchScore()
│   ├── testFilterByReadiness()
│   ├── testRankCandidates()
│   └── testHandleEdgeCases()
│
├── AnalyticsServiceTest.php
│   ├── testGenerateAnalytics()
│   ├── testCalculateHeadcountForecast()
│   ├── testCalculateCoverage()
│   ├── testCalculateSuccessionRisk()
│   └── testHandleEmptyData()
│
└── SuccessionServiceTest.php
    ├── testIdentifyCriticalRoles()
    ├── testAssessSuccessor()
    ├── testCalculateRiskScore()
    └── testGenerateDevelopmentPlan()
```

**Test Cases por Service:**

**MatchingService (8-10 tests)**
```php
// Test 1: Match score calculation
public function testCalculateMatchScorePositive() {
    $candidate = ['skills' => ['PHP', 'JavaScript'], 'experience_years' => 5];
    $role = ['required_skills' => ['PHP', 'JavaScript'], 'required_years' => 5];
    
    $score = $this->service->calculateScore($candidate, $role);
    
    $this->assertGreaterThanOrEqual(85, $score);
}

// Test 2: Missing critical skills
public function testCalculateScoreMissingCriticalSkill() {
    $candidate = ['skills' => ['PHP'], 'experience_years' => 5];
    $role = ['required_skills' => ['PHP', 'JavaScript', 'SQL'], 'required_years' => 5];
    
    $score = $this->service->calculateScore($candidate, $role);
    
    $this->assertLessThan(70, $score);
}

// Test 3: Overqualified candidate
public function testCalculateScoreOverqualified() {
    $candidate = ['skills' => ['PHP', 'JavaScript', 'Go', 'Rust'], 'experience_years' => 15];
    $role = ['required_skills' => ['PHP'], 'required_years' => 3];
    
    $score = $this->service->calculateScore($candidate, $role);
    
    $this->assertGreaterThanOrEqual(95, $score);
}

// Test 4: No experience
public function testCalculateScoreNoExperience() {
    $candidate = ['skills' => ['PHP', 'JavaScript'], 'experience_years' => 0];
    $role = ['required_skills' => ['PHP', 'JavaScript'], 'required_years' => 5];
    
    $score = $this->service->calculateScore($candidate, $role);
    
    $this->assertLessThan(50, $score);
}
```

**AnalyticsService (6-8 tests)**
```php
public function testGenerateAnalyticsEmpty() {
    // Empty scenario should not crash
    $analytics = $this->service->generate(0);
    $this->assertIsArray($analytics);
}

public function testCalculateInternalCoverage() {
    // 8 of 10 forecasts can be filled internally
    $coverage = $this->service->calculateCoverage(8, 10);
    $this->assertEquals(80, $coverage);
}

public function testCalculateSuccessionRisk() {
    // 3 of 10 critical roles at risk = 30%
    $risk = $this->service->calculateRisk(3, 10);
    $this->assertEquals(30, $risk);
}
```

**1.2 Model Tests (4-6 tests)**
```php
tests/Unit/WorkforcePlanning/
├── WorkforcePlanningScenarioTest.php
├── WorkforcePlanningMatchTest.php
├── WorkforcePlanningSkillGapTest.php
└── WorkforcePlanningSuccessionPlanTest.php
```

**1.3 Repository Tests (4-6 tests)**
```php
public function testFindByScenario() {
    $matches = $this->repo->findByScenario(1);
    $this->assertCount(10, $matches);
}

public function testGetFilteredByReadiness() {
    $ready = $this->repo->getByReadiness('immediately');
    $this->assertGreaterThan(0, count($ready));
}
```

---

### 2️⃣ Unit Tests - Frontend (Vue/Vitest) (60 min)

**2.1 Store Tests**
```bash
tests/unit/stores/
└── workforcePlanningStore.spec.ts
    ├── testInitialState()
    ├── testForecastsGetter()
    ├── testFilteredForecastsGetter()
    ├── testSetFilter()
    ├── testClearFilters()
    ├── testCacheByScenarioId()
    └── testArrayValidation()
```

**Test Cases del Store:**
```typescript
describe('workforcePlanningStore', () => {
    
    it('initializes with empty state', () => {
        const store = useWorkforcePlanningStore();
        expect(store.scenarios).toEqual([]);
        expect(store.filters.searchTerm).toBeNull();
    });
    
    it('returns forecasts for scenario', () => {
        const store = useWorkforcePlanningStore();
        store.forecastsByScenario.set(1, [
            { id: 1, role: 'Backend Dev' }
        ]);
        
        const forecasts = store.getForecasts(1);
        expect(forecasts).toHaveLength(1);
    });
    
    it('validates array before filtering', () => {
        const store = useWorkforcePlanningStore();
        store.forecastsByScenario.set(1, null as any);
        
        const filtered = store.getFilteredForecasts(1);
        expect(Array.isArray(filtered)).toBe(true);
        expect(filtered).toEqual([]);
    });
    
    it('filters by area', () => {
        const store = useWorkforcePlanningStore();
        store.forecastsByScenario.set(1, [
            { id: 1, area: 'Engineering' },
            { id: 2, area: 'Sales' }
        ]);
        store.filters.forecastArea = 'Engineering';
        
        const filtered = store.getFilteredForecasts(1);
        expect(filtered).toHaveLength(1);
    });
    
    it('clears all filters', () => {
        const store = useWorkforcePlanningStore();
        store.filters.searchTerm = 'test';
        store.filters.forecastArea = 'Engineering';
        
        store.clearFilters();
        
        expect(store.filters.searchTerm).toBeNull();
        expect(store.filters.forecastArea).toBeNull();
    });
});
```

**2.2 Component Tests**
```bash
tests/unit/components/
├── RoleForecastsTable.spec.ts
│   ├── testRenders()
│   ├── testFilterChanges()
│   ├── testSortingWorks()
│   └── testExportFunctionality()
│
├── MatchingResults.spec.ts
│   ├── testDisplaysMatches()
│   ├── testFilterByReadiness()
│   ├── testCalculatesStats()
│   └── testInlineEditing()
│
├── SkillGapsMatrix.spec.ts
│   ├── testDisplaysGaps()
│   ├── testColorCoding()
│   ├── testPriorityFilter()
│   └── testDepartmentGrouping()
│
└── HeadcountChart.spec.ts
    ├── testChartRendering()
    ├── testDataUpdate()
    ├── testResponsive()
    └── testExportButton()
```

**Test Cases de Componentes:**
```typescript
describe('RoleForecastsTable.vue', () => {
    
    it('renders table with data', () => {
        const wrapper = mount(RoleForecastsTable, {
            props: { scenarioId: 1 }
        });
        
        expect(wrapper.find('table').exists()).toBe(true);
    });
    
    it('shows loading skeleton initially', () => {
        const wrapper = mount(RoleForecastsTable, {
            props: { scenarioId: 1 }
        });
        
        expect(wrapper.find('.loading-skeleton').exists()).toBe(true);
    });
    
    it('updates data when filter changes', async () => {
        const wrapper = mount(RoleForecastsTable, {
            props: { scenarioId: 1 }
        });
        
        await wrapper.vm.setFilter('forecastArea', 'Engineering');
        await wrapper.vm.$nextTick();
        
        expect(wrapper.vm.filteredForecasts).toHaveLength(3);
    });
    
    it('exports to CSV', () => {
        const wrapper = mount(RoleForecastsTable);
        const spy = vi.spyOn(window, 'fetch');
        
        wrapper.vm.exportToCSV();
        
        expect(spy).toHaveBeenCalledWith('/api/export/csv');
    });
});
```

---

### 3️⃣ Integration Tests (60 min)

**3.1 API Integration Tests**
```bash
tests/Feature/WorkforcePlanning/
├── ScenariosApiTest.php
│   ├── testGetScenarios()
│   ├── testCreateScenario()
│   ├── testUpdateScenario()
│   ├── testDeleteScenario()
│   ├── testGetAnalytics()
│   └── testRunAnalysis()
│
├── MatchingApiTest.php
│   ├── testGetMatches()
│   ├── testRunMatching()
│   ├── testFilterMatches()
│   └── testBulkUpdate()
│
├── SkillGapsApiTest.php
│   ├── testGetGaps()
│   ├── testCreateGap()
│   ├── testUpdateGap()
│   └── testDeleteGap()
│
└── SuccessionApiTest.php
    ├── testGetPlans()
    ├── testCreatePlan()
    └── testUpdateReadiness()
```

**Test Case Example - API Response Contract:**
```php
public function testGetAnalyticsResponseStructure() {
    $scenario = Scenario::factory()->create();
    
    $response = $this->getJson("/api/v1/workforce-planning/scenarios/{$scenario->id}/analytics");
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'total_headcount_current',
        'total_headcount_projected',
        'net_growth',
        'internal_coverage_percentage',
        'external_gap_percentage',
        'succession_risk_percentage',
        'estimated_recruitment_cost',
        'estimated_training_cost',
    ]);
}
```

**3.2 Database Integration**
```php
public function testScenariosWithMatches() {
    // Create scenario with related data
    $scenario = Scenario::factory()
        ->has(Match::factory()->count(5))
        ->create();
    
    $matches = $scenario->matches;
    
    $this->assertCount(5, $matches);
    $this->assertInstanceOf(Match::class, $matches[0]);
}
```

---

### 4️⃣ E2E Tests - Playwright (60 min)

**4.1 Critical User Flows**
```bash
tests/e2e/
├── workflows.spec.ts
│   ├── testCreateScenarioFlow()
│   ├── testRunAnalysisFlow()
│   ├── testFilterAndExportFlow()
│   ├── testEditInlineFlow()
│   └── testDeleteWithConfirmationFlow()
│
├── navigation.spec.ts
│   ├── testNavigateTabs()
│   ├── testBreadcrumbs()
│   └── testBackButton()
│
├── errorHandling.spec.ts
│   ├── test404Errors()
│   ├── testNetworkFailure()
│   ├── testValidationErrors()
│   └── testRecovery()
│
└── accessibility.spec.ts
    ├── testScreenReaderSupport()
    ├── testKeyboardNavigation()
    ├── testColorContrast()
    └── testFormLabels()
```

**E2E Test Example:**
```typescript
test('Complete workflow: Create scenario → Run analysis → Export report', async ({ page }) => {
    // Navigate to app
    await page.goto('/workforce-planning');
    
    // Create scenario
    await page.click('[data-testid="btn-create-scenario"]');
    await page.fill('[data-testid="input-name"]', 'Q1 2026 Planning');
    await page.fill('[data-testid="input-description"]', 'Recruitment planning');
    await page.click('[data-testid="btn-save"]');
    
    // Verify scenario created
    await expect(page.locator('text=Q1 2026 Planning')).toBeVisible();
    
    // Navigate to dashboard
    await page.click('[data-testid="link-dashboard"]');
    
    // Run analysis
    await page.click('[data-testid="btn-run-analysis"]');
    await page.waitForLoadState('networkidle');
    
    // Verify charts loaded
    await expect(page.locator('svg')).toHaveCount(7); // 7 charts
    
    // Export report
    await page.click('[data-testid="btn-export"]');
    
    // Verify download
    const downloadPromise = page.waitForEvent('download');
    await page.click('[data-testid="btn-export-pdf"]');
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toBe('scenario-q1-2026.pdf');
});
```

**4.2 Error Scenarios**
```typescript
test('Handle 500 error gracefully', async ({ page }) => {
    // Mock API to return 500
    await page.route('**/api/v1/workforce-planning/**', route => {
        route.abort('servererror');
    });
    
    await page.goto('/workforce-planning');
    
    // Should show error message
    await expect(page.locator('[data-testid="error-message"]')).toBeVisible();
    await expect(page.locator('text=Something went wrong')).toBeVisible();
    
    // Should show retry button
    await expect(page.locator('[data-testid="btn-retry"]')).toBeEnabled();
});
```

---

### 5️⃣ Performance Tests (30 min)

**5.1 Lighthouse Audits**
```bash
npm run test:lighthouse

Requirements:
- Performance: >= 85
- Accessibility: >= 90
- Best Practices: >= 85
- SEO: >= 90
```

**5.2 Bundle Size Analysis**
```bash
npm run analyze:bundle

Limits:
- Total: < 500KB
- Main chunk: < 300KB
- Dashboard chunk: < 100KB
- Charts chunk: < 80KB
```

**5.3 Runtime Performance**
```typescript
test('Dashboard renders in < 1 second', async () => {
    const start = performance.now();
    const wrapper = mount(OverviewDashboard);
    const end = performance.now();
    
    expect(end - start).toBeLessThan(1000);
});
```

---

## 📊 Test Coverage Targets

| Category | Target | Current |
|----------|--------|---------|
| Unit Tests (Backend) | 90% | 0% |
| Unit Tests (Frontend) | 85% | 0% |
| Integration Tests | 80% | 0% |
| E2E Tests | 6+ flows | 0 |
| Performance | 85+ score | TBD |
| Accessibility | 90+ score | TBD |

---

## 🛠️ Tools & Framework

```json
{
  "testing": {
    "backend": {
      "framework": "PHPUnit",
      "coverage": "php-code-coverage",
      "mocking": "Mockery"
    },
    "frontend": {
      "framework": "Vitest",
      "components": "Vue Test Utils",
      "mocking": "Vitest mocks"
    },
    "e2e": {
      "framework": "Playwright",
      "reporters": ["html", "json"]
    },
    "performance": {
      "lighthouse": "npm package",
      "bundle": "rollup-plugin-visualizer"
    }
  }
}
```

---

## 📝 Test Commands

```bash
# Backend tests
php artisan test tests/Unit/WorkforcePlanning
php artisan test tests/Feature/WorkforcePlanning
php artisan test --coverage

# Frontend tests
npm run test:unit
npm run test:coverage
npm run test:watch

# E2E tests
npm run test:e2e
npm run test:e2e:ui

# Performance
npm run test:lighthouse
npm run analyze:bundle

# All tests
npm run test:all
```

---

## 📅 Implementation Timeline

```
Session 1 (2-3 hours total):
├─ Opción A: Charts (45 min) ✅ DONE
├─ Opción B: UX Polish (120 min) ⏳ TODO
└─ Opción C: Testing (120 min) ⏳ TODO

Testing Breakdown:
├─ 1. Unit Tests Backend (60 min)
├─ 2. Unit Tests Frontend (60 min)
├─ 3. Integration Tests (60 min)
├─ 4. E2E Tests (60 min)
└─ 5. Performance Tests (30 min)

Total: 270 minutes (4.5 hours) - pero se puede paralelizar
```

---

## ✅ Definition of Done

**Para cada test suite:**
1. ✅ Todos los tests escritos
2. ✅ 100% de tests pasando
3. ✅ Coverage >= target
4. ✅ Edge cases cubiertos
5. ✅ Documentación incluida
6. ✅ CI/CD integrado

**Para Opción C completa:**
1. ✅ 50+ tests escritos
2. ✅ 80%+ code coverage
3. ✅ 0 failing tests
4. ✅ E2E workflows validated
5. ✅ Performance benchmarks met
6. ✅ All tests in CI/CD pipeline

---

## 🎯 Success Criteria

✅ **All 4 critical flows work end-to-end:**
- Create scenario → View dashboard
- Upload candidates → Run matching
- Apply filters → Export report
- Delete with confirmation → Verify deletion

✅ **Performance targets met:**
- Page load: < 2 seconds
- Chart rendering: < 500ms
- Filter response: < 300ms
- API responses: < 1 second (p95)

✅ **Quality gates:**
- 80%+ code coverage
- 0 critical bugs
- 0 security vulnerabilities
- 0 accessibility violations (WCAG AA)

---

## 📖 Documentation Needed

- [ ] Test setup guide
- [ ] How to write new tests
- [ ] Test data fixtures
- [ ] CI/CD configuration
- [ ] Performance baseline
- [ ] Troubleshooting guide

---

## 🔗 Related Documents

- [Opción A - Charts (COMPLETADA)](./OPCION_A_CHARTS_COMPLETADA.md)
- [Opción B - UX Polish (Próximo)](./OPCION_B_UX_POLISH_PLAN.md)
- [Testing Best Practices](./TESTING_BEST_PRACTICES.md) *(Crear)*

---

**Documento creado:** 2026-01-15 16:05 UTC  
**Versión:** 1.0.0  
**Estado:** 📋 Planificado - Esperando finalización de Opción B

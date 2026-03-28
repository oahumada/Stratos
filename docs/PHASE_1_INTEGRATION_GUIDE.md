# Task 2 Phase 1 - Integration Guide

**Status**: Ready for Component Integration  
**Target**: Create Scenario Planning landing page to display Phase 1 components

---

## Step 1: Create Scenario Planning Category

### A. Create Page Directory

```bash
mkdir -p resources/js/Pages/ScenarioPlanning
```

### B. Create Landing Page

**File**: `resources/js/Pages/ScenarioPlanning/Index.vue`

Copy/paste the template below:

```vue
<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Scenario Planning
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Compare, analyze, and plan organizational transformation
                    scenarios
                </p>
            </div>
            <button
                @click="showNewScenarioModal = true"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-white transition hover:bg-emerald-700"
            >
                + New Scenario
            </button>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex space-x-8">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-1 py-3 text-sm font-medium transition',
                        activeTab === tab.id
                            ? 'border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-400'
                            : 'border-b-2 border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300',
                    ]"
                >
                    {{ tab.name }}
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="min-h-96">
            <!-- Comparison Tab -->
            <div v-show="activeTab === 'comparison'">
                <ScenarioComparison />
            </div>

            <!-- Metrics Tab -->
            <div v-show="activeTab === 'metrics'">
                <ScenarioMetrics
                    :scenario-id="selectedScenarioId"
                    :financial-impact="sampleFinancialData"
                    :risk-metrics="sampleRiskData"
                    :headcount-data="sampleHeadcountData"
                />
            </div>

            <!-- Timeline Tab -->
            <div v-show="activeTab === 'timeline'">
                <ScenarioTimeline :scenarios="sampleScenarios" />
            </div>

            <!-- Risk Assessment Tab -->
            <div v-show="activeTab === 'risk'">
                <RiskAssessment />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import ScenarioComparison from '@/components/ScenarioPlanning/ScenarioComparison.vue';
import ScenarioMetrics from '@/components/ScenarioPlanning/ScenarioMetrics.vue';
import ScenarioTimeline from '@/components/ScenarioPlanning/ScenarioTimeline.vue';
import RiskAssessment from '@/components/ScenarioPlanning/RiskAssessment.vue';

// State
const activeTab = ref('comparison');
const selectedScenarioId = ref(1);
const showNewScenarioModal = ref(false);

// Tab Configuration
const tabs = [
    { id: 'comparison', name: '📊 Comparison' },
    { id: 'metrics', name: '📈 Metrics' },
    { id: 'timeline', name: '⏱️ Timeline' },
    { id: 'risk', name: '⚠️ Risk Assessment' },
];

// Sample Data (Replace with API calls in Phase 2)
const sampleScenarios = [
    {
        id: 1,
        name: 'Conservative Approach',
        iq: 72,
        timeline_months: 12,
        cost_estimate: '€2.5M',
    },
    {
        id: 2,
        name: 'Aggressive Modernization',
        iq: 88,
        timeline_months: 8,
        cost_estimate: '€4.2M',
    },
];

const sampleFinancialData = {
    total_impact: 285000,
    roi_percentage: 125.5,
    cost_breakdown: {
        training: 45000,
        hiring: 120000,
        reallocation: 78000,
        external_services: 42000,
    },
    budget_allocation: {
        Q1: 2024,
        Q2: 2024,
        Q3: 2024,
        Q4: 2024,
    },
    payback_period_months: 8.5,
};

const sampleRiskData = {
    overall_risk: 35,
    probability: 0.45,
    impact: 0.65,
    risk_items: [
        { name: 'Talent Pool', probability: 0.75, impact: 0.8 },
        { name: 'Market Conditions', probability: 0.5, impact: 0.65 },
        { name: 'Adoption Rate', probability: 0.3, impact: 0.9 },
    ],
};

const sampleHeadcountData = {
    current: 150,
    target: 175,
    change_percent: 16.7,
    by_role: {
        'Data Analyst': 10,
        'ML Engineer': 8,
        DevOps: 5,
        'Business Analyst': 2,
    },
};
</script>

<style scoped>
/* Optional: Add smooth transitions */
div[v-show] {
    animation: fadeIn 0.2s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
```

---

## Step 2: Register Route

### A. Add Route to Web Routes

**File**: `routes/web.php`

Add this line within the authenticated routes group (find the appropriate namespace):

```php
// Scenario Planning Routes
Route::prefix('scenario-planning')
    ->middleware('auth:sanctum', 'tenant')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('ScenarioPlanning/Index');
        })->name('scenario-planning.index');
    });
```

Or if using a controller pattern (preferred):

```php
use App\Http\Controllers\ScenarioPlanning\ScenarioController;

Route::prefix('scenario-planning')
    ->middleware('auth:sanctum', 'tenant')
    ->group(function () {
        Route::get('/', [ScenarioController::class, 'index'])->name('scenario-planning.index');
    });
```

### B. Create Controller (Optional but Recommended)

**File**: `app/Http/Controllers/ScenarioPlanning/ScenarioController.php`

```php
<?php

namespace App\Http\Controllers\ScenarioPlanning;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ScenarioController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ScenarioPlanning/Index', [
            // Pass initial data if needed
            // 'scenarios' => Scenario::where('organization_id', auth()->user()->organization_id)->get(),
        ]);
    }
}
```

---

## Step 3: Add Sidebar Navigation

### Find the Navigation Component

Search for where sidebar items are defined (typically in a Layout.vue or Navigation.vue):

```bash
grep -r "Admin Operations\|sidebar\|navigation" resources/js --include="*.vue" | head -20
```

### Add Menu Item

Add this entry to your sidebar/navigation menu:

```vue
<!-- Scenario Planning -->
<Link
    href="/scenario-planning"
    :active="route().current('scenario-planning.*')"
    icon="📊"
>
    Scenario Planning
</Link>
```

Or if using a menu array:

```typescript
{
    name: 'Scenario Planning',
    href: '/scenario-planning',
    icon: 'chart-bar',  // or your icon system
    badge: 'Beta',
}
```

---

## Step 4: Verify Component Imports

### A. Check Component Paths

Verify all components are in the correct location:

```bash
ls -la resources/js/components/ScenarioPlanning/
```

Expected output:

```
ScenarioComparison.vue
ScenarioMetrics.vue
ScenarioTimeline.vue
RiskAssessment.vue
```

### B. Test Component Imports

Run your dev build to verify imports resolve:

```bash
npm run dev
```

Watch for errors like:

- `Module not found: @/components/ScenarioPlanning/...`
- `Failed to load component`

---

## Step 5: API Integration (Phase 2 - When Real Data Available)

### A. Replace Sample Data with API Calls

In Phase 1, the page uses hardcoded sample data. For Phase 2, replace this:

**Before (Sample Data)**:

```typescript
const sampleScenarios = [...]
const sampleFinancialData = {...}
```

**After (API Calls)**:

```typescript
import { onMounted, ref } from 'vue';

const scenarios = ref([]);
const financialData = ref(null);
const riskData = ref(null);

onMounted(async () => {
    // Fetch scenarios
    const scenarioRes = await fetch('/api/scenarios');
    scenarios.value = await scenarioRes.json();

    // Fetch analytics for first scenario
    if (scenarios.value.length > 0) {
        const analyticsRes = await fetch(
            `/api/scenarios/${scenarios.value[0].id}/analytics`,
        );
        const analytics = await analyticsRes.json();
        financialData.value = analytics.financial_impact;
        riskData.value = analytics.risk_metrics;
    }
});
```

### B. API Endpoints Available (Phase 1)

```
POST   /api/scenarios/compare
GET    /api/scenarios/{scenario}/analytics
GET    /api/scenarios/{scenario}/financial-impact
GET    /api/scenarios/{scenario}/risk-assessment
GET    /api/scenarios/{scenario}/skill-gaps
```

See [ScenarioAnalyticsController](app/Http/Controllers/Api/ScenarioAnalyticsController.php) for full details.

---

## Step 6: Build & Test

### A. Build

```bash
npm run build
```

Expected output:

```
✓ built in 1m 0s
0 errors
```

### B. Run Dev Server

```bash
composer run dev
```

### C. Test in Browser

1. Navigate to `http://localhost:8000/scenario-planning`
2. Verify all 4 tabs render without errors
3. Test tab switching
4. Check dark mode toggle

---

## Step 7: Run Tests

### A. Fix Database Migration (Known Issue)

```bash
# In test environment
php artisan migrate:fresh --env=testing
```

### B. Run Phase 1 Tests

```bash
php artisan test tests/Feature/ScenarioAnalyticsControllerTest.php --compact
```

Expected:

```
15 tests
✓ PASSED
```

---

## Integration Checklist

- [ ] **Step 1**: Created `/resources/js/Pages/ScenarioPlanning/Index.vue`
- [ ] **Step 2**: Added routes in `routes/web.php`
- [ ] **Step 2b**: (Optional) Created `ScenarioController`
- [ ] **Step 3**: Added sidebar navigation item
- [ ] **Step 4**: Verified component file locations
- [ ] **Step 5**: Confirmed imports resolve
- [ ] **Step 6**: Build successful (`npm run build`)
- [ ] **Step 6**: Dev server running (`composer run dev`)
- [ ] **Step 6**: Navigated to page in browser ✓
- [ ] **Step 7**: Tests passing (15/15) ✓

---

## Quick Troubleshooting

### "Cannot find module @/components/ScenarioPlanning/..."

**Solution**: Check file paths:

```bash
find resources/js/components -name "*.vue" | grep Scenario
```

### "Page not found" when navigating to `/scenario-planning`

**Solution**: Verify route exists:

```bash
php artisan route:list | grep scenario
```

### Components render but no data displays

**Solution**: Check browser console for API errors:

- Open DevTools (F12)
- Click Console tab
- Look for network errors
- Verify API endpoints in [ScenarioAnalyticsController](app/Http/Controllers/Api/ScenarioAnalyticsController.php)

### Dark mode not working

**Solution**: Verify Tailwind dark mode classes are used:

```bash
grep "dark:" resources/js/components/ScenarioPlanning/*.vue | head -5
```

---

## Database Reset (If Needed)

If tests fail due to migration issues:

```bash
# Full reset
php artisan migrate:fresh --seed --env=testing

# Then run tests
php artisan test tests/Feature/ScenarioAnalyticsControllerTest.php
```

---

## Next: Phase 2 Preparation

Once Phase 1 integration is complete:

1. Document any integration issues discovered
2. Create feedback ticket for Phase 2 workflow improvements
3. Begin Phase 2 kickoff: Workflow Engine & Approval System
4. Update [TASK_2_PLANNING_SCENARIO_PLANNING_PHASE2.md](TASK_2_PLANNING_SCENARIO_PLANNING_PHASE2.md) with real metrics

---

**Integration Estimated Time**: 45 minutes - 1 hour  
**Complexity**: Low (template-based, straightforward route/component registration)

👉 **Follow the steps above in order for successful Phase 1 integration.**

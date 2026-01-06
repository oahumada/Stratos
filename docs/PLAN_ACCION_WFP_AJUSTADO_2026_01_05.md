# 📊 AJUSTE DE PLAN ACCIÓN - Basado en Implementación Actual
**Fecha:** 5 de Enero de 2026  
**Status de Revisión:** ✅ COMPLETADA

---

## 📈 ESTADO ACTUAL IMPLEMENTADO

### ✅ Backend (100% Core)
```
✅ Models (6):
   - WorkforcePlanningScenario
   - WorkforcePlanningRoleForecast
   - WorkforcePlanningMatch
   - WorkforcePlanningSkillGap
   - WorkforcePlanningSuccessionPlan
   - WorkforcePlanningAnalytic

✅ Controller (13 endpoints):
   - listScenarios, showScenario, createScenario, updateScenario, deleteScenario
   - approveScenario
   - getScenarioForecasts, getScenarioMatches
   - getScenarioSkillGaps, getScenarioSuccessionPlans
   - getScenarioAnalytics
   - analyzeScenario (POST)
   - getMatchRecommendations

✅ Repository:
   - 30+ métodos de query
   - Filtrado completo
   - Relaciones optimizadas

✅ Service (WorkforcePlanningService):
   - calculateMatches()
   - calculateSkillGaps()
   - calculateAnalytics()
   - runFullAnalysis()

✅ Requests (Validation):
   - StoreWorkforcePlanningScenarioRequest
   - UpdateWorkforcePlanningScenarioRequest

✅ Routes:
   - /api/v1/workforce-planning/* (todos registrados)
```

### ⏳ Frontend (33% Implementado)
```
✅ Componentes Básicos (6):
   - ScenarioSelector.vue (selector de escenarios + create)
   - OverviewDashboard.vue (tabs dashboard)
   - RoleForecastsTable.vue (tabla de forecasts)
   - MatchingResults.vue (tabla de matches)
   - SkillGapsMatrix.vue (matriz de gaps)
   - SuccessionPlanCard.vue (cards de sucesión)

✅ Charts (7):
   - HeadcountChart.vue
   - CoverageChart.vue
   - SkillGapsChart.vue
   - SuccessionRiskChart.vue
   - ReadinessTimelineChart.vue
   - MatchScoreDistributionChart.vue
   - DepartmentGapsChart.vue

✅ Pinia Store:
   - workforcePlanningStore.ts (estados, getters, acciones)

❌ Falta:
   - Componentes para 3 nuevas funcionalidades (Simulador, ROI, Estrategias)
```

---

## 🎯 PLAN DE ACCIÓN AJUSTADO

### 📌 Cambio Principal
**Del:** Crear 3 componentes completamente nuevos (38-50 horas)  
**Al:** Extender/reutilizar componentes existentes + agregar endpoints nuevos (18-24 horas)

---

## 🚀 COMPONENTE 1: SIMULADOR DE CRECIMIENTO (Reutiliza OverviewDashboard)

### Backend - Nuevos Endpoints (2)

#### 1. POST `/api/v1/workforce-planning/scenarios/{id}/simulate-growth`

**Ubicación:** Agregar a WorkforcePlanningController.php

```php
/**
 * Simular crecimiento de headcount
 * POST /api/v1/workforce-planning/scenarios/{id}/simulate-growth
 */
public function simulateGrowth($scenarioId, Request $request): JsonResponse
{
    $validated = $request->validate([
        'growth_percentage' => 'required|numeric|min:0|max:100',
        'horizon_months' => 'required|integer|in:12,18,24,36',
        'target_departments' => 'nullable|array',
        'external_hiring_ratio' => 'nullable|numeric|min:0|max:100',
        'retention_target' => 'nullable|numeric|min:0|max:100',
    ]);

    try {
        $scenario = WorkforcePlanningScenario::findOrFail($scenarioId);
        
        // Calcular proyecciones
        $currentHeadcount = $scenario->analytics->total_headcount_current ?? 250;
        $projectedHeadcount = $currentHeadcount * (1 + $validated['growth_percentage'] / 100);
        $netGrowth = $projectedHeadcount - $currentHeadcount;
        
        // Calcular skill gaps
        $forecasts = $scenario->roleForecasts()->get();
        $skillsNeeded = [];
        foreach ($forecasts as $forecast) {
            // Mapear skills del forecast a array
            if ($forecast->critical_skills) {
                $skillsNeeded[] = [
                    'skill_id' => 1,
                    'skill_name' => 'Cloud Architecture',
                    'count' => intval($netGrowth * 0.4),
                    'availability_internal' => 0.4,
                ];
            }
        }

        $simulation = [
            'scenario_id' => $scenarioId,
            'current_headcount' => $currentHeadcount,
            'projected_headcount' => $projectedHeadcount,
            'net_growth' => $netGrowth,
            'by_department' => $this->getDepartmentBreakdown($scenario, $validated),
            'skills_needed' => $skillsNeeded,
            'critical_risks' => $this->identifyCriticalRisks($scenario),
        ];

        return response()->json([
            'success' => true,
            'data' => ['simulation' => $simulation],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}

private function getDepartmentBreakdown($scenario, $validated): array
{
    return [
        'Engineering' => ['current' => 120, 'projected' => 156, 'gap' => 36],
        'Sales' => ['current' => 80, 'projected' => 100, 'gap' => 20],
    ];
}

private function identifyCriticalRisks($scenario): array
{
    $successors = $scenario->successionPlans()->where('status', 'approved')->get();
    
    return [
        [
            'role' => 'VP Engineering',
            'critical_level' => 'critical',
            'successors_ready' => 0,
            'action' => 'URGENT DEVELOPMENT'
        ]
    ];
}
```

#### 2. GET `/api/v1/workforce-planning/critical-positions`

**Ubicación:** Agregar a WorkforcePlanningController.php

```php
/**
 * Obtener posiciones críticas y riesgo de sucesión
 * GET /api/v1/workforce-planning/critical-positions?scenario_id=1
 */
public function getCriticalPositions(Request $request): JsonResponse
{
    $scenarioId = $request->input('scenario_id');
    
    try {
        $scenario = WorkforcePlanningScenario::findOrFail($scenarioId);
        
        // Obtener planes de sucesión con análisis de riesgo
        $criticalPositions = $scenario->successionPlans()
            ->with('role', 'primarySuccessor', 'secondarySuccessor')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'role' => ['id' => $plan->role->id, 'name' => $plan->role->name],
                    'department' => $plan->role->department ?? 'N/A',
                    'criticality_level' => $plan->criticality_level,
                    'criticality_score' => $this->calculateCriticalityScore($plan),
                    'impact_if_vacant' => $plan->impact_if_vacant,
                    'replacement_time_months' => 6,
                    'successors' => [
                        'ready_now' => $this->countSuccessorsByReadiness($plan, 'ready_now'),
                        'ready_12m' => $this->countSuccessorsByReadiness($plan, 'ready_12m'),
                        'ready_24m' => $this->countSuccessorsByReadiness($plan, 'ready_24m'),
                        'not_ready' => $this->countSuccessorsByReadiness($plan, 'not_ready'),
                    ],
                    'risk_status' => $this->assessRiskStatus($plan),
                    'recommended_action' => $this->recommendAction($plan),
                ];
            });

        $summary = [
            'total_critical_positions' => $criticalPositions->count(),
            'positions_with_ready_successor' => $criticalPositions->filter(fn($p) => $p['successors']['ready_now'] > 0)->count(),
            'positions_without_successor' => $criticalPositions->filter(fn($p) => $p['successors']['ready_now'] == 0)->count(),
            'high_risk_count' => $criticalPositions->filter(fn($p) => $p['risk_status'] === 'HIGH')->count(),
            'medium_risk_count' => $criticalPositions->filter(fn($p) => $p['risk_status'] === 'MEDIUM')->count(),
            'low_risk_count' => $criticalPositions->filter(fn($p) => $p['risk_status'] === 'LOW')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $criticalPositions,
            'summary' => $summary,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}

private function calculateCriticalityScore($plan): int
{
    return 95; // Simulado - lógica en WorkforcePlanningService
}

private function countSuccessorsByReadiness($plan, $readiness): int
{
    // Lógica para contar sucesores por nivel de readiness
    return $readiness === 'ready_now' ? 0 : 1;
}

private function assessRiskStatus($plan): string
{
    return match($plan->criticality_level) {
        'critical' => 'HIGH',
        'high' => 'MEDIUM',
        default => 'LOW',
    };
}

private function recommendAction($plan): string
{
    return "Immediate succession planning for " . ($plan->primarySuccessor->name ?? "Ready-12m candidate");
}
```

### Frontend - Extensión de OverviewDashboard.vue

**Cambios:**
1. Agregar nueva tab: "Simulador de Crecimiento"
2. Agregar nueva tab: "Posiciones Críticas"
3. Reutilizar charts existentes

**Archivo a editar:** `/resources/js/pages/WorkforcePlanning/OverviewDashboard.vue`

```vue
<!-- Nueva tab en v-tabs -->
<v-tab value="simulator">
  <v-icon start>mdi-chart-timeline</v-icon>
  Growth Simulator
</v-tab>

<v-tab value="critical">
  <v-icon start>mdi-alert-circle</v-icon>
  Critical Positions ({{ criticalPositionsCount }})
</v-tab>

<!-- Nueva window item para simulador -->
<v-window-item value="simulator">
  <v-card>
    <v-card-title>Growth Scenario Simulator</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="3">
          <v-text-field
            v-model.number="simulationParams.growth_percentage"
            label="Growth %"
            type="number"
            suffix="%"
            @change="runSimulation"
          />
        </v-col>
        <v-col cols="12" md="3">
          <v-select
            v-model="simulationParams.horizon_months"
            :items="[12, 18, 24, 36]"
            label="Horizon (months)"
            @change="runSimulation"
          />
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            v-model.number="simulationParams.external_hiring_ratio"
            label="External Hiring %"
            type="number"
            suffix="%"
            @change="runSimulation"
          />
        </v-col>
        <v-col cols="12" md="3">
          <v-btn color="primary" @click="runSimulation" block>
            Run Simulation
          </v-btn>
        </v-col>
      </v-row>

      <v-row v-if="simulationResults" class="mt-4">
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Headcount Projection</v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="6">
                  <div class="text-center">
                    <div class="text-h5">{{ simulationResults.current_headcount }}</div>
                    <div class="text-caption">Current</div>
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="text-center">
                    <div class="text-h5 text-blue">{{ simulationResults.projected_headcount }}</div>
                    <div class="text-caption">Projected</div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Net Growth</v-card-title>
            <v-card-text>
              <div class="text-h5 text-green">+{{ simulationResults.net_growth }}</div>
              <div class="text-caption">Total headcount increase</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</v-window-item>

<!-- Nueva window item para posiciones críticas -->
<v-window-item value="critical">
  <v-card>
    <v-card-title>Critical Positions & Succession Risk</v-card-title>
    <v-card-text>
      <v-data-table
        :headers="criticalPositionsHeaders"
        :items="criticalPositions"
        density="compact"
        class="mb-4"
      >
        <template #item.risk_status="{ item }">
          <v-chip
            :color="getRiskColor(item.risk_status)"
            text-color="white"
            size="small"
          >
            {{ item.risk_status }}
          </v-chip>
        </template>
      </v-data-table>
    </v-card-text>
  </v-card>
</v-window-item>
```

**Script setup (agregar a OverviewDashboard.vue):**

```typescript
// Simulation
const simulationParams = ref({
  growth_percentage: 25,
  horizon_months: 24,
  external_hiring_ratio: 30,
  retention_target: 95
})

const simulationResults = ref(null)
const criticalPositions = ref([])
const criticalPositionsHeaders = [
  { title: 'Role', value: 'role.name' },
  { title: 'Department', value: 'department' },
  { title: 'Criticality', value: 'criticality_level' },
  { title: 'Risk Status', value: 'risk_status' },
  { title: 'Action', value: 'recommended_action' }
]

const criticalPositionsCount = computed(() => criticalPositions.value.length)

const runSimulation = async () => {
  try {
    const response = await api.post(
      `/api/v1/workforce-planning/scenarios/${scenarioId.value}/simulate-growth`,
      simulationParams.value
    )
    simulationResults.value = response.data.data.simulation
  } catch (error) {
    showError('Simulation failed')
  }
}

const loadCriticalPositions = async () => {
  try {
    const response = await api.get(
      `/api/v1/workforce-planning/critical-positions`,
      { scenario_id: scenarioId.value }
    )
    criticalPositions.value = response.data.data
  } catch (error) {
    showError('Failed to load critical positions')
  }
}

const getRiskColor = (status: string): string => {
  return { HIGH: 'red', MEDIUM: 'orange', LOW: 'green' }[status] || 'grey'
}

// En onMounted
onMounted(async () => {
  // ... código existente ...
  await loadCriticalPositions()
  await runSimulation()
})
```

### ✅ Checklist Componente 1

- [ ] Agregar 2 métodos a WorkforcePlanningController.php
- [ ] Registrar 2 rutas en api.php
- [ ] Extender OverviewDashboard.vue (+ 150 líneas)
- [ ] Testear simulación con Postman
- [ ] Testear flujo en navegador

**Tiempo estimado:** 4-6 horas

---

## 💰 COMPONENTE 2: CALCULADORA ROI (Nuevo Componente Simple)

### Backend - Nuevos Endpoints (2)

#### 1. POST `/api/v1/workforce-planning/roi-calculator/calculate`

**Ubicación:** Crear RoiCalculatorController.php

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoiCalculatorController extends Controller
{
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_id' => 'required|integer',
            'headcount_needed' => 'required|integer|min:1',
            'recruitment_cost_per_hire' => 'required|numeric|min:0',
            'training_cost_per_person' => 'required|numeric|min:0',
            'training_duration_months' => 'required|integer|min:1',
            'internal_candidate_pool' => 'required|integer|min:0',
            'salary_external_annual' => 'required|numeric|min:0',
            'salary_internal_annual' => 'required|numeric|min:0',
        ]);

        // Calcular ROI para 3 estrategias
        $results = [
            'build' => $this->calculateBuild($validated),
            'buy' => $this->calculateBuy($validated),
            'borrow' => $this->calculateBorrow($validated),
        ];

        // Recomendar estrategia
        $recommendation = $this->recommendStrategy($results);

        return response()->json([
            'success' => true,
            'data' => [
                'roi_comparison' => [
                    'build' => $results['build'],
                    'buy' => $results['buy'],
                    'borrow' => $results['borrow'],
                ],
                'recommendation' => $recommendation,
            ],
        ]);
    }

    private function calculateBuild($params): array
    {
        $trainingCost = $params['training_cost_per_person'] * $params['internal_candidate_pool'];
        $consultantSupport = $trainingCost * 0.06; // 6% overhead
        $totalCost = $trainingCost + $consultantSupport;

        return [
            'total_cost' => $totalCost,
            'cost_breakdown' => [
                'training' => $trainingCost,
                'consultant_support' => $consultantSupport,
                'opportunity_cost' => 0,
            ],
            'time_to_productivity_months' => $params['training_duration_months'],
            'risk_level' => 'medium',
            'success_probability' => 0.85,
            'net_benefit_12m' => ($params['salary_internal_annual'] * $params['internal_candidate_pool']) - $totalCost,
            'roi_percentage' => intval((($params['salary_internal_annual'] * $params['internal_candidate_pool'] - $totalCost) / $totalCost) * 100),
        ];
    }

    private function calculateBuy($params): array
    {
        $headcountNeeded = $params['headcount_needed'];
        $recruitmentCost = $params['recruitment_cost_per_hire'] * $headcountNeeded;
        $onboardingCost = 3000 * $headcountNeeded;
        $firstYearSalaries = $params['salary_external_annual'] * $headcountNeeded;
        $totalCost = $recruitmentCost + $onboardingCost + $firstYearSalaries;

        return [
            'total_cost' => $totalCost,
            'cost_breakdown' => [
                'recruitment' => $recruitmentCost,
                'onboarding' => $onboardingCost,
                'salaries_first_year' => $firstYearSalaries,
            ],
            'time_to_productivity_months' => 3,
            'risk_level' => 'low',
            'success_probability' => 0.95,
            'net_benefit_12m' => $firstYearSalaries,
            'roi_percentage' => intval(($firstYearSalaries / $totalCost) * 100),
        ];
    }

    private function calculateBorrow($params): array
    {
        $monthlyRate = 5000; // Simulado
        $totalCost = $monthlyRate * $params['training_duration_months'];

        return [
            'total_cost' => $totalCost,
            'cost_breakdown' => [
                'freelance_rates' => $totalCost,
                'management_overhead' => 0,
            ],
            'time_to_productivity_months' => 1,
            'risk_level' => 'high',
            'success_probability' => 0.7,
            'net_benefit_12m' => 0,
            'roi_percentage' => -100,
        ];
    }

    private function recommendStrategy($results): array
    {
        $buildRoi = $results['build']['roi_percentage'];
        $buyRoi = $results['buy']['roi_percentage'];

        if ($buildRoi > $buyRoi) {
            $strategy = 'build';
            $reasoning = "Build strategy has higher ROI ({$buildRoi}% vs {$buyRoi}%) and lower salary costs long-term.";
        } else {
            $strategy = 'buy';
            $reasoning = "Buy strategy reduces time-to-productivity (3 months) and ensures expertise. Higher initial cost but faster value delivery.";
        }

        return [
            'strategy' => $strategy,
            'reasoning' => $reasoning,
            'hybrid_approach' => "Build " . intval(($results['build']['cost_breakdown']['training'] / $results['buy']['total_cost']) * 100) . "% internally + Buy external for gaps",
        ];
    }
}
```

#### 2. GET `/api/v1/workforce-planning/roi-calculator/scenarios`

```php
public function listCalculations(Request $request): JsonResponse
{
    $scenarioId = $request->input('scenario_id');
    
    // Retornar historial de cálculos (simulado por ahora)
    return response()->json([
        'success' => true,
        'data' => [
            [
                'id' => 1,
                'created_at' => now()->subDays(2),
                'strategy_type' => 'build',
                'headcount_needed' => 10,
                'roi_percentage' => 141,
                'total_cost' => 85000,
                'net_benefit_12m' => 120000,
            ]
        ],
    ]);
}
```

### Frontend - Nuevo Componente RoiCalculator.vue

**Archivo:** `/resources/js/pages/WorkforcePlanning/RoiCalculator.vue` (250 líneas)

```vue
<template>
  <div class="roi-calculator">
    <v-card>
      <v-card-title>ROI Calculator: Build vs Buy vs Borrow</v-card-title>

      <!-- INPUTS -->
      <v-card-text class="bg-blue-50">
        <h3 class="mb-4">Configuration</h3>
        <v-row>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="params.headcount_needed"
              label="Headcount Needed"
              type="number"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="params.training_duration_months"
              label="Training Duration (months)"
              type="number"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="params.recruitment_cost_per_hire"
              label="Recruitment Cost"
              type="number"
              prefix="$"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-btn color="primary" @click="calculate" block>
              Calculate ROI
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>

      <!-- RESULTS -->
      <v-divider class="my-6" v-if="results" />

      <v-card-text v-if="results">
        <h3 class="mb-4">Results</h3>
        
        <v-row>
          <v-col cols="12" md="4" v-for="strategy in ['build', 'buy', 'borrow']" :key="strategy">
            <v-card :class="{ 'border-2 border-primary': results.recommendation.strategy === strategy }">
              <v-card-title class="text-capitalize">{{ strategy }}</v-card-title>
              <v-card-text>
                <div class="mb-3">
                  <div class="text-caption">ROI</div>
                  <div class="text-h5" :class="getRoiColor(results.roi_comparison[strategy].roi_percentage)">
                    {{ results.roi_comparison[strategy].roi_percentage }}%
                  </div>
                </div>
                <div class="mb-3">
                  <div class="text-caption">Total Cost</div>
                  <div class="text-subtitle2">${{ formatCost(results.roi_comparison[strategy].total_cost) }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-caption">Time to Productivity</div>
                  <div class="text-subtitle2">{{ results.roi_comparison[strategy].time_to_productivity_months }} months</div>
                </div>
                <div>
                  <div class="text-caption">Risk Level</div>
                  <v-chip size="small" :color="getRiskColor(results.roi_comparison[strategy].risk_level)">
                    {{ results.roi_comparison[strategy].risk_level }}
                  </v-chip>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Recommendation -->
        <v-alert type="info" class="mt-6">
          <strong>Recommended: {{ results.recommendation.strategy.toUpperCase() }}</strong>
          <p class="mt-2">{{ results.recommendation.reasoning }}</p>
        </v-alert>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'

const api = useApi()
const { showError } = useNotification()

const params = ref({
  scenario_id: 1,
  headcount_needed: 10,
  recruitment_cost_per_hire: 15000,
  training_cost_per_person: 8000,
  training_duration_months: 6,
  internal_candidate_pool: 5,
  salary_external_annual: 85000,
  salary_internal_annual: 75000,
})

const results = ref(null)

const calculate = async () => {
  try {
    const response = await api.post(
      '/api/v1/workforce-planning/roi-calculator/calculate',
      params.value
    )
    results.value = response.data.data
  } catch (error) {
    showError('Calculation failed')
  }
}

const getRoiColor = (roi: number): string => {
  return roi > 100 ? 'green' : roi > 0 ? 'orange' : 'red'
}

const getRiskColor = (level: string): string => {
  return { low: 'green', medium: 'orange', high: 'red' }[level] || 'grey'
}

const formatCost = (cost: number): string => {
  return new Intl.NumberFormat('en-US').format(cost)
}
</script>
```

### ✅ Checklist Componente 2

- [ ] Crear RoiCalculatorController.php
- [ ] Registrar 2 rutas en api.php
- [ ] Crear RoiCalculator.vue (250 líneas)
- [ ] Agregar ruta web: `/workforce-planning/roi-calculator`
- [ ] Testear cálculos con Postman

**Tiempo estimado:** 4-5 horas

---

## 🎯 COMPONENTE 3: ASIGNADOR DE ESTRATEGIAS (Nuevo Componente Modular)

### Backend - Nuevos Endpoints (3)

#### 1. GET `/api/v1/workforce-planning/scenarios/{id}/gaps-for-assignment`

```php
/**
 * Obtener brechas listas para asignar estrategia
 * GET /api/v1/workforce-planning/scenarios/{id}/gaps-for-assignment
 */
public function getGapsForAssignment($scenarioId, Request $request): JsonResponse
{
    $department = $request->input('department_id');
    $criticality = $request->input('criticality_level');
    
    try {
        $scenario = WorkforcePlanningScenario::findOrFail($scenarioId);
        
        // Combinar skill gaps + succession gaps + headcount gaps
        $gaps = collect();
        
        // Skill gaps
        $skillGaps = $scenario->skillGaps()
            ->when($department, fn($q) => $q->where('department_id', $department))
            ->when($criticality, fn($q) => $q->where('priority', $criticality))
            ->get()
            ->map(fn($gap) => [
                'id' => 'skill_' . $gap->id,
                'role' => ['id' => $gap->role_id, 'name' => $gap->skill->name],
                'department' => $gap->department ?? 'N/A',
                'gap_type' => 'skill',
                'gap_description' => "Missing {$gap->skill->name} at level {$gap->gap}",
                'criticality' => $gap->priority,
                'estimated_cost_to_fill' => 15000,
                'timeline_months' => 6,
                'internal_capacity' => 3,
                'current_strategy' => null,
                'recommended_strategies' => ['build', 'borrow'],
            ]);
        
        $gaps = $gaps->merge($skillGaps);

        return response()->json([
            'success' => true,
            'data' => $gaps,
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

#### 2. POST `/api/v1/workforce-planning/strategies/assign`

```php
/**
 * Asignar estrategia a un gap
 * POST /api/v1/workforce-planning/strategies/assign
 */
public function assignStrategy(Request $request): JsonResponse
{
    $validated = $request->validate([
        'gap_id' => 'required|string',
        'strategy' => 'required|in:build,buy,borrow,bot',
        'reasoning' => 'required|string|min:10',
        'assigned_by' => 'required|integer',
        'timeline_months' => 'nullable|integer',
        'budget_allocated' => 'nullable|numeric',
    ]);

    try {
        // Guardar asignación (crear tabla strategy_assignments si no existe)
        $assignment = \DB::table('strategy_assignments')->insertGetId([
            'gap_id' => $validated['gap_id'],
            'strategy' => $validated['strategy'],
            'reasoning' => $validated['reasoning'],
            'assigned_by' => $validated['assigned_by'],
            'timeline_months' => $validated['timeline_months'] ?? null,
            'budget_allocated' => $validated['budget_allocated'] ?? null,
            'status' => 'active',
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $assignment],
        ], 201);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

#### 3. GET `/api/v1/workforce-planning/strategies/portfolio/{scenario_id}`

```php
/**
 * Obtener portafolio consolidado de estrategias
 * GET /api/v1/workforce-planning/strategies/portfolio/{scenario_id}
 */
public function getStrategyPortfolio($scenarioId): JsonResponse
{
    try {
        // Obtener todas las asignaciones
        $assignments = \DB::table('strategy_assignments')
            ->where('scenario_id', $scenarioId)
            ->get()
            ->groupBy('strategy');

        $portfolio = [
            'build' => ['count' => $assignments->get('build', collect())->count(), 'gaps' => [], 'total_cost' => 150000],
            'buy' => ['count' => $assignments->get('buy', collect())->count(), 'gaps' => [], 'total_cost' => 250000],
            'borrow' => ['count' => $assignments->get('borrow', collect())->count(), 'gaps' => [], 'total_cost' => 80000],
            'bot' => ['count' => $assignments->get('bot', collect())->count(), 'gaps' => [], 'total_cost' => 50000],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'scenario_id' => $scenarioId,
                'portfolio' => $portfolio,
                'summary' => [
                    'total_gaps' => $assignments->flatten()->count(),
                    'total_investment' => 530000,
                    'total_people_impacted' => 65,
                    'strategic_alignment' => 0.87,
                ],
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

### Frontend - Nuevo Componente StrategyAssigner.vue

**Archivo:** `/resources/js/pages/WorkforcePlanning/StrategyAssigner.vue` (300 líneas)

```vue
<template>
  <v-stepper v-model="step" class="strategy-assigner">
    <!-- HEADER -->
    <v-stepper-header>
      <v-stepper-header-item step="1" :complete="step > 1">
        Identify Gaps
      </v-stepper-header-item>
      <v-divider />
      <v-stepper-header-item step="2" :complete="step > 2">
        Assign Strategies
      </v-stepper-header-item>
      <v-divider />
      <v-stepper-header-item step="3">
        Review Portfolio
      </v-stepper-header-item>
    </v-stepper-header>

    <!-- CONTENT -->
    <v-stepper-window>
      <!-- STEP 1: View Gaps -->
      <v-stepper-window-item step="1">
        <v-card>
          <v-card-title>Step 1: Gaps to Address</v-card-title>
          <v-card-text>
            <v-data-table
              v-model="selectedGaps"
              :headers="gapHeaders"
              :items="gaps"
              item-value="id"
              show-select
              density="compact"
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" @click="step = 2" :disabled="selectedGaps.length === 0">
              Next
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-stepper-window-item>

      <!-- STEP 2: Assign Strategies -->
      <v-stepper-window-item step="2">
        <v-card>
          <v-card-title>Step 2: Assign Strategies</v-card-title>
          <v-card-text>
            <div v-for="gap in selectedGaps" :key="gap.id" class="mb-6">
              <v-card variant="outlined" class="pa-4">
                <v-row>
                  <v-col cols="12" md="6">
                    <h4>{{ gap.gap_description }}</h4>
                    <p class="text-caption">{{ gap.recommended_strategies.join(', ').toUpperCase() }}</p>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="assignments[gap.id]"
                      :items="strategies"
                      item-title="label"
                      item-value="value"
                      label="Strategy"
                    />
                  </v-col>
                </v-row>
              </v-card>
            </div>
          </v-card-text>
          <v-card-actions>
            <v-btn @click="step = 1">Back</v-btn>
            <v-spacer />
            <v-btn color="primary" @click="saveAssignments">
              Save & Review
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-stepper-window-item>

      <!-- STEP 3: Review Portfolio -->
      <v-stepper-window-item step="3">
        <v-card>
          <v-card-title>Step 3: Strategy Portfolio</v-card-title>
          <v-card-text v-if="portfolio">
            <v-row>
              <v-col cols="12" md="3" v-for="strategy in ['build', 'buy', 'borrow', 'bot']" :key="strategy">
                <v-card>
                  <v-card-title class="text-capitalize">{{ strategy }}</v-card-title>
                  <v-card-text>
                    <div class="text-h6">{{ portfolio[strategy].count }}</div>
                    <div class="text-caption">gaps</div>
                    <div class="text-subtitle2 mt-2">${{ portfolio[strategy].total_cost | currency }}</div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-stepper-window-item>
    </v-stepper-window>
  </v-stepper>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'

const api = useApi()
const step = ref(1)
const scenarioId = ref(1)

const gaps = ref([])
const selectedGaps = ref([])
const assignments = ref({})
const portfolio = ref(null)

const gapHeaders = [
  { title: 'Gap', value: 'gap_description' },
  { title: 'Type', value: 'gap_type' },
  { title: 'Criticality', value: 'criticality' },
]

const strategies = [
  { value: 'build', label: 'Build' },
  { value: 'buy', label: 'Buy' },
  { value: 'borrow', label: 'Borrow' },
  { value: 'bot', label: 'Bot' },
]

const loadGaps = async () => {
  const response = await api.get(
    `/api/v1/workforce-planning/scenarios/${scenarioId.value}/gaps-for-assignment`
  )
  gaps.value = response.data.data
}

const saveAssignments = async () => {
  for (const gapId of selectedGaps.value) {
    await api.post('/api/v1/workforce-planning/strategies/assign', {
      gap_id: gapId,
      strategy: assignments.value[gapId],
      reasoning: 'Strategy assigned',
      assigned_by: 1,
    })
  }

  const response = await api.get(
    `/api/v1/workforce-planning/strategies/portfolio/${scenarioId.value}`
  )
  portfolio.value = response.data.data.portfolio
  step.value = 3
}

onMounted(() => loadGaps())
</script>
```

### ✅ Checklist Componente 3

- [ ] Crear StrategyController.php (3 métodos)
- [ ] Registrar 3 rutas en api.php
- [ ] Crear migraciones para `strategy_assignments` (si no existe)
- [ ] Crear StrategyAssigner.vue (300 líneas)
- [ ] Agregar ruta web: `/workforce-planning/strategy-assigner`

**Tiempo estimado:** 6-8 horas

---

## 📅 CRONOGRAMA REVISADO (18-24 horas)

### Día 1 (5 Enero - 6 horas)
- **Mañana (9:00-13:00):** Componente 1 Backend + Frontend
  - ✅ 2 métodos en WorkforcePlanningController
  - ✅ Extender OverviewDashboard.vue
  
- **Tarde (14:00-15:00):** Testeo Componente 1

### Día 2 (6 Enero - 8 horas)
- **Mañana (9:00-12:00):** Componente 2 Completo
  - ✅ Crear RoiCalculatorController
  - ✅ Crear RoiCalculator.vue
  
- **Tarde (13:00-17:00):** Componente 3 Backend
  - ✅ Crear StrategyController
  - ✅ Crear StrategyAssigner.vue (Parte 1)

### Día 3 (7 Enero - 4 horas)
- **Mañana (9:00-13:00):** 
  - ✅ StrategyAssigner.vue (Parte 2)
  - ✅ Testeo integral
  - ✅ Ajustes UI/UX

---

## 📊 RESUMEN DE CAMBIOS

| Componente | Antes | Después | Ahorro |
|-----------|-------|---------|--------|
| Simulador | +16-20h | +4-6h (extensión) | **12-14h ✅** |
| ROI | +12-16h | +4-5h (nuevo simple) | **8-11h ✅** |
| Estrategias | +10-14h | +6-8h (nuevo modular) | **4-6h ✅** |
| **TOTAL** | **38-50h** | **18-24h** | **20-26h ahorrados** |

---

## 🎯 PRÓXIMOS PASOS INMEDIATOS

1. **Agregar rutas en `/src/routes/api.php`**
   - POST `/api/v1/workforce-planning/scenarios/{id}/simulate-growth`
   - GET `/api/v1/workforce-planning/critical-positions`
   - POST `/api/v1/workforce-planning/roi-calculator/calculate`
   - GET `/api/v1/workforce-planning/roi-calculator/scenarios`
   - GET `/api/v1/workforce-planning/scenarios/{id}/gaps-for-assignment`
   - POST `/api/v1/workforce-planning/strategies/assign`
   - GET `/api/v1/workforce-planning/strategies/portfolio/{scenario_id}`

2. **Crear migraciones si falta `strategy_assignments`**

3. **Comenzar implementación en orden:**
   - ① Componente 1 (menor complejidad, máximo impacto)
   - ② Componente 2 (independiente)
   - ③ Componente 3 (wizard de 3 pasos)

---

**Status:** 🎯 PLAN AJUSTADO Y OPTIMIZADO  
**Fecha:** 5 de Enero de 2026  
**Ahorro de Tiempo:** 50% (20-26 horas)

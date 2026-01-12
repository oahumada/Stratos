# 🚀 GUÍA RÁPIDA IMPLEMENTACIÓN - 5 Enero 2026

## ⚡ Paso 1: Agregar Rutas en api.php

**Archivo:** `/src/routes/api.php`

**Ubicación:** En el bloque `Route::prefix('v1/workforce-planning')->group(function () {`

Agregar estas líneas ANTES de la última `});`:

```php
    // Simulación de crecimiento (Componente 1)
    Route::post('/scenarios/{id}/simulate-growth', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'simulateGrowth']);
    Route::get('/critical-positions', [\App\Http\Controllers\Api\V1\WorkforcePlanningController::class, 'getCriticalPositions']);

    // ROI Calculator (Componente 2)
    Route::post('/roi-calculator/calculate', [\App\Http\Controllers\Api\V1\RoiCalculatorController::class, 'calculate']);
    Route::get('/roi-calculator/scenarios', [\App\Http\Controllers\Api\V1\RoiCalculatorController::class, 'listCalculations']);

    // Strategy Assignment (Componente 3)
    Route::get('/scenarios/{id}/gaps-for-assignment', [\App\Http\Controllers\Api\V1\StrategyController::class, 'getGapsForAssignment']);
    Route::post('/strategies/assign', [\App\Http\Controllers\Api\V1\StrategyController::class, 'assignStrategy']);
    Route::get('/strategies/portfolio/{scenario_id}', [\App\Http\Controllers\Api\V1\StrategyController::class, 'getStrategyPortfolio']);
```

---

## ⚡ Paso 2: Crear Métodos en WorkforcePlanningController.php

**Archivo:** `/src/app/Http/Controllers/Api/V1/WorkforcePlanningController.php`

Agregar al final de la clase (antes del último `}`):

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
            $scenario = StrategicPlanningScenarios::findOrFail($scenarioId);

            $currentHeadcount = 250; // Obtener del analytics
            $projectedHeadcount = $currentHeadcount * (1 + $validated['growth_percentage'] / 100);
            $netGrowth = $projectedHeadcount - $currentHeadcount;

            $simulation = [
                'scenario_id' => $scenarioId,
                'current_headcount' => $currentHeadcount,
                'projected_headcount' => $projectedHeadcount,
                'net_growth' => $netGrowth,
                'by_department' => [
                    'Engineering' => ['current' => 120, 'projected' => 156, 'gap' => 36],
                    'Sales' => ['current' => 80, 'projected' => 100, 'gap' => 20],
                ],
                'skills_needed' => [
                    [
                        'skill_id' => 1,
                        'skill_name' => 'Cloud Architecture',
                        'count' => intval($netGrowth * 0.4),
                        'availability_internal' => 0.4,
                    ]
                ],
                'critical_risks' => [
                    [
                        'role' => 'VP Engineering',
                        'critical_level' => 'critical',
                        'successors_ready' => 0,
                        'action' => 'URGENT DEVELOPMENT'
                    ]
                ],
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

    /**
     * Obtener posiciones críticas
     * GET /api/v1/workforce-planning/critical-positions
     */
    public function getCriticalPositions(Request $request): JsonResponse
    {
        $scenarioId = $request->input('scenario_id');

        try {
            $scenario = StrategicPlanningScenarios::findOrFail($scenarioId);

            $criticalPositions = [
                [
                    'id' => 1,
                    'role' => ['id' => 1, 'name' => 'VP Engineering'],
                    'department' => 'Engineering',
                    'criticality_level' => 'critical',
                    'criticality_score' => 95,
                    'impact_if_vacant' => 'Paraliza decisiones técnicas',
                    'replacement_time_months' => 6,
                    'successors' => [
                        'ready_now' => 0,
                        'ready_12m' => 1,
                        'ready_24m' => 2,
                        'not_ready' => 0,
                    ],
                    'risk_status' => 'HIGH',
                    'recommended_action' => 'Immediate succession planning',
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $criticalPositions,
                'summary' => [
                    'total_critical_positions' => 8,
                    'positions_with_ready_successor' => 2,
                    'positions_without_successor' => 6,
                    'high_risk_count' => 4,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
```

---

## ⚡ Paso 3: Crear RoiCalculatorController.php

**Archivo:** `/src/app/Http/Controllers/Api/V1/RoiCalculatorController.php`

Crear archivo con contenido:

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
        $build = $this->calculateBuild($validated);
        $buy = $this->calculateBuy($validated);
        $borrow = $this->calculateBorrow($validated);

        // Recomendar estrategia
        $buildRoi = $build['roi_percentage'];
        $buyRoi = $buy['roi_percentage'];

        if ($buildRoi > $buyRoi) {
            $strategy = 'build';
            $reasoning = "Build strategy has higher ROI ({$buildRoi}% vs {$buyRoi}%) and lower salary costs long-term.";
        } else {
            $strategy = 'buy';
            $reasoning = "Buy strategy reduces time-to-productivity (3 months) and ensures expertise. Higher initial cost but faster value delivery.";
        }

        return response()->json([
            'success' => true,
            'data' => [
                'roi_comparison' => [
                    'build' => $build,
                    'buy' => $buy,
                    'borrow' => $borrow,
                ],
                'recommendation' => [
                    'strategy' => $strategy,
                    'reasoning' => $reasoning,
                ],
            ],
        ]);
    }

    private function calculateBuild($params): array
    {
        $trainingCost = $params['training_cost_per_person'] * $params['internal_candidate_pool'];
        $consultantSupport = $trainingCost * 0.06;
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
            'roi_percentage' => intval((($params['salary_internal_annual'] * $params['internal_candidate_pool'] - $totalCost) / max($totalCost, 1)) * 100),
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
            'roi_percentage' => intval(($firstYearSalaries / max($totalCost, 1)) * 100),
        ];
    }

    private function calculateBorrow($params): array
    {
        $monthlyRate = 5000;
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

    public function listCalculations(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
```

---

## ⚡ Paso 4: Crear StrategyController.php

**Archivo:** `/src/app/Http/Controllers/Api/V1/StrategyController.php`

Crear archivo con contenido:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StrategicPlanningScenarios;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StrategyController extends Controller
{
    public function getGapsForAssignment($scenarioId, Request $request): JsonResponse
    {
        try {
            $scenario = StrategicPlanningScenarios::findOrFail($scenarioId);

            $gaps = [
                [
                    'id' => 'skill_1',
                    'role' => ['id' => 5, 'name' => 'Senior Developer'],
                    'department' => 'Engineering',
                    'gap_type' => 'skill',
                    'gap_description' => '3 people need React skills',
                    'headcount_gap' => 3,
                    'affected_people' => 10,
                    'criticality' => 'high',
                    'estimated_cost_to_fill' => 45000,
                    'timeline_months' => 6,
                    'internal_capacity' => 5,
                    'current_strategy' => null,
                    'recommended_strategies' => ['build', 'borrow'],
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $gaps,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

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
            // Simular almacenamiento
            $id = rand(1, 1000);

            return response()->json([
                'success' => true,
                'data' => ['id' => $id],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getStrategyPortfolio($scenarioId): JsonResponse
    {
        try {
            $portfolio = [
                'build' => ['count' => 15, 'gaps' => [], 'total_cost' => 150000, 'timeline_months' => 8, 'people_impacted' => 45],
                'buy' => ['count' => 8, 'gaps' => [], 'total_cost' => 250000, 'timeline_months' => 3, 'people_impacted' => 12],
                'borrow' => ['count' => 5, 'gaps' => [], 'total_cost' => 80000, 'timeline_months' => 1, 'people_impacted' => 8],
                'bot' => ['count' => 3, 'gaps' => [], 'total_cost' => 50000, 'timeline_months' => 12, 'people_impacted' => 0],
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'scenario_id' => $scenarioId,
                    'portfolio' => $portfolio,
                    'summary' => [
                        'total_gaps' => 31,
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
}
```

---

## ⚡ Paso 5: Extender OverviewDashboard.vue

**Archivo:** `/src/resources/js/pages/WorkforcePlanning/OverviewDashboard.vue`

Localizar la línea con `<v-tab value="overview">` y agregar ANTES de la última `</v-tabs>`:

```vue
<v-tab value="simulator">
        <v-icon start>mdi-chart-timeline</v-icon>
        Growth Simulator
      </v-tab>

<v-tab value="critical">
        <v-icon start>mdi-alert-circle</v-icon>
        Critical Positions ({{ criticalPositionsCount }})
      </v-tab>
```

Luego localizar `</v-window-item>` al final y agregar ANTES de `</v-window>`:

```vue
<!-- Growth Simulator Tab -->
<v-window-item value="simulator">
        <v-card>
          <v-card-title>Growth Scenario Simulator</v-card-title>
          <v-card-text class="bg-blue-50">
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
              <v-col cols="12" md="6">
                <v-btn color="primary" @click="runSimulation" block size="large">
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

<!-- Critical Positions Tab -->
<v-window-item value="critical">
        <v-card>
          <v-card-title>Critical Positions & Succession Risk</v-card-title>
          <v-card-text>
            <v-data-table
              :headers="criticalPositionsHeaders"
              :items="criticalPositions"
              density="compact"
            >
              <template #item.risk_status="{ item }">
                <v-chip :color="getRiskColor(item.risk_status)" text-color="white" size="small">
                  {{ item.risk_status }}
                </v-chip>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-window-item>
```

Luego en el `<script setup>` agregar ANTES del `onMounted`:

```typescript
// Simulation
const simulationParams = ref({
  growth_percentage: 25,
  horizon_months: 24,
  external_hiring_ratio: 30,
  retention_target: 95,
});

const simulationResults = ref(null);
const criticalPositions = ref([]);

const criticalPositionsHeaders = [
  { title: "Role", key: "role.name" },
  { title: "Department", key: "department" },
  { title: "Criticality", key: "criticality_level" },
  { title: "Risk Status", key: "risk_status" },
];

const criticalPositionsCount = computed(() => criticalPositions.value.length);

const runSimulation = async () => {
  try {
    const response = await api.post(
      `/api/v1/workforce-planning/scenarios/${scenarioId.value}/simulate-growth`,
      simulationParams.value
    );
    simulationResults.value = response.data.data.simulation;
  } catch (error) {
    showError("Simulation failed");
    console.error(error);
  }
};

const loadCriticalPositions = async () => {
  try {
    const response = await api.get(
      `/api/v1/workforce-planning/critical-positions`,
      { scenario_id: scenarioId.value }
    );
    criticalPositions.value = response.data.data;
  } catch (error) {
    showError("Failed to load critical positions");
    console.error(error);
  }
};

const getRiskColor = (status: string): string => {
  return { HIGH: "red", MEDIUM: "orange", LOW: "green" }[status] || "grey";
};
```

Y agregar en el `onMounted`:

```typescript
await loadCriticalPositions();
await runSimulation();
```

---

## ⚡ Paso 6: Crear RoiCalculator.vue

**Archivo:** `/src/resources/js/pages/WorkforcePlanning/RoiCalculator.vue`

Crear archivo nuevo con contenido completo (ver documento PLAN_ACCION_WFP_AJUSTADO_2026_01_05.md - sección Componente 2)

---

## ⚡ Paso 7: Crear StrategyAssigner.vue

**Archivo:** `/src/resources/js/pages/WorkforcePlanning/StrategyAssigner.vue`

Crear archivo nuevo con contenido completo (ver documento PLAN_ACCION_WFP_AJUSTADO_2026_01_05.md - sección Componente 3)

---

## ✅ Checklist de Implementación

### Backend (1 hora)

- [ ] Agregar rutas en `/src/routes/api.php` (7 rutas)
- [ ] Agregar 2 métodos en WorkforcePlanningController
- [ ] Crear RoiCalculatorController.php
- [ ] Crear StrategyController.php

### Frontend Componente 1 (2 horas)

- [ ] Extender OverviewDashboard.vue (agregar 2 tabs)
- [ ] Agregar variables de estado (simulation, critical)
- [ ] Agregar métodos (runSimulation, loadCriticalPositions)
- [ ] Testear en navegador

### Frontend Componente 2 (1.5 horas)

- [ ] Crear RoiCalculator.vue (250 líneas)
- [ ] Agregar ruta web en Routes (si es necesario)
- [ ] Testear cálculos

### Frontend Componente 3 (1.5 horas)

- [ ] Crear StrategyAssigner.vue (300 líneas)
- [ ] Agregar ruta web en Routes
- [ ] Testear 3-step wizard

### Testing (1 hora)

- [ ] Testear todos los endpoints con Postman
- [ ] Validar flujos en navegador
- [ ] Ajustes menores

---

## 🚀 TESTEO RÁPIDO CON POSTMAN

### Simulador de Crecimiento

```
POST http://localhost:8000/api/v1/workforce-planning/scenarios/1/simulate-growth
Body:
{
  "growth_percentage": 25,
  "horizon_months": 24,
  "external_hiring_ratio": 30,
  "retention_target": 95
}
```

### ROI Calculator

```
POST http://localhost:8000/api/v1/workforce-planning/roi-calculator/calculate
Body:
{
  "scenario_id": 1,
  "headcount_needed": 10,
  "recruitment_cost_per_hire": 15000,
  "training_cost_per_person": 8000,
  "training_duration_months": 6,
  "internal_candidate_pool": 5,
  "salary_external_annual": 85000,
  "salary_internal_annual": 75000
}
```

### Get Gaps for Assignment

```
GET http://localhost:8000/api/v1/workforce-planning/scenarios/1/gaps-for-assignment?department_id=1
```

---

**Tiempo total estimado:** 6-8 horas  
**Próximas actualizaciones:** POST-implementación

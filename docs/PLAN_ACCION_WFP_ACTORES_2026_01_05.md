# 🚀 PLAN DE ACCIÓN - Workforce Planning para Actores Clave

**Fecha:** 5 de Enero de 2026  
**Objetivo:** Implementar 3 componentes críticos para responder a necesidades de CEO, CFO y CHRO  
**Duración estimada:** 48-72 horas  
**Status:** 🎯 INICIADO

---

## 📋 RESUMEN EJECUTIVO

Tras evaluar los **11 casos de uso** del documento `CasosDeUso.md` contra la arquitectura actual, identificamos **3 implementaciones urgentes** que habilitarán decisiones estratégicas en tiempo real.

| Componente                          | Actor(es) | Impacto    | Complejidad | Horas      |
| ----------------------------------- | --------- | ---------- | ----------- | ---------- |
| **Dashboard Ejecutivo + Simulador** | CEO       | 🔴 CRÍTICA | Alta        | 16-20h     |
| **Calculadora ROI Build vs Buy**    | CFO       | 🔴 CRÍTICA | Media       | 12-16h     |
| **Asignador de Estrategias 4B**     | CHRO      | 🟠 ALTA    | Media       | 10-14h     |
| **TOTAL**                           | -         | -          | -           | **38-50h** |

---

## 🎯 COMPONENTE 1: DASHBOARD EJECUTIVO PARA CEO

### 📌 Casos de Uso Cubiertos

- ✅ Simulación de escenarios de crecimiento/transformación
- ✅ Monitor de riesgo de continuidad de negocio (puestos críticos)
- ✅ Decisiones basadas en viabilidad de talento

### 🏗️ Especificación Técnica

#### Backend - Nuevos Endpoints (2)

**1. POST `//api/workforce-planning/scenarios/{id}/simulate-growth`**

```php
// Request
{
  "growth_percentage": 25,           // % crecimiento de headcount
  "horizon_months": 24,
  "target_departments": ["Engineering", "Sales"],
  "external_hiring_ratio": 0.3,      // 30% hiring externo vs 70% interno
  "retention_target": 95              // % retención esperado
}

// Response
{
  "scenario_id": 1,
  "simulation": {
    "current_headcount": 250,
    "projected_headcount": 312,
    "net_growth": 62,
    "by_department": {
      "Engineering": { "current": 120, "projected": 156, "gap": 36 },
      "Sales": { "current": 80, "projected": 100, "gap": 20 }
    },
    "skills_needed": [
      { "skill_id": 1, "skill_name": "Cloud Architecture", "count": 15, "availability_internal": 0.4 },
      { "skill_id": 2, "skill_name": "React", "count": 20, "availability_internal": 0.6 }
    ],
    "critical_risks": [
      { "role": "VP Engineering", "critical_level": "critical", "successors_ready": 0, "action": "URGENT DEVELOPMENT" }
    ]
  }
}
```

**2. GET `//api/workforce-planning/critical-positions`**

```php
// Query params: scenario_id, organization_id, include_successors=true
// Response
{
  "data": [
    {
      "id": 1,
      "role": { "id": 5, "name": "VP Engineering" },
      "department": "Engineering",
      "criticality_level": "critical",
      "criticality_score": 95,   // 0-100
      "impact_if_vacant": "Paraliza todas las decisiones técnicas y roadmap",
      "replacement_time_months": 6,
      "successors": {
        "ready_now": 0,
        "ready_12m": 1,
        "ready_24m": 2,
        "not_ready": 0
      },
      "risk_status": "HIGH",
      "recommended_action": "Immediate succession planning for Ready-12m candidate"
    }
  ],
  "summary": {
    "total_critical_positions": 8,
    "positions_with_ready_successor": 2,
    "positions_without_successor": 6,
    "high_risk_count": 4,
    "medium_risk_count": 3,
    "low_risk_count": 1
  }
}
```

#### Frontend - Componente Vue (1)

**Archivo:** `/resources/js/pages/WorkforcePlanning/ExecutiveDashboard.vue` (350+ líneas)

```vue
<template>
  <div class="executive-dashboard">
    <!-- TAB 1: OVERVIEW -->
    <v-tabs v-model="activeTab">
      <v-tab value="overview">
        <v-icon start>mdi-chart-line</v-icon>
        Growth Simulation
      </v-tab>
      <v-tab value="critical">
        <v-icon start>mdi-alert-circle</v-icon>
        Critical Positions ({{ criticalPositionsCount }})
      </v-tab>
    </v-tabs>

    <v-window v-model="activeTab">
      <!-- TAB 1: Growth Simulation -->
      <v-window-item value="overview">
        <!-- Inputs para simulación -->
        <v-card class="mb-4">
          <v-card-title>Growth Scenario Simulator</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="3">
                <v-text-field
                  v-model.number="simulation.growth_percentage"
                  label="Growth %"
                  type="number"
                  suffix="%"
                  @change="runSimulation"
                />
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="simulation.horizon_months"
                  :items="[12, 18, 24, 36]"
                  label="Horizon (months)"
                  @change="runSimulation"
                />
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field
                  v-model.number="simulation.external_hiring_ratio"
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
          </v-card-text>
        </v-card>

        <!-- Resultados visuales -->
        <v-row>
          <!-- Headcount Projection Card -->
          <v-col cols="12" md="6">
            <v-card>
              <v-card-title>Headcount Projection</v-card-title>
              <v-card-text>
                <chart-headcount-projection
                  :current="results.current_headcount"
                  :projected="results.projected_headcount"
                  :by-department="results.by_department"
                />
              </v-card-text>
            </v-card>
          </v-col>

          <!-- Skills Availability Card -->
          <v-col cols="12" md="6">
            <v-card>
              <v-card-title>Skills Availability Gap</v-card-title>
              <v-card-text>
                <chart-skills-gap :skills-needed="results.skills_needed" />
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Critical Risks Card -->
        <v-card class="mt-4">
          <v-card-title>Critical Risks in Expansion</v-card-title>
          <v-card-text>
            <table-critical-risks :risks="results.critical_risks" />
          </v-card-text>
        </v-card>
      </v-window-item>

      <!-- TAB 2: Critical Positions Heatmap -->
      <v-window-item value="critical">
        <v-card>
          <v-card-title>Critical Positions & Succession Risk</v-card-title>
          <v-card-subtitle>
            {{ criticalPositionsCount }} roles críticos identificados
          </v-card-subtitle>
          <v-card-text>
            <!-- Heatmap de riesgos -->
            <heatmap-critical-positions
              :positions="criticalPositions"
              :departments="departments"
            />

            <!-- Tabla detallada -->
            <v-divider class="my-4" />
            <h3 class="mb-3">Detailed Succession Status</h3>
            <table-succession-status :positions="criticalPositions" />
          </v-card-text>
        </v-card>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useApi } from "@/composables/useApi";

const { get, post } = useApi();
const activeTab = ref("overview");
const scenarioId = ref(1);

// Simulation inputs
const simulation = ref({
  growth_percentage: 25,
  horizon_months: 24,
  external_hiring_ratio: 30,
  retention_target: 95,
});

// Results
const results = ref({
  current_headcount: 0,
  projected_headcount: 0,
  net_growth: 0,
  by_department: [],
  skills_needed: [],
  critical_risks: [],
});

const criticalPositions = ref([]);

const criticalPositionsCount = computed(() => criticalPositions.value.length);

// Methods
const runSimulation = async () => {
  try {
    const response = await post(
      `//api/workforce-planning/scenarios/${scenarioId.value}/simulate-growth`,
      simulation.value,
    );
    results.value = response.data.simulation;
  } catch (error) {
    console.error("Simulation failed:", error);
  }
};

const loadCriticalPositions = async () => {
  try {
    const response = await get(`//api/workforce-planning/critical-positions`, {
      scenario_id: scenarioId.value,
    });
    criticalPositions.value = response.data.data;
  } catch (error) {
    console.error("Failed to load critical positions:", error);
  }
};

const departments = computed(() => {
  return Array.from(new Set(criticalPositions.value.map((p) => p.department)));
});

onMounted(() => {
  loadCriticalPositions();
  runSimulation();
});
</script>
```

#### Sub-componentes Necesarios (3)

1. **ChartHeadcountProjection.vue** - Gráfico de barras de headcount actual vs proyectado
2. **ChartSkillsGap.vue** - Gráfico de disponibilidad de skills (stacked bar)
3. **HeatmapCriticalPositions.vue** - Heatmap interactivo de criticidad por rol/departamento

### 📊 Datos Clave a Retornar

```
✅ Headcount projection by department
✅ Skills gap analysis (demand vs availability)
✅ Critical position identification
✅ Successor readiness assessment
✅ Risk scoring by role
✅ Recommended actions
```

### ✅ Checklist

- [ ] Crear 2 endpoints en WorkforcePlanningController
- [ ] Crear migraciones para tabla `critical_positions` (si no existe)
- [ ] Crear componente ExecutiveDashboard.vue
- [ ] Crear 3 sub-componentes (charts + heatmap)
- [ ] Agregar ruta web: `/workforce-planning/executive`
- [ ] Testear con Postman
- [ ] Testear flujo completo en navegador

**Tiempo estimado:** 16-20 horas

---

## 💰 COMPONENTE 2: CALCULADORA ROI BUILD VS BUY

### 📌 Casos de Uso Cubiertos

- ✅ Optimización del presupuesto OPEX (CFO)
- ✅ Comparación costo Build (academia interna) vs Buy (contratación externa)
- ✅ ROI analysis + Time-to-Productivity impacto

### 🏗️ Especificación Técnica

#### Backend - Nuevos Endpoints (2)

**1. POST `//api/workforce-planning/roi-calculator/calculate`**

```php
// Request
{
  "scenario_id": 1,
  "strategy_type": "build",  // "build" | "buy" | "borrow"
  "target_skills": [1, 2, 3],  // Skill IDs
  "headcount_needed": 10,
  "recruitment_cost_per_hire": 15000,
  "training_cost_per_person": 8000,
  "training_duration_months": 6,
  "internal_candidate_pool": 5,  // Cuántos pueden ser reconvertidos
  "salary_external_annual": 85000,
  "salary_internal_annual": 75000
}

// Response
{
  "roi_comparison": {
    "build": {
      "total_cost": 85000,
      "cost_breakdown": {
        "training": 80000,
        "consultant_support": 5000,
        "opportunity_cost": 0
      },
      "time_to_productivity_months": 6,
      "risk_level": "medium",
      "success_probability": 0.85,
      "net_benefit_12m": 120000,
      "roi_percentage": 141
    },
    "buy": {
      "total_cost": 215000,
      "cost_breakdown": {
        "recruitment": 150000,  // 10 × $15k
        "onboarding": 30000,
        "salaries_first_year": 850000  // 10 × $85k
      },
      "time_to_productivity_months": 3,
      "risk_level": "low",
      "success_probability": 0.95,
      "net_benefit_12m": 950000,
      "roi_percentage": 442
    },
    "borrow": {
      "total_cost": 120000,
      "cost_breakdown": {
        "freelance_rates": 120000,
        "management_overhead": 0
      },
      "time_to_productivity_months": 1,
      "risk_level": "high",
      "success_probability": 0.7,
      "net_benefit_12m": 0,
      "roi_percentage": -100
    }
  },
  "recommendation": {
    "strategy": "build",
    "reasoning": "ROI 141% vs Buy 442%, pero Build ahorra $130k en salarios anuales post-año 1. Buy es mejor para corto plazo, Build para largo plazo.",
    "hybrid_approach": "Build 5 internos + Buy 5 externos para mitigar riesgo"
  }
}
```

**2. GET `//api/workforce-planning/roi-calculator/scenarios`**

```php
// Query params: scenario_id
// Response: Lista histórica de cálculos ROI para comparar
{
  "data": [
    {
      "id": 1,
      "created_at": "2026-01-05T10:00:00Z",
      "strategy_type": "build",
      "headcount_needed": 10,
      "roi_percentage": 141,
      "total_cost": 85000,
      "net_benefit_12m": 120000
    }
  ]
}
```

#### Frontend - Componente Vue (1)

**Archivo:** `/resources/js/pages/WorkforcePlanning/RoiCalculator.vue` (400+ líneas)

```vue
<template>
  <div class="roi-calculator">
    <v-card>
      <v-card-title>ROI Calculator: Build vs Buy vs Borrow</v-card-title>
      <v-card-subtitle>
        Compare cost and time-to-productivity for different talent acquisition
        strategies
      </v-card-subtitle>

      <!-- INPUT SECTION -->
      <v-card-text class="bg-blue-50">
        <h3 class="mb-4">Configuration</h3>
        <v-row>
          <v-col cols="12" md="4">
            <v-select
              v-model="config.strategy_type"
              :items="['build', 'buy', 'borrow', 'all']"
              label="Strategy Type"
              @change="resetResults"
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field
              v-model.number="config.headcount_needed"
              label="Headcount Needed"
              type="number"
              @change="resetResults"
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field
              v-model.number="config.training_duration_months"
              label="Training Duration (months)"
              type="number"
              @change="resetResults"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="config.recruitment_cost_per_hire"
              label="Recruitment Cost/Hire"
              type="number"
              prefix="$"
              @change="resetResults"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="config.training_cost_per_person"
              label="Training Cost/Person"
              type="number"
              prefix="$"
              @change="resetResults"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="config.salary_external_annual"
              label="External Salary/Year"
              type="number"
              prefix="$"
              @change="resetResults"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="config.salary_internal_annual"
              label="Internal Salary/Year"
              type="number"
              prefix="$"
              @change="resetResults"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" md="6">
            <v-text-field
              v-model.number="config.internal_candidate_pool"
              label="Internal Candidates Available"
              type="number"
              @change="resetResults"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-btn color="primary" size="large" @click="calculate" block>
              <v-icon start>mdi-calculator</v-icon>
              Calculate ROI
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>

      <!-- RESULTS SECTION -->
      <v-divider class="my-6" />

      <v-card-text v-if="results">
        <h3 class="mb-4">ROI Comparison Results</h3>

        <!-- Comparison Cards -->
        <v-row>
          <v-col
            cols="12"
            md="4"
            v-for="strategy in ['build', 'buy', 'borrow']"
            :key="strategy"
          >
            <comparison-card
              :strategy="strategy"
              :data="results.roi_comparison[strategy]"
              :recommended="results.recommendation.strategy === strategy"
            />
          </v-col>
        </v-row>

        <!-- Recommendation Box -->
        <v-alert
          :type="
            results.recommendation.strategy === 'build' ? 'info' : 'warning'
          "
          class="mt-6"
        >
          <strong
            >Recommendation:
            {{ results.recommendation.strategy.toUpperCase() }}</strong
          >
          <p class="mt-2">{{ results.recommendation.reasoning }}</p>
          <p v-if="results.recommendation.hybrid_approach" class="mt-2">
            <strong>Hybrid Approach:</strong>
            {{ results.recommendation.hybrid_approach }}
          </p>
        </v-alert>

        <!-- Detailed Comparison Chart -->
        <v-row class="mt-6">
          <v-col cols="12">
            <chart-roi-comparison :data="results.roi_comparison" />
          </v-col>
        </v-row>

        <!-- Cost Breakdown -->
        <v-row class="mt-4">
          <v-col cols="12" md="6">
            <chart-cost-breakdown :data="results.roi_comparison" />
          </v-col>
          <v-col cols="12" md="6">
            <chart-time-to-productivity :data="results.roi_comparison" />
          </v-col>
        </v-row>

        <!-- Save Calculation -->
        <v-card class="mt-6 bg-grey-100">
          <v-card-text>
            <v-btn color="success" @click="saveCalculation" block>
              <v-icon start>mdi-content-save</v-icon>
              Save Calculation for Future Reference
            </v-btn>
          </v-card-text>
        </v-card>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useApi } from "@/composables/useApi";

const { post } = useApi();

const config = ref({
  scenario_id: 1,
  strategy_type: "all",
  headcount_needed: 10,
  recruitment_cost_per_hire: 15000,
  training_cost_per_person: 8000,
  training_duration_months: 6,
  internal_candidate_pool: 5,
  salary_external_annual: 85000,
  salary_internal_annual: 75000,
});

const results = ref(null);

const calculate = async () => {
  try {
    const response = await post(
      "//api/workforce-planning/roi-calculator/calculate",
      config.value,
    );
    results.value = response.data;
  } catch (error) {
    console.error("ROI calculation failed:", error);
  }
};

const resetResults = () => {
  results.value = null;
};

const saveCalculation = async () => {
  // POST to save calculation for audit trail
  console.log("Calculation saved");
};
</script>
```

#### Sub-componentes Necesarios (4)

1. **ComparisonCard.vue** - Card para Build/Buy/Borrow con métricas clave
2. **ChartRoiComparison.vue** - Gráfico de barras/línea de ROI %
3. **ChartCostBreakdown.vue** - Gráfico de pastel de costos por estrategia
4. **ChartTimeToProductivity.vue** - Timeline de impacto en tiempo

### 📊 Datos Clave a Retornar

```
✅ Total cost by strategy (Build/Buy/Borrow)
✅ Cost breakdown (recruitment, training, salaries, overhead)
✅ Time-to-productivity
✅ Risk level & success probability
✅ Net benefit at 12 months
✅ ROI percentage
✅ Recommendation with reasoning
✅ Hybrid approach suggestion
```

### ✅ Checklist

- [ ] Crear 2 endpoints en WorkforcePlanningController
- [ ] Crear RoiCalculatorService con algoritmo de cálculo
- [ ] Crear componente RoiCalculator.vue
- [ ] Crear 4 sub-componentes (cards + charts)
- [ ] Agregar ruta web: `/workforce-planning/roi-calculator`
- [ ] Testear con múltiples escenarios
- [ ] Validar cálculos de costos

**Tiempo estimado:** 12-16 horas

---

## 🎯 COMPONENTE 3: ASIGNADOR DE ESTRATEGIAS (BUILD-BUY-BORROW-BOT)

### 📌 Casos de Uso Cubiertos

- ✅ Diseño del portafolio de estrategias (CHRO)
- ✅ Asignación de estrategias a cada brecha detectada
- ✅ Plan maestro consolidado

### 🏗️ Especificación Técnica

#### Backend - Nuevos Endpoints (3)

**1. GET `//api/workforce-planning/scenarios/{id}/gaps-for-assignment`**

```php
// Query params: department_id, criticality_level
// Response: Brechas listas para asignar estrategia
{
  "data": [
    {
      "id": 1,
      "role": { "id": 5, "name": "Senior Developer" },
      "department": "Engineering",
      "gap_type": "skill",  // "skill" | "headcount" | "succession"
      "gap_description": "3 people need React skills",
      "headcount_gap": 3,
      "affected_people": 10,
      "criticality": "high",
      "estimated_cost_to_fill": 45000,
      "timeline_months": 6,
      "internal_capacity": 5,
      "current_strategy": null,
      "recommended_strategies": ["build", "borrow"]
    }
  ]
}
```

**2. POST `//api/workforce-planning/strategies/assign`**

```php
// Request
{
  "gap_id": 1,
  "strategy": "build",  // "build" | "buy" | "borrow" | "bot"
  "reasoning": "React is core skill, invest in internal development",
  "assigned_by": 1,
  "timeline_months": 6,
  "budget_allocated": 45000,
  "success_metrics": [
    { "metric": "headcount_upskilled", "target": 3, "unit": "people" },
    { "metric": "skill_level_improvement", "target": 2, "unit": "levels" }
  ]
}

// Response
{
  "id": 1,
  "gap_id": 1,
  "strategy": "build",
  "status": "active",
  "created_at": "2026-01-05T10:00:00Z"
}
```

**3. GET `//api/workforce-planning/strategies/portfolio/{scenario_id}`**

```php
// Response: Portafolio consolidado de todas las estrategias
{
  "scenario_id": 1,
  "portfolio": {
    "build": {
      "count": 15,
      "gaps": [...],
      "total_cost": 150000,
      "timeline_months": 8,
      "people_impacted": 45
    },
    "buy": {
      "count": 8,
      "gaps": [...],
      "total_cost": 250000,
      "timeline_months": 3,
      "people_impacted": 12
    },
    "borrow": {
      "count": 5,
      "gaps": [...],
      "total_cost": 80000,
      "timeline_months": 1,
      "people_impacted": 8
    },
    "bot": {
      "count": 3,
      "gaps": [...],
      "total_cost": 50000,
      "timeline_months": 12,
      "people_impacted": 0
    }
  },
  "summary": {
    "total_gaps": 31,
    "total_investment": 530000,
    "total_people_impacted": 65,
    "strategic_alignment": 0.87
  }
}
```

#### Frontend - Componente Vue (1)

**Archivo:** `/resources/js/pages/WorkforcePlanning/StrategyAssigner.vue` (450+ líneas)

```vue
<template>
  <div class="strategy-assigner">
    <!-- STEP 1: View Gaps to Assign -->
    <v-stepper v-model="step">
      <v-stepper-header>
        <v-stepper-header-item step="1" :complete="step > 1">
          Identify Gaps
        </v-stepper-header-item>
        <v-divider></v-divider>
        <v-stepper-header-item step="2" :complete="step > 2">
          Assign Strategies
        </v-stepper-header-item>
        <v-divider></v-divider>
        <v-stepper-header-item step="3" :complete="step > 3">
          Review Portfolio
        </v-stepper-header-item>
      </v-stepper-header>

      <v-stepper-window>
        <!-- STEP 1: View Gaps -->
        <v-stepper-window-item step="1">
          <v-card>
            <v-card-title>Step 1: Gaps to Address</v-card-title>
            <v-card-text>
              <v-row class="mb-4">
                <v-col cols="12" md="4">
                  <v-select
                    v-model="filters.department_id"
                    :items="departments"
                    item-title="name"
                    item-value="id"
                    label="Filter by Department"
                    @change="loadGaps"
                  />
                </v-col>
                <v-col cols="12" md="4">
                  <v-select
                    v-model="filters.criticality_level"
                    :items="['critical', 'high', 'medium', 'low']"
                    label="Filter by Criticality"
                    @change="loadGaps"
                  />
                </v-col>
                <v-col cols="12" md="4">
                  <v-btn color="primary" @click="loadGaps" block>
                    <v-icon start>mdi-reload</v-icon>
                    Load Gaps
                  </v-btn>
                </v-col>
              </v-row>

              <table-gaps-to-assign
                :gaps="gaps"
                :selected="selectedGaps"
                @select="selectedGaps = $event"
              />
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="primary"
                @click="step = 2"
                :disabled="selectedGaps.length === 0"
              >
                Next: Assign Strategies
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-window-item>

        <!-- STEP 2: Assign Strategies -->
        <v-stepper-window-item step="2">
          <v-card>
            <v-card-title>Step 2: Assign Strategy to Each Gap</v-card-title>
            <v-card-text>
              <div
                v-for="(gap, idx) in selectedGaps"
                :key="gap.id"
                class="mb-6"
              >
                <v-card variant="outlined" class="pa-4">
                  <v-row>
                    <v-col cols="12" md="6">
                      <h4>{{ gap.role.name }} - {{ gap.gap_description }}</h4>
                      <p class="text-caption">
                        Criticality: <strong>{{ gap.criticality }}</strong>
                      </p>
                      <p class="text-caption">
                        Recommended:
                        {{
                          gap.recommended_strategies.join(", ").toUpperCase()
                        }}
                      </p>
                    </v-col>

                    <v-col cols="12" md="6">
                      <v-select
                        v-model="assignments[gap.id]"
                        :items="strategies"
                        item-title="label"
                        item-value="value"
                        label="Select Strategy"
                      >
                        <template #item="{ props, item }">
                          <v-list-item v-bind="props">
                            <template #prepend>
                              <v-icon :color="item.raw.color">{{
                                item.raw.icon
                              }}</v-icon>
                            </template>
                            <v-list-item-title>{{
                              item.raw.label
                            }}</v-list-item-title>
                            <v-list-item-subtitle>
                              {{ item.raw.description }}
                            </v-list-item-subtitle>
                          </v-list-item>
                        </template>
                      </v-select>

                      <v-textarea
                        v-model="reasonings[gap.id]"
                        label="Reasoning"
                        placeholder="Why choose this strategy?"
                        rows="2"
                        class="mt-2"
                      />
                    </v-col>
                  </v-row>
                </v-card>
              </div>
            </v-card-text>

            <v-card-actions>
              <v-btn @click="step = 1">Back</v-btn>
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="saveAssignments">
                Save & Review Portfolio
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-window-item>

        <!-- STEP 3: Review Portfolio -->
        <v-stepper-window-item step="3">
          <v-card>
            <v-card-title>Step 3: Strategy Portfolio Summary</v-card-title>
            <v-card-text>
              <!-- Portfolio Cards -->
              <v-row>
                <v-col
                  cols="12"
                  md="3"
                  v-for="strategy in ['build', 'buy', 'borrow', 'bot']"
                  :key="strategy"
                >
                  <portfolio-card
                    :strategy="strategy"
                    :data="portfolio[strategy]"
                  />
                </v-col>
              </v-row>

              <!-- Summary Metrics -->
              <v-divider class="my-6" />
              <v-row>
                <v-col cols="12">
                  <h3>Overall Strategy Impact</h3>
                  <v-row class="mt-4">
                    <v-col cols="12" md="3">
                      <metric-card
                        title="Total Gaps"
                        :value="portfolio.total_gaps"
                        icon="mdi-list-status"
                      />
                    </v-col>
                    <v-col cols="12" md="3">
                      <metric-card
                        title="Total Investment"
                        :value="`$${portfolio.total_investment | currency}`"
                        icon="mdi-currency-usd"
                      />
                    </v-col>
                    <v-col cols="12" md="3">
                      <metric-card
                        title="People Impacted"
                        :value="portfolio.total_people_impacted"
                        icon="mdi-account-multiple"
                      />
                    </v-col>
                    <v-col cols="12" md="3">
                      <metric-card
                        title="Strategic Alignment"
                        :value="`${(portfolio.strategic_alignment * 100).toFixed(0)}%`"
                        icon="mdi-target"
                      />
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>

              <!-- Distribution Chart -->
              <v-row class="mt-6">
                <v-col cols="12">
                  <chart-strategy-distribution :data="portfolio" />
                </v-col>
              </v-row>

              <!-- Export Options -->
              <v-divider class="my-6" />
              <v-row>
                <v-col cols="12">
                  <v-btn color="success" @click="exportPortfolio">
                    <v-icon start>mdi-download</v-icon>
                    Export Portfolio (PDF)
                  </v-btn>
                  <v-btn color="info" @click="approvePortfolio" class="ml-2">
                    <v-icon start>mdi-check-circle</v-icon>
                    Approve Portfolio
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-stepper-window-item>
      </v-stepper-window>
    </v-stepper>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useApi } from "@/composables/useApi";

const { get, post } = useApi();

const step = ref(1);
const scenarioId = ref(1);

const filters = ref({
  department_id: null,
  criticality_level: null,
});

const gaps = ref([]);
const selectedGaps = ref([]);
const assignments = ref({});
const reasonings = ref({});

const departments = ref([]);
const portfolio = ref(null);

const strategies = [
  {
    value: "build",
    label: "Build",
    icon: "mdi-hammer",
    color: "blue",
    description: "Develop skills internally through training",
  },
  {
    value: "buy",
    label: "Buy",
    icon: "mdi-shopping-outline",
    color: "orange",
    description: "Hire external talent",
  },
  {
    value: "borrow",
    label: "Borrow",
    icon: "mdi-handshake",
    color: "purple",
    description: "Contract freelancers or outsource",
  },
  {
    value: "bot",
    label: "Bot",
    icon: "mdi-robot",
    color: "green",
    description: "Automate through technology",
  },
];

const loadGaps = async () => {
  try {
    const response = await get(
      `//api/workforce-planning/scenarios/${scenarioId.value}/gaps-for-assignment`,
      filters.value,
    );
    gaps.value = response.data.data;
  } catch (error) {
    console.error("Failed to load gaps:", error);
  }
};

const saveAssignments = async () => {
  try {
    for (const gap of selectedGaps.value) {
      await post("//api/workforce-planning/strategies/assign", {
        gap_id: gap.id,
        strategy: assignments.value[gap.id],
        reasoning: reasonings.value[gap.id],
        assigned_by: 1,
      });
    }

    // Load portfolio
    const response = await get(
      `//api/workforce-planning/strategies/portfolio/${scenarioId.value}`,
    );
    portfolio.value = response.data.portfolio;

    step.value = 3;
  } catch (error) {
    console.error("Failed to save assignments:", error);
  }
};

const exportPortfolio = () => {
  // Trigger PDF export
  console.log("Exporting portfolio as PDF");
};

const approvePortfolio = async () => {
  // Mark portfolio as approved
  console.log("Portfolio approved");
};

onMounted(() => {
  loadGaps();
});
</script>
```

#### Sub-componentes Necesarios (4)

1. **TableGapsToAssign.vue** - Tabla de brechas con checkbox
2. **PortfolioCard.vue** - Card para Build/Buy/Borrow/Bot con métricas
3. **ChartStrategyDistribution.vue** - Gráfico de distribución de estrategias
4. **MetricCard.vue** - Card pequeño para métricas clave

### 📊 Datos Clave a Retornar

```
✅ List of unassigned gaps by department/criticality
✅ Recommended strategies per gap
✅ Strategy assignments with reasoning
✅ Portfolio summary (count, cost, timeline, people impacted)
✅ Strategic alignment score
✅ Distribution visualization (pie/donut)
✅ Export capability (PDF)
```

### ✅ Checklist

- [ ] Crear 3 endpoints en WorkforcePlanningController
- [ ] Crear StrategyService con lógica de asignación
- [ ] Crear migraciones para tabla `strategy_assignments`
- [ ] Crear componente StrategyAssigner.vue
- [ ] Crear 4 sub-componentes (table + cards + chart)
- [ ] Agregar ruta web: `/workforce-planning/strategy-assigner`
- [ ] Implementar lógica de PDF export
- [ ] Testear flujo completo (3 steps)

**Tiempo estimado:** 10-14 horas

---

## 📅 CRONOGRAMA ESTIMADO

### Día 1 (5 Enero - 8 horas)

- **Mañana (9:00-13:00):** Setup de endpoints Backend + validaciones
  - ✅ Componente 1: 2 endpoints
  - ✅ Componente 2: 2 endpoints
  - ✅ Componente 3: 3 endpoints
- **Tarde (14:00-17:00):** Componente 1 - Dashboard Ejecutivo
  - ✅ ExecutiveDashboard.vue
  - ✅ Sub-componentes (charts)

### Día 2 (6 Enero - 8 horas)

- **Mañana (9:00-13:00):** Componente 2 - Calculadora ROI
  - ✅ RoiCalculator.vue
  - ✅ Sub-componentes (comparison cards, charts)
- **Tarde (14:00-17:00):** Componente 3 - Asignador de Estrategias (Parte 1)
  - ✅ StrategyAssigner.vue (Step 1 & 2)

### Día 3 (7 Enero - 4 horas)

- **Mañana (9:00-13:00):** Componente 3 - Asignador de Estrategias (Parte 2)
  - ✅ StrategyAssigner.vue (Step 3)
  - ✅ Sub-componentes (portfolio, distribution)
- **Testing & Pulido:**
  - ✅ Validación en todos los endpoints
  - ✅ Testeo de flujos completos
  - ✅ Ajustes de UI/UX

---

## 🎯 MÉTRICAS DE ÉXITO

### Componente 1: Dashboard Ejecutivo

- ✅ CEO puede simular escenarios en < 2 minutos
- ✅ Visualización clara de puestos críticos y riesgo
- ✅ Identificación automática de brechas por rol
- ✅ Dashboard cargue en < 3 segundos

### Componente 2: Calculadora ROI

- ✅ CFO compara estrategias en < 5 minutos
- ✅ ROI % generado automáticamente
- ✅ Recomendación clara (Build vs Buy vs Borrow)
- ✅ Cálculos auditables y validados

### Componente 3: Asignador de Estrategias

- ✅ CHRO asigna estrategias en < 10 minutos
- ✅ Portafolio consolidado generado automáticamente
- ✅ Export a PDF con signature
- ✅ Trazabilidad completa (audit trail)

---

## 🚀 PRÓXIMOS PASOS

1. **Iniciar Componente 1** - Crear endpoints Backend
2. **Crear estructura de componentes** - Scaffold Vue files
3. **Implementar Services** - RoiCalculatorService, StrategyService
4. **Testing progresivo** - Cada componente con Postman antes de Frontend
5. **Documentación** - Actualizar memories.md con nuevos endpoints

---

## 📞 REFERENCIAS

- **CasosDeUso.md** - Requerimientos completos de actores
- **WORKFORCE_PLANNING_ESPECIFICACION.md** - Especificación técnica existente
- **memories.md** - Arquitectura general y patrones

---

**Status:** 🎯 PLAN CREADO - LISTO PARA EJECUTAR  
**Fecha de Creación:** 5 de Enero de 2026  
**Próxima Actualización:** Post-implementación de Componente 1

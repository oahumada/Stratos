<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

// State
const vuetifyTheme = useVuetifyTheme();
const _loading = ref(false);
const selectedPeriod = ref('month');
const dashboardData = ref({
  talentMetrics: {
    totalEmployees: 124,
    employeesWithGaps: 87,
    employeesReadyForPromotion: 23,
    newHiresLast30Days: 8,
  },
  marketplaceMetrics: {
    openPositions: 12,
    candidatesExcellent: 7,
    candidatesGood: 12,
    candidatesModerate: 5,
    candidatesLow: 3,
    positionsWithoutCandidates: 2,
  },
  recruitmentMetrics: {
    averageTimeToHire: 32,
    internalPlacementRate: 65,
    externalHires: 5,
    internalPromotions: 3,
    retentionRate: 92,
  },
  developmentMetrics: {
    employeesInDevelopment: 45,
    completedCourses: 18,
    skillsCovered: 156,
    criticalGaps: 34,
    developmentPlansActive: 12,
  },
  riskMetrics: {
    talentAtRisk: 5,
    vacanciesUrgent: 2,
    employeesUnderperforming: 3,
    skillsDeprecating: 7,
  },
});

// Computed properties for KPIs
const keyMetrics = computed(() => [
  {
    title: 'Empleados Totales',
    value: dashboardData.value.talentMetrics.totalEmployees,
    icon: 'mdi-account-group',
    color: 'primary',
    subtitle: 'Activos en la organización',
  },
  {
    title: 'Posiciones Abiertas',
    value: dashboardData.value.marketplaceMetrics.openPositions,
    icon: 'mdi-briefcase-outline',
    color: 'warning',
    subtitle: 'Vacantes por cubrir',
  },
  {
    title: 'Tasa de Colocación Interna',
    value: `${dashboardData.value.recruitmentMetrics.internalPlacementRate}%`,
    icon: 'mdi-trending-up',
    color: 'success',
    subtitle: 'De vacantes cubiertas internamente',
  },
  {
    title: 'Retención',
    value: `${dashboardData.value.recruitmentMetrics.retentionRate}%`,
    icon: 'mdi-heart',
    color: 'error',
    subtitle: 'Tasa de retención anual',
  },
]);

const talentReadiness = computed(() => [
  {
    category: 'Listos para Promoción',
    value: dashboardData.value.talentMetrics.employeesReadyForPromotion,
    color: 'success',
    icon: 'mdi-trending-up',
  },
  {
    category: 'En Desarrollo',
    value: dashboardData.value.developmentMetrics.employeesInDevelopment,
    color: 'warning',
    icon: 'mdi-school',
  },
  {
    category: 'Con Gaps Críticos',
    value: dashboardData.value.talentMetrics.employeesWithGaps,
    color: 'error',
    icon: 'mdi-alert-circle',
  },
]);

const candidateDistribution = computed(() => [
  {
    label: 'Excelente (≥80%)',
    value: dashboardData.value.marketplaceMetrics.candidatesExcellent,
    color: 'success',
    percentage: Math.round((dashboardData.value.marketplaceMetrics.candidatesExcellent / 
      (dashboardData.value.marketplaceMetrics.candidatesExcellent +
       dashboardData.value.marketplaceMetrics.candidatesGood +
       dashboardData.value.marketplaceMetrics.candidatesModerate +
       dashboardData.value.marketplaceMetrics.candidatesLow)) * 100),
  },
  {
    label: 'Bueno (70-79%)',
    value: dashboardData.value.marketplaceMetrics.candidatesGood,
    color: 'info',
    percentage: Math.round((dashboardData.value.marketplaceMetrics.candidatesGood / 
      (dashboardData.value.marketplaceMetrics.candidatesExcellent +
       dashboardData.value.marketplaceMetrics.candidatesGood +
       dashboardData.value.marketplaceMetrics.candidatesModerate +
       dashboardData.value.marketplaceMetrics.candidatesLow)) * 100),
  },
  {
    label: 'Moderado (50-69%)',
    value: dashboardData.value.marketplaceMetrics.candidatesModerate,
    color: 'warning',
    percentage: Math.round((dashboardData.value.marketplaceMetrics.candidatesModerate / 
      (dashboardData.value.marketplaceMetrics.candidatesExcellent +
       dashboardData.value.marketplaceMetrics.candidatesGood +
       dashboardData.value.marketplaceMetrics.candidatesModerate +
       dashboardData.value.marketplaceMetrics.candidatesLow)) * 100),
  },
  {
    label: 'Bajo (40-49%)',
    value: dashboardData.value.marketplaceMetrics.candidatesLow,
    color: 'error',
    percentage: Math.round((dashboardData.value.marketplaceMetrics.candidatesLow / 
      (dashboardData.value.marketplaceMetrics.candidatesExcellent +
       dashboardData.value.marketplaceMetrics.candidatesGood +
       dashboardData.value.marketplaceMetrics.candidatesModerate +
       dashboardData.value.marketplaceMetrics.candidatesLow)) * 100),
  },
]);

const riskIndicators = computed(() => [
  {
    title: 'Talento en Riesgo',
    value: dashboardData.value.riskMetrics.talentAtRisk,
    icon: 'mdi-alert-octagon',
    color: 'error',
    trend: -2,
    subtitle: 'Empleados en riesgo de salida',
  },
  {
    title: 'Vacantes Urgentes',
    value: dashboardData.value.riskMetrics.vacanciesUrgent,
    icon: 'mdi-clock-alert',
    color: 'error',
    trend: 1,
    subtitle: 'Posiciones críticas sin candidatos',
  },
  {
    title: 'Desempeño Bajo',
    value: dashboardData.value.riskMetrics.employeesUnderperforming,
    icon: 'mdi-trending-down',
    color: 'warning',
    trend: 0,
    subtitle: 'Requieren intervención',
  },
  {
    title: 'Skills Depreciadas',
    value: dashboardData.value.riskMetrics.skillsDeprecating,
    icon: 'mdi-progress-clock',
    color: 'warning',
    trend: 3,
    subtitle: 'Tecnologías en declive',
  },
]);

const _getStatusColor = (percentage: number): string => {
  if (percentage >= 80) return 'success';
  if (percentage >= 60) return 'warning';
  return 'error';
};

const getTrendIcon = (trend: number): string => {
  if (trend > 0) return 'mdi-trending-up';
  if (trend < 0) return 'mdi-trending-down';
  return 'mdi-minus';
};

const getTrendColor = (trend: number): string => {
  if (trend > 0) return 'error'; // Más riesgo es malo
  if (trend < 0) return 'success'; // Menos riesgo es bueno
  return 'grey';
};

const headerGradient = computed(() => {
  const theme = vuetifyTheme.global.current.value;
  return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

// mark intentionally-unused bindings to satisfy linter during refactor
void _loading.value
void _getStatusColor

</script>

<template>
  <div class="pa-6">
    <!-- Header -->
    <div class="mb-6">
      <div
        class="d-flex align-center justify-space-between pa-6"
        :style="{ background: headerGradient }"
        style="border-radius: 12px;"
      >
        <div>
          <div class="text-overline text-white text-uppercase">Analytics</div>
          <h1 class="text-h4 font-weight-bold mb-1" style="color: white;">Dashboard de Talento</h1>
          <p class="text-body-2" style="color: rgba(255,255,255,0.85);">
            Visión integral de métricas de talento, marketplace y reclutamiento
          </p>
        </div>
      </div>
    </div>

    <!-- Period Selector -->
    <div class="mb-6">
      <v-card elevation="0" variant="outlined">
        <v-card-text class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <span class="text-body-2 font-weight-medium">Período:</span>
            <v-chip-group v-model="selectedPeriod" selected-class="text-primary">
              <v-chip value="week" variant="outlined" filter>Semana</v-chip>
              <v-chip value="month" variant="outlined" filter>Mes</v-chip>
              <v-chip value="quarter" variant="outlined" filter>Trimestre</v-chip>
              <v-chip value="year" variant="outlined" filter>Año</v-chip>
            </v-chip-group>
            <div class="text-caption text-medium-emphasis ml-4">
              ℹ️ Datos actualizados: Hoy a las 14:32
            </div>
          </div>
        </v-card-text>
      </v-card>
    </div>

    <!-- Key Metrics Row -->
    <div class="mb-6">
      <h2 class="text-h6 font-weight-bold mb-4">Indicadores Clave (KPIs)</h2>
      <v-row class="gap-4">
        <v-col v-for="metric in keyMetrics" :key="metric.title" cols="12" sm="6" md="3">
          <v-card elevation="0" variant="outlined" class="h-100">
            <v-card-text class="pa-6">
              <div class="d-flex align-center justify-space-between mb-4">
                <v-avatar :color="metric.color" size="40">
                  <v-icon :color="metric.color === 'error' ? 'white' : 'white'">{{ metric.icon }}</v-icon>
                </v-avatar>
                <v-chip size="small" :color="metric.color" variant="tonal">
                  {{ metric.color === 'primary' ? 'Info' : metric.color }}
                </v-chip>
              </div>
              <div class="text-h5 font-weight-bold mb-1">{{ metric.value }}</div>
              <div class="text-caption text-medium-emphasis">{{ metric.title }}</div>
              <div class="text-body-2 mt-2 text-grey">{{ metric.subtitle }}</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- Talent Readiness + Candidate Distribution Row -->
    <v-row class="gap-4 mb-6">
      <!-- Talent Readiness -->
      <v-col cols="12" md="6">
        <h2 class="text-h6 font-weight-bold mb-4">Estado del Talento</h2>
        <v-card elevation="0" variant="outlined">
          <v-card-text class="pa-6">
            <v-list class="pa-0">
              <template v-for="(item, index) in talentReadiness" :key="item.category">
                <v-list-item class="px-0 py-3">
                  <template #prepend>
                    <v-avatar :color="item.color" size="36" class="mr-3">
                      <v-icon size="20" color="white">{{ item.icon }}</v-icon>
                    </v-avatar>
                  </template>
                  <v-list-item-title class="font-weight-medium">{{ item.category }}</v-list-item-title>
                  <template #append>
                    <div class="text-h6 font-weight-bold">{{ item.value }}</div>
                  </template>
                </v-list-item>
                <v-divider v-if="index < talentReadiness.length - 1" class="my-2" />
              </template>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Candidate Distribution -->
      <v-col cols="12" md="6">
        <h2 class="text-h6 font-weight-bold mb-4">Distribución de Candidatos Marketplace</h2>
        <v-card elevation="0" variant="outlined">
          <v-card-text class="pa-6">
            <v-list class="pa-0">
              <template v-for="(item, index) in candidateDistribution" :key="item.label">
                <v-list-item class="px-0 py-4">
                  <template #prepend>
                    <div style="min-width: 40px" class="mr-3">
                      <v-chip size="small" :color="item.color" variant="flat" text-color="white">
                        {{ item.percentage }}%
                      </v-chip>
                    </div>
                  </template>
                  <v-list-item-title class="font-weight-medium">{{ item.label }}</v-list-item-title>
                  <template #append>
                    <div class="text-h6 font-weight-bold">{{ item.value }}</div>
                  </template>
                </v-list-item>
                <v-divider v-if="index < candidateDistribution.length - 1" class="my-2" />
              </template>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Risk Indicators -->
    <div class="mb-6">
      <h2 class="text-h6 font-weight-bold mb-4">Indicadores de Riesgo 🚨</h2>
      <v-row class="gap-4">
        <v-col v-for="risk in riskIndicators" :key="risk.title" cols="12" sm="6" md="3">
          <v-card elevation="0" variant="outlined" :style="{ borderTopColor: `var(--v-${risk.color})`, borderTopWidth: '4px' }">
            <v-card-text class="pa-6">
              <div class="d-flex align-center justify-space-between mb-3">
                <v-avatar :color="risk.color" size="36">
                  <v-icon size="20" color="white">{{ risk.icon }}</v-icon>
                </v-avatar>
                <v-icon :color="getTrendColor(risk.trend)" size="20">{{ getTrendIcon(risk.trend) }}</v-icon>
              </div>
              <div class="text-h5 font-weight-bold mb-1">{{ risk.value }}</div>
              <div class="text-caption text-medium-emphasis">{{ risk.title }}</div>
              <div class="text-body-2 mt-2 text-grey">{{ risk.subtitle }}</div>
              <div v-if="risk.trend !== 0" class="mt-2 text-caption" :class="`text-${getTrendColor(risk.trend)}`">
                {{ risk.trend > 0 ? '↑' : '↓' }} {{ Math.abs(risk.trend) }} vs período anterior
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- Development & Skills Section -->
    <v-row class="gap-4 mb-6">
      <v-col cols="12" md="6">
        <h2 class="text-h6 font-weight-bold mb-4">Desarrollo & Capacitación</h2>
        <v-card elevation="0" variant="outlined">
          <v-card-text class="pa-6">
            <div class="mb-6">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-body-2 font-weight-medium">Planes de Desarrollo Activos</span>
                <span class="text-h6 font-weight-bold">{{ dashboardData.developmentMetrics.developmentPlansActive }}</span>
              </div>
              <v-progress-linear :value="(dashboardData.developmentMetrics.developmentPlansActive / 20) * 100" color="success" height="8" />
            </div>
            <v-divider class="my-4" />
            <div class="mb-6">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-body-2 font-weight-medium">Cursos Completados</span>
                <span class="text-h6 font-weight-bold">{{ dashboardData.developmentMetrics.completedCourses }}</span>
              </div>
              <v-progress-linear :value="(dashboardData.developmentMetrics.completedCourses / 30) * 100" color="info" height="8" />
            </div>
            <v-divider class="my-4" />
            <div>
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-body-2 font-weight-medium">Cobertura de Skills</span>
                <span class="text-h6 font-weight-bold">{{ dashboardData.developmentMetrics.skillsCovered }} skills</span>
              </div>
              <v-progress-linear :value="(dashboardData.developmentMetrics.skillsCovered / 200) * 100" color="warning" height="8" />
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <h2 class="text-h6 font-weight-bold mb-4">Métricas de Reclutamiento</h2>
        <v-card elevation="0" variant="outlined">
          <v-card-text class="pa-6">
            <v-list class="pa-0">
              <v-list-item class="px-0 py-3">
                <v-list-item-title class="text-body-2">Tiempo Promedio de Contratación</v-list-item-title>
                <template #append>
                  <span class="font-weight-bold">{{ dashboardData.recruitmentMetrics.averageTimeToHire }} días</span>
                </template>
              </v-list-item>
              <v-divider class="my-2" />
              <v-list-item class="px-0 py-3">
                <v-list-item-title class="text-body-2">Contrataciones Internas</v-list-item-title>
                <template #append>
                  <span class="font-weight-bold">{{ dashboardData.recruitmentMetrics.internalPromotions }}</span>
                </template>
              </v-list-item>
              <v-divider class="my-2" />
              <v-list-item class="px-0 py-3">
                <v-list-item-title class="text-body-2">Contrataciones Externas</v-list-item-title>
                <template #append>
                  <span class="font-weight-bold">{{ dashboardData.recruitmentMetrics.externalHires }}</span>
                </template>
              </v-list-item>
              <v-divider class="my-2" />
              <v-list-item class="px-0 py-3">
                <v-list-item-title class="text-body-2">Nuevos en Últimos 30 Días</v-list-item-title>
                <template #append>
                  <v-chip size="small" color="success" variant="flat" text-color="white">
                    +{{ dashboardData.talentMetrics.newHiresLast30Days }}
                  </v-chip>
                </template>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Alerts & Recommendations -->
    <div class="mb-6">
      <h2 class="text-h6 font-weight-bold mb-4">Recomendaciones & Alertas</h2>
      <v-alert type="error" variant="tonal" class="mb-3" prominent>
        <template #prepend>
          <v-icon>mdi-alert-circle</v-icon>
        </template>
        <div class="font-weight-bold">{{ dashboardData.riskMetrics.talentAtRisk }} Empleados en Riesgo</div>
        <div class="text-body-2 mt-1">Se recomienda realizar retención inmediata con estos 5 empleados.</div>
      </v-alert>

      <v-alert type="warning" variant="tonal" class="mb-3">
        <template #prepend>
          <v-icon>mdi-information</v-icon>
        </template>
        <div class="font-weight-bold">{{ dashboardData.marketplaceMetrics.positionsWithoutCandidates }} Posiciones sin Candidatos Viables</div>
        <div class="text-body-2 mt-1">Iniciar búsqueda externa para: Senior Backend (urgente), Data Scientist (moderado).</div>
      </v-alert>

      <v-alert type="success" variant="tonal">
        <template #prepend>
          <v-icon>mdi-check-circle</v-icon>
        </template>
        <div class="font-weight-bold">{{ dashboardData.talentMetrics.employeesReadyForPromotion }} Candidatos para Promoción</div>
        <div class="text-body-2 mt-1">23 empleados con match ≥80% están listos para nuevos roles. Considera planes de carrera.</div>
      </v-alert>
    </div>

    <!-- Footer Note -->
    <v-card elevation="0" variant="outlined" color="blue-grey-50">
      <v-card-text class="pa-4">
        <v-icon size="16" class="mr-2">mdi-information-outline</v-icon>
        <span class="text-body-2 text-medium-emphasis">
          <strong>Nota:</strong> Este dashboard actualmente muestra datos mockados para demostrar el potencial visual e indicadores disponibles.
          En la próxima fase se conectará a datos reales del sistema.
        </span>
      </v-card-text>
    </v-card>
  </div>
</template>

<style scoped>
.gap-4 {
  gap: 16px;
}

.h-100 {
  height: 100%;
}
</style>

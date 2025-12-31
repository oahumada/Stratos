<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const { notify } = useNotification();

interface DashboardMetrics {
  total_peoples: number;
  total_roles: number;
  total_skills: number;
  avg_match_percentage: number;
  roles_at_risk: number;
  high_performers: number;
  skills_coverage: number;
  critical_gaps: number;
}

const metrics = ref<DashboardMetrics | null>(null);
const loading = ref(false);

const loadMetrics = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/dashboard/metrics');
    metrics.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load metrics', err);
    notify({
      type: 'error',
      text: 'Error loading dashboard metrics'
    });
  } finally {
    loading.value = false;
  }
};

const getMetricColor = (value: number, threshold: number = 70): string => {
  if (value >= threshold) return 'success';
  if (value >= 50) return 'warning';
  return 'error';
};

onMounted(() => {
  loadMetrics();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <v-container class="pa-4">
            <!-- Loading State -->
            <v-card v-if="loading" class="mb-4">
                <v-card-text class="text-center py-8">
                    <v-progress-circular indeterminate color="primary" />
                    <p class="mt-4">Loading metrics...</p>
                </v-card-text>
            </v-card>

            <!-- Metrics Grid -->
            <v-row v-if="metrics && !loading" no-gutters class="gap-4">
                <!-- Total Peoples -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <p class="text-caption text-grey mb-2">Total Peoples</p>
                                    <p class="text-h4 font-weight-bold">{{ metrics.total_peoples }}</p>
                                </div>
                                <v-icon size="48" color="info">mdi-account-multiple</v-icon>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Total Roles -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <p class="text-caption text-grey mb-2">Total Roles</p>
                                    <p class="text-h4 font-weight-bold">{{ metrics.total_roles }}</p>
                                </div>
                                <v-icon size="48" color="primary">mdi-briefcase</v-icon>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Total Skills -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <p class="text-caption text-grey mb-2">Total Skills</p>
                                    <p class="text-h4 font-weight-bold">{{ metrics.total_skills }}</p>
                                </div>
                                <v-icon size="48" color="secondary">mdi-lightbulb-multiple</v-icon>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Average Match -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <p class="text-caption text-grey mb-2">Avg Match %</p>
                            <div class="d-flex align-center gap-2">
                                <p class="text-h4 font-weight-bold">{{ metrics.avg_match_percentage }}%</p>
                            </div>
                            <v-progress-linear
                                :value="metrics.avg_match_percentage"
                                :color="getMetricColor(metrics.avg_match_percentage)"
                                height="4"
                                class="mt-2"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Roles at Risk -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <p class="text-caption text-grey mb-2">Roles at Risk</p>
                                    <p class="text-h4 font-weight-bold text-error">{{ metrics.roles_at_risk }}</p>
                                </div>
                                <v-icon size="48" color="error">mdi-alert-circle</v-icon>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- High Performers -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <p class="text-caption text-grey mb-2">High Performers</p>
                                    <p class="text-h4 font-weight-bold text-success">{{ metrics.high_performers }}</p>
                                </div>
                                <v-icon size="48" color="success">mdi-star</v-icon>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Skills Coverage -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <p class="text-caption text-grey mb-2">Skills Coverage</p>
                            <div class="d-flex align-center gap-2">
                                <p class="text-h4 font-weight-bold">{{ metrics.skills_coverage }}%</p>
                            </div>
                            <v-progress-linear
                                :value="metrics.skills_coverage"
                                :color="getMetricColor(metrics.skills_coverage)"
                                height="4"
                                class="mt-2"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Critical Gaps -->
                <v-col cols="12" sm="6" md="4">
                    <v-card class="h-100" elevation="0" border>
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <p class="text-caption text-grey mb-2">Critical Gaps</p>
                                    <p class="text-h4 font-weight-bold text-warning">{{ metrics.critical_gaps }}</p>
                                </div>
                                <v-icon size="48" color="warning">mdi-exclamation</v-icon>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Empty State -->
            <v-card v-if="!loading && !metrics" class="mb-4">
                <v-card-text class="text-center py-12">
                    <v-icon size="64" class="mb-4 text-grey">mdi-chart-box-outline</v-icon>
                    <p class="text-body1 text-grey">No data available</p>
                </v-card-text>
            </v-card>
        </v-container>
    </AppLayout>
</template>

<style scoped>
.gap-4 {
    gap: 16px;
}

.h-100 {
    height: 100%;
}

.d-flex {
    display: flex;
}

.justify-space-between {
    justify-content: space-between;
}

.align-center {
    align-items: center;
}

.pa-4 {
    padding: 16px;
}

.mb-2 {
    margin-bottom: 8px;
}

.mb-4 {
    margin-bottom: 16px;
}

.mt-2 {
    margin-top: 8px;
}

.mt-4 {
    margin-top: 16px;
}

.text-h4 {
    font-size: 2rem;
    font-weight: bold;
}

.text-caption {
    font-size: 0.75rem;
}

.text-grey {
    color: #757575;
}

.gap-2 {
    gap: 8px;
}

.text-error {
    color: #f44336;
}

.text-success {
    color: #4caf50;
}

.text-warning {
    color: #ff9800;
}
</style>

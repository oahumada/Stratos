<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

type ReportTab = 'completion' | 'compliance' | 'time-to-complete' | 'engagement';

const activeTab = ref<ReportTab>('completion');
const loading = ref(false);
const exporting = ref(false);
const error = ref('');
const reportData = ref<Record<string, unknown> | null>(null);

const tabs: Array<{ key: ReportTab; label: string; icon: string }> = [
    { key: 'completion', label: 'Completación', icon: 'mdi-check-all' },
    { key: 'compliance', label: 'Cumplimiento', icon: 'mdi-shield-check' },
    { key: 'time-to-complete', label: 'Tiempo', icon: 'mdi-timer-outline' },
    { key: 'engagement', label: 'Engagement', icon: 'mdi-trending-up' },
];

const endpointMap: Record<ReportTab, string> = {
    completion: '/api/lms/reports/completion',
    compliance: '/api/lms/reports/compliance',
    'time-to-complete': '/api/lms/reports/time-to-complete',
    engagement: '/api/lms/reports/engagement',
};

async function fetchReport(tab: ReportTab) {
    activeTab.value = tab;
    loading.value = true;
    error.value = '';
    reportData.value = null;
    try {
        const res = await axios.get(endpointMap[tab]);
        reportData.value = res.data.data;
    } catch {
        error.value = 'Error al cargar el reporte.';
    } finally {
        loading.value = false;
    }
}

async function exportCsv() {
    exporting.value = true;
    try {
        const res = await axios.get(`/api/lms/reports/export/${activeTab.value}`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `lms-report-${activeTab.value}-${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch {
        error.value = 'Error al exportar el reporte.';
    } finally {
        exporting.value = false;
    }
}

onMounted(() => fetchReport('completion'));
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center mb-4">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-1">Reportes LMS</h1>
                        <p class="text-subtitle-1 text-grey-darken-1">
                            Reportes de completación, cumplimiento, tiempos y engagement.
                        </p>
                    </div>
                    <v-spacer />
                    <v-btn color="success" variant="flat" :loading="exporting" @click="exportCsv">
                        <v-icon start>mdi-download</v-icon>
                        Exportar CSV
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <v-alert v-if="error" type="error" closable class="mb-4" @click:close="error = ''">
            {{ error }}
        </v-alert>

        <v-row>
            <v-col cols="12">
                <v-card class="rounded-xl" elevation="1">
                    <v-tabs v-model="activeTab" color="primary" @update:model-value="(v: unknown) => fetchReport(v as ReportTab)">
                        <v-tab v-for="t in tabs" :key="t.key" :value="t.key">
                            <v-icon start>{{ t.icon }}</v-icon>
                            {{ t.label }}
                        </v-tab>
                    </v-tabs>

                    <v-divider />

                    <v-progress-linear v-if="loading" indeterminate color="primary" />

                    <v-card-text v-if="!loading && reportData">
                        <!-- Completion Report -->
                        <template v-if="activeTab === 'completion'">
                            <div v-if="(reportData as any).totals" class="d-flex ga-6 mb-4">
                                <v-chip color="blue" variant="tonal">
                                    Total: {{ (reportData as any).totals.total_enrollments }}
                                </v-chip>
                                <v-chip color="green" variant="tonal">
                                    Completados: {{ (reportData as any).totals.completed }}
                                </v-chip>
                                <v-chip color="primary" variant="tonal">
                                    Tasa: {{ (reportData as any).totals.overall_completion_rate }}%
                                </v-chip>
                            </div>
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Completados</th>
                                        <th class="text-center">En Progreso</th>
                                        <th class="text-center">Progreso Prom.</th>
                                        <th class="text-center">Tasa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in (reportData as any).by_category" :key="row.category">
                                        <td>{{ row.category || 'Sin categoría' }}</td>
                                        <td class="text-center">{{ row.total_enrollments }}</td>
                                        <td class="text-center">{{ row.completed }}</td>
                                        <td class="text-center">{{ row.in_progress }}</td>
                                        <td class="text-center">{{ row.avg_progress }}%</td>
                                        <td class="text-center">{{ row.completion_rate }}%</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </template>

                        <!-- Compliance Report -->
                        <template v-if="activeTab === 'compliance'">
                            <v-chip color="primary" variant="tonal" class="mb-4">
                                Tasa General: {{ (reportData as any).overall_compliance_rate }}%
                            </v-chip>
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th>Curso</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Completados</th>
                                        <th class="text-center">Pendientes</th>
                                        <th class="text-center">Vencidos</th>
                                        <th class="text-center">Tasa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in (reportData as any).courses" :key="row.course_id">
                                        <td>{{ row.title }}</td>
                                        <td>{{ row.compliance_category || '—' }}</td>
                                        <td class="text-center">{{ row.total_records }}</td>
                                        <td class="text-center">{{ row.completed }}</td>
                                        <td class="text-center">{{ row.pending }}</td>
                                        <td class="text-center text-red">{{ row.overdue }}</td>
                                        <td class="text-center">{{ row.compliance_rate }}%</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </template>

                        <!-- Time to Complete Report -->
                        <template v-if="activeTab === 'time-to-complete'">
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th>Curso</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Completaciones</th>
                                        <th class="text-center">Prom. (hrs)</th>
                                        <th class="text-center">Mín. (hrs)</th>
                                        <th class="text-center">Máx. (hrs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in (reportData as any).courses" :key="row.course_id">
                                        <td>{{ row.title }}</td>
                                        <td>{{ row.category || '—' }}</td>
                                        <td class="text-center">{{ row.completions }}</td>
                                        <td class="text-center">{{ row.avg_hours }}</td>
                                        <td class="text-center">{{ row.min_hours }}</td>
                                        <td class="text-center">{{ row.max_hours }}</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </template>

                        <!-- Engagement Trends Report -->
                        <template v-if="activeTab === 'engagement'">
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-center">Inscripciones</th>
                                        <th class="text-center">Completaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in (reportData as any).months" :key="row.month">
                                        <td>{{ row.month }}</td>
                                        <td class="text-center">{{ row.enrollments }}</td>
                                        <td class="text-center">{{ row.completions }}</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </template>
                    </v-card-text>

                    <v-card-text v-if="!loading && !reportData && !error">
                        <p class="text-body-2 text-grey text-center">Sin datos disponibles.</p>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

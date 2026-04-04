<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

interface DashboardData {
    total_records: number;
    completed: number;
    pending: number;
    overdue: number;
    compliance_rate_pct: number;
    by_category: Array<{
        category: string | null;
        total: number;
        completed: number;
        overdue: number;
    }>;
    upcoming_due: Array<{
        id: number;
        user_name: string;
        user_email: string;
        course_title: string;
        category: string | null;
        due_date: string;
        days_until_due: number;
    }>;
    overdue_list: Array<{
        id: number;
        user_name: string;
        user_email: string;
        course_title: string;
        category: string | null;
        due_date: string;
        days_overdue: number;
        escalation_level: number;
    }>;
}

const loading = ref(true);
const exporting = ref(false);
const checking = ref(false);
const data = ref<DashboardData | null>(null);
const error = ref('');

const summaryCards = computed(() => {
    if (!data.value) return [];
    return [
        { title: 'Total', value: data.value.total_records, icon: 'mdi-clipboard-list', color: 'blue' },
        { title: 'Completados', value: data.value.completed, icon: 'mdi-check-circle', color: 'green' },
        { title: 'Pendientes', value: data.value.pending, icon: 'mdi-clock-outline', color: 'orange' },
        { title: 'Vencidos', value: data.value.overdue, icon: 'mdi-alert-circle', color: 'red' },
    ];
});

const escalationLabel = (level: number) => {
    const labels: Record<number, string> = { 0: 'Ninguna', 1: 'Recordatorio', 2: 'Urgente', 3: 'Escalado' };
    return labels[level] ?? `Nivel ${level}`;
};

const escalationColor = (level: number) => {
    const colors: Record<number, string> = { 0: 'grey', 1: 'info', 2: 'warning', 3: 'error' };
    return colors[level] ?? 'grey';
};

async function fetchDashboard() {
    loading.value = true;
    error.value = '';
    try {
        const res = await axios.get('/api/lms/compliance/dashboard');
        data.value = res.data.data;
    } catch {
        error.value = 'Error al cargar datos de cumplimiento.';
    } finally {
        loading.value = false;
    }
}

async function runCheck() {
    checking.value = true;
    try {
        await axios.post('/api/lms/compliance/check');
        await fetchDashboard();
    } catch {
        error.value = 'Error al ejecutar verificación.';
    } finally {
        checking.value = false;
    }
}

async function exportCsv() {
    exporting.value = true;
    try {
        const res = await axios.get('/api/lms/compliance/export', { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `compliance-${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch {
        error.value = 'Error al exportar CSV.';
    } finally {
        exporting.value = false;
    }
}

onMounted(fetchDashboard);
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center mb-4">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-1">Cumplimiento LMS</h1>
                        <p class="text-subtitle-1 text-grey-darken-1">
                            Panel de seguimiento de cumplimiento de capacitaciones obligatorias.
                        </p>
                    </div>
                    <v-spacer />
                    <v-btn
                        color="primary"
                        variant="outlined"
                        class="mr-2"
                        :loading="checking"
                        @click="runCheck"
                    >
                        <v-icon start>mdi-refresh</v-icon>
                        Verificar
                    </v-btn>
                    <v-btn
                        color="success"
                        variant="flat"
                        :loading="exporting"
                        @click="exportCsv"
                    >
                        <v-icon start>mdi-download</v-icon>
                        Exportar CSV
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <v-alert v-if="error" type="error" closable class="mb-4" @click:close="error = ''">
            {{ error }}
        </v-alert>

        <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4" />

        <template v-if="data && !loading">
            <!-- Summary Cards -->
            <v-row class="mb-4">
                <v-col v-for="card in summaryCards" :key="card.title" cols="12" sm="6" md="3">
                    <v-card class="pa-4 rounded-xl text-center" elevation="1">
                        <v-icon :color="card.color" size="36" class="mb-2">{{ card.icon }}</v-icon>
                        <div class="text-h4 font-weight-bold">{{ card.value }}</div>
                        <div class="text-body-2 text-grey">{{ card.title }}</div>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Compliance Rate -->
            <v-row class="mb-4">
                <v-col cols="12" md="4">
                    <v-card class="pa-6 rounded-xl text-center" elevation="1">
                        <h3 class="text-h6 mb-4">Tasa de Cumplimiento</h3>
                        <v-progress-circular
                            :model-value="data.compliance_rate_pct"
                            :size="120"
                            :width="12"
                            :color="data.compliance_rate_pct >= 80 ? 'green' : data.compliance_rate_pct >= 50 ? 'orange' : 'red'"
                        >
                            <span class="text-h5 font-weight-bold">{{ data.compliance_rate_pct }}%</span>
                        </v-progress-circular>
                    </v-card>
                </v-col>
                <v-col cols="12" md="8">
                    <v-card class="pa-6 rounded-xl" elevation="1">
                        <h3 class="text-h6 mb-4">Por Categoría</h3>
                        <v-table density="compact" v-if="data.by_category.length">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Completados</th>
                                    <th class="text-center">Vencidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cat in data.by_category" :key="cat.category">
                                    <td>
                                        <v-chip size="small" :color="cat.overdue > 0 ? 'red' : 'green'" variant="tonal">
                                            {{ cat.category || 'Sin categoría' }}
                                        </v-chip>
                                    </td>
                                    <td class="text-center">{{ cat.total }}</td>
                                    <td class="text-center">{{ cat.completed }}</td>
                                    <td class="text-center">{{ cat.overdue }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                        <p v-else class="text-body-2 text-grey">Sin datos de categorías.</p>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Overdue Table -->
            <v-row class="mb-4" v-if="data.overdue_list.length">
                <v-col cols="12">
                    <v-card class="pa-6 rounded-xl" elevation="1">
                        <h3 class="text-h6 mb-4 text-red">
                            <v-icon color="red" class="mr-1">mdi-alert</v-icon>
                            Registros Vencidos ({{ data.overdue_list.length }})
                        </h3>
                        <v-table density="compact">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Curso</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Días Vencido</th>
                                    <th class="text-center">Escalación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in data.overdue_list" :key="item.id">
                                    <td>{{ item.user_name }}</td>
                                    <td>{{ item.course_title }}</td>
                                    <td>{{ item.category || '—' }}</td>
                                    <td class="text-center text-red font-weight-bold">{{ item.days_overdue }}</td>
                                    <td class="text-center">
                                        <v-chip size="x-small" :color="escalationColor(item.escalation_level)">
                                            {{ escalationLabel(item.escalation_level) }}
                                        </v-chip>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Upcoming Due -->
            <v-row v-if="data.upcoming_due.length">
                <v-col cols="12">
                    <v-card class="pa-6 rounded-xl" elevation="1">
                        <h3 class="text-h6 mb-4">
                            <v-icon color="orange" class="mr-1">mdi-clock-alert-outline</v-icon>
                            Próximos a Vencer (30 días)
                        </h3>
                        <v-table density="compact">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Curso</th>
                                    <th>Fecha Límite</th>
                                    <th class="text-center">Días Restantes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in data.upcoming_due" :key="item.id">
                                    <td>{{ item.user_name }}</td>
                                    <td>{{ item.course_title }}</td>
                                    <td>{{ item.due_date }}</td>
                                    <td class="text-center">
                                        <v-chip size="x-small" :color="item.days_until_due <= 7 ? 'warning' : 'info'">
                                            {{ item.days_until_due }}d
                                        </v-chip>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>
                </v-col>
            </v-row>
        </template>
    </v-container>
</template>

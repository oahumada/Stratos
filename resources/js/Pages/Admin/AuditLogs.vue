<template>
    <AppLayout title="Audit Logs">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Audit Logs
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Registro de actividad de la organización
                </p>
            </div>

            <!-- Filters -->
            <v-card elevation="1" class="pa-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <v-select
                        v-model="filters.action"
                        label="Acción"
                        :items="actionOptions"
                        clearable
                        density="compact"
                        @update:model-value="() => loadLogs()"
                    />
                    <v-select
                        v-model="filters.entity_type"
                        label="Entidad"
                        :items="entityOptions"
                        clearable
                        density="compact"
                        @update:model-value="() => loadLogs()"
                    />
                    <v-select
                        v-model="filters.days"
                        label="Período"
                        :items="dayOptions"
                        density="compact"
                        @update:model-value="() => loadLogs()"
                    />
                    <v-btn
                        color="primary"
                        variant="outlined"
                        prepend-icon="mdi-refresh"
                        @click="loadLogs"
                    >
                        Actualizar
                    </v-btn>
                </div>
            </v-card>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <v-card
                    class="pa-4 text-center"
                    elevation="1"
                    v-for="(val, key) in stats"
                    :key="key"
                >
                    <div class="text-xs text-gray-500 capitalize">
                        {{ formatStatKey(String(key)) }}
                    </div>
                    <div class="text-2xl font-bold text-blue-600">
                        {{ val }}
                    </div>
                </v-card>
            </div>

            <!-- Logs table -->
            <v-card elevation="1">
                <v-data-table-server
                    :headers="headers"
                    :items="logs"
                    :items-length="pagination.total"
                    :loading="loading"
                    :items-per-page="pagination.per_page"
                    :page="pagination.current_page"
                    item-value="id"
                    @update:page="changePage"
                >
                    <template #item.action="{ item }">
                        <v-chip
                            size="small"
                            :color="actionColor(item.action)"
                            variant="tonal"
                        >
                            {{ item.action }}
                        </v-chip>
                    </template>
                    <template #item.created_at="{ item }">
                        <span class="text-xs text-gray-500">{{
                            formatDate(item.created_at)
                        }}</span>
                    </template>
                    <template #item.changes="{ item }">
                        <v-btn
                            v-if="
                                item.changes && Object.keys(item.changes).length
                            "
                            size="x-small"
                            variant="outlined"
                            @click="
                                selectedLog = item;
                                showChanges = true;
                            "
                        >
                            Ver cambios
                        </v-btn>
                    </template>
                </v-data-table-server>
            </v-card>
        </div>

        <!-- Changes dialog -->
        <v-dialog v-model="showChanges" max-width="600">
            <v-card>
                <v-card-title>Cambios registrados</v-card-title>
                <v-card-text>
                    <pre
                        class="max-h-96 overflow-auto rounded bg-gray-100 p-3 text-xs dark:bg-gray-800"
                        >{{
                            JSON.stringify(selectedLog?.changes, null, 2)
                        }}</pre
                    >
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="showChanges = false">Cerrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';

interface AuditLog {
    id: number;
    action: string;
    entity_type: string;
    entity_id: number | null;
    user_id: number | null;
    user_name?: string;
    changes?: Record<string, unknown>;
    created_at: string;
}

interface Pagination {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

const loading = ref(false);
const logs = ref<AuditLog[]>([]);
const pagination = ref<Pagination>({
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 20,
});
const stats = ref<Record<string, number>>({});
const showChanges = ref(false);
const selectedLog = ref<AuditLog | null>(null);

const filters = ref({
    action: null as string | null,
    entity_type: null as string | null,
    days: 7,
});

const actionOptions = [
    'created',
    'updated',
    'deleted',
    'approved',
    'archived',
    'login',
    'logout',
];
const entityOptions = [
    'WorkforcePlan',
    'Employee',
    'Organization',
    'User',
    'Course',
    'Scenario',
    'ApprovalRequest',
    'DevelopmentAction',
];
const dayOptions = [
    { title: 'Últimas 24h', value: 1 },
    { title: 'Últimos 7 días', value: 7 },
    { title: 'Últimos 30 días', value: 30 },
    { title: 'Últimos 90 días', value: 90 },
];

const headers = [
    { title: 'Acción', key: 'action' },
    { title: 'Entidad', key: 'entity_type' },
    { title: 'ID', key: 'entity_id' },
    { title: 'Usuario', key: 'user_name' },
    { title: 'Fecha', key: 'created_at' },
    { title: 'Cambios', key: 'changes', sortable: false },
];

function actionColor(action: string) {
    const map: Record<string, string> = {
        created: 'green',
        updated: 'blue',
        deleted: 'red',
        approved: 'teal',
        archived: 'orange',
        login: 'purple',
        logout: 'gray',
    };
    return map[action] ?? 'default';
}

function formatDate(d: string) {
    return new Date(d).toLocaleString('es-MX', {
        dateStyle: 'short',
        timeStyle: 'short',
    });
}

function formatStatKey(k: string) {
    return k.replaceAll('_', ' ');
}

async function loadLogs(page = 1) {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            page: String(page),
            per_page: '20',
            days: String(filters.value.days),
        });
        if (filters.value.action) params.set('action', filters.value.action);
        if (filters.value.entity_type)
            params.set('entity_type', filters.value.entity_type);

        const res = await fetch(`/api/audit-logs?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        logs.value = json.data ?? [];
        pagination.value = json.pagination ?? pagination.value;
        stats.value = json.stats ?? {};
    } catch {
        // silent
    } finally {
        loading.value = false;
    }
}

function changePage(page: number) {
    loadLogs(page);
}

onMounted(() => loadLogs());
</script>

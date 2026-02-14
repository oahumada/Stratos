<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import { useScenarioPlanning } from '@/composables/useScenarioPlanning';
import AppLayout from '@/layouts/AppLayout.vue';
import type { ScenarioPlan } from '@/types/scenarioPlanning';
import { onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

const { listPlans } = useScenarioPlanning();
const { showError } = useNotification();

const loading = ref(false);
const items = ref<ScenarioPlan[]>([]);

const headers = [
    { title: 'Nombre', value: 'name' },
    { title: 'Código', value: 'code' },
    { title: 'Estado', value: 'status' },
    { title: 'Inicio', value: 'start_date' },
    { title: 'Término', value: 'end_date' },
];

const load = async () => {
    loading.value = true;
    try {
        const res: any = await listPlans();
        const data = res?.data?.data ?? res?.data ?? [];
        items.value = Array.isArray(data) ? data : [];
    } catch (e) {
        void e;
        showError('No fue posible cargar los planes');
    } finally {
        loading.value = false;
    }
};

onMounted(load);
</script>

<template>
    <div class="pa-4">
        <v-row class="mb-4" align="center">
            <v-col><h2>Escenarios</h2></v-col>
            <v-col class="text-right">
                <v-btn color="primary" :to="{ name: 'scenario-planning.create' }">
                    <v-icon start>mdi-plus</v-icon>
                    Nuevo Escenario
                </v-btn>
            </v-col>
        </v-row>

        <v-card>
            <v-data-table
                :headers="headers"
                :items="items"
                :loading="loading"
                density="comfortable"
            >
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.name="{ item }">
                    <RouterLink
                        :to="{
                            name: 'scenario-planning.show',
                            params: { id: item.id },
                        }"
                        >{{ item.name }}</RouterLink
                    >
                </template>
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.status="{ item }">
                    <v-chip
                        size="small"
                        :color="
                            item.status === 'active'
                                ? 'success'
                                : item.status === 'approved'
                                  ? 'info'
                                  : 'grey'
                        "
                        >{{ item.status }}</v-chip
                    >
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

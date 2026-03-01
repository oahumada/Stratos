<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useNotification } from '@/composables/useNotification';
import { useScenarioPlanning } from '@/composables/useStrategicPlanningScenarios';
import type { ScenarioPlan } from '@/types/scenarioPlanning';
import { onMounted, ref } from 'vue';

const { list } = useScenarioPlanning();
const { showError } = useNotification();

const loading = ref(false);
const plans = ref<ScenarioPlan[]>([]);

const getStatusVariant = (status: string | null) => {
    switch (status?.toLowerCase()) {
        case 'active':
            return 'success';
        case 'approved':
            return 'primary';
        case 'rejected':
            return 'secondary';
        default:
            return 'glass';
    }
};

const load = async () => {
    loading.value = true;
    try {
        const res: any = await list();
        // compatible with paginated or direct data
        const data = res?.data?.data ?? res?.data ?? res;
        plans.value = Array.isArray(data) ? data : (data?.data ?? []);
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
    <div class="scenario-plans-overview animate-in pb-12 duration-500 fade-in">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h3 class="mb-1 text-2xl font-black tracking-tight text-white">
                    Scenario <span class="text-indigo-400">Architectures</span>
                </h3>
                <p class="text-sm font-medium text-white/40">
                    Overview of strategic planning scenarios
                </p>
            </div>
            <StButtonGlass
                variant="primary"
                icon="mdi-plus"
                size="sm"
                @click="load"
            >
                Refresh Data
            </StButtonGlass>
        </div>

        <StCardGlass
            variant="glass"
            class="overflow-hidden border-white/10 !p-0"
            :no-hover="true"
        >
            <div
                class="flex items-center gap-3 border-b border-white/5 bg-white/5 px-6 py-4"
            >
                <v-icon color="indigo-400" size="20">mdi-list-status</v-icon>
                <h4
                    class="text-xs font-black tracking-widest text-white/80 uppercase"
                >
                    Execution Repository
                </h4>
            </div>

            <div
                v-if="loading"
                class="flex flex-col items-center justify-center py-20"
            >
                <v-progress-circular
                    indeterminate
                    color="indigo-400"
                    size="48"
                    width="3"
                />
                <span
                    class="mt-4 text-xs font-black tracking-widest text-indigo-400/60 uppercase"
                    >Loading Scenarios...</span
                >
            </div>

            <div
                v-else-if="plans.length === 0"
                class="flex flex-col items-center justify-center py-20 text-center"
            >
                <v-icon size="48" color="white/10" class="mb-4"
                    >mdi-folder-open-outline</v-icon
                >
                <h3 class="mb-1 text-lg font-black text-white/40">
                    No Scenarios Found
                </h3>
                <p class="text-xs text-white/20">
                    There are no strategic scenarios defined.
                </p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full min-w-[600px] border-collapse text-left">
                    <thead>
                        <tr class="bg-white/2">
                            <th
                                class="px-6 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >
                                Scenario Name
                            </th>
                            <th
                                class="px-6 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="plan in plans"
                            :key="plan.id"
                            class="group transition-colors hover:bg-white/[0.02]"
                        >
                            <td class="px-6 py-5">
                                <RouterLink
                                    :to="{
                                        name: 'scenario-planning.show',
                                        params: { id: plan.id },
                                    }"
                                    class="text-sm font-bold text-white transition-colors hover:text-indigo-400"
                                >
                                    {{ plan.name }}
                                </RouterLink>
                            </td>
                            <td class="px-6 py-5">
                                <StBadgeGlass
                                    :variant="getStatusVariant(plan.status)"
                                    size="sm"
                                >
                                    {{ (plan.status || 'draft').toUpperCase() }}
                                </StBadgeGlass>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <StButtonGlass
                                    variant="ghost"
                                    size="sm"
                                    icon="mdi-arrow-right"
                                    :to="{
                                        name: 'scenario-planning.show',
                                        params: { id: plan.id },
                                    }"
                                >
                                    Open Designer
                                </StButtonGlass>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </StCardGlass>
    </div>
</template>

<style scoped>
.pa-4 {
    padding: 16px;
}
</style>

<template>
    <div class="role-forecasts-container relative min-h-[400px]">
        <!-- Background Glows -->
        <div
            class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 bg-indigo-500/10 blur-[100px]"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 bg-emerald-500/10 blur-[100px]"
        ></div>

        <div class="relative z-10 mb-8">
            <h3 class="mb-1 text-2xl font-black tracking-tight text-white">
                Role <span class="text-indigo-400">Forecasts</span>
            </h3>
            <p class="text-sm font-medium text-white/40">
                Projected bandwidth shifts and structural evolution vectors
            </p>
        </div>

        <!-- Feedback Alerts -->
        <transition-group name="fade">
            <div
                v-if="error"
                key="error"
                class="mb-6 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 backdrop-blur-xl"
            >
                <div class="flex items-center gap-3">
                    <v-icon color="rose-400" size="20">mdi-alert-circle</v-icon>
                    <span class="text-sm font-bold text-rose-200">{{
                        error
                    }}</span>
                </div>
            </div>
            <div
                v-if="success"
                key="success"
                class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 backdrop-blur-xl"
            >
                <div class="flex items-center gap-3">
                    <v-icon color="emerald-400" size="20"
                        >mdi-check-circle</v-icon
                    >
                    <span class="text-sm font-bold text-emerald-200">{{
                        success
                    }}</span>
                </div>
            </div>
        </transition-group>

        <!-- Loading State -->
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
                >Synthesizing Projections...</span
            >
        </div>

        <!-- Forecasts Table -->
        <div
            v-else
            class="relative overflow-hidden rounded-3xl border border-white/10 bg-black/20 backdrop-blur-md"
        >
            <div class="custom-scrollbar overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="px-6 py-4 text-left">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Node Identity</span
                                >
                            </th>
                            <th class="px-6 py-4 text-center">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Current FTE</span
                                >
                            </th>
                            <th class="px-6 py-4 text-center">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Future FTE</span
                                >
                            </th>
                            <th class="px-6 py-4 text-center">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Net Delta</span
                                >
                            </th>
                            <th class="px-6 py-4 text-left">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Evolution Vector</span
                                >
                            </th>
                            <th class="px-6 py-4 text-left">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Criticality</span
                                >
                            </th>
                            <th class="px-6 py-4 text-center">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Actions</span
                                >
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-if="forecasts.length === 0">
                            <td colspan="7" class="py-16 text-center">
                                <v-icon size="40" class="mb-3 opacity-10"
                                    >mdi-chart-line-variant</v-icon
                                >
                                <p
                                    class="text-sm font-bold tracking-widest text-white/20 uppercase"
                                >
                                    No spectral projections defined
                                </p>
                            </td>
                        </tr>
                        <tr
                            v-for="forecast in forecasts"
                            :key="forecast.id"
                            class="group transition-colors hover:bg-white/[0.02]"
                        >
                            <td class="px-6 py-4">
                                <div
                                    class="font-black text-white transition-colors group-hover:text-indigo-300"
                                >
                                    {{ forecast.role_name }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-center font-bold text-white/60"
                            >
                                {{ forecast.fte_current }}
                            </td>
                            <td
                                class="px-6 py-4 text-center font-black text-indigo-400"
                            >
                                {{ forecast.fte_future }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div
                                    class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-black"
                                    :class="{
                                        'border border-emerald-500/20 bg-emerald-500/10 text-emerald-400':
                                            forecast.fte_delta > 0,
                                        'border border-rose-500/20 bg-rose-500/10 text-rose-400':
                                            forecast.fte_delta < 0,
                                        'border border-white/10 bg-white/5 text-white/30':
                                            forecast.fte_delta === 0,
                                    }"
                                >
                                    <v-icon size="10">{{
                                        forecast.fte_delta > 0
                                            ? 'mdi-trending-up'
                                            : forecast.fte_delta < 0
                                              ? 'mdi-trending-down'
                                              : 'mdi-minus'
                                    }}</v-icon>
                                    {{ forecast.fte_delta > 0 ? '+' : ''
                                    }}{{ forecast.fte_delta }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <StBadgeGlass
                                    :variant="
                                        getEvolutionBadge(
                                            forecast.evolution_type,
                                        )
                                    "
                                    size="sm"
                                    class="!px-3 text-[9px] font-black"
                                >
                                    {{
                                        formatEvolutionType(
                                            forecast.evolution_type,
                                        ).toUpperCase()
                                    }}
                                </StBadgeGlass>
                            </td>
                            <td class="px-6 py-4">
                                <StBadgeGlass
                                    :variant="
                                        getImpactBadge(forecast.impact_level)
                                    "
                                    size="sm"
                                    class="!px-3 text-[9px] font-black"
                                >
                                    {{
                                        formatImpact(
                                            forecast.impact_level,
                                        ).toUpperCase()
                                    }}
                                </StBadgeGlass>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div
                                    class="flex items-center justify-center gap-1"
                                >
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        circle
                                        icon="mdi-pencil-outline"
                                        @click="editForecast(forecast)"
                                    />
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        circle
                                        icon="mdi-delete-outline"
                                        class="hover:!text-rose-400"
                                        @click="deleteForecast(forecast.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Dialog -->
        <v-dialog
            v-model="showEditDialog"
            max-width="500px"
            class="backdrop-blur-sm"
        >
            <StCardGlass
                v-if="editData"
                variant="media"
                class="overflow-hidden border-indigo-500/20"
            >
                <div
                    class="flex items-center justify-between border-b border-white/5 p-6"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5"
                        >
                            <v-icon color="indigo-300" size="24"
                                >mdi-chart-areaspline</v-icon
                            >
                        </div>
                        <h2 class="text-xl font-black text-white">
                            Modify Projection
                        </h2>
                    </div>
                </div>

                <div class="space-y-6 p-8">
                    <div
                        class="rounded-2xl border border-white/5 bg-white/5 p-4"
                    >
                        <label
                            class="mb-1 block text-[9px] font-black tracking-widest text-white/20 uppercase"
                            >Active Node</label
                        >
                        <div class="text-lg font-black text-white">
                            {{ editData.role_name }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <v-text-field
                            v-model.number="editData.fte_current"
                            type="number"
                            label="Current FTE"
                            variant="outlined"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                        />
                        <v-text-field
                            v-model.number="editData.fte_future"
                            type="number"
                            label="Target FTE"
                            variant="outlined"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <v-select
                            v-model="editData.evolution_type"
                            :items="evolutionTypes"
                            label="Evolution Vector"
                            variant="outlined"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                        />
                        <v-select
                            v-model="editData.impact_level"
                            :items="impactLevels"
                            label="Criticality Index"
                            variant="outlined"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                        />
                    </div>

                    <v-textarea
                        v-model="editData.rationale"
                        label="Structural Justification"
                        rows="3"
                        variant="outlined"
                        density="comfortable"
                        base-color="white/10"
                        color="indigo-400"
                        placeholder="Add strategic context for this bandwidth shift..."
                    />
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-white/5 bg-black/40 p-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="showEditDialog = false"
                        >Dismiss</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        :loading="saving"
                        @click="saveForecast"
                        >Commit Projection</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { onMounted, ref, watch } from 'vue';

interface RoleForecast {
    id: number;
    scenario_id: number;
    role_id: number;
    role_name: string;
    fte_current: number;
    fte_future: number;
    fte_delta: number;
    evolution_type: string;
    impact_level: string;
    rationale?: string;
}

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();

const api = useApi();
const loading = ref(true);
const saving = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);

const forecasts = ref<RoleForecast[]>([]);
const showEditDialog = ref(false);
const editData = ref<RoleForecast | null>(null);

const evolutionTypes = [
    { title: 'New Node', value: 'new_role' },
    { title: 'Upskill Plan', value: 'upgrade_skills' },
    { title: 'Transition', value: 'transformation' },
    { title: 'Optimization', value: 'downsize' },
    { title: 'Phase Out', value: 'elimination' },
];
const impactLevels = [
    { title: 'Critical', value: 'critical' },
    { title: 'High', value: 'high' },
    { title: 'Medium', value: 'medium' },
    { title: 'Low', value: 'low' },
];

const loadForecasts = async () => {
    if (!props.scenarioId) return;

    try {
        loading.value = true;
        const response: any = await api.get(
            `/api/scenarios/${props.scenarioId}/step2/role-forecasts`,
        );

        const data = response.data || [];
        forecasts.value = data.map((forecast: RoleForecast) => ({
            ...forecast,
            fte_delta: forecast.fte_future - forecast.fte_current,
        }));
    } catch (err: any) {
        error.value =
            err.response?.data?.message || 'Projections retrieval failed';
    } finally {
        loading.value = false;
    }
};

const editForecast = (forecast: RoleForecast) => {
    editData.value = { ...forecast };
    showEditDialog.value = true;
};

const saveForecast = async () => {
    if (!editData.value || !editData.value.id) return;

    try {
        saving.value = true;
        await api.put(
            `/api/scenarios/${props.scenarioId}/step2/role-forecasts/${editData.value.id}`,
            editData.value,
        );

        success.value = 'Neural projection committed successfully';
        showEditDialog.value = false;
        setTimeout(() => (success.value = null), 3000);
        await loadForecasts();
    } catch (err: any) {
        error.value =
            err.response?.data?.message || 'Failed to update projection';
    } finally {
        saving.value = false;
    }
};

const deleteForecast = async (id: number) => {
    console.log('Decommissioning projection for id:', id);
    // Logic for deletion if necessary
};

const getEvolutionBadge = (type: string) => {
    const map: Record<string, string> = {
        new_role: 'secondary',
        upgrade_skills: 'primary',
        transformation: 'glass',
        downsize: 'glass',
        elimination: 'secondary',
    };
    return map[type] || 'glass';
};

const getImpactBadge = (level: string) => {
    const map: Record<string, string> = {
        critical: 'primary',
        high: 'secondary',
        medium: 'glass',
        low: 'glass',
    };
    return map[level] || 'glass';
};

const formatEvolutionType = (type: string) => {
    const labels: Record<string, string> = {
        new_role: 'New Node',
        upgrade_skills: 'Upskill',
        transformation: 'Evolution',
        downsize: 'Reduction',
        elimination: 'Sunset',
    };
    return labels[type] || type;
};

const formatImpact = (level: string) => {
    const labels: Record<string, string> = {
        critical: 'Critical',
        high: 'High',
        medium: 'Medium',
        low: 'Low',
    };
    return labels[level] || level;
};

onMounted(() => {
    loadForecasts();
});

watch(
    () => props.scenarioId,
    () => {
        loadForecasts();
    },
);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}
</style>

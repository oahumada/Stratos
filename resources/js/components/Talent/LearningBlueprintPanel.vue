<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps<{
    peopleId: number;
    targetRoleId?: number | null;
}>();

const emit = defineEmits<{
    (e: 'materialized', path: any): void;
}>();

const loading = ref(false);
const materializing = ref(false);
const blueprint = ref<any>(null);
const error = ref<string | null>(null);

async function generateBlueprint() {
    loading.value = true;
    error.value = null;
    blueprint.value = null;

    try {
        const { data } = await axios.post(
            `/api/learning-blueprints/${props.peopleId}`,
            {
                target_role_id: props.targetRoleId,
            },
        );
        blueprint.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error al generar blueprint';
    } finally {
        loading.value = false;
    }
}

async function materialize() {
    if (!blueprint.value) return;
    materializing.value = true;

    try {
        const { data } = await axios.post(
            `/api/learning-blueprints/${props.peopleId}/materialize`,
            {
                blueprint: blueprint.value,
            },
        );
        emit('materialized', data.data ?? data);
    } catch (e: any) {
        error.value =
            e.response?.data?.message ?? 'Error al materializar blueprint';
    } finally {
        materializing.value = false;
    }
}

function getModelIcon(type: string): string {
    if (type === 'experience') return '🏋️';
    if (type === 'exposure') return '👥';
    return '📚';
}

function getModelColor(type: string): string {
    if (type === 'experience') return 'border-indigo-500/20 bg-indigo-500/5';
    if (type === 'exposure') return 'border-emerald-500/20 bg-emerald-500/5';
    return 'border-amber-500/20 bg-amber-500/5';
}

function getModelLabel(type: string): string {
    if (type === 'experience') return '70% Experience';
    if (type === 'exposure') return '20% Exposure';
    return '10% Education';
}
</script>

<template>
    <StCardGlass>
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-fuchsia-600 text-lg shadow-lg shadow-violet-500/20"
                >
                    🧬
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">
                        Learning Blueprint
                    </h3>
                    <p class="text-[0.6rem] text-white/40">
                        Rutas de aprendizaje generadas por IA
                    </p>
                </div>
            </div>
            <StButtonGlass
                size="sm"
                variant="primary"
                @click="generateBlueprint"
                :loading="loading"
            >
                {{ blueprint ? 'Regenerar' : 'Generar Blueprint' }}
            </StButtonGlass>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center py-8">
            <div class="flex flex-col items-center gap-3">
                <div
                    class="h-8 w-8 animate-spin rounded-full border-2 border-violet-500 border-t-transparent"
                ></div>
                <span class="text-xs text-white/40"
                    >Generando blueprint con IA...</span
                >
            </div>
        </div>

        <!-- Error -->
        <div
            v-else-if="error"
            class="rounded-xl border border-rose-500/20 bg-rose-500/5 p-3"
        >
            <p class="text-sm text-rose-300">{{ error }}</p>
        </div>

        <!-- Blueprint Result -->
        <template v-else-if="blueprint">
            <!-- Summary -->
            <div class="mb-4 grid grid-cols-3 gap-3">
                <div
                    class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                >
                    <p class="text-2xl font-black text-violet-400">
                        {{ blueprint.success_probability ?? 0 }}%
                    </p>
                    <p class="text-[0.55rem] text-white/40 uppercase">
                        Prob. Éxito
                    </p>
                </div>
                <div
                    class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                >
                    <p class="text-2xl font-black text-white">
                        {{ blueprint.estimated_months ?? '—' }}m
                    </p>
                    <p class="text-[0.55rem] text-white/40 uppercase">
                        Timeline
                    </p>
                </div>
                <div
                    class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                >
                    <p class="text-2xl font-black text-emerald-400">
                        ${{
                            Math.round((blueprint.projected_roi ?? 0) / 1000)
                        }}K
                    </p>
                    <p class="text-[0.55rem] text-white/40 uppercase">
                        ROI Proyectado
                    </p>
                </div>
            </div>

            <!-- Gaps -->
            <div v-if="blueprint.gaps?.length" class="mb-4">
                <p
                    class="mb-2 text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                >
                    Brechas Identificadas
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <div
                        v-for="gap in blueprint.gaps"
                        :key="gap.skill"
                        class="flex items-center gap-1 rounded-md border border-white/10 bg-white/5 px-2 py-1"
                    >
                        <span class="text-xs text-white/60">{{
                            gap.skill
                        }}</span>
                        <span class="text-[0.6rem] text-rose-400"
                            >{{ gap.current }}→{{ gap.required }}</span
                        >
                    </div>
                </div>
            </div>

            <!-- 70-20-10 Actions -->
            <div v-if="blueprint.actions?.length" class="mb-4">
                <p
                    class="mb-2 text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                >
                    Plan de Desarrollo (70-20-10)
                </p>
                <div class="space-y-2">
                    <div
                        v-for="action in blueprint.actions"
                        :key="action.title"
                        class="rounded-xl border p-3"
                        :class="getModelColor(action.type)"
                    >
                        <div class="flex items-start gap-2">
                            <span class="text-base">{{
                                getModelIcon(action.type)
                            }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-white/80">
                                        {{ action.title }}
                                    </p>
                                    <StBadgeGlass variant="glass" size="sm">{{
                                        getModelLabel(action.type)
                                    }}</StBadgeGlass>
                                </div>
                                <p class="mt-0.5 text-[0.65rem] text-white/40">
                                    {{ action.description }}
                                </p>
                                <p
                                    v-if="action.resource"
                                    class="mt-1 text-[0.6rem] text-indigo-300/60"
                                >
                                    📎 {{ action.resource }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materialize Button -->
            <div class="flex justify-end border-t border-white/5 pt-2">
                <StButtonGlass
                    variant="secondary"
                    size="sm"
                    @click="materialize"
                    :loading="materializing"
                >
                    ✨ Materializar como Ruta de Desarrollo
                </StButtonGlass>
            </div>
        </template>

        <!-- Empty State -->
        <div v-else class="py-6 text-center">
            <p class="text-xs text-white/40">
                Genera un Learning Blueprint para ver la ruta de desarrollo
                óptima
            </p>
        </div>
    </StCardGlass>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import AppLayout from '@/layouts/app/AppLayout.vue';
import { apiHelper } from '@/utils/apiHelper';
import { Head } from '@inertiajs/vue3';
import {
    PhCheckCircle,
    PhGraduationCap,
    PhLightbulb,
    PhShareNetwork,
    PhSparkle,
    PhTrendUp,
    PhUserSwitch,
    PhWarning,
} from '@phosphor-icons/vue';
import { onMounted, ref } from 'vue';

const loading = ref(true);
const dashboardData = ref<any>(null);
const generatingBlueprint = ref(false);
const selectedMatch = ref<any>(null);
const aiBlueprint = ref<any>(null);

const fetchData = async () => {
    loading.value = true;
    try {
        dashboardData.value = await apiHelper.get('/social-learning/dashboard');
    } catch (error) {
        console.error('Error fetching social learning data:', error);
    } finally {
        loading.value = false;
    }
};

const generateBlueprint = async (match: any) => {
    selectedMatch.value = match;
    generatingBlueprint.value = true;
    aiBlueprint.value = null;

    try {
        const response = await apiHelper.post(
            '/social-learning/generate-blueprint',
            {
                mentor_id: match.person_at_risk.id, // En este caso el mentor es el experto en riesgo
                mentee_id: match.potential_mentees[0].mentee.id, // Tomamos el primero por simplicidad en demo
                skill_id: match.skill_critical.id,
            },
        );
        aiBlueprint.value = response;
    } catch (error) {
        console.error('Error generating blueprint:', error);
    } finally {
        generatingBlueprint.value = false;
    }
};

onMounted(fetchData);

const getRiskLevel = (score: number) => {
    if (score > 80) return 'error';
    if (score > 60) return 'warning';
    return 'success';
};
</script>

<template>
    <AppLayout>
        <Head title="Social Learning - Knowledge Transfer">
            <title>Social Learning - Knowledge Transfer</title>
        </Head>

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <header
                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <h1
                        class="flex items-center gap-3 text-3xl font-bold tracking-tight text-white"
                    >
                        <PhShareNetwork :size="32" class="text-indigo-400" />
                        Social Learning Engine
                    </h1>
                    <p class="mt-1 text-gray-400">
                        Mitigación de silos de conocimiento y transferencia
                        viral de capacidades críticas.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 px-4 py-2"
                    >
                        <span
                            class="block text-xs font-bold tracking-widest text-gray-500 uppercase"
                            >Cross-Pollination Index</span
                        >
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold text-emerald-400"
                                >{{
                                    dashboardData?.GlobalMarketCrossPollination
                                }}%</span
                            >
                            <PhTrendUp :size="16" class="text-emerald-500" />
                        </div>
                    </div>
                </div>
            </header>

            <div v-if="loading" class="flex h-64 items-center justify-center">
                <div
                    class="h-8 w-8 animate-spin rounded-full border-4 border-indigo-500/30 border-t-indigo-500"
                ></div>
            </div>

            <div v-else class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column: Critical Knowledge Silos -->
                <div class="space-y-6 lg:col-span-2">
                    <h2
                        class="flex items-center gap-2 text-xl font-semibold text-white"
                    >
                        <PhWarning :size="24" class="text-rose-400" />
                        Knowledge Silos Detectados
                    </h2>

                    <div
                        v-for="(risk, idx) in dashboardData?.ContinuityRisks"
                        :key="idx"
                        class="group relative overflow-hidden rounded-2xl border border-white/5 bg-white/5 p-6 transition-all hover:bg-white/[0.07]"
                    >
                        <div class="mb-6 flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 overflow-hidden rounded-full border-2 border-rose-500/30"
                                >
                                    <img
                                        :src="
                                            'https://ui-avatars.com/api/?name=' +
                                            risk.person_at_risk.full_name
                                        "
                                        alt="Foto de perfil del experto"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">
                                        {{ risk.person_at_risk.full_name }}
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        {{
                                            risk.person_at_risk.role?.name ||
                                            'Expert'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <StBadgeGlass
                                :variant="getRiskLevel(risk.risk_score)"
                            >
                                {{ risk.risk_score }}% Flight Risk
                            </StBadgeGlass>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Skill in Jeopardy -->
                            <div
                                class="rounded-xl border border-white/5 bg-black/20 p-4"
                            >
                                <span
                                    class="text-xs font-bold text-gray-500 uppercase"
                                    >Skill en Riesgo</span
                                >
                                <div class="mt-1 flex items-center gap-2">
                                    <PhLightbulb
                                        :size="20"
                                        class="text-yellow-400"
                                    />
                                    <span class="font-medium text-white">{{
                                        risk.skill_critical.name
                                    }}</span>
                                </div>
                            </div>

                            <!-- Proposed Mitigation -->
                            <div
                                class="rounded-xl border border-indigo-500/20 bg-indigo-500/10 p-4"
                            >
                                <span
                                    class="text-xs font-bold text-indigo-300 uppercase"
                                    >Acción Sugerida</span
                                >
                                <div class="mt-1 flex items-center gap-2">
                                    <PhUserSwitch
                                        :size="20"
                                        class="text-indigo-400"
                                    />
                                    <span class="font-medium text-white"
                                        >Bucle de Mentoría Inversa</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Potential Mentees -->
                        <div class="mt-6">
                            <h4
                                class="mb-3 text-sm font-bold tracking-wider text-gray-400 uppercase"
                            >
                                Sucesores Potenciales (Candidatos Mentees)
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="match in risk.potential_mentees"
                                    :key="match.mentee.id"
                                    class="flex cursor-pointer items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 transition-colors hover:border-indigo-400"
                                >
                                    <img
                                        :src="
                                            'https://ui-avatars.com/api/?name=' +
                                            match.mentee.full_name
                                        "
                                        alt="Foto del sucesor potencial"
                                        class="h-5 w-5 rounded-full"
                                    />
                                    <span class="text-xs text-white">{{
                                        match.mentee.full_name
                                    }}</span>
                                    <span
                                        class="font-mono text-[10px] text-emerald-400"
                                        >{{ match.match_score }}%</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <StButtonGlass
                                variant="primary"
                                size="sm"
                                @click="generateBlueprint(risk)"
                                :loading="
                                    generatingBlueprint &&
                                    selectedMatch === risk
                                "
                            >
                                <PhSparkle class="mr-2" />
                                Generar Social Learning Plan IA
                            </StButtonGlass>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Learning Blueprint Preview -->
                <div class="space-y-6">
                    <h2
                        class="flex items-center gap-2 text-xl font-semibold text-white"
                    >
                        <PhGraduationCap :size="24" class="text-indigo-400" />
                        AI Learning Blueprint
                    </h2>

                    <div
                        v-if="aiBlueprint"
                        class="relative overflow-hidden rounded-2xl border border-indigo-500/30 bg-indigo-900/20 p-6"
                    >
                        <div
                            class="absolute -top-12 -right-12 h-32 w-32 rounded-full bg-indigo-500/20 blur-3xl"
                        ></div>

                        <div class="relative z-10">
                            <div class="mb-6 flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white">
                                    {{ aiBlueprint.blueprint_name }}
                                </h3>
                                <PhCheckCircle
                                    :size="24"
                                    class="text-emerald-400"
                                />
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="step in aiBlueprint.weekly_milestones"
                                    :key="step.week"
                                    class="flex gap-4 rounded-xl border border-white/5 bg-white/5 p-3"
                                >
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500 font-bold text-white"
                                    >
                                        {{ step.week }}
                                    </div>
                                    <div>
                                        <p
                                            class="mb-1 text-sm font-bold text-white"
                                        >
                                            {{ step.objective }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ step.activity }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 border-t border-white/10 pt-6">
                                <span
                                    class="mb-2 block text-xs font-bold text-gray-500 uppercase"
                                    >KPI Éxito Mentor</span
                                >
                                <p
                                    class="text-sm font-medium text-indigo-300 italic"
                                >
                                    "{{ aiBlueprint.success_indicator }}"
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-3">
                                <StButtonGlass
                                    class="w-full"
                                    variant="secondary"
                                    size="sm"
                                    >Aprobar Plan</StButtonGlass
                                >
                                <StButtonGlass
                                    class="w-full"
                                    variant="ghost"
                                    size="sm"
                                    >Ajustar con IA</StButtonGlass
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex h-[400px] flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 p-8 text-center"
                    >
                        <div
                            class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/5"
                        >
                            <PhSparkle :size="32" class="text-gray-500" />
                        </div>
                        <h3 class="font-medium text-gray-400">
                            No hay plan activo
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Selecciona un silo de conocimiento para que la IA
                            diseñe una transferencia de conocimiento.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}
</style>

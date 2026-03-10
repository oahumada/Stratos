<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    PhBrain,
    PhCaretDown,
    PhCaretLeft,
    PhChartBar,
    PhCheckCircle,
    PhClockClockwise,
    PhListBullets,
    PhShieldCheck,
    PhWarningCircle,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import StBadgeGlass from '../../components/StBadgeGlass.vue';
import StButtonGlass from '../../components/StButtonGlass.vue';

const props = defineProps<{
    peopleId: string | number;
}>();

interface TriangulatedSkill {
    skill_id: number;
    raw_score: number;
    stratos_score: number;
    bias_flag: string;
    ai_justification: string;
}

interface TriangulatedCompetency {
    competency_id: number;
    competency_name: string;
    competency_score: number;
    competency_justification: string;
    skills: TriangulatedSkill[];
}

interface ContextEvaluation {
    source: string;
    raw_score: number;
    qualitative_comment: string;
}

interface ContextSkill {
    skill_id: number;
    skill_name: string;
    raw_average_score: number;
    evaluations: ContextEvaluation[];
}

interface ContextCompetency {
    competency_id: number | null;
    competency_name: string;
    skills: ContextSkill[];
}

const loading = ref(true);
const error = ref<string | null>(null);

const report = ref<{
    overall_bias_detected: string;
    triangulated_competencies: TriangulatedCompetency[];
} | null>(null);

const rawContext = ref<ContextCompetency[]>([]);

const loadTriangulation = async () => {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await axios.post(
            `/api/strategic-planning/assessments/${props.peopleId}/triangulate`,
        );

        if (data.status === 'success') {
            report.value = data.report;
            rawContext.value = data.context;
        } else {
            error.value = data.message || 'Error en validación.';
        }
    } catch (e: any) {
        console.error('Failed to triangulate:', e);
        error.value =
            e.response?.data?.message ||
            'Hubo un error interpretando los resultados. Verifique la conexión al AI Orchestrator.';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadTriangulation();
});

const getContextForSkill = (skillId: number): ContextSkill | undefined => {
    for (const comp of rawContext.value) {
        const found = comp.skills.find((s) => s.skill_id === skillId);
        if (found) return found;
    }
    return undefined;
};

const expandedCompetencies = ref<Record<number, boolean>>({});
const toggleCompetency = (id: number) => {
    expandedCompetencies.value[id] = !expandedCompetencies.value[id];
};

const getVariationColor = (stratosScore: number, rawScore: number) => {
    const diff = stratosScore - rawScore;
    if (diff > 0.5) return 'text-emerald-400';
    if (diff < -0.5) return 'text-amber-400';
    return 'text-white/60';
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 px-4 py-8 text-white md:px-8">
        <Head title="AI Triangulation - Stratos 360" />

        <!-- Top Navigation -->
        <div class="mx-auto mb-8 max-w-5xl">
            <StButtonGlass
                :icon="PhCaretLeft"
                variant="ghost"
                class="mb-6 opacity-60 hover:opacity-100"
                @click="router.visit('/talento360/comando')"
            >
                Volver al Comando 360
            </StButtonGlass>

            <div
                class="flex flex-col items-start gap-4 md:flex-row md:items-center"
            >
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 shadow-[0_0_30px_rgba(99,102,241,0.2)]"
                >
                    <PhBrain :size="32" weight="duotone" />
                </div>
                <div>
                    <h1
                        class="text-3xl font-black tracking-tight text-white md:text-4xl"
                    >
                        Triangulación
                        <span
                            class="bg-linear-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent"
                            >Cognitiva 360</span
                        >
                    </h1>
                    <p class="mt-1 font-medium text-white/50">
                        Calibración AI para neutralizar sesgos en las
                        evaluaciones de Talento.
                    </p>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="mx-auto flex max-w-5xl flex-col items-center justify-center rounded-[32px] border border-white/5 bg-slate-900/30 py-32 backdrop-blur-3xl"
        >
            <div class="relative flex h-20 w-20 items-center justify-center">
                <div
                    class="absolute inset-0 animate-ping rounded-full bg-indigo-500/20"
                ></div>
                <div
                    class="absolute inset-2 animate-spin rounded-full border-2 border-indigo-500/20 border-t-indigo-500"
                ></div>
                <PhBrain :size="32" class="animate-pulse text-indigo-400" />
            </div>
            <h3 class="mt-8 text-xl font-bold tracking-tight text-white">
                Analizando patrones y cruzando fuentes...
            </h3>
            <p class="mt-2 text-sm text-white/40">
                El Córtex de Inteligencia Estratégica está trabajando.
            </p>
        </div>

        <!-- Error State -->
        <div
            v-else-if="error"
            class="mx-auto max-w-5xl rounded-3xl border border-red-500/20 bg-red-500/5 p-8 text-center backdrop-blur-xl"
        >
            <PhWarningCircle :size="48" class="mx-auto mb-4 text-red-400" />
            <h3 class="text-xl font-bold text-red-200">
                Error en Triangulación
            </h3>
            <p class="mt-2 text-red-200/60">{{ error }}</p>
            <StButtonGlass
                :icon="PhClockClockwise"
                variant="primary"
                class="mx-auto mt-6"
                @click="loadTriangulation"
            >
                Reintentar
            </StButtonGlass>
        </div>

        <!-- Results State -->
        <div
            v-else-if="report && report.triangulated_competencies"
            class="mx-auto max-w-5xl space-y-6"
        >
            <!-- Overall Summary Card -->
            <div
                class="overflow-hidden rounded-3xl border border-indigo-500/20 bg-slate-900/40 p-8 shadow-2xl backdrop-blur-xl"
            >
                <div class="mb-4 flex items-center gap-3">
                    <PhShieldCheck :size="24" class="text-indigo-400" />
                    <h2 class="text-xl font-bold text-white">
                        Síntesis Antisesgo (General)
                    </h2>
                </div>
                <p class="text-lg leading-relaxed text-indigo-100/70">
                    {{ report.overall_bias_detected }}
                </p>
            </div>

            <!-- Competencies Mapping -->
            <div class="space-y-4">
                <div
                    v-for="comp in report.triangulated_competencies"
                    :key="comp.competency_id"
                    class="overflow-hidden rounded-3xl border border-white/5 bg-white/2 shadow-xl backdrop-blur-xl transition-all duration-300 hover:border-white/10 hover:bg-white/4"
                >
                    <!-- Competency Header Row -->
                    <div
                        class="flex cursor-pointer flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between"
                        @click="toggleCompetency(comp.competency_id)"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/5 text-white/60"
                            >
                                <PhChartBar :size="24" />
                            </div>
                            <div>
                                <h3
                                    class="text-lg font-black tracking-tight text-white"
                                >
                                    {{ comp.competency_name }}
                                </h3>
                                <p
                                    class="line-clamp-1 max-w-md text-sm text-white/40"
                                >
                                    {{ comp.competency_justification }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <div
                                    class="text-[10px] font-bold tracking-widest text-white/30 uppercase"
                                >
                                    Score Triangulado
                                </div>
                                <div
                                    class="text-2xl font-black text-indigo-400"
                                >
                                    {{ comp.competency_score.toFixed(1) }}
                                </div>
                            </div>
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/5 text-white/40 transition-transform duration-300"
                                :class="
                                    expandedCompetencies[comp.competency_id]
                                        ? 'rotate-180'
                                        : ''
                                "
                            >
                                <PhCaretDown :size="16" />
                            </div>
                        </div>
                    </div>

                    <!-- Skills Drill-down -->
                    <div
                        v-show="expandedCompetencies[comp.competency_id]"
                        class="border-t border-white/5 bg-black/20 p-6"
                    >
                        <div class="mb-4 flex items-center gap-2">
                            <PhListBullets :size="16" class="text-white/40" />
                            <h4
                                class="text-sm font-bold tracking-widest text-white/60 uppercase"
                            >
                                Desglose Atómico (Skills)
                            </h4>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div
                                v-for="skill in comp.skills"
                                :key="skill.skill_id"
                                class="rounded-2xl border border-white/5 bg-white/5 p-4"
                            >
                                <div
                                    class="mb-3 flex items-start justify-between"
                                >
                                    <div class="font-bold text-white/90">
                                        {{
                                            getContextForSkill(skill.skill_id)
                                                ?.skill_name ||
                                            `Skill #${skill.skill_id}`
                                        }}
                                    </div>
                                    <div
                                        class="flex items-center gap-2 text-sm font-bold"
                                    >
                                        <div
                                            class="text-white/40 line-through decoration-white/20"
                                        >
                                            {{ skill.raw_score.toFixed(1) }} raw
                                        </div>
                                        <div
                                            class="flex items-center gap-1 text-indigo-300"
                                        >
                                            <PhCheckCircle
                                                :size="16"
                                                weight="fill"
                                            />
                                            {{ skill.stratos_score }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <StBadgeGlass
                                        v-if="
                                            skill.bias_flag &&
                                            skill.bias_flag !== 'None' &&
                                            skill.bias_flag !== 'none'
                                        "
                                        variant="warning"
                                        size="sm"
                                    >
                                        {{ skill.bias_flag }}
                                    </StBadgeGlass>
                                    <StBadgeGlass
                                        v-else
                                        variant="glass"
                                        size="sm"
                                        >Consenso verificado</StBadgeGlass
                                    >
                                </div>
                                <p
                                    class="text-xs leading-relaxed text-white/50 italic"
                                >
                                    "{{ skill.ai_justification }}"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Glass UI utilities taken from modern standards */
</style>

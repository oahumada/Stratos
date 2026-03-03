<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { ref, watch } from 'vue';

interface CareerPath {
    role_id: number;
    role_name: string;
    match_score: number;
    skills_matched: number;
    skills_required: number;
    gaps: Array<{
        skill: string;
        current: number;
        required: number;
        gap: number;
    }>;
    estimated_months: number;
}

interface QuickWin {
    skill: string;
    target_role: string;
    effort: string;
}

interface CareerPathResult {
    person: {
        id: number;
        name: string;
        current_role: string;
        current_skills_count: number;
    };
    career_paths: CareerPath[];
    quick_wins: QuickWin[];
    recommended_path: CareerPath | null;
}

interface TransitionPrediction {
    person: string;
    target_role: string;
    success_probability: number;
    match_score: number;
    factors: {
        skill_match: number;
        tenure_bonus: number;
        development_activity_bonus: number;
    };
    gaps_to_close: Array<{
        skill: string;
        current: number;
        required: number;
        gap: number;
    }>;
    estimated_preparation_months: number;
    recommendation: string;
}

const props = defineProps<{
    peopleId?: number;
}>();

const loading = ref(false);
const loadingPrediction = ref(false);
const result = ref<CareerPathResult | null>(null);
const selectedPath = ref<CareerPath | null>(null);
const prediction = ref<TransitionPrediction | null>(null);
const error = ref<string | null>(null);

async function loadCareerPaths() {
    if (!props.peopleId) return;
    loading.value = true;
    error.value = null;
    try {
        const { data } = await axios.get(`/api/career-paths/${props.peopleId}`);
        result.value = data.data ?? data;
    } catch (e: any) {
        error.value =
            e.response?.data?.message ?? 'Error al cargar rutas de carrera';
    } finally {
        loading.value = false;
    }
}

async function predictTransition(path: CareerPath) {
    if (!props.peopleId) return;
    selectedPath.value = path;
    loadingPrediction.value = true;
    prediction.value = null;
    try {
        const { data } = await axios.get(
            `/api/career-paths/predict/${props.peopleId}/${path.role_id}`,
        );
        prediction.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error en predicción';
    } finally {
        loadingPrediction.value = false;
    }
}

function getMatchColor(score: number): string {
    if (score >= 75) return 'text-emerald-400';
    if (score >= 50) return 'text-amber-400';
    return 'text-rose-400';
}

function getMatchBadge(score: number): 'success' | 'warning' | 'error' {
    if (score >= 75) return 'success';
    if (score >= 50) return 'warning';
    return 'error';
}

function getProbabilityGradient(prob: number): string {
    if (prob >= 70) return 'from-emerald-500 to-teal-400';
    if (prob >= 50) return 'from-amber-500 to-orange-400';
    return 'from-rose-500 to-pink-400';
}

watch(
    () => props.peopleId,
    () => {
        if (props.peopleId) loadCareerPaths();
    },
    { immediate: true },
);
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">
                    <span class="mr-2">🧭</span> Career Path Explorer
                </h2>
                <p class="mt-1 text-sm text-white/50" v-if="result?.person">
                    Rutas de carrera viables para
                    <span class="font-semibold text-white/70">{{
                        result.person.name
                    }}</span>
                    · Rol actual:
                    <span class="text-indigo-300">{{
                        result.person.current_role
                    }}</span>
                </p>
            </div>
            <StButtonGlass
                size="sm"
                @click="loadCareerPaths"
                :loading="loading"
            >
                Recalcular
            </StButtonGlass>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-3">
                <div
                    class="h-10 w-10 animate-spin rounded-full border-2 border-indigo-500 border-t-transparent"
                ></div>
                <span class="text-sm text-white/50"
                    >Calculando rutas óptimas...</span
                >
            </div>
        </div>

        <!-- Error State -->
        <StCardGlass v-else-if="error" class="border-rose-500/30 bg-rose-500/5">
            <p class="text-sm text-rose-300">{{ error }}</p>
        </StCardGlass>

        <!-- Results -->
        <template v-else-if="result">
            <!-- Quick Wins Banner -->
            <StCardGlass
                v-if="result.quick_wins?.length"
                class="border-emerald-500/20 bg-emerald-500/5"
            >
                <div class="mb-3 flex items-center gap-2">
                    <span class="text-lg">⚡</span>
                    <h3
                        class="text-sm font-bold tracking-widest text-emerald-300 uppercase"
                    >
                        Quick Wins
                    </h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="(qw, i) in result.quick_wins"
                        :key="i"
                        class="flex items-center gap-2 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3 py-1.5"
                    >
                        <span class="text-xs font-semibold text-emerald-300">{{
                            qw.skill
                        }}</span>
                        <span class="text-[0.6rem] text-white/40"
                            >→ {{ qw.target_role }}</span
                        >
                        <StBadgeGlass variant="success" size="sm">{{
                            qw.effort
                        }}</StBadgeGlass>
                    </div>
                </div>
            </StCardGlass>

            <!-- Recommended Path (Hero) -->
            <StCardGlass
                v-if="result.recommended_path"
                class="border-indigo-500/30 bg-gradient-to-br from-indigo-500/10 to-purple-500/5"
            >
                <div class="mb-2 flex items-center gap-2">
                    <span class="text-lg">🌟</span>
                    <h3
                        class="text-sm font-bold tracking-widest text-indigo-300 uppercase"
                    >
                        Ruta Recomendada
                    </h3>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xl font-bold text-white">
                            {{ result.recommended_path.role_name }}
                        </p>
                        <p class="mt-1 text-sm text-white/50">
                            {{ result.recommended_path.skills_matched }}/{{
                                result.recommended_path.skills_required
                            }}
                            skills coinciden · ~{{
                                result.recommended_path.estimated_months
                            }}
                            meses
                        </p>
                    </div>
                    <div class="text-right">
                        <div
                            :class="[
                                'text-3xl font-black',
                                getMatchColor(
                                    result.recommended_path.match_score,
                                ),
                            ]"
                        >
                            {{ result.recommended_path.match_score }}%
                        </div>
                        <p
                            class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                        >
                            Match Score
                        </p>
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <StButtonGlass
                        size="sm"
                        variant="primary"
                        @click="predictTransition(result.recommended_path!)"
                    >
                        Predecir Éxito
                    </StButtonGlass>
                </div>
            </StCardGlass>

            <!-- All Career Paths Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <StCardGlass
                    v-for="path in result.career_paths"
                    :key="path.role_id"
                    class="cursor-pointer transition-all duration-300"
                    :class="
                        selectedPath?.role_id === path.role_id
                            ? 'border-indigo-500/40 ring-1 ring-indigo-500/20'
                            : ''
                    "
                    @click="predictTransition(path)"
                >
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-base font-bold text-white">
                                {{ path.role_name }}
                            </p>
                            <p class="mt-1 text-xs text-white/40">
                                {{ path.skills_matched }}/{{
                                    path.skills_required
                                }}
                                skills
                            </p>
                        </div>
                        <StBadgeGlass
                            :variant="getMatchBadge(path.match_score)"
                            size="md"
                        >
                            {{ path.match_score }}%
                        </StBadgeGlass>
                    </div>

                    <!-- Match Bar -->
                    <div class="mt-3">
                        <div
                            class="h-1.5 overflow-hidden rounded-full bg-white/5"
                        >
                            <div
                                class="h-full rounded-full transition-all duration-500"
                                :class="
                                    path.match_score >= 75
                                        ? 'bg-emerald-500'
                                        : path.match_score >= 50
                                          ? 'bg-amber-500'
                                          : 'bg-rose-500'
                                "
                                :style="{ width: `${path.match_score}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Gaps Preview -->
                    <div v-if="path.gaps?.length" class="mt-3 space-y-1">
                        <p
                            class="text-[0.6rem] tracking-widest text-white/30 uppercase"
                        >
                            Brechas a cerrar
                        </p>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="gap in path.gaps.slice(0, 3)"
                                :key="gap.skill"
                                class="rounded-md border border-white/10 bg-white/5 px-1.5 py-0.5 text-[0.65rem] text-white/50"
                            >
                                {{ gap.skill }} ({{ gap.gap > 0 ? '+' : ''
                                }}{{ gap.gap }})
                            </span>
                            <span
                                v-if="path.gaps.length > 3"
                                class="text-[0.65rem] text-white/30"
                            >
                                +{{ path.gaps.length - 3 }} más
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-white/30">
                        ~{{ path.estimated_months }} meses de preparación
                    </div>
                </StCardGlass>
            </div>

            <!-- Transition Prediction Modal -->
            <Transition name="slide-up">
                <StCardGlass v-if="prediction" class="border-indigo-500/30">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🎯</span>
                            <h3
                                class="text-sm font-bold tracking-widest text-indigo-300 uppercase"
                            >
                                Predicción de Transición
                            </h3>
                        </div>
                        <button
                            @click="prediction = null"
                            class="text-sm text-white/30 hover:text-white/60"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Success Probability -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="relative h-28 w-28">
                                <svg
                                    class="h-28 w-28 -rotate-90"
                                    viewBox="0 0 100 100"
                                >
                                    <circle
                                        cx="50"
                                        cy="50"
                                        r="42"
                                        fill="none"
                                        stroke="rgba(255,255,255,0.05)"
                                        stroke-width="8"
                                    />
                                    <circle
                                        cx="50"
                                        cy="50"
                                        r="42"
                                        fill="none"
                                        :stroke="
                                            prediction.success_probability >= 70
                                                ? '#10b981'
                                                : prediction.success_probability >=
                                                    50
                                                  ? '#f59e0b'
                                                  : '#ef4444'
                                        "
                                        stroke-width="8"
                                        stroke-linecap="round"
                                        :stroke-dasharray="`${prediction.success_probability * 2.64} 264`"
                                    />
                                </svg>
                                <div
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <span class="text-2xl font-black text-white"
                                        >{{
                                            prediction.success_probability
                                        }}%</span
                                    >
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-white/40">
                                Probabilidad de Éxito
                            </p>
                        </div>

                        <!-- Factors -->
                        <div class="space-y-3">
                            <p
                                class="text-[0.6rem] tracking-widest text-white/30 uppercase"
                            >
                                Factores
                            </p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-white/60"
                                        >Skill Match</span
                                    >
                                    <span class="text-sm font-bold text-white"
                                        >{{
                                            prediction.factors.skill_match
                                        }}%</span
                                    >
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-white/60"
                                        >Bonus Tenure</span
                                    >
                                    <span
                                        class="text-sm font-bold text-emerald-400"
                                        >+{{
                                            prediction.factors.tenure_bonus
                                        }}</span
                                    >
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-white/60"
                                        >Bonus Desarrollo</span
                                    >
                                    <span
                                        class="text-sm font-bold text-emerald-400"
                                        >+{{
                                            prediction.factors
                                                .development_activity_bonus
                                        }}</span
                                    >
                                </div>
                            </div>
                            <div class="border-t border-white/5 pt-2">
                                <p class="text-xs text-white/40">
                                    ⏱️
                                    {{
                                        prediction.estimated_preparation_months
                                    }}
                                    meses de preparación
                                </p>
                            </div>
                        </div>

                        <!-- Recommendation -->
                        <div>
                            <p
                                class="mb-2 text-[0.6rem] tracking-widest text-white/30 uppercase"
                            >
                                Recomendación
                            </p>
                            <div
                                class="rounded-xl p-3 text-sm"
                                :class="
                                    prediction.success_probability >= 70
                                        ? 'border border-emerald-500/20 bg-emerald-500/10 text-emerald-300'
                                        : prediction.success_probability >= 50
                                          ? 'border border-amber-500/20 bg-amber-500/10 text-amber-300'
                                          : 'border border-rose-500/20 bg-rose-500/10 text-rose-300'
                                "
                            >
                                {{ prediction.recommendation }}
                            </div>
                            <div
                                v-if="prediction.gaps_to_close?.length"
                                class="mt-3"
                            >
                                <p
                                    class="mb-1 text-[0.6rem] tracking-widest text-white/30 uppercase"
                                >
                                    Gaps a cerrar
                                </p>
                                <div class="space-y-1">
                                    <div
                                        v-for="gap in prediction.gaps_to_close.slice(
                                            0,
                                            4,
                                        )"
                                        :key="gap.skill"
                                        class="flex items-center justify-between text-xs"
                                    >
                                        <span class="text-white/50">{{
                                            gap.skill
                                        }}</span>
                                        <span class="text-rose-400"
                                            >{{ gap.current }} →
                                            {{ gap.required }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </StCardGlass>
            </Transition>
        </template>

        <!-- Empty State -->
        <StCardGlass v-else-if="!loading && !result" class="py-12 text-center">
            <p class="text-white/40">
                Selecciona una persona para explorar sus rutas de carrera
            </p>
        </StCardGlass>
    </div>
</template>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(12px);
}
</style>

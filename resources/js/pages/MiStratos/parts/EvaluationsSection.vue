<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhCalendar,
    PhClipboardText,
    PhLightning,
    PhStar,
    PhTarget,
    PhTrendUp,
} from '@phosphor-icons/vue';

interface Props {
    evaluations: Array<any>;
}

defineProps<Props>();

const getScoreColorClass = (score: number) => {
    if (score >= 90) return 'text-emerald-400';
    if (score >= 75) return 'text-indigo-400';
    if (score >= 60) return 'text-amber-400';
    return 'text-rose-400';
};
</script>

<template>
    <div class="animate-in duration-700 fade-in slide-in-from-bottom-4">
        <div
            class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h2
                    class="flex items-center gap-3 text-2xl font-black text-white"
                >
                    <PhClipboardText
                        :size="28"
                        weight="duotone"
                        class="text-indigo-400"
                    />
                    Mis Evaluaciones Históricas
                </h2>
                <p class="mt-1 text-sm font-light text-white/40">
                    Desempeño, evaluaciones 360 y feedback de talento.
                </p>
            </div>
            <div
                class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-2"
            >
                <PhTrendUp
                    :size="20"
                    weight="duotone"
                    class="text-emerald-400"
                />
                <div class="flex flex-col">
                    <span
                        class="text-[9px] leading-none font-black tracking-widest text-white/30 uppercase"
                        >Promedio Global</span
                    >
                    <span class="text-base font-black text-white">88.4%</span>
                </div>
            </div>
        </div>

        <div
            v-if="evaluations.length === 0"
            class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-20"
        >
            <PhClipboardText
                :size="64"
                weight="thin"
                class="mb-6 text-white/20"
            />
            <h3 class="mb-2 text-xl font-bold text-white/60">
                Sin registros oficiales
            </h3>
            <p class="text-sm text-white/40">
                Tus evaluaciones históricas aparecerán aquí.
            </p>
        </div>

        <div v-else class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <StCardGlass
                v-for="ev in evaluations"
                :key="ev.id"
                class="group overflow-hidden p-12!"
            >
                <div class="relative">
                    <div class="mb-8 flex items-start justify-between">
                        <div>
                            <h3
                                class="text-xl font-black text-white transition-colors group-hover:text-indigo-300"
                            >
                                {{ ev.title }}
                            </h3>
                            <div
                                class="mt-1 flex items-center gap-2 text-[10px] font-bold tracking-widest text-white/40 uppercase"
                            >
                                <PhCalendar :size="12" />
                                {{ ev.date }}
                            </div>
                        </div>
                        <div
                            class="flex h-20 w-20 flex-col items-center justify-center rounded-2xl border border-white/10 bg-white/5"
                        >
                            <span
                                :class="[
                                    'text-2xl font-black tracking-tight',
                                    getScoreColorClass(ev.score),
                                ]"
                            >
                                {{ ev.score }}%
                            </span>
                            <span
                                class="text-[8px] font-black tracking-widest text-white/40 uppercase"
                                >Score</span
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Strengths -->
                        <div v-if="ev.strengths?.length">
                            <h4
                                class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-emerald-400/60 uppercase"
                            >
                                <PhStar :size="14" weight="fill" />
                                Fortalezas Destacadas
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="s in ev.strengths"
                                    :key="s"
                                    class="rounded-lg border border-emerald-500/10 bg-emerald-500/10 px-3 py-1 text-[11px] font-medium text-emerald-300"
                                >
                                    {{ s }}
                                </span>
                            </div>
                        </div>

                        <!-- Opportunities -->
                        <div v-if="ev.opportunities?.length">
                            <h4
                                class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-amber-400/60 uppercase"
                            >
                                <PhTarget :size="14" weight="fill" />
                                Oportunidades de Mejora
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="o in ev.opportunities"
                                    :key="o"
                                    class="rounded-lg border border-amber-500/10 bg-amber-500/10 px-3 py-1 text-[11px] font-medium text-amber-300"
                                >
                                    {{ o }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-8 flex justify-end border-t border-white/5 pt-6"
                    >
                        <div
                            class="flex cursor-pointer items-center gap-2 text-xs font-bold tracking-widest text-indigo-400 uppercase transition-colors hover:text-indigo-300"
                        >
                            Ver Reporte Detallado
                            <PhLightning :size="16" weight="duotone" />
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>

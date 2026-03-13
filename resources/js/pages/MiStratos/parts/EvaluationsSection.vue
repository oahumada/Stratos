<script setup lang="ts">
import { 
    PhClipboardText, 
    PhStar, 
    PhLightning, 
    PhTarget,
    PhCalendar,
    PhTrendUp
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';

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
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-3">
                    <PhClipboardText :size="28" weight="duotone" class="text-indigo-400" />
                    Mis Evaluaciones Históricas
                </h2>
                <p class="text-sm text-white/40 mt-1 font-light">Desempeño, evaluaciones 360 y feedback de talento.</p>
            </div>
            <div class="px-4 py-2 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                <PhTrendUp :size="20" weight="duotone" class="text-emerald-400" />
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase tracking-widest text-white/30 leading-none">Promedio Global</span>
                    <span class="text-base font-black text-white">88.4%</span>
                </div>
            </div>
        </div>

        <div v-if="evaluations.length === 0" class="py-20 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed">
            <PhClipboardText :size="64" weight="thin" class="text-white/20 mb-6" />
            <h3 class="text-xl font-bold text-white/60 mb-2">Sin registros oficiales</h3>
            <p class="text-sm text-white/40">Tus evaluaciones históricas aparecerán aquí.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <StCardGlass 
                v-for="ev in evaluations" 
                :key="ev.id"
                class="overflow-hidden group"
            >
                <div class="p-8 relative">
                    <div class="flex items-start justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-white group-hover:text-indigo-300 transition-colors">
                                {{ ev.title }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1 text-white/40 font-bold uppercase tracking-widest text-[10px]">
                                <PhCalendar :size="12" />
                                {{ ev.date }}
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center w-20 h-20 rounded-2xl bg-white/5 border border-white/10">
                            <span :class="['text-2xl font-black tracking-tight', getScoreColorClass(ev.score)]">
                                {{ ev.score }}%
                            </span>
                            <span class="text-[8px] font-black uppercase tracking-widest text-white/40">Score</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Strengths -->
                        <div v-if="ev.strengths?.length">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400/60 mb-3 flex items-center gap-2">
                                <PhStar :size="14" weight="fill" />
                                Fortalezas Destacadas
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="s in ev.strengths" :key="s" class="px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/10 text-[11px] font-medium text-emerald-300">
                                    {{ s }}
                                </span>
                            </div>
                        </div>

                        <!-- Opportunities -->
                        <div v-if="ev.opportunities?.length">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-400/60 mb-3 flex items-center gap-2">
                                <PhTarget :size="14" weight="fill" />
                                Oportunidades de Mejora
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="o in ev.opportunities" :key="o" class="px-3 py-1 rounded-lg bg-amber-500/10 border border-amber-500/10 text-[11px] font-medium text-amber-300">
                                    {{ o }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 flex justify-end">
                         <div class="flex items-center gap-2 text-indigo-400 text-xs font-bold uppercase tracking-widest cursor-pointer hover:text-indigo-300 transition-colors">
                            Ver Reporte Detallado
                            <PhLightning :size="16" weight="duotone" />
                         </div>
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>

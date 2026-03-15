<script setup lang="ts">
import { 
    PhTarget, 
    PhWarningCircle, 
    PhTrendUp,
    PhChartPieSlice,
    PhArrowRight,
    PhSealCheck
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';

interface Props {
    gapAnalysis: any;
}

defineProps<Props>();
const emit = defineEmits(['go-to-learning']);


const getCategoryColor = (category: string) => {
    const cats: Record<string, string> = {
        'Alineado': 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'En Desarrollo': 'bg-amber-500/10 text-amber-400 border-amber-500/20',
        'Brecha Crítica': 'bg-rose-500/10 text-rose-400 border-rose-500/20',
    };
    return cats[category] || 'bg-white/5 text-white/50 border-white/10';
};
</script>

<template>
    <div class="animate-in fade-in slide-in-from-right-4 duration-700">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
            <PhTarget :size="28" weight="duotone" class="text-rose-400" />
            Análisis de Brecha
        </h2>

        <div v-if="gapAnalysis" class="space-y-8">
            <!-- Summary Card -->
            <StCardGlass indicator="rose" class="p-12! border-rose-500/10">
                <div class="flex flex-col md:flex-row items-center gap-10">
                    <!-- Circular Score -->
                    <div class="relative flex items-center justify-center shrink-0">
                        <svg class="w-32 h-32 transform -rotate-90">
                            <circle
                                class="text-white/5"
                                stroke-width="10"
                                stroke="currentColor"
                                fill="transparent"
                                r="56"
                                cx="64"
                                cy="64"
                            />
                            <circle
                                :style="{ strokeDasharray: 351, strokeDashoffset: 351 - (351 * (gapAnalysis.summary?.match_percentage || 0)) / 100 }"
                                stroke-width="10"
                                stroke="currentColor"
                                stroke-linecap="round"
                                fill="transparent"
                                r="56"
                                cx="64"
                                cy="64"
                                class="transition-all duration-1000 ease-out text-rose-500"
                            />
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span class="text-4xl font-black text-white tracking-tighter">
                                {{ gapAnalysis.summary?.match_percentage || 0 }}%
                            </span>
                        </div>
                    </div>

                    <div class="grow space-y-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-2xl font-bold text-white">Match con tu Rol Actual</h3>
                            <div :class="['px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border', getCategoryColor(gapAnalysis.summary?.category)]">
                                {{ gapAnalysis.summary?.category || 'Sin categorizar' }}
                            </div>
                        </div>
                        <p class="text-white/60 font-light text-lg max-w-xl">
                            Este porcentaje representa cuánto de tu perfil actual (competencias y habilidades) se alinea con las exigencias de tu posición.
                        </p>
                        <div class="flex gap-4 pt-2">
                             <div class="flex items-center gap-2 text-white/40 text-xs font-bold uppercase tracking-widest">
                                <PhTrendUp :size="16" class="text-emerald-400" />
                                +2% este mes
                             </div>
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Detailed Gaps Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Gaps List -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h4 class="text-sm font-black uppercase tracking-widest text-white/40">Brechas Detectadas</h4>
                        <span class="text-[10px] font-bold text-rose-400">Prioridad: Crítica</span>
                    </div>

                    <div v-if="!gapAnalysis.gaps?.length" class="py-12 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed opacity-50">
                        <PhSealCheck :size="48" weight="duotone" class="text-emerald-400" />
                        <p class="mt-4 text-sm font-light text-emerald-200">¡Felicidades! No hay brechas críticas detectadas.</p>
                    </div>

                    <div v-else class="space-y-3">
                        <StCardGlass 
                            v-for="(gap, idx) in gapAnalysis.gaps.filter((g: any) => g.gap > 0).slice(0, 10)" 
                            :key="idx"
                            class="p-8 hover:bg-white/5 transition-colors group"
                        >
                            <div class="flex items-center gap-6">
                                <div class="grow">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-bold text-white group-hover:text-rose-300 transition-colors">{{ gap.skill_name }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-tighter text-white/30">Gap:</span>
                                            <span class="text-xs font-black text-rose-400">{{ gap.gap }}</span>
                                        </div>
                                    </div>
                                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden flex">
                                        <div 
                                            class="h-full bg-emerald-500/40"
                                            :style="{ width: `${(gap.current / gap.required) * 100}%` }"
                                        />
                                        <div 
                                            class="h-full bg-rose-500 animate-pulse"
                                            :style="{ width: `${(gap.gap / gap.required) * 100}%` }"
                                        />
                                    </div>
                                    <div class="flex justify-between mt-2 text-[10px] uppercase font-bold tracking-widest text-white/20">
                                        <span>Actual: {{ gap.current }}</span>
                                        <span>Meta: {{ gap.required }}</span>
                                    </div>
                                </div>
                            </div>
                        </StCardGlass>
                    </div>
                </div>

                <!-- Insights / Call to Action -->
                <div class="space-y-6">
                     <StCardGlass indicator="indigo" class="p-10! bg-indigo-500/5 border-indigo-500/20">
                        <PhChartPieSlice :size="32" weight="duotone" class="text-indigo-400 mb-4" />
                        <h4 class="text-lg font-bold text-white mb-2">Análisis IA</h4>
                        <p class="text-sm text-white/60 font-light leading-relaxed mb-6">
                            Tu brecha más significativa está en habilidades tecnológicas. Cerrar esta brecha aumentaría tu match global en un 12%.
                        </p>
                        <StButtonGlass variant="primary" size="md" full @click="emit('go-to-learning')">
                            Iniciar Plan de Acción
                            <PhArrowRight :size="16" weight="bold" />
                        </StButtonGlass>
                     </StCardGlass>

                     <div class="p-6 rounded-3xl bg-white/2 border border-white/5 flex items-start gap-4">
                        <PhWarningCircle :size="24" weight="fill" class="text-amber-400 shrink-0" />
                        <div>
                            <div class="text-xs font-bold text-amber-200 uppercase mb-1">Nota de Calidad</div>
                            <p class="text-[10px] text-white/40 leading-relaxed uppercase tracking-wider">
                                El análisis se actualiza cada vez que completas una evaluación o hito de aprendizaje.
                            </p>
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <div v-else class="py-20 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed">
            <PhChartPieSlice :size="64" weight="thin" class="text-white/20 mb-6" />
            <h3 class="text-xl font-bold text-white/60 mb-2">Sin análisis disponible</h3>
            <p class="text-sm text-white/40">Completa tu perfil para activar el análisis de brecha.</p>
        </div>
    </div>
</template>

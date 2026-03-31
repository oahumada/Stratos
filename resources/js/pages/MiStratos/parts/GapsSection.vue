<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowRight,
    PhChartPieSlice,
    PhSealCheck,
    PhTarget,
    PhTrendUp,
    PhWarningCircle,
} from '@phosphor-icons/vue';

interface Props {
    gapAnalysis: any;
}

defineProps<Props>();
const emit = defineEmits(['go-to-learning']);

const getCategoryColor = (category: string) => {
    const cats: Record<string, string> = {
        Alineado: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'En Desarrollo': 'bg-amber-500/10 text-amber-400 border-amber-500/20',
        'Brecha Crítica': 'bg-rose-500/10 text-rose-400 border-rose-500/20',
    };
    return cats[category] || 'bg-white/5 text-white/50 border-white/10';
};
</script>

<template>
    <div class="animate-in duration-700 fade-in slide-in-from-right-4">
        <h2 class="mb-6 flex items-center gap-3 text-2xl font-black text-white">
            <PhTarget :size="28" weight="duotone" class="text-rose-400" />
            Análisis de Brecha
        </h2>

        <div v-if="gapAnalysis" class="space-y-8">
            <!-- Summary Card -->
            <StCardGlass indicator="rose" class="border-rose-500/10 p-12!">
                <div class="flex flex-col items-center gap-10 md:flex-row">
                    <!-- Circular Score -->
                    <div
                        class="relative flex shrink-0 items-center justify-center"
                    >
                        <svg class="h-32 w-32 -rotate-90 transform">
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
                                :style="{
                                    strokeDasharray: 351,
                                    strokeDashoffset:
                                        351 -
                                        (351 *
                                            (gapAnalysis.summary
                                                ?.match_percentage || 0)) /
                                            100,
                                }"
                                stroke-width="10"
                                stroke="currentColor"
                                stroke-linecap="round"
                                fill="transparent"
                                r="56"
                                cx="64"
                                cy="64"
                                class="text-rose-500 transition-all duration-1000 ease-out"
                            />
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span
                                class="text-4xl font-black tracking-tighter text-white"
                            >
                                {{
                                    gapAnalysis.summary?.match_percentage || 0
                                }}%
                            </span>
                        </div>
                    </div>

                    <div class="grow space-y-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-2xl font-bold text-white">
                                Match con tu Rol Actual
                            </h3>
                            <div
                                :class="[
                                    'rounded-full border px-3 py-1 text-[10px] font-black tracking-wider uppercase',
                                    getCategoryColor(
                                        gapAnalysis.summary?.category,
                                    ),
                                ]"
                            >
                                {{
                                    gapAnalysis.summary?.category ||
                                    'Sin categorizar'
                                }}
                            </div>
                        </div>
                        <p class="max-w-xl text-lg font-light text-white/60">
                            Este porcentaje representa cuánto de tu perfil
                            actual (competencias y habilidades) se alinea con
                            las exigencias de tu posición.
                        </p>
                        <div class="flex gap-4 pt-2">
                            <div
                                class="flex items-center gap-2 text-xs font-bold tracking-widest text-white/40 uppercase"
                            >
                                <PhTrendUp
                                    :size="16"
                                    class="text-emerald-400"
                                />
                                +2% este mes
                            </div>
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Detailed Gaps Grid -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Gaps List -->
                <div class="space-y-4 lg:col-span-2">
                    <div class="flex items-center justify-between px-2">
                        <h4
                            class="text-sm font-black tracking-widest text-white/40 uppercase"
                        >
                            Brechas Detectadas
                        </h4>
                        <span class="text-[10px] font-bold text-rose-400"
                            >Prioridad: Crítica</span
                        >
                    </div>

                    <div
                        v-if="!gapAnalysis.gaps?.length"
                        class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-12 opacity-50"
                    >
                        <PhSealCheck
                            :size="48"
                            weight="duotone"
                            class="text-emerald-400"
                        />
                        <p class="mt-4 text-sm font-light text-emerald-200">
                            ¡Felicidades! No hay brechas críticas detectadas.
                        </p>
                    </div>

                    <div v-else class="space-y-3">
                        <StCardGlass
                            v-for="(gap, idx) in gapAnalysis.gaps
                                .filter((g: any) => g.gap > 0)
                                .slice(0, 10)"
                            :key="idx"
                            class="group p-8 transition-colors hover:bg-white/5"
                        >
                            <div class="flex items-center gap-6">
                                <div class="grow">
                                    <div
                                        class="mb-2 flex items-center justify-between"
                                    >
                                        <span
                                            class="font-bold text-white transition-colors group-hover:text-rose-300"
                                            >{{ gap.skill_name }}</span
                                        >
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-black tracking-tighter text-white/30 uppercase"
                                                >Gap:</span
                                            >
                                            <span
                                                class="text-xs font-black text-rose-400"
                                                >{{ gap.gap }}</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                                    >
                                        <div
                                            class="h-full bg-emerald-500/40"
                                            :style="{
                                                width: `${(gap.current / gap.required) * 100}%`,
                                            }"
                                        />
                                        <div
                                            class="h-full animate-pulse bg-rose-500"
                                            :style="{
                                                width: `${(gap.gap / gap.required) * 100}%`,
                                            }"
                                        />
                                    </div>
                                    <div
                                        class="mt-2 flex justify-between text-[10px] font-bold tracking-widest text-white/20 uppercase"
                                    >
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
                    <StCardGlass
                        indicator="indigo"
                        class="border-indigo-500/20 bg-indigo-500/5 p-10!"
                    >
                        <PhChartPieSlice
                            :size="32"
                            weight="duotone"
                            class="mb-4 text-indigo-400"
                        />
                        <h4 class="mb-2 text-lg font-bold text-white">
                            Análisis IA
                        </h4>
                        <p
                            class="mb-6 text-sm leading-relaxed font-light text-white/60"
                        >
                            Tu brecha más significativa está en habilidades
                            tecnológicas. Cerrar esta brecha aumentaría tu match
                            global en un 12%.
                        </p>
                        <StButtonGlass
                            variant="primary"
                            size="md"
                            full
                            @click="emit('go-to-learning')"
                        >
                            Iniciar Plan de Acción
                            <PhArrowRight :size="16" weight="bold" />
                        </StButtonGlass>
                    </StCardGlass>

                    <div
                        class="flex items-start gap-4 rounded-3xl border border-white/5 bg-white/2 p-6"
                    >
                        <PhWarningCircle
                            :size="24"
                            weight="fill"
                            class="shrink-0 text-amber-400"
                        />
                        <div>
                            <div
                                class="mb-1 text-xs font-bold text-amber-200 uppercase"
                            >
                                Nota de Calidad
                            </div>
                            <p
                                class="text-[10px] leading-relaxed tracking-wider text-white/40 uppercase"
                            >
                                El análisis se actualiza cada vez que completas
                                una evaluación o hito de aprendizaje.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-20"
        >
            <PhChartPieSlice
                :size="64"
                weight="thin"
                class="mb-6 text-white/20"
            />
            <h3 class="mb-2 text-xl font-bold text-white/60">
                Sin análisis disponible
            </h3>
            <p class="text-sm text-white/40">
                Completa tu perfil para activar el análisis de brecha.
            </p>
        </div>
    </div>
</template>

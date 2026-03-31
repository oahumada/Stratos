<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowRight,
    PhBooks,
    PhCalendar,
    PhCheckCircle,
    PhGraduationCap,
    PhRoadHorizon,
} from '@phosphor-icons/vue';

interface Props {
    learningPaths: Array<any>;
    formatDate: (date: string) => string;
}

defineProps<Props>();

const getStatusColor = (status: string) => {
    const statuses: Record<string, string> = {
        completada: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'en progreso': 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
        pendiente: 'bg-white/5 text-white/40 border-white/10',
    };
    return (
        statuses[status.toLowerCase()] ||
        'bg-white/5 text-white/40 border-white/10'
    );
};

const getProgressColor = (progress: number) => {
    if (progress >= 80) return 'bg-emerald-500';
    if (progress >= 40) return 'bg-indigo-500';
    return 'bg-amber-500';
};
</script>

<template>
    <div class="animate-in duration-700 fade-in slide-in-from-bottom-4">
        <h2 class="mb-6 flex items-center gap-3 text-2xl font-black text-white">
            <PhGraduationCap
                :size="28"
                weight="duotone"
                class="text-indigo-400"
            />
            Mi Ruta de Aprendizaje
        </h2>

        <div
            v-if="learningPaths.length === 0"
            class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-20"
        >
            <PhBooks :size="64" weight="thin" class="mb-6 text-white/20" />
            <h3 class="mb-2 text-xl font-bold text-white/60">
                No tienes rutas asignadas
            </h3>
            <p class="text-sm text-white/40">
                Tus planes de desarrollo aparecerán aquí.
            </p>
        </div>

        <div v-else class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <StCardGlass
                v-for="path in learningPaths"
                :key="path.id"
                indicator="indigo"
                class="group relative overflow-hidden p-12!"
            >
                <!-- Decorative background gradient -->
                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-indigo-500/5 to-transparent"
                />

                <div class="relative">
                    <div class="mb-8 flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-400"
                            >
                                <PhRoadHorizon :size="28" weight="duotone" />
                            </div>
                            <div>
                                <h4
                                    class="text-xl leading-tight font-black text-white transition-colors group-hover:text-indigo-300"
                                >
                                    {{ path.title }}
                                </h4>
                                <div
                                    class="mt-1 flex items-center gap-2 text-[10px] font-bold tracking-widest text-white/40 uppercase"
                                >
                                    <PhCalendar :size="12" />
                                    {{ formatDate(path.created_at) }}
                                </div>
                            </div>
                        </div>
                        <div
                            :class="[
                                'rounded-full border px-3 py-1 text-[10px] font-black tracking-wider uppercase',
                                getStatusColor(path.status),
                            ]"
                        >
                            {{ path.status }}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <span
                                    class="text-xs font-bold tracking-widest text-white/60 uppercase"
                                    >Progreso Global</span
                                >
                                <span class="text-lg font-black text-white"
                                    >{{ path.progress }}%</span
                                >
                            </div>
                            <div
                                class="h-2 w-full overflow-hidden rounded-full bg-white/5"
                            >
                                <div
                                    class="h-full transition-all duration-1000 ease-out"
                                    :class="getProgressColor(path.progress)"
                                    :style="{ width: `${path.progress}%` }"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between border-t border-white/5 pt-4"
                        >
                            <div class="flex items-center gap-2 text-white/40">
                                <PhCheckCircle
                                    :size="18"
                                    class="text-emerald-400"
                                />
                                <span class="text-xs font-medium">
                                    <strong class="text-white">{{
                                        path.completed_actions
                                    }}</strong>
                                    / {{ path.total_actions }} hitos
                                </span>
                            </div>
                            <StButtonGlass variant="secondary" size="sm">
                                Continuar
                                <PhArrowRight :size="14" weight="bold" />
                            </StButtonGlass>
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>

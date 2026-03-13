<script setup lang="ts">
import { 
    PhGraduationCap, 
    PhRoadHorizon, 
    PhCalendar, 
    PhCheckCircle,
    PhArrowRight,
    PhBooks
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';

interface Props {
    learningPaths: Array<any>;
    formatDate: (date: string) => string;
}

defineProps<Props>();

const getStatusColor = (status: string) => {
    const statuses: Record<string, string> = {
        'completada': 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'en progreso': 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
        'pendiente': 'bg-white/5 text-white/40 border-white/10',
    };
    return statuses[status.toLowerCase()] || 'bg-white/5 text-white/40 border-white/10';
};

const getProgressColor = (progress: number) => {
    if (progress >= 80) return 'bg-emerald-500';
    if (progress >= 40) return 'bg-indigo-500';
    return 'bg-amber-500';
};
</script>

<template>
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
            <PhGraduationCap :size="28" weight="duotone" class="text-indigo-400" />
            Mi Ruta de Aprendizaje
        </h2>

        <div v-if="learningPaths.length === 0" class="py-20 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed">
            <PhBooks :size="64" weight="thin" class="text-white/20 mb-6" />
            <h3 class="text-xl font-bold text-white/60 mb-2">No tienes rutas asignadas</h3>
            <p class="text-sm text-white/40">Tus planes de desarrollo aparecerán aquí.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <StCardGlass 
                v-for="path in learningPaths" 
                :key="path.id"
                class="overflow-hidden group"
            >
                <!-- Decorative background gradient -->
                <div class="absolute inset-0 bg-linear-to-br from-indigo-500/5 to-transparent pointer-events-none" />
                
                <div class="p-8 relative">
                    <div class="flex items-start justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                                <PhRoadHorizon :size="28" weight="duotone" />
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-white leading-tight group-hover:text-indigo-300 transition-colors">
                                    {{ path.title }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-white/40 font-bold uppercase tracking-widest text-[10px]">
                                    <PhCalendar :size="12" />
                                    {{ formatDate(path.created_at) }}
                                </div>
                            </div>
                        </div>
                        <div :class="['px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border', getStatusColor(path.status)]">
                            {{ path.status }}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-white/60 uppercase tracking-widest">Progreso Global</span>
                                <span class="text-lg font-black text-white">{{ path.progress }}%</span>
                            </div>
                            <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden">
                                <div 
                                    class="h-full transition-all duration-1000 ease-out"
                                    :class="getProgressColor(path.progress)"
                                    :style="{ width: `${path.progress}%` }"
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-white/5">
                            <div class="flex items-center gap-2 text-white/40">
                                <PhCheckCircle :size="18" class="text-emerald-400" />
                                <span class="text-xs font-medium">
                                    <strong class="text-white">{{ path.completed_actions }}</strong> / {{ path.total_actions }} hitos
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

<script setup lang="ts">
import { 
    PhLightning, 
    PhShieldCheck, 
    PhGraduationCap, 
    PhStar, 
    PhWarningCircle, 
    PhChatTeardropDots, 
    PhBooks,
    PhArrowRight,
    PhRobot,
    PhChartLineUp,
    PhMonitorPlay
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';

interface Props {
    kpis: any;
    conversations: Array<any>;
    learningPaths: Array<any>;
    greeting: string;
}

defineProps<Props>();
const emit = defineEmits(['go-to-learning', 'toggle-mentor-chat']);

const kpiColorClass = (value: number) => {
    if (value >= 80) return 'text-emerald-400';
    if (value >= 60) return 'text-amber-400';
    if (value >= 40) return 'text-orange-400';
    return 'text-rose-400';
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        completed: 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
        active: 'text-indigo-400 bg-indigo-400/10 border-indigo-400/20',
        pending: 'text-amber-400 bg-amber-400/10 border-amber-400/20',
        in_progress: 'text-cyan-400 bg-cyan-400/10 border-cyan-400/20',
    };
    return colors[status] || 'text-slate-400 bg-slate-400/10 border-slate-400/20';
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('es-CL', {
        day: 'numeric',
        month: 'short',
    });
};
</script>

<template>
    <div class="animate-in fade-in duration-700">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
            <PhChartLineUp :size="28" weight="duotone" class="text-indigo-400" />
            Tu mapa de hoy
        </h2>

        <!-- KPI Cards Grid -->
        <div v-if="kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Potential -->
            <StCardGlass 
                indicator="indigo"
                class="group p-12! border-white/5 hover:border-indigo-500/30 transition-all duration-500"
            >
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 rounded-2xl bg-indigo-500/10 text-indigo-400 group-hover:scale-110 transition-transform">
                        <PhLightning :size="24" weight="duotone" />
                    </div>
                    <div :class="['text-3xl font-black tracking-tighter', kpiColorClass(kpis.potential)]">
                        {{ kpis.potential }}%
                    </div>
                </div>
                <div class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-2">Potencial de Match</div>
                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    <div 
                        class="h-full bg-linear-to-r from-indigo-500 to-indigo-400 transition-all duration-1000"
                        :style="{ width: `${kpis.potential}%` }"
                    />
                </div>
            </StCardGlass>

            <!-- Readiness -->
            <StCardGlass 
                indicator="emerald"
                class="group p-12! border-white/5 hover:border-emerald-500/30 transition-all duration-500"
            >
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 rounded-2xl bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition-transform">
                        <PhShieldCheck :size="24" weight="duotone" />
                    </div>
                    <div :class="['text-3xl font-black tracking-tighter', kpiColorClass(kpis.readiness)]">
                        {{ kpis.readiness }}%
                    </div>
                </div>
                <div class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-2">Readiness Operativa</div>
                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    <div 
                        class="h-full bg-linear-to-r from-emerald-500 to-emerald-400 transition-all duration-1000"
                        :style="{ width: `${kpis.readiness}%` }"
                    />
                </div>
            </StCardGlass>

            <!-- Learning -->
            <StCardGlass 
                indicator="amber"
                class="group p-12! border-white/5 hover:border-amber-500/30 transition-all duration-500"
            >
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 rounded-2xl bg-amber-500/10 text-amber-400 group-hover:scale-110 transition-transform">
                        <PhGraduationCap :size="24" weight="duotone" />
                    </div>
                    <div :class="['text-3xl font-black tracking-tighter', kpiColorClass(kpis.learning)]">
                        {{ kpis.learning }}%
                    </div>
                </div>
                <div class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-2">Velocidad de Aprendizaje</div>
                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    <div 
                        class="h-full bg-linear-to-r from-amber-500 to-amber-400 transition-all duration-1000"
                        :style="{ width: `${kpis.learning}%` }"
                    />
                </div>
            </StCardGlass>

            <!-- Skills/Gaps -->
            <StCardGlass 
                indicator="fuchsia"
                class="group p-12! border-white/5 hover:border-fuchsia-500/30 transition-all duration-500"
            >
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 rounded-2xl bg-fuchsia-500/10 text-fuchsia-400 group-hover:scale-110 transition-transform">
                        <PhStar :size="24" weight="duotone" />
                    </div>
                    <div class="text-3xl font-black tracking-tighter text-fuchsia-400">
                        {{ kpis.skills_count }}
                    </div>
                </div>
                <div class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-2">Puntos de Maestría (Skills)</div>
                <div class="flex items-center gap-2 mt-2">
                    <PhWarningCircle :size="16" weight="fill" class="text-rose-400" />
                    <span class="text-xs font-bold text-rose-300">{{ kpis.gap_count }} brechas prioritarias</span>
                </div>
            </StCardGlass>
        </div>

        <!-- Next Step Premium Panel -->
        <StCardGlass v-if="kpis" class="relative group overflow-visible p-0 mb-12 border-indigo-500/30 shadow-[0_0_50px_-12px_rgba(79,70,229,0.3)]">
            <div class="absolute inset-0 bg-linear-to-r from-indigo-600/10 to-transparent pointer-events-none rounded-3xl" />
            <div class="relative p-16! flex flex-col md:flex-row items-center gap-12">
                <div class="grow flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400">Tu siguiente paso recomendado</span>
                    </div>
                    <h3 class="text-3xl font-black text-white tracking-tight">Avanza en tu Ruta de Aprendizaje</h3>
                    <p class="text-white/60 text-lg max-w-2xl font-light">
                        En base a tu match actual y tus brechas críticas, hemos priorizado acciones para acelerar tu desarrollo profesional.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0">
                    <StButtonGlass 
                        variant="primary" 
                        size="lg" 
                        class="shadow-indigo-500/40"
                        @click="emit('go-to-learning')"
                    >
                        Ver mi Ruta
                        <PhArrowRight :size="20" weight="bold" />
                    </StButtonGlass>
                    <button 
                        class="flex items-center gap-2 text-white/70 hover:text-cyan-400 transition-colors py-2 px-4 rounded-xl hover:bg-white/5 active:scale-95"
                        @click="emit('toggle-mentor-chat')"
                    >
                        <PhRobot :size="24" weight="duotone" class="animate-pulse" />
                        <span class="text-sm font-bold uppercase tracking-wider">Hablar con Mentor IA</span>
                    </button>
                </div>
            </div>
        </StCardGlass>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Conversations -->
            <StCardGlass class="p-12!">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <PhChatTeardropDots :size="24" weight="duotone" class="text-fuchsia-400" />
                        Conversaciones Activas
                    </h3>
                    <span v-if="conversations.length" class="text-[10px] font-black text-white/30 uppercase tracking-widest">Recientes</span>
                </div>

                <div v-if="conversations.length === 0" class="py-12 flex flex-col items-center justify-center opacity-40">
                    <PhChatTeardropDots :size="48" weight="thin" />
                    <p class="mt-4 text-sm font-light">Sin conversaciones pendientes</p>
                </div>

                <div v-else class="space-y-3">
                    <div v-for="conv in conversations" :key="conv.id" class="group flex items-center justify-between p-4 rounded-2xl bg-white/2 border border-white/5 hover:border-white/10 hover:bg-white/5 transition-all cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/60 group-hover:text-indigo-400 transition-colors">
                                <PhMonitorPlay :size="20" weight="duotone" />
                            </div>
                            <div>
                                <div class="text-sm font-bold text-white group-hover:translate-x-1 transition-transform">
                                    {{ conv.type.charAt(0).toUpperCase() + conv.type.slice(1) }}
                                </div>
                                <div class="text-[10px] text-white/40 uppercase tracking-wider">{{ formatDate(conv.created_at) }}</div>
                            </div>
                        </div>
                        <div :class="['px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest', getStatusColor(conv.status)]">
                            {{ conv.status }}
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Learning Quick Preview -->
            <StCardGlass class="p-12!">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <PhBooks :size="24" weight="duotone" class="text-emerald-400" />
                        Rutas de Aprendizaje
                    </h3>
                    <StButtonGlass variant="ghost" size="sm" @click="emit('go-to-learning')">Ver todas</StButtonGlass>
                </div>

                <div v-if="learningPaths.length === 0" class="py-12 flex flex-col items-center justify-center opacity-40">
                    <PhBooks :size="48" weight="thin" />
                    <p class="mt-4 text-sm font-light">Sin rutas asignadas</p>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="path in learningPaths.slice(0, 2)" :key="path.id" class="p-4 rounded-2xl bg-white/2 border border-white/5">
                        <div class="flex justify-between items-start mb-3">
                            <div class="font-bold text-white leading-tight max-w-[70%]">{{ path.title }}</div>
                            <div :class="['text-xs font-black tracking-tight', kpiColorClass(path.progress)]">{{ path.progress }}%</div>
                        </div>
                        <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden mb-2">
                            <div 
                                class="h-full bg-linear-to-r from-emerald-500 to-emerald-400 transition-all duration-1000"
                                :style="{ width: `${path.progress}%` }"
                            />
                        </div>
                        <div class="flex justify-between items-center text-[10px] uppercase tracking-widest text-white/30">
                            <span>{{ path.completed_actions }}/{{ path.total_actions }} Acciones</span>
                            <span :class="getStatusColor(path.status)">{{ path.status }}</span>
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>

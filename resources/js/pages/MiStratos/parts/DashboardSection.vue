<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowRight,
    PhBooks,
    PhChartLineUp,
    PhChatTeardropDots,
    PhGraduationCap,
    PhLightning,
    PhMonitorPlay,
    PhRobot,
    PhShieldCheck,
    PhStar,
    PhWarningCircle,
} from '@phosphor-icons/vue';

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
    return (
        colors[status] || 'text-slate-400 bg-slate-400/10 border-slate-400/20'
    );
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
    <div class="animate-in duration-700 fade-in">
        <h2 class="mb-6 flex items-center gap-3 text-2xl font-black text-white">
            <PhChartLineUp
                :size="28"
                weight="duotone"
                class="text-indigo-400"
            />
            Tu mapa de hoy
        </h2>

        <!-- KPI Cards Grid -->
        <div
            v-if="kpis"
            class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
        >
            <!-- Potential -->
            <StCardGlass
                indicator="indigo"
                class="group border-white/5 p-12! transition-all duration-500 hover:border-indigo-500/30"
            >
                <div class="mb-4 flex items-start justify-between">
                    <div
                        class="rounded-2xl bg-indigo-500/10 p-3 text-indigo-400 transition-transform group-hover:scale-110"
                    >
                        <PhLightning :size="24" weight="duotone" />
                    </div>
                    <div
                        :class="[
                            'text-3xl font-black tracking-tighter',
                            kpiColorClass(kpis.potential),
                        ]"
                    >
                        {{ kpis.potential }}%
                    </div>
                </div>
                <div
                    class="mb-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                >
                    Potencial de Match
                </div>
                <div
                    class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                >
                    <div
                        class="h-full bg-linear-to-r from-indigo-500 to-indigo-400 transition-all duration-1000"
                        :style="{ width: `${kpis.potential}%` }"
                    />
                </div>
            </StCardGlass>

            <!-- Readiness -->
            <StCardGlass
                indicator="emerald"
                class="group border-white/5 p-12! transition-all duration-500 hover:border-emerald-500/30"
            >
                <div class="mb-4 flex items-start justify-between">
                    <div
                        class="rounded-2xl bg-emerald-500/10 p-3 text-emerald-400 transition-transform group-hover:scale-110"
                    >
                        <PhShieldCheck :size="24" weight="duotone" />
                    </div>
                    <div
                        :class="[
                            'text-3xl font-black tracking-tighter',
                            kpiColorClass(kpis.readiness),
                        ]"
                    >
                        {{ kpis.readiness }}%
                    </div>
                </div>
                <div
                    class="mb-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                >
                    Readiness Operativa
                </div>
                <div
                    class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                >
                    <div
                        class="h-full bg-linear-to-r from-emerald-500 to-emerald-400 transition-all duration-1000"
                        :style="{ width: `${kpis.readiness}%` }"
                    />
                </div>
            </StCardGlass>

            <!-- Learning -->
            <StCardGlass
                indicator="amber"
                class="group border-white/5 p-12! transition-all duration-500 hover:border-amber-500/30"
            >
                <div class="mb-4 flex items-start justify-between">
                    <div
                        class="rounded-2xl bg-amber-500/10 p-3 text-amber-400 transition-transform group-hover:scale-110"
                    >
                        <PhGraduationCap :size="24" weight="duotone" />
                    </div>
                    <div
                        :class="[
                            'text-3xl font-black tracking-tighter',
                            kpiColorClass(kpis.learning),
                        ]"
                    >
                        {{ kpis.learning }}%
                    </div>
                </div>
                <div
                    class="mb-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                >
                    Velocidad de Aprendizaje
                </div>
                <div
                    class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                >
                    <div
                        class="h-full bg-linear-to-r from-amber-500 to-amber-400 transition-all duration-1000"
                        :style="{ width: `${kpis.learning}%` }"
                    />
                </div>
            </StCardGlass>

            <!-- Skills/Gaps -->
            <StCardGlass
                indicator="fuchsia"
                class="group border-white/5 p-12! transition-all duration-500 hover:border-fuchsia-500/30"
            >
                <div class="mb-4 flex items-start justify-between">
                    <div
                        class="rounded-2xl bg-fuchsia-500/10 p-3 text-fuchsia-400 transition-transform group-hover:scale-110"
                    >
                        <PhStar :size="24" weight="duotone" />
                    </div>
                    <div
                        class="text-3xl font-black tracking-tighter text-fuchsia-400"
                    >
                        {{ kpis.skills_count }}
                    </div>
                </div>
                <div
                    class="mb-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                >
                    Puntos de Maestría (Skills)
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <PhWarningCircle
                        :size="16"
                        weight="fill"
                        class="text-rose-400"
                    />
                    <span class="text-xs font-bold text-rose-300"
                        >{{ kpis.gap_count }} brechas prioritarias</span
                    >
                </div>
            </StCardGlass>
        </div>

        <!-- Next Step Premium Panel -->
        <StCardGlass
            v-if="kpis"
            class="group relative mb-12 overflow-visible border-indigo-500/30 p-0 shadow-[0_0_50px_-12px_rgba(79,70,229,0.3)]"
        >
            <div
                class="pointer-events-none absolute inset-0 rounded-3xl bg-linear-to-r from-indigo-600/10 to-transparent"
            />
            <div
                class="relative flex flex-col items-center gap-12 p-16! md:flex-row"
            >
                <div class="flex grow flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex h-3 w-3 rounded-full bg-indigo-500"
                            ></span>
                        </span>
                        <span
                            class="text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                            >Tu siguiente paso recomendado</span
                        >
                    </div>
                    <h3 class="text-3xl font-black tracking-tight text-white">
                        Avanza en tu Ruta de Aprendizaje
                    </h3>
                    <p class="max-w-2xl text-lg font-light text-white/60">
                        En base a tu match actual y tus brechas críticas, hemos
                        priorizado acciones para acelerar tu desarrollo
                        profesional.
                    </p>
                </div>
                <div
                    class="flex shrink-0 flex-col items-center gap-4 sm:flex-row"
                >
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
                        class="flex items-center gap-2 rounded-xl px-4 py-2 text-white/70 transition-colors hover:bg-white/5 hover:text-cyan-400 active:scale-95"
                        @click="emit('toggle-mentor-chat')"
                    >
                        <PhRobot
                            :size="24"
                            weight="duotone"
                            class="animate-pulse"
                        />
                        <span class="text-sm font-bold tracking-wider uppercase"
                            >Hablar con Mentor IA</span
                        >
                    </button>
                </div>
            </div>
        </StCardGlass>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Active Conversations -->
            <StCardGlass class="p-12!">
                <div class="mb-6 flex items-center justify-between">
                    <h3
                        class="flex items-center gap-3 text-lg font-bold text-white"
                    >
                        <PhChatTeardropDots
                            :size="24"
                            weight="duotone"
                            class="text-fuchsia-400"
                        />
                        Conversaciones Activas
                    </h3>
                    <span
                        v-if="conversations.length"
                        class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >Recientes</span
                    >
                </div>

                <div
                    v-if="conversations.length === 0"
                    class="flex flex-col items-center justify-center py-12 opacity-40"
                >
                    <PhChatTeardropDots :size="48" weight="thin" />
                    <p class="mt-4 text-sm font-light">
                        Sin conversaciones pendientes
                    </p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="conv in conversations"
                        :key="conv.id"
                        class="group flex cursor-pointer items-center justify-between rounded-2xl border border-white/5 bg-white/2 p-4 transition-all hover:border-white/10 hover:bg-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-white/60 transition-colors group-hover:text-indigo-400"
                            >
                                <PhMonitorPlay :size="20" weight="duotone" />
                            </div>
                            <div>
                                <div
                                    class="text-sm font-bold text-white transition-transform group-hover:translate-x-1"
                                >
                                    {{
                                        conv.type.charAt(0).toUpperCase() +
                                        conv.type.slice(1)
                                    }}
                                </div>
                                <div
                                    class="text-[10px] tracking-wider text-white/40 uppercase"
                                >
                                    {{ formatDate(conv.created_at) }}
                                </div>
                            </div>
                        </div>
                        <div
                            :class="[
                                'rounded-lg px-2 py-1 text-[9px] font-black tracking-widest uppercase',
                                getStatusColor(conv.status),
                            ]"
                        >
                            {{ conv.status }}
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Learning Quick Preview -->
            <StCardGlass class="p-12!">
                <div class="mb-6 flex items-center justify-between">
                    <h3
                        class="flex items-center gap-3 text-lg font-bold text-white"
                    >
                        <PhBooks
                            :size="24"
                            weight="duotone"
                            class="text-emerald-400"
                        />
                        Rutas de Aprendizaje
                    </h3>
                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        @click="emit('go-to-learning')"
                        >Ver todas</StButtonGlass
                    >
                </div>

                <div
                    v-if="learningPaths.length === 0"
                    class="flex flex-col items-center justify-center py-12 opacity-40"
                >
                    <PhBooks :size="48" weight="thin" />
                    <p class="mt-4 text-sm font-light">Sin rutas asignadas</p>
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="path in learningPaths.slice(0, 2)"
                        :key="path.id"
                        class="rounded-2xl border border-white/5 bg-white/2 p-4"
                    >
                        <div class="mb-3 flex items-start justify-between">
                            <div
                                class="max-w-[70%] leading-tight font-bold text-white"
                            >
                                {{ path.title }}
                            </div>
                            <div
                                :class="[
                                    'text-xs font-black tracking-tight',
                                    kpiColorClass(path.progress),
                                ]"
                            >
                                {{ path.progress }}%
                            </div>
                        </div>
                        <div
                            class="mb-2 h-2 w-full overflow-hidden rounded-full bg-white/5"
                        >
                            <div
                                class="h-full bg-linear-to-r from-emerald-500 to-emerald-400 transition-all duration-1000"
                                :style="{ width: `${path.progress}%` }"
                            />
                        </div>
                        <div
                            class="flex items-center justify-between text-[10px] tracking-widest text-white/30 uppercase"
                        >
                            <span
                                >{{ path.completed_actions }}/{{
                                    path.total_actions
                                }}
                                Acciones</span
                            >
                            <span :class="getStatusColor(path.status)">{{
                                path.status
                            }}</span>
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>

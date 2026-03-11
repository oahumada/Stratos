<script setup lang="ts">
import {
    PhCheckCircle,
    PhCircle,
    PhFlashlight,
    PhMedal,
    PhRocket,
    PhStar,
    PhTrendUp,
} from '@phosphor-icons/vue';

interface TimelineEvent {
    date: string;
    timestamp: string;
    type:
        | 'skill_evolution'
        | 'xp_gain'
        | 'badge_award'
        | 'quest_complete'
        | string;
    title: string;
    description: string;
    status: string;
    icon: string;
    color?: string;
    meta?: any;
}

defineProps<{
    events: TimelineEvent[];
}>();

const getEventIcon = (type: string, _icon: string) => {
    switch (type) {
        case 'skill_evolution':
            return PhTrendUp;
        case 'xp_gain':
            return PhFlashlight;
        case 'badge_award':
            return PhMedal;
        case 'quest_complete':
            return PhCheckCircle;
        default:
            return PhCircle;
    }
};

const getEventColor = (type: string, color?: string) => {
    if (color) return color;
    switch (type) {
        case 'skill_evolution':
            return 'text-emerald-400';
        case 'xp_gain':
            return 'text-amber-400';
        case 'badge_award':
            return 'text-purple-400';
        case 'quest_complete':
            return 'text-blue-400';
        default:
            return 'text-gray-400';
    }
};

const getBgColor = (type: string) => {
    switch (type) {
        case 'skill_evolution':
            return 'bg-emerald-500/10 border-emerald-500/20';
        case 'xp_gain':
            return 'bg-amber-500/10 border-amber-500/20';
        case 'badge_award':
            return 'bg-purple-500/10 border-purple-500/20';
        case 'quest_complete':
            return 'bg-blue-500/10 border-blue-500/20';
        default:
            return 'bg-white/5 border-white/10';
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <div class="relative px-2 py-4">
        <!-- Vertical Line -->
        <div
            class="absolute top-10 bottom-10 left-6 w-0.5 bg-linear-to-b from-indigo-500/50 via-purple-500/30 to-transparent"
        ></div>

        <div class="space-y-8">
            <div
                v-for="(event, index) in events"
                :key="index"
                class="animate-fade-in relative flex gap-6"
                :style="{ animationDelay: index * 100 + 'ms' }"
            >
                <!-- Icon Sphere -->
                <div
                    class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center"
                >
                    <div
                        class="absolute inset-0 rounded-full opacity-50 blur-md"
                        :class="getBgColor(event.type)"
                    ></div>
                    <div
                        class="relative flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-gray-900 text-white shadow-xl"
                    >
                        <component
                            :is="getEventIcon(event.type, event.icon)"
                            :size="20"
                            :class="getEventColor(event.type, event.color)"
                        />
                    </div>
                </div>

                <!-- Content Card -->
                <div
                    class="grow rounded-2xl border p-4 transition-all hover:translate-x-1 hover:bg-white/3"
                    :class="getBgColor(event.type)"
                >
                    <div class="mb-1 flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                        >
                            {{ formatDate(event.date) }}
                        </span>
                        <div
                            v-if="event.type === 'xp_gain'"
                            class="flex items-center gap-1"
                        >
                            <PhStar :size="12" class="text-amber-400" />
                            <span class="text-xs font-bold text-amber-400"
                                >XP Boost</span
                            >
                        </div>
                    </div>
                    <h4 class="text-sm font-bold text-white">
                        {{ event.title }}
                    </h4>
                    <p class="mt-1 text-xs leading-relaxed text-gray-400">
                        {{ event.description }}
                    </p>

                    <!-- Meta details (optional) -->
                    <div v-if="event.meta" class="mt-3 flex flex-wrap gap-2">
                        <div
                            v-if="event.meta.current_level"
                            class="rounded-full border border-emerald-500/20 bg-white/5 px-2 py-0.5 text-[10px] text-emerald-300"
                        >
                            Nivel {{ event.meta.current_level }}
                        </div>
                        <div
                            v-if="event.meta.points"
                            class="rounded-full border border-amber-500/20 bg-white/5 px-2 py-0.5 text-[10px] text-amber-300"
                        >
                            +{{ event.meta.points }} Puntos
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="events.length === 0" class="py-12 text-center">
                <div
                    class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/5"
                >
                    <PhRocket :size="24" class="text-gray-600" />
                </div>
                <p class="text-sm text-gray-500 italic">
                    Tu viaje evolutivo está por comenzar.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(10px);
}

@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

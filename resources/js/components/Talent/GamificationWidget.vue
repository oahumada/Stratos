<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import { PhMedal, PhStar, PhTrophy } from '@phosphor-icons/vue';
import { computed } from 'vue';

interface Badge {
    id: number;
    name: string;
    slug: string;
    description: string;
    icon: string;
    color: string;
    pivot?: {
        awarded_at: string;
    };
}

interface Props {
    points: number;
    badges: Badge[];
}

const props = defineProps<Props>();

// Next level logic (Simple XP-like system)
const level = computed(() => Math.floor(props.points / 1000) + 1);
const pointsInLevel = computed(() => props.points % 1000);
const progressToNextLevel = computed(() => (pointsInLevel.value / 1000) * 100);

function getBadgeColor(color: string) {
    const map: Record<string, string> = {
        'red-accent-3': 'text-rose-400 border-rose-500/20 bg-rose-500/10',
        'deep-purple': 'text-purple-400 border-purple-500/20 bg-purple-500/10',
        amber: 'text-amber-400 border-amber-500/20 bg-amber-500/10',
        indigo: 'text-indigo-400 border-indigo-500/20 bg-indigo-500/10',
        emerald: 'text-emerald-400 border-emerald-500/20 bg-emerald-500/10',
        primary: 'text-indigo-400 border-indigo-500/20 bg-indigo-500/10',
    };
    return map[color] || map.primary;
}
</script>

<template>
    <div class="space-y-4">
        <!-- Points & Level Card -->
        <StCardGlass variant="glass" border-accent="indigo">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-500/30 bg-indigo-500/20 text-indigo-400"
                    >
                        <PhStar :size="20" weight="fill" />
                    </div>
                    <div>
                        <h3
                            class="text-sm font-bold tracking-wider text-white uppercase"
                        >
                            Level {{ level }}
                        </h3>
                        <p class="text-[0.6rem] text-white/40 uppercase">
                            Professional Status
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl leading-none font-black text-white">{{
                        points
                    }}</span>
                    <span
                        class="block text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                        >Total XP Points</span
                    >
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-1.5">
                <div class="flex items-end justify-between px-0.5">
                    <span
                        class="text-[0.6rem] font-bold text-white/40 uppercase"
                        >Progress to Level {{ level + 1 }}</span
                    >
                    <span class="text-[0.6rem] font-black text-indigo-400"
                        >{{ pointsInLevel }} / 1000 XP</span
                    >
                </div>
                <div
                    class="h-2 w-full overflow-hidden rounded-full border border-white/5 bg-white/5"
                >
                    <div
                        class="h-full rounded-full bg-linear-to-r from-indigo-600 to-violet-500 shadow-lg shadow-indigo-500/20 transition-all duration-1000 ease-out"
                        :style="{ width: `${progressToNextLevel}%` }"
                    ></div>
                </div>
            </div>
        </StCardGlass>

        <!-- Badges Card -->
        <StCardGlass variant="glass">
            <div class="mb-4 flex items-center gap-3">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-amber-500/20 bg-amber-500/10 text-amber-500"
                >
                    <PhMedal :size="18" weight="bold" />
                </div>
                <div>
                    <h4
                        class="text-xs font-black tracking-widest text-white/80 uppercase"
                    >
                        Insignias Obtenidas
                    </h4>
                    <p class="text-[0.6rem] text-white/40 uppercase">
                        Achievements & Recognition
                    </p>
                </div>
            </div>

            <div v-if="badges.length" class="grid grid-cols-2 gap-2">
                <div
                    v-for="badge in badges"
                    :key="badge.id"
                    class="group flex flex-col gap-2 rounded-2xl border p-3 transition-all duration-300 hover:scale-[1.02]"
                    :class="getBadgeColor(badge.color)"
                    v-title="badge.description"
                >
                    <div class="flex items-center gap-2">
                        <v-icon
                            :icon="badge.icon"
                            size="18"
                            class="text-current"
                        />
                        <span
                            class="truncate text-[0.65rem] leading-tight font-black uppercase"
                            >{{ badge.name }}</span
                        >
                    </div>
                    <p
                        class="line-clamp-2 text-[0.55rem] leading-tight text-white/50"
                    >
                        {{ badge.description }}
                    </p>
                </div>
            </div>
            <div
                v-else
                class="rounded-2xl border border-dashed border-white/10 bg-white/2 py-6 text-center"
            >
                <PhTrophy :size="24" class="mx-auto mb-2 text-white/10" />
                <p
                    class="text-[0.65rem] font-bold tracking-widest text-white/30 uppercase"
                >
                    No hay insignias aún
                </p>
            </div>
        </StCardGlass>
    </div>
</template>

<style scoped>
.v-title {
    cursor: help;
}
</style>

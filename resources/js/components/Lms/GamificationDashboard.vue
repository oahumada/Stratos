<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { GraduationCap, Star, Trophy } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Stats {
    xp: number;
    level: number;
    points: number;
    next_level_xp: number;
    progress_percentage: number;
    rank_name: string;
}

interface LeaderboardItem {
    id: number;
    first_name: string;
    last_name: string;
    total_xp: number;
    level: number;
    photo_url: string | null;
}

const stats = ref<Stats | null>(null);
const leaderboard = ref<LeaderboardItem[]>([]);
const loading = ref(true);

const fetchDashboardData = async () => {
    loading.value = true;
    try {
        const [statsRes, leaderboardRes] = await Promise.all([
            axios.get('/api/lms/stats'),
            axios.get('/api/lms/leaderboard'),
        ]);

        if (statsRes.data.success) stats.value = statsRes.data.stats;
        if (leaderboardRes.data.success)
            leaderboard.value = leaderboardRes.data.leaderboard;
    } catch (error) {
        console.error('Error fetching gamification data:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchDashboardData);
</script>

<template>
    <div class="gamification-dashboard grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Main Stats Card (Level & Progress) -->
        <StCardGlass
            class="group relative col-span-1 overflow-hidden md:col-span-2"
        >
            <div
                class="absolute -top-10 -right-10 opacity-10 transition-transform duration-700 group-hover:scale-110"
            >
                <Trophy :size="200" class="text-primary" />
            </div>

            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <h3
                        class="mb-2 text-sm font-medium tracking-wider text-muted-foreground uppercase"
                    >
                        Progreso de Talento
                    </h3>
                    <div class="flex items-baseline gap-3">
                        <span
                            class="bg-linear-to-r from-primary to-blue-400 bg-clip-text text-5xl font-bold text-transparent"
                        >
                            Nivel {{ stats?.level ?? 1 }}
                        </span>
                        <StBadgeGlass variant="primary" class="animate-pulse">
                            {{ stats?.rank_name ?? 'Cargando...' }}
                        </StBadgeGlass>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-semibold"
                        >{{ stats?.xp ?? 0 }} XP</span
                    >
                    <p class="text-xs text-muted-foreground">Total Acumulado</p>
                </div>
            </div>

            <div class="relative z-10 mt-8 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Progreso al siguiente nivel</span>
                    <span
                        >{{ stats?.xp ?? 0 }} /
                        {{ stats?.next_level_xp ?? 1000 }} XP</span
                    >
                </div>
                <!-- Progress Bar -->
                <div
                    class="h-4 w-full overflow-hidden rounded-full border border-white/10 bg-white/5 p-0.5"
                >
                    <div
                        class="h-full rounded-full bg-linear-to-r from-primary via-blue-500 to-indigo-500 shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-1000 ease-out"
                        :style="{
                            width: `${stats?.progress_percentage ?? 0}%`,
                        }"
                    ></div>
                </div>
                <p class="text-center text-xs text-muted-foreground italic">
                    ¡Faltan
                    {{ (stats?.next_level_xp ?? 1000) - (stats?.xp ?? 0) }} XP
                    para el siguiente rango!
                </p>
            </div>
        </StCardGlass>

        <!-- Points / Currency Card -->
        <StCardGlass
            class="flex flex-col items-center justify-center border-yellow-500/20 p-8 text-center"
        >
            <div
                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-500/10"
            >
                <Star class="fill-yellow-500/20 text-yellow-500" :size="32" />
            </div>
            <span class="text-4xl font-bold text-yellow-500">{{
                stats?.points ?? 0
            }}</span>
            <span class="mt-1 text-sm font-medium text-muted-foreground"
                >Stratos Credits</span
            >
            <button
                class="mt-6 rounded-full border border-yellow-500/30 bg-yellow-500/20 px-4 py-2 text-xs font-bold tracking-widest text-yellow-500 uppercase transition-colors hover:bg-yellow-500/30"
            >
                Canjear Beneficios
            </button>
        </StCardGlass>

        <!-- Leaderboard -->
        <StCardGlass class="col-span-1 md:col-span-3">
            <div class="mb-6 flex items-center gap-2">
                <Trophy class="text-amber-500" :size="20" />
                <h4 class="text-lg font-bold">
                    Top Aprendices de la Organización
                </h4>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(user, index) in leaderboard"
                    :key="user.id"
                    class="flex items-center justify-between rounded-xl border border-transparent bg-white/5 p-3 transition-all hover:border-white/10 hover:bg-white/10"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-8 text-center font-bold"
                            :class="{
                                'text-xl text-yellow-500': index === 0,
                                'text-lg text-slate-300': index === 1,
                                'text-amber-700': index === 2,
                                'text-muted-foreground': index > 2,
                            }"
                        >
                            {{ index + 1 }}°
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-white/20 bg-primary/20"
                        >
                            <img
                                v-if="user.photo_url"
                                :src="user.photo_url"
                                :alt="user.first_name"
                                class="h-full w-full object-cover"
                            />
                            <GraduationCap
                                v-else
                                class="text-primary/60"
                                :size="20"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-semibold">
                                {{ user.first_name }} {{ user.last_name }}
                            </p>
                            <div class="flex items-center gap-2">
                                <StBadgeGlass
                                    variant="glass"
                                    class="h-4 border-primary/30 px-2 py-0 text-[10px] text-primary/80"
                                >
                                    Nivel {{ user.level }}
                                </StBadgeGlass>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-mono font-bold text-primary">{{
                            user.total_xp.toLocaleString()
                        }}</span>
                        <span
                            class="ml-1 text-[10px] text-muted-foreground uppercase"
                            >XP</span
                        >
                    </div>
                </div>
            </div>

            <div
                v-if="leaderboard.length === 0"
                class="py-8 text-center text-muted-foreground italic"
            >
                Aún no hay suficientes datos para el ranking. ¡Comienza un curso
                hoy!
            </div>
        </StCardGlass>
    </div>
</template>

<style scoped>
.gamification-dashboard {
    perspective: 1000px;
}
</style>

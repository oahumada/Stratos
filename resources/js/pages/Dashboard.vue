<script setup lang="ts">
import CultureSentinelWidget from '@/components/Dashboard/CultureSentinelWidget.vue';
import SentinelHealthWidget from '@/components/SentinelHealthWidget.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { Head } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhBriefcase,
    PhChartBar,
    PhLightbulb,
    PhShieldCheck,
    PhStar,
    PhTrendUp,
    PhUsersThree,
    PhWarning,
    PhWarningCircle,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const { notify } = useNotification();

interface DashboardMetrics {
    total_peoples: number;
    total_roles: number;
    total_skills: number;
    avg_match_percentage: number;
    roles_at_risk: number;
    high_performers: number;
    skills_coverage: number;
    critical_gaps: number;
}

const metrics = ref<DashboardMetrics | null>(null);
const loading = ref(false);

const loadMetrics = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/dashboard/metrics');
        metrics.value = response.data.data || response.data;
    } catch (err) {
        console.error('Failed to load metrics', err);
        notify({
            type: 'error',
            text: 'Error loading dashboard metrics',
        });
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadMetrics();
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="mx-auto max-w-7xl space-y-8 p-8">
        <!-- Dashboard Header with Indicator Light -->
        <div
            class="relative mb-12 animate-in pt-8 duration-700 fade-in slide-in-from-top-4"
        >
            <div
                class="absolute top-0 left-0 h-px w-full bg-linear-to-r from-transparent via-indigo-500 to-transparent shadow-[0_0_20px_rgba(99,102,241,0.5)]"
            ></div>

            <div class="mb-2 flex items-center gap-4">
                <div class="h-2 w-12 rounded-full bg-indigo-500"></div>
                <span
                    class="text-xs font-black tracking-[0.3em] text-indigo-400 uppercase"
                >
                    Organizational Insights
                </span>
            </div>

            <div
                class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end"
            >
                <div>
                    <h1
                        class="mb-3 text-4xl font-black tracking-tight text-white md:text-5xl"
                    >
                        Stratos
                        <span
                            class="bg-linear-to-r from-indigo-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent"
                            >Command</span
                        >
                    </h1>
                    <p class="max-w-2xl text-lg font-medium text-white/40">
                        Monitor de signos vitales, brechas críticas y potencial
                        estratégico de la organización en tiempo real.
                    </p>
                </div>

                <div
                    class="hidden rounded-2xl border border-white/10 bg-white/5 p-6 shadow-[0_0_20px_rgba(99,102,241,0.1)] backdrop-blur-sm md:flex"
                >
                    <PhShieldCheck
                        :size="48"
                        weight="duotone"
                        class="text-indigo-400/60"
                    />
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <StCardGlass v-if="loading" class="animate-pulse p-16! text-center">
            <div class="flex flex-col items-center justify-center gap-6">
                <div
                    class="h-16 w-16 animate-spin rounded-full border-4 border-indigo-500/20 border-t-indigo-500"
                ></div>
                <p class="text-xl font-bold text-indigo-300">
                    Sincronizando pulso organizacional...
                </p>
            </div>
        </StCardGlass>

        <!-- Metrics Grid -->
        <div
            v-if="metrics && !loading"
            class="grid animate-in grid-cols-1 gap-6 duration-1000 zoom-in-95 fade-in md:grid-cols-2 lg:grid-cols-4"
        >
            <!-- Total Peoples -->
            <StCardGlass
                class="group relative overflow-hidden p-12! transition-all duration-500 hover:bg-white/10"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-indigo-500/5 transition-colors group-hover:text-indigo-500/10"
                >
                    <PhUsersThree :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-white/50 uppercase"
                >
                    Total Talento
                </p>
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-5xl font-black tracking-tighter text-white"
                        >
                            {{ metrics.total_peoples }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-indigo-400/80">
                            Colaboradores activos
                        </p>
                    </div>
                </div>
            </StCardGlass>

            <!-- Total Roles -->
            <StCardGlass
                class="group relative overflow-hidden p-12! transition-all duration-500 hover:bg-white/10"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-indigo-500/5 transition-colors group-hover:text-indigo-500/10"
                >
                    <PhBriefcase :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-white/50 uppercase"
                >
                    Arquitectura
                </p>
                <div>
                    <p class="text-5xl font-black tracking-tighter text-white">
                        {{ metrics.total_roles }}
                    </p>
                    <p class="mt-1 text-xs font-medium text-indigo-400/80">
                        Nodos de ejecución
                    </p>
                </div>
            </StCardGlass>

            <!-- Total Skills -->
            <StCardGlass
                class="group relative overflow-hidden p-12! transition-all duration-500 hover:bg-white/10"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-indigo-500/5 transition-colors group-hover:text-indigo-500/10"
                >
                    <PhLightbulb :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-white/50 uppercase"
                >
                    Capital Técnico
                </p>
                <div>
                    <p class="text-5xl font-black tracking-tighter text-white">
                        {{ metrics.total_skills }}
                    </p>
                    <p class="mt-1 text-xs font-medium text-indigo-400/80">
                        Habilidades mapeadas
                    </p>
                </div>
            </StCardGlass>

            <!-- Average Match -->
            <StCardGlass
                class="group relative overflow-hidden p-12! transition-all duration-500 hover:bg-white/10"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-white/5 transition-colors group-hover:text-white/10"
                >
                    <PhTrendUp :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-white/50 uppercase"
                >
                    Alineación Estratégica
                </p>
                <div>
                    <div class="flex items-baseline gap-1">
                        <p
                            class="text-5xl font-black tracking-tighter text-white"
                        >
                            {{ metrics.avg_match_percentage }}
                        </p>
                        <p class="text-2xl font-black text-white/30">%</p>
                    </div>
                    <div
                        class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-white/10"
                    >
                        <div
                            class="h-full transition-all duration-1000"
                            :class="
                                metrics.avg_match_percentage >= 70
                                    ? 'bg-emerald-500'
                                    : 'bg-amber-500'
                            "
                            :style="{
                                width: `${metrics.avg_match_percentage}%`,
                            }"
                        ></div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Roles at Risk -->
            <StCardGlass
                class="group relative overflow-hidden border-rose-500/10 p-12! transition-all duration-500 hover:bg-rose-500/5"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-rose-500/5 transition-colors group-hover:text-rose-500/10"
                >
                    <PhWarningCircle :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-rose-400/70 uppercase"
                >
                    Puntos de Falla
                </p>
                <div>
                    <p
                        class="text-5xl font-black tracking-tighter text-rose-500"
                    >
                        {{ metrics.roles_at_risk }}
                    </p>
                    <p class="mt-1 text-xs font-medium text-rose-400">
                        Roles bajo umbral crítico
                    </p>
                </div>
            </StCardGlass>

            <!-- High Performers -->
            <StCardGlass
                class="group relative overflow-hidden border-emerald-500/10 p-12! transition-all duration-500 hover:bg-emerald-500/5"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-emerald-500/5 transition-colors group-hover:text-emerald-500/10"
                >
                    <PhStar :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-emerald-400/70 uppercase"
                >
                    High Potentials
                </p>
                <div>
                    <p
                        class="text-5xl font-black tracking-tighter text-emerald-400"
                    >
                        {{ metrics.high_performers }}
                    </p>
                    <p class="mt-1 text-xs font-medium text-emerald-400">
                        Modelos de éxito detectados
                    </p>
                </div>
            </StCardGlass>

            <!-- Skills Coverage -->
            <StCardGlass
                class="group relative overflow-hidden p-12! transition-all duration-500 hover:bg-white/10"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-white/5 transition-colors group-hover:text-white/10"
                >
                    <PhChartBar :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-white/50 uppercase"
                >
                    Cobertura de Skills
                </p>
                <div>
                    <div class="flex items-baseline gap-1">
                        <p
                            class="text-5xl font-black tracking-tighter text-white"
                        >
                            {{ metrics.skills_coverage }}
                        </p>
                        <p class="text-2xl font-black text-white/30">%</p>
                    </div>
                    <div
                        class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-white/10"
                    >
                        <div
                            class="h-full bg-linear-to-r from-indigo-500 to-cyan-400 transition-all duration-1000"
                            :style="{ width: `${metrics.skills_coverage}%` }"
                        ></div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Critical Gaps -->
            <StCardGlass
                class="group relative overflow-hidden border-amber-500/10 p-12! transition-all duration-500 hover:bg-amber-500/5"
            >
                <div
                    class="absolute -right-6 -bottom-6 text-amber-500/5 transition-colors group-hover:text-amber-500/10"
                >
                    <PhWarning :size="140" weight="duotone" />
                </div>
                <p
                    class="mb-4 text-[10px] font-black tracking-[0.2em] text-amber-400/70 uppercase"
                >
                    Gaps Críticos
                </p>
                <div>
                    <p
                        class="text-5xl font-black tracking-tighter text-amber-500"
                    >
                        {{ metrics.critical_gaps }}
                    </p>
                    <p class="mt-1 text-xs font-medium text-amber-400">
                        Brechas que requieren IA
                    </p>
                </div>
            </StCardGlass>
        </div>

        <!-- System Monitors Row -->
        <div
            v-if="metrics && !loading"
            class="grid animate-in grid-cols-1 gap-8 delay-300 duration-1000 fade-in slide-in-from-bottom-4 md:grid-cols-2"
        >
            <CultureSentinelWidget />
            <SentinelHealthWidget />
        </div>

        <!-- Empty State -->
        <StCardGlass
            v-if="!loading && !metrics"
            class="border-2 border-dashed border-white/10 p-24! text-center"
        >
            <div class="flex flex-col items-center justify-center gap-4">
                <PhChartBar :size="64" weight="light" class="text-white/20" />
                <p class="text-2xl font-medium text-white/40">
                    No hay telemetría disponible
                </p>
                <div
                    class="mt-4 rounded-full bg-white/5 p-1 px-3 text-xs font-bold tracking-widest text-white/30 uppercase"
                >
                    Sistema a la espera de ingesta
                </div>
            </div>
        </StCardGlass>
    </div>
</template>

<style scoped></style>

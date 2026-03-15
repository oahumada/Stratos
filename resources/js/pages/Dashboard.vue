<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import CultureSentinelWidget from '@/components/Dashboard/CultureSentinelWidget.vue';
import SentinelHealthWidget from '@/components/SentinelHealthWidget.vue';
import {
    PhUsersThree,
    PhBriefcase,
    PhLightbulb,
    PhWarningCircle,
    PhStar,
    PhWarning,
    PhChartBar,
    PhTrendUp,
    PhShieldCheck
} from '@phosphor-icons/vue';
import { Head } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
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
        <div class="relative mb-12 pt-8 animate-in fade-in slide-in-from-top-4 duration-700">
            <div class="absolute top-0 left-0 h-px w-full bg-linear-to-r from-transparent via-indigo-500 to-transparent shadow-[0_0_20px_rgba(99,102,241,0.5)]"></div>
            
            <div class="flex items-center gap-4 mb-2">
                <div class="h-2 w-12 rounded-full bg-indigo-500"></div>
                <span class="text-xs font-black tracking-[0.3em] text-indigo-400 uppercase">
                    Organizational Insights
                </span>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-3">
                        Stratos <span class="bg-linear-to-r from-indigo-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent">Command</span>
                    </h1>
                    <p class="text-lg font-medium text-white/40 max-w-2xl">
                        Monitor de signos vitales, brechas críticas y potencial estratégico de la organización en tiempo real.
                    </p>
                </div>
                
                <div class="hidden md:flex p-6 rounded-2xl bg-white/5 border border-white/10 shadow-[0_0_20px_rgba(99,102,241,0.1)] backdrop-blur-sm">
                    <PhShieldCheck :size="48" weight="duotone" class="text-indigo-400/60" />
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <StCardGlass v-if="loading" class="animate-pulse p-16! text-center">
            <div class="flex flex-col items-center justify-center gap-6">
                <div class="h-16 w-16 animate-spin rounded-full border-4 border-indigo-500/20 border-t-indigo-500"></div>
                <p class="text-xl font-bold text-indigo-300">Sincronizando pulso organizacional...</p>
            </div>
        </StCardGlass>

        <!-- Metrics Grid -->
        <div v-if="metrics && !loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-in fade-in zoom-in-95 duration-1000">
            <!-- Total Peoples -->
            <StCardGlass class="group relative overflow-hidden p-12! hover:bg-white/10 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-indigo-500/5 group-hover:text-indigo-500/10 transition-colors">
                    <PhUsersThree :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-white/50 uppercase mb-4">Total Talento</p>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-5xl font-black text-white tracking-tighter">{{ metrics.total_peoples }}</p>
                        <p class="text-xs font-medium text-indigo-400/80 mt-1">Colaboradores activos</p>
                    </div>
                </div>
            </StCardGlass>

            <!-- Total Roles -->
            <StCardGlass class="group relative overflow-hidden p-12! hover:bg-white/10 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-indigo-500/5 group-hover:text-indigo-500/10 transition-colors">
                    <PhBriefcase :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-white/50 uppercase mb-4">Arquitectura</p>
                <div>
                    <p class="text-5xl font-black text-white tracking-tighter">{{ metrics.total_roles }}</p>
                    <p class="text-xs font-medium text-indigo-400/80 mt-1">Nodos de ejecución</p>
                </div>
            </StCardGlass>

            <!-- Total Skills -->
            <StCardGlass class="group relative overflow-hidden p-12! hover:bg-white/10 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-indigo-500/5 group-hover:text-indigo-500/10 transition-colors">
                    <PhLightbulb :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-white/50 uppercase mb-4">Capital Técnico</p>
                <div>
                    <p class="text-5xl font-black text-white tracking-tighter">{{ metrics.total_skills }}</p>
                    <p class="text-xs font-medium text-indigo-400/80 mt-1">Habilidades mapeadas</p>
                </div>
            </StCardGlass>

            <!-- Average Match -->
            <StCardGlass class="group relative overflow-hidden p-12! hover:bg-white/10 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-white/5 group-hover:text-white/10 transition-colors">
                    <PhTrendUp :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-white/50 uppercase mb-4">Alineación Estratégica</p>
                <div>
                    <div class="flex items-baseline gap-1">
                        <p class="text-5xl font-black text-white tracking-tighter">{{ metrics.avg_match_percentage }}</p>
                        <p class="text-2xl font-black text-white/30">%</p>
                    </div>
                    <div class="mt-4 h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-1000" 
                             :class="metrics.avg_match_percentage >= 70 ? 'bg-emerald-500' : 'bg-amber-500'"
                             :style="{ width: `${metrics.avg_match_percentage}%` }"></div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Roles at Risk -->
            <StCardGlass class="group relative overflow-hidden p-12! border-rose-500/10 hover:bg-rose-500/5 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-rose-500/5 group-hover:text-rose-500/10 transition-colors">
                    <PhWarningCircle :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-rose-400/70 uppercase mb-4">Puntos de Falla</p>
                <div>
                    <p class="text-5xl font-black text-rose-500 tracking-tighter">{{ metrics.roles_at_risk }}</p>
                    <p class="text-xs font-medium text-rose-400 mt-1">Roles bajo umbral crítico</p>
                </div>
            </StCardGlass>

            <!-- High Performers -->
            <StCardGlass class="group relative overflow-hidden p-12! border-emerald-500/10 hover:bg-emerald-500/5 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-emerald-500/5 group-hover:text-emerald-500/10 transition-colors">
                    <PhStar :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-emerald-400/70 uppercase mb-4">High Potentials</p>
                <div>
                    <p class="text-5xl font-black text-emerald-400 tracking-tighter">{{ metrics.high_performers }}</p>
                    <p class="text-xs font-medium text-emerald-400 mt-1">Modelos de éxito detectados</p>
                </div>
            </StCardGlass>

            <!-- Skills Coverage -->
            <StCardGlass class="group relative overflow-hidden p-12! hover:bg-white/10 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-white/5 group-hover:text-white/10 transition-colors">
                    <PhChartBar :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-white/50 uppercase mb-4">Cobertura de Skills</p>
                <div>
                    <div class="flex items-baseline gap-1">
                        <p class="text-5xl font-black text-white tracking-tighter">{{ metrics.skills_coverage }}</p>
                        <p class="text-2xl font-black text-white/30">%</p>
                    </div>
                    <div class="mt-4 h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-linear-to-r from-indigo-500 to-cyan-400 transition-all duration-1000" 
                             :style="{ width: `${metrics.skills_coverage}%` }"></div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Critical Gaps -->
            <StCardGlass class="group relative overflow-hidden p-12! border-amber-500/10 hover:bg-amber-500/5 transition-all duration-500">
                <div class="absolute -right-6 -bottom-6 text-amber-500/5 group-hover:text-amber-500/10 transition-colors">
                    <PhWarning :size="140" weight="duotone" />
                </div>
                <p class="text-[10px] font-black tracking-[0.2em] text-amber-400/70 uppercase mb-4">Gaps Críticos</p>
                <div>
                    <p class="text-5xl font-black text-amber-500 tracking-tighter">{{ metrics.critical_gaps }}</p>
                    <p class="text-xs font-medium text-amber-400 mt-1">Brechas que requieren IA</p>
                </div>
            </StCardGlass>
        </div>

        <!-- System Monitors Row -->
        <div v-if="metrics && !loading" class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-1000 delay-300">
            <CultureSentinelWidget />
            <SentinelHealthWidget />
        </div>

        <!-- Empty State -->
        <StCardGlass v-if="!loading && !metrics" class="text-center p-24! border-dashed border-white/10 border-2">
            <div class="flex flex-col items-center justify-center gap-4">
                <PhChartBar :size="64" weight="light" class="text-white/20" />
                <p class="text-2xl font-medium text-white/40">No hay telemetría disponible</p>
                <div class="mt-4 p-1 px-3 bg-white/5 rounded-full text-xs font-bold text-white/30 tracking-widest uppercase">
                    Sistema a la espera de ingesta
                </div>
            </div>
        </StCardGlass>
    </div>
</template>

<style scoped>
</style>

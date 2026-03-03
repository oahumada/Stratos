<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { onMounted, onUnmounted, ref } from 'vue';

interface ModuleHealth {
    health_score: number;
    issues?: Array<{ type: string; message: string; severity: string }>;
    [key: string]: any;
}

interface ScanResult {
    scan_timestamp: string;
    overall_health: number;
    modules: Record<string, ModuleHealth>;
    alerts: Array<{ level: string; module: string; message: string }>;
    recommendations: string[];
}

const loading = ref(false);
const scanning = ref(false);
const healthScore = ref<number | null>(null);
const scanResult = ref<ScanResult | null>(null);
const showDetails = ref(false);

const moduleLabels: Record<string, { label: string; icon: string }> = {
    data_integrity: { label: 'Integridad de Datos', icon: '🗃️' },
    ai_quality: { label: 'Calidad IA', icon: '🤖' },
    development_pipeline: { label: 'Pipeline Desarrollo', icon: '📈' },
    scenario_coverage: { label: 'Cobertura Escenarios', icon: '🎯' },
    ethical_compliance: { label: 'Compliance Ético', icon: '⚖️' },
};

async function loadHealth() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/sentinel/health');
        const result = data.data ?? data;
        healthScore.value = result.health_score;
        if (result.last_scan) {
            scanResult.value = result.last_scan;
        }
    } catch {
        healthScore.value = null;
    } finally {
        loading.value = false;
    }
}

async function runFullScan() {
    scanning.value = true;
    try {
        const { data } = await axios.get('/api/sentinel/scan');
        scanResult.value = data.data ?? data;
        healthScore.value = scanResult.value?.overall_health ?? null;
    } catch {
        // Fail silently
    } finally {
        scanning.value = false;
    }
}

function getHealthColor(score: number): string {
    if (score >= 70) return 'text-emerald-400';
    if (score >= 50) return 'text-amber-400';
    return 'text-rose-400';
}

function getHealthBg(score: number): string {
    if (score >= 70) return 'from-emerald-500 to-teal-500';
    if (score >= 50) return 'from-amber-500 to-orange-500';
    return 'from-rose-500 to-pink-500';
}

function getHealthGradient(score: number): string {
    if (score >= 70) return '#10b981';
    if (score >= 50) return '#f59e0b';
    return '#ef4444';
}

function getAlertIcon(level: string): string {
    if (level === 'critical') return '🔴';
    if (level === 'high') return '🟠';
    if (level === 'warning') return '🟡';
    return '🔵';
}

function formatTimestamp(ts: string): string {
    if (!ts) return '';
    try {
        const d = new Date(ts);
        return d.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return ts;
    }
}

let pollInterval: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
    loadHealth();
    // Auto-refresh every 5 minutes
    pollInterval = setInterval(loadHealth, 300000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
    <StCardGlass class="relative overflow-hidden">
        <!-- Background Glow -->
        <div
            v-if="healthScore != null"
            class="absolute -top-10 -right-10 h-40 w-40 rounded-full opacity-10 blur-3xl"
            :class="{
                'bg-emerald-500': healthScore >= 70,
                'bg-amber-500': healthScore >= 50 && healthScore < 70,
                'bg-rose-500': healthScore < 50,
            }"
        ></div>

        <!-- Header -->
        <div class="relative mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-lg shadow-lg shadow-indigo-500/20"
                >
                    🛡️
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">
                        Stratos Sentinel
                    </h3>
                    <p class="text-[0.6rem] text-white/40">
                        Monitor de salud del sistema
                    </p>
                </div>
            </div>
            <StButtonGlass size="sm" @click="runFullScan" :loading="scanning">
                {{ scanning ? 'Escaneando...' : 'Scan Completo' }}
            </StButtonGlass>
        </div>

        <!-- Health Score Circle -->
        <div class="relative flex justify-center py-4">
            <div
                v-if="loading"
                class="flex h-24 w-24 items-center justify-center"
            >
                <div
                    class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-500 border-t-transparent"
                ></div>
            </div>
            <div v-else-if="healthScore != null" class="relative h-28 w-28">
                <svg class="h-28 w-28 -rotate-90" viewBox="0 0 100 100">
                    <circle
                        cx="50"
                        cy="50"
                        r="42"
                        fill="none"
                        stroke="rgba(255,255,255,0.05)"
                        stroke-width="6"
                    />
                    <circle
                        cx="50"
                        cy="50"
                        r="42"
                        fill="none"
                        :stroke="getHealthGradient(healthScore)"
                        stroke-width="6"
                        stroke-linecap="round"
                        :stroke-dasharray="`${healthScore * 2.64} 264`"
                        class="transition-all duration-1000"
                    />
                </svg>
                <div
                    class="absolute inset-0 flex flex-col items-center justify-center"
                >
                    <span
                        :class="[
                            'text-3xl font-black',
                            getHealthColor(healthScore),
                        ]"
                        >{{ healthScore }}</span
                    >
                    <span
                        class="text-[0.5rem] tracking-widest text-white/30 uppercase"
                        >Health Score</span
                    >
                </div>
            </div>
            <div v-else class="py-6 text-center">
                <p class="text-sm text-white/40">No hay datos disponibles</p>
            </div>
        </div>

        <!-- Module Health Bars -->
        <div v-if="scanResult?.modules" class="mt-2 space-y-2.5">
            <p
                class="text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
            >
                Módulos
            </p>
            <div
                v-for="(module, key) in scanResult.modules"
                :key="String(key)"
                class="flex items-center gap-3"
            >
                <span class="w-5 text-center text-sm">{{
                    moduleLabels[String(key)]?.icon ?? '📦'
                }}</span>
                <span class="w-24 truncate text-xs text-white/50">{{
                    moduleLabels[String(key)]?.label ?? key
                }}</span>
                <div class="h-2 flex-1 overflow-hidden rounded-full bg-white/5">
                    <div
                        class="h-full rounded-full transition-all duration-700"
                        :class="{
                            'bg-emerald-500': module.health_score >= 70,
                            'bg-amber-500':
                                module.health_score >= 50 &&
                                module.health_score < 70,
                            'bg-rose-500': module.health_score < 50,
                        }"
                        :style="{ width: `${module.health_score}%` }"
                    ></div>
                </div>
                <span
                    :class="[
                        'w-8 text-right text-xs font-bold',
                        getHealthColor(module.health_score),
                    ]"
                >
                    {{ module.health_score }}
                </span>
            </div>
        </div>

        <!-- Alerts -->
        <div v-if="scanResult?.alerts?.length" class="mt-4">
            <div class="mb-2 flex items-center justify-between">
                <p
                    class="text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                >
                    Alertas
                </p>
                <StBadgeGlass variant="error" size="sm">{{
                    scanResult.alerts.length
                }}</StBadgeGlass>
            </div>
            <div class="space-y-1.5">
                <div
                    v-for="(alert, i) in scanResult.alerts.slice(0, 3)"
                    :key="i"
                    class="flex items-start gap-2 rounded-lg border border-white/5 bg-white/3 p-2"
                >
                    <span class="mt-0.5 text-sm">{{
                        getAlertIcon(alert.level)
                    }}</span>
                    <p class="text-xs text-white/60">{{ alert.message }}</p>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div v-if="scanResult?.recommendations?.length" class="mt-4">
            <button
                @click="showDetails = !showDetails"
                class="text-[0.6rem] font-bold tracking-widest text-indigo-300 uppercase transition-colors hover:text-indigo-200"
            >
                {{ showDetails ? '▼' : '▶' }} Recomendaciones ({{
                    scanResult.recommendations.length
                }})
            </button>
            <Transition name="expand">
                <div v-if="showDetails" class="mt-2 space-y-1.5">
                    <div
                        v-for="(rec, i) in scanResult.recommendations"
                        :key="i"
                        class="flex items-start gap-2 text-xs text-white/50"
                    >
                        <span class="mt-0.5 text-emerald-400">→</span>
                        <span>{{ rec }}</span>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Last Scan Timestamp -->
        <div
            v-if="scanResult?.scan_timestamp"
            class="mt-4 border-t border-white/5 pt-3"
        >
            <p class="text-center text-[0.55rem] text-white/20">
                Último scan: {{ formatTimestamp(scanResult.scan_timestamp) }}
            </p>
        </div>
    </StCardGlass>
</template>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    transition: all 0.3s ease;
}
.expand-enter-from,
.expand-leave-to {
    opacity: 0;
    max-height: 0;
    overflow: hidden;
}
</style>

<template>
    <v-card
        class="culture-sentinel-card overflow-hidden rounded-3xl border border-white/5"
        :class="{ 'pulse-glow': hasAnomalies }"
        elevation="8"
    >
        <!-- Header -->
        <div class="sentinel-header pa-6 d-flex align-center">
            <v-avatar
                :color="healthColor"
                size="48"
                class="sentinel-avatar mr-4"
            >
                <v-icon :icon="healthIcon" size="28" color="white"></v-icon>
            </v-avatar>
            <div class="flex-grow-1">
                <div
                    class="text-overline mb-n1 text-white/50"
                    style="letter-spacing: 0.15em"
                >
                    CULTURE SENTINEL
                </div>
                <div class="text-h5 font-weight-black text-white">
                    Pulso Organizacional
                </div>
            </div>
            <v-btn
                variant="tonal"
                color="white"
                icon="mdi-radar"
                size="small"
                :loading="scanning"
                @click="runScan"
            ></v-btn>
        </div>

        <!-- Health Score Ring -->
        <div
            class="px-6 py-6 text-center"
            style="background: rgba(0, 0, 0, 0.2)"
        >
            <v-progress-circular
                :model-value="healthScore"
                :size="160"
                :width="14"
                :color="healthColor"
                class="health-ring"
            >
                <div class="text-center">
                    <div class="text-h3 font-weight-black text-white">
                        {{ healthScore }}
                    </div>
                    <div
                        class="text-caption text-white/40 uppercase"
                        style="letter-spacing: 0.1em"
                    >
                        Health Score
                    </div>
                </div>
            </v-progress-circular>

            <div class="d-flex mt-6 justify-center gap-6">
                <div class="text-center">
                    <div class="text-h6 font-weight-bold" :class="trendColor">
                        <v-icon :icon="trendIcon" size="18"></v-icon>
                        {{ signals.avg_sentiment }}%
                    </div>
                    <div class="text-caption text-white/40">Sentimiento</div>
                </div>
                <v-divider vertical class="border-opacity-10"></v-divider>
                <div class="text-center">
                    <div class="text-h6 font-weight-bold text-white">
                        {{ signals.pulse_count }}
                    </div>
                    <div class="text-caption text-white/40">Pulsos (30d)</div>
                </div>
                <v-divider vertical class="border-opacity-10"></v-divider>
                <div class="text-center">
                    <div class="text-h6 font-weight-bold text-white">
                        {{ signals.profiles_analyzed }}
                    </div>
                    <div class="text-caption text-white/40">Perfiles</div>
                </div>
            </div>
        </div>

        <!-- Anomalies -->
        <div v-if="anomalies.length > 0" class="px-6 py-4">
            <div
                class="text-caption font-weight-bold mb-3 text-red-300 uppercase"
                style="letter-spacing: 0.12em"
            >
                <v-icon icon="mdi-alert-circle" size="14" class="mr-1"></v-icon>
                Anomalías Detectadas ({{ anomalies.length }})
            </div>
            <div
                v-for="(anomaly, i) in anomalies"
                :key="i"
                class="anomaly-item d-flex align-center ga-3 pa-3 mb-2 rounded-xl"
                :class="anomalySeverityClass(anomaly.severity)"
            >
                <v-icon
                    :icon="anomaly.icon"
                    size="20"
                    :color="severityColor(anomaly.severity)"
                ></v-icon>
                <div class="text-caption flex-grow-1 text-white/80">
                    {{ anomaly.message }}
                </div>
                <v-chip
                    :color="severityColor(anomaly.severity)"
                    size="x-small"
                    variant="flat"
                    class="font-weight-bold"
                >
                    {{ anomaly.severity.toUpperCase() }}
                </v-chip>
            </div>
        </div>

        <!-- AI Diagnosis -->
        <div v-if="aiAnalysis" class="px-6 pb-6">
            <v-divider class="border-opacity-10 mb-4"></v-divider>
            <div
                class="text-caption font-weight-bold mb-2 text-cyan-300 uppercase"
                style="letter-spacing: 0.12em"
            >
                <v-icon icon="mdi-brain" size="14" class="mr-1"></v-icon>
                Diagnóstico Sentinel
            </div>
            <div class="diagnosis-box pa-4 mb-4 rounded-xl">
                <p class="text-body-2 mb-0 text-white/90 italic">
                    "{{ aiAnalysis.diagnosis }}"
                </p>
            </div>

            <div
                class="text-caption font-weight-bold mb-2 text-amber-300 uppercase"
                style="letter-spacing: 0.12em"
            >
                Acciones para el CEO:
            </div>
            <div
                v-for="(action, i) in aiAnalysis.ceo_actions"
                :key="'action-' + i"
                class="d-flex align-start ga-2 mb-2"
            >
                <v-icon
                    icon="mdi-lightning-bolt"
                    size="16"
                    color="amber-accent-2"
                    class="mt-1 flex-shrink-0"
                ></v-icon>
                <span class="text-caption text-white/70">{{ action }}</span>
            </div>

            <div
                class="critical-node-badge d-flex align-center ga-2 pa-3 mt-4 rounded-xl"
            >
                <v-icon
                    icon="mdi-map-marker-alert"
                    size="20"
                    color="red-lighten-1"
                ></v-icon>
                <div>
                    <div class="text-caption font-weight-bold text-red-300">
                        Nodo Crítico:
                    </div>
                    <div class="text-body-2 text-white">
                        {{ aiAnalysis.critical_node }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!scanning && !aiAnalysis" class="pa-8 text-center">
            <v-icon
                icon="mdi-shield-search"
                size="64"
                class="mb-4 text-white/10"
            ></v-icon>
            <p class="text-body-2 text-white/30">
                Presiona el radar para ejecutar un escaneo de salud
                organizacional.
            </p>
        </div>
    </v-card>
</template>

<script setup lang="ts">
import axios from 'axios';
import { computed, ref } from 'vue';

interface Anomaly {
    type: string;
    severity: 'high' | 'medium' | 'low';
    message: string;
    icon: string;
}

interface AiAnalysis {
    diagnosis: string;
    ceo_actions: string[];
    critical_node: string;
}

const scanning = ref(false);
const healthScore = ref(0);
const signals = ref({
    pulse_count: 0,
    avg_sentiment: 0,
    sentiment_trend: 'stable',
    profiles_analyzed: 0,
});
const anomalies = ref<Anomaly[]>([]);
const aiAnalysis = ref<AiAnalysis | null>(null);

const hasAnomalies = computed(() =>
    anomalies.value.some((a) => a.severity === 'high'),
);

const healthColor = computed(() => {
    if (healthScore.value >= 70) return 'green';
    if (healthScore.value >= 40) return 'amber';
    return 'red';
});

const healthIcon = computed(() => {
    if (healthScore.value >= 70) return 'mdi-shield-check';
    if (healthScore.value >= 40) return 'mdi-shield-alert';
    return 'mdi-shield-off';
});

const trendIcon = computed(() => {
    if (signals.value.sentiment_trend === 'improving') return 'mdi-trending-up';
    if (signals.value.sentiment_trend === 'declining')
        return 'mdi-trending-down';
    return 'mdi-minus';
});

const trendColor = computed(() => {
    if (signals.value.sentiment_trend === 'improving')
        return 'text-green-accent-3';
    if (signals.value.sentiment_trend === 'declining')
        return 'text-red-accent-2';
    return 'text-white';
});

const anomalySeverityClass = (severity: string) => ({
    'bg-red-500/10 border border-red-500/20': severity === 'high',
    'bg-amber-500/10 border border-amber-500/20': severity === 'medium',
    'bg-blue-500/10 border border-blue-500/20': severity === 'low',
});

const severityColor = (severity: string) => {
    if (severity === 'high') return 'red-lighten-1';
    if (severity === 'medium') return 'amber';
    return 'blue-lighten-2';
};

const runScan = async () => {
    scanning.value = true;
    try {
        const response = await axios.get('/api/pulse/health-scan');
        const data = response.data.data;

        healthScore.value = data.health_score;
        signals.value = data.signals;
        anomalies.value = data.anomalies;
        aiAnalysis.value = data.ai_analysis;
    } catch (error) {
        console.error('Error en escaneo de salud:', error);
    } finally {
        scanning.value = false;
    }
};
</script>

<style scoped>
.culture-sentinel-card {
    background: linear-gradient(
        145deg,
        rgba(15, 23, 42, 0.95),
        rgba(30, 41, 59, 0.9)
    ) !important;
    backdrop-filter: blur(16px);
    max-width: 480px;
}

.sentinel-header {
    background: linear-gradient(
        135deg,
        rgba(99, 102, 241, 0.1),
        rgba(236, 72, 153, 0.05)
    );
}

.sentinel-avatar {
    box-shadow: 0 0 20px rgba(var(--v-theme-success), 0.3);
}

.health-ring :deep(.v-progress-circular__underlay) {
    stroke: rgba(255, 255, 255, 0.05) !important;
}

.diagnosis-box {
    background: rgba(6, 182, 212, 0.08);
    border: 1px solid rgba(6, 182, 212, 0.15);
}

.critical-node-badge {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.15);
}

.pulse-glow {
    animation: pulse-border 3s ease-in-out infinite;
}

@keyframes pulse-border {
    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
    50% {
        box-shadow: 0 0 20px 4px rgba(239, 68, 68, 0.15);
    }
}
</style>

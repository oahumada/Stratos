<template>
    <div
        class="simulation-status-panel glass-panel-strong pa-4 elevation-5 rounded-xl"
        v-if="visible"
    >
        <div class="d-flex align-center mb-4">
            <v-avatar color="deep-orange-darken-4" size="40" class="mr-3">
                <v-icon icon="mdi-molecule" color="white"></v-icon>
            </v-avatar>
            <div>
                <div class="text-overline line-height-1 text-white opacity-70">
                    Scenario IQ
                </div>
                <div class="text-h6 font-weight-bold text-white">
                    Simulación Activa
                </div>
            </div>
            <v-spacer></v-spacer>
            <v-chip size="x-small" color="success" variant="flat"
                >IA ANALYZING</v-chip
            >
        </div>

        <v-divider class="border-opacity-25 mb-4"></v-divider>

        <v-row dense>
            <v-col cols="6">
                <div
                    class="metric-card glass-panel pa-3 rounded-lg text-center"
                >
                    <div class="text-caption text-white opacity-60">
                        Probabilidad Éxito
                    </div>
                    <div class="text-h5 font-weight-bold text-cyan-accent-2">
                        {{ metrics.success_probability }}%
                    </div>
                </div>
            </v-col>
            <v-col cols="6">
                <div
                    class="metric-card glass-panel pa-3 rounded-lg text-center"
                >
                    <div class="text-caption text-white opacity-60">
                        Sinergia Estimada
                    </div>
                    <div
                        class="text-h5 font-weight-bold text-light-green-accent-3"
                    >
                        {{ metrics.synergy_score }}/10
                    </div>
                </div>
            </v-col>
            <v-col cols="6">
                <div
                    class="metric-card glass-panel pa-3 rounded-lg text-center"
                >
                    <div class="text-caption text-white opacity-60">
                        Fricción Cultural
                    </div>
                    <div class="text-h5 font-weight-bold text-orange-accent-2">
                        {{ metrics.cultural_friction }}%
                    </div>
                </div>
            </v-col>
            <v-col cols="6">
                <div
                    class="metric-card glass-panel pa-3 rounded-lg text-center"
                >
                    <div class="text-caption text-white opacity-60">
                        Tiempo Pico Perfección
                    </div>
                    <div class="text-h5 font-weight-bold text-purple-accent-1">
                        {{ metrics.time_to_peak }}m
                    </div>
                </div>
            </v-col>
        </v-row>

        <div class="mt-4">
            <div class="text-caption mb-2 text-white opacity-70">
                Resumen del Simulador Orgánico:
            </div>
            <div class="text-body-2 line-clamp-3 text-white italic">
                "La estructura propuesta presenta un alto acoplamiento en el
                nodo de {{ metrics.key_node }}. Recomiendo fortalecer las
                capacidades de {{ metrics.recommendation }} para mitigar riesgos
                de ejecución."
            </div>
        </div>

        <v-btn
            block
            color="white"
            variant="tonal"
            class="text-none mt-4"
            prepend-icon="mdi-refresh"
            size="small"
            @click="$emit('re-run')"
        >
            Recalcular Escenario
        </v-btn>

        <v-btn
            block
            color="pink-darken-1"
            class="text-none mt-2 shadow-lg"
            prepend-icon="mdi-shield-check"
            size="small"
            :loading="mitigating"
            @click="getMitigationPlan"
        >
            Generar Plan de Remediación
        </v-btn>

        <div v-if="mitigationPlan" class="animate-fade-in mt-4">
            <v-divider class="border-opacity-25 mb-4"></v-divider>
            <div
                class="text-caption font-weight-bold mb-2 tracking-wider text-pink-300 uppercase"
            >
                Plan de Remediación (Sentinel):
            </div>
            <div class="space-y-2">
                <div
                    v-for="(action, i) in mitigationPlan.actions"
                    :key="i"
                    class="pa-2 rounded border border-white/5 bg-white/5 text-xs text-white/80"
                >
                    • {{ action }}
                </div>
            </div>
            <div
                class="pa-2 mt-3 rounded border border-cyan-500/20 bg-cyan-950/30 text-xs"
            >
                <v-icon
                    icon="mdi-school"
                    size="14"
                    class="mr-1"
                    color="cyan"
                ></v-icon>
                <strong>Capacitación:</strong> {{ mitigationPlan.training }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { defineEmits, defineProps, ref } from 'vue';

const props = defineProps({
    visible: { type: Boolean, default: true },
    scenarioId: { type: [Number, String], required: true },
    metrics: {
        type: Object,
        default: () => ({
            success_probability: 78,
            synergy_score: 8.4,
            cultural_friction: 12,
            time_to_peak: 6,
            key_node: 'Arquitectura de Datos',
            recommendation: 'MLOps y Gobernanza',
        }),
    },
});

defineEmits(['re-run']);

const mitigating = ref(false);
const mitigationPlan = ref<{
    actions: string[];
    training: string;
    security_insight: string;
} | null>(null);

const getMitigationPlan = async () => {
    mitigating.value = true;
    try {
        const response = await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/mitigate`,
            {
                metrics: props.metrics,
            },
        );
        mitigationPlan.value = response.data.plan;
    } catch (error) {
        console.error('Error al mitigar:', error);
    } finally {
        mitigating.value = false;
    }
};
</script>

<style scoped>
.simulation-status-panel {
    position: absolute;
    bottom: 24px;
    right: 24px;
    width: 340px;
    z-index: 100;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(15, 23, 42, 0.8) !important;
}

.glass-panel {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.line-height-1 {
    line-height: 1;
}
</style>

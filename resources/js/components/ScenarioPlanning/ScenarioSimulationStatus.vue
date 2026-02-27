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
    </div>
</template>

<script setup lang="ts">
import { defineEmits, defineProps } from 'vue';

const props = defineProps({
    visible: { type: Boolean, default: true },
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

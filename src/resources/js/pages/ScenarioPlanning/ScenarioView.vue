<script setup lang="ts">
import InsightsPanel from './InsightsPanel.vue';
const { scenario: _scenario, capabilities, connections } = defineProps({
    scenario: Object,
    capabilities: Array,
    connections: Array, // Nueva prop para las conexiones
});
// mark unused prop to satisfy linter
void _scenario;

const handleNodeClick = (node) => {
    console.log('Capacidad seleccionada:', node);
    // Aquí puedes abrir un modal, navegar a detalle, etc.
};
</script>

<template>
    <div
        class="from-stratos-darker via-stratos-dark min-h-screen bg-gradient-to-br to-blue-950"
    >
        <!-- Header existente -->

        <div class="flex h-[calc(100vh-73px)]">
            <!-- Canvas Central con el Cerebro -->
            <main class="relative flex-1 overflow-hidden">
                <BrainCanvas
                    :capabilities="capabilities"
                    :connections="connections"
                    @node-click="handleNodeClick"
                />
            </main>

            <!-- Panel lateral existente -->
            <aside
                class="w-96 overflow-y-auto bg-black/20 p-6 backdrop-blur-md"
            >
                <InsightsPanel />
            </aside>
        </div>
    </div>
</template>

<style scoped>
/* Estilos específicos para el efecto de profundidad */
.glass-panel-strong {
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(24px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
</style>

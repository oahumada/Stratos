<template>
    <div
        class="from-stratos-darker via-stratos-dark min-h-screen bg-gradient-to-br to-blue-950 text-white"
    >
        <!-- Header -->
        <header
            class="flex items-center justify-between border-b border-white/5 px-8 py-4"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 text-lg font-bold"
                >
                    S
                </div>
                <div>
                    <h1 class="text-xl font-semibold">Stratos — AI Adoption</h1>
                    <p class="text-xs text-white/40">
                        Scenario: IA Adoption Accelerator
                    </p>
                </div>
            </div>
            <div class="text-sm text-white/50">
                Última ejecución: 2026-01-12 · Owner: Omar
            </div>
        </header>

        <div class="flex h-[calc(100vh-73px)]">
            <!-- Canvas Central -->
            <div class="relative flex-1 p-8">
                <!-- Mini-map (opcional) -->
                <div
                    class="glass-panel absolute top-8 right-8 flex h-32 w-48 items-center justify-center rounded-xl text-xs text-white/30"
                >
                    Mini-map
                </div>

                <!-- Capability Ecosystem -->
                <CapabilityEcosystem :capabilities="capabilities" />

                <!-- Bottom Info Bar -->
                <div
                    class="glass-panel-strong absolute right-96 bottom-8 left-8 flex items-center justify-between rounded-2xl px-6 py-4"
                >
                    <div>
                        <div class="mb-1 text-xs text-white/50">
                            Health: 72% · Coverage
                        </div>
                        <div class="text-sm">
                            Top risks: System Design, Kubernetes, Mentoring
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-xs text-white/50">
                            Horizon: 12 months
                        </div>
                        <button
                            class="rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-2 text-sm font-semibold transition-all hover:shadow-lg hover:shadow-cyan-500/50"
                        >
                            Run full analysis
                        </button>
                    </div>
                </div>
            </div>

            <!-- Insights Panel -->
            <aside
                class="glass-panel-strong w-96 overflow-y-auto border-l border-white/10 p-6"
            >
                <InsightsPanel :scenario="scenario" />
            </aside>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import CapabilityEcosystem from './CapabilityEcosystem.vue';
import InsightsPanel from './InsightsPanel.vue';

const scenario = ref({
    name: 'IA Adoption Accelerator',
    health: 72,
    coverage: 68,
    risk: 3,
});

const capabilities = ref([
    {
        id: 1,
        name: 'System Design',
        level: 2,
        required: 4,
        importance: 5,
        x: 50,
        y: 40,
    },
    {
        id: 2,
        name: 'Data Governance',
        level: 3,
        required: 4,
        importance: 4,
        x: 30,
        y: 60,
    },
    {
        id: 3,
        name: 'MLOps',
        level: 2,
        required: 5,
        importance: 5,
        x: 70,
        y: 50,
    },
    {
        id: 4,
        name: 'Prompt Engineering',
        level: 4,
        required: 4,
        importance: 3,
        x: 40,
        y: 25,
    },
    {
        id: 5,
        name: 'Risk Analysis',
        level: 3,
        required: 3,
        importance: 3,
        x: 60,
        y: 70,
    },
    {
        id: 6,
        name: 'Change Management',
        level: 3,
        required: 4,
        importance: 4,
        x: 20,
        y: 35,
    },
]);
</script>

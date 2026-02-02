<template>
    <div
        class="min-h-screen bg-gradient-to-b from-slate-900 to-slate-800 p-6 text-white"
    >
        <div class="mx-auto max-w-7xl">
            <h1 class="mb-4 text-2xl font-light">Scenario Canvas — Demo</h1>

            <div class="mb-4 flex items-center gap-4">
                <button
                    @click="reset"
                    class="rounded bg-indigo-600 px-3 py-1 hover:bg-indigo-500"
                >
                    Reset layout
                </button>
                <div class="text-sm text-slate-300">
                    Seleccionado:
                    <span class="text-white">{{ selected?.id ?? '-' }}</span>
                </div>
            </div>

            <div
                class="h-[640px] w-full overflow-hidden rounded-lg bg-[rgba(255,255,255,0.02)] shadow-lg"
            >
                <ScenarioCanvas
                    :width="1200"
                    :height="700"
                    :nodes="nodes"
                    :links="links"
                    containerClass="p-2"
                    @select="onSelect"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import ScenarioCanvas from '@/components/ScenarioCanvas.vue';
import { ref } from 'vue';

const nodes = ref([
    { id: 'Plan', group: 1 },
    { id: 'Scope', group: 1 },
    { id: 'Roles', group: 2 },
    { id: 'Projects', group: 2 },
    { id: 'Risks', group: 3 },
    { id: 'Stakeholders', group: 3 },
]);

const links = ref([
    { source: 'Plan', target: 'Scope' },
    { source: 'Plan', target: 'Roles' },
    { source: 'Scope', target: 'Projects' },
    { source: 'Roles', target: 'Risks' },
    { source: 'Stakeholders', target: 'Projects' },
]);

const selected = ref(null as any);

function onSelect(node: any) {
    selected.value = node;
}

function reset() {
    // reassign arrays to trigger recreation in the canvas
    nodes.value = JSON.parse(JSON.stringify(nodes.value));
    links.value = JSON.parse(JSON.stringify(links.value));
    selected.value = null;
}
</script>

<style scoped>
:root {
    --card-bg: rgba(255, 255, 255, 0.02);
}
</style>

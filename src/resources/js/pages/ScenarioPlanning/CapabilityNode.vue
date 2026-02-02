<template>
    <div
        class="group absolute -translate-x-1/2 -translate-y-1/2 transform cursor-pointer transition-all duration-500 hover:scale-110"
        :style="{ width: nodeSize, height: nodeSize }"
    >
        <!-- Glow exterior -->
        <div
            class="absolute inset-0 animate-pulse rounded-full opacity-30 blur-2xl"
            :class="glowColor"
        ></div>

        <!-- Nodo principal -->
        <div
            class="relative flex h-full w-full items-center justify-center rounded-full border-2 transition-all duration-300"
            :class="[borderColor, bgColor, 'backdrop-blur-xl']"
        >
            <!-- Contenido -->
            <div class="px-4 text-center">
                <div class="text-xs font-semibold text-white/90">
                    {{ capability.name }}
                </div>
                <div class="mt-1 text-[10px] text-white/50">
                    Gap: {{ gap }} levels · Confidence: {{ confidence }}%
                </div>
            </div>

            <!-- Tooltip on hover -->
            <div
                class="pointer-events-none absolute -bottom-16 left-1/2 -translate-x-1/2 transform opacity-0 transition-opacity duration-300 group-hover:opacity-100"
            >
                <div
                    class="glass-panel-strong rounded-lg px-3 py-2 text-xs whitespace-nowrap"
                >
                    <div>
                        Current:
                        <span class="text-cyan-400"
                            >N{{ capability.level }}</span
                        >
                    </div>
                    <div>
                        Required:
                        <span class="text-purple-400"
                            >R{{ capability.required }}</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps(['capability']);

const gap = computed(() => props.capability.required - props.capability.level);
const confidence = computed(() => Math.max(50, 100 - gap.value * 15));

const nodeSize = computed(() => {
    const base = 120;
    const scale = props.capability.importance * 20;
    return `${base + scale}px`;
});

const borderColor = computed(() => {
    if (gap.value <= 0) return 'border-cyan-400/60';
    if (gap.value === 1) return 'border-teal-400/60';
    if (gap.value === 2) return 'border-yellow-400/60';
    return 'border-red-400/60';
});

const bgColor = computed(() => {
    if (gap.value <= 0) return 'bg-cyan-500/10';
    if (gap.value === 1) return 'bg-teal-500/10';
    if (gap.value === 2) return 'bg-yellow-500/10';
    return 'bg-red-500/10';
});

const glowColor = computed(() => {
    if (gap.value <= 0) return 'bg-cyan-400';
    if (gap.value === 1) return 'bg-teal-400';
    if (gap.value === 2) return 'bg-yellow-400';
    return 'bg-red-400';
});
</script>

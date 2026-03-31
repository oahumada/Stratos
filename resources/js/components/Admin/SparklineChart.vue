<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label: string;
    data: number[];
    color?: 'green' | 'amber' | 'red' | 'blue';
    height?: number;
}

const props = withDefaults(defineProps<Props>(), {
    color: 'green',
    height: 40,
});

const min = computed(() => Math.min(...props.data));
const max = computed(() => Math.max(...props.data));
const range = computed(() => max.value - min.value || 1);
const average = computed(() =>
    props.data.length > 0
        ? props.data.reduce((a, b) => a + b, 0) / props.data.length
        : 0,
);

const points = computed(() => {
    const width = 100;
    const pointWidth = width / Math.max(props.data.length - 1, 1);

    return props.data
        .map((value, index) => {
            const x = index * pointWidth;
            const y = 100 - ((value - min.value) / range.value) * 100;
            return `${x},${y}`;
        })
        .join(' ');
});

const colorClasses = computed(() => {
    switch (props.color) {
        case 'green':
            return {
                line: 'stroke-green-500',
                fill: 'fill-green-100 dark:fill-green-950',
            };
        case 'amber':
            return {
                line: 'stroke-amber-500',
                fill: 'fill-amber-100 dark:fill-amber-950',
            };
        case 'red':
            return {
                line: 'stroke-red-500',
                fill: 'fill-red-100 dark:fill-red-950',
            };
        case 'blue':
            return {
                line: 'stroke-blue-500',
                fill: 'fill-blue-100 dark:fill-blue-950',
            };
        default:
            return {
                line: 'stroke-green-500',
                fill: 'fill-green-100 dark:fill-green-950',
            };
    }
});

const currentValue = computed(() => props.data[props.data.length - 1]);
const trend = computed(() => {
    if (props.data.length < 2) return 'stable';
    const last = props.data[props.data.length - 1];
    const previous = props.data[props.data.length - 2];
    if (last > previous) return 'up';
    if (last < previous) return 'down';
    return 'stable';
});
</script>

<template>
    <div class="w-full">
        <div class="mb-2 flex items-baseline justify-between">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ label }}
            </p>
            <div class="flex items-center gap-1">
                <span class="text-sm font-bold text-gray-900 dark:text-white">
                    {{ Math.round(currentValue) }}
                </span>
                <span
                    v-if="trend === 'up'"
                    class="text-xs text-green-600 dark:text-green-400"
                >
                    ↑
                </span>
                <span
                    v-else-if="trend === 'down'"
                    class="text-xs text-red-600 dark:text-red-400"
                >
                    ↓
                </span>
                <span v-else class="text-xs text-gray-400">→</span>
            </div>
        </div>

        <svg
            :viewBox="`0 0 100 ${height}`"
            class="w-full"
            :style="{ height: `${height}px` }"
        >
            <!-- Area fill -->
            <defs>
                <linearGradient
                    id="sparkGradient"
                    x1="0%"
                    y1="0%"
                    x2="0%"
                    y2="100%"
                >
                    <stop
                        offset="0%"
                        :stop-color="`var(--color-${color})`"
                        stop-opacity="0.3"
                    />
                    <stop
                        offset="100%"
                        :stop-color="`var(--color-${color})`"
                        stop-opacity="0"
                    />
                </linearGradient>
            </defs>

            <!-- Polyline area -->
            <polyline
                :points="`0,${height} ${points} 100,${height}`"
                fill="url(#sparkGradient)"
            />

            <!-- Line -->
            <polyline
                :points="points"
                :class="`${colorClasses.line} fill-none`"
                stroke-width="1.5"
            />

            <!-- Data points (small circles) -->
            <circle
                v-for="(value, index) in data"
                :key="index"
                :cx="(100 / Math.max(data.length - 1, 1)) * index"
                :cy="100 - ((value - min) / range) * 100"
                r="1"
                :class="colorClasses.line"
                fill="currentColor"
            />
        </svg>

        <!-- Stats -->
        <div
            class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400"
        >
            <span>Min: {{ Math.round(min) }}</span>
            <span>Avg: {{ Math.round(average) }}</span>
            <span>Max: {{ Math.round(max) }}</span>
        </div>
    </div>
</template>

<style scoped>
svg {
    overflow: visible;
}
</style>

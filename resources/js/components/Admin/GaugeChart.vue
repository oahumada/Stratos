<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label: string;
    value: number;
    max?: number;
    color?: 'green' | 'amber' | 'red' | 'blue';
    unit?: string;
    size?: 'small' | 'medium' | 'large';
}

const props = withDefaults(defineProps<Props>(), {
    max: 100,
    color: 'green',
    unit: '%',
    size: 'medium',
});

const percentage = computed(() => Math.min((props.value / props.max) * 100, 100));

const gaugeSize = computed(() => {
    switch (props.size) {
        case 'small':
            return 'h-24 w-24';
        case 'large':
            return 'h-40 w-40';
        default:
            return 'h-32 w-32';
    }
});

const gaugeRadius = computed(() => {
    switch (props.size) {
        case 'small':
            return 36;
        case 'large':
            return 60;
        default:
            return 48;
    }
});

const gaugeStrokeWidth = computed(() => {
    switch (props.size) {
        case 'small':
            return 4;
        case 'large':
            return 6;
        default:
            return 5;
    }
});

const circumference = computed(() => 2 * Math.PI * gaugeRadius.value);

const strokeDashoffset = computed(
    () => circumference.value - (circumference.value * percentage.value) / 100
);

const colorClasses = computed(() => {
    switch (props.color) {
        case 'green':
            return 'stroke-green-500';
        case 'amber':
            return 'stroke-amber-500';
        case 'red':
            return 'stroke-red-500';
        case 'blue':
            return 'stroke-blue-500';
        default:
            return 'stroke-green-500';
    }
});

const textColorClasses = computed(() => {
    switch (props.color) {
        case 'green':
            return 'text-green-600 dark:text-green-400';
        case 'amber':
            return 'text-amber-600 dark:text-amber-400';
        case 'red':
            return 'text-red-600 dark:text-red-400';
        case 'blue':
            return 'text-blue-600 dark:text-blue-400';
        default:
            return 'text-green-600 dark:text-green-400';
    }
});

const textSize = computed(() => {
    switch (props.size) {
        case 'small':
            return 'text-sm';
        case 'large':
            return 'text-2xl';
        default:
            return 'text-lg';
    }
});
</script>

<template>
    <div class="flex flex-col items-center gap-2">
        <div :class="`relative ${gaugeSize} mx-auto`">
            <svg
                :viewBox="`0 0 ${gaugeRadius * 2 + 20} ${gaugeRadius * 2 + 20}`"
                class="w-full h-full"
            >
                <!-- Background circle -->
                <circle
                    :cx="gaugeRadius + 10"
                    :cy="gaugeRadius + 10"
                    :r="gaugeRadius"
                    fill="none"
                    stroke="currentColor"
                    class="stroke-gray-200 dark:stroke-gray-700"
                    :stroke-width="gaugeStrokeWidth"
                />

                <!-- Progress circle -->
                <circle
                    :cx="gaugeRadius + 10"
                    :cy="gaugeRadius + 10"
                    :r="gaugeRadius"
                    fill="none"
                    :class="colorClasses"
                    :stroke-width="gaugeStrokeWidth"
                    stroke-linecap="round"
                    style="
                        transform-origin: center;
                        transform: rotate(-90deg);
                        transition: stroke-dashoffset 0.3s ease;
                    "
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="strokeDashoffset"
                />
            </svg>

            <!-- Center text -->
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <div :class="`font-bold ${textColorClasses} ${textSize}`">
                    {{ Math.round(value) }}{{ unit }}
                </div>
            </div>
        </div>

        <!-- Label -->
        <p class="text-center text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
        </p>
    </div>
</template>

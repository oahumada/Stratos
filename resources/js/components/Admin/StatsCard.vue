<template>
    <div
        class="rounded-lg border-l-4 bg-white p-6 shadow dark:bg-gray-800"
        :class="borderColor"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ label }}
                </p>
                <p
                    class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                >
                    {{ value }}
                </p>
            </div>
            <div class="text-4xl">{{ icon }}</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label: string;
    value: number | string;
    icon?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    icon: '📊',
});

const borderColor = computed(() => {
    if (props.class?.includes('green')) return 'border-green-500';
    if (props.class?.includes('red')) return 'border-red-500';
    if (props.class?.includes('yellow')) return 'border-yellow-500';
    return 'border-blue-500';
});
</script>

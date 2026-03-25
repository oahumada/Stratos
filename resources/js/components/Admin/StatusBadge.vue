<template>
    <span :class="badgeClass">
        {{ statusLabel }}
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    status: string;
}

const props = defineProps<Props>();

const statusLabel = computed(() => {
    return (
        props.status.charAt(0).toUpperCase() +
        props.status.slice(1).replace('_', ' ')
    );
});

const badgeClass = computed(() => {
    const baseClass = 'px-2 py-1 rounded text-xs font-medium';
    switch (props.status) {
        case 'success':
            return `${baseClass} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200`;
        case 'failed':
            return `${baseClass} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200`;
        case 'pending':
            return `${baseClass} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200`;
        case 'running':
            return `${baseClass} bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200`;
        case 'dry_run':
            return `${baseClass} bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200`;
        case 'cancelled':
            return `${baseClass} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
        default:
            return `${baseClass} bg-gray-100 text-gray-800`;
    }
});
</script>

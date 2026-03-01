<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

interface Props {
    class?: HTMLAttributes['class'];
    noHover?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    noHover: false,
});
</script>

<template>
    <div
        data-slot="st-card-glass"
        :class="
            cn(
                'st-card-glass flex flex-col gap-4 p-6',
                props.noHover &&
                    'hover:transform-none hover:border-transparent',
                props.class,
            )
        "
    >
        <slot />
    </div>
</template>

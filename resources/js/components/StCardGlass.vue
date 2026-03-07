<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

interface Props {
    class?: HTMLAttributes['class'];
    noHover?: boolean;
    as?: 'div' | 'section' | 'article' | 'aside';
    role?: string;
    ariaLabel?: string;
    ariaLabelledby?: string;
}

const props = withDefaults(defineProps<Props>(), {
    noHover: false,
    as: 'section',
});
</script>

<template>
    <component
        :is="props.as"
        data-slot="st-card-glass"
        :role="props.role"
        :aria-label="props.ariaLabel"
        :aria-labelledby="props.ariaLabelledby"
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
    </component>
</template>

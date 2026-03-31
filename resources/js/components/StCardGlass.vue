<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    class?: HTMLAttributes['class'];
    noHover?: boolean;
    as?: any;
    role?: string;
    ariaLabel?: string;
    ariaLabelledby?: string;
    indicator?:
        | 'indigo'
        | 'fuchsia'
        | 'emerald'
        | 'amber'
        | 'rose'
        | 'cyan'
        | 'purple'
        | boolean;
}

const props = withDefaults(defineProps<Props>(), {
    noHover: false,
    as: 'section',
    indicator: false,
});

const indicatorColors = {
    indigo: 'from-transparent via-indigo-500 to-transparent shadow-[0_0_20px_rgba(99,102,241,0.6)]',
    fuchsia:
        'from-transparent via-fuchsia-500 to-transparent shadow-[0_0_20px_rgba(217,70,239,0.6)]',
    emerald:
        'from-transparent via-emerald-500 to-transparent shadow-[0_0_20px_rgba(16,185,129,0.6)]',
    amber: 'from-transparent via-amber-500 to-transparent shadow-[0_0_20px_rgba(245,158,11,0.6)]',
    rose: 'from-transparent via-rose-500 to-transparent shadow-[0_0_20px_rgba(244,63,94,0.6)]',
    cyan: 'from-transparent via-cyan-500 to-transparent shadow-[0_0_20px_rgba(34,211,238,0.6)]',
    purple: 'from-transparent via-purple-500 to-transparent shadow-[0_0_20px_rgba(168,85,247,0.6)]',
};

const indicatorClass =
    indicatorColors[props.indicator as keyof typeof indicatorColors] ||
    indicatorColors.indigo;
</script>

<template>
    <component
        :is="props.as"
        v-bind="$attrs"
        data-slot="st-card-glass"
        :role="props.role"
        :aria-label="props.ariaLabel"
        :aria-labelledby="props.ariaLabelledby"
        :class="
            cn(
                'st-card-glass relative flex flex-col gap-4 p-8',
                props.noHover &&
                    'hover:transform-none hover:border-transparent',
                props.class,
            )
        "
    >
        <!-- Indicator Light (UX Pilar 1) -->
        <div
            v-if="props.indicator"
            class="absolute top-0 left-0 h-px w-full bg-linear-to-r transition-all duration-500"
            :class="indicatorClass"
        ></div>

        <slot />
    </component>
</template>

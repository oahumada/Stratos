<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    variant?:
        | 'primary'
        | 'secondary'
        | 'info'
        | 'error'
        | 'warning'
        | 'success'
        | 'sky'
        | 'fuchsia'
        | 'glass';
    size?: 'sm' | 'md';
    as?: 'span' | 'div';
    role?: string;
    ariaLabel?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'glass',
    size: 'sm',
    as: 'span',
});

const variantClasses = {
    primary: 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
    secondary: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
    error: 'bg-rose-500/20 text-rose-300 border-rose-500/30',
    warning: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
    success: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
    info: 'bg-indigo-500/20 text-indigo-200 border-indigo-500/30',
    sky: 'bg-sky-500/20 text-sky-300 border-sky-500/30',
    fuchsia: 'bg-fuchsia-500/20 text-fuchsia-300 border-fuchsia-500/30',
    glass: 'bg-white/10 text-white border-white/10',
};

const sizeClasses = {
    sm: 'px-2 py-0.5 text-[0.65rem]',
    md: 'px-3 py-1 text-xs',
};

const classes = computed(() => {
    return cn(
        'inline-flex items-center justify-center rounded-full border font-black tracking-widest uppercase transition-all duration-300',
        variantClasses[props.variant],
        sizeClasses[props.size],
        props.class,
    );
});
</script>

<template>
    <component
        :is="props.as"
        :class="classes"
        :role="props.role"
        :aria-label="props.ariaLabel"
    >
        <span
            v-if="variant !== 'glass'"
            class="mr-1.5 h-1 w-1 animate-pulse rounded-full bg-current opacity-80"
        ></span>
        <slot />
    </component>
</template>

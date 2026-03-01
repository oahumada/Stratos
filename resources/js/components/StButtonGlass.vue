<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { Component } from 'vue';
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'secondary' | 'ghost' | 'glass';
    size?: 'sm' | 'md' | 'lg';
    loading?: boolean;
    disabled?: boolean;
    block?: boolean;
    circle?: boolean;
    icon?: string | Component;
    iconWeight?: 'thin' | 'light' | 'regular' | 'bold' | 'fill' | 'duotone';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'glass',
    size: 'md',
    loading: false,
    disabled: false,
    block: false,
    circle: false,
    iconWeight: 'regular',
});

const variantClasses = {
    primary:
        'bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-500/20 border-indigo-400/30',
    secondary:
        'bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 border-emerald-400/30',
    ghost: 'bg-transparent hover:bg-white/5 text-white/70 hover:text-white border-transparent',
    glass: 'bg-white/5 hover:bg-white/10 backdrop-blur-md border-white/10 hover:border-white/20 text-white shadow-xl',
};

const sizeClasses = {
    sm: 'h-8 min-w-[32px] px-3 py-1.5 text-xs rounded-lg gap-1.5',
    md: 'h-10 min-w-[40px] px-6 py-2.5 text-sm rounded-xl gap-2',
    lg: 'h-12 min-w-[48px] px-8 py-3.5 text-base rounded-2xl gap-3',
};

const classes = computed(() => {
    return cn(
        'inline-flex items-center justify-center overflow-hidden border font-bold tracking-tight transition-all duration-300 active:scale-95 disabled:pointer-events-none disabled:opacity-50',
        props.circle ? 'aspect-square min-w-0 rounded-full p-0' : '',
        props.block && !props.circle && 'flex w-full',
        variantClasses[props.variant],
        sizeClasses[props.size],
        props.class,
    );
});
</script>

<template>
    <button :class="classes" :disabled="disabled || loading">
        <v-progress-circular
            v-if="loading"
            indeterminate
            size="18"
            width="2"
            class="mr-2"
        />
        <!-- Soporte para antiguos iconos MDI como string -->
        <v-icon
            v-if="icon && typeof icon === 'string' && !loading"
            :size="size === 'sm' ? 16 : 20"
            :class="['shrink-0', $slots.default && !circle ? 'mr-2' : '']"
            >{{ icon }}</v-icon
        >
        <!-- Soporte para nuevos iconos componetizados (Phosphor, etc.) -->
        <component
            :is="icon"
            v-else-if="icon && typeof icon !== 'string' && !loading"
            :size="size === 'sm' ? 16 : 20"
            :weight="iconWeight"
            :class="['shrink-0', $slots.default && !circle ? 'mr-2' : '']"
        />
        <slot v-if="!circle" />
    </button>
</template>

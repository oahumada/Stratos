<template>
    <div class="header-wrap mb-10">
        <div class="mb-2 flex items-center gap-3">
            <div class="h-2 w-12 rounded-full bg-indigo-500"></div>
            <span
                class="text-xs font-black tracking-[0.3em] text-indigo-400 uppercase"
            >
                {{ overline }}
            </span>
        </div>

        <div class="flex items-start justify-between gap-6">
            <div>
                <h1
                    class="mb-3 text-4xl font-black tracking-tight text-white md:text-5xl"
                >
                    <span>{{ titleParts.first }}</span>
                    <span
                        v-if="titleParts.rest"
                        :class="[
                            'bg-linear-to-r bg-clip-text text-transparent',
                            secondTermGradientClass,
                        ]"
                    >
                        {{ titleParts.rest }}
                    </span>
                </h1>
                <p class="max-w-3xl text-lg font-medium text-white/40">
                    {{ slogan }}
                </p>
            </div>

            <div class="icon-glass-header hidden shrink-0 md:flex">
                <v-icon
                    v-if="typeof icon === 'string'"
                    :icon="icon"
                    size="48"
                />
                <component v-else :is="icon" :size="48" weight="duotone" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue';
import { computed } from 'vue';

interface Props {
    title: string;
    slogan: string;
    icon: string | Component;
    overline?: string;
    gradientVariant?: 'navigator' | 'emerald' | 'sunset' | 'rose';
}

const props = withDefaults(defineProps<Props>(), {
    overline: 'Stratos Navigator',
    gradientVariant: 'navigator',
});

const secondTermGradientClass = computed(() => {
    const variants = {
        navigator: 'from-indigo-400 via-purple-400 to-cyan-400',
        emerald: 'from-emerald-300 via-cyan-300 to-indigo-300',
        sunset: 'from-amber-300 via-orange-400 to-rose-400',
        rose: 'from-fuchsia-300 via-pink-400 to-rose-400',
    } as const;

    return variants[props.gradientVariant] ?? variants.navigator;
});

const titleParts = computed(() => {
    const parts = props.title.trim().split(/\s+/);

    if (parts.length <= 1) {
        return {
            first: props.title,
            rest: '',
        };
    }

    return {
        first: `${parts[0]} `,
        rest: parts.slice(1).join(' '),
    };
});
</script>

<style scoped>
.header-wrap {
    margin-bottom: 32px;
}
.text-muted {
    color: var(--muted);
}
</style>

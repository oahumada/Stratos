<template>
    <Link
        :href="href"
        class="glass-card group flex min-h-[240px] cursor-pointer flex-col items-center justify-center gap-5 p-6 text-center"
    >
        <div :class="['icon-glass', iconColor]">
            <v-icon v-if="typeof icon === 'string'" :icon="icon" size="48" />
            <component v-else :is="icon" :size="48" weight="duotone" />
        </div>
        <div class="space-y-2">
            <div
                class="text-xl leading-tight font-black tracking-tight text-white transition-colors group-hover:text-indigo-300"
            >
                {{ title }}
            </div>
            <div class="text-sm leading-relaxed text-white/70">
                {{ description }}
            </div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Component } from 'vue';

interface Props {
    icon: string | Component;
    title: string;
    description: string;
    href?: string;
    iconColor?: string;
}

defineProps<Props>();
</script>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(8px);
}
</style>

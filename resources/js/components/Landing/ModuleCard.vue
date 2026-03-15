<template>
    <StCardGlass
        :as="href ? Link : 'div'"
        :href="href"
        class="group relative flex min-h-[260px] cursor-pointer flex-col items-center justify-center gap-6 p-10! text-center transition-all duration-500"
    >
        <!-- Background Refraction Highlight -->
        <div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/20 to-transparent pointer-events-none" />

        <!-- Contenedor de Icono Principal -->
        <div :class="['relative z-10 flex items-center justify-center p-5 rounded-3xl bg-white/5 border border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.1)] backdrop-blur-md transition-all duration-500 group-hover:scale-110 group-hover:bg-white/10 group-hover:border-white/25', iconColor]">
            <v-icon v-if="typeof icon === 'string'" :icon="icon" size="44" />
            <component v-else :is="icon" :size="44" weight="duotone" />
            
            <!-- Glow effect under the icon -->
            <div class="absolute inset-0 bg-current opacity-0 group-hover:opacity-10 blur-xl transition-opacity duration-500" />
        </div>

        <div class="relative z-10 space-y-2">
            <div
                class="text-xl leading-tight font-black tracking-tight text-white transition-colors group-hover:text-indigo-300"
            >
                {{ title }}
            </div>
            <div class="text-xs leading-relaxed text-white/40 group-hover:text-white/70 transition-colors max-w-[200px]">
                {{ description }}
            </div>
        </div>
    </StCardGlass>
</template>

<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
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
</style>

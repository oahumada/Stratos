<template>
    <div class="header-wrap mb-8 flex items-center gap-6">
        <div class="icon-glass-header">
            <v-icon v-if="typeof icon === 'string'" :icon="icon" size="48" />
            <component v-else :is="icon" :size="48" weight="duotone" />
        </div>
        <div>
            <div class="text-2xl font-black text-white">{{ title }}</div>
            <div class="mt-1 text-base text-white/60">{{ slogan }}</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue';

interface Props {
    title: string;
    slogan: string;
    icon: string | Component;
}

defineProps<Props>();
</script>

<style scoped>
.header-wrap {
    margin-bottom: 32px;
}
.text-muted {
    color: var(--muted);
}
</style>

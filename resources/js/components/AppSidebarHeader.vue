<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import SmartAlertsWidget from '@/components/Intelligence/SmartAlertsWidget.vue';
import LocaleSelector from '@/components/LocaleSelector.vue';
import ThemeSelector from '@/components/ThemeSelector.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();

const currentModule = computed(() => {
    const url = page.url || '';
    if (url.includes('scenario-planning')) return 'scenario_planning';
    if (url.includes('talento-360')) return 'talento_360';
    if (url.includes('mi-stratos')) return 'mi_stratos';
    if (url.includes('people')) return 'people';
    if (url.includes('gap-analysis')) return 'gap_analysis';
    if (url.includes('marketplace')) return 'marketplace';
    if (url.includes('learning')) return 'learning_paths';
    return 'dashboard';
});

const moduleColor = computed(() => {
    const colors = {
        scenario_planning: { text: 'text-fuchsia-500', bg: 'bg-fuchsia-500', shadow: 'rgba(217, 70, 239, 0.6)', shadowLight: 'rgba(217, 70, 239, 0.2)' },
        gap_analysis: { text: 'text-fuchsia-500', bg: 'bg-fuchsia-500', shadow: 'rgba(217, 70, 239, 0.6)', shadowLight: 'rgba(217, 70, 239, 0.2)' },
        talento_360: { text: 'text-emerald-500', bg: 'bg-emerald-500', shadow: 'rgba(16, 185, 129, 0.6)', shadowLight: 'rgba(16, 185, 129, 0.2)' },
        learning_paths: { text: 'text-emerald-500', bg: 'bg-emerald-500', shadow: 'rgba(16, 185, 129, 0.6)', shadowLight: 'rgba(16, 185, 129, 0.2)' },
        mi_stratos: { text: 'text-cyan-500', bg: 'bg-cyan-500', shadow: 'rgba(6, 182, 212, 0.6)', shadowLight: 'rgba(6, 182, 212, 0.2)' },
        marketplace: { text: 'text-amber-500', bg: 'bg-amber-500', shadow: 'rgba(245, 158, 11, 0.6)', shadowLight: 'rgba(245, 158, 11, 0.2)' },
        people: { text: 'text-indigo-500', bg: 'bg-indigo-500', shadow: 'rgba(99, 102, 241, 0.6)', shadowLight: 'rgba(99, 102, 241, 0.2)' },
        dashboard: { text: 'text-indigo-500', bg: 'bg-indigo-500', shadow: 'rgba(99, 102, 241, 0.6)', shadowLight: 'rgba(99, 102, 241, 0.2)' },
    };
    return colors[currentModule.value as keyof typeof colors] || colors.dashboard;
});
</script>

<template>
    <header
        class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-2 border-b border-white/10 bg-linear-to-b from-slate-950/90 to-slate-950/50 px-6 backdrop-blur-xl transition-all duration-300 md:px-4"
    >
        <!-- Indicator Light (Top) -->
        <div
            class="animate-soft-pulse absolute top-0 left-0 h-px w-full bg-linear-to-r from-transparent via-current to-transparent transition-colors duration-1000"
            :class="moduleColor.text"
            :style="{ boxShadow: `0 0 20px ${moduleColor.shadow}` }"
        ></div>

        <!-- Content Glow Divider (Bottom) -->
        <div
            class="animate-soft-pulse-delayed absolute bottom-0 left-0 h-px w-full bg-linear-to-r from-transparent via-current to-transparent opacity-30 transition-colors duration-1000"
            :class="moduleColor.text"
            :style="{ boxShadow: `0 1px 10px ${moduleColor.shadowLight}` }"
        ></div>

        <div class="flex flex-1 items-center gap-2">
            <SidebarTrigger
                class="-ml-1 text-white transition-all hover:scale-110 hover:bg-white/10"
            />
            <div class="mx-3 hidden h-6 w-px bg-white/20 md:block"></div>
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs
                    :breadcrumbs="breadcrumbs"
                    class="text-sm font-bold tracking-tight text-white"
                />
            </template>
        </div>

        <div class="flex items-center gap-3">
            <SmartAlertsWidget />
            <div class="mx-1 h-6 w-px bg-white/20"></div>
            <div
                class="flex items-center gap-2 rounded-2xl bg-white/5 p-1 ring-1 ring-white/10"
            >
                <ThemeSelector />
                <LocaleSelector />
            </div>
        </div>
    </header>
</template>

<style scoped>
@keyframes soft-pulse {
    0%,
    100% {
        opacity: 0.6;
        filter: blur(1px);
    }
    50% {
        opacity: 1;
        filter: blur(2px);
    }
}

.animate-soft-pulse {
    animation: soft-pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-soft-pulse-delayed {
    animation: soft-pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    animation-delay: 2s;
}
</style>

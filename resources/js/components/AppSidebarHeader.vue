<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import SmartAlertsWidget from '@/components/Intelligence/SmartAlertsWidget.vue';
import LocaleSelector from '@/components/LocaleSelector.vue';
import ThemeSelector from '@/components/ThemeSelector.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-2 border-b border-white/10 bg-linear-to-b from-slate-950/90 to-slate-950/50 px-6 backdrop-blur-xl transition-all duration-300 md:px-4"
    >
        <!-- Indicator Light -->
        <div
            class="absolute top-0 left-0 h-px w-full bg-linear-to-r from-transparent via-indigo-500/80 to-transparent shadow-[0_0_20px_rgba(99,102,241,0.6)]"
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

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
        class="flex h-16 shrink-0 items-center gap-2 border-b border-white/5 bg-transparent px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex flex-1 items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="flex items-center gap-2">
            <SmartAlertsWidget />
            <ThemeSelector />
            <LocaleSelector />
        </div>
    </header>
</template>

<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import StratosGuideWidget from '@/components/StratosGuideWidget.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const hasUser = computed(() => !!page.props?.auth?.user);

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
</script>

<template>
    <AppShell variant="sidebar" class="st-glass-container">
        <template v-if="hasUser">
            <AppSidebar />
        </template>

        <AppContent variant="sidebar" class="overflow-x-hidden bg-transparent!">
            <template v-if="hasUser">
                <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            </template>
            <slot />
        </AppContent>

        <!-- Stratos Guide Floating Widget -->
        <StratosGuideWidget v-if="hasUser" :current-module="currentModule" />
    </AppShell>
</template>

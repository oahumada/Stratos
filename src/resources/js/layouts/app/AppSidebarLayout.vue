<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
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
</script>

<template>
    <AppShell variant="sidebar">
        <template v-if="hasUser">
            <AppSidebar />
        </template>

        <AppContent variant="sidebar" class="overflow-x-hidden">
            <template v-if="hasUser">
                <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            </template>
            <slot />
        </AppContent>
    </AppShell>
</template>

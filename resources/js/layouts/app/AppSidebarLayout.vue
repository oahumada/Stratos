<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import TicketReportModal from '@/components/Quality/TicketReportModal.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StratosGuideWidget from '@/components/StratosGuideWidget.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ShieldAlert } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const hasUser = computed(() => !!page.props?.auth?.user);
const showTicketModal = ref(false);

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
    <AppShell
        variant="sidebar"
        class="st-glass-container relative min-h-screen overflow-x-hidden bg-[#020617] selection:bg-indigo-500/30"
    >
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <div
                class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-indigo-500/10 blur-[120px]"
            ></div>
            <div
                class="absolute top-[20%] -right-[10%] h-[35%] w-[35%] rounded-full bg-purple-500/10 blur-[120px]"
            ></div>
            <div
                class="absolute -bottom-[10%] left-[20%] h-[30%] w-[30%] rounded-full bg-blue-500/10 blur-[120px]"
            ></div>
        </div>

        <template v-if="hasUser">
            <AppSidebar class="relative z-10" />
        </template>

        <AppContent
            variant="sidebar"
            class="relative z-10 overflow-x-hidden bg-transparent!"
        >
            <template v-if="hasUser">
                <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            </template>
            <slot />
        </AppContent>

        <!-- Global QA Reporting Action -->
        <div v-if="hasUser" class="fixed right-6 bottom-22 z-50">
            <StButtonGlass
                variant="glass"
                size="md"
                class="rounded-full border-white/20 p-3! shadow-xl backdrop-blur-md transition-transform hover:scale-110"
                @click="showTicketModal = true"
                title="Reportar Error / Sugerencia"
            >
                <ShieldAlert class="h-7 w-7 text-amber-400" />
            </StButtonGlass>
        </div>

        <!-- Stratos Guide Floating Widget -->
        <StratosGuideWidget v-if="hasUser" :current-module="currentModule" />

        <!-- QA Modal -->
        <TicketReportModal
            :show="showTicketModal"
            @close="showTicketModal = false"
        />
    </AppShell>
</template>

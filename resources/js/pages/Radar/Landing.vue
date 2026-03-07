<template>
    <div class="radar-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.radar.title')"
            :slogan="t('landings.radar.slogan')"
            :icon="PhTarget"
        />
        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="mod in modules" :key="mod.title">
                <ModuleCard
                    :title="mod.title"
                    :description="mod.description"
                    :icon="mod.icon"
                    :href="mod.href"
                    :iconColor="mod.iconColor"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import GroupHeader from '@/components/Landing/GroupHeader.vue';
import ModuleCard from '@/components/Landing/ModuleCard.vue';
import {
    PhChartLineUp,
    PhClipboardText,
    PhShieldCheck,
    PhTarget,
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const modules = computed(() => [
    {
        title: t('landings.radar.modules.strategic_scenario_list.title'),
        description: t(
            'landings.radar.modules.strategic_scenario_list.description',
        ),
        icon: PhClipboardText,
        href: '/scenario-planning',
        iconColor: 'text-indigo-300',
    },
    {
        title: t('landings.radar.modules.executive_dashboard.title'),
        description: t(
            'landings.radar.modules.executive_dashboard.description',
        ),
        icon: PhShieldCheck,
        href: '/dashboard/analytics',
        iconColor: 'text-amber-300',
    },
    {
        title: t('landings.radar.modules.gap_analysis_engine.title'),
        description: t(
            'landings.radar.modules.gap_analysis_engine.description',
        ),
        icon: PhChartLineUp,
        href: '/gap-analysis',
        iconColor: 'text-fuchsia-300',
    },
    {
        title: t('landings.radar.modules.investor_dashboard.title'),
        description: t('landings.radar.modules.investor_dashboard.description'),
        icon: PhChartLineUp,
        href: '/dashboard/investor',
        iconColor: 'text-cyan-300',
    },
]);
</script>

<style scoped>
.radar-landing {
    padding: 32px 0;
}
</style>

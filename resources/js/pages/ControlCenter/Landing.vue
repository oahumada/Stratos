<template>
    <div class="controlcenter-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.control_center.title')"
            :slogan="t('landings.control_center.slogan')"
            :icon="PhGear"
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
    PhGear,
    PhRobot,
    PhShieldCheck,
    PhTarget,
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const modules = computed(() => [
    {
        title: t('landings.control_center.modules.rbac_manager.title'),
        description: t(
            'landings.control_center.modules.rbac_manager.description',
        ),
        icon: PhShieldCheck,
        href: '/settings/rbac',
        iconColor: 'text-indigo-300',
    },
    {
        title: t('landings.control_center.modules.ai_agent_supervisor.title'),
        description: t(
            'landings.control_center.modules.ai_agent_supervisor.description',
        ),
        icon: PhRobot,
        href: '/talent-agents',
        iconColor: 'text-amber-300',
    },
    {
        title: t('landings.control_center.modules.quality_sentinel.title'),
        description: t(
            'landings.control_center.modules.quality_sentinel.description',
        ),
        icon: PhTarget,
        href: '/quality-hub',
        iconColor: 'text-pink-300',
    },
    {
        title: t('landings.control_center.modules.ragas_neural_dash.title'),
        description: t(
            'landings.control_center.modules.ragas_neural_dash.description',
        ),
        icon: PhChartLineUp,
        href: '/quality/ragas-metrics',
        iconColor: 'text-cyan-300',
    },
]);
</script>

<style scoped>
.controlcenter-landing {
    padding: 32px 0;
}
</style>

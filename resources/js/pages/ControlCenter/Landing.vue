<template>
    <div class="controlcenter-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.control_center.title')"
            :slogan="t('landings.control_center.slogan')"
            :icon="PhGear"
        />
        <div
            class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:gap-7 xl:grid-cols-3"
        >
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
    PhBrain,
    PhCertificate,
    PhChartLineUp,
    PhCheckCircle,
    PhEye,
    PhGear,
    PhPlug,
    PhRobot,
    PhRocketLaunch,
    PhScroll,
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
        title: t(
            'landings.control_center.modules.intelligence_monitoring_hub.title',
        ),
        description: t(
            'landings.control_center.modules.intelligence_monitoring_hub.description',
        ),
        icon: PhEye,
        href: '/intelligence/monitoring-hub',
        iconColor: 'text-cyan-300',
    },
    {
        title: t('landings.control_center.modules.stratos_compliance.title'),
        description: t(
            'landings.control_center.modules.stratos_compliance.description',
        ),
        icon: PhCertificate,
        href: '/quality/compliance-audit',
        iconColor: 'text-emerald-300',
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
    {
        title: t('landings.control_center.modules.comando_360.title'),
        description: t(
            'landings.control_center.modules.comando_360.description',
        ),
        icon: PhRocketLaunch,
        href: '/talento360/comando',
        iconColor: 'text-purple-300',
    },
    {
        title: t('landings.control_center.modules.comando_px.title'),
        description: t(
            'landings.control_center.modules.comando_px.description',
        ),
        icon: PhBrain,
        href: '/people-experience/comando',
        iconColor: 'text-rose-300',
    },
    {
        title: t('landings.control_center.modules.cultural_blueprint.title'),
        description: t(
            'landings.control_center.modules.cultural_blueprint.description',
        ),
        icon: PhScroll,
        href: '/controlcenter/culture',
        iconColor: 'text-fuchsia-300',
    },
    {
        title: t('landings.control_center.modules.verification_hub.title'),
        description: t(
            'landings.control_center.modules.verification_hub.description',
        ),
        icon: PhCheckCircle,
        href: '/deployment/verification-hub',
        iconColor: 'text-teal-300',
    },
    {
        title: t(
            'landings.control_center.modules.verification_deployment.title',
        ),
        description: t(
            'landings.control_center.modules.verification_deployment.description',
        ),
        icon: PhPlug,
        href: '/deployment/verification-config',
        iconColor: 'text-blue-300',
    },
    {
        title: 'Admin Operations',
        description:
            'Manage system operations: backfill metrics, generate scenarios, cleanup data, rebuild indexes',
        icon: PhGear,
        href: '/admin/operations',
        iconColor: 'text-slate-300',
    },
]);
</script>

<style scoped>
.controlcenter-landing {
    padding: 32px 0;
}
</style>

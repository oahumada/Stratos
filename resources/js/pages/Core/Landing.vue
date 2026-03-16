<template>
    <div class="core-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.core.title')"
            :slogan="t('landings.core.slogan')"
            :icon="PhShieldCheck"
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
    PhCompass,
    PhShieldCheck,
    PhTarget,
    PhUsersThree,
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const modules = computed(() => [
    {
        title: t('landings.core.modules.people_inventory.title'),
        description: t('landings.core.modules.people_inventory.description'),
        icon: PhUsersThree,
        href: '/people',
        iconColor: 'text-indigo-300',
    },
    {
        title: t('landings.core.modules.role_catalog.title'),
        description: t('landings.core.modules.role_catalog.description'),
        icon: PhShieldCheck,
        href: '/roles',
        iconColor: 'text-amber-300',
    },
    {
        title: t('landings.core.modules.competency_dictionary.title'),
        description: t('landings.core.modules.competency_dictionary.description'),
        icon: PhClipboardText,
        href: '/competencies',
        iconColor: 'text-pink-300',
    },
    {
        title: t('landings.core.modules.skills_matrix.title'),
        description: t('landings.core.modules.skills_matrix.description'),
        icon: PhChartLineUp,
        href: '/skills',
        iconColor: 'text-cyan-300',
    },
    {
        title: t('landings.core.modules.organization_flow.title'),
        description: t('landings.core.modules.organization_flow.description'),
        icon: PhCompass,
        href: '/departments/org-chart',
        iconColor: 'text-emerald-300',
    },
    {
        title: t('landings.core.modules.stratos_map.title'),
        description: t('landings.core.modules.stratos_map.description'),
        icon: PhTarget,
        href: '/talento360/map',
        iconColor: 'text-fuchsia-300',
    },
]);
</script>

<style scoped>
.core-landing {
    padding: 32px 0;
}
</style>

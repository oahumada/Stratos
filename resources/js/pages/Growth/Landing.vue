<template>
    <div class="growth-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.growth.title')"
            :slogan="t('landings.growth.slogan')"
            :icon="PhTrendUp"
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
    PhTrendUp,
    PhUserFocus,
    PhUsersThree,
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const modules = computed(() => [
    {
        title: t('landings.growth.modules.stratos_navigator.title'),
        description: t('landings.growth.modules.stratos_navigator.description'),
        icon: PhCompass,
        href: '/learning-paths',
        iconColor: 'text-indigo-300',
    },
    {
        title: t('landings.growth.modules.stratos_360.title'),
        description: t('landings.growth.modules.stratos_360.description'),
        icon: PhClipboardText,
        href: '/talento360',
        iconColor: 'text-pink-300',
    },
    {
        title: t('landings.growth.modules.commander_360.title'),
        description: t('landings.growth.modules.commander_360.description'),
        icon: PhShieldCheck,
        href: '/talento360/comando',
        iconColor: 'text-cyan-300',
    },
    {
        title: t('landings.growth.modules.social_learning_hub.title'),
        description: t(
            'landings.growth.modules.social_learning_hub.description',
        ),
        icon: PhUsersThree,
        href: '/people-experience',
        iconColor: 'text-emerald-300',
    },
    {
        title: t('landings.growth.modules.mentoring.title'),
        description: t('landings.growth.modules.mentoring.description'),
        icon: PhUserFocus,
        href: '/mentoring',
        iconColor: 'text-amber-300',
    },
    {
        title: t('landings.growth.modules.opportunity_marketplace.title'),
        description: t(
            'landings.growth.modules.opportunity_marketplace.description',
        ),
        icon: PhChartLineUp,
        href: '/marketplace',
        iconColor: 'text-pink-300',
    },
]);
</script>

<style scoped>
.growth-landing {
    padding: 32px 0;
}
</style>

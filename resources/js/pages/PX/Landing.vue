<template>
    <div class="px-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.px.title')"
            :slogan="t('landings.px.slogan')"
            :icon="PhSmiley"
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
import { PhFire, PhHandshake, PhSmiley, PhUser } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const modules = computed(() => [
    {
        title: t('landings.px.modules.mi_stratos.title'),
        description: t('landings.px.modules.mi_stratos.description'),
        icon: PhUser,
        href: '/mi-stratos',
        iconColor: 'text-indigo-300',
    },
    {
        title: t('landings.px.modules.comando_px.title'),
        description: t('landings.px.modules.comando_px.description'),
        icon: PhFire,
        href: '/people-experience/comando',
        iconColor: 'text-fuchsia-300',
    },
    {
        title: t('landings.px.modules.stratos_match.title'),
        description: t('landings.px.modules.stratos_match.description'),
        icon: PhHandshake,
        href: '/marketplace',
        iconColor: 'text-teal-300',
    },
]);
</script>

<style scoped>
.px-landing {
    padding: 32px 0;
}
</style>

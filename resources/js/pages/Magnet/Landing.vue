<template>
    <div class="magnet-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.magnet.title')"
            :slogan="t('landings.magnet.slogan')"
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
import { PhCompass, PhTarget, PhUsersThree } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const modules = computed(() => [
    {
        title: t('landings.magnet.modules.magnet_home.title'),
        description: t('landings.magnet.modules.magnet_home.description'),
        icon: PhCompass,
        href: '/career/tenant',
        iconColor: 'text-indigo-300',
    },
    {
        title: t('landings.magnet.modules.candidate_portal.title'),
        description: t('landings.magnet.modules.candidate_portal.description'),
        icon: PhUsersThree,
        href: '/candidate-portal/1',
        iconColor: 'text-pink-300',
    },
]);
</script>

<style scoped>
.magnet-landing {
    padding: 32px 0;
}
</style>

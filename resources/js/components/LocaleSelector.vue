<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import StButtonGlass from './StButtonGlass.vue';

const { locale } = useI18n();

const toggleLocale = () => {
    locale.value = locale.value === 'en' ? 'es' : 'en';
    localStorage.setItem('locale', locale.value);
};
</script>

<template>
    <StButtonGlass
        variant="ghost"
        size="sm"
        icon="mdi-translate"
        @click="toggleLocale"
        class="ml-2 px-2!"
        :title="locale === 'en' ? 'Switch to Spanish' : 'Switch to English'"
    >
        <span class="text-xs font-bold uppercase">{{ locale }}</span>
    </StButtonGlass>
</template>

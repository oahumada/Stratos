<script setup lang="ts">
import { useTheme, type ThemeName } from '@/composables/useTheme';

const { currentTheme, setTheme, themeOptions } = useTheme();
</script>

<template>
    <v-menu>
        <template v-slot:activator="{ props }">
            <v-btn
                v-bind="props"
                icon="mdi-palette"
                variant="text"
                title="Cambiar tema de colores"
            />
        </template>
        <v-card min-width="280">
            <v-card-title class="text-subtitle-1 font-weight-bold">
                <v-icon class="mr-2">mdi-palette</v-icon>
                Paleta de Colores
            </v-card-title>
            <v-divider />
            <v-list>
                <v-list-item
                    v-for="theme in themeOptions"
                    :key="theme.name"
                    @click="setTheme(theme.name as ThemeName)"
                    :active="currentTheme === theme.name"
                    class="theme-item"
                >
                    <template v-slot:prepend>
                        <div class="theme-preview">
                            <div
                                class="color-square"
                                :style="{
                                    backgroundColor: theme.colors.primary,
                                }"
                            />
                            <div
                                class="color-square"
                                :style="{
                                    backgroundColor: theme.colors.secondary,
                                }"
                            />
                        </div>
                    </template>
                    <v-list-item-title class="font-weight-medium">
                        {{ theme.label }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        {{ theme.description }}
                    </v-list-item-subtitle>
                    <template v-slot:append v-if="currentTheme === theme.name">
                        <v-icon color="primary">mdi-check-circle</v-icon>
                    </template>
                </v-list-item>
            </v-list>
        </v-card>
    </v-menu>
</template>

<style scoped>
.theme-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.theme-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

.theme-preview {
    display: flex;
    gap: 4px;
    margin-right: 8px;
}

.color-square {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
</style>

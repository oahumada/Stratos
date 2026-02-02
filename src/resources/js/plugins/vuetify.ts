import { themeOptions } from '@/composables/useTheme';
import '@mdi/font/css/materialdesignicons.css';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { aliases, mdi } from 'vuetify/iconsets/mdi';
import 'vuetify/styles';

// Fuente única de paletas: construimos los temas de Vuetify a partir de themeOptions
const basePalette = {
    accent: '#64b5f6',
    error: '#f44336',
    info: '#03a9f4',
    success: '#4caf50',
    warning: '#ff9800',
    background: '#f5f7fa',
    surface: '#ffffff',
};

const themes = themeOptions.reduce<Record<string, any>>((acc, theme) => {
    acc[theme.name] = {
        dark: false,
        colors: {
            ...basePalette,
            primary: theme.colors.primary,
            secondary: theme.colors.secondary,
        },
    };
    return acc;
}, {});

const defaultTheme = localStorage.getItem('app-theme') || 'purple';

export default createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
    theme: {
        defaultTheme,
        themes,
    },
});

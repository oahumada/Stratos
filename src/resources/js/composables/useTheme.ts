import { ref, watch } from 'vue';
import { useTheme as useVuetifyTheme } from 'vuetify';

export type ThemeName = 'purple' | 'pastel' | 'green' | 'blue';

export interface ThemeOption {
    name: ThemeName;
    label: string;
    description: string;
    colors: {
        primary: string;
        secondary: string;
    };
}

export const themeOptions: ThemeOption[] = [
    {
        name: 'purple',
        label: 'Morado Moderno',
        description: 'Gradientes púrpuras elegantes',
        colors: {
            primary: '#596eceff',
            secondary: '#52297aff',
        },
    },
    {
        name: 'pastel',
        label: 'Tonos Pastel',
        description: 'Suaves y delicados',
        colors: {
            primary: '#ffb6c1',
            secondary: '#a35a5aff',
        },
    },
    {
        name: 'green',
        label: 'Verde Natural',
        description: 'Fresco y profesional',
        colors: {
            primary: '#4caf50',
            secondary: '#66bb6a',
        },
    },
    {
        name: 'blue',
        label: 'Azul Corporativo',
        description: 'Clásico y confiable',
        colors: {
            primary: '#2196f3',
            secondary: '#135388ff',
        },
    },
];

export function useTheme() {
    const vuetifyTheme = useVuetifyTheme();
    const currentTheme = ref<ThemeName>(
        (localStorage.getItem('app-theme') as ThemeName) || 'purple'
    );

    const setTheme = (themeName: ThemeName) => {
        currentTheme.value = themeName;
        vuetifyTheme.global.name.value = themeName;
        localStorage.setItem('app-theme', themeName);
    };

    return {
        currentTheme,
        setTheme,
        themeOptions,
    };
}

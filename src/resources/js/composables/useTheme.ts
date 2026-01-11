import { ref } from 'vue';
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
            primary: '#061a70ff',
            secondary: '#9d78c2ff',
        },
    },
    {
        name: 'pastel',
        label: 'Tonos Pastel',
        description: 'Suaves y delicados',
        colors: {
            primary: '#7a1e2cff',
            secondary: '#ce7b7bff',
        },
    },
    {
        name: 'green',
        label: 'Verde Natural',
        description: 'Fresco y profesional',
        colors: {
            primary: '#0d9212ff',
            secondary: '#76d47bff',
        },
    },
    {
        name: 'blue',
        label: 'Azul Corporativo',
        description: 'Clásico y confiable',
        colors: {
            primary: '#081f30ff',
            secondary: '#7da1beff',
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

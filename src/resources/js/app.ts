import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import VueApexCharts from 'vue3-apexcharts';
import AppLayout from './layouts/AppLayout.vue';
import vuetify from './plugins/vuetify';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        );

        // Default layout fallback for pages that do not declare one
        page.default.layout = page.default.layout || AppLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(VueApexCharts)
            .use(vuetify)
            .mount(el);
    },
    progress: {
        color: '#1ee61eff',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

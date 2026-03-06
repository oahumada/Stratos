import { usePage } from '@inertiajs/vue3';
import { defineStore } from 'pinia';

export const useTenantStore = defineStore('tenant', {
    state: () => ({
        // Initialize from Inertia shared props
        activeModules: [] as string[],
    }),
    actions: {
        initFromProps() {
            const pageProps = usePage().props;
            // @ts-ignore
            if (pageProps?.auth?.active_modules) {
                // @ts-ignore
                this.activeModules = pageProps.auth.active_modules;
            } else {
                this.activeModules = ['core']; // Fallback
            }
        },
        hasModule(moduleAlias: string): boolean {
            if (moduleAlias === 'core') return true;
            return this.activeModules.includes(moduleAlias);
        },
    },
});

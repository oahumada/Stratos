// @vitest-environment jsdom
import { shallowMount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@/composables/usePermissions', () => ({
    usePermissions: () => ({
        can: () => true,
        hasRole: (...roles: string[]) =>
            roles.includes('admin') || roles.includes('hr_leader'),
    }),
}));

vi.mock('@/stores/tenantStore', () => ({
    useTenantStore: () => ({
        initFromProps: () => {},
        hasModule: () => true,
    }),
}));

import AppSidebar from '@/components/AppSidebar.vue';

describe('AppSidebar', () => {
    it('includes Stratos Compliance in main navigation for governance roles', () => {
        const wrapper = shallowMount(AppSidebar, {
            global: {
                stubs: {
                    AppLogo: true,
                    Sidebar: { template: '<div><slot /></div>' },
                    SidebarContent: { template: '<div><slot /></div>' },
                    SidebarFooter: { template: '<div><slot /></div>' },
                    SidebarHeader: { template: '<div><slot /></div>' },
                    SidebarMenu: { template: '<div><slot /></div>' },
                    SidebarMenuButton: {
                        template: '<button><slot /></button>',
                    },
                    SidebarMenuItem: { template: '<div><slot /></div>' },
                    NavUser: true,
                    NavFooter: true,
                    Link: true,
                    NavMain: {
                        props: ['items'],
                        template:
                            '<div class="nav-main-stub"><div v-for="item in items" :key="item.title" class="nav-item" :data-title="item.title" :data-href="item.href">{{ item.title }}</div></div>',
                    },
                },
            },
        });

        const complianceItem = wrapper
            .findAll('.nav-item')
            .find(
                (item) =>
                    item.attributes('data-title') === 'Stratos Compliance',
            );

        expect(complianceItem).toBeTruthy();
        expect(complianceItem?.attributes('data-href')).toBe(
            '/quality/compliance-audit',
        );
    });
});

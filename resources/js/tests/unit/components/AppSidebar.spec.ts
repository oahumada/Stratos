// @vitest-environment jsdom
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import { defineComponent, h } from 'vue';

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

vi.mock('@/components/ui/sidebar', () => {
    const passthrough = defineComponent({
        setup(_, { slots }) {
            return () => h('div', {}, slots.default ? slots.default() : []);
        },
    });

    return {
        Sidebar: passthrough,
        SidebarContent: passthrough,
        SidebarFooter: passthrough,
        SidebarHeader: passthrough,
        SidebarMenu: passthrough,
        SidebarMenuButton: passthrough,
        SidebarMenuItem: passthrough,
    };
});

vi.mock('@/components/NavMain.vue', () => ({
    default: defineComponent({
        props: ['items'],
        setup(props: any) {
            return () =>
                h(
                    'div',
                    { class: 'nav-main-stub' },
                    (props.items || []).map((item: any) =>
                        h(
                            'div',
                            {
                                class: 'nav-item',
                                'data-title': item.title,
                                'data-href': item.href,
                                key: item.title,
                            },
                            item.title,
                        ),
                    ),
                );
        },
    }),
}));

vi.mock('@/components/NavFooter.vue', () => ({
    default: defineComponent({
        setup() {
            return () => h('div', { class: 'nav-footer-stub' });
        },
    }),
}));

vi.mock('@/components/NavUser.vue', () => ({
    default: defineComponent({
        setup() {
            return () => h('div', { class: 'nav-user-stub' });
        },
    }),
}));

import AppSidebar from '@/components/AppSidebar.vue';

describe('AppSidebar', () => {
    it('includes Stratos Compliance in main navigation for governance roles', () => {
        const wrapper = mount(AppSidebar, {
            global: {
                stubs: {
                    AppLogo: true,
                    Link: true,
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

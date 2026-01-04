<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Star, User } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { defineComponent, h } from 'vue';
import { VIcon } from 'vuetify/components';

const SkillsIcon = defineComponent(() => () => h(VIcon, { icon: 'mdi-star-circle', size: 20 }));
const GapAnalysisIcon = defineComponent(() => () => h(VIcon, { icon: 'mdi-chart-line', size: 20 }));
const MarketplaceIcon = defineComponent(() => () => h(VIcon, { icon: 'mdi-briefcase-search', size: 20 }));
const WorkforcePlanningIcon = defineComponent(() => () => h(VIcon, { icon: 'mdi-chart-timeline-variant', size: 20 }));

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard/analytics',
        icon: LayoutGrid,
    },
    {
        title: 'People',
        href: '/people',
        icon: User,
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: BookOpen,
    },
    {
        title: 'Skills',
        href: '/skills',
        icon: SkillsIcon,
    },
    {
        title: 'Gap Analysis',
        href: '/gap-analysis',
        icon: GapAnalysisIcon,
    },
    {
        title: 'Learning Paths',
        href: '/learning-paths',
        icon: Star,
    },
    {
        title: 'Marketplace',
        href: '/marketplace',
        icon: MarketplaceIcon,
    },
    {
        title: 'Workforce Planning',
        href: '/workforce-planning',
        icon: WorkforcePlanningIcon,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentación',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Mesa de ayuda',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: Star,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

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
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Star, User } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
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
        icon: Star,
    },
    {
        title: 'Gap Analysis',
        href: '/gap-analysis',
        icon: Star,
    },
    {
        title: 'Learning Paths',
        href: '/learning-paths',
        icon: Star,
    },
    {
        title: 'Marketplace',
        href: '/marketplace',
        icon: Star,
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

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
import { defineComponent, h } from 'vue';
import { VIcon } from 'vuetify/components';
import AppLogo from './AppLogo.vue';

const SkillsIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-star-circle', size: 20 }),
);
const GapAnalysisIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-chart-line', size: 20 }),
);
const MarketplaceIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-briefcase-search', size: 20 }),
);
const ScenarioPlanningIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-chart-timeline-variant', size: 20 }),
);
const PeopleExperienceIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-account-group', size: 20 }),
);
const Talento360Icon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-account-convert', size: 20 }),
);
const TalentAgentsIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-robot-vacuum-variant', size: 20 }),
);
const CerberoMapIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-transit-connection-variant', size: 20 }),
);

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
        title: 'Mapa Cerbero',
        href: '/talento360/relationships',
        icon: CerberoMapIcon,
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
        title: 'Strategic Talent Scenarios',
        href: '/scenario-planning',
        icon: ScenarioPlanningIcon,
    },
    {
        title: 'Talento 360°',
        href: '/talento360',
        icon: Talento360Icon,
    },
    {
        title: 'Comando 360',
        href: '/talento360/comando',
        icon: defineComponent(
            () => () => h(VIcon, { icon: 'mdi-rocket-launch', size: 20 }),
        ),
    },
    {
        title: 'People Experience',
        href: '/people-experience',
        icon: PeopleExperienceIcon,
    },
    {
        title: 'Talent Agents',
        href: '/talent-agents',
        icon: TalentAgentsIcon,
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
</template>

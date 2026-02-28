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
import { usePermissions } from '@/composables/usePermissions';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Star, User } from 'lucide-vue-next';
import { computed, defineComponent, h } from 'vue';
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
const MiStratosIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-account-star', size: 20 }),
);
const InvestorRadarIcon = defineComponent(
    () => () => h(VIcon, { icon: 'mdi-radar', size: 20 }),
);

const { can, hasRole } = usePermissions();

const allNavItems: NavItem[] = [
    // Visible for everyone
    {
        title: 'Mi Stratos',
        href: '/mi-stratos',
        icon: MiStratosIcon,
    },
    {
        title: 'Dashboard',
        href: '/dashboard/analytics',
        icon: LayoutGrid,
    },
    {
        title: 'Investor Radar',
        href: '/dashboard/investor',
        icon: InvestorRadarIcon,
        requiredRole: ['admin', 'hr_leader', 'observer'],
    },
    // People management — requires people.view
    {
        title: 'People',
        href: '/people',
        icon: User,
        requiredPermission: 'people.view',
    },
    // Roles — requires roles.view
    {
        title: 'Roles',
        href: '/roles',
        icon: BookOpen,
        requiredPermission: 'roles.view',
    },
    // Competencies — requires competencies.view
    {
        title: 'Competencies',
        href: '/competencies',
        icon: defineComponent(
            () => () => h(VIcon, { icon: 'mdi-puzzle', size: 20 }),
        ),
        requiredPermission: 'competencies.view',
    },
    // Skills — requires competencies.view
    {
        title: 'Skills',
        href: '/skills',
        icon: SkillsIcon,
        requiredPermission: 'competencies.view',
    },
    // Cerbero Map — requires assessments.view
    {
        title: 'Mapa Cerbero',
        href: '/talento360/relationships',
        icon: CerberoMapIcon,
        requiredPermission: 'assessments.view',
    },
    // Gap Analysis — requires people.view
    {
        title: 'Gap Analysis',
        href: '/gap-analysis',
        icon: GapAnalysisIcon,
        requiredPermission: 'people.view',
    },
    // Learning Paths — requires people.view
    {
        title: 'Learning Paths',
        href: '/learning-paths',
        icon: Star,
        requiredPermission: 'people.view',
    },
    // Marketplace — requires people.view
    {
        title: 'Marketplace',
        href: '/marketplace',
        icon: MarketplaceIcon,
        requiredPermission: 'people.view',
    },
    // Strategic Scenarios — requires scenarios.view
    {
        title: 'Strategic Talent Scenarios',
        href: '/scenario-planning',
        icon: ScenarioPlanningIcon,
        requiredPermission: 'scenarios.view',
    },
    // Talento 360 — requires assessments.view
    {
        title: 'Talento 360°',
        href: '/talento360',
        icon: Talento360Icon,
        requiredPermission: 'assessments.view',
    },
    // Comando 360 — admin/hr_leader only
    {
        title: 'Comando 360',
        href: '/talento360/comando',
        icon: defineComponent(
            () => () => h(VIcon, { icon: 'mdi-rocket-launch', size: 20 }),
        ),
        requiredRole: ['admin', 'hr_leader'],
    },
    // People Experience — requires people.view
    {
        title: 'People Experience',
        href: '/people-experience',
        icon: PeopleExperienceIcon,
        requiredPermission: 'people.view',
    },
    // Comando PX — admin/hr_leader only
    {
        title: 'Comando PX',
        href: '/people-experience/comando',
        icon: defineComponent(
            () => () => h(VIcon, { icon: 'mdi-brain', size: 20 }),
        ),
        requiredRole: ['admin', 'hr_leader'],
    },
    // Talent Agents — requires agents.view
    {
        title: 'Talent Agents',
        href: '/talent-agents',
        icon: TalentAgentsIcon,
        requiredPermission: 'agents.view',
    },
];

/**
 * Filter nav items based on RBAC permissions and roles.
 */
const mainNavItems = computed<NavItem[]>(() => {
    return allNavItems.filter((item) => {
        // No restriction? Always show
        if (!item.requiredPermission && !item.requiredRole) return true;

        // Check permission
        if (item.requiredPermission && !can(item.requiredPermission))
            return false;

        // Check role
        if (item.requiredRole && !hasRole(...item.requiredRole)) return false;

        return true;
    });
});

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

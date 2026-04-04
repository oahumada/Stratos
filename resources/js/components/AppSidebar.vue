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
import { useTenantStore } from '@/stores/tenantStore';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    PhBell,
    PhBookOpen,
    PhBrain,
    PhChalkboardTeacher,
    PhChartBar,
    PhChartLineUp,
    PhChartPieSlice,
    PhChatCircle,
    PhClipboardText,
    PhEye,
    PhGear,
    PhGraph,
    PhHouse,
    PhLockKey,
    PhMagnifyingGlass,
    PhMapTrifold,
    PhPath,
    PhPencilLine,
    PhPuzzlePiece,
    PhQuestion,
    PhRobot,
    PhRocketLaunch,
    PhScroll,
    PhShieldCheck,
    PhSquaresFour,
    PhStar,
    PhSword,
    PhTreeStructure,
    PhUser,
    PhUsers,
    PhUsersFour,
} from '@phosphor-icons/vue';
import { computed, defineComponent, h } from 'vue';

// Pre-define standard Phosphor wrappers for complex conditionals
const SkillsIcon = defineComponent(() => () => h(PhStar, { size: 20 }));
const GapAnalysisIcon = defineComponent(
    () => () => h(PhChartLineUp, { size: 20 }),
);
const MarketplaceIcon = defineComponent(
    () => () => h(PhMagnifyingGlass, { size: 20 }),
);
const ScenarioPlanningIcon = defineComponent(
    () => () => h(PhChartPieSlice, { size: 20 }),
);
const PeopleExperienceIcon = defineComponent(
    () => () => h(PhUsersFour, { size: 20 }),
);
const Talento360Icon = defineComponent(() => () => h(PhUsers, { size: 20 }));
const TalentAgentsIcon = defineComponent(() => () => h(PhRobot, { size: 20 }));
const CerberoMapIcon = defineComponent(() => () => h(PhGraph, { size: 20 }));
const MiStratosIcon = defineComponent(() => () => h(PhHouse, { size: 20 }));
const InvestorRadarIcon = defineComponent(() => () => h(PhGraph, { size: 20 }));
const MobilityWarRoomIcon = defineComponent(
    () => () => h(PhSword, { size: 20 }),
);

const { can, hasRole } = usePermissions();
const tenantStore = useTenantStore();
tenantStore.initFromProps();

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
        icon: PhSquaresFour,
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
        icon: PhUser,
        requiredPermission: 'people.view',
    },
    // Roles — requires roles.view
    {
        title: 'Roles',
        href: '/roles',
        icon: PhBookOpen,
        requiredPermission: 'roles.view',
    },
    {
        title: 'Cultura Organizacional',
        href: '/controlcenter/culture',
        icon: defineComponent(() => () => h(PhScroll, { size: 20 })),
        requiredRole: ['admin'],
    },
    // Organigrama — requires people.view
    {
        title: 'Organigrama',
        href: '/departments/org-chart',
        icon: defineComponent(() => () => h(PhTreeStructure, { size: 20 })),
        requiredPermission: 'people.view',
        requiredModule: 'core',
    },
    // Competencies — requires competencies.view
    {
        title: 'Competencies',
        href: '/competencies',
        icon: defineComponent(() => () => h(PhPuzzlePiece, { size: 20 })),
        requiredPermission: 'competencies.view',
        requiredModule: 'core',
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
        requiredModule: 'st-map',
    },
    // Gap Analysis — requires people.view
    {
        title: 'Gap Analysis',
        href: '/gap-analysis',
        icon: GapAnalysisIcon,
        requiredPermission: 'people.view',
    },
    // Succession Planning — requires people.view
    {
        title: 'Plan de Sucesión',
        href: '/succession',
        icon: defineComponent(() => () => h(PhUsersFour, { size: 20 })),
        requiredPermission: 'people.view',
    },
    // Learning Paths — LMS learning paths with prerequisites
    {
        title: 'Rutas de Aprendizaje',
        href: '/lms/learning-paths',
        icon: defineComponent(() => () => h(PhPath, { size: 20 })),
        requiredPermission: 'lms.courses.view',
    },
    // Quiz Builder (instructors)
    {
        title: 'Quizzes',
        href: '/lms/quiz-builder',
        icon: defineComponent(() => () => h(PhQuestion, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    // Course Designer (AI-assisted)
    {
        title: 'Diseñador de Cursos',
        href: '/lms/course-designer',
        icon: defineComponent(() => () => h(PhPencilLine, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    // Mentoring Hub — requires people.view
    {
        title: 'Mentoring Hub',
        href: '/mentoring',
        icon: defineComponent(() => () => h(PhStar, { size: 20 })),
        requiredPermission: 'people.view',
        requiredModule: 'st-grow',
    },
    // Marketplace — requires people.view
    {
        title: 'Marketplace',
        href: '/marketplace',
        icon: MarketplaceIcon,
        requiredPermission: 'people.view',
        requiredModule: 'st-match',
    },
    // Scenario Planning
    {
        title: 'Scenario Planning',
        href: '/strategic-planning',
        icon: ScenarioPlanningIcon,
    },
    // Talent Pass (CV 2.0)
    {
        title: 'Talent Pass',
        href: '/talent-pass',
        icon: defineComponent(
            () => () => h(PhIdentificationCard, { size: 20 }),
        ),
        requiredModule: 'core',
    },
    // Mensajes
    {
        title: 'Mensajes',
        href: '/messaging',
        icon: defineComponent(() => () => h(PhChatCircle, { size: 20 })),
    },
    // Scenario Planning Analytics (Phase 1)
    {
        title: 'Planning Analytics',
        href: '/scenario-planning/analytics',
        icon: PhChartBar,
    },
    // Workforce Planning Fase 2-4
    {
        title: 'WFP Recomendaciones',
        href: '/workforce-planning/recomendaciones',
        icon: defineComponent(() => () => h(PhRocketLaunch, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'WFP Gobernanza',
        href: '/workforce-planning/gobernanza',
        icon: defineComponent(() => () => h(PhChartBar, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'WFP Comparador',
        href: '/workforce-planning/comparador',
        icon: defineComponent(() => () => h(PhGraph, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Skill Intelligence',
        href: '/skill-intelligence',
        icon: defineComponent(() => () => h(PhBrain, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Performance AI',
        href: '/performance',
        icon: defineComponent(() => () => h(PhChartLineUp, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Organigrama',
        href: '/org-chart',
        icon: defineComponent(() => () => h(PhTreeStructure, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    // Talento 360 — requires assessments.view
    {
        title: 'Talento 360°',
        href: '/talento360',
        icon: Talento360Icon,
        requiredPermission: 'assessments.view',
        requiredModule: 'st-360',
    },
    // Stratos Map Heatmap
    {
        title: 'Stratos Map (Heatmap)',
        href: '/talento360/map',
        icon: defineComponent(() => () => h(PhMapTrifold, { size: 20 })),
        requiredModule: 'core',
    },
    // Comando 360 — admin/hr_leader only
    {
        title: 'Comando 360',
        href: '/talento360/comando',
        icon: defineComponent(() => () => h(PhRocketLaunch, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    // Mobility War-Room — leadership/observers
    {
        title: 'Mobility War-Room',
        href: '/talento360/war-room',
        icon: MobilityWarRoomIcon,
        requiredRole: ['admin', 'hr_leader', 'observer'],
    },
    // People Experience — requires people.view
    {
        title: 'People Experience',
        href: '/people-experience',
        icon: PeopleExperienceIcon,
        requiredPermission: 'people.view',
        requiredModule: 'st-px',
    },
    // Comando PX — admin/hr_leader only
    {
        title: 'Comando PX',
        href: '/people-experience/comando',
        icon: defineComponent(() => () => h(PhBrain, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Social Learning',
        href: '/people-experience/social-learning',
        icon: defineComponent(() => () => h(PhChalkboardTeacher, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    // Talent Agents — requires agents.view
    {
        title: 'Talent Agents',
        href: '/talent-agents',
        icon: TalentAgentsIcon,
        requiredPermission: 'agents.view',
    },
    {
        title: 'Roles & Permisos',
        href: '/settings/rbac',
        icon: PhLockKey,
        requiredRole: ['admin'],
    },
    {
        title: 'Quality Hub',
        href: '/quality-hub',
        icon: defineComponent(() => () => h(PhShieldCheck, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Intelligence Hub',
        href: '/intelligence/monitoring-hub',
        icon: defineComponent(() => () => h(PhEye, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Stratos Compliance',
        href: '/quality/compliance-audit',
        icon: defineComponent(() => () => h(PhScroll, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'LLM Quality (RAGAS)',
        href: '/quality/ragas-metrics',
        icon: defineComponent(() => () => h(PhChartBar, { size: 20 })),
        requiredRole: ['admin', 'hr_leader'],
    },
    {
        title: 'Centro de Control',
        href: '/admin/alert-configuration',
        icon: defineComponent(() => () => h(PhBell, { size: 20 })),
        requiredRole: ['admin'],
    },
    {
        title: 'Admin Operations',
        href: '/admin/operations',
        icon: defineComponent(() => () => h(PhGear, { size: 20 })),
        requiredRole: ['admin'],
    },
    {
        title: 'Audit Logs',
        href: '/admin/audit-logs',
        icon: defineComponent(() => () => h(PhClipboardText, { size: 20 })),
        requiredRole: ['admin'],
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

        // Check module
        if (item.requiredModule && !tenantStore.hasModule(item.requiredModule))
            return false;

        return true;
    });
});

const footerNavItems: NavItem[] = [
    {
        title: 'Documentación',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: defineComponent(() => () => h(PhBookOpen, { size: 20 })),
    },
    {
        title: 'Mesa de ayuda',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: defineComponent(() => () => h(PhStar, { size: 20 })),
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="border-none bg-transparent! p-2"
    >
        <SidebarHeader
            class="mb-2 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md"
        >
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

        <SidebarContent
            class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-md"
        >
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter
            class="mt-2 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md"
        >
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>

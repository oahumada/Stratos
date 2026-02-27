<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mi Stratos', href: '/mi-stratos' },
];

interface PersonData {
    person: {
        id: number;
        first_name: string;
        last_name: string;
        full_name: string;
        email: string;
        photo_url: string | null;
        hire_date: string | null;
        is_high_potential: boolean;
        department: { id: number; name: string } | null;
        role: {
            id: number;
            name: string;
            archetype: string | null;
            mastery_level: number | null;
        } | null;
    } | null;
    kpis: {
        potential: number;
        readiness: number;
        learning: number;
        skills_count: number;
        gap_count: number;
        is_high_potential: boolean;
    };
    gap_analysis: any;
    competencies: Array<{
        id: number;
        name: string;
        skills: Array<{
            id: number;
            name: string;
            required_level: number;
            current_level: number;
            is_critical: boolean;
        }>;
    }>;
    learning_paths: Array<{
        id: number;
        title: string;
        status: string;
        total_actions: number;
        completed_actions: number;
        progress: number;
        created_at: string;
    }>;
    conversations: Array<{
        id: number;
        type: string;
        status: string;
        created_at: string;
        completed_at: string | null;
    }>;
    psychometric: any;
    message?: string;
}

const loading = ref(true);
const data = ref<PersonData | null>(null);
const activeSection = ref('dashboard');
const greeting = ref('');

// Greeting based on time of day
const updateGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) greeting.value = '¡Buenos días';
    else if (hour < 18) greeting.value = '¡Buenas tardes';
    else greeting.value = '¡Buenas noches';
};

const fetchDashboard = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/mi-stratos/dashboard');
        data.value = response.data.data;
    } catch (error) {
        console.error('Error loading Mi Stratos:', error);
    } finally {
        loading.value = false;
    }
};

const person = computed(() => data.value?.person);
const kpis = computed(() => data.value?.kpis);
const competencies = computed(() => data.value?.competencies ?? []);
const learningPaths = computed(() => data.value?.learning_paths ?? []);
const conversations = computed(() => data.value?.conversations ?? []);

const archetypeLabel = computed(() => {
    const arch = person.value?.role?.archetype;
    if (!arch) return null;
    const labels: Record<string, string> = {
        E: 'Estratégico',
        T: 'Táctico',
        O: 'Operativo',
        strategic: 'Estratégico',
        tactical: 'Táctico',
        operational: 'Operativo',
    };
    return labels[arch] ?? arch;
});

const cubeLabel = computed(() => {
    const arch = person.value?.role?.archetype;
    const level = person.value?.role?.mastery_level;
    if (!arch || !level) return null;
    return `${arch}${level}`;
});

const kpiColor = (value: number) => {
    if (value >= 80) return '#4caf50';
    if (value >= 60) return '#ff9800';
    if (value >= 40) return '#ff5722';
    return '#f44336';
};

const getConversationIcon = (type: string) => {
    const icons: Record<string, string> = {
        evaluation: 'mdi-clipboard-check',
        interview: 'mdi-robot',
        pulse: 'mdi-heart-pulse',
        mentor: 'mdi-account-star',
    };
    return icons[type] || 'mdi-message';
};

const getConversationLabel = (type: string) => {
    const labels: Record<string, string> = {
        evaluation: 'Evaluación 360',
        interview: 'Entrevista IA',
        pulse: 'Pulse Check',
        mentor: 'Mentor AI',
    };
    return labels[type] || type;
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        completed: 'success',
        active: 'info',
        pending: 'warning',
        in_progress: 'info',
    };
    return colors[status] || 'grey';
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('es-CL', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const sidebarSections = [
    { key: 'dashboard', icon: 'mdi-view-dashboard', label: 'Dashboard' },
    { key: 'role', icon: 'mdi-badge-account', label: 'Mi Rol' },
    { key: 'gaps', icon: 'mdi-target', label: 'Mi Brecha' },
    { key: 'learning', icon: 'mdi-school', label: 'Mi Ruta' },
    { key: 'conversations', icon: 'mdi-forum', label: 'Conversaciones' },
];

onMounted(() => {
    updateGreeting();
    fetchDashboard();
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Mi Stratos — Portal Personal" />

    <div class="mi-stratos-container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-overlay">
            <v-progress-circular
                indeterminate
                color="white"
                size="64"
                width="5"
            />
            <p class="loading-text">Preparando tu espacio personal...</p>
        </div>

        <!-- Empty State: No person linked -->
        <div v-else-if="!person" class="empty-state">
            <v-icon size="80" color="grey-lighten-1">mdi-account-off</v-icon>
            <h2 class="text-h4 text-grey-lighten-1 mt-4">
                Sin perfil vinculado
            </h2>
            <p class="text-grey mt-2">
                {{
                    data?.message ||
                    'No hay un perfil de colaborador asociado a tu cuenta.'
                }}
            </p>
        </div>

        <!-- Main Content -->
        <template v-else>
            <!-- Hero Header -->
            <div class="hero-header">
                <div class="hero-backdrop" />
                <div class="hero-content">
                    <div class="d-flex align-center flex-wrap gap-6">
                        <v-avatar size="100" class="hero-avatar">
                            <v-img
                                :src="
                                    person.photo_url ||
                                    '/placeholder-avatar.png'
                                "
                                cover
                            />
                        </v-avatar>

                        <div class="flex-grow-1">
                            <p class="greeting-text">
                                {{ greeting }}, {{ person.first_name }}!
                            </p>
                            <h1 class="hero-name">{{ person.full_name }}</h1>

                            <div
                                class="d-flex align-center mt-2 flex-wrap gap-3"
                            >
                                <v-chip
                                    v-if="person.role"
                                    color="white"
                                    variant="outlined"
                                    size="small"
                                    prepend-icon="mdi-badge-account"
                                >
                                    {{ person.role.name }}
                                </v-chip>
                                <v-chip
                                    v-if="cubeLabel"
                                    color="amber"
                                    variant="flat"
                                    size="small"
                                    prepend-icon="mdi-cube"
                                >
                                    Cubo: {{ cubeLabel }}
                                </v-chip>
                                <v-chip
                                    v-if="archetypeLabel"
                                    variant="tonal"
                                    color="cyan"
                                    size="small"
                                    prepend-icon="mdi-shape"
                                >
                                    {{ archetypeLabel }}
                                </v-chip>
                                <v-chip
                                    v-if="person.department"
                                    variant="tonal"
                                    color="white"
                                    size="small"
                                    prepend-icon="mdi-domain"
                                >
                                    {{ person.department.name }}
                                </v-chip>
                                <v-chip
                                    v-if="person.is_high_potential"
                                    color="purple"
                                    variant="flat"
                                    size="small"
                                    prepend-icon="mdi-star"
                                >
                                    High Potential
                                </v-chip>
                            </div>
                        </div>

                        <!-- Quick KPI -->
                        <div
                            v-if="kpis"
                            class="kpi-hero-badge d-none d-md-flex"
                        >
                            <v-progress-circular
                                :model-value="kpis.potential"
                                :color="kpiColor(kpis.potential)"
                                :size="80"
                                :width="8"
                                class="kpi-circle"
                            >
                                <span
                                    class="text-h5 font-weight-black text-white"
                                >
                                    {{ kpis.potential }}%
                                </span>
                            </v-progress-circular>
                            <span class="kpi-hero-label">Match</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body: Sidebar + Content -->
            <div class="portal-body">
                <!-- Mini Sidebar -->
                <div class="portal-sidebar">
                    <div
                        v-for="section in sidebarSections"
                        :key="section.key"
                        class="sidebar-item"
                        :class="{ active: activeSection === section.key }"
                        @click="activeSection = section.key"
                    >
                        <v-icon :icon="section.icon" size="20" />
                        <span class="sidebar-label">{{ section.label }}</span>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="portal-content">
                    <!-- SECTION: Dashboard -->
                    <transition name="fade" mode="out-in">
                        <div
                            v-if="activeSection === 'dashboard'"
                            key="dashboard"
                        >
                            <h2 class="section-title">
                                <v-icon class="mr-2" color="primary"
                                    >mdi-view-dashboard</v-icon
                                >
                                Dashboard Personal
                            </h2>

                            <!-- KPI Cards -->
                            <v-row v-if="kpis" class="mb-6">
                                <v-col cols="12" sm="6" md="3">
                                    <div class="kpi-card kpi-potential">
                                        <div class="kpi-icon-wrapper">
                                            <v-icon size="28"
                                                >mdi-lightning-bolt</v-icon
                                            >
                                        </div>
                                        <div class="kpi-value">
                                            {{ kpis.potential }}%
                                        </div>
                                        <div class="kpi-label">Potencial</div>
                                        <v-progress-linear
                                            :model-value="kpis.potential"
                                            :color="kpiColor(kpis.potential)"
                                            height="4"
                                            rounded
                                            class="mt-2"
                                        />
                                    </div>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <div class="kpi-card kpi-readiness">
                                        <div class="kpi-icon-wrapper">
                                            <v-icon size="28"
                                                >mdi-shield-check</v-icon
                                            >
                                        </div>
                                        <div class="kpi-value">
                                            {{ kpis.readiness }}%
                                        </div>
                                        <div class="kpi-label">Readiness</div>
                                        <v-progress-linear
                                            :model-value="kpis.readiness"
                                            :color="kpiColor(kpis.readiness)"
                                            height="4"
                                            rounded
                                            class="mt-2"
                                        />
                                    </div>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <div class="kpi-card kpi-learning">
                                        <div class="kpi-icon-wrapper">
                                            <v-icon size="28"
                                                >mdi-school</v-icon
                                            >
                                        </div>
                                        <div class="kpi-value">
                                            {{ kpis.learning }}%
                                        </div>
                                        <div class="kpi-label">Aprendizaje</div>
                                        <v-progress-linear
                                            :model-value="kpis.learning"
                                            :color="kpiColor(kpis.learning)"
                                            height="4"
                                            rounded
                                            class="mt-2"
                                        />
                                    </div>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <div class="kpi-card kpi-skills">
                                        <div class="kpi-icon-wrapper">
                                            <v-icon size="28"
                                                >mdi-star-circle</v-icon
                                            >
                                        </div>
                                        <div class="kpi-value">
                                            {{ kpis.skills_count }}
                                        </div>
                                        <div class="kpi-label">
                                            Skills Activas
                                        </div>
                                        <div class="kpi-sub mt-2">
                                            <v-icon
                                                size="14"
                                                color="warning"
                                                class="mr-1"
                                                >mdi-alert-circle</v-icon
                                            >
                                            {{ kpis.gap_count }} brechas
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>

                            <!-- Quick Panels -->
                            <v-row>
                                <!-- Active Conversations -->
                                <v-col cols="12" md="6">
                                    <v-card class="glass-card" flat>
                                        <v-card-title
                                            class="d-flex align-center"
                                        >
                                            <v-icon
                                                class="mr-2"
                                                color="secondary"
                                                >mdi-forum</v-icon
                                            >
                                            Conversaciones Activas
                                        </v-card-title>
                                        <v-card-text>
                                            <div
                                                v-if="
                                                    conversations.length === 0
                                                "
                                                class="empty-panel"
                                            >
                                                <v-icon
                                                    size="40"
                                                    color="grey-lighten-1"
                                                    >mdi-message-off</v-icon
                                                >
                                                <p class="text-grey mt-2">
                                                    Sin conversaciones activas
                                                </p>
                                            </div>
                                            <v-list
                                                v-else
                                                bg-color="transparent"
                                                density="compact"
                                            >
                                                <v-list-item
                                                    v-for="conv in conversations"
                                                    :key="conv.id"
                                                    :prepend-icon="
                                                        getConversationIcon(
                                                            conv.type,
                                                        )
                                                    "
                                                    :title="
                                                        getConversationLabel(
                                                            conv.type,
                                                        )
                                                    "
                                                    :subtitle="
                                                        formatDate(
                                                            conv.created_at,
                                                        )
                                                    "
                                                    class="conversation-item"
                                                >
                                                    <template #append>
                                                        <v-chip
                                                            :color="
                                                                getStatusColor(
                                                                    conv.status,
                                                                )
                                                            "
                                                            size="x-small"
                                                            variant="tonal"
                                                        >
                                                            {{ conv.status }}
                                                        </v-chip>
                                                    </template>
                                                </v-list-item>
                                            </v-list>
                                        </v-card-text>
                                    </v-card>
                                </v-col>

                                <!-- Learning Preview -->
                                <v-col cols="12" md="6">
                                    <v-card class="glass-card" flat>
                                        <v-card-title
                                            class="d-flex align-center"
                                        >
                                            <v-icon
                                                class="mr-2"
                                                color="secondary"
                                                >mdi-school</v-icon
                                            >
                                            Rutas de Aprendizaje
                                        </v-card-title>
                                        <v-card-text>
                                            <div
                                                v-if="
                                                    learningPaths.length === 0
                                                "
                                                class="empty-panel"
                                            >
                                                <v-icon
                                                    size="40"
                                                    color="grey-lighten-1"
                                                    >mdi-bookshelf</v-icon
                                                >
                                                <p class="text-grey mt-2">
                                                    Sin rutas asignadas
                                                </p>
                                            </div>
                                            <div v-else>
                                                <div
                                                    v-for="path in learningPaths"
                                                    :key="path.id"
                                                    class="learning-item"
                                                >
                                                    <div
                                                        class="d-flex justify-space-between align-center mb-1"
                                                    >
                                                        <span
                                                            class="learning-title"
                                                            >{{
                                                                path.title
                                                            }}</span
                                                        >
                                                        <span
                                                            class="learning-progress-text"
                                                            >{{
                                                                path.progress
                                                            }}%</span
                                                        >
                                                    </div>
                                                    <v-progress-linear
                                                        :model-value="
                                                            path.progress
                                                        "
                                                        :color="
                                                            kpiColor(
                                                                path.progress,
                                                            )
                                                        "
                                                        height="6"
                                                        rounded
                                                    />
                                                    <div
                                                        class="text-caption text-grey mt-1"
                                                    >
                                                        {{
                                                            path.completed_actions
                                                        }}/{{
                                                            path.total_actions
                                                        }}
                                                        acciones completadas
                                                    </div>
                                                </div>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </div>
                    </transition>

                    <!-- SECTION: Mi Rol -->
                    <transition name="fade" mode="out-in">
                        <div v-if="activeSection === 'role'" key="role">
                            <h2 class="section-title">
                                <v-icon class="mr-2" color="primary"
                                    >mdi-badge-account</v-icon
                                >
                                Mi Rol
                            </h2>

                            <v-card
                                v-if="person.role"
                                class="glass-card mb-4"
                                flat
                            >
                                <v-card-text>
                                    <div class="d-flex align-center mb-4 gap-4">
                                        <v-avatar color="primary" size="56">
                                            <v-icon size="28"
                                                >mdi-badge-account</v-icon
                                            >
                                        </v-avatar>
                                        <div>
                                            <h3
                                                class="text-h5 font-weight-bold"
                                            >
                                                {{ person.role.name }}
                                            </h3>
                                            <div class="d-flex mt-1 gap-2">
                                                <v-chip
                                                    v-if="archetypeLabel"
                                                    color="cyan"
                                                    size="small"
                                                    variant="tonal"
                                                >
                                                    {{ archetypeLabel }}
                                                </v-chip>
                                                <v-chip
                                                    v-if="cubeLabel"
                                                    color="amber"
                                                    size="small"
                                                    variant="flat"
                                                >
                                                    Cubo {{ cubeLabel }}
                                                </v-chip>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Competencies Grid -->
                                    <h4
                                        class="text-subtitle-1 font-weight-bold mb-3"
                                    >
                                        Competencias Asignadas
                                    </h4>
                                    <div
                                        v-if="competencies.length === 0"
                                        class="text-grey py-4 text-center"
                                    >
                                        Sin competencias asignadas
                                    </div>
                                    <v-expansion-panels
                                        v-else
                                        variant="accordion"
                                        class="competencies-panels"
                                    >
                                        <v-expansion-panel
                                            v-for="comp in competencies"
                                            :key="comp.id"
                                            class="competency-panel"
                                        >
                                            <v-expansion-panel-title>
                                                <div
                                                    class="d-flex align-center gap-2"
                                                >
                                                    <v-icon
                                                        size="18"
                                                        color="secondary"
                                                        >mdi-puzzle</v-icon
                                                    >
                                                    <span
                                                        class="font-weight-medium"
                                                        >{{ comp.name }}</span
                                                    >
                                                    <v-chip
                                                        size="x-small"
                                                        variant="tonal"
                                                        color="primary"
                                                    >
                                                        {{
                                                            comp.skills.length
                                                        }}
                                                        skills
                                                    </v-chip>
                                                </div>
                                            </v-expansion-panel-title>
                                            <v-expansion-panel-text>
                                                <div
                                                    v-for="skill in comp.skills"
                                                    :key="skill.id"
                                                    class="skill-row"
                                                >
                                                    <div
                                                        class="d-flex justify-space-between align-center"
                                                    >
                                                        <div
                                                            class="d-flex align-center gap-2"
                                                        >
                                                            <v-icon
                                                                v-if="
                                                                    skill.is_critical
                                                                "
                                                                size="14"
                                                                color="error"
                                                            >
                                                                mdi-alert
                                                            </v-icon>
                                                            <span>{{
                                                                skill.name
                                                            }}</span>
                                                        </div>
                                                        <div
                                                            class="d-flex align-center gap-2"
                                                        >
                                                            <span
                                                                class="text-caption text-grey"
                                                            >
                                                                {{
                                                                    skill.current_level
                                                                }}/{{
                                                                    skill.required_level
                                                                }}
                                                            </span>
                                                            <v-progress-linear
                                                                :model-value="
                                                                    (skill.current_level /
                                                                        Math.max(
                                                                            1,
                                                                            skill.required_level,
                                                                        )) *
                                                                    100
                                                                "
                                                                :color="
                                                                    skill.current_level >=
                                                                    skill.required_level
                                                                        ? 'success'
                                                                        : 'warning'
                                                                "
                                                                height="6"
                                                                rounded
                                                                style="
                                                                    width: 80px;
                                                                "
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </v-expansion-panel-text>
                                        </v-expansion-panel>
                                    </v-expansion-panels>
                                </v-card-text>
                            </v-card>

                            <div v-else class="empty-panel py-8 text-center">
                                <v-icon size="48" color="grey-lighten-1"
                                    >mdi-briefcase-off</v-icon
                                >
                                <p class="text-grey mt-2">
                                    No tienes un rol asignado
                                </p>
                            </div>
                        </div>
                    </transition>

                    <!-- SECTION: Mi Brecha -->
                    <transition name="fade" mode="out-in">
                        <div v-if="activeSection === 'gaps'" key="gaps">
                            <h2 class="section-title">
                                <v-icon class="mr-2" color="primary"
                                    >mdi-target</v-icon
                                >
                                Mi Brecha
                            </h2>

                            <v-card
                                v-if="data?.gap_analysis"
                                class="glass-card"
                                flat
                            >
                                <v-card-text>
                                    <div class="d-flex align-center mb-6 gap-4">
                                        <v-progress-circular
                                            :model-value="
                                                data.gap_analysis.summary
                                                    ?.match_percentage || 0
                                            "
                                            :color="
                                                kpiColor(
                                                    data.gap_analysis.summary
                                                        ?.match_percentage || 0,
                                                )
                                            "
                                            :size="80"
                                            :width="8"
                                        >
                                            <span
                                                class="text-h6 font-weight-bold"
                                            >
                                                {{
                                                    data.gap_analysis.summary
                                                        ?.match_percentage || 0
                                                }}%
                                            </span>
                                        </v-progress-circular>
                                        <div>
                                            <h3
                                                class="text-h6 font-weight-bold"
                                            >
                                                Match con tu Rol
                                            </h3>
                                            <v-chip
                                                :color="
                                                    getStatusColor(
                                                        data.gap_analysis
                                                            .summary
                                                            ?.category || '',
                                                    )
                                                "
                                                variant="tonal"
                                                size="small"
                                            >
                                                {{
                                                    data.gap_analysis.summary
                                                        ?.category ||
                                                    'Sin calificar'
                                                }}
                                            </v-chip>
                                        </div>
                                    </div>

                                    <div v-if="data.gap_analysis.gaps?.length">
                                        <h4
                                            class="text-subtitle-1 font-weight-bold mb-3"
                                        >
                                            Brechas Detectadas
                                        </h4>
                                        <div
                                            v-for="(
                                                gap, idx
                                            ) in data.gap_analysis.gaps
                                                .filter((g: any) => g.gap > 0)
                                                .slice(0, 8)"
                                            :key="idx"
                                            class="gap-row"
                                        >
                                            <div
                                                class="d-flex justify-space-between align-center mb-1"
                                            >
                                                <span
                                                    class="font-weight-medium"
                                                    >{{ gap.skill_name }}</span
                                                >
                                                <span class="text-caption">
                                                    Actual: {{ gap.current }} →
                                                    Requerido:
                                                    {{ gap.required }}
                                                </span>
                                            </div>
                                            <v-progress-linear
                                                :model-value="
                                                    (gap.current /
                                                        Math.max(
                                                            1,
                                                            gap.required,
                                                        )) *
                                                    100
                                                "
                                                color="warning"
                                                bg-color="error"
                                                height="6"
                                                rounded
                                            />
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="text-grey py-4 text-center"
                                    >
                                        ¡Excelente! No se detectaron brechas
                                        críticas.
                                    </div>
                                </v-card-text>
                            </v-card>

                            <div v-else class="empty-panel py-8 text-center">
                                <v-icon size="48" color="grey-lighten-1"
                                    >mdi-chart-line</v-icon
                                >
                                <p class="text-grey mt-2">
                                    No hay análisis de brecha disponible
                                </p>
                            </div>
                        </div>
                    </transition>

                    <!-- SECTION: Mi Ruta (Learning) -->
                    <transition name="fade" mode="out-in">
                        <div v-if="activeSection === 'learning'" key="learning">
                            <h2 class="section-title">
                                <v-icon class="mr-2" color="primary"
                                    >mdi-school</v-icon
                                >
                                Mi Ruta de Aprendizaje
                            </h2>

                            <div
                                v-if="learningPaths.length === 0"
                                class="empty-panel py-8 text-center"
                            >
                                <v-icon size="48" color="grey-lighten-1"
                                    >mdi-bookshelf</v-icon
                                >
                                <p class="text-grey mt-2">
                                    No tienes rutas de aprendizaje asignadas
                                </p>
                            </div>

                            <v-row v-else>
                                <v-col
                                    v-for="path in learningPaths"
                                    :key="path.id"
                                    cols="12"
                                    md="6"
                                >
                                    <v-card
                                        class="glass-card learning-card"
                                        flat
                                    >
                                        <v-card-text>
                                            <div
                                                class="d-flex align-center mb-3 gap-3"
                                            >
                                                <v-avatar
                                                    color="primary"
                                                    variant="tonal"
                                                    size="40"
                                                >
                                                    <v-icon
                                                        >mdi-road-variant</v-icon
                                                    >
                                                </v-avatar>
                                                <div class="flex-grow-1">
                                                    <h4
                                                        class="font-weight-bold"
                                                    >
                                                        {{ path.title }}
                                                    </h4>
                                                    <span
                                                        class="text-caption text-grey"
                                                    >
                                                        Iniciada:
                                                        {{
                                                            formatDate(
                                                                path.created_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                                <v-chip
                                                    :color="
                                                        getStatusColor(
                                                            path.status,
                                                        )
                                                    "
                                                    size="x-small"
                                                    variant="tonal"
                                                >
                                                    {{ path.status }}
                                                </v-chip>
                                            </div>

                                            <div
                                                class="d-flex justify-space-between mb-1"
                                            >
                                                <span class="text-body-2"
                                                    >Progreso</span
                                                >
                                                <span
                                                    class="text-body-2 font-weight-bold"
                                                >
                                                    {{ path.progress }}%
                                                </span>
                                            </div>
                                            <v-progress-linear
                                                :model-value="path.progress"
                                                :color="kpiColor(path.progress)"
                                                height="8"
                                                rounded
                                            />
                                            <div
                                                class="text-caption text-grey mt-2"
                                            >
                                                {{ path.completed_actions }} de
                                                {{
                                                    path.total_actions
                                                }}
                                                acciones completadas
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </div>
                    </transition>

                    <!-- SECTION: Conversations -->
                    <transition name="fade" mode="out-in">
                        <div
                            v-if="activeSection === 'conversations'"
                            key="conversations"
                        >
                            <h2 class="section-title">
                                <v-icon class="mr-2" color="primary"
                                    >mdi-forum</v-icon
                                >
                                Mis Conversaciones
                            </h2>

                            <div
                                v-if="conversations.length === 0"
                                class="empty-panel py-8 text-center"
                            >
                                <v-icon size="48" color="grey-lighten-1"
                                    >mdi-message-off</v-icon
                                >
                                <p class="text-grey mt-2">
                                    No tienes conversaciones activas
                                </p>
                            </div>

                            <v-row v-else>
                                <v-col
                                    v-for="conv in conversations"
                                    :key="conv.id"
                                    cols="12"
                                    md="6"
                                >
                                    <v-card class="glass-card" flat>
                                        <v-card-text>
                                            <div
                                                class="d-flex align-center gap-3"
                                            >
                                                <v-avatar
                                                    color="secondary"
                                                    variant="tonal"
                                                    size="48"
                                                >
                                                    <v-icon>{{
                                                        getConversationIcon(
                                                            conv.type,
                                                        )
                                                    }}</v-icon>
                                                </v-avatar>
                                                <div class="flex-grow-1">
                                                    <h4
                                                        class="font-weight-bold"
                                                    >
                                                        {{
                                                            getConversationLabel(
                                                                conv.type,
                                                            )
                                                        }}
                                                    </h4>
                                                    <span
                                                        class="text-caption text-grey"
                                                    >
                                                        {{
                                                            formatDate(
                                                                conv.created_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                                <v-chip
                                                    :color="
                                                        getStatusColor(
                                                            conv.status,
                                                        )
                                                    "
                                                    size="small"
                                                    variant="tonal"
                                                >
                                                    {{ conv.status }}
                                                </v-chip>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </div>
                    </transition>
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped>
/* ═══════════════════════════════════════════════
   MI STRATOS — Premium Personal Portal
   ═══════════════════════════════════════════════ */

.mi-stratos-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29 0%, #1a1a3e 40%, #24243e 100%);
    color: #e0e0e0;
    font-family: 'Inter', 'Roboto', sans-serif;
}

/* Loading */
.loading-overlay {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    gap: 16px;
}

.loading-text {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.1rem;
    font-weight: 300;
    animation: pulse-text 2s ease-in-out infinite;
}

@keyframes pulse-text {
    0%,
    100% {
        opacity: 0.6;
    }
    50% {
        opacity: 1;
    }
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 50vh;
    text-align: center;
}

/* Hero Header */
.hero-header {
    position: relative;
    padding: 40px 32px 32px;
    overflow: hidden;
}

.hero-backdrop {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(103, 58, 183, 0.35) 0%,
        rgba(33, 150, 243, 0.25) 50%,
        rgba(0, 188, 212, 0.2) 100%
    );
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.hero-content {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-avatar {
    border: 3px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 0 30px rgba(103, 58, 183, 0.4);
    transition: transform 0.3s ease;
}

.hero-avatar:hover {
    transform: scale(1.05);
}

.greeting-text {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 300;
    letter-spacing: 0.5px;
}

.hero-name {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.kpi-hero-badge {
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.kpi-hero-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.kpi-circle {
    filter: drop-shadow(0 0 12px rgba(103, 58, 183, 0.4));
}

/* Portal Body */
.portal-body {
    display: flex;
    gap: 0;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px 32px;
}

/* Portal Sidebar */
.portal-sidebar {
    width: 200px;
    min-width: 200px;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    cursor: pointer;
    color: rgba(255, 255, 255, 0.6);
    transition: all 0.25s ease;
    font-size: 0.9rem;
}

.sidebar-item:hover {
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.06);
}

.sidebar-item.active {
    color: #ffffff;
    background: linear-gradient(
        135deg,
        rgba(103, 58, 183, 0.3) 0%,
        rgba(33, 150, 243, 0.2) 100%
    );
    border-left: 3px solid #7c4dff;
    box-shadow: 0 2px 12px rgba(103, 58, 183, 0.2);
}

.sidebar-label {
    font-weight: 500;
}

/* Content Area */
.portal-content {
    flex: 1;
    padding: 20px 0 20px 24px;
    min-width: 0;
}

.section-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

/* Glass Cards */
.glass-card {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 16px !important;
    color: #e0e0e0 !important;
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.glass-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

/* KPI Cards */
.kpi-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.kpi-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.kpi-potential .kpi-icon-wrapper {
    background: rgba(103, 58, 183, 0.2);
    color: #b388ff;
}
.kpi-readiness .kpi-icon-wrapper {
    background: rgba(33, 150, 243, 0.2);
    color: #82b1ff;
}
.kpi-learning .kpi-icon-wrapper {
    background: rgba(76, 175, 80, 0.2);
    color: #69f0ae;
}
.kpi-skills .kpi-icon-wrapper {
    background: rgba(255, 193, 7, 0.2);
    color: #ffd740;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
}

.kpi-label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 4px;
}

.kpi-sub {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Empty Panel */
.empty-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px;
}

/* Conversation Items */
.conversation-item {
    border-radius: 8px;
    transition: background 0.2s ease;
}

.conversation-item:hover {
    background: rgba(255, 255, 255, 0.04);
}

/* Learning Items */
.learning-item {
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.learning-item:last-child {
    border-bottom: none;
}

.learning-title {
    font-weight: 600;
    font-size: 0.9rem;
}

.learning-progress-text {
    font-weight: 700;
    font-size: 0.85rem;
    color: #82b1ff;
}

/* Competencies */
.competencies-panels {
    background: transparent !important;
}

.competency-panel {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    border-radius: 10px !important;
    margin-bottom: 8px !important;
    color: #e0e0e0 !important;
}

.skill-row {
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}

.skill-row:last-child {
    border-bottom: none;
}

/* Gap Rows */
.gap-row {
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.gap-row:last-child {
    border-bottom: none;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.3s ease,
        transform 0.3s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 960px) {
    .portal-body {
        flex-direction: column;
        padding: 0 12px 24px;
    }

    .portal-sidebar {
        width: 100%;
        min-width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding: 12px 0;
        gap: 4px;
    }

    .sidebar-item {
        flex-shrink: 0;
        padding: 8px 14px;
    }

    .sidebar-item.active {
        border-left: none;
        border-bottom: 3px solid #7c4dff;
    }

    .portal-content {
        padding: 12px 0;
    }

    .hero-header {
        padding: 24px 16px;
    }

    .hero-name {
        font-size: 1.5rem;
    }
}
</style>

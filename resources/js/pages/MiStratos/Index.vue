<script setup lang="ts">
import HeroSection from './parts/HeroSection.vue';
import DashboardSection from './parts/DashboardSection.vue';
import RoleSection from './parts/RoleSection.vue';
import GapsSection from './parts/GapsSection.vue';
import LearningSection from './parts/LearningSection.vue';
import ConversationsSection from './parts/ConversationsSection.vue';
import EvaluationsSection from './parts/EvaluationsSection.vue';
import DnaSection from './parts/DnaSection.vue';
import AssessmentChat from '@/components/Assessments/AssessmentChat.vue';
import GamificationWidget from '@/components/Talent/GamificationWidget.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

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
        current_points: number;
        badges: Array<{
            id: number;
            name: string;
            description: string;
            icon: string;
            color: string;
            slug: string;
        }>;
    } | null;
    quests: Array<any>;
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
    evaluations: Array<{
        id: number;
        title: string;
        date: string;
        score: number;
        strengths: string[];
        opportunities: string[];
    }>;
    message?: string;
}

const loading = ref(true);
const data = ref<PersonData | null>(null);
const activeSection = ref('dashboard');
const greeting = ref('');
const isMentorChatOpen = ref(false);
const isRetakingDna = ref(false);

const toggleMentorChat = () => {
    isMentorChatOpen.value = !isMentorChatOpen.value;
};

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
const evaluations = computed(() => data.value?.evaluations ?? []);
const quests = computed(() => data.value?.quests ?? []);

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

// Eliminar funciones redundantes (ahora en componentes)

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
    { key: 'gaps', icon: 'mdi-target', label: 'Mis Brechas' },
    { key: 'learning', icon: 'mdi-school', label: 'Mi Ruta de Aprendizaje' },
    { key: 'conversations', icon: 'mdi-forum', label: 'Conversaciones' },
    { key: 'evaluations', icon: 'mdi-chart-radar', label: 'Mis Evaluaciones' },
    { key: 'dna', icon: 'mdi-dna', label: 'Mi ADN' },
    { key: 'achievements', icon: 'mdi-trophy', label: 'Mis Logros' },
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
            <!-- SECTION: Hero Header -->
            <HeroSection 
                v-if="person"
                :person="person"
                :greeting="greeting"
                :archetype-label="archetypeLabel"
                :cube-label="cubeLabel"
                :kpis="kpis"
            />

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
                    <!-- SECTION: Principal (Dashboard) -->
                    <transition name="fade" mode="out-in">
                        <DashboardSection 
                            v-if="activeSection === 'dashboard'"
                            key="dashboard"
                            :kpis="kpis"
                            :conversations="conversations"
                            :learning-paths="learningPaths"
                            :greeting="greeting"
                            @go-to-learning="activeSection = 'learning'"
                            @toggle-mentor-chat="toggleMentorChat"
                        />
                    </transition>

                    <!-- SECTION: Mi Rol -->
                    <transition name="fade" mode="out-in">
                        <RoleSection 
                            v-if="activeSection === 'role' && person"
                            key="role"
                            :person="person"
                            :competencies="competencies"
                            :archetype-label="archetypeLabel"
                            :cube-label="cubeLabel"
                        />
                    </transition>

                    <!-- SECTION: Mi Brecha -->
                    <transition name="fade" mode="out-in">
                        <GapsSection 
                            v-if="activeSection === 'gaps' && data"
                            key="gaps"
                            :gap-analysis="data.gap_analysis"
                            @go-to-learning="activeSection = 'learning'"
                        />
                    </transition>

                    <!-- SECTION: Mi Ruta (Learning) -->
                    <transition name="fade" mode="out-in">
                        <LearningSection 
                            v-if="activeSection === 'learning'"
                            key="learning"
                            :learning-paths="learningPaths"
                            :format-date="formatDate"
                        />
                    </transition>

                    <!-- SECTION: Conversations -->
                    <transition name="fade" mode="out-in">
                        <ConversationsSection 
                            v-if="activeSection === 'conversations'"
                            key="conversations"
                            :conversations="conversations"
                            :format-date="formatDate"
                        />
                    </transition>

                    <!-- SECTION: Mis Evaluaciones -->
                    <transition name="fade" mode="out-in">
                        <EvaluationsSection 
                            v-if="activeSection === 'evaluations'"
                            key="evaluations"
                            :evaluations="evaluations"
                        />
                    </transition>

                    <!-- SECTION: Mi ADN (Psychometric Profile) -->
                    <transition name="fade" mode="out-in">
                        <DnaSection 
                            v-if="activeSection === 'dna'"
                            key="dna"
                            v-model:is-retaking-dna="isRetakingDna"
                            :person="person"
                            :data="data"
                            :kpi-color="kpiColor"
                            @refresh="fetchDashboard"
                        />
                    </transition>

                    <!-- SECTION: Mis Logros -->
                    <transition name="fade" mode="out-in">
                        <div
                            v-if="activeSection === 'achievements'"
                            key="achievements"
                        >
                            <h2 class="section-title">
                                <v-icon class="mr-2" color="primary"
                                    >mdi-trophy</v-icon
                                >
                                Mis Logros
                            </h2>

                            <div class="mx-auto mt-6" style="max-width: 800px">
                                <GamificationWidget
                                    :points="person?.current_points || 0"
                                    :badges="person?.badges || []"
                                    :quests="quests"
                                />
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <!-- Floating Mentor AI Button -->
            <v-btn
                icon="mdi-robot"
                color="secondary"
                size="large"
                elevation="8"
                class="mentor-fab"
                @click="toggleMentorChat"
            />

            <!-- Mentor AI Chat Dialog -->
            <v-dialog
                v-model="isMentorChatOpen"
                max-width="600px"
                transition="dialog-bottom-transition"
            >
                <v-card
                    class="glass-card"
                    style="background: rgba(36, 36, 62, 0.95) !important"
                >
                    <v-toolbar
                        color="transparent"
                        title="Mentor AI"
                        class="text-white"
                    >
                        <template #prepend>
                            <v-icon color="secondary" class="ml-4"
                                >mdi-robot</v-icon
                            >
                        </template>
                        <v-spacer></v-spacer>
                        <v-btn
                            icon="mdi-close"
                            variant="text"
                            @click="isMentorChatOpen = false"
                            class="mr-2"
                        ></v-btn>
                    </v-toolbar>
                    <v-card-text class="pa-0">
                        <AssessmentChat
                            v-if="person"
                            :person-id="person.id"
                            type="mentor"
                            @completed="isMentorChatOpen = false"
                        />
                    </v-card-text>
                </v-card>
            </v-dialog>
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

/* Psychometric & Achievements Styles */
.trait-row {
    background: rgba(255, 255, 255, 0.02);
    padding: 12px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.achievement-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    height: 100%;
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.achievement-card.earned {
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.08) 0%,
        rgba(255, 255, 255, 0.02) 100%
    );
    border: 1px solid rgba(255, 215, 0, 0.2);
}

.achievement-card.locked {
    opacity: 0.6;
    filter: grayscale(100%);
}

.achievement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.achievement-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.05);
}

.achievement-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #fff;
}

.achievement-desc {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.4;
}

/* Floating Mentor Action Button */
.mentor-fab {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 99;
    border: 2px solid rgba(255, 255, 255, 0.2);
    animation: glow-pulse 3s infinite;
}

@keyframes glow-pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(76, 175, 80, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
}

:deep(.assessment-chat) {
    border-radius: 0 0 16px 16px;
    background: transparent;
}
:deep(.assessment-chat .chat-messages-bg) {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
:deep(.assessment-chat .text-subtitle-2),
:deep(.assessment-chat .text-body-2) {
    color: #fff !important;
}
:deep(.assessment-chat .v-field__input) {
    color: #fff;
}
</style>

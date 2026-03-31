<script setup lang="ts">
import AssessmentChat from '@/components/Assessments/AssessmentChat.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import GamificationWidget from '@/components/Talent/GamificationWidget.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    PhChartPieSlice,
    PhChatTeardropDots,
    PhDna,
    PhGraduationCap,
    PhRobot,
    PhStar,
    PhTarget,
    PhTrophy,
    PhUserCircle,
    PhWarningCircle,
    PhX,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import ConversationsSection from './parts/ConversationsSection.vue';
import DashboardSection from './parts/DashboardSection.vue';
import DnaSection from './parts/DnaSection.vue';
import EvaluationsSection from './parts/EvaluationsSection.vue';
import GapsSection from './parts/GapsSection.vue';
import HeroSection from './parts/HeroSection.vue';
import LearningSection from './parts/LearningSection.vue';
import RoleSection from './parts/RoleSection.vue';

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

const formatDate = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('es-CL', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const sidebarSections = [
    { key: 'dashboard', icon: PhChartPieSlice, label: 'Dashboard' },
    { key: 'role', icon: PhUserCircle, label: 'Mi Rol' },
    { key: 'gaps', icon: PhTarget, label: 'Mis Brechas' },
    { key: 'learning', icon: PhGraduationCap, label: 'Mi Ruta' },
    { key: 'conversations', icon: PhChatTeardropDots, label: 'Conversaciones' },
    { key: 'evaluations', icon: PhStar, label: 'Mis Evaluaciones' },
    { key: 'dna', icon: PhDna, label: 'Mi ADN' },
    { key: 'achievements', icon: PhTrophy, label: 'Mis Logros' },
];

onMounted(() => {
    updateGreeting();
    fetchDashboard();
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Mi Stratos — Portal Personal" />

    <div class="mi-stratos-container relative min-h-screen bg-[#020617] p-8!">
        <!-- Background Decoration (UX Pilar 1) -->
        <div
            class="pointer-events-none fixed inset-0 z-0 overflow-hidden text-indigo-500"
        >
            <div
                class="absolute -top-[5%] -left-[5%] h-[40%] w-[40%] rounded-full bg-current opacity-5 blur-[120px]"
            ></div>
            <div
                class="absolute right-[10%] -bottom-[10%] h-[35%] w-[35%] rounded-full bg-cyan-500/5 blur-[120px]"
            ></div>
        </div>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-950/60 backdrop-blur-xl transition-all duration-700"
        >
            <div class="relative mb-8">
                <div
                    class="absolute inset-0 animate-pulse rounded-full bg-indigo-500/20 blur-2xl"
                ></div>
                <div
                    class="h-16 w-16 animate-spin rounded-full border-4 border-indigo-500 border-t-transparent shadow-[0_0_20px_rgba(99,102,241,0.4)]"
                ></div>
            </div>
            <p
                class="animate-pulse text-lg font-light tracking-widest text-white/50 uppercase"
            >
                Sincronizando con Cerbero IA...
            </p>
        </div>

        <!-- Empty State: No person linked -->
        <div
            v-else-if="!person"
            class="relative z-10 mx-auto max-w-2xl overflow-hidden py-24 text-center"
        >
            <StCardGlass indicator="rose" class="p-16!">
                <PhWarningCircle
                    :size="80"
                    weight="thin"
                    class="mx-auto mb-6 text-rose-500/40"
                />
                <h2 class="mb-4 text-3xl font-black tracking-tight text-white">
                    Sin perfil vinculado
                </h2>
                <p class="mb-8 leading-relaxed font-light text-white/40">
                    {{
                        data?.message ||
                        'No hay un perfil de colaborador asociado a tu cuenta de acceso.'
                    }}
                </p>
                <StButtonGlass variant="ghost" class="mx-auto">
                    Contactar a Soporte
                </StButtonGlass>
            </StCardGlass>
        </div>

        <!-- Main Content -->
        <template v-else>
            <div class="relative z-10 mx-auto max-w-7xl">
                <!-- SECTION: Hero Header (Vanguard Standard) -->
                <HeroSection
                    v-if="person"
                    :person="person"
                    :greeting="greeting"
                    :archetype-label="archetypeLabel"
                    :cube-label="cubeLabel"
                    :kpis="kpis"
                    class="mb-10"
                />

                <!-- Body: Sidebar + Content -->
                <div class="flex flex-col gap-8 lg:flex-row">
                    <!-- Premium Portal Sidebar (UX Pilar 3) -->
                    <div class="w-full shrink-0 lg:w-64">
                        <div class="sticky top-24 space-y-2">
                            <button
                                v-for="section in sidebarSections"
                                :key="section.key"
                                @click="activeSection = section.key"
                                :class="[
                                    'group flex w-full items-center gap-3 rounded-2xl p-4 text-sm transition-all duration-300',
                                    activeSection === section.key
                                        ? 'bg-indigo-500/10 text-indigo-300 shadow-[0_4px_15px_rgba(99,102,241,0.15)] ring-1 ring-indigo-500/30'
                                        : 'text-white/40 hover:bg-white/5 hover:text-white',
                                ]"
                            >
                                <component
                                    :is="section.icon"
                                    :size="22"
                                    :weight="
                                        activeSection === section.key
                                            ? 'fill'
                                            : 'duotone'
                                    "
                                />
                                <span class="font-bold tracking-tight">{{
                                    section.label
                                }}</span>
                                <div
                                    v-if="activeSection === section.key"
                                    class="ml-auto h-1 w-1 rounded-full bg-indigo-500 shadow-[0_0_8px_#6366f1]"
                                ></div>
                            </button>
                        </div>
                    </div>

                    <!-- Content Area -->
                    <div class="min-w-0 grow">
                        <transition name="portal-fade" mode="out-in">
                            <div :key="activeSection">
                                <!-- SECTION: Principal (Dashboard) -->
                                <DashboardSection
                                    v-if="activeSection === 'dashboard'"
                                    :kpis="kpis"
                                    :conversations="conversations"
                                    :learning-paths="learningPaths"
                                    :greeting="greeting"
                                    @go-to-learning="activeSection = 'learning'"
                                    @toggle-mentor-chat="toggleMentorChat"
                                />

                                <!-- SECTION: Mi Rol -->
                                <RoleSection
                                    v-if="activeSection === 'role' && person"
                                    :person="person"
                                    :competencies="competencies"
                                    :archetype-label="archetypeLabel"
                                    :cube-label="cubeLabel"
                                />

                                <!-- SECTION: Mi Brecha -->
                                <GapsSection
                                    v-if="activeSection === 'gaps' && data"
                                    :gap-analysis="data.gap_analysis"
                                    @go-to-learning="activeSection = 'learning'"
                                />

                                <!-- SECTION: Mi Ruta (Learning) -->
                                <LearningSection
                                    v-if="activeSection === 'learning'"
                                    :learning-paths="learningPaths"
                                    :format-date="formatDate"
                                />

                                <!-- SECTION: Conversations -->
                                <ConversationsSection
                                    v-if="activeSection === 'conversations'"
                                    :conversations="conversations"
                                    :format-date="formatDate"
                                />

                                <!-- SECTION: Mis Evaluaciones -->
                                <EvaluationsSection
                                    v-if="activeSection === 'evaluations'"
                                    :evaluations="evaluations"
                                />

                                <!-- SECTION: Mi ADN (Psychometric Profile) -->
                                <DnaSection
                                    v-if="activeSection === 'dna'"
                                    v-model:is-retaking-dna="isRetakingDna"
                                    :person="person"
                                    :data="data"
                                    :kpi-color="kpiColor"
                                    @refresh="fetchDashboard"
                                />

                                <!-- SECTION: Mis Logros -->
                                <div v-if="activeSection === 'achievements'">
                                    <h2
                                        class="mb-8 flex items-center gap-3 text-2xl font-black text-white"
                                    >
                                        <PhTrophy
                                            :size="28"
                                            weight="duotone"
                                            class="text-amber-400"
                                        />
                                        Galpón de Logros
                                    </h2>
                                    <div class="mx-auto max-w-3xl">
                                        <GamificationWidget
                                            :points="
                                                person?.current_points || 0
                                            "
                                            :badges="person?.badges || []"
                                            :quests="quests"
                                        />
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>

            <!-- Floating Mentor AI Button (UX Pilar 4) -->
            <button
                @click="toggleMentorChat"
                class="group fixed right-8 bottom-8 z-40 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-600 text-white shadow-[0_8px_30px_rgba(79,70,229,0.5)] transition-all duration-300 hover:scale-110 hover:shadow-[0_12px_40px_rgba(79,70,229,0.7)] active:scale-95"
            >
                <div
                    class="absolute inset-0 animate-pulse rounded-3xl bg-white/20"
                ></div>
                <PhRobot
                    :size="32"
                    weight="fill"
                    class="relative transition-transform group-hover:rotate-12"
                />
            </button>

            <!-- Mentor AI Chat Dialog (Clean Glass Style) -->
            <div
                v-if="isMentorChatOpen"
                class="fixed inset-0 z-50 flex items-end justify-end p-4 md:p-8"
            >
                <div
                    class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm"
                    @click="isMentorChatOpen = false"
                ></div>

                <StCardGlass
                    class="relative h-[600px] w-full max-w-2xl animate-in overflow-hidden border-indigo-500/30 shadow-2xl duration-500 slide-in-from-bottom-8"
                    indicator="indigo"
                >
                    <div
                        class="mb-6 flex items-center justify-between border-b border-white/5 pb-6"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-400"
                            >
                                <PhRobot :size="24" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="font-black text-white">
                                    Mentor Estratégico AI
                                </h3>
                                <p
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    Escuchando sesión activa
                                </p>
                            </div>
                        </div>
                        <StButtonGlass
                            variant="ghost"
                            size="sm"
                            circle
                            @click="isMentorChatOpen = false"
                        >
                            <PhX :size="20" />
                        </StButtonGlass>
                    </div>

                    <div class="min-h-0 grow overflow-y-auto">
                        <AssessmentChat
                            v-if="person"
                            :person-id="person.id"
                            type="mentor"
                            @completed="isMentorChatOpen = false"
                        />
                    </div>
                </StCardGlass>
            </div>
        </template>
    </div>
</template>

<style scoped>
.portal-fade-enter-active,
.portal-fade-leave-active {
    transition:
        opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.portal-fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}

.portal-fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

/* Scrollbar styling for chat container */
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
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

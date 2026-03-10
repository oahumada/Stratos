<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import MentorCard from '@/components/Talent/MentorCard.vue';
import MentorshipSessionDialog from '@/components/Talent/MentorshipSessionDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    PhCalendar,
    PhClock,
    PhGraduationCap,
    PhMagnifyingGlass,
    PhSelection,
    PhSparkle,
    PhStar,
    PhTrendUp,
    PhUserFocus,
    PhUsers,
} from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

const loading = ref(true);
const mentors = ref([]);
const activeSessions = ref([]);
const searchQuery = ref('');
const sessionDialog = ref(null);
const selectedActionId = ref(null);

const stats = ref([
    {
        label: 'Mentores Activos',
        value: '12',
        icon: PhUsers,
        color: 'text-blue-400',
    },
    {
        label: 'Sesiones este mes',
        value: '48',
        icon: PhCalendar,
        color: 'text-emerald-400',
    },
    {
        label: 'Rating Promedio',
        value: '4.9',
        icon: PhStar,
        color: 'text-amber-400',
    },
    {
        label: 'Horas de Impacto',
        value: '120h',
        icon: PhClock,
        color: 'text-purple-400',
    },
]);

const fetchMentoringData = async () => {
    loading.value = true;
    try {
        // Mocking some data for the first load to ensure a premium feel
        mentors.value = [
            {
                id: 1,
                full_name: 'Elena Rodríguez',
                role: 'Head of Engineering',
                expertise_level: 5,
                match_score: 98,
                avatar: 'https://i.pravatar.cc/150?u=elena',
            },
            {
                id: 2,
                full_name: 'Marco Chen',
                role: 'Product Analytics Lead',
                expertise_level: 4,
                match_score: 94,
                avatar: 'https://i.pravatar.cc/150?u=marco',
            },
            {
                id: 3,
                full_name: 'Sarah Jenkins',
                role: 'Talent Strategy Director',
                expertise_level: 5,
                match_score: 91,
                avatar: 'https://i.pravatar.cc/150?u=sarah',
            },
            {
                id: 4,
                full_name: 'David Vales',
                role: 'UX Architect',
                expertise_level: 4,
                match_score: 89,
                avatar: 'https://i.pravatar.cc/150?u=david',
            },
        ];

        activeSessions.value = [
            {
                id: 1,
                mentor_name: 'Elena Rodríguez',
                topic: 'Arquitectura Microservicios',
                next_date: '2024-03-15 10:00',
                progress: 65,
            },
            {
                id: 2,
                mentor_name: 'Marco Chen',
                topic: 'Data Storytelling',
                next_date: '2024-03-18 15:30',
                progress: 30,
            },
        ];
    } catch (e) {
        console.error('Error fetching mentoring info', e);
    } finally {
        loading.value = false;
    }
};

const filteredMentors = computed(() => {
    if (!searchQuery.value) return mentors.value;
    return mentors.value.filter(
        (m) =>
            m.full_name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            m.role.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});

const openSessionDialog = (mentorId: number) => {
    // In a real scenario, we'd find the development action associated with this mentor
    // For now, we allow logging a session
    sessionDialog.value?.open();
};

onMounted(fetchMentoringData);
</script>

<template>
    <Head title="Mentoring Hub | Stratos Grow" />

    <div class="mx-auto min-h-screen max-w-7xl space-y-8 p-8">
        <!-- Header Section -->
        <header
            class="flex flex-col justify-between gap-6 md:flex-row md:items-end"
        >
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div
                        class="rounded-2xl bg-amber-500/20 p-2.5 text-amber-400"
                    >
                        <PhUserFocus :size="28" weight="duotone" />
                    </div>
                    <div>
                        <h1
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            Mentoring Hub
                        </h1>
                        <p class="font-medium text-slate-400">
                            Conectando el conocimiento con el potencial.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="group relative">
                    <PhMagnifyingGlass
                        :size="18"
                        class="absolute top-1/2 left-4 -translate-y-1/2 text-slate-500 transition-colors group-focus-within:text-amber-400"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar mentores por rol o nombre..."
                        class="w-full rounded-2xl border border-white/10 bg-white/5 py-3 pr-6 pl-11 text-sm text-white backdrop-blur-sm transition-all placeholder:text-slate-600 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/50 focus:outline-none md:w-80"
                    />
                </div>
                <StButtonGlass variant="primary" :icon="PhSparkle">
                    Encontrar Match IA
                </StButtonGlass>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <StCardGlass
                v-for="stat in stats"
                :key="stat.label"
                class="group !p-6 transition-transform hover:translate-y-[-4px]"
            >
                <div class="flex items-center gap-4">
                    <div :class="['rounded-xl bg-white/5 p-3', stat.color]">
                        <component
                            :is="stat.icon"
                            :size="24"
                            weight="duotone"
                        />
                    </div>
                    <div>
                        <p
                            class="text-xs font-bold tracking-widest text-slate-500 uppercase"
                        >
                            {{ stat.label }}
                        </p>
                        <p
                            class="text-2xl font-black tracking-tight text-white italic"
                        >
                            {{ stat.value }}
                        </p>
                    </div>
                </div>
            </StCardGlass>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column: Mentors -->
            <div class="space-y-6 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h2
                        class="flex items-center gap-2 text-xl font-black tracking-tight text-white italic"
                    >
                        <PhSelection :size="20" class="text-amber-400" />
                        MENTORES RECOMENDADOS
                    </h2>
                    <StBadgeGlass variant="glass">Top Matches</StBadgeGlass>
                </div>

                <div
                    v-if="loading"
                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                >
                    <div
                        v-for="i in 4"
                        :key="i"
                        class="h-48 animate-pulse rounded-3xl border border-white/5 bg-white/5"
                    />
                </div>

                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <MentorCard
                        v-for="mentor in filteredMentors"
                        :key="mentor.id"
                        :mentor="mentor"
                        @request="openSessionDialog(mentor.id)"
                    />
                </div>
            </div>

            <!-- Right Column: Active Sessions -->
            <div class="space-y-6">
                <h2
                    class="flex items-center gap-2 text-xl font-black tracking-tight text-white uppercase italic"
                >
                    <PhTrendUp :size="20" class="text-emerald-400" />
                    Tus Mentorías
                </h2>

                <div class="space-y-4">
                    <StCardGlass
                        v-for="session in activeSessions"
                        :key="session.id"
                        class="!p-5"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-white">
                                    {{ session.topic }}
                                </h3>
                                <p
                                    class="mt-0.5 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >
                                    Mentor: {{ session.mentor_name }}
                                </p>
                            </div>
                            <div
                                class="rounded-lg bg-emerald-500/10 p-2 text-emerald-400"
                            >
                                <PhGraduationCap :size="16" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                class="flex justify-between text-[10px] font-bold"
                            >
                                <span class="text-slate-500">PROGRESO</span>
                                <span class="text-white"
                                    >{{ session.progress }}%</span
                                >
                            </div>
                            <div
                                class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                            >
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400"
                                    :style="{ width: session.progress + '%' }"
                                />
                            </div>
                            <div
                                class="mt-4 flex items-center gap-2 text-[11px] font-medium text-slate-400"
                            >
                                <PhCalendar
                                    :size="14"
                                    class="text-emerald-400"
                                />
                                Prox. Sesión: {{ session.next_date }}
                            </div>
                        </div>

                        <div
                            class="mt-4 flex gap-2 border-t border-white/5 pt-4"
                        >
                            <StButtonGlass size="xs" variant="ghost" block
                                >Materiales</StButtonGlass
                            >
                            <StButtonGlass
                                size="xs"
                                variant="primary"
                                block
                                @click="openSessionDialog(session.id)"
                                >Registrar Log</StButtonGlass
                            >
                        </div>
                    </StCardGlass>

                    <div
                        v-if="activeSessions.length === 0"
                        class="rounded-3xl border border-dashed border-white/10 py-12 text-center"
                    >
                        <PhUsers
                            :size="32"
                            class="mx-auto mb-3 text-slate-700"
                        />
                        <p class="text-sm font-medium text-slate-500">
                            No tienes mentorías activas.
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            ¡Inicia una conexión hoy mismo!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <MentorshipSessionDialog
        ref="sessionDialog"
        :action-id="selectedActionId"
    />
</template>

<style scoped>
.italic {
    font-style: italic;
}
</style>

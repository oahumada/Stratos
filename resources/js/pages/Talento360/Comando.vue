<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    PhBuildings,
    PhCalendar,
    PhCalendarBlank,
    PhCalendarCheck,
    PhCaretLeft,
    PhCaretRight,
    PhChartBar,
    PhChartDonut,
    PhCheckCircle,
    PhClockClockwise,
    PhDotsThreeVertical,
    PhFlask,
    PhHeartbeat,
    PhInfinity,
    PhLightning,
    PhMagnifyingGlass,
    PhPlus,
    PhPulse,
    PhRobot,
    PhRocketLaunch,
    PhTreeStructure,
    PhUsers,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import StButtonGlass from '../../components/StButtonGlass.vue';
import { usePermissions } from '../../composables/usePermissions';

const { t } = useI18n();

const { can } = usePermissions();

interface AssessmentCycle {
    id: number;
    name: string;
    description: string | null;
    mode: 'specific_date' | 'quarterly' | 'annual' | 'continuous';
    status: 'draft' | 'scheduled' | 'active' | 'completed' | 'cancelled';
    schedule_config: Record<string, any>;
    scope: { type: string; ids: number[] };
    evaluators: {
        self: boolean;
        manager: boolean;
        peers: number;
        reports: boolean;
        ai: boolean;
    };
    instruments: string[];
    starts_at: string | null;
    ends_at: string | null;
    completion_rate?: number;
    created_at: string;
}

const cycles = ref<AssessmentCycle[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);
const step = ref(1);
const search = ref('');

// Stats
const stats = computed(() => {
    return {
        active: cycles.value.filter((c) => c.status === 'active').length,
        total_participants: 1240, // Mock for now
        avg_completion: 68, // Mock
        pending_evals: 450, // Mock
    };
});

const newCycle = ref<
    Partial<AssessmentCycle> & { notifications?: Record<string, boolean> }
>({
    name: '',
    description: '',
    mode: 'quarterly',
    scope: { type: 'all', ids: [] },
    evaluators: {
        self: true,
        manager: true,
        peers: 2,
        reports: true,
        ai: true,
    },
    instruments: ['bars', 'pulse'],
    schedule_config: { quarter: 1, year: 2026 },
    notifications: { email: true, in_app: true },
    status: 'draft',
    starts_at: null,
    ends_at: null,
});

const modeOptions = computed(() => [
    {
        title: t('assessment_command.modes.specific_date.title'),
        value: 'specific_date',
        icon: PhCalendarCheck,
        desc: t('assessment_command.modes.specific_date.desc'),
    },
    {
        title: t('assessment_command.modes.quarterly.title'),
        value: 'quarterly',
        icon: PhCalendarBlank,
        desc: t('assessment_command.modes.quarterly.desc'),
    },
    {
        title: t('assessment_command.modes.annual.title'),
        value: 'annual',
        icon: PhCalendar, // Simplified or could be PhCake
        desc: t('assessment_command.modes.annual.desc'),
    },
    {
        title: t('assessment_command.modes.continuous.title'),
        value: 'continuous',
        icon: PhInfinity,
        desc: t('assessment_command.modes.continuous.desc'),
    },
]);

const instrumentOptions = computed(() => [
    {
        title: t('assessment_command.instruments.bars'),
        value: 'bars',
        icon: PhChartBar,
    },
    {
        title: t('assessment_command.instruments.pulse'),
        value: 'pulse',
        icon: PhPulse,
    },
    {
        title: t('assessment_command.instruments.disc'),
        value: 'disc',
        icon: PhFlask,
    },
    {
        title: t('assessment_command.instruments.interview'),
        value: 'interview',
        icon: PhRobot,
    },
]);

const loadCycles = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/assessment-cycles');
        cycles.value = data.data;
    } catch (e) {
        console.error('Failed to load assessment cycles', e);
    } finally {
        loading.value = false;
    }
};

const saveCycle = async () => {
    saving.value = true;
    try {
        await axios.post('/api/assessment-cycles', newCycle.value);
        wizardDialog.value = false;
        resetWizard();
        loadCycles();
    } catch (e) {
        console.error('Failed to save cycle', e);
    } finally {
        saving.value = false;
    }
};

const activateCycle = async (id: number) => {
    loading.value = true;
    try {
        await axios.put(`/api/assessment-cycles/${id}`, { status: 'active' });
        loadCycles();
    } catch (e) {
        console.error('Failed to activate cycle', e);
    } finally {
        loading.value = false;
    }
};

const resetWizard = () => {
    step.value = 1;
    newCycle.value = {
        name: '',
        description: '',
        mode: 'quarterly',
        scope: { type: 'all', ids: [] },
        evaluators: {
            self: true,
            manager: true,
            peers: 2,
            reports: true,
            ai: true,
        },
        instruments: ['bars', 'pulse'],
        schedule_config: { quarter: 1, year: 2026 },
        notifications: { email: true, in_app: true },
        status: 'draft',
    };
};

const previewLoading = ref(false);
const previewData = ref({
    participants: 0,
    impacted_areas: 0,
});

watch(step, (newStep) => {
    if (newStep === 4) {
        previewLoading.value = true;
        setTimeout(() => {
            const scopeType = newCycle.value.scope?.type;
            if (scopeType === 'all') {
                previewData.value = { participants: 184, impacted_areas: 6 };
            } else if (scopeType === 'scenario') {
                previewData.value = { participants: 42, impacted_areas: 2 };
            } else if (scopeType === 'department') {
                previewData.value = { participants: 18, impacted_areas: 1 };
            } else {
                previewData.value = { participants: 8, impacted_areas: 1 };
            }
            previewLoading.value = false;
        }, 1500);
    }
});

const formatDate = (date: string | null) => {
    if (!date) return t('assessment_command.date_none');
    return format(new Date(date), 'dd MMM yyyy', { locale: es });
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey-lighten-1',
        scheduled: 'cyan-accent-3',
        active: 'green-accent-3',
        completed: 'indigo-accent-2',
        cancelled: 'red-accent-2',
    };
    return map[status] || 'grey';
};

onMounted(() => {
    loadCycles();
});
</script>

<template>
    <div class="pa-0 pa-md-6 min-vh-100">
        <!-- Header Section -->
        <div
            class="mb-10 flex flex-col items-start justify-between gap-6 px-4 md:flex-row md:items-center md:px-0"
        >
            <div>
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-500/30 bg-indigo-500/10 text-indigo-400"
                    >
                        <PhPulse :size="24" weight="bold" />
                    </div>
                    <h1 class="text-4xl font-black tracking-tighter text-white">
                        {{ t('assessment_command.title') }}
                        <span
                            class="bg-linear-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent"
                            >Cerbero</span
                        >
                    </h1>
                </div>
                <p class="mt-2 text-lg font-medium text-white/40">
                    {{ t('assessment_command.subtitle') }}z
                </p>
            </div>

            <StButtonGlass
                v-if="can('assessments.manage')"
                :icon="PhRocketLaunch"
                variant="primary"
                size="lg"
                class="shadow-xl shadow-indigo-500/20"
                @click="wizardDialog = true"
            >
                {{ t('assessment_command.config_cycle') }}
            </StButtonGlass>
        </div>

        <!-- Stats Dashboard Row -->
        <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Active Cycles -->
            <div
                class="group relative overflow-hidden rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur-xl transition-all duration-300 hover:border-indigo-500/30 hover:bg-white/10"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-400 transition-transform duration-500 group-hover:scale-110"
                    >
                        <PhClockClockwise :size="30" weight="duotone" />
                    </div>
                    <div>
                        <div
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            {{ stats.active }}
                        </div>
                        <div
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            {{ t('assessment_command.stats.active_cycles') }}
                        </div>
                    </div>
                </div>
                <!-- Decorative background element -->
                <div
                    class="absolute -right-4 -bottom-4 h-20 w-20 rounded-full bg-indigo-500/5 blur-2xl transition-all duration-500 group-hover:bg-indigo-500/10"
                />
            </div>

            <!-- Collaborators -->
            <div
                class="group relative overflow-hidden rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur-xl transition-all duration-300 hover:border-emerald-500/30 hover:bg-white/10"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 transition-transform duration-500 group-hover:scale-110"
                    >
                        <PhUsers :size="30" weight="duotone" />
                    </div>
                    <div>
                        <div
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            {{ stats.total_participants }}
                        </div>
                        <div
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            {{ t('assessment_command.stats.collaborators') }}
                        </div>
                    </div>
                </div>
                <!-- Decorative background element -->
                <div
                    class="absolute -right-4 -bottom-4 h-20 w-20 rounded-full bg-emerald-500/5 blur-2xl transition-all duration-500 group-hover:bg-emerald-500/10"
                />
            </div>

            <!-- Avg. Completion -->
            <div
                class="group relative overflow-hidden rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur-xl transition-all duration-300 hover:border-cyan-500/30 hover:bg-white/10"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-cyan-500/20 bg-cyan-500/10 text-cyan-400 transition-transform duration-500 group-hover:scale-110"
                    >
                        <PhChartDonut :size="30" weight="duotone" />
                    </div>
                    <div class="min-w-0 grow">
                        <div class="mb-1 flex items-end justify-between">
                            <div
                                class="text-3xl font-black tracking-tight text-white"
                            >
                                {{ stats.avg_completion }}%
                            </div>
                        </div>
                        <div
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            {{ t('assessment_command.stats.avg_completion') }}
                        </div>
                        <div
                            class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                        >
                            <div
                                class="h-full rounded-full bg-linear-to-r from-cyan-500 to-indigo-500 transition-all duration-1000"
                                :style="{
                                    width: `${stats.avg_completion}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
                <!-- Decorative background element -->
                <div
                    class="absolute -right-4 -bottom-4 h-20 w-20 rounded-full bg-cyan-500/5 blur-2xl transition-all duration-500 group-hover:bg-cyan-500/10"
                />
            </div>

            <!-- Pending -->
            <div
                class="group relative overflow-hidden rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur-xl transition-all duration-300 hover:border-amber-500/30 hover:bg-white/10"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-amber-500/20 bg-amber-500/10 text-amber-400 transition-transform duration-500 group-hover:scale-110"
                    >
                        <PhHeartbeat :size="30" weight="duotone" />
                    </div>
                    <div>
                        <div
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            {{ stats.pending_evals }}
                        </div>
                        <div
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            {{ t('assessment_command.stats.pending_evals') }}
                        </div>
                    </div>
                </div>
                <!-- Decorative background element -->
                <div
                    class="absolute -right-4 -bottom-4 h-20 w-20 rounded-full bg-amber-500/5 blur-2xl transition-all duration-500 group-hover:bg-amber-500/10"
                />
            </div>
        </div>

        <!-- Main Listing Row -->
        <div
            class="overflow-hidden rounded-[2.5rem] border border-white/10 bg-slate-900/40 shadow-2xl backdrop-blur-3xl"
        >
            <!-- Toolbar -->
            <div
                class="flex flex-col items-center justify-between gap-4 border-b border-white/5 px-8 py-6 md:flex-row"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-400"
                    >
                        <PhClockClockwise :size="22" weight="bold" />
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-white">
                        {{ t('assessment_command.history_title') }}
                    </h2>
                </div>

                <div class="flex w-full items-center gap-4 md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <PhMagnifyingGlass
                            :size="18"
                            class="absolute top-1/2 left-4 -translate-y-1/2 text-white/20"
                        />
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="
                                t('assessment_command.search_placeholder')
                            "
                            class="w-full rounded-2xl border border-white/10 bg-white/5 py-2.5 pr-4 pl-11 text-sm text-white transition-all focus:border-indigo-500/50 focus:outline-none"
                        />
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        :icon="PhDotsThreeVertical"
                        circle
                        size="sm"
                    />
                </div>
            </div>

            <!-- Custom Table / List -->
            <div class="custom-scrollbar overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="border-b border-white/5 bg-white/5 text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            <th class="px-8 py-4">
                                {{ t('assessment_command.headers.id') }}
                            </th>
                            <th class="px-8 py-4">
                                {{ t('assessment_command.headers.mode') }}
                            </th>
                            <th class="px-8 py-4">
                                {{ t('assessment_command.headers.progress') }}
                            </th>
                            <th class="px-8 py-4">
                                {{ t('assessment_command.headers.status') }}
                            </th>
                            <th class="px-8 py-4">
                                {{ t('assessment_command.headers.launch') }}
                            </th>
                            <th class="px-8 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="item in cycles"
                            :key="item.id"
                            class="group transition-colors hover:bg-white/5"
                        >
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-white transition-colors group-hover:text-indigo-400"
                                    >
                                        {{ item.name }}
                                    </span>
                                    <span
                                        class="max-w-[200px] truncate text-[10px] text-white/30"
                                    >
                                        {{
                                            item.description ||
                                            'Sin descripción'
                                        }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/5 text-white/40"
                                    >
                                        <component
                                            :is="
                                                modeOptions.find(
                                                    (o) =>
                                                        o.value === item.mode,
                                                )?.icon || PhCalendar
                                            "
                                            :size="14"
                                        />
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-white/60"
                                    >
                                        {{
                                            modeOptions.find(
                                                (o) => o.value === item.mode,
                                            )?.title || item.mode
                                        }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div
                                    class="flex min-w-[150px] items-center gap-3"
                                >
                                    <div
                                        class="h-1.5 flex-1 overflow-hidden rounded-full bg-white/5"
                                    >
                                        <div
                                            class="h-full rounded-full bg-emerald-500 transition-all duration-1000"
                                            :style="{
                                                width: `${item.completion_rate || 0}%`,
                                            }"
                                        />
                                    </div>
                                    <span
                                        class="text-xs font-black text-white/40"
                                    >
                                        {{ item.completion_rate || 0 }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <StBadgeGlass
                                    :variant="getStatusColor(item.status)"
                                    size="sm"
                                >
                                    {{ item.status }}
                                </StBadgeGlass>
                            </td>
                            <td class="px-8 py-5">
                                <div
                                    class="flex items-center gap-2 text-xs font-medium text-white/40"
                                >
                                    <PhCalendar :size="14" />
                                    {{ formatDate(item.starts_at) }}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div
                                    class="flex justify-end gap-1 opacity-100 transition-opacity group-hover:opacity-100 md:opacity-0"
                                >
                                    <StButtonGlass
                                        v-if="
                                            item.status === 'draft' ||
                                            item.status === 'scheduled'
                                        "
                                        variant="ghost"
                                        :icon="PhRocketLaunch"
                                        circle
                                        size="sm"
                                        class="hover:text-emerald-400"
                                        @click="activateCycle(item.id)"
                                    />
                                    <StButtonGlass
                                        v-if="item.status === 'active'"
                                        variant="ghost"
                                        :icon="PhChartBar"
                                        circle
                                        size="sm"
                                        class="hover:text-indigo-400"
                                        @click="
                                            router.visit(
                                                '/talento360/dashboard',
                                            )
                                        "
                                    />
                                    <StButtonGlass
                                        variant="ghost"
                                        :icon="PhDotsThreeVertical"
                                        circle
                                        size="sm"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="loading" class="flex justify-center py-20">
                    <div
                        class="h-10 w-10 animate-spin rounded-full border-2 border-indigo-500/20 border-t-indigo-500"
                    />
                </div>

                <div
                    v-else-if="cycles.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="mb-4 text-white/10">
                        <PhRocketLaunch :size="64" weight="thin" />
                    </div>
                    <p class="text-lg font-bold text-white/20">
                        {{ t('assessment_command.no_data') }}
                    </p>
                    <StButtonGlass
                        variant="primary"
                        :icon="PhPlus"
                        class="mt-6"
                        @click="wizardDialog = true"
                    >
                        {{ t('assessment_command.launch_first') }}
                    </StButtonGlass>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Wizard -->
    <v-dialog
        v-model="wizardDialog"
        max-width="1000"
        persistent
        transition="dialog-bottom-transition"
    >
        <v-card
            class="glass-card border-auth overflow-hidden rounded-[32px]! shadow-2xl"
            height="700"
        >
            <div class="flex h-full flex-col overflow-hidden md:flex-row">
                <!-- Sidebar Navigation -->
                <div
                    class="flex w-full flex-col border-b border-white/5 bg-black/20 p-8 md:w-80 md:border-r md:border-b-0"
                >
                    <div class="mb-10">
                        <div
                            class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-400"
                        >
                            <PhRocketLaunch :size="32" weight="duotone" />
                        </div>
                        <h3
                            class="mb-1 text-2xl font-black tracking-tight text-white"
                        >
                            {{ t('assessment_command.wizard.title') }}
                        </h3>
                        <p
                            class="text-xs font-medium tracking-widest text-white/30 uppercase"
                        >
                            {{ t('assessment_command.wizard.subtitle') }}
                        </p>
                    </div>

                    <nav class="grow space-y-2">
                        <button
                            v-for="n in 4"
                            :key="n"
                            @click="step = n"
                            :disabled="step < n"
                            class="group flex w-full items-center gap-4 rounded-2xl p-4 transition-all duration-300"
                            :class="[
                                step === n
                                    ? 'bg-indigo-500/10 text-indigo-400 shadow-lg'
                                    : step < n
                                      ? 'cursor-not-allowed opacity-30 grayscale'
                                      : 'text-white/40 hover:bg-white/5',
                            ]"
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl border text-lg font-black transition-colors"
                                :class="[
                                    step === n
                                        ? 'border-indigo-500/50 bg-indigo-500 text-white'
                                        : 'border-white/10 bg-white/5 group-hover:border-white/20',
                                ]"
                            >
                                {{ n }}
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-bold tracking-tight">
                                    {{
                                        t(
                                            `assessment_command.wizard.step${n}.label`,
                                        )
                                    }}
                                </div>
                                <div
                                    class="text-[10px] font-medium tracking-widest uppercase opacity-50"
                                >
                                    Fase {{ n }}
                                </div>
                            </div>
                        </button>
                    </nav>

                    <div class="mt-auto pt-8">
                        <StButtonGlass
                            variant="ghost"
                            block
                            :icon="PhCaretLeft"
                            @click="wizardDialog = false"
                        >
                            {{ t('assessment_command.wizard.actions.cancel') }}
                        </StButtonGlass>
                    </div>
                </div>

                <!-- Content Area -->
                <div
                    class="relative flex h-full flex-col overflow-hidden bg-slate-950/50 md:flex-1"
                >
                    <!-- Decorative Background -->
                    <div
                        class="pointer-events-none absolute inset-0 overflow-hidden"
                    >
                        <div
                            class="absolute -top-24 -right-24 h-96 w-96 animate-pulse rounded-full bg-indigo-500/20 blur-[130px]"
                        ></div>
                        <div
                            class="absolute -bottom-24 -left-24 h-96 w-96 animate-pulse rounded-full bg-blue-500/20 blur-[130px]"
                            style="animation-delay: 1s"
                        ></div>
                    </div>

                    <div class="relative grow overflow-y-auto p-8 lg:p-12">
                        <v-window v-model="step" class="h-full bg-transparent">
                            <!-- Step 1: Identity -->
                            <v-window-item
                                :value="1"
                                class="h-full transition-all duration-500"
                            >
                                <div class="mb-10">
                                    <div
                                        class="mb-2 text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step1.label',
                                            )
                                        }}
                                    </div>
                                    <h2
                                        class="mb-4 text-4xl font-black tracking-tight text-white lg:text-5xl"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step1.title',
                                            )
                                        }}
                                    </h2>
                                    <p
                                        class="max-w-xl text-lg leading-relaxed font-medium text-white/40"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step1.desc',
                                            )
                                        }}
                                    </p>
                                </div>

                                <div class="grid gap-8 lg:grid-cols-1">
                                    <div class="space-y-6">
                                        <div>
                                            <div
                                                class="mb-2 block text-xs font-bold tracking-widest text-white/30 uppercase"
                                            >
                                                {{
                                                    t(
                                                        'assessment_command.wizard.step1.name_label',
                                                    )
                                                }}
                                            </div>
                                            <input
                                                v-model="newCycle.name"
                                                type="text"
                                                class="block w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-white transition-all placeholder:text-white/10 focus:border-indigo-500/50 focus:bg-white/10 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                                                :placeholder="
                                                    t(
                                                        'assessment_command.wizard.step1.name_placeholder',
                                                    )
                                                "
                                            />
                                        </div>

                                        <div>
                                            <div
                                                class="mb-2 block text-xs font-bold tracking-widest text-white/30 uppercase"
                                            >
                                                {{
                                                    t(
                                                        'assessment_command.wizard.step1.description_label',
                                                    )
                                                }}
                                            </div>
                                            <textarea
                                                v-model="newCycle.description"
                                                rows="3"
                                                class="block w-full resize-none rounded-2xl border border-white/10 bg-white/5 p-4 text-white transition-all placeholder:text-white/10 focus:border-indigo-500/50 focus:bg-white/10 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                                                :placeholder="
                                                    t(
                                                        'assessment_command.wizard.step1.description_placeholder',
                                                    )
                                                "
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <h3
                                            class="mb-6 text-sm font-bold tracking-widest text-white/30 uppercase"
                                        >
                                            {{
                                                t(
                                                    'assessment_command.wizard.step1.mode_title',
                                                )
                                            }}
                                        </h3>
                                        <div
                                            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <button
                                                v-for="mode in modeOptions"
                                                :key="mode.value"
                                                @click="
                                                    newCycle.mode =
                                                        mode.value as any
                                                "
                                                class="group relative flex items-center gap-5 rounded-2xl border p-5 text-left transition-all duration-300"
                                                :class="[
                                                    newCycle.mode === mode.value
                                                        ? 'border-indigo-500/50 bg-indigo-500/10 ring-4 ring-indigo-500/10'
                                                        : 'bg-white-opacity-5 hover:bg-white-opacity-10 border-white/5 hover:border-white/20',
                                                ]"
                                            >
                                                <div
                                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-all duration-300"
                                                    :class="[
                                                        newCycle.mode ===
                                                        mode.value
                                                            ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30'
                                                            : 'bg-white/5 text-white/40 group-hover:bg-white/10 group-hover:text-white/60',
                                                    ]"
                                                >
                                                    <component
                                                        :is="mode.icon"
                                                        :size="24"
                                                        weight="duotone"
                                                    />
                                                </div>
                                                <div>
                                                    <div
                                                        class="mb-1 text-base font-black tracking-tight text-white"
                                                    >
                                                        {{ mode.title }}
                                                    </div>
                                                    <div
                                                        class="text-xs leading-snug font-medium text-white/30"
                                                    >
                                                        {{ mode.desc }}
                                                    </div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </v-window-item>

                            <!-- Step 2: Scope -->
                            <v-window-item :value="2" class="h-full">
                                <div class="mb-10">
                                    <div
                                        class="mb-2 text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step2.label',
                                            )
                                        }}
                                    </div>
                                    <h2
                                        class="mb-4 text-4xl font-black tracking-tight text-white lg:text-5xl"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step2.title',
                                            )
                                        }}
                                    </h2>
                                    <p
                                        class="max-w-xl text-lg leading-relaxed font-medium text-white/40"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step2.desc',
                                            )
                                        }}
                                    </p>
                                </div>

                                <div class="space-y-10">
                                    <div>
                                        <div
                                            class="mb-4 block text-xs font-bold tracking-widest text-white/30 uppercase"
                                        >
                                            {{
                                                t(
                                                    'assessment_command.wizard.step2.segmentation_label',
                                                )
                                            }}
                                        </div>
                                        <div
                                            class="grid grid-cols-2 gap-4 sm:grid-cols-4"
                                        >
                                            <button
                                                v-for="opt in [
                                                    {
                                                        value: 'all',
                                                        icon: PhUsers,
                                                    },
                                                    {
                                                        value: 'department',
                                                        icon: PhBuildings,
                                                    },
                                                    {
                                                        value: 'scenario',
                                                        icon: PhTreeStructure,
                                                    },
                                                    {
                                                        value: 'hipo',
                                                        icon: PhLightning,
                                                    },
                                                ]"
                                                :key="opt.value"
                                                @click="
                                                    newCycle.scope!.type =
                                                        opt.value as any
                                                "
                                                class="flex flex-col items-center gap-3 rounded-2xl border p-4 transition-all duration-300"
                                                :class="[
                                                    newCycle.scope!.type ===
                                                    opt.value
                                                        ? 'border-indigo-500/50 bg-indigo-500/10 text-indigo-400 ring-4 ring-indigo-500/10'
                                                        : 'border-white/5 bg-white/5 text-white/40 hover:border-white/20 hover:bg-white/10',
                                                ]"
                                            >
                                                <component
                                                    :is="opt.icon"
                                                    :size="24"
                                                    weight="duotone"
                                                />
                                                <span
                                                    class="text-[11px] font-black tracking-wider uppercase"
                                                >
                                                    {{
                                                        t(
                                                            `assessment_command.wizard.step2.segmentation_options.${opt.value}`,
                                                        )
                                                    }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <transition name="fade-slide">
                                        <div
                                            v-if="
                                                newCycle.scope!.type ===
                                                'scenario'
                                            "
                                            class="rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-6 backdrop-blur-xl"
                                        >
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-400"
                                                >
                                                    <PhRocketLaunch
                                                        :size="20"
                                                        weight="duotone"
                                                    />
                                                </div>
                                                <div
                                                    class="text-sm font-medium text-white/60"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step2.scenario_alert',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </transition>

                                    <div>
                                        <div
                                            class="mb-4 block text-xs font-bold tracking-widest text-white/30 uppercase"
                                        >
                                            {{
                                                t(
                                                    'assessment_command.wizard.step2.schedule_title',
                                                )
                                            }}
                                        </div>
                                        <div
                                            class="grid grid-cols-1 gap-6 sm:grid-cols-2"
                                        >
                                            <div class="space-y-2">
                                                <div
                                                    class="ml-1 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step2.start_date',
                                                        )
                                                    }}
                                                </div>
                                                <input
                                                    v-model="newCycle.starts_at"
                                                    type="date"
                                                    class="block w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-white transition-all focus:border-indigo-500/50 focus:bg-white/10 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                                                />
                                            </div>
                                            <div class="space-y-2">
                                                <div
                                                    class="ml-1 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step2.end_date',
                                                        )
                                                    }}
                                                </div>
                                                <input
                                                    v-model="newCycle.ends_at"
                                                    type="date"
                                                    class="block w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-white transition-all focus:border-indigo-500/50 focus:bg-white/10 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </v-window-item>

                            <!-- Step 3: Instruments & Red -->
                            <v-window-item
                                :value="3"
                                class="h-full transition-all duration-500"
                            >
                                <div class="mb-10">
                                    <div
                                        class="mb-2 text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step3.label',
                                            )
                                        }}
                                    </div>
                                    <h2
                                        class="mb-4 text-4xl font-black tracking-tight text-white lg:text-5xl"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step3.title',
                                            )
                                        }}
                                    </h2>
                                    <p
                                        class="max-w-xl text-lg leading-relaxed font-medium text-white/40"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step3.desc',
                                            )
                                        }}
                                    </p>
                                </div>

                                <div class="space-y-10">
                                    <div>
                                        <h3
                                            class="mb-6 text-sm font-bold tracking-widest text-white/30 uppercase"
                                        >
                                            {{
                                                t(
                                                    'assessment_command.wizard.step3.instruments_title',
                                                )
                                            }}
                                        </h3>
                                        <div
                                            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <div
                                                v-for="inst in instrumentOptions"
                                                :key="inst.value"
                                                class="flex items-center gap-4 rounded-2xl border border-white/5 bg-white/5 p-4 transition-all hover:border-white/20 hover:bg-white/10"
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="
                                                        newCycle.instruments
                                                    "
                                                    :value="inst.value"
                                                    class="h-5 w-5 rounded border-white/20 bg-white/10 text-indigo-500 transition-all focus:ring-indigo-500/50"
                                                />
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <component
                                                        :is="inst.icon"
                                                        :size="20"
                                                        class="text-indigo-400"
                                                        weight="duotone"
                                                    />
                                                    <span
                                                        class="text-sm font-bold text-white"
                                                        >{{ inst.title }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h3
                                            class="mb-6 text-sm font-bold tracking-widest text-white/30 uppercase"
                                        >
                                            {{
                                                t(
                                                    'assessment_command.wizard.step3.network_title',
                                                )
                                            }}
                                        </h3>
                                        <div
                                            class="rounded-3xl border border-white/5 bg-white/5 p-8"
                                        >
                                            <div
                                                class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4"
                                            >
                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/40"
                                                        >
                                                            <PhUser
                                                                :size="18"
                                                                weight="duotone"
                                                            />
                                                        </div>
                                                        <span
                                                            class="text-xs font-bold tracking-wider text-white uppercase"
                                                            >{{
                                                                t(
                                                                    'assessment_command.wizard.step3.network_options.self',
                                                                )
                                                            }}</span
                                                        >
                                                    </div>
                                                    <input
                                                        type="checkbox"
                                                        v-model="
                                                            newCycle.evaluators!
                                                                .self
                                                        "
                                                        class="toggle-premium"
                                                    />
                                                </div>

                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/40"
                                                        >
                                                            <PhUser
                                                                :size="18"
                                                                weight="duotone"
                                                            />
                                                        </div>
                                                        <span
                                                            class="text-xs font-bold tracking-wider text-white uppercase"
                                                            >{{
                                                                t(
                                                                    'assessment_command.wizard.step3.network_options.manager',
                                                                )
                                                            }}</span
                                                        >
                                                    </div>
                                                    <input
                                                        type="checkbox"
                                                        v-model="
                                                            newCycle.evaluators!
                                                                .manager
                                                        "
                                                        class="toggle-premium"
                                                    />
                                                </div>

                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/40"
                                                        >
                                                            <PhUser
                                                                :size="18"
                                                                weight="duotone"
                                                            />
                                                        </div>
                                                        <span
                                                            class="text-xs font-bold tracking-wider text-white uppercase"
                                                            >{{
                                                                t(
                                                                    'assessment_command.wizard.step3.network_options.reports',
                                                                )
                                                            }}</span
                                                        >
                                                    </div>
                                                    <input
                                                        type="checkbox"
                                                        v-model="
                                                            newCycle.evaluators!
                                                                .reports
                                                        "
                                                        class="toggle-premium"
                                                    />
                                                </div>

                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/40"
                                                        >
                                                            <PhRobot
                                                                :size="18"
                                                                weight="duotone"
                                                            />
                                                        </div>
                                                        <span
                                                            class="text-xs font-bold tracking-wider text-white uppercase"
                                                            >{{
                                                                t(
                                                                    'assessment_command.wizard.step3.network_options.ai',
                                                                )
                                                            }}</span
                                                        >
                                                    </div>
                                                    <input
                                                        type="checkbox"
                                                        v-model="
                                                            newCycle.evaluators!
                                                                .ai
                                                        "
                                                        class="toggle-premium"
                                                    />
                                                </div>
                                            </div>

                                            <div
                                                class="mt-10 border-t border-white/5 pt-10"
                                            >
                                                <div
                                                    class="mb-6 flex items-center justify-between"
                                                >
                                                    <div
                                                        class="text-sm font-bold text-white"
                                                    >
                                                        {{
                                                            t(
                                                                'assessment_command.wizard.step3.peers_count_label',
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-2xl font-black text-indigo-400"
                                                    >
                                                        {{
                                                            newCycle.evaluators!
                                                                .peers
                                                        }}
                                                    </div>
                                                </div>
                                                <input
                                                    v-model.number="
                                                        newCycle.evaluators!
                                                            .peers
                                                    "
                                                    type="range"
                                                    min="0"
                                                    max="8"
                                                    step="1"
                                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-white/10 accent-indigo-500"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </v-window-item>

                            <!-- Step 4: Confirmation -->
                            <v-window-item
                                :value="4"
                                class="h-full transition-all duration-500"
                            >
                                <div class="mb-12 text-center">
                                    <div
                                        class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-emerald-500/10 text-emerald-400 shadow-[0_0_50px_-12px_rgba(16,185,129,0.3)]"
                                    >
                                        <PhCheckCircle
                                            :size="64"
                                            weight="duotone"
                                        />
                                    </div>
                                    <h2
                                        class="mb-4 text-4xl font-black tracking-tight text-white lg:text-5xl"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step4.title',
                                            )
                                        }}
                                    </h2>
                                    <p
                                        class="mx-auto max-w-lg text-lg leading-relaxed font-medium text-white/40"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step4.desc',
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-6 lg:grid-cols-2"
                                >
                                    <div
                                        class="rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur-xl lg:col-span-2"
                                    >
                                        <div
                                            class="flex flex-wrap items-center gap-6"
                                        >
                                            <div class="grow">
                                                <div
                                                    class="mb-1 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.items.name',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xl font-black text-white italic"
                                                >
                                                    "{{ newCycle.name }}"
                                                </div>
                                            </div>
                                            <div
                                                class="h-10 w-px bg-white/5"
                                            ></div>
                                            <div>
                                                <div
                                                    class="mb-1 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.items.mode',
                                                        )
                                                    }}
                                                </div>
                                                <StBadgeGlass
                                                    variant="primary"
                                                    size="sm"
                                                >
                                                    {{
                                                        modeOptions.find(
                                                            (o) =>
                                                                o.value ===
                                                                newCycle.mode,
                                                        )?.title
                                                    }}
                                                </StBadgeGlass>
                                            </div>
                                            <div
                                                class="h-10 w-px bg-white/5"
                                            ></div>
                                            <div>
                                                <div
                                                    class="mb-1 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.items.instruments',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-lg font-black text-white"
                                                >
                                                    {{
                                                        newCycle.instruments
                                                            ?.length
                                                    }}
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.items.selected_suffix',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="rounded-3xl border border-white/5 bg-white/5 p-6"
                                    >
                                        <div
                                            class="mb-4 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                        >
                                            {{
                                                t(
                                                    'assessment_command.wizard.step4.preview_title',
                                                )
                                            }}
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div
                                                class="rounded-2xl bg-black/20 p-4 text-center"
                                            >
                                                <div
                                                    class="text-3xl font-black text-white"
                                                >
                                                    {{
                                                        previewData.participants
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[9px] font-bold tracking-wider text-white/30 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.preview_stats.participants',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                class="rounded-2xl bg-black/20 p-4 text-center"
                                            >
                                                <div
                                                    class="text-3xl font-black text-white"
                                                >
                                                    {{
                                                        previewData.impacted_areas
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[9px] font-bold tracking-wider text-white/30 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.preview_stats.areas',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between rounded-3xl border border-white/5 bg-white/5 p-6"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex h-12 w-12 items-center justify-center rounded-2xl"
                                                :class="
                                                    newCycle.evaluators?.ai
                                                        ? 'bg-emerald-500/10 text-emerald-400'
                                                        : 'bg-white/5 text-white/20'
                                                "
                                            >
                                                <PhRobot
                                                    :size="24"
                                                    weight="duotone"
                                                />
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-black text-white"
                                                >
                                                    {{
                                                        newCycle.evaluators?.ai
                                                            ? t(
                                                                  'assessment_command.wizard.step4.items.ai_active',
                                                              )
                                                            : t(
                                                                  'assessment_command.wizard.step4.items.ai_inactive',
                                                              )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[10px] font-bold tracking-wider text-white/30 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'assessment_command.wizard.step4.items.ai_engine',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="mt-8 flex items-center gap-4 rounded-2xl border border-amber-500/20 bg-amber-500/5 p-5"
                                >
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-amber-400"
                                    >
                                        <PhPulse :size="20" weight="duotone" />
                                    </div>
                                    <div
                                        class="text-sm leading-snug font-medium text-amber-200/60"
                                    >
                                        {{
                                            t(
                                                'assessment_command.wizard.step4.draft_alert',
                                            )
                                        }}
                                    </div>
                                </div>
                            </v-window-item>
                        </v-window>
                    </div>

                    <!-- Footer Actions -->
                    <div
                        class="mt-auto flex items-center justify-between border-t border-white/5 bg-black/20 p-8 lg:px-12"
                    >
                        <div>
                            <StButtonGlass
                                v-if="step > 1"
                                variant="ghost"
                                :icon="PhCaretLeft"
                                @click="step--"
                            >
                                {{
                                    t('assessment_command.wizard.actions.back')
                                }}
                            </StButtonGlass>
                        </div>

                        <div class="flex items-center gap-4">
                            <StButtonGlass
                                v-if="step < 4"
                                variant="primary"
                                size="lg"
                                class="min-w-[160px]"
                                @click="step++"
                            >
                                {{
                                    t('assessment_command.wizard.actions.next')
                                }}
                                <template #append>
                                    <PhCaretRight :size="18" weight="bold" />
                                </template>
                            </StButtonGlass>

                            <StButtonGlass
                                v-else
                                variant="secondary"
                                size="lg"
                                class="min-w-[200px]"
                                :loading="saving"
                                @click="saveCycle"
                            >
                                <template #prepend>
                                    <PhRocketLaunch :size="20" weight="bold" />
                                </template>
                                {{
                                    t(
                                        'assessment_command.wizard.actions.launch',
                                    )
                                }}
                            </StButtonGlass>
                        </div>
                    </div>
                </div>
            </div>
        </v-card>
    </v-dialog>
</template>

<style scoped>
.glass-card {
    background: rgba(30, 30, 36, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
}

.border-auth {
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.border-right-auth {
    border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.shadow-premium {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2) !important;
}

.tracking-tight {
    letter-spacing: -0.02em;
}

.v-list-wizard .v-list-item--active {
    background: rgba(var(--v-theme-primary), 0.15) !important;
    color: rgb(var(--v-theme-primary)) !important;
}

.bg-white-opacity-5 {
    background-color: rgba(255, 255, 255, 0.03) !important;
}

.transition-all {
    transition: all 0.3s ease;
}

.max-width-300 {
    max-width: 300px;
}

.bg-primary-darken-4 {
    background-color: #0d1117 !important;
}

.bg-surface-dark {
    background-color: #161b22 !important;
}

/* Premium Wizard Styles */
.toggle-premium {
    height: 1.5rem;
    width: 2.75rem;
    cursor: pointer;
    appearance: none;
    border-radius: 9999px;
    background-color: rgba(255, 255, 255, 0.1);
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.toggle-premium::before {
    content: '';
    position: absolute;
    top: 0.25rem;
    left: 0.25rem;
    height: 1rem;
    width: 1rem;
    border-radius: 9999px;
    background-color: rgba(255, 255, 255, 0.4);
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.toggle-premium:checked {
    border-color: rgba(99, 102, 241, 0.5);
    background-color: rgba(99, 102, 241, 0.4);
}

.toggle-premium:checked::before {
    left: 1.5rem;
    background-color: rgb(255, 255, 255);
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
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
    PhMagnifyingGlass,
    PhPulse,
    PhRobot,
    PhRocketLaunch,
    PhUserGroup,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import StButtonGlass from '../../components/StButtonGlass.vue';
import { usePermissions } from '../../composables/usePermissions';
import AppLayout from '../../layouts/AppLayout.vue';

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
    created_at: string;
}

const cycles = ref<AssessmentCycle[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);
const step = ref(1);

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
    <AppLayout>
        <v-container fluid class="pa-0 pa-md-6 min-vh-100">
            <!-- Header Section -->
            <div
                class="d-flex flex-column flex-md-row align-start align-md-center justify-space-between px-md-0 mb-8 px-4"
            >
                <div>
                    <h1
                        class="text-h3 font-weight-black font-premium mb-2 tracking-tight text-white"
                    >
                        {{ $t('assessment_command.title') }}
                        <span class="text-primary">Cerbero</span>
                    </h1>
                    <p class="text-h6 font-weight-regular text-grey-lighten-1">
                        {{ $t('assessment_command.subtitle') }}
                    </p>
                </div>

                <StButtonGlass
                    v-if="can('assessments.manage')"
                    :icon="PhRocketLaunch"
                    variant="flat"
                    color="primary"
                    height="56"
                    class="mt-md-0 mt-4 px-8"
                    @click="wizardDialog = true"
                >
                    {{ $t('assessment_command.config_cycle') }}
                </StButtonGlass>
            </div>

            <!-- Stats Dashboard Row -->
            <v-row class="mb-8">
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="primary-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <PhClockClockwise
                                    color="rgb(var(--v-theme-primary))"
                                    :size="32"
                                />
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ stats.active }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    {{
                                        $t(
                                            'assessment_command.stats.active_cycles',
                                        )
                                    }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="success-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <PhUserGroup
                                    color="rgb(var(--v-theme-success))"
                                    :size="32"
                                />
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ stats.total_participants }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    {{
                                        $t(
                                            'assessment_command.stats.collaborators',
                                        )
                                    }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="info-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <PhChartDonut
                                    color="rgb(var(--v-theme-info))"
                                    :size="32"
                                />
                            </v-avatar>
                            <div class="flex-grow-1">
                                <div
                                    class="d-flex align-end justify-space-between"
                                >
                                    <div
                                        class="text-h4 font-weight-black text-white"
                                    >
                                        {{ stats.avg_completion }}%
                                    </div>
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    {{
                                        $t(
                                            'assessment_command.stats.avg_completion',
                                        )
                                    }}
                                </div>
                                <v-progress-linear
                                    :model-value="stats.avg_completion"
                                    color="info"
                                    height="4"
                                    rounded
                                    class="mt-1"
                                ></v-progress-linear>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="warning-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <PhHeartbeat
                                    color="rgb(var(--v-theme-warning))"
                                    :size="32"
                                />
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ stats.pending_evals }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    {{
                                        $t(
                                            'assessment_command.stats.pending_evals',
                                        )
                                    }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Main Listing Row -->
            <v-row>
                <v-col cols="12">
                    <v-card
                        class="glass-card shadow-premium border-auth overflow-hidden"
                        elevation="0"
                    >
                        <v-toolbar color="transparent" class="px-4 py-2">
                            <v-toolbar-title
                                class="text-h6 font-weight-bold text-white"
                            >
                                <v-icon
                                    :icon="PhClockClockwise"
                                    color="primary"
                                    class="mr-2"
                                ></v-icon>
                                {{ $t('assessment_command.history_title') }}
                            </v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-text-field
                                v-model="search"
                                :placeholder="
                                    $t('assessment_command.search_placeholder')
                                "
                                variant="solo"
                                hide-details
                                density="compact"
                                class="max-width-300 mr-4"
                                rounded="lg"
                            >
                                <template #prepend-inner>
                                    <PhMagnifyingGlass
                                        :size="18"
                                        class="text-grey"
                                    />
                                </template>
                            </v-text-field>
                            <v-btn variant="text" color="grey">
                                <PhDotsThreeVertical :size="20" />
                            </v-btn>
                        </v-toolbar>

                        <v-divider color="white" class="opacity-10"></v-divider>

                        <v-data-table
                            :headers="[
                                {
                                    title: $t('assessment_command.headers.id'),
                                    key: 'name',
                                },
                                {
                                    title: $t(
                                        'assessment_command.headers.mode',
                                    ),
                                    key: 'mode',
                                },
                                {
                                    title: $t(
                                        'assessment_command.headers.progress',
                                    ),
                                    key: 'progress',
                                    sortable: false,
                                },
                                {
                                    title: $t(
                                        'assessment_command.headers.status',
                                    ),
                                    key: 'status',
                                },
                                {
                                    title: $t(
                                        'assessment_command.headers.launch',
                                    ),
                                    key: 'starts_at',
                                },
                                {
                                    title: '',
                                    key: 'actions',
                                    sortable: false,
                                    align: 'end',
                                },
                            ]"
                            :items="cycles"
                            :loading="loading"
                            class="bg-transparent"
                            theme="dark"
                        >
                            <template #[`item.name`]="{ item }">
                                <div class="py-4">
                                    <div
                                        class="text-body-1 font-weight-bold text-white"
                                    >
                                        {{ item.name }}
                                    </div>
                                    <div
                                        class="text-caption text-grey-lighten-1"
                                    >
                                        {{
                                            item.description ||
                                            'Sin descripción'
                                        }}
                                    </div>
                                </div>
                            </template>

                            <template #[`item.mode`]="{ item }">
                                <v-chip
                                    size="small"
                                    variant="outlined"
                                    color="primary-lighten-1"
                                    class="text-caption"
                                >
                                    <v-icon
                                        start
                                        :icon="
                                            modeOptions.find(
                                                (o) => o.value === item.mode,
                                            )?.icon || PhCalendar
                                        "
                                        size="14"
                                    ></v-icon>
                                    {{
                                        modeOptions.find(
                                            (o) => o.value === item.mode,
                                        )?.title || item.mode
                                    }}
                                </v-chip>
                            </template>

                            <template #[`item.progress`]="{ item }">
                                <div
                                    class="d-flex align-center"
                                    style="min-width: 150px"
                                >
                                    <v-progress-linear
                                        :model-value="item.completion_rate || 0"
                                        color="success"
                                        height="6"
                                        rounded
                                        class="mr-3"
                                    ></v-progress-linear>
                                    <span
                                        class="text-caption font-weight-bold text-white"
                                    >
                                        {{ item.completion_rate || 0 }}%
                                    </span>
                                </div>
                            </template>

                            <template #[`item.status`]="{ item }">
                                <v-chip
                                    :color="getStatusColor(item.status)"
                                    size="small"
                                    variant="flat"
                                    class="text-uppercase font-weight-black text-black"
                                >
                                    {{ item.status }}
                                </v-chip>
                            </template>

                            <template #[`item.starts_at`]="{ item }">
                                <div class="text-caption">
                                    <v-icon
                                        icon="mdi-calendar"
                                        size="14"
                                        class="mr-1"
                                    ></v-icon>
                                    {{ formatDate(item.starts_at) }}
                                </div>
                            </template>

                            <template #[`item.actions`]="{ item }">
                                <v-btn
                                    v-if="
                                        item.status === 'draft' ||
                                        item.status === 'scheduled'
                                    "
                                    variant="tonal"
                                    size="small"
                                    color="success"
                                    class="mr-2"
                                    @click="
                                        activateCycle(
                                            (item as AssessmentCycle).id,
                                        )
                                    "
                                    :title="
                                        $t(
                                            'assessment_command.actions_tooltips.activate',
                                        )
                                    "
                                >
                                    <PhRocketLaunch :size="18" />
                                </v-btn>
                                <v-btn
                                    v-if="item.status === 'active'"
                                    variant="tonal"
                                    size="small"
                                    color="primary"
                                    class="mr-2"
                                    :title="
                                        $t(
                                            'assessment_command.actions_tooltips.dashboard',
                                        )
                                    "
                                    @click="
                                        router.visit('/talento360/dashboard')
                                    "
                                >
                                    <PhChartBar :size="18" />
                                </v-btn>
                                <v-btn
                                    variant="text"
                                    size="small"
                                    color="grey-lighten-1"
                                >
                                    <PhDotsThreeVertical :size="18" />
                                </v-btn>
                            </template>

                            <template #no-data>
                                <div class="pa-12 text-center">
                                    <v-btn
                                        variant="text"
                                        color="primary"
                                        prepend-icon="mdi-plus"
                                        @click="wizardDialog = true"
                                        >Lanzar primer ciclo</v-btn
                                    >
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Premium Wizard -->
        <v-dialog
            v-model="wizardDialog"
            max-width="1000"
            persistent
            transition="dialog-bottom-transition"
        >
            <v-card class="glass-card border-auth overflow-hidden" height="700">
                <v-row class="ma-0 h-100">
                    <!-- Sidebar Navigation -->
                    <v-col
                        cols="3"
                        class="bg-primary-darken-4 pa-6 d-flex flex-column border-right-auth"
                    >
                        <div class="mb-8">
                            <v-icon
                                :icon="PhRocketLaunch"
                                color="primary"
                                size="32"
                                class="mb-2"
                            ></v-icon>
                            <h3
                                class="text-h5 font-weight-bold mb-1 text-white"
                            >
                                {{ $t('assessment_command.wizard.title') }}
                            </h3>
                            <p class="text-caption text-grey-lighten-1">
                                {{ $t('assessment_command.wizard.subtitle') }}
                            </p>
                        </div>

                        <div class="flex-grow-1">
                            <v-list
                                bg-color="transparent"
                                density="compact"
                                nav
                                class="pa-0 v-list-wizard"
                            >
                                <v-list-item
                                    :active="step === 1"
                                    title="Identidad & Tipo"
                                    class="mb-2 rounded-lg"
                                    @click="step = 1"
                                >
                                    <template #prepend>
                                        <div
                                            class="text-h6 font-weight-bold mr-4"
                                            :class="
                                                step === 1
                                                    ? 'text-primary'
                                                    : 'text-grey'
                                            "
                                        >
                                            1
                                        </div>
                                    </template>
                                </v-list-item>
                                <v-list-item
                                    :active="step === 2"
                                    :disabled="step < 2"
                                    title="Población & Alcance"
                                    class="mb-2 rounded-lg"
                                    @click="step = 2"
                                >
                                    <template #prepend>
                                        <div
                                            class="text-h6 font-weight-bold mr-4"
                                            :class="
                                                step === 2
                                                    ? 'text-primary'
                                                    : 'text-grey'
                                            "
                                        >
                                            2
                                        </div>
                                    </template>
                                </v-list-item>
                                <v-list-item
                                    :active="step === 3"
                                    :disabled="step < 3"
                                    title="Instrumentos & Red"
                                    class="mb-2 rounded-lg"
                                    @click="step = 3"
                                >
                                    <template #prepend>
                                        <div
                                            class="text-h6 font-weight-bold mr-4"
                                            :class="
                                                step === 3
                                                    ? 'text-primary'
                                                    : 'text-grey'
                                            "
                                        >
                                            3
                                        </div>
                                    </template>
                                </v-list-item>
                                <v-list-item
                                    :active="step === 4"
                                    :disabled="step < 4"
                                    title="Confirmación"
                                    class="mb-2 rounded-lg"
                                    @click="step = 4"
                                >
                                    <template #prepend>
                                        <div
                                            class="text-h6 font-weight-bold mr-4"
                                            :class="
                                                step === 4
                                                    ? 'text-primary'
                                                    : 'text-grey'
                                            "
                                        >
                                            4
                                        </div>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </div>

                        <StButtonGlass
                            block
                            variant="tonal"
                            color="white"
                            class="rounded-lg"
                            @click="wizardDialog = false"
                        >
                            {{ $t('assessment_command.wizard.actions.cancel') }}
                        </StButtonGlass>
                    </v-col>

                    <!-- Content Area -->
                    <v-col
                        cols="9"
                        class="pa-0 d-flex flex-column bg-surface-dark h-100"
                    >
                        <v-card-text
                            class="pa-8 flex-grow-1 overflow-y-auto pb-0"
                        >
                            <!-- Step 1: Identity -->
                            <v-window v-model="step" class="h-100">
                                <v-window-item :value="1">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline font-weight-black mb-1 text-primary"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step1.label',
                                                )
                                            }}
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold font-premium mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step1.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step1.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <v-text-field
                                        v-model="newCycle.name"
                                        :label="
                                            $t(
                                                'assessment_command.wizard.step1.name_label',
                                            )
                                        "
                                        :placeholder="
                                            $t(
                                                'assessment_command.wizard.step1.name_placeholder',
                                            )
                                        "
                                        variant="filled"
                                        color="primary"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-4 rounded-lg"
                                    ></v-text-field>

                                    <v-textarea
                                        v-model="newCycle.description"
                                        :label="
                                            $t(
                                                'assessment_command.wizard.step1.description_label',
                                            )
                                        "
                                        :placeholder="
                                            $t(
                                                'assessment_command.wizard.step1.description_placeholder',
                                            )
                                        "
                                        variant="filled"
                                        color="primary"
                                        bg-color="rgba(255,255,255,0.05)"
                                        rows="3"
                                        class="mb-6 rounded-lg"
                                    ></v-textarea>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        {{
                                            $t(
                                                'assessment_command.wizard.step1.mode_title',
                                            )
                                        }}
                                    </h3>
                                    <v-row>
                                        <v-col
                                            cols="6"
                                            v-for="mode in modeOptions"
                                            :key="mode.value"
                                        >
                                            <v-card
                                                @click="
                                                    newCycle.mode =
                                                        mode.value as any
                                                "
                                                :class="[
                                                    'pa-4 border-auth cursor-pointer rounded-xl transition-all',
                                                    newCycle.mode === mode.value
                                                        ? 'bg-primary-darken-3 elevation-xl border-primary'
                                                        : 'bg-white-opacity-5',
                                                ]"
                                                flat
                                            >
                                                <div
                                                    class="d-flex align-center mb-2"
                                                >
                                                    <v-avatar
                                                        :color="
                                                            newCycle.mode ===
                                                            mode.value
                                                                ? 'primary'
                                                                : 'grey-darken-3'
                                                        "
                                                        size="40"
                                                        class="mr-3 rounded-lg"
                                                    >
                                                        <component
                                                            :is="mode.icon"
                                                            :size="24"
                                                            :color="
                                                                newCycle.mode ===
                                                                mode.value
                                                                    ? 'white'
                                                                    : 'grey'
                                                            "
                                                        />
                                                    </v-avatar>
                                                    <div
                                                        class="text-subtitle-1 font-weight-bold font-premium text-white"
                                                    >
                                                        {{ mode.title }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="text-caption text-grey-lighten-1"
                                                >
                                                    {{ mode.desc }}
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Step 2: Scope -->
                                <v-window-item :value="2">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline font-weight-black mb-1 text-primary"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step2.label',
                                                )
                                            }}
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold font-premium mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step2.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step2.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <v-select
                                        v-model="newCycle.scope!.type"
                                        :label="
                                            $t(
                                                'assessment_command.wizard.step2.segmentation_label',
                                            )
                                        "
                                        :items="[
                                            {
                                                title: $t(
                                                    'assessment_command.wizard.step2.segmentation_options.all',
                                                ),
                                                value: 'all',
                                            },
                                            {
                                                title: $t(
                                                    'assessment_command.wizard.step2.segmentation_options.department',
                                                ),
                                                value: 'department',
                                            },
                                            {
                                                title: $t(
                                                    'assessment_command.wizard.step2.segmentation_options.scenario',
                                                ),
                                                value: 'scenario',
                                            },
                                            {
                                                title: $t(
                                                    'assessment_command.wizard.step2.segmentation_options.hipo',
                                                ),
                                                value: 'hipo',
                                            },
                                        ]"
                                        variant="filled"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-6 rounded-lg"
                                    ></v-select>

                                    <v-alert
                                        v-if="
                                            newCycle.scope!.type === 'scenario'
                                        "
                                        type="info"
                                        variant="tonal"
                                        border="start"
                                        class="mb-6 rounded-lg"
                                    >
                                        <template #prepend>
                                            <v-avatar
                                                color="info"
                                                size="32"
                                                class="mr-3"
                                            >
                                                <PhRocketLaunch
                                                    color="white"
                                                    :size="18"
                                                />
                                            </v-avatar>
                                        </template>
                                        {{
                                            $t(
                                                'assessment_command.wizard.step2.scenario_alert',
                                            )
                                        }}
                                    </v-alert>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        {{
                                            $t(
                                                'assessment_command.wizard.step2.schedule_title',
                                            )
                                        }}
                                    </h3>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-text-field
                                                v-model="newCycle.starts_at"
                                                :label="
                                                    $t(
                                                        'assessment_command.wizard.step2.start_date',
                                                    )
                                                "
                                                type="date"
                                                variant="filled"
                                                bg-color="rgba(255,255,255,0.05)"
                                                class="rounded-lg"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-text-field
                                                v-model="newCycle.ends_at"
                                                :label="
                                                    $t(
                                                        'assessment_command.wizard.step2.end_date',
                                                    )
                                                "
                                                type="date"
                                                variant="filled"
                                                bg-color="rgba(255,255,255,0.05)"
                                                class="rounded-lg"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Step 3: Instruments & Red -->
                                <v-window-item :value="3">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline font-weight-black mb-1 text-primary"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step3.label',
                                                )
                                            }}
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold font-premium mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step3.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step3.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-3 text-white"
                                    >
                                        {{
                                            $t(
                                                'assessment_command.wizard.step3.instruments_title',
                                            )
                                        }}
                                    </h3>
                                    <v-row class="mb-6">
                                        <v-col
                                            cols="6"
                                            v-for="inst in instrumentOptions"
                                            :key="inst.value"
                                        >
                                            <v-checkbox
                                                v-model="newCycle.instruments"
                                                :value="inst.value"
                                                :label="inst.title"
                                                color="primary"
                                                hide-details
                                                class="pa-2 bg-white-opacity-5 border-auth rounded-lg"
                                            >
                                                <template #label>
                                                    <div
                                                        class="d-flex align-center"
                                                    >
                                                        <component
                                                            :is="inst.icon"
                                                            :size="18"
                                                            class="mr-2 text-primary"
                                                        />
                                                        <span
                                                            class="text-white"
                                                            >{{
                                                                inst.title
                                                            }}</span
                                                        >
                                                    </div>
                                                </template>
                                            </v-checkbox>
                                        </v-col>
                                    </v-row>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-3 text-white"
                                    >
                                        {{
                                            $t(
                                                'assessment_command.wizard.step3.network_title',
                                            )
                                        }}
                                    </h3>
                                    <v-card
                                        class="bg-white-opacity-5 border-auth pa-4 rounded-xl"
                                    >
                                        <v-row>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .self
                                                    "
                                                    :label="
                                                        $t(
                                                            'assessment_command.wizard.step3.network_options.self',
                                                        )
                                                    "
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .manager
                                                    "
                                                    :label="
                                                        $t(
                                                            'assessment_command.wizard.step3.network_options.manager',
                                                        )
                                                    "
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .reports
                                                    "
                                                    :label="
                                                        $t(
                                                            'assessment_command.wizard.step3.network_options.reports',
                                                        )
                                                    "
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!.ai
                                                    "
                                                    :label="
                                                        $t(
                                                            'assessment_command.wizard.step3.network_options.ai',
                                                        )
                                                    "
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                        </v-row>
                                        <v-divider
                                            class="my-4 opacity-10"
                                        ></v-divider>
                                        <div
                                            class="d-flex align-center justify-space-between"
                                        >
                                            <div class="text-body-2 text-white">
                                                {{
                                                    $t(
                                                        'assessment_command.wizard.step3.peers_count_label',
                                                    )
                                                }}
                                            </div>
                                            <div style="width: 200px">
                                                <v-slider
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .peers
                                                    "
                                                    min="0"
                                                    max="8"
                                                    step="1"
                                                    thumb-label
                                                    hide-details
                                                    color="primary"
                                                ></v-slider>
                                            </div>
                                        </div>
                                    </v-card>
                                </v-window-item>

                                <!-- Step 4: Confirmation -->
                                <v-window-item :value="4">
                                    <div class="mb-8 text-center">
                                        <v-avatar
                                            color="primary"
                                            size="80"
                                            class="elevation-xl mb-4"
                                        >
                                            <v-icon
                                                :icon="PhCheckCircle"
                                                size="48"
                                                color="white"
                                            ></v-icon>
                                        </v-avatar>
                                        <h2
                                            class="text-h4 font-weight-bold font-premium mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step4.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'assessment_command.wizard.step4.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <v-card
                                        class="bg-white-opacity-5 border-auth mb-6 overflow-hidden rounded-xl"
                                    >
                                        <v-list
                                            bg-color="transparent"
                                            class="pa-0"
                                        >
                                            <v-list-item class="px-6 py-3">
                                                <template #prepend
                                                    ><v-icon
                                                        :icon="PhTag"
                                                        color="primary"
                                                    ></v-icon
                                                ></template>
                                                <v-list-item-title
                                                    class="font-weight-bold text-white"
                                                    >{{
                                                        newCycle.name
                                                    }}</v-list-item-title
                                                >
                                                <v-list-item-subtitle
                                                    class="text-grey"
                                                    >{{
                                                        $t(
                                                            'assessment_command.wizard.step4.items.name',
                                                        )
                                                    }}</v-list-item-subtitle
                                                >
                                            </v-list-item>
                                            <v-divider
                                                class="opacity-10"
                                            ></v-divider>
                                            <v-row class="ma-0">
                                                <v-col
                                                    cols="6"
                                                    class="pa-0 border-right-auth"
                                                >
                                                    <v-list-item
                                                        class="px-6 py-3"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold font-premium text-white"
                                                            >{{
                                                                modeOptions.find(
                                                                    (o) =>
                                                                        o.value ===
                                                                        newCycle.mode,
                                                                )?.title
                                                            }}</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >{{
                                                                $t(
                                                                    'assessment_command.wizard.step4.items.mode',
                                                                )
                                                            }}</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                                <v-col cols="6" class="pa-0">
                                                    <v-list-item
                                                        class="px-6 py-3"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold font-premium text-white"
                                                            >{{
                                                                newCycle
                                                                    .instruments
                                                                    ?.length
                                                            }}
                                                            {{
                                                                $t(
                                                                    'assessment_command.wizard.step4.items.selected_suffix',
                                                                )
                                                            }}</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >{{
                                                                $t(
                                                                    'assessment_command.wizard.step4.items.instruments',
                                                                )
                                                            }}</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                            </v-row>
                                            <v-divider
                                                class="opacity-10"
                                            ></v-divider>
                                            <v-list-item class="px-6 py-3">
                                                <template #prepend>
                                                    <v-avatar
                                                        size="24"
                                                        class="mr-2"
                                                    >
                                                        <PhRobot
                                                            :color="
                                                                newCycle
                                                                    .evaluators
                                                                    ?.ai
                                                                    ? '#10b981'
                                                                    : '#6b7280'
                                                            "
                                                            :size="18"
                                                        />
                                                    </v-avatar>
                                                </template>
                                                <v-list-item-title
                                                    :class="
                                                        newCycle.evaluators?.ai
                                                            ? 'text-success font-weight-bold'
                                                            : 'text-grey'
                                                    "
                                                >
                                                    {{
                                                        newCycle.evaluators?.ai
                                                            ? $t(
                                                                  'assessment_command.wizard.step4.items.ai_active',
                                                              )
                                                            : $t(
                                                                  'assessment_command.wizard.step4.items.ai_inactive',
                                                              )
                                                    }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle
                                                    class="text-grey"
                                                >
                                                    {{
                                                        $t(
                                                            'assessment_command.wizard.step4.items.ai_engine',
                                                        )
                                                    }}
                                                </v-list-item-subtitle>
                                            </v-list-item>
                                        </v-list>
                                    </v-card>

                                    <!-- Preview Section Start -->
                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        {{
                                            $t(
                                                'assessment_command.wizard.step4.preview_title',
                                            )
                                        }}
                                    </h3>

                                    <v-card
                                        class="bg-primary-darken-3 border-auth pa-6 mb-8 rounded-xl text-center"
                                        flat
                                    >
                                        <div v-if="previewLoading" class="py-4">
                                            <v-progress-circular
                                                indeterminate
                                                color="primary"
                                                class="mb-3"
                                            ></v-progress-circular>
                                            <div
                                                class="text-caption text-grey-lighten-1"
                                            >
                                                {{
                                                    $t(
                                                        'assessment_command.wizard.step4.calculating',
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <v-row v-else>
                                            <v-col
                                                cols="6"
                                                class="border-right-auth"
                                            >
                                                <div
                                                    class="text-h3 font-weight-black font-premium mb-1 text-white"
                                                >
                                                    {{
                                                        previewData.participants
                                                    }}
                                                </div>
                                                <div
                                                    class="text-caption text-indigo-lighten-2 text-uppercase font-weight-bold"
                                                >
                                                    {{
                                                        $t(
                                                            'assessment_command.wizard.step4.preview_stats.participants',
                                                        )
                                                    }}
                                                </div>
                                            </v-col>
                                            <v-col cols="6">
                                                <div
                                                    class="text-h3 font-weight-black font-premium mb-1 text-white"
                                                >
                                                    {{
                                                        previewData.impacted_areas
                                                    }}
                                                </div>
                                                <div
                                                    class="text-caption text-indigo-lighten-2 text-uppercase font-weight-bold"
                                                >
                                                    {{
                                                        $t(
                                                            'assessment_command.wizard.step4.preview_stats.areas',
                                                        )
                                                    }}
                                                </div>
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                    <!-- Preview Section End -->

                                    <v-alert
                                        type="warning"
                                        variant="tonal"
                                        border="start"
                                        class="mb-6 rounded-xl"
                                    >
                                        <template #prepend>
                                            <PhPulse :size="20" class="mr-2" />
                                        </template>
                                        {{
                                            $t(
                                                'assessment_command.wizard.step4.draft_alert',
                                            )
                                        }}
                                    </v-alert>
                                </v-window-item>
                            </v-window>
                        </v-card-text>

                        <!-- Footer Actions -->
                        <v-divider color="white" class="opacity-10"></v-divider>
                        <div
                            class="pa-8 d-flex justify-space-between align-center"
                        >
                            <v-btn
                                v-if="step > 1"
                                variant="text"
                                color="grey-lighten-1"
                                @click="step--"
                            >
                                <template #prepend>
                                    <PhCaretLeft :size="18" class="mr-1" />
                                </template>
                                {{
                                    $t('assessment_command.wizard.actions.back')
                                }}
                            </v-btn>
                            <div v-else></div>

                            <StButtonGlass
                                v-if="step < 4"
                                color="primary"
                                height="48"
                                class="rounded-lg px-8"
                                @click="step++"
                            >
                                {{
                                    $t('assessment_command.wizard.actions.next')
                                }}
                                <template #append>
                                    <PhCaretRight :size="18" class="ml-1" />
                                </template>
                            </StButtonGlass>

                            <StButtonGlass
                                v-else
                                color="success"
                                height="48"
                                class="font-weight-bold rounded-lg px-8"
                                :loading="saving"
                                @click="saveCycle"
                            >
                                <template #prepend>
                                    <PhCheckCircle :size="18" class="mr-1" />
                                </template>
                                {{
                                    $t(
                                        'assessment_command.wizard.actions.launch',
                                    )
                                }}
                            </StButtonGlass>
                        </div>
                    </v-col>
                </v-row>
            </v-card>
        </v-dialog>
    </AppLayout>
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
</style>

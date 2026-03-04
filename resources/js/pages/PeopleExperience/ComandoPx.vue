<script setup lang="ts">
import {
    PhBrain,
    PhCalendar,
    PhCaretLeft,
    PhCaretRight,
    PhChartLineUp,
    PhCheck,
    PhCheckCircle,
    PhCloudSun,
    PhDotsThreeVertical,
    PhFire,
    PhHeart,
    PhHeartbeat,
    PhMagnifyingGlass,
    PhNumberCircleFour,
    PhNumberCircleOne,
    PhNumberCircleThree,
    PhNumberCircleTwo,
    PhPlus,
    PhPulse,
    PhRadio,
    PhRobot,
    PhSealCheck,
    PhShieldCheck,
    PhShieldWarning,
    PhSmiley,
    PhStack,
    PhTag,
    PhTarget,
    PhUserCheck,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import StButtonGlass from '../../components/StButtonGlass.vue';
import StCardGlass from '../../components/StCardGlass.vue';
import { usePermissions } from '../../composables/usePermissions';

const { can } = usePermissions();
const { t, locale } = useI18n();

interface PxCampaign {
    id: number;
    name: string;
    description: string | null;
    mode: 'agent_autonomous' | 'recurring' | 'specific_date';
    status: 'draft' | 'scheduled' | 'active' | 'paused' | 'completed';
    topics: string[];
    scope: { type: string; target_pct?: number; ids?: number[] };
    schedule_config: Record<string, any>;
    starts_at: string | null;
    ends_at: string | null;
    created_at: string;
}

const campaigns = ref<PxCampaign[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);
const step = ref(1);

// Stats
const stats = computed(() => {
    return {
        active: campaigns.value.filter((c) => c.status === 'active').length,
        avg_engagement: 84, // Mock
        sentiment_index: 4.2, // Mock 1-5
        burnout_risk: t('px_command.risk_low'), // Mock
    };
});

const newCampaign = ref<Partial<PxCampaign>>({
    name: '',
    description: '',
    mode: 'agent_autonomous',
    scope: { type: 'randomized_sample', target_pct: 20 },
    topics: ['clima', 'stress'],
    schedule_config: { frequency: 'monthly' },
    status: 'draft',
    starts_at: null,
    ends_at: null,
});

const modeOptions = computed(() => [
    {
        title: t('px_command.modes.ai_autonomous'),
        value: 'agent_autonomous',
        icon: PhRobot,
        desc: t('px_command.modes.ai_autonomous_desc'),
    },
    {
        title: t('px_command.modes.recurring'),
        value: 'recurring',
        icon: PhCalendar,
        desc: t('px_command.modes.recurring_desc'),
    },
    {
        title: t('px_command.modes.specific_date'),
        value: 'specific_date',
        icon: PhTarget,
        desc: t('px_command.modes.specific_date_desc'),
    },
]);

const topicOptions = computed(() => [
    { title: t('px_command.topics.clima'), value: 'clima', icon: PhCloudSun },
    { title: t('px_command.topics.stress'), value: 'stress', icon: PhFire },
    {
        title: t('px_command.topics.happiness'),
        value: 'happiness',
        icon: PhSmiley,
    },
    {
        title: t('px_command.topics.health'),
        value: 'health',
        icon: PhHeartbeat,
    },
    {
        title: t('px_command.topics.leadership'),
        value: 'leadership',
        icon: PhUserCheck,
    },
]);

const scopeOptions = computed(() => [
    {
        title: t('px_command.scopes.randomized_sample'),
        value: 'randomized_sample',
    },
    { title: t('px_command.scopes.all'), value: 'all' },
    { title: t('px_command.scopes.department'), value: 'department' },
]);

const loadCampaigns = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/px-campaigns');
        campaigns.value = data.data;
    } catch (e) {
        console.error('Failed to load px campaigns', e);
    } finally {
        loading.value = false;
    }
};

const saveCampaign = async () => {
    saving.value = true;
    try {
        await axios.post('/api/px-campaigns', newCampaign.value);
        wizardDialog.value = false;
        resetWizard();
        loadCampaigns();
    } catch (e) {
        console.error('Failed to save px campaign', e);
    } finally {
        saving.value = false;
    }
};

const resetWizard = () => {
    step.value = 1;
    newCampaign.value = {
        name: '',
        description: '',
        mode: 'agent_autonomous',
        scope: { type: 'randomized_sample', target_pct: 20 },
        topics: ['clima', 'stress'],
        schedule_config: { frequency: 'monthly' },
        status: 'draft',
    };
};

onMounted(() => {
    loadCampaigns();
});
</script>

<template>
    <div
        class="mx-auto max-w-[1400px] animate-in space-y-8 p-6 duration-500 fade-in slide-in-from-bottom-2"
    >
        <!-- Header Section -->
        <div
            class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h1
                    class="flex items-center text-3xl font-black tracking-tight text-white"
                >
                    {{ $t('px_command.title') }}
                    <span
                        class="ml-2 text-teal-400 drop-shadow-[0_0_10px_rgba(20,184,166,0.5)]"
                        >{{ $t('px_command.title_highlight') }}</span
                    >
                </h1>
                <p class="mt-1 text-sm text-white/60">
                    {{ $t('px_command.subtitle') }}
                </p>
            </div>

            <StButtonGlass
                v-if="can('assessments.manage')"
                :icon="PhBrain"
                variant="primary"
                @click="wizardDialog = true"
            >
                {{ $t('px_command.launch_campaign') }}
            </StButtonGlass>
        </div>

        <!-- Dashboard Stats Row -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
            <StCardGlass
                v-for="(val, key) in stats"
                :key="key"
                class="flex items-center p-4 transition-all duration-300 hover:bg-white/10"
            >
                <div
                    class="mr-4 flex items-center justify-center rounded-xl border border-teal-500/20 bg-teal-500/10 p-3 text-teal-400 shadow-[0_0_15px_rgba(20,184,166,0.15)] transition-all duration-300"
                    :class="
                        key === 'burnout_risk'
                            ? val === 'Bajo'
                                ? 'border-teal-500/20 bg-teal-500/10 text-teal-400 shadow-[0_0_15px_rgba(20,184,166,0.2)]'
                                : 'border-pink-500/20 bg-pink-500/10 text-pink-400 shadow-[0_0_15px_rgba(236,72,153,0.2)]'
                            : ''
                    "
                >
                    <component
                        :is="
                            key === 'active'
                                ? PhPulse
                                : key === 'avg_engagement'
                                  ? PhHeart
                                  : key === 'sentiment_index'
                                    ? PhSmiley
                                    : PhShieldWarning
                        "
                        weight="duotone"
                        :size="28"
                    />
                </div>
                <div>
                    <div class="text-2xl font-black tracking-tight text-white">
                        {{ val }}{{ key === 'avg_engagement' ? '%' : '' }}
                    </div>
                    <div
                        class="mt-0.5 text-[10px] font-bold tracking-widest text-white/50 uppercase"
                    >
                        {{ $t(`px_command.metrics.${key}`) }}
                    </div>
                </div>
            </StCardGlass>
        </div>

        <!-- Main Listing Row -->
        <StCardGlass class="mt-8 overflow-hidden">
            <div
                class="flex items-center justify-between border-b border-white/5 bg-black/20 px-6 py-4"
            >
                <div class="flex items-center text-lg font-bold text-white">
                    <PhClockCounterClockwise
                        color="rgb(20, 184, 166)"
                        class="mr-3"
                        :size="24"
                    />
                    {{ $t('px_command.history_title') }}
                </div>
                <div class="w-full max-w-xs">
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <PhMagnifyingGlass
                                :size="20"
                                class="text-gray-400"
                            />
                        </div>
                        <input
                            type="text"
                            class="block w-full rounded-lg border border-white/10 bg-white/5 py-2 pr-3 pl-10 leading-5 text-gray-300 placeholder-gray-500 transition duration-150 ease-in-out focus:border-teal-500 focus:bg-white/10 focus:outline-none sm:text-sm"
                            :placeholder="$t('px_command.search_placeholder')"
                        />
                    </div>
                </div>
            </div>

            <v-data-table
                :headers="[
                    {
                        title: $t('px_command.headers.name'),
                        key: 'name',
                    },
                    {
                        title: $t('px_command.headers.topics'),
                        key: 'topics',
                    },
                    {
                        title: $t('px_command.headers.mode'),
                        key: 'mode',
                    },
                    {
                        title: $t('px_command.headers.impact'),
                        key: 'impact',
                        sortable: false,
                    },
                    {
                        title: $t('px_command.headers.status'),
                        key: 'status',
                    },
                    {
                        title: '',
                        key: 'actions',
                        sortable: false,
                        align: 'end',
                    },
                ]"
                :items="campaigns"
                :loading="loading"
                class="bg-transparent text-white"
                theme="dark"
            >
                <template #[`item.name`]="{ item }">
                    <div class="py-4">
                        <div class="text-body-1 font-weight-bold text-white">
                            {{ item.name }}
                        </div>
                        <div class="text-caption text-grey-lighten-1">
                            {{
                                item.description ||
                                $t('px_command.default_desc')
                            }}
                        </div>
                    </div>
                </template>

                <template #[`item.topics`]="{ item }">
                    <div class="d-flex flex-wrap gap-1">
                        <v-chip
                            v-for="t in item.topics"
                            :key="t"
                            size="x-small"
                            color="success"
                            variant="tonal"
                            class="mr-1 mb-1 border border-white/10 bg-white/5 text-teal-400"
                        >
                            {{
                                topicOptions.find((o) => o.value === t)
                                    ?.title || t
                            }}
                        </v-chip>
                    </div>
                </template>

                <template #[`item.mode`]="{ item }">
                    <div
                        class="inline-flex items-center rounded-full border border-teal-500/30 bg-teal-500/10 px-2.5 py-0.5 text-xs font-medium text-teal-400"
                    >
                        <component
                            class="mr-1.5"
                            :is="
                                modeOptions.find((o) => o.value === item.mode)
                                    ?.icon || PhRadio
                            "
                            :size="14"
                            weight="bold"
                        />
                        {{
                            modeOptions.find((o) => o.value === item.mode)
                                ?.title || item.mode
                        }}
                    </div>
                </template>

                <template #[`item.impact`]="{ item }">
                    <div class="d-flex align-center" style="min-width: 120px">
                        <v-progress-linear
                            :model-value="item.status === 'active' ? 75 : 0"
                            color="success"
                            height="6"
                            rounded
                            class="mr-3 bg-white/10"
                        ></v-progress-linear>
                        <span class="text-caption text-white">{{
                            item.status === 'active' ? '75%' : '0%'
                        }}</span>
                    </div>
                </template>

                <template #[`item.status`]="{ item }">
                    <div
                        class="inline-flex items-center rounded px-2.5 py-0.5 text-xs font-bold tracking-wider uppercase"
                        :class="
                            item.status === 'active'
                                ? 'bg-teal-400 text-black'
                                : 'bg-white/20 text-white'
                        "
                    >
                        {{ item.status }}
                    </div>
                </template>

                <template #[`item.actions`]="{ item }">
                    <v-btn variant="text" size="small" color="grey-lighten-1">
                        <PhDotsThreeVertical :size="20" />
                    </v-btn>
                    <v-btn
                        v-if="item.status === 'active'"
                        variant="tonal"
                        size="small"
                        color="success"
                        class="bg-success/10 text-success border-success/20 ml-2 border"
                    >
                        <PhChartLineUp :size="20" />
                    </v-btn>
                </template>

                <template #no-data>
                    <div class="pa-12 text-center text-white/50">
                        <PhShieldCheck
                            :size="64"
                            class="mb-4 text-white/20"
                            weight="light"
                        />
                        <p class="mb-4">
                            {{ $t('px_command.no_campaigns') }}
                        </p>
                        <StButtonGlass
                            variant="primary"
                            :icon="PhPlus"
                            @click="wizardDialog = true"
                        >
                            {{ $t('px_command.launch_first') }}
                        </StButtonGlass>
                    </div>
                </template>
            </v-data-table>
        </StCardGlass>
    </div>

    <!-- Premium Wizard PX -->
    <v-dialog
        v-model="wizardDialog"
        max-width="1000"
        persistent
        transition="dialog-bottom-transition"
        content-class="backdrop-blur-sm"
    >
        <div
            class="bg-opacity-80 flex h-[700px] max-h-[90vh] flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#0f1412] shadow-[0_0_50px_rgba(0,0,0,0.5)] backdrop-blur-2xl md:flex-row"
        >
            <!-- Sidebar Navigation -->
            <div
                class="flex w-full flex-col overflow-y-auto border-b border-white/5 bg-white/5 p-6 px-4 md:w-1/4 md:border-r md:border-b-0"
            >
                <div class="mb-8">
                    <div
                        class="mb-4 inline-flex rounded-xl border border-teal-500/20 bg-teal-500/10 p-3 text-teal-400 shadow-[0_0_15px_rgba(20,184,166,0.2)]"
                    >
                        <PhHeartbeat :size="24" weight="fill" />
                    </div>
                    <h3 class="mb-1 text-xl font-bold text-white">
                        {{ $t('px_command.wizard.title') }}
                    </h3>
                    <p class="text-xs text-white/50">
                        {{ $t('px_command.wizard.subtitle') }}
                    </p>
                </div>

                <div class="grow space-y-2">
                    <div
                        v-for="st in [
                            {
                                num: 1,
                                icon: PhNumberCircleOne,
                                title: 'step1_title',
                            },
                            {
                                num: 2,
                                icon: PhNumberCircleTwo,
                                title: 'step2_title',
                            },
                            {
                                num: 3,
                                icon: PhNumberCircleThree,
                                title: 'step3_title',
                            },
                            {
                                num: 4,
                                icon: PhNumberCircleFour,
                                title: 'step4_title',
                            },
                        ]"
                        :key="st.num"
                        @click="
                            step >= st.num || step === st.num - 1
                                ? (step = st.num)
                                : null
                        "
                        class="flex items-center rounded-xl p-3 transition-all duration-300"
                        :class="[
                            step === st.num
                                ? 'border border-teal-500/30 bg-teal-500/15 text-teal-400'
                                : step > st.num
                                  ? 'cursor-pointer text-teal-400/50 hover:bg-white/5'
                                  : 'text-gray-500 opacity-50',
                            step < st.num && step !== st.num - 1
                                ? 'cursor-not-allowed'
                                : 'cursor-pointer',
                        ]"
                    >
                        <component
                            :is="st.icon"
                            :size="20"
                            class="mr-3"
                            :weight="step === st.num ? 'fill' : 'regular'"
                        />
                        <span class="text-sm font-medium">{{
                            $t(`px_command.wizard.${st.title}`)
                        }}</span>
                    </div>
                </div>

                <StButtonGlass
                    variant="ghost"
                    class="mt-4 w-full"
                    @click="wizardDialog = false"
                >
                    {{ $t('px_command.wizard.close') }}
                </StButtonGlass>
            </div>

            <!-- Content Area -->
            <div
                class="relative flex h-full w-full flex-col bg-transparent md:w-3/4"
            >
                <div class="grow overflow-y-auto p-8">
                    <!-- Step 1: Topics -->
                    <v-window v-model="step" class="h-100">
                        <v-window-item :value="1">
                            <div class="mb-8">
                                <div
                                    class="mb-2 flex items-center text-[10px] font-black tracking-[0.2em] text-teal-400 uppercase"
                                >
                                    <div
                                        class="mr-3 h-px w-8 bg-teal-500/30"
                                    ></div>
                                    {{
                                        $t(
                                            'px_command.wizard.step1.step_label',
                                            { num: 1 },
                                        ) || 'PASO 1'
                                    }}
                                </div>
                                <h2
                                    class="mb-3 text-3xl font-bold tracking-tight text-white"
                                >
                                    {{ $t('px_command.wizard.step1.title') }}
                                </h2>
                                <p
                                    class="max-w-xl text-sm leading-relaxed text-white/50"
                                >
                                    {{ $t('px_command.wizard.step1.desc') }}
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="mb-3 ml-1 block text-xs font-bold tracking-widest text-white/70 uppercase"
                                    >
                                        {{
                                            $t(
                                                'px_command.wizard.step1.name_label',
                                            )
                                        }}
                                    </label>
                                    <input
                                        v-model="newCampaign.name"
                                        type="text"
                                        :placeholder="
                                            $t(
                                                'px_command.wizard.step1.name_placeholder',
                                            )
                                        "
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-white transition-all placeholder:text-white/20 focus:border-teal-500/50 focus:ring-2 focus:ring-teal-500/30 focus:outline-none"
                                    />
                                </div>

                                <div>
                                    <h3
                                        class="mb-4 ml-1 flex items-center text-sm font-bold text-white/90"
                                    >
                                        <PhStack
                                            :size="16"
                                            class="mr-2 text-teal-400"
                                        />
                                        {{
                                            $t(
                                                'px_command.wizard.step1.dimensions',
                                            )
                                        }}
                                    </h3>

                                    <div
                                        class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                    >
                                        <div
                                            v-for="topic in topicOptions"
                                            :key="topic.value"
                                            @click="
                                                newCampaign.topics?.includes(
                                                    topic.value,
                                                )
                                                    ? (newCampaign.topics =
                                                          newCampaign.topics.filter(
                                                              (t) =>
                                                                  t !==
                                                                  topic.value,
                                                          ))
                                                    : newCampaign.topics?.push(
                                                          topic.value,
                                                      )
                                            "
                                            class="group relative cursor-pointer overflow-hidden rounded-2xl border p-4 transition-all duration-300"
                                            :class="[
                                                newCampaign.topics?.includes(
                                                    topic.value,
                                                )
                                                    ? 'border-teal-500/40 bg-teal-500/10 shadow-[0_0_20px_rgba(20,184,166,0.1)]'
                                                    : 'border-white/5 bg-white/5 hover:border-white/20 hover:bg-white/10',
                                            ]"
                                        >
                                            <div
                                                class="relative z-10 flex items-center space-x-4"
                                            >
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-xl transition-colors"
                                                    :class="[
                                                        newCampaign.topics?.includes(
                                                            topic.value,
                                                        )
                                                            ? 'bg-teal-400 text-teal-950'
                                                            : 'bg-white/10 text-white/50 group-hover:bg-white/20 group-hover:text-white',
                                                    ]"
                                                >
                                                    <component
                                                        :is="topic.icon"
                                                        :size="20"
                                                        weight="fill"
                                                    />
                                                </div>
                                                <div
                                                    class="text-sm font-bold tracking-wide"
                                                    :class="
                                                        newCampaign.topics?.includes(
                                                            topic.value,
                                                        )
                                                            ? 'text-white'
                                                            : 'text-white/60'
                                                    "
                                                >
                                                    {{ topic.title }}
                                                </div>
                                            </div>
                                            <!-- Checkmark indicator -->
                                            <div
                                                v-if="
                                                    newCampaign.topics?.includes(
                                                        topic.value,
                                                    )
                                                "
                                                class="absolute top-2 right-2 text-teal-400"
                                            >
                                                <PhCheckCircle
                                                    :size="16"
                                                    weight="fill"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </v-window-item>

                        <!-- Step 2: Mode & Frequency -->
                        <v-window-item :value="2">
                            <div class="mb-8">
                                <div
                                    class="mb-2 flex items-center text-[10px] font-black tracking-[0.2em] text-teal-400 uppercase"
                                >
                                    <div
                                        class="mr-3 h-px w-8 bg-teal-500/30"
                                    ></div>
                                    {{
                                        $t(
                                            'px_command.wizard.step2.step_label',
                                            { num: 2 },
                                        ) || 'PASO 2'
                                    }}
                                </div>
                                <h2
                                    class="mb-3 text-3xl font-bold tracking-tight text-white"
                                >
                                    {{ $t('px_command.wizard.step2.title') }}
                                </h2>
                                <p
                                    class="max-w-xl text-sm leading-relaxed text-white/50"
                                >
                                    {{ $t('px_command.wizard.step2.desc') }}
                                </p>
                            </div>

                            <div class="mb-8 space-y-4">
                                <div
                                    v-for="mode in modeOptions"
                                    :key="mode.value"
                                    @click="
                                        newCampaign.mode = mode.value as any
                                    "
                                    class="group relative cursor-pointer overflow-hidden rounded-2xl border p-5 transition-all duration-300"
                                    :class="[
                                        newCampaign.mode === mode.value
                                            ? 'border-teal-500/40 bg-teal-500/10 shadow-[0_0_30px_rgba(20,184,166,0.1)]'
                                            : 'border-white/5 bg-white/5 hover:border-white/20 hover:bg-white/10',
                                    ]"
                                >
                                    <div
                                        class="relative z-10 flex items-center space-x-5"
                                    >
                                        <div
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl transition-all duration-500"
                                            :class="[
                                                newCampaign.mode === mode.value
                                                    ? 'scale-110 bg-teal-400 text-teal-950'
                                                    : 'bg-white/10 text-white/40 group-hover:text-white',
                                            ]"
                                        >
                                            <component
                                                :is="mode.icon"
                                                :size="28"
                                                weight="duotone"
                                            />
                                        </div>
                                        <div class="grow">
                                            <div
                                                class="mb-0.5 text-lg font-bold tracking-tight text-white"
                                            >
                                                {{ mode.title }}
                                            </div>
                                            <div
                                                class="line-clamp-2 text-xs text-white/40"
                                            >
                                                {{ mode.desc }}
                                            </div>
                                        </div>
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full border-2 transition-all"
                                            :class="[
                                                newCampaign.mode === mode.value
                                                    ? 'border-teal-400 bg-teal-400'
                                                    : 'border-white/10',
                                            ]"
                                        >
                                            <PhCheck
                                                v-if="
                                                    newCampaign.mode ===
                                                    mode.value
                                                "
                                                :size="14"
                                                weight="bold"
                                                class="text-teal-950"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="newCampaign.mode === 'agent_autonomous'"
                                class="flex items-start space-x-4 rounded-2xl border border-indigo-500/20 bg-indigo-500/10 p-5 shadow-[0_0_20px_rgba(99,102,241,0.05)]"
                            >
                                <div
                                    class="rounded-xl bg-indigo-500/20 p-2 text-indigo-400"
                                >
                                    <PhRobot :size="24" weight="duotone" />
                                </div>
                                <div class="text-xs leading-relaxed">
                                    <span
                                        class="mb-1 block font-bold text-indigo-300"
                                    >
                                        {{
                                            $t(
                                                'px_command.wizard.step2.ai_enabled',
                                            )
                                        }}
                                    </span>
                                    <span class="text-white/60">
                                        {{
                                            $t(
                                                'px_command.wizard.step2.ai_desc',
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </v-window-item>

                        <!-- Step 3: Scope -->
                        <v-window-item :value="3">
                            <div class="mb-8">
                                <div
                                    class="mb-2 flex items-center text-[10px] font-black tracking-[0.2em] text-teal-400 uppercase"
                                >
                                    <div
                                        class="mr-3 h-px w-8 bg-teal-500/30"
                                    ></div>
                                    {{
                                        $t(
                                            'px_command.wizard.step3.step_label',
                                            { num: 3 },
                                        ) || 'PASO 3'
                                    }}
                                </div>
                                <h2
                                    class="mb-3 text-3xl font-bold tracking-tight text-white"
                                >
                                    {{ $t('px_command.wizard.step3.title') }}
                                </h2>
                                <p
                                    class="max-w-xl text-sm leading-relaxed text-white/50"
                                >
                                    {{ $t('px_command.wizard.step3.desc') }}
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="mb-3 ml-1 block text-xs font-bold tracking-widest text-white/70 uppercase"
                                    >
                                        {{
                                            $t(
                                                'px_command.wizard.step3.segmentation',
                                            )
                                        }}
                                    </label>
                                    <v-select
                                        v-model="newCampaign.scope!.type"
                                        :items="scopeOptions"
                                        variant="filled"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="overflow-hidden rounded-2xl"
                                        theme="dark"
                                        hide-details
                                    ></v-select>
                                </div>

                                <div
                                    class="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-8 text-center transition-all"
                                    v-if="
                                        newCampaign.scope!.type ===
                                        'randomized_sample'
                                    "
                                >
                                    <div
                                        class="absolute inset-0 bg-teal-500/5 opacity-0 transition-opacity group-hover:opacity-100"
                                    ></div>
                                    <div class="relative z-10">
                                        <div
                                            class="mb-1 text-6xl font-black tracking-tighter text-white drop-shadow-[0_0_15px_rgba(20,184,166,0.3)]"
                                        >
                                            {{ newCampaign.scope!.target_pct }}%
                                        </div>
                                        <div
                                            class="mb-8 text-xs font-bold tracking-widest text-teal-400/70 uppercase"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step3.random_traffic',
                                                )
                                            }}
                                        </div>

                                        <v-slider
                                            v-model="
                                                newCampaign.scope!.target_pct
                                            "
                                            min="5"
                                            max="100"
                                            step="5"
                                            color="teal-accent-3"
                                            track-color="white"
                                            track-opacity="0.1"
                                            class="mx-4 mb-4"
                                            hide-details
                                        ></v-slider>

                                        <p
                                            class="mx-auto max-w-xs text-xs text-white/40 italic"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step3.sample_desc',
                                                    {
                                                        pct: newCampaign.scope!
                                                            .target_pct,
                                                    },
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </v-window-item>

                        <!-- Step 4: Confirmation -->
                        <v-window-item :value="4">
                            <div class="mb-10 text-center">
                                <div
                                    class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-3xl border border-teal-500/30 bg-teal-500/20 text-teal-400 shadow-[0_0_30px_rgba(20,184,166,0.2)] ring-8 ring-teal-500/5"
                                >
                                    <PhShieldCheck :size="40" weight="fill" />
                                </div>
                                <h2
                                    class="mb-3 text-3xl font-bold tracking-tight text-white"
                                >
                                    {{ $t('px_command.wizard.step4.title') }}
                                </h2>
                                <p
                                    class="mx-auto max-w-sm text-sm leading-relaxed text-white/50"
                                >
                                    {{ $t('px_command.wizard.step4.desc') }}
                                </p>
                            </div>

                            <div
                                class="mb-8 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl"
                            >
                                <div
                                    class="flex items-center border-b border-white/5 p-6"
                                >
                                    <div
                                        class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-teal-500/10 text-teal-400"
                                    >
                                        <PhTag :size="20" weight="fill" />
                                    </div>
                                    <div>
                                        <div
                                            class="mb-0.5 text-xs font-bold tracking-widest text-white/40 uppercase"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step4.name',
                                                )
                                            }}
                                        </div>
                                        <div
                                            class="text-lg font-bold text-white"
                                        >
                                            {{ newCampaign.name }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="grid grid-cols-2 divide-x divide-white/5"
                                >
                                    <div class="p-6">
                                        <div
                                            class="mb-1 text-[10px] font-black tracking-widest text-white/40 uppercase"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step4.distribution',
                                                )
                                            }}
                                        </div>
                                        <div
                                            class="flex items-center font-bold text-white"
                                        >
                                            <div
                                                class="mr-2 h-2 w-2 rounded-full bg-teal-400"
                                            ></div>
                                            {{
                                                modeOptions.find(
                                                    (o) =>
                                                        o.value ===
                                                        newCampaign.mode,
                                                )?.title
                                            }}
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <div
                                            class="mb-1 text-[10px] font-black tracking-widest text-white/40 uppercase"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step4.metrics',
                                                )
                                            }}
                                        </div>
                                        <div class="font-bold text-white">
                                            {{ newCampaign.topics?.length }}
                                            {{
                                                $t(
                                                    'px_command.wizard.step4.dimensions',
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center rounded-2xl border border-teal-500/10 bg-teal-500/5 p-4"
                            >
                                <div
                                    class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-teal-500/20 text-teal-400"
                                >
                                    <PhRobot :size="18" weight="duotone" />
                                </div>
                                <p
                                    class="text-[11px] leading-normal text-white/60"
                                >
                                    {{ $t('px_command.wizard.step4.ai_desc') }}
                                </p>
                            </div>
                        </v-window-item>
                    </v-window>
                </div>

                <!-- Footer Actions -->
                <div class="h-px w-full bg-white/10"></div>
                <div
                    class="mt-auto flex items-center justify-between border-t border-white/5 bg-black/20 p-8"
                >
                    <StButtonGlass
                        v-if="step > 1"
                        variant="ghost"
                        :icon="PhCaretLeft"
                        @click="step--"
                    >
                        {{ $t('px_command.wizard.actions.back') }}
                    </StButtonGlass>
                    <div v-else></div>

                    <StButtonGlass
                        v-if="step < 4"
                        variant="primary"
                        :icon="PhCaretRight"
                        right-icon
                        @click="step++"
                    >
                        {{ $t('px_command.wizard.actions.next') }}
                    </StButtonGlass>

                    <StButtonGlass
                        v-else
                        variant="primary"
                        :loading="saving"
                        class="shadow-[0_0_20px_rgba(20,184,166,0.3)]"
                        :icon="PhSealCheck"
                        @click="saveCampaign"
                    >
                        {{ $t('px_command.wizard.actions.launch') }}
                    </StButtonGlass>
                </div>
            </div>
        </div>
    </v-dialog>
</template>

<style scoped>
.glass-card {
    background: rgba(30, 36, 30, 0.8) !important;
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
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3) !important;
}

.tracking-tight {
    letter-spacing: -0.02em;
}

.v-list-wizard.success-theme .v-list-item--active {
    background: rgba(var(--v-theme-success), 0.15) !important;
    color: rgb(var(--v-theme-success)) !important;
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

:deep(.v-data-table) {
    color: rgba(255, 255, 255, 1) !important;
}

:deep(.v-data-table th) {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    font-size: 0.7rem !important;
}

.bg-success-darken-4 {
    background-color: #0d1a11 !important;
}

.bg-surface-dark {
    background-color: #161c18 !important;
}

.gap-1 {
    gap: 4px;
}
</style>

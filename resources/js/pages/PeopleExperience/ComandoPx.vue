<script setup lang="ts">
import {
    PhBrain,
    PhCalendar,
    PhCaretLeft,
    PhCaretRight,
    PhChartLineUp,
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
    PhTag,
    PhTarget,
    PhUserCheck,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { format } from 'date-fns';
import { enUS, es } from 'date-fns/locale';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import StButtonGlass from '../../components/StButtonGlass.vue';
import { usePermissions } from '../../composables/usePermissions';
import AppLayout from '../../layouts/AppLayout.vue';

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

const formatDate = (date: string | null) => {
    if (!date) return t('px_command.date_ia_decides');
    return format(new Date(date), 'dd MMM yyyy', {
        locale: locale.value === 'es' ? es : enUS,
    });
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey-lighten-1',
        scheduled: 'cyan-accent-3',
        active: 'success',
        paused: 'warning-accent-2',
        completed: 'indigo-accent-2',
    };
    return map[status] || 'grey';
};

onMounted(() => {
    loadCampaigns();
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
                        class="text-h3 font-weight-black mb-2 tracking-tight text-white"
                    >
                        {{ $t('px_command.title') }}
                        <span class="text-success">{{
                            $t('px_command.title_highlight')
                        }}</span>
                    </h1>
                    <p class="text-h6 font-weight-regular text-grey-lighten-1">
                        {{ $t('px_command.subtitle') }}
                    </p>
                </div>

                <StButtonGlass
                    v-if="can('assessments.manage')"
                    :icon="PhBrain"
                    variant="flat"
                    color="success"
                    height="56"
                    class="elevation-xl mt-md-0 mt-4 rounded-xl px-8"
                    @click="wizardDialog = true"
                >
                    {{ $t('px_command.launch_campaign') }}
                </StButtonGlass>
            </div>

            <!-- Dashboard Stats Row -->
            <v-row class="mb-8">
                <v-col
                    cols="12"
                    sm="6"
                    md="3"
                    v-for="(val, key) in stats"
                    :key="key"
                >
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                :color="
                                    key === 'burnout_risk'
                                        ? val === 'Bajo'
                                            ? 'success'
                                            : 'error'
                                        : 'success-lighten-4'
                                "
                                size="56"
                                class="mr-4 rounded-xl"
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
                                    weight="fill"
                                    :color="
                                        key === 'burnout_risk'
                                            ? 'white'
                                            : 'rgb(var(--v-theme-success))'
                                    "
                                    :size="32"
                                />
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ val
                                    }}{{ key === 'avg_engagement' ? '%' : '' }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    {{ $t(`px_command.metrics.${key}`) }}
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
                                class="text-h6 font-weight-bold d-flex align-center text-white"
                            >
                                <PhClockCounterClockwise
                                    color="rgb(var(--v-theme-success))"
                                    class="mr-2"
                                    :size="24"
                                />
                                {{ $t('px_command.history_title') }}
                            </v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-text-field
                                :placeholder="
                                    $t('px_command.search_placeholder')
                                "
                                variant="solo"
                                hide-details
                                density="compact"
                                class="max-width-300 mr-4"
                                rounded="lg"
                            >
                                <template #prepend-inner>
                                    <PhMagnifyingGlass
                                        :size="20"
                                        class="text-grey-lighten-1 mr-2"
                                    />
                                </template>
                            </v-text-field>
                        </v-toolbar>

                        <v-divider color="white" class="opacity-10"></v-divider>

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
                                        class="mr-1 mb-1"
                                    >
                                        {{
                                            topicOptions.find(
                                                (o) => o.value === t,
                                            )?.title || t
                                        }}
                                    </v-chip>
                                </div>
                            </template>

                            <template #[`item.mode`]="{ item }">
                                <v-chip
                                    size="small"
                                    variant="outlined"
                                    color="success-lighten-1"
                                >
                                    <component
                                        class="mr-2"
                                        :is="
                                            modeOptions.find(
                                                (o) => o.value === item.mode,
                                            )?.icon || PhRadio
                                        "
                                        :size="14"
                                        weight="bold"
                                    />
                                    {{
                                        modeOptions.find(
                                            (o) => o.value === item.mode,
                                        )?.title || item.mode
                                    }}
                                </v-chip>
                            </template>

                            <template #[`item.impact`]="{ item }">
                                <div
                                    class="d-flex align-center"
                                    style="min-width: 120px"
                                >
                                    <v-progress-linear
                                        :model-value="
                                            item.status === 'active' ? 75 : 0
                                        "
                                        color="success"
                                        height="6"
                                        rounded
                                        class="mr-3"
                                    ></v-progress-linear>
                                    <span class="text-caption text-white">{{
                                        item.status === 'active' ? '75%' : '0%'
                                    }}</span>
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

                            <template #[`item.actions`]="{ item }">
                                <v-btn
                                    variant="text"
                                    size="small"
                                    color="grey-lighten-1"
                                >
                                    <PhDotsThreeVertical :size="20" />
                                </v-btn>
                                <v-btn
                                    v-if="item.status === 'active'"
                                    variant="tonal"
                                    size="small"
                                    color="success"
                                    class="ml-2"
                                >
                                    <PhChartLineUp :size="20" />
                                </v-btn>
                            </template>

                            <template #no-data>
                                <div class="pa-12 text-center">
                                    <PhShieldCheckOutline
                                        :size="64"
                                        class="text-grey-darken-3 mb-4"
                                        weight="light"
                                    />
                                    <p class="text-grey mb-4">
                                        {{ $t('px_command.no_campaigns') }}
                                    </p>
                                    <StButtonGlass
                                        color="success"
                                        :icon="PhPlus"
                                        @click="wizardDialog = true"
                                    >
                                        {{ $t('px_command.launch_first') }}
                                    </StButtonGlass>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Premium Wizard PX -->
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
                        class="bg-success-darken-4 pa-6 d-flex flex-column border-right-auth"
                    >
                        <div class="mb-8">
                            <PhHeartbeat
                                color="rgb(var(--v-theme-success))"
                                :size="32"
                                weight="fill"
                                class="mb-2"
                            />
                            <h3
                                class="text-h5 font-weight-bold mb-1 text-white"
                            >
                                {{ $t('px_command.wizard.title') }}
                            </h3>
                            <p class="text-caption text-grey-lighten-1">
                                {{ $t('px_command.wizard.subtitle') }}
                            </p>
                        </div>

                        <div class="flex-grow-1">
                            <v-list
                                bg-color="transparent"
                                density="compact"
                                nav
                                class="pa-0 v-list-wizard success-theme"
                            >
                                <v-list-item
                                    :active="step === 1"
                                    class="mb-2 rounded-lg"
                                    @click="step = 1"
                                >
                                    <template #prepend>
                                        <PhNumberCircleOne
                                            :size="20"
                                            class="mr-3"
                                            :color="
                                                step === 1
                                                    ? 'rgb(var(--v-theme-success))'
                                                    : 'grey'
                                            "
                                        />
                                    </template>
                                    <v-list-item-title>{{
                                        $t('px_command.wizard.step1_title')
                                    }}</v-list-item-title>
                                </v-list-item>
                                <v-list-item
                                    :active="step === 2"
                                    :disabled="step < 2"
                                    class="mb-2 rounded-lg"
                                    @click="step = 2"
                                >
                                    <template #prepend>
                                        <PhNumberCircleTwo
                                            :size="20"
                                            class="mr-3"
                                            :color="
                                                step === 2
                                                    ? 'rgb(var(--v-theme-success))'
                                                    : 'grey'
                                            "
                                        />
                                    </template>
                                    <v-list-item-title>{{
                                        $t('px_command.wizard.step2_title')
                                    }}</v-list-item-title>
                                </v-list-item>
                                <v-list-item
                                    :active="step === 3"
                                    :disabled="step < 3"
                                    class="mb-2 rounded-lg"
                                    @click="step = 3"
                                >
                                    <template #prepend>
                                        <PhNumberCircleThree
                                            :size="20"
                                            class="mr-3"
                                            :color="
                                                step === 3
                                                    ? 'rgb(var(--v-theme-success))'
                                                    : 'grey'
                                            "
                                        />
                                    </template>
                                    <v-list-item-title>{{
                                        $t('px_command.wizard.step3_title')
                                    }}</v-list-item-title>
                                </v-list-item>
                                <v-list-item
                                    :active="step === 4"
                                    :disabled="step < 4"
                                    class="mb-2 rounded-lg"
                                    @click="step = 4"
                                >
                                    <template #prepend>
                                        <PhNumberCircleFour
                                            :size="20"
                                            class="mr-3"
                                            :color="
                                                step === 4
                                                    ? 'rgb(var(--v-theme-success))'
                                                    : 'grey'
                                            "
                                        />
                                    </template>
                                    <v-list-item-title>{{
                                        $t('px_command.wizard.step4_title')
                                    }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </div>

                        <v-btn
                            block
                            variant="tonal"
                            color="white"
                            class="rounded-lg"
                            @click="wizardDialog = false"
                        >
                            {{ $t('px_command.wizard.close') }}
                        </v-btn>
                    </v-col>

                    <!-- Content Area -->
                    <v-col
                        cols="9"
                        class="pa-0 d-flex flex-column bg-surface-dark h-100"
                    >
                        <v-card-text
                            class="pa-8 flex-grow-1 overflow-y-auto pb-0"
                        >
                            <!-- Step 1: Topics -->
                            <v-window v-model="step" class="h-100">
                                <v-window-item :value="1">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline text-success font-weight-black mb-1"
                                        >
                                            PASO 1
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step1.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step1.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <v-text-field
                                        v-model="newCampaign.name"
                                        :label="
                                            $t(
                                                'px_command.wizard.step1.name_label',
                                            )
                                        "
                                        :placeholder="
                                            $t(
                                                'px_command.wizard.step1.name_placeholder',
                                            )
                                        "
                                        variant="filled"
                                        color="success"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-6 rounded-lg"
                                    ></v-text-field>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        {{
                                            $t(
                                                'px_command.wizard.step1.dimensions',
                                            )
                                        }}
                                    </h3>
                                    <v-row>
                                        <v-col
                                            cols="6"
                                            v-for="topic in topicOptions"
                                            :key="topic.value"
                                        >
                                            <v-card
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
                                                :class="[
                                                    'pa-4 border-auth cursor-pointer rounded-xl transition-all',
                                                    newCampaign.topics?.includes(
                                                        topic.value,
                                                    )
                                                        ? 'bg-success-darken-3 border-success elevation-xl'
                                                        : 'bg-white-opacity-5',
                                                ]"
                                                flat
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                >
                                                    <v-avatar
                                                        :color="
                                                            newCampaign.topics?.includes(
                                                                topic.value,
                                                            )
                                                                ? 'success'
                                                                : 'grey-darken-3'
                                                        "
                                                        size="40"
                                                        class="mr-3"
                                                    >
                                                        <component
                                                            :is="topic.icon"
                                                            :size="20"
                                                            weight="fill"
                                                        />
                                                    </v-avatar>
                                                    <div
                                                        class="text-subtitle-1 font-weight-bold text-white"
                                                    >
                                                        {{ topic.title }}
                                                    </div>
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Step 2: Mode & Frequency -->
                                <v-window-item :value="2">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline text-success font-weight-black mb-1"
                                        >
                                            PASO 2
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step2.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step2.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <v-row class="mb-6">
                                        <v-col
                                            cols="12"
                                            v-for="mode in modeOptions"
                                            :key="mode.value"
                                        >
                                            <v-card
                                                @click="
                                                    newCampaign.mode =
                                                        mode.value as any
                                                "
                                                :class="[
                                                    'pa-4 border-auth cursor-pointer rounded-xl transition-all',
                                                    newCampaign.mode ===
                                                    mode.value
                                                        ? 'bg-success-darken-3 border-success elevation-xl'
                                                        : 'bg-white-opacity-5',
                                                ]"
                                                flat
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                >
                                                    <v-avatar
                                                        :color="
                                                            newCampaign.mode ===
                                                            mode.value
                                                                ? 'success'
                                                                : 'grey-darken-3'
                                                        "
                                                        size="48"
                                                        class="mr-4"
                                                    >
                                                        <component
                                                            :is="mode.icon"
                                                            :size="24"
                                                            weight="duotone"
                                                        />
                                                    </v-avatar>
                                                    <div class="flex-grow-1">
                                                        <div
                                                            class="text-h6 font-weight-bold text-white"
                                                        >
                                                            {{ mode.title }}
                                                        </div>
                                                        <div
                                                            class="text-caption text-grey-lighten-1"
                                                        >
                                                            {{ mode.desc }}
                                                        </div>
                                                    </div>
                                                    <PhCheckCircle
                                                        v-if="
                                                            newCampaign.mode ===
                                                            mode.value
                                                        "
                                                        weight="fill"
                                                        :size="24"
                                                        color="rgb(var(--v-theme-success))"
                                                    />
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>

                                    <v-alert
                                        v-if="
                                            newCampaign.mode ===
                                            'agent_autonomous'
                                        "
                                        type="info"
                                        variant="tonal"
                                        class="border-success rounded-xl opacity-90"
                                    >
                                        <PhRobot
                                            :size="20"
                                            class="d-inline-block mr-2"
                                            weight="duotone"
                                        />
                                        <strong>{{
                                            $t(
                                                'px_command.wizard.step2.ai_enabled',
                                            )
                                        }}</strong>
                                        {{
                                            $t(
                                                'px_command.wizard.step2.ai_desc',
                                            )
                                        }}
                                    </v-alert>
                                </v-window-item>

                                <!-- Step 3: Scope -->
                                <v-window-item :value="3">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline text-success font-weight-black mb-1"
                                        >
                                            PASO 3
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step3.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step3.desc',
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <v-select
                                        v-model="newCampaign.scope!.type"
                                        :label="
                                            $t(
                                                'px_command.wizard.step3.segmentation',
                                            )
                                        "
                                        :items="scopeOptions"
                                        variant="filled"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-6 rounded-lg"
                                    ></v-select>

                                    <div
                                        class="bg-white-opacity-5 border-auth pa-8 rounded-xl text-center"
                                        v-if="
                                            newCampaign.scope!.type ===
                                            'randomized_sample'
                                        "
                                    >
                                        <div
                                            class="text-h2 font-weight-black mb-2 text-white"
                                        >
                                            {{ newCampaign.scope!.target_pct }}%
                                        </div>
                                        <div
                                            class="text-subtitle-1 text-grey mb-6"
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
                                            color="success"
                                            thumb-label
                                            class="mx-4"
                                        ></v-slider>
                                        <p
                                            class="text-caption text-grey-lighten-1"
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
                                </v-window-item>

                                <!-- Step 4: Confirmation -->
                                <v-window-item :value="4">
                                    <div class="mb-8 text-center">
                                        <v-avatar
                                            color="success"
                                            size="80"
                                            class="elevation-xl mb-4"
                                        >
                                            <PhShieldCheck
                                                :size="48"
                                                color="white"
                                                weight="fill"
                                            />
                                        </v-avatar>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step4.title',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            {{
                                                $t(
                                                    'px_command.wizard.step4.desc',
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
                                            <v-list-item class="px-6 py-4">
                                                <template #prepend>
                                                    <PhTag
                                                        :size="24"
                                                        class="text-success mr-4"
                                                    />
                                                </template>
                                                <v-list-item-title
                                                    class="font-weight-bold mb-1 text-white"
                                                    >{{
                                                        newCampaign.name
                                                    }}</v-list-item-title
                                                >
                                                <v-list-item-subtitle
                                                    class="text-grey"
                                                    >{{
                                                        $t(
                                                            'px_command.wizard.step4.name',
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
                                                        class="px-6 py-4"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold mb-1 text-white"
                                                            >{{
                                                                modeOptions.find(
                                                                    (o) =>
                                                                        o.value ===
                                                                        newCampaign.mode,
                                                                )?.title
                                                            }}</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >{{
                                                                $t(
                                                                    'px_command.wizard.step4.distribution',
                                                                )
                                                            }}</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                                <v-col cols="6" class="pa-0">
                                                    <v-list-item
                                                        class="px-6 py-4"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold mb-1 text-white"
                                                            >{{
                                                                newCampaign
                                                                    .topics
                                                                    ?.length
                                                            }}
                                                            {{
                                                                $t(
                                                                    'px_command.wizard.step4.dimensions',
                                                                )
                                                            }}</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >{{
                                                                $t(
                                                                    'px_command.wizard.step4.metrics',
                                                                )
                                                            }}</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                            </v-row>
                                        </v-list>
                                    </v-card>

                                    <v-alert
                                        type="success"
                                        variant="tonal"
                                        border="start"
                                        class="rounded-xl px-1 py-1"
                                    >
                                        <div class="d-flex pa-2 align-center">
                                            <PhRobot
                                                :size="24"
                                                class="text-success mr-3 flex-shrink-0"
                                                weight="duotone"
                                            />
                                            <div>
                                                {{
                                                    $t(
                                                        'px_command.wizard.step4.ai_desc',
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </v-alert>
                                </v-window-item>
                            </v-window>
                        </v-card-text>

                        <!-- Footer Actions -->
                        <v-divider color="white" class="opacity-10"></v-divider>
                        <div
                            class="pa-8 d-flex justify-space-between align-center"
                        >
                            <StButtonGlass
                                v-if="step > 1"
                                variant="text"
                                :icon="PhCaretLeft"
                                color="grey-lighten-1"
                                @click="step--"
                                >{{
                                    $t('px_command.wizard.actions.back')
                                }}</StButtonGlass
                            >
                            <div v-else></div>

                            <StButtonGlass
                                v-if="step < 4"
                                color="success"
                                height="48"
                                class="rounded-lg px-8"
                                :icon="PhCaretRight"
                                right-icon
                                @click="step++"
                                >{{
                                    $t('px_command.wizard.actions.next')
                                }}</StButtonGlass
                            >

                            <StButtonGlass
                                v-else
                                color="success"
                                height="48"
                                class="shadow-premium rounded-lg px-8"
                                :loading="saving"
                                :icon="PhSealCheck"
                                @click="saveCampaign"
                                >{{
                                    $t('px_command.wizard.actions.launch')
                                }}</StButtonGlass
                            >
                        </div>
                    </v-col>
                </v-row>
            </v-card>
        </v-dialog>
    </AppLayout>
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

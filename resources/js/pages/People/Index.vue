<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import { ref } from 'vue';
import FormSchema from '../form-template/FormSchema.vue';
import configJson from './people-form/config.json';
import filtersJson from './people-form/filters.json';
import itemFormJson from './people-form/itemForm.json';

import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import AssessmentChat from '@/components/Talent/AssessmentChat.vue';
import DevelopmentTab from '@/components/Talent/DevelopmentTab.vue';
import FeedbackRequestDialog from '@/components/Talent/FeedbackRequestDialog.vue';
import LearningBlueprintPanel from '@/components/Talent/LearningBlueprintPanel.vue';
import {
    PhArrowsClockwise,
    PhBuildings,
    PhCalendar,
    PhChartPolar,
    PhCompass,
    PhCornersOut,
    PhEnvelope,
    PhEyeSlash,
    PhRoadHorizon,
    PhRocketLaunch,
    PhTimer,
    PhUser,
    PhX,
} from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import CareerPathExplorer from '../ScenarioPlanning/CareerPathExplorer.vue';
import tableConfigJson from './people-form/tableConfig.json';

defineOptions({ layout: AppLayout });

const { t } = useI18n();

// Load configs from JSON files
const config: Config = {
    ...configJson,
    titulo: t('people_module.title'),
    descripcion: t('people_module.description'),
} as Config;

const tableConfig: TableConfig = {
    ...tableConfigJson,
    headers: (tableConfigJson.headers as any[]).map((h) => ({
        ...h,
        text:
            h.value === 'actions'
                ? ''
                : t(`form_schema.headers.${h.value}`, h.text),
    })),
} as unknown as TableConfig;

const itemForm: ItemForm = itemFormJson as unknown as ItemForm;
const filters: FilterConfig[] = filtersJson as unknown as FilterConfig[];

const formatDate = (value: any): string => {
    if (!value) return '';
    try {
        const date = new Date(value);
        if (!isNaN(date.getTime())) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    } catch (e) {
        void e;
        return value as string;
    }
    return value as string;
};

const formRef = ref(null);

const handleRefresh = () => {
    if (formRef.value) {
        (formRef.value as any).loadItems();
    }
};
</script>

<template>
    <FormSchema
        ref="formRef"
        :config="config"
        :table-config="tableConfig"
        :item-form="itemForm"
        :filters="filters"
        :enable-row-detail="true"
    >
        <template #detail="{ item, tab, setTab, sync, close }">
            <div
                class="relative mb-6 overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <div
                    class="flex flex-col items-start justify-between gap-4 md:flex-row"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/10 shadow-lg shadow-indigo-500/20"
                        >
                            <PhUser :size="32" class="text-indigo-400" />
                        </div>
                        <div>
                            <h2
                                class="text-2xl font-black tracking-tight text-white"
                            >
                                {{ item.first_name }} {{ item.last_name }}
                            </h2>
                            <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1">
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-white/50"
                                >
                                    <PhEnvelope :size="14" />
                                    {{ item.email }}
                                </span>
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-white/50"
                                >
                                    <PhBuildings :size="14" />
                                    {{
                                        item.department?.name ||
                                        item.department_id
                                    }}
                                </span>
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-white/50"
                                >
                                    <PhCalendar :size="14" />
                                    {{ t('people_module.profile.hired') }}:
                                    {{ formatDate(item.hire_date) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col items-center justify-between border-t border-white/5 pt-6 md:flex-row"
                >
                    <div
                        class="flex items-center gap-2 overflow-x-auto pb-4 md:pb-0"
                    >
                        <button
                            v-for="tValue in [
                                {
                                    val: 'active',
                                    icon: PhChartPolar,
                                    label: t(
                                        'people_module.tabs.active_skills',
                                    ),
                                },
                                {
                                    val: 'psychometric',
                                    icon: PhRocketLaunch,
                                    label: t('people_module.tabs.potential'),
                                },
                                {
                                    val: 'development',
                                    icon: PhRoadHorizon,
                                    label: t('talent_development.title'),
                                },
                                {
                                    val: 'history',
                                    icon: PhTimer,
                                    label: t('people_module.tabs.history'),
                                },
                                {
                                    val: 'career',
                                    icon: PhCompass,
                                    label: t(
                                        'people_module.tabs.career',
                                        'Career & Hub',
                                    ),
                                },
                            ]"
                            :key="tValue.val"
                            @click="setTab(tValue.val)"
                            :class="[
                                'flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold tracking-tight transition-all duration-300',
                                tab === tValue.val
                                    ? 'border border-indigo-500/30 bg-indigo-500/20 text-indigo-300'
                                    : 'border border-transparent text-white/40 hover:bg-white/5 hover:text-white',
                            ]"
                        >
                            <component :is="tValue.icon" :size="18" />
                            {{ tValue.label }}
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <StButtonGlass
                            size="sm"
                            variant="secondary"
                            :icon="PhArrowsClockwise"
                            @click="sync"
                        >
                            {{ t('people_module.profile.sync_btn') }}
                        </StButtonGlass>
                        <StButtonGlass
                            size="sm"
                            variant="primary"
                            :icon="PhCornersOut"
                            @click="$inertia.visit(`/people/${item.id}`)"
                        >
                            {{ t('people_module.profile.view_profile') }}
                        </StButtonGlass>
                        <StButtonGlass
                            size="sm"
                            variant="ghost"
                            :icon="PhX"
                            circle
                            @click="close"
                        />
                    </div>
                </div>

                <div
                    class="mt-6 animate-in duration-500 fade-in slide-in-from-top-4"
                >
                    <div v-if="tab === 'active'">
                        <div v-if="item.skills?.length" class="space-y-4">
                            <h3
                                class="text-xs font-black tracking-widest text-white/30 uppercase"
                            >
                                {{ t('people_module.skills.title') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="skill in item.skills"
                                    :key="skill.id"
                                    class="group flex items-center gap-3 rounded-2xl border border-white/5 bg-white/5 p-3 pr-4 transition-colors hover:bg-white/10"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-400 transition-transform group-hover:scale-110"
                                    >
                                        <PhChartPolar :size="20" />
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-bold tracking-tight text-white uppercase"
                                        >
                                            {{ skill.name }}
                                        </div>
                                        <div
                                            class="mt-0.5 flex items-center gap-2"
                                        >
                                            <StBadgeGlass
                                                :variant="
                                                    skill.pivot
                                                        ?.current_level >=
                                                    skill.pivot?.required_level
                                                        ? 'success'
                                                        : 'warning'
                                                "
                                            >
                                                {{ skill.pivot?.current_level }}
                                                /
                                                {{
                                                    skill.pivot?.required_level
                                                }}
                                            </StBadgeGlass>
                                            <span
                                                class="text-[10px] font-bold tracking-widest text-white/30 uppercase"
                                            >
                                                {{
                                                    t(
                                                        'people_module.skills.level_label',
                                                    )
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-white/5 py-12 text-center"
                        >
                            <div
                                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 text-white/20"
                            >
                                <PhChartPolar :size="32" />
                            </div>
                            <div class="text-sm font-medium text-white/40">
                                {{ t('people_module.skills.empty') }}
                            </div>
                        </div>
                    </div>

                    <div v-if="tab === 'psychometric'">
                        <div
                            v-if="item.psychometric_profiles?.length"
                            class="space-y-6"
                        >
                            <div
                                class="flex items-center gap-3 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-400"
                            >
                                <PhRocketLaunch :size="24" />
                                <div class="text-sm font-medium">
                                    {{
                                        item.metadata?.summary_report ||
                                        t('people_module.potential.analyzed_ok')
                                    }}
                                </div>
                            </div>

                            <div
                                v-if="item.metadata?.blind_spots?.length"
                                class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-5"
                            >
                                <div
                                    class="mb-3 flex items-center gap-3 text-amber-400"
                                >
                                    <PhEyeSlash :size="20" />
                                    <h4
                                        class="text-sm font-black tracking-widest uppercase"
                                    >
                                        {{
                                            t(
                                                'people_module.potential.blind_spots',
                                            )
                                        }}
                                    </h4>
                                </div>
                                <ul class="space-y-2">
                                    <li
                                        v-for="(spot, i) in item.metadata
                                            .blind_spots"
                                        :key="i"
                                        class="flex items-start gap-2 text-sm text-white/70"
                                    >
                                        <div
                                            class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-amber-500"
                                        />
                                        {{ spot }}
                                    </li>
                                </ul>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div
                                    v-for="trait in item.psychometric_profiles"
                                    :key="trait.id"
                                    class="rounded-2xl border border-white/10 bg-white/5 p-5 transition-colors hover:border-indigo-500/30"
                                >
                                    <div
                                        class="mb-3 flex items-end justify-between"
                                    >
                                        <span
                                            class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                                        >
                                            {{ trait.trait_name }}
                                        </span>
                                        <span
                                            class="text-xl font-black text-white"
                                        >
                                            {{
                                                (trait.score * 100).toFixed(0)
                                            }}%
                                        </span>
                                    </div>
                                    <div
                                        class="h-2 w-full overflow-hidden rounded-full border border-white/5 bg-white/5"
                                    >
                                        <div
                                            class="h-full rounded-full bg-linear-to-r from-indigo-500 to-indigo-400"
                                            :style="{
                                                width: `${trait.score * 100}%`,
                                            }"
                                        />
                                    </div>
                                    <p
                                        class="mt-3 text-xs leading-relaxed text-white/50"
                                    >
                                        {{ trait.rationale }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-center gap-4 pt-4">
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhArrowsClockwise"
                                    @click="handleRefresh"
                                >
                                    {{
                                        t(
                                            'people_module.potential.reevaluate_btn',
                                        )
                                    }}
                                </StButtonGlass>
                                <FeedbackRequestDialog
                                    :subject-id="item.id"
                                    @requested="handleRefresh"
                                />
                            </div>
                        </div>
                        <div
                            v-else
                            class="overflow-hidden rounded-3xl border border-white/10"
                        >
                            <AssessmentChat
                                :person-id="item.id"
                                @completed="handleRefresh"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <div v-if="tab === 'development'" class="space-y-4">
                        <DevelopmentTab
                            :person-id="item.id"
                            :skills="item.skills || []"
                        />
                    </div>

                    <div v-if="tab === 'history'">
                        <div
                            class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-white/5 py-12 text-center"
                        >
                            <div
                                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 text-white/20"
                            >
                                <PhTimer :size="32" />
                            </div>
                            <div
                                class="max-w-sm text-sm font-medium text-white/40"
                            >
                                {{
                                    t(
                                        'talent_development.history.empty',
                                        t('people_module.history.empty'),
                                    )
                                }}
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="tab === 'career'"
                        class="grid grid-cols-1 gap-6 lg:grid-cols-2"
                    >
                        <CareerPathExplorer :people-id="item.id" />
                        <LearningBlueprintPanel :people-id="item.id" />
                    </div>
                </div>
            </div>
        </template>
    </FormSchema>
</template>

<style scoped>
/* Custom styles */
</style>

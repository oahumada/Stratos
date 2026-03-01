<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import {
    PhCheckCircle,
    PhCircle,
    PhInfo,
    PhMagicWand,
    PhRobot,
    PhStar,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import FormSchema from '../form-template/FormSchema.vue';

const { t } = useI18n();

// Import JSON configs
import configJson from './competencies-form/config.json';
import filtersJson from './competencies-form/filters.json';
import itemFormJson from './competencies-form/itemForm.json';
import tableConfigJson from './competencies-form/tableConfig.json';

defineOptions({ layout: AppLayout });

const detailTab = ref('info');

const curating = ref(false);

const curateCompetency = async (id: number, refresh: () => void) => {
    curating.value = true;
    try {
        await axios.post(
            `/api/strategic-planning/curator/competencies/${id}/curate`,
        );
        refresh();
    } catch (error) {
        console.error('Error curating competency:', error);
    } finally {
        curating.value = false;
    }
};
</script>

<template>
    <div class="ma-4">
        <FormSchema
            :config="configJson as Config"
            :tableConfig="tableConfigJson as unknown as TableConfig"
            :itemForm="itemFormJson as unknown as ItemForm"
            :filters="filtersJson as FilterConfig[]"
        >
            <template #detail="{ item, refresh }">
                <v-tabs
                    v-model="detailTab"
                    color="indigo-accent-2"
                    class="mb-4"
                >
                    <v-tab value="info" class="text-none">
                        <component :is="PhInfo" :size="18" class="mr-2" />
                        {{ t('competencies_module.tabs.info') }}
                    </v-tab>
                    <v-tab value="ai" class="text-none">
                        <component :is="PhRobot" :size="18" class="mr-2" />
                        {{ t('competencies_module.tabs.ai_design') }}
                    </v-tab>
                </v-tabs>

                <v-window v-model="detailTab">
                    <v-window-item value="info">
                        <v-card flat border class="pa-4 glass-card rounded-xl">
                            <div
                                class="text-subtitle-1 font-weight-bold font-premium mb-2 text-white"
                            >
                                {{ item.name }}
                            </div>
                            <template v-if="item.description">
                                <div class="text-body-2 mb-4 text-slate-300">
                                    {{ item.description }}
                                </div>
                            </template>
                            <template v-else>
                                <div
                                    class="text-body-2 font-italic mb-4 text-slate-500"
                                >
                                    {{
                                        t(
                                            'competencies_module.info_section.no_description',
                                        )
                                    }}
                                </div>
                            </template>

                            <v-divider
                                class="mb-4"
                                style="opacity: 0.05"
                            ></v-divider>

                            <div class="d-flex align-center gap-3">
                                <v-chip
                                    size="small"
                                    :color="
                                        item.status === 'active'
                                            ? 'emerald-accent-2'
                                            : 'slate-400'
                                    "
                                    variant="flat"
                                    class="font-weight-bold rounded-lg"
                                >
                                    <component
                                        :is="
                                            item.status === 'active'
                                                ? PhCheckCircle
                                                : PhCircle
                                        "
                                        size="14"
                                        class="mr-1"
                                    />
                                    {{
                                        item.status === 'active'
                                            ? t(
                                                  'competencies_module.info_section.status_active',
                                              )
                                            : t(
                                                  'competencies_module.info_section.status_draft',
                                              )
                                    }}
                                </v-chip>

                                <v-chip
                                    v-if="item.skills_count > 0"
                                    size="small"
                                    color="indigo-accent-1"
                                    variant="tonal"
                                    class="font-weight-bold rounded-lg"
                                >
                                    <component
                                        :is="PhStar"
                                        size="14"
                                        class="mr-1"
                                    />
                                    {{
                                        t(
                                            'competencies_module.info_section.skills_count',
                                            { count: item.skills_count },
                                        )
                                    }}
                                </v-chip>
                            </div>

                            <div v-if="item.agent" class="mt-4">
                                <div class="text-caption mb-2 text-slate-400">
                                    {{
                                        t(
                                            'competencies_module.info_section.ai_agent',
                                        )
                                    }}:
                                </div>
                                <v-chip
                                    size="small"
                                    color="indigo"
                                    variant="flat"
                                    class="rounded-lg"
                                >
                                    <component
                                        :is="PhRobot"
                                        size="14"
                                        class="mr-2"
                                    />
                                    {{ item.agent.name }}
                                </v-chip>
                            </div>
                        </v-card>
                    </v-window-item>

                    <v-window-item value="ai">
                        <v-card flat border class="pa-4 glass-card rounded-xl">
                            <div
                                class="d-flex align-center justify-space-between mb-4 flex-wrap gap-4"
                            >
                                <div>
                                    <div
                                        class="text-subtitle-1 font-weight-bold font-premium text-white"
                                    >
                                        {{
                                            t(
                                                'competencies_module.ai_section.title',
                                                {
                                                    agent:
                                                        item.agent?.name ||
                                                        'Stratos AI',
                                                },
                                            )
                                        }}
                                    </div>
                                    <div class="text-caption text-slate-400">
                                        {{
                                            t(
                                                'competencies_module.ai_section.description',
                                            )
                                        }}
                                    </div>
                                </div>
                                <StButtonGlass
                                    variant="primary"
                                    size="md"
                                    :loading="curating"
                                    :icon="PhMagicWand"
                                    @click="curateCompetency(item.id, refresh)"
                                >
                                    {{
                                        t(
                                            'competencies_module.ai_section.curate_btn',
                                        )
                                    }}
                                </StButtonGlass>
                            </div>

                            <v-alert
                                v-if="!item.skills_count"
                                type="info"
                                variant="tonal"
                                class="rounded-xl border border-indigo-500/20"
                                color="indigo-accent-1"
                            >
                                <template #title>{{
                                    t(
                                        'competencies_module.ai_section.alert_title',
                                    )
                                }}</template>
                                {{
                                    t(
                                        'competencies_module.ai_section.alert_desc',
                                    )
                                }}
                            </v-alert>

                            <div
                                v-else
                                class="rounded-xl border border-dashed border-white/10 bg-white/5 py-12 text-center"
                            >
                                <component
                                    :is="PhMagicWand"
                                    :size="48"
                                    class="text-indigo-accent-1 mb-3 opacity-50"
                                />
                                <div class="text-body-2 text-slate-400">
                                    {{
                                        t(
                                            'competencies_module.ai_section.ready_desc',
                                        )
                                    }}
                                </div>
                            </div>
                        </v-card>
                    </v-window-item>
                </v-window>
            </template>
        </FormSchema>
    </div>
</template>

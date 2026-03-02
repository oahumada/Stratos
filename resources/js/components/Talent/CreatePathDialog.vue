<template>
    <v-dialog v-model="dialog" max-width="600" persistent class="glass-dialog">
        <template #activator="{ props: activatorProps }">
            <StButtonGlass
                v-if="!hasActivator"
                v-bind="activatorProps"
                variant="primary"
                :icon="PhPlus"
                size="sm"
            >
                {{ t('talent_development.create_plan.trigger') }}
            </StButtonGlass>
            <button
                v-show="false"
                id="open-create-path-dialog"
                v-bind="activatorProps"
            ></button>
        </template>

        <div
            class="overflow-hidden rounded-[2.5rem] border border-white/10 bg-slate-900/90 p-1 shadow-2xl shadow-indigo-500/20 backdrop-blur-3xl"
        >
            <div
                class="relative overflow-hidden rounded-[2.3rem] bg-gradient-to-br from-indigo-500/10 via-transparent to-transparent p-8"
            >
                <!-- AI Sparkle Effect -->
                <div
                    class="absolute -top-12 -right-12 h-32 w-32 rounded-full bg-indigo-500/20 blur-3xl"
                />

                <!-- Header -->
                <div class="relative mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 text-indigo-400 shadow-lg shadow-indigo-500/20"
                        >
                            <PhSparkle
                                :size="28"
                                weight="fill"
                                class="animate-pulse"
                            />
                        </div>
                        <div>
                            <h2
                                class="text-2xl font-black tracking-tight text-white"
                            >
                                {{ t('talent_development.create_plan.title') }}
                            </h2>
                            <p class="text-xs font-medium text-white/40">
                                {{
                                    t('talent_development.create_plan.subtitle')
                                }}
                            </p>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        :icon="PhX"
                        circle
                        size="sm"
                        @click="dialog = false"
                    />
                </div>

                <!-- Content -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label
                            class="px-1 text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                        >
                            {{
                                t(
                                    'talent_development.create_plan.form.select_skill',
                                )
                            }}
                        </label>

                        <div class="space-y-2">
                            <div
                                v-for="option in skillOptions"
                                :key="option.id"
                                @click="selectedSkillId = option.id"
                                :class="[
                                    'group relative flex cursor-pointer items-center justify-between gap-4 overflow-hidden rounded-2xl border p-4 transition-all duration-300',
                                    selectedSkillId === option.id
                                        ? 'border-indigo-500/50 bg-indigo-500/10'
                                        : 'border-white/5 bg-white/5 hover:border-white/20 hover:bg-white/10',
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="[
                                            'flex h-10 w-10 items-center justify-center rounded-xl border transition-all',
                                            selectedSkillId === option.id
                                                ? 'border-indigo-500/30 bg-indigo-500/20 text-indigo-400'
                                                : 'border-white/10 bg-white/5 text-white/20',
                                        ]"
                                    >
                                        <PhBrain :size="20" />
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-bold text-white uppercase"
                                        >
                                            {{ option.title }}
                                        </div>
                                        <div
                                            class="text-[10px] font-medium tracking-tighter text-white/30 uppercase"
                                        >
                                            {{
                                                t(
                                                    'talent_development.create_plan.form.level_evolution',
                                                    {
                                                        current:
                                                            option.current_level,
                                                        target: option.required_level,
                                                    },
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <StBadgeGlass
                                        :variant="
                                            option.gap > 1 ? 'error' : 'warning'
                                        "
                                        size="sm"
                                    >
                                        Gap: {{ option.gap }}
                                    </StBadgeGlass>
                                    <div
                                        v-if="selectedSkillId === option.id"
                                        class="animate-in text-indigo-400 duration-300 zoom-in"
                                    >
                                        <PhCheckCircle
                                            :size="20"
                                            weight="fill"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Alert -->
                    <div
                        v-if="selectedSkill"
                        class="animate-in rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-5 duration-500 fade-in slide-in-from-top-2"
                    >
                        <div
                            class="mb-3 flex items-center gap-2 text-indigo-400"
                        >
                            <PhTarget :size="18" />
                            <h4
                                class="text-xs font-black tracking-widest uppercase"
                            >
                                {{
                                    t(
                                        'talent_development.create_plan.analysis.title',
                                    )
                                }}
                            </h4>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <div
                                    class="text-[10px] font-bold text-white/40 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.create_plan.analysis.current',
                                        )
                                    }}
                                </div>
                                <div class="text-lg font-black text-white">
                                    {{ selectedSkill.current_level }}
                                </div>
                            </div>
                            <div class="space-y-1 text-right">
                                <div
                                    class="text-[10px] font-bold text-white/40 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.create_plan.analysis.target',
                                        )
                                    }}
                                </div>
                                <div class="text-lg font-black text-white">
                                    {{ selectedSkill.required_level }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="mt-4 border-t border-white/5 pt-3 text-[11px] leading-relaxed text-indigo-100/60"
                        >
                            {{
                                t(
                                    'talent_development.create_plan.analysis.description',
                                    { months: selectedSkill.gap > 1 ? 6 : 3 },
                                )
                            }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 flex justify-end gap-3">
                    <StButtonGlass variant="ghost" @click="dialog = false">
                        {{ t('talent_development.create_plan.actions.cancel') }}
                    </StButtonGlass>
                    <StButtonGlass
                        variant="primary"
                        :loading="generating"
                        :disabled="!selectedSkillId"
                        :icon="PhSparkle"
                        @click="generate"
                    >
                        {{
                            t('talent_development.create_plan.actions.generate')
                        }}
                    </StButtonGlass>
                </div>
            </div>
        </div>
    </v-dialog>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import {
    PhBrain,
    PhCheckCircle,
    PhPlus,
    PhSparkle,
    PhTarget,
    PhX,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    personId: {
        type: Number,
        required: true,
    },
    skills: {
        type: Array as () => any[],
        default: () => [],
    },
    hasActivator: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['generated']);

const dialog = ref(false);
const generating = ref(false);
const selectedSkillId = ref<number | null>(null);

const skillOptions = computed(() => {
    return props.skills
        .filter((s) => {
            const pivot = s.pivot || {};
            return (pivot.required_level || 0) > (pivot.current_level || 0);
        })
        .map((s) => {
            const current = s.pivot?.current_level || 0;
            const required = s.pivot?.required_level || 0;
            const gap = required - current;
            return {
                id: s.id,
                title: s.name,
                current_level: current,
                required_level: required,
                gap: gap,
            };
        });
});

const selectedSkill = computed(() => {
    return skillOptions.value.find((s) => s.id === selectedSkillId.value);
});

const open = () => {
    dialog.value = true;
};

const generate = async () => {
    if (!selectedSkillId.value || !selectedSkill.value) return;

    generating.value = true;
    try {
        const payload = {
            people_id: props.personId,
            skill_id: selectedSkillId.value,
            current_level: selectedSkill.value.current_level,
            target_level: selectedSkill.value.required_level,
        };

        await axios.post('/api/development-paths/generate', payload);

        emit('generated');
        dialog.value = false;
        selectedSkillId.value = null;
    } catch (e) {
        console.error('Error creating path', e);
    } finally {
        generating.value = false;
    }
};

defineExpose({ open });
</script>

<style scoped>
.glass-dialog {
    backdrop-filter: blur(20px);
}
</style>

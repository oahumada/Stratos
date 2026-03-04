<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    modelValue: boolean;
    node: any; // The node data from D3
    scenarioId: number;
}>();

const emit = defineEmits(['update:modelValue', 'saved']);

const { showSuccess, showError } = useNotification();
const api = useApi();

const internalValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const saving = ref(false);

// Form State
const form = ref<any>({
    name: '',
    description: '',
    importance: 3,
    category: '',
    type: '',
    // Pivot fields
    strategic_role: 'target',
    strategic_weight: 10,
    priority: 1,
    required_level: 3,
    is_critical: false,
    rationale: '',
    // Skill specific
    complexity_level: 'tactical',
    domain_tag: '',
    scope_type: 'domain',
});

// Watch for node changes to populate form
watch(
    () => props.node,
    (newNode) => {
        if (newNode) {
            form.value = {
                name: newNode.name || '',
                description: newNode.description || '',
                importance: newNode.importance || 3,
                category: newNode.category || '',
                type: newNode.type_string || newNode.type || '',
                // Pivot fields (checking raw data if available)
                strategic_role:
                    newNode.pivot?.strategic_role ||
                    newNode.strategic_role ||
                    'target',
                strategic_weight:
                    newNode.pivot?.strategic_weight ||
                    newNode.strategic_weight ||
                    10,
                priority: newNode.pivot?.priority || newNode.priority || 1,
                required_level:
                    newNode.pivot?.required_level ||
                    newNode.required_level ||
                    newNode.level ||
                    3,
                is_critical: !!(
                    newNode.pivot?.is_critical || newNode.is_critical
                ),
                rationale: newNode.pivot?.rationale || newNode.rationale || '',
                // Skill specific
                complexity_level: newNode.complexity_level || 'tactical',
                domain_tag: newNode.domain_tag || '',
                scope_type: newNode.scope_type || 'domain',
            };
        }
    },
    { immediate: true },
);

const nodeType = computed(() => {
    if (!props.node) return 'unknown';
    if (props.node.id?.toString().startsWith('cap-')) return 'capability';
    if (props.node.id?.toString().startsWith('comp-')) return 'competency';
    if (props.node.id?.toString().startsWith('skill-')) return 'skill';
    return props.node.type || 'unknown';
});

const nodeColor = computed(() => {
    switch (nodeType.value) {
        case 'capability':
            return 'indigo';
        case 'competency':
            return 'violet';
        case 'skill':
            return 'cyan';
        default:
            return 'white';
    }
});

const handleSave = async () => {
    if (!props.node || !props.scenarioId) return;

    saving.value = true;
    try {
        const idParts = props.node.id.toString().split('-');
        const numericId = idParts[idParts.length - 1];

        if (nodeType.value === 'capability') {
            // Update Capability Base
            await api.patch(`/api/capabilities/${numericId}`, {
                name: form.value.name,
                description: form.value.description,
                importance: form.value.importance,
                category: form.value.category,
                type: form.value.type,
            });

            // Update Pivot
            await api.patch(
                `/api/strategic-planning/scenarios/${props.scenarioId}/capabilities/${numericId}`,
                {
                    strategic_role: form.value.strategic_role,
                    strategic_weight: form.value.strategic_weight,
                    priority: form.value.priority,
                    required_level: form.value.required_level,
                    is_critical: form.value.is_critical,
                    rationale: form.value.rationale,
                },
            );

            showSuccess('Capability updated successfully');
        } else if (nodeType.value === 'competency') {
            await api.patch(`/api/competencies/${numericId}`, {
                name: form.value.name,
                description: form.value.description,
            });
            showSuccess('Competency updated successfully');
        } else if (nodeType.value === 'skill') {
            await api.patch(`/api/skills/${numericId}`, {
                name: form.value.name,
                description: form.value.description,
                category: form.value.category,
                complexity_level: form.value.complexity_level,
                domain_tag: form.value.domain_tag,
                scope_type: form.value.scope_type,
            });
            // Pivot for skill might need more context (active competency),
            // but for now we update the base skill.
            showSuccess('Skill updated successfully');
        }

        emit('saved');
        internalValue.value = false;
    } catch (err: any) {
        showError(err.response?.data?.message || 'Failed to update node');
    } finally {
        saving.value = false;
    }
};

const categories = [
    'technical',
    'leadership',
    'business',
    'operational',
    'behavioral',
];
const strategicRoles = [
    { title: 'Core Target', value: 'target' },
    { title: 'Support', value: 'support' },
    { title: 'Enabler', value: 'enabler' },
    { title: 'Edge', value: 'edge' },
];
const complexityLevels = ['basic', 'tactical', 'strategic'];
const scopeTypes = ['domain', 'global', 'local'];
</script>

<template>
    <v-dialog v-model="internalValue" max-width="700" persistent>
        <StCardGlass
            variant="glass"
            :border-accent="nodeColor"
            class="overflow-hidden bg-[#020617]/95! p-0! backdrop-blur-2xl"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-white/10 bg-white/5 px-8 py-6"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border"
                        :class="[
                            nodeColor === 'indigo'
                                ? 'border-indigo-500/30 bg-indigo-500/10 text-indigo-400'
                                : nodeColor === 'violet'
                                  ? 'border-violet-500/30 bg-violet-500/10 text-violet-400'
                                  : 'border-cyan-500/30 bg-cyan-500/10 text-cyan-400',
                        ]"
                    >
                        <v-icon size="24">
                            {{
                                nodeType === 'capability'
                                    ? 'mdi-shield-check-outline'
                                    : nodeType === 'competency'
                                      ? 'mdi-vector-combine'
                                      : 'mdi-atom'
                            }}
                        </v-icon>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3
                                class="text-xl font-black tracking-tight text-white"
                            >
                                Edit
                                {{
                                    nodeType.charAt(0).toUpperCase() +
                                    nodeType.slice(1)
                                }}
                            </h3>
                            <StBadgeGlass
                                :variant="
                                    nodeColor === 'indigo'
                                        ? 'primary'
                                        : nodeColor === 'violet'
                                          ? 'secondary'
                                          : 'glass'
                                "
                                size="sm"
                            >
                                ID: {{ node?.id }}
                            </StBadgeGlass>
                        </div>
                        <p
                            class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase"
                        >
                            Configuration Protocol
                        </p>
                    </div>
                </div>
                <StButtonGlass
                    variant="ghost"
                    icon="mdi-close"
                    circle
                    @click="internalValue = false"
                />
            </div>

            <!-- Content -->
            <div class="custom-scrollbar max-h-[70vh] overflow-y-auto p-8">
                <div class="space-y-8">
                    <!-- Base Information -->
                    <section class="space-y-6">
                        <div class="flex items-center gap-2">
                            <v-icon size="16" class="text-white/20"
                                >mdi-text-box-outline</v-icon
                            >
                            <h4
                                class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >
                                Base Parameters
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <v-text-field
                                v-model="form.name"
                                label="Display Name"
                                variant="outlined"
                                density="comfortable"
                                color="indigo-400"
                                persistent-placeholder
                                class="glass-input"
                                hide-details
                            />

                            <v-select
                                v-model="form.category"
                                :items="categories"
                                label="Primary Category"
                                variant="outlined"
                                density="comfortable"
                                color="indigo-400"
                                class="glass-input"
                                hide-details
                                v-if="nodeType !== 'competency'"
                            />

                            <v-textarea
                                v-model="form.description"
                                label="Strategic Description"
                                variant="outlined"
                                density="comfortable"
                                color="indigo-400"
                                rows="3"
                                class="glass-input col-span-full"
                                hide-details
                            />
                        </div>
                    </section>

                    <!-- Capability Specific / Pivot -->
                    <section v-if="nodeType === 'capability'" class="space-y-6">
                        <div class="flex items-center gap-2">
                            <v-icon size="16" class="text-indigo-400/60"
                                >mdi-fountain-pen-tip</v-icon
                            >
                            <h4
                                class="text-[10px] font-black tracking-widest text-indigo-400/40 uppercase"
                            >
                                Strategic Context (Scenario Pivot)
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <v-select
                                v-model="form.strategic_role"
                                :items="strategicRoles"
                                label="Strategic Significance"
                                variant="outlined"
                                density="comfortable"
                                color="indigo-400"
                                class="glass-input"
                                hide-details
                            />

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black tracking-widest text-white/20 uppercase"
                                    >Strategic Weight (1-10)</label
                                >
                                <v-slider
                                    v-model="form.strategic_weight"
                                    min="1"
                                    max="10"
                                    step="1"
                                    color="indigo-400"
                                    hide-details
                                    thumb-label
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black tracking-widest text-white/20 uppercase"
                                    >Implementation Priority (1-5)</label
                                >
                                <v-slider
                                    v-model="form.priority"
                                    min="1"
                                    max="5"
                                    step="1"
                                    color="orange-400"
                                    hide-details
                                    thumb-label
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black tracking-widest text-white/20 uppercase"
                                    >Required Mastery Level</label
                                >
                                <v-slider
                                    v-model="form.required_level"
                                    min="1"
                                    max="5"
                                    step="1"
                                    color="emerald-400"
                                    hide-details
                                    thumb-label
                                />
                            </div>

                            <div
                                class="col-span-full rounded-2xl border border-white/5 bg-white/2 p-4"
                            >
                                <v-checkbox
                                    v-model="form.is_critical"
                                    label="Critical Architecture Component"
                                    color="rose-500"
                                    hide-details
                                    density="compact"
                                />
                                <p
                                    class="mt-1 ml-8 text-[11px] font-medium text-white/30"
                                >
                                    If enabled, any performance gap in this node
                                    will trigger high-priority organizational
                                    alerts.
                                </p>
                            </div>

                            <v-textarea
                                v-model="form.rationale"
                                label="Strategic Rationale"
                                variant="outlined"
                                density="comfortable"
                                color="indigo-400"
                                rows="2"
                                class="glass-input col-span-full"
                                placeholder="Why is this capability required for this specific scenario?"
                                hide-details
                            />
                        </div>
                    </section>

                    <!-- Skill Specific -->
                    <section v-if="nodeType === 'skill'" class="space-y-6">
                        <div class="flex items-center gap-2">
                            <v-icon size="16" class="text-cyan-400/60"
                                >mdi-auto-fix</v-icon
                            >
                            <h4
                                class="text-[10px] font-black tracking-widest text-cyan-400/40 uppercase"
                            >
                                Skill Taxonomy Details
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <v-select
                                v-model="form.complexity_level"
                                :items="complexityLevels"
                                label="Complexity"
                                variant="outlined"
                                density="comfortable"
                                color="cyan-400"
                                class="glass-input"
                                hide-details
                            />
                            <v-select
                                v-model="form.scope_type"
                                :items="scopeTypes"
                                label="Scope"
                                variant="outlined"
                                density="comfortable"
                                color="cyan-400"
                                class="glass-input"
                                hide-details
                            />
                            <v-text-field
                                v-model="form.domain_tag"
                                label="Domain Tag"
                                variant="outlined"
                                density="comfortable"
                                color="cyan-400"
                                class="glass-input"
                                hide-details
                            />
                        </div>
                    </section>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-end gap-3 border-t border-white/10 bg-black/40 px-8 py-6"
            >
                <StButtonGlass variant="ghost" @click="internalValue = false"
                    >Cancel</StButtonGlass
                >
                <StButtonGlass
                    variant="primary"
                    icon="mdi-shield-check"
                    @click="handleSave"
                    :loading="saving"
                    class="px-8!"
                >
                    Apply Changes
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
.glass-input :deep(.v-field) {
    background: rgba(255, 255, 255, 0.03) !important;
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.glass-input :deep(.v-field--focused) {
    border-color: rgba(99, 102, 241, 0.4) !important;
    background: rgba(255, 255, 255, 0.05) !important;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

:deep(.v-label) {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5) !important;
}
</style>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import { useApi } from '@/composables/useApi';
import { computed, onMounted, ref } from 'vue';

interface Props {
    modelValue: number | null;
    organizationId: number;
    scopeType?: 'organization' | 'department' | 'role_family';
    scopeId?: number | null;
    disabled?: boolean;
    label?: string;
    hint?: string;
}

interface Scenario {
    id: number;
    name: string;
    description?: string;
    scope_type: string;
    scope_id: number | null;
    decision_status: string;
    parent_id: number | null;
    children_count?: number;
}

const props = withDefaults(defineProps<Props>(), {
    scopeType: 'organization',
    scopeId: null,
    disabled: false,
    label: 'Parent Scenario (Optional)',
    hint: 'Select a parent scenario to inherit mandatory skills',
});

const emit =
    defineEmits<(e: 'update:modelValue', value: number | null) => void>();

const api = useApi();
const loading = ref(false);
const scenarios = ref<Scenario[]>([]);
const search = ref('');

onMounted(() => {
    loadParentCandidates();
});

const loadParentCandidates = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            organization_id: props.organizationId.toString(),
            decision_status: 'approved',
        });

        if (props.scopeType === 'department') {
            params.append('scope_type', 'organization');
        } else if (props.scopeType === 'role_family') {
            params.append('scope_type', 'department,organization');
        }

        const res = await api.get(
            `/api/strategic-planning/scenarios?${params.toString()}`,
        );
        scenarios.value = res.data || [];
    } catch (error) {
        console.error('Error loading parent candidates:', error);
        scenarios.value = [];
    } finally {
        loading.value = false;
    }
};

const filteredScenarios = computed(() => {
    if (!search.value) return scenarios.value;

    const query = search.value.toLowerCase();
    return scenarios.value.filter(
        (s) =>
            s.name.toLowerCase().includes(query) ||
            s.description?.toLowerCase().includes(query),
    );
});

const selectedScenario = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const getScopeIcon = (scopeType: string): string => {
    const map: Record<string, string> = {
        organization: 'mdi-domain',
        department: 'mdi-office-building',
        role_family: 'mdi-account-group',
    };
    return map[scopeType] || 'mdi-circle';
};

const getScopeColor = (scopeType: string): string => {
    const map: Record<string, string> = {
        organization: 'primary',
        department: 'info',
        role_family: 'success',
    };
    return map[scopeType] || 'white';
};

const getStatusBadge = (status: string) => {
    const map: Record<
        string,
        { color: 'glass' | 'primary' | 'secondary' | 'success'; text: string }
    > = {
        draft: { color: 'glass', text: 'Draft' },
        pending_approval: { color: 'secondary', text: 'Pending' },
        approved: { color: 'primary', text: 'Approved' },
        rejected: { color: 'secondary', text: 'Rejected' },
    };
    return map[status] || { color: 'glass', text: status };
};
</script>

<template>
    <div class="parent-scenario-selector space-y-2">
        <label
            v-if="label"
            class="ml-1 text-[10px] font-black tracking-widest text-indigo-400 uppercase"
        >
            {{ label }}
        </label>

        <v-autocomplete
            v-model="selectedScenario"
            v-model:search="search"
            :items="filteredScenarios"
            :loading="loading"
            :disabled="disabled"
            :label="''"
            :hint="hint"
            persistent-hint
            item-title="name"
            item-value="id"
            variant="outlined"
            density="comfortable"
            bg-color="rgba(255, 255, 255, 0.05)"
            color="indigo-400"
            base-color="white"
            clearable
            prepend-inner-icon="mdi-family-tree"
            class="custom-glass-autocomplete"
            @update:model-value="$emit('update:modelValue', $event)"
        >
            <template #item="{ props: itemProps, item }">
                <v-list-item
                    v-bind="itemProps"
                    class="transition-colors hover:bg-indigo-500/10"
                >
                    <template #prepend>
                        <v-icon
                            :icon="getScopeIcon(item.raw.scope_type)"
                            :color="getScopeColor(item.raw.scope_type)"
                            size="small"
                        />
                    </template>

                    <v-list-item-title class="text-sm font-bold text-white">
                        {{ item.raw.name }}
                    </v-list-item-title>

                    <v-list-item-subtitle
                        v-if="item.raw.description"
                        class="mt-1 text-xs text-wrap text-white/50"
                    >
                        {{ item.raw.description }}
                    </v-list-item-subtitle>

                    <template #append>
                        <div class="flex flex-col items-end gap-1">
                            <StBadgeGlass
                                :variant="
                                    getStatusBadge(item.raw.decision_status)
                                        .color
                                "
                                size="xs"
                            >
                                {{
                                    getStatusBadge(item.raw.decision_status)
                                        .text
                                }}
                            </StBadgeGlass>

                            <StBadgeGlass
                                v-if="
                                    item.raw.children_count &&
                                    item.raw.children_count > 0
                                "
                                variant="glass"
                                size="xs"
                            >
                                <v-icon
                                    icon="mdi-file-tree"
                                    size="10"
                                    class="mr-1"
                                />
                                {{ item.raw.children_count }} children
                            </StBadgeGlass>
                        </div>
                    </template>
                </v-list-item>
            </template>

            <template #selection="{ item }">
                <div class="flex items-center">
                    <v-icon
                        :icon="getScopeIcon(item.raw.scope_type)"
                        :color="getScopeColor(item.raw.scope_type)"
                        size="16"
                        class="mr-2"
                    />
                    <span class="text-sm font-bold text-white">{{
                        item.raw.name
                    }}</span>
                </div>
            </template>

            <template #no-data>
                <div class="p-4 text-center">
                    <div
                        class="text-xs font-black tracking-widest text-white/40 uppercase"
                    >
                        No approved candidate scenarios available
                    </div>
                </div>
            </template>

            <template #details="{ message }">
                <span class="text-[10px] font-medium text-white/40">{{
                    message
                }}</span>
            </template>
        </v-autocomplete>

        <!-- Information Alert -->
        <div
            v-if="modelValue"
            class="mt-2 flex items-center gap-3 rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-4 py-3"
        >
            <v-icon icon="mdi-information" color="indigo-400" size="18" />
            <span class="text-xs font-medium text-indigo-100/70">
                Mandatory skills from the parent architecture will be seamlessly
                synchronized.
            </span>
        </div>
    </div>
</template>

<style scoped>
.parent-scenario-selector {
    width: 100%;
}

/* Base custom styles to ensure glass feel for vuetify autocomplete */
:deep(.v-field) {
    border-radius: 12px !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
:deep(.v-field__input) {
    color: white !important;
}
:deep(.v-field__overlay) {
    background-color: transparent !important;
}
:deep(.v-list) {
    background-color: rgba(15, 23, 42, 0.95) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}
</style>

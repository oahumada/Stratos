<script setup lang="ts">
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
    label: 'Escenario Padre (Opcional)',
    hint: 'Selecciona un escenario padre para heredar skills obligatorias',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | null): void;
}>();

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
        // Obtener escenarios de nivel superior (organization scope) o del mismo nivel
        const params = new URLSearchParams({
            organization_id: props.organizationId.toString(),
            decision_status: 'approved', // Solo aprobados pueden ser padres
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
    return map[scopeType] || 'grey';
};

const getStatusBadge = (status: string) => {
    const map: Record<string, { color: string; text: string }> = {
        draft: { color: 'grey', text: 'Borrador' },
        pending_approval: { color: 'warning', text: 'Pendiente' },
        approved: { color: 'success', text: 'Aprobado' },
        rejected: { color: 'error', text: 'Rechazado' },
    };
    return map[status] || { color: 'grey', text: status };
};

// `clearSelection` removed — not referenced in template
</script>

<template>
    <div class="parent-scenario-selector">
        <v-autocomplete
            v-model="selectedScenario"
            v-model:search="search"
            :items="filteredScenarios"
            :loading="loading"
            :disabled="disabled"
            :label="label"
            :hint="hint"
            persistent-hint
            item-title="name"
            item-value="id"
            variant="outlined"
            clearable
            prepend-inner-icon="mdi-family-tree"
            @update:model-value="$emit('update:modelValue', $event)"
        >
            <template #item="{ props: itemProps, item }">
                <v-list-item v-bind="itemProps">
                    <template #prepend>
                        <v-icon
                            :icon="getScopeIcon(item.raw.scope_type)"
                            :color="getScopeColor(item.raw.scope_type)"
                        />
                    </template>

                    <v-list-item-title>
                        {{ item.raw.name }}
                    </v-list-item-title>

                    <v-list-item-subtitle
                        v-if="item.raw.description"
                        class="text-wrap"
                    >
                        {{ item.raw.description }}
                    </v-list-item-subtitle>

                    <template #append>
                        <div class="d-flex flex-column align-end">
                            <v-chip
                                :color="
                                    getStatusBadge(item.raw.decision_status)
                                        .color
                                "
                                size="x-small"
                                variant="flat"
                                class="mb-1"
                            >
                                {{
                                    getStatusBadge(item.raw.decision_status)
                                        .text
                                }}
                            </v-chip>
                            <v-chip
                                v-if="
                                    item.raw.children_count &&
                                    item.raw.children_count > 0
                                "
                                size="x-small"
                                variant="outlined"
                                prepend-icon="mdi-file-tree"
                            >
                                {{ item.raw.children_count }} hijos
                            </v-chip>
                        </div>
                    </template>
                </v-list-item>
            </template>

            <template #selection="{ item }">
                <div class="d-flex align-center">
                    <v-icon
                        :icon="getScopeIcon(item.raw.scope_type)"
                        :color="getScopeColor(item.raw.scope_type)"
                        size="small"
                        class="mr-2"
                    />
                    <span>{{ item.raw.name }}</span>
                </div>
            </template>

            <template #no-data>
                <v-list-item>
                    <v-list-item-title class="text-medium-emphasis">
                        No hay escenarios aprobados disponibles como padre
                    </v-list-item-title>
                </v-list-item>
            </template>
        </v-autocomplete>

        <v-alert
            v-if="modelValue"
            type="info"
            variant="tonal"
            density="compact"
            class="mt-2"
            prepend-icon="mdi-information"
        >
            Las skills obligatorias del padre se sincronizarán automáticamente
        </v-alert>
    </div>
</template>

<style scoped>
.parent-scenario-selector {
    width: 100%;
}
</style>

<template>
    <div class="role-competency-matrix">
        <!-- Header -->
        <div class="matrix-header mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">
                        {{ store.scenarioName || 'Escenario' }}
                    </h2>
                    <p class="mt-1 text-gray-600">
                        Mapeo: Roles ↔ Competencias (Horizonte:
                        {{ store.horizonMonths }}
                        meses)
                    </p>
                </div>
                <div class="flex gap-2">
                    <v-btn
                        color="secondary"
                        variant="outlined"
                        prepend-icon="mdi-robot"
                        :loading="isDesigning"
                        @click="handleDesignTalent"
                    >
                        Consultar Agentes
                    </v-btn>
                    <v-btn
                        color="primary"
                        prepend-icon="mdi-plus"
                        @click="showAddRoleDialog = true"
                    >
                        + Nuevo Rol
                    </v-btn>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div v-if="store.error" class="mb-4">
            <v-alert type="error" closable @click:close="store.clearMessages()">
                {{ store.error }}
            </v-alert>
        </div>
        <v-snackbar v-model="showSuccess" color="success" timeout="2000">
            <div class="d-flex align-center">
                <v-icon class="mr-2">mdi-check-circle</v-icon>
                {{ store.success }}
            </div>
        </v-snackbar>

        <!-- Loading -->
        <div v-if="store.loading" class="flex justify-center py-8">
            <v-progress-circular
                indeterminate
                color="primary"
            ></v-progress-circular>
        </div>

        <div v-else>
            <!-- Instructions -->
            <div class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 p-4">
                <div class="flex items-start gap-3">
                    <v-icon icon="mdi-information" class="mt-1 text-blue-600" />
                    <div>
                        <h3 class="mb-1 font-semibold text-blue-800">
                            Cómo asignar competencias
                        </h3>
                        <p class="text-sm text-blue-700">
                            Haz click en cualquier celda para asignar una
                            competencia a un rol. Las celdas vacías muestran un
                            ícono + para indicar que se pueden asignar.
                        </p>
                        <p class="mt-2 text-sm text-blue-700">
                            Paso 2 consiste en conectar competencias con roles y
                            definir su transición (mantención, transformación,
                            enriquecimiento o extinción) de forma simple y
                            visual.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabs por categoría -->
            <v-card>
                <v-tabs
                    v-model="activeTab"
                    bg-color="gray-100"
                    class="border-b"
                >
                    <v-tab
                        v-for="(cat, idx) in categories"
                        :key="cat.name"
                        :value="idx"
                        :class="activeTab === idx ? 'bg-blue-50' : ''"
                    >
                        <v-tooltip :text="cat.name" location="top">
                            <template #activator="{ props: tooltipProps }">
                                <span
                                    v-bind="tooltipProps"
                                    class="tab-label text-truncate"
                                >
                                    {{ cat.name }}
                                </span>
                            </template>
                        </v-tooltip>
                        <v-badge
                            :content="countMappedInCategory(cat.name)"
                            color="primary"
                            location="top end"
                            offset-x="-8"
                            offset-y="-8"
                        />
                    </v-tab>
                </v-tabs>

                <!-- Tab Content -->
                <v-window v-model="activeTab" class="matrix-container">
                    <v-window-item
                        v-for="(cat, idx) in categories"
                        :key="cat.name"
                        :value="idx"
                    >
                        <table class="matrix-table">
                            <thead>
                                <tr>
                                    <th
                                        class="role-column-header sticky left-0 z-20 border-b border-gray-300 bg-gray-100"
                                    >
                                        <div
                                            class="group flex w-48 items-center justify-between p-3"
                                        >
                                            <strong>Rol</strong>
                                            <div
                                                class="cursor-col-resize opacity-0 transition-opacity group-hover:opacity-100"
                                            >
                                                ⋮⋮
                                            </div>
                                        </div>
                                    </th>
                                    <th
                                        v-for="comp in cat.comps"
                                        :key="comp.id"
                                        class="border-b border-gray-300 bg-gray-50"
                                    >
                                        <div class="w-32 p-3 text-center">
                                            <v-tooltip
                                                :text="comp.name"
                                                location="top"
                                            >
                                                <template
                                                    #activator="{
                                                        props: tooltipProps,
                                                    }"
                                                >
                                                    <div
                                                        v-bind="tooltipProps"
                                                        class="text-truncate comp-name text-sm font-semibold"
                                                    >
                                                        {{ comp.name }}
                                                    </div>
                                                </template>
                                            </v-tooltip>
                                            <v-tooltip
                                                :text="
                                                    comp.capability_name ||
                                                    comp.category
                                                "
                                                location="top"
                                            >
                                                <template
                                                    #activator="{
                                                        props: tooltipProps,
                                                    }"
                                                >
                                                    <div
                                                        v-bind="tooltipProps"
                                                        class="text-truncate comp-capability text-xs text-gray-500"
                                                    >
                                                        {{
                                                            comp.capability_name ||
                                                            comp.category
                                                        }}
                                                    </div>
                                                </template>
                                            </v-tooltip>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="row in store.matrixRows"
                                    :key="row.roleId"
                                >
                                    <td
                                        class="sticky left-0 z-10 border-b border-gray-200 bg-white"
                                    >
                                        <div
                                            class="w-48 border-r border-gray-200 p-3"
                                        >
                                            <div class="font-semibold">
                                                {{ row.roleName }}
                                            </div>
                                            <div
                                                class="mt-1 flex flex-wrap items-center gap-1.5"
                                            >
                                                <v-tooltip location="top">
                                                    <template
                                                        #activator="{
                                                            props: tooltipProps,
                                                        }"
                                                    >
                                                        <v-chip
                                                            v-if="row.archetype"
                                                            v-bind="
                                                                tooltipProps
                                                            "
                                                            size="small"
                                                            variant="flat"
                                                            :color="
                                                                row.archetype ===
                                                                'E'
                                                                    ? 'deep-purple'
                                                                    : row.archetype ===
                                                                        'T'
                                                                      ? 'blue'
                                                                      : 'teal'
                                                            "
                                                            :prepend-icon="
                                                                row.archetype ===
                                                                'E'
                                                                    ? 'mdi-chess-king'
                                                                    : row.archetype ===
                                                                        'T'
                                                                      ? 'mdi-account-tie'
                                                                      : 'mdi-wrench'
                                                            "
                                                            label
                                                            class="font-weight-bold"
                                                        >
                                                            {{
                                                                row.archetype ===
                                                                'E'
                                                                    ? 'Estratégico'
                                                                    : row.archetype ===
                                                                        'T'
                                                                      ? 'Táctico'
                                                                      : 'Operacional'
                                                            }}
                                                        </v-chip>
                                                    </template>
                                                    <div
                                                        style="max-width: 260px"
                                                    >
                                                        <div
                                                            class="font-weight-bold mb-1"
                                                        >
                                                            {{
                                                                row.archetype ===
                                                                'E'
                                                                    ? '♟ Rol Estratégico'
                                                                    : row.archetype ===
                                                                        'T'
                                                                      ? '🎯 Rol Táctico'
                                                                      : '⚙️ Rol Operacional'
                                                            }}
                                                        </div>
                                                        <div
                                                            class="text-caption"
                                                        >
                                                            {{
                                                                row.archetype ===
                                                                'E'
                                                                    ? 'Visión global, toma de decisiones de alto impacto. Niveles objetivo sugeridos: 4-5.'
                                                                    : row.archetype ===
                                                                        'T'
                                                                      ? 'Coordinación de equipos y gestión experta. Niveles objetivo sugeridos: 2-4.'
                                                                      : 'Ejecución técnica y operaciones. Niveles objetivo sugeridos: 1-3.'
                                                            }}
                                                        </div>
                                                        <div
                                                            class="text-caption font-italic mt-1"
                                                        >
                                                            Human Leverage:
                                                            {{
                                                                row.human_leverage ??
                                                                '—'
                                                            }}%
                                                        </div>
                                                    </div>
                                                </v-tooltip>
                                                <span
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{ row.fte }} FTE •
                                                    {{ row.status }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        v-for="comp in cat.comps"
                                        :key="`${row.roleId}-${comp.id}`"
                                        class="border-b border-gray-200 p-0"
                                    >
                                        <div
                                            class="flex h-24 w-32 cursor-pointer items-center justify-center border-r border-gray-200 transition hover:bg-gray-50"
                                            @click="
                                                openEditModal(
                                                    row.roleId,
                                                    comp.id,
                                                )
                                            "
                                        >
                                            <CellContent
                                                :mapping="
                                                    row.mappings.get(comp.id)
                                                "
                                                :role-id="row.roleId"
                                                :role-name="row.roleName"
                                                :competency-id="comp.id"
                                                :competency-name="comp.name"
                                                @edit="
                                                    openEditModal(
                                                        row.roleId,
                                                        comp.id,
                                                    )
                                                "
                                                @remove="
                                                    removeMapping(
                                                        row.roleId,
                                                        comp.id,
                                                    )
                                                "
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </v-window-item>
                </v-window>
            </v-card>
        </div>

        <!-- Edit Modal -->
        <RoleCompetencyStateModal
            v-if="selectedMapping"
            :visible="showEditModal"
            :role-id="selectedMapping.roleId"
            :role-name="selectedMapping.roleName"
            :archetype="selectedMapping.archetype"
            :competency-id="selectedMapping.competencyId"
            :competency-name="selectedMapping.competencyName"
            :mapping="selectedMapping.mapping"
            @save="saveMapping"
            @close="showEditModal = false"
        />

        <!-- Add Role Dialog -->
        <AddRoleDialog
            :visible="showAddRoleDialog"
            @save="handleAddRole"
            @close="showAddRoleDialog = false"
        />

        <!-- Agent Proposals Modal -->
        <AgentProposalsModal
            :visible="showAgentProposals"
            :loading="isDesigning"
            :proposals="agentProposals"
            @close="showAgentProposals = false"
        />
    </div>
</template>

<script setup lang="ts">
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import { computed, onMounted, ref, watch } from 'vue';
import AddRoleDialog from './AddRoleDialog.vue';
import AgentProposalsModal from './AgentProposalsModal.vue';
import CellContent from './CellContent.vue';
import RoleCompetencyStateModal from './RoleCompetencyStateModal.vue';

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();
const store = useRoleCompetencyStore();

const showEditModal = ref(false);
const showAddRoleDialog = ref(false);
const activeTab = ref(0);
const selectedMapping = ref<{
    roleId: number;
    roleName: string;
    archetype: string;
    competencyId: number;
    competencyName: string;
    mapping: any;
} | null>(null);
const showSuccess = ref(false);
const isDesigning = ref(false);
const showAgentProposals = ref(false);
const agentProposals = ref<any>(null);

const handleDesignTalent = async () => {
    isDesigning.value = true;
    showAgentProposals.value = true;
    try {
        const proposals = await store.designTalent();
        if (proposals) {
            agentProposals.value = proposals;
            console.log('Propuestas de Agentes:', proposals);
        }
    } finally {
        isDesigning.value = false;
    }
};

const categories = computed(() => {
    const map: Record<string, any[]> = {};
    store.competencyColumns.forEach((c: any) => {
        const cap = c.capability_name || c.category || 'General';
        if (!map[cap]) map[cap] = [];
        map[cap].push(c);
    });
    return Object.keys(map).map((k) => ({
        name: k,
        comps: map[k],
        count: map[k].length,
    }));
});

const countMappedInCategory = (categoryName: string) => {
    let total = 0;
    store.matrixRows.forEach((row: any) => {
        const comps =
            categories.value.find((c: any) => c.name === categoryName)?.comps ||
            [];
        comps.forEach((comp: any) => {
            if (row.mappings && row.mappings.get && row.mappings.get(comp.id)) {
                total += 1;
            }
        });
    });
    return total;
};

onMounted(async () => {
    await store.loadScenarioData(props.scenarioId);
});

watch(
    () => store.success,
    (val) => {
        if (val) showSuccess.value = true;
    },
);

const openEditModal = (roleId: number, competencyId: number) => {
    const role = store.roles.find((r) => r.id === roleId);
    const competency = store.competencies.find((c) => c.id === competencyId);
    const mapping = store.getMapping(roleId, competencyId);

    selectedMapping.value = {
        roleId,
        roleName: role?.role_name || '',
        archetype: role?.archetype || 'T',
        competencyId,
        competencyName: competency?.name || '',
        mapping,
    };

    showEditModal.value = true;
};

const saveMapping = async (mappingData: any) => {
    if (!selectedMapping.value) return;

    const newMapping = {
        id: mappingData.id,
        scenario_id: store.scenarioId!,
        role_id: selectedMapping.value.roleId,
        competency_id: selectedMapping.value.competencyId,
        competency_name: selectedMapping.value.competencyName,
        required_level: mappingData.required_level,
        is_core: mappingData.is_core,
        change_type: mappingData.change_type,
        rationale: mappingData.rationale,
    };

    await store.saveMapping(newMapping);
    showEditModal.value = false;
};

const removeMapping = async (roleId: number, competencyId: number) => {
    const mapping = store.getMapping(roleId, competencyId);
    if (mapping && confirm('¿Eliminar esta asociación?')) {
        await store.removeMapping(roleId, competencyId, mapping.id);
    }
};

const handleAddRole = async (roleData: any) => {
    await store.addNewRole(roleData);
    showAddRoleDialog.value = false;
};
</script>

<style scoped>
.matrix-container {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background: white;
    overflow-x: auto;
}

.matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.matrix-table th {
    padding: 0;
    text-align: center;
    vertical-align: middle;
}

.matrix-table td {
    padding: 0;
}

.matrix-header {
    padding: 1.5rem;
    background: white;
}

.matrix-table .w-32 {
    min-width: 160px;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.tab-label {
    max-width: 140px;
    display: inline-block;
}

.comp-name {
    max-width: 140px;
}

.comp-capability {
    max-width: 140px;
}

.role-column-header {
    resize: horizontal;
    overflow: hidden;
}

.cursor-col-resize {
    cursor: col-resize;
}

:deep(.v-tab.bg-blue-50) {
    background-color: rgba(59, 130, 246, 0.1) !important;
}
</style>

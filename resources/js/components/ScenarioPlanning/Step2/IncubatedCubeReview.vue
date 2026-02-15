<template>
    <div class="incubated-cube-review">
        <!-- Header Explicativo -->
        <div class="mb-6 border-l-4 border-indigo-500 bg-indigo-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <v-icon color="indigo">mdi-cube-outline</v-icon>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg leading-6 font-medium text-indigo-900">
                        Revisión de Estructura Propuesta (IA)
                    </h3>
                    <div class="mt-2 text-sm text-indigo-700">
                        <p>
                            El sistema ha generado una propuesta tridimensional
                            (Procesos x Roles x Competencias). Revisa la
                            coherencia de los arquetipos antes de aprobar la
                            incorporación al escenario.
                        </p>
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <v-btn
                        color="indigo darken-1"
                        :loading="approving"
                        @click="approveSelection"
                        :disabled="selectedIds.length === 0"
                    >
                        Aprobar Seleccionados ({{ selectedIds.length }})
                    </v-btn>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center p-12">
            <v-progress-circular
                indeterminate
                color="indigo"
                size="64"
            ></v-progress-circular>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="!hasData"
            class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center"
        >
            <v-icon size="64" color="gray lighten-1"
                >mdi-cube-off-outline</v-icon
            >
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                No hay items incubados
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Genera un escenario con IA para ver propuestas aquí.
            </p>
        </div>

        <!-- Cube Visualization (Grouped by Capability/Process) -->
        <div v-else class="space-y-8">
            <div
                v-for="cap in capabilities"
                :key="cap.id"
                class="capability-process-group overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
            >
                <!-- Process Header (Eje Z) -->
                <div
                    class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-4"
                >
                    <div class="flex items-center gap-3">
                        <v-checkbox
                            v-model="groupSelections[cap.id]"
                            @change="toggleGroup(cap.id)"
                            hide-details
                            density="compact"
                            color="indigo"
                        ></v-checkbox>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">
                                {{ cap.name }}
                            </h4>
                            <span
                                class="text-xs font-semibold tracking-wider text-gray-500 uppercase"
                                >Proceso / Dominio</span
                            >
                        </div>
                    </div>
                    <v-chip size="small" color="blue-grey" variant="outlined">{{
                        cap.category || 'General'
                    }}</v-chip>
                </div>

                <!-- Roles Grid (Eje X) -->
                <div
                    class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <!-- Associated Roles (Talent Blueprints mapped to this capability implicitly or explicitly) -->
                    <!-- Note: Since we don't have explicit DB link yet, we simulate filtering or show relevant roles. 
               For now, we list roles that match keywords or show all if generic. 
               Ideally, the backend would provide this grouping. -->

                    <div
                        v-for="role in getRolesForCapability(cap)"
                        :key="role.id"
                        class="role-card relative rounded-md border bg-white p-4 transition-shadow hover:shadow-md"
                    >
                        <!-- Role Selection -->
                        <div class="absolute top-3 right-3">
                            <v-checkbox
                                :model-value="isSelected(role.id, 'role')"
                                @update:model-value="
                                    toggleItem(role.id, 'role')
                                "
                                hide-details
                                density="compact"
                                color="indigo"
                            ></v-checkbox>
                        </div>

                        <!-- Role Archetype Badge (Inferred) -->
                        <div class="mb-2">
                            <v-chip
                                size="x-small"
                                :color="getArchetypeColor(role)"
                                class="font-weight-bold"
                            >
                                {{ getArchetypeLabel(role) }}
                            </v-chip>
                        </div>

                        <h5 class="mb-1 text-base font-bold text-gray-900">
                            {{ role.role_name }}
                        </h5>
                        <p class="mb-3 line-clamp-2 text-xs text-gray-500">
                            {{ role.role_description }}
                        </p>

                        <!-- Competencies (Eje Y) -->
                        <div class="mt-4 space-y-2">
                            <div
                                class="text-xs font-semibold text-gray-400 uppercase"
                            >
                                Competencias Clave (Dominio)
                            </div>
                            <div
                                v-for="comp in getCompetenciesForRole(role)"
                                :key="comp.name"
                                class="flex items-center justify-between text-sm"
                            >
                                <span
                                    class="mr-2 truncate text-gray-700"
                                    :title="comp.name"
                                    >{{ comp.name }}</span
                                >
                                <div class="flex items-center">
                                    <!-- Dots for Level 1-5 -->
                                    <div class="flex gap-0.5">
                                        <div
                                            v-for="n in 5"
                                            :key="n"
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="
                                                n <= (comp.level || 0)
                                                    ? 'bg-indigo-500'
                                                    : 'bg-gray-200'
                                            "
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unassigned Competencies Section (if any) -->
            <div
                v-if="orphanCompetencies.length > 0"
                class="mt-8 rounded-lg border border-gray-200 bg-white shadow-sm"
            >
                <div class="border-b border-yellow-200 bg-yellow-50 px-6 py-4">
                    <h4 class="text-lg font-bold text-yellow-900">
                        Competencias Incubadas (Sin Rol Asignado)
                    </h4>
                </div>
                <div class="grid grid-cols-2 gap-4 p-6 md:grid-cols-4">
                    <div
                        v-for="comp in orphanCompetencies"
                        :key="comp.id"
                        class="flex items-center gap-2 rounded border bg-gray-50 p-2"
                    >
                        <v-checkbox
                            :model-value="isSelected(comp.id, 'competency')"
                            @update:model-value="
                                toggleItem(comp.id, 'competency')
                            "
                            hide-details
                            density="compact"
                            color="indigo"
                        ></v-checkbox>
                        <span class="text-sm font-medium">{{ comp.name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    scenarioId: number;
}>();

const { showSuccess, showError } = useNotification();

// Data Types
interface Capability {
    id: number;
    name: string;
    category: string;
}

interface Role {
    id: number;
    role_name: string;
    role_description: string;
    human_leverage: number;
    key_competencies: any; // JSON string or object
}

interface Competency {
    id: number;
    name: string;
    capability_id: number;
}

// State
const loading = ref(false);
const approving = ref(false);
const capabilities = ref<Capability[]>([]);
const roles = ref<Role[]>([]);
const competencies = ref<Competency[]>([]);
const selectedIds = ref<string[]>([]); // Format: "type:id" e.g., "role:12"
const groupSelections = ref<Record<number, boolean>>({});

// Computed
const hasData = computed(
    () => capabilities.value.length > 0 || roles.value.length > 0,
);

const orphanCompetencies = computed(() => {
    // Competencies that match capabilities displayed are technically 'assigned' to that process block visually.
    // We can treat this simply as 'all competencies' for now if we want to allow granular approval.
    return []; // Simplified for now
});

// Methods
const fetchData = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/scenarios/${props.scenarioId}/incubated-items`,
        );
        capabilities.value = res.data.capabilities || [];
        roles.value = res.data.roles || []; // Assuming backend returns TalentBlueprints as roles
        competencies.value = res.data.competencies || [];
    } catch (e) {
        console.error(e);
        showError('Error al cargar items incubados.');
    } finally {
        loading.value = false;
    }
};

// Helper: infer archetype from role description or leverage
const getArchetypeLabel = (role: Role) => {
    if (role.human_leverage > 70) return 'Estratégico (E)';
    if (role.human_leverage > 40) return 'Táctico (T)';
    return 'Operacional (O)';
};

const getArchetypeColor = (role: Role) => {
    if (role.human_leverage > 70) return 'purple';
    if (role.human_leverage > 40) return 'blue';
    return 'teal';
};

// Helper: associate roles to capability (Simplified matching logic since DB link is missing)
const getRolesForCapability = (cap: Capability) => {
    // If we had a direct link, use it. For now, since TalentBlueprints are scenario-wide,
    // we might show ALL roles under a "General" block or try to match by name.
    // IMPROVEMENT: For the Cube Demo, we'll return all roles in the first capability block
    // and hide them in others to avoid duplication, OR distribute them if we can guess.

    // Quick Hack for Demo: Assign roles to capabilities cyclicly or by simple keyword match?
    // Let's just return all roles in a "General Strategy" fake capability if needed,
    // or for this UI, let's assume the backend 'roles' array belongs to the scenario context.

    // Better approach: If capabilities exist, maybe the roles are linked via the LLM prompt structure logic?
    // Since we lack the link, let's display roles under the first capability only, or create a 'Desconocido' block.

    // Return all roles for the first capability in the list, empty for others (to prevent duplication)
    if (capabilities.value.length > 0 && cap.id === capabilities.value[0].id) {
        return roles.value;
    }
    return [];
};

const getCompetenciesForRole = (role: Role) => {
    // Parse JSON key_competencies
    let comps = [];
    try {
        comps =
            typeof role.key_competencies === 'string'
                ? JSON.parse(role.key_competencies)
                : role.key_competencies || [];
    } catch (e) {
        comps = [];
    }

    // Format: "Name: Level" or just strings
    return Array.isArray(comps)
        ? comps.map((c: any) => {
              // Handle different formats from LLM (string or object)
              if (typeof c === 'string') return { name: c, level: 3 }; // Default level
              return {
                  name: c.name || c.key || 'Skill',
                  level: c.level || c.score || 3,
              };
          })
        : [];
};

// Selection Logic
const isSelected = (id: number, type: 'role' | 'capability' | 'competency') => {
    return selectedIds.value.includes(`${type}:${id}`);
};

const toggleItem = (id: number, type: 'role' | 'capability' | 'competency') => {
    const key = `${type}:${id}`;
    if (selectedIds.value.includes(key)) {
        selectedIds.value = selectedIds.value.filter((k) => k !== key);
    } else {
        selectedIds.value.push(key);
    }
};

const toggleGroup = (capId: number) => {
    const isChecked = !!groupSelections.value[capId];
    // Select capability itself
    if (isChecked) {
        if (!selectedIds.value.includes(`capability:${capId}`))
            selectedIds.value.push(`capability:${capId}`);
        // Select all child roles (visual grouping logic)
        roles.value.forEach((r) => {
            if (!selectedIds.value.includes(`role:${r.id}`))
                selectedIds.value.push(`role:${r.id}`);
        });
    } else {
        selectedIds.value = selectedIds.value.filter(
            (k) => k !== `capability:${capId}`,
        );
        // Deselect roles (simplification: deselects all roles for now)
        roles.value.forEach((r) => {
            selectedIds.value = selectedIds.value.filter(
                (k) => k !== `role:${r.id}`,
            );
        });
    }
};

const approveSelection = async () => {
    approving.value = true;
    try {
        // Separate IDs
        const payload = {
            capability_ids: selectedIds.value
                .filter((s) => s.startsWith('capability:'))
                .map((s) => Number(s.split(':')[1])),
            role_ids: selectedIds.value
                .filter((s) => s.startsWith('role:'))
                .map((s) => Number(s.split(':')[1])),
            competency_ids: selectedIds.value
                .filter((s) => s.startsWith('competency:'))
                .map((s) => Number(s.split(':')[1])),
        };

        await axios.post(
            `/api/scenarios/${props.scenarioId}/incubated-items/approve`,
            payload,
        );
        showSuccess('Elementos aprobados y activados exitosamente.');

        // Refresh to update list
        await fetchData();
        selectedIds.value = [];
        groupSelections.value = {};
    } catch (e) {
        showError('Error al aprobar elementos.');
    } finally {
        approving.value = false;
    }
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped>
.incubated-cube-review {
    /* Minimal styles */
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

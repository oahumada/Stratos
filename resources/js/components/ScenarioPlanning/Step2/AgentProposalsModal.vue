<template>
    <v-dialog
        :model-value="visible"
        fullscreen
        transition="dialog-bottom-transition"
        persistent
    >
        <v-card>
            <!-- Header -->
            <v-toolbar color="secondary" dark>
                <v-icon start class="ml-4">mdi-robot</v-icon>
                <v-toolbar-title>
                    Propuestas del Agente Diseñador de Roles
                </v-toolbar-title>
                <v-spacer />

                <!-- Alignment score badge -->
                <v-chip
                    v-if="proposals?.alignment_score"
                    color="white"
                    text-color="secondary"
                    class="font-weight-bold mr-4"
                    prepend-icon="mdi-chart-line"
                >
                    Alineación:
                    {{ Math.round((proposals.alignment_score ?? 0) * 100) }}%
                </v-chip>

                <v-btn icon @click="$emit('close')">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>

            <!-- Loading state -->
            <div
                v-if="loading"
                class="d-flex flex-column align-center justify-center"
                style="min-height: 400px"
            >
                <v-progress-circular
                    indeterminate
                    color="secondary"
                    size="64"
                />
                <p class="text-h6 text-medium-emphasis mt-6">
                    Los agentes están colaborando...
                </p>
                <p class="text-body-2 text-medium-emphasis mt-2">
                    Analizando blueprint estratégico y catálogo organizacional
                </p>
            </div>

            <!-- Empty state -->
            <div
                v-else-if="!proposals"
                class="d-flex flex-column align-center justify-center"
                style="min-height: 400px"
            >
                <v-icon size="80" color="grey-lighten-1">mdi-robot-off</v-icon>
                <p class="text-h6 text-medium-emphasis mt-4">
                    No hay propuestas disponibles.
                </p>
                <p class="text-body-2 text-medium-emphasis">
                    Intenta consultar de nuevo a los agentes.
                </p>
            </div>

            <!-- Main content -->
            <v-card-text v-else class="pa-6">
                <!-- Info banner -->
                <v-alert
                    type="info"
                    variant="tonal"
                    class="mb-6"
                    icon="mdi-information-outline"
                >
                    El <strong>Diseñador de Roles</strong> propone los
                    siguientes cambios basado en el blueprint estratégico y el
                    catálogo actual. Revisa cada propuesta, ajusta el arquetipo
                    o el nivel de competencias si lo necesitas, y aprueba las
                    que quieras aplicar a la matriz.
                </v-alert>

                <!-- ═══════════════════════════════════════ -->
                <!-- SECCIÓN: ROLES -->
                <!-- ═══════════════════════════════════════ -->
                <div class="d-flex align-center justify-space-between mb-4">
                    <div class="d-flex align-center gap-2">
                        <v-icon color="primary" size="20"
                            >mdi-account-tie</v-icon
                        >
                        <h3 class="text-h6 font-weight-bold">
                            Roles Propuestos
                            <v-chip
                                size="small"
                                class="ml-2"
                                :color="
                                    approvedRoleCount > 0 ? 'primary' : 'grey'
                                "
                            >
                                {{ approvedRoleCount }} /
                                {{ localRoleProposals.length }}
                            </v-chip>
                        </h3>
                    </div>
                    <div class="d-flex gap-2">
                        <v-btn
                            size="small"
                            variant="tonal"
                            color="success"
                            @click="approveAllRoles"
                        >
                            <v-icon start>mdi-check-all</v-icon>Aprobar todos
                        </v-btn>
                        <v-btn
                            size="small"
                            variant="tonal"
                            color="error"
                            @click="rejectAllRoles"
                        >
                            <v-icon start>mdi-close-circle</v-icon>Rechazar
                            todos
                        </v-btn>
                    </div>
                </div>

                <div
                    v-for="(role, idx) in localRoleProposals"
                    :key="`role-${idx}`"
                    class="mb-4"
                >
                    <v-card
                        :variant="
                            role._status === 'rejected' ? 'text' : 'outlined'
                        "
                        :class="{
                            'border-success': role._status === 'approved',
                            'border-error opacity-50':
                                role._status === 'rejected',
                            'proposal-card': true,
                        }"
                    >
                        <!-- Card header -->
                        <v-card-item>
                            <template #prepend>
                                <v-chip
                                    :color="getTypeColor(role.type)"
                                    size="small"
                                    label
                                    class="font-weight-bold mr-2"
                                >
                                    <v-icon start size="14">{{
                                        getTypeIcon(role.type)
                                    }}</v-icon>
                                    {{ role.type?.toUpperCase() }}
                                </v-chip>
                            </template>

                            <v-card-title class="text-body-1 font-weight-bold">
                                {{ role.proposed_name }}
                            </v-card-title>

                            <v-card-subtitle
                                v-if="role.proposed_description"
                                class="mt-1"
                            >
                                {{ role.proposed_description }}
                            </v-card-subtitle>

                            <template #append>
                                <!-- Status chip -->
                                <v-chip
                                    v-if="role._status === 'approved'"
                                    color="success"
                                    size="small"
                                    variant="flat"
                                    prepend-icon="mdi-check-circle"
                                    >Aprobado</v-chip
                                >
                                <v-chip
                                    v-else-if="role._status === 'rejected'"
                                    color="error"
                                    size="small"
                                    variant="flat"
                                    prepend-icon="mdi-close-circle"
                                    >Rechazado</v-chip
                                >
                                <v-chip
                                    v-else
                                    color="grey"
                                    size="small"
                                    variant="tonal"
                                    >Pendiente</v-chip
                                >
                            </template>
                        </v-card-item>

                        <v-divider v-if="role._status !== 'rejected'" />

                        <v-card-text v-if="role._status !== 'rejected'">
                            <v-row>
                                <!-- Archetype selector -->
                                <v-col cols="12" md="3">
                                    <v-label
                                        class="text-caption font-weight-medium d-block mb-1"
                                    >
                                        Arquetipo (Cubo)
                                    </v-label>
                                    <v-btn-toggle
                                        v-model="role.archetype"
                                        density="compact"
                                        rounded="lg"
                                        mandatory
                                        color="primary"
                                        border
                                    >
                                        <v-btn value="E" size="small">E</v-btn>
                                        <v-btn value="T" size="small">T</v-btn>
                                        <v-btn value="O" size="small">O</v-btn>
                                    </v-btn-toggle>
                                    <div
                                        class="text-caption text-medium-emphasis mt-1"
                                    >
                                        {{ archetypeLabel(role.archetype) }}
                                    </div>
                                </v-col>

                                <!-- FTE -->
                                <v-col cols="12" md="2">
                                    <v-label
                                        class="text-caption font-weight-medium d-block mb-1"
                                        >FTE Sugerido</v-label
                                    >
                                    <v-text-field
                                        v-model.number="role.fte_suggested"
                                        type="number"
                                        min="0.1"
                                        step="0.5"
                                        density="compact"
                                        variant="outlined"
                                        hide-details
                                    />
                                </v-col>

                                <!-- Human/IA mix -->
                                <v-col
                                    v-if="role.talent_composition"
                                    cols="12"
                                    md="3"
                                >
                                    <v-label
                                        class="text-caption font-weight-medium d-block mb-1"
                                    >
                                        Mix Humano / IA
                                    </v-label>
                                    <div class="d-flex align-center mt-1 gap-2">
                                        <v-icon size="16" color="primary"
                                            >mdi-account</v-icon
                                        >
                                        <span
                                            class="text-body-2 font-weight-medium"
                                            >{{
                                                role.talent_composition
                                                    .human_percentage
                                            }}%</span
                                        >
                                        <span
                                            class="text-caption text-medium-emphasis"
                                            >/</span
                                        >
                                        <v-icon size="16" color="secondary"
                                            >mdi-robot</v-icon
                                        >
                                        <span
                                            class="text-body-2 font-weight-medium"
                                            >{{
                                                role.talent_composition
                                                    .synthetic_percentage
                                            }}%</span
                                        >
                                    </div>
                                    <div
                                        class="text-caption text-medium-emphasis mt-1"
                                    >
                                        {{
                                            role.talent_composition
                                                .logic_justification
                                        }}
                                    </div>
                                </v-col>
                            </v-row>

                            <!-- Competency mappings table -->
                            <div
                                v-if="role.competency_mappings?.length"
                                class="mt-4"
                            >
                                <v-label
                                    class="text-caption font-weight-medium d-block mb-2"
                                >
                                    Competencias propuestas ({{
                                        role.competency_mappings.length
                                    }})
                                </v-label>
                                <v-table
                                    density="compact"
                                    class="rounded-lg border"
                                >
                                    <thead>
                                        <tr>
                                            <th>Competencia</th>
                                            <th>Tipo cambio</th>
                                            <th class="text-center">
                                                Nivel req.
                                            </th>
                                            <th class="text-center">Core</th>
                                            <th>Semáforo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                mapping, mIdx
                                            ) in role.competency_mappings"
                                            :key="mIdx"
                                        >
                                            <td class="text-body-2">
                                                {{ mapping.competency_name }}
                                            </td>
                                            <td>
                                                <v-chip
                                                    :color="
                                                        getChangeTypeColor(
                                                            mapping.change_type,
                                                        )
                                                    "
                                                    size="x-small"
                                                    label
                                                >
                                                    {{
                                                        changeTypeLabel(
                                                            mapping.change_type,
                                                        )
                                                    }}
                                                </v-chip>
                                            </td>
                                            <td class="text-center">
                                                <v-btn-toggle
                                                    v-model="
                                                        mapping.required_level
                                                    "
                                                    density="compact"
                                                    border
                                                    mandatory
                                                    color="primary"
                                                >
                                                    <v-btn
                                                        v-for="n in 5"
                                                        :key="n"
                                                        :value="n"
                                                        size="x-small"
                                                        >{{ n }}</v-btn
                                                    >
                                                </v-btn-toggle>
                                            </td>
                                            <td class="text-center">
                                                <v-checkbox
                                                    v-model="mapping.is_core"
                                                    density="compact"
                                                    hide-details
                                                    color="primary"
                                                />
                                            </td>
                                            <td>
                                                <v-icon
                                                    :color="
                                                        cubeSignalColor(
                                                            role.archetype,
                                                            mapping.required_level,
                                                            mapping.is_core,
                                                        )
                                                    "
                                                    size="18"
                                                    :title="
                                                        cubeSignalLabel(
                                                            role.archetype,
                                                            mapping.required_level,
                                                            mapping.is_core,
                                                        )
                                                    "
                                                >
                                                    mdi-circle
                                                </v-icon>
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </div>
                        </v-card-text>

                        <!-- Actions -->
                        <v-card-actions v-if="role._status !== 'rejected'">
                            <v-spacer />
                            <v-btn
                                size="small"
                                color="error"
                                variant="text"
                                prepend-icon="mdi-close"
                                @click="role._status = 'rejected'"
                                >Rechazar</v-btn
                            >
                            <v-btn
                                size="small"
                                :color="
                                    role._status === 'approved'
                                        ? 'success'
                                        : 'primary'
                                "
                                :variant="
                                    role._status === 'approved'
                                        ? 'flat'
                                        : 'tonal'
                                "
                                :prepend-icon="
                                    role._status === 'approved'
                                        ? 'mdi-check-circle'
                                        : 'mdi-check'
                                "
                                @click="
                                    role._status =
                                        role._status === 'approved'
                                            ? 'pending'
                                            : 'approved'
                                "
                            >
                                {{
                                    role._status === 'approved'
                                        ? 'Aprobado ✓'
                                        : 'Aprobar'
                                }}
                            </v-btn>
                        </v-card-actions>
                        <v-card-actions v-else>
                            <v-btn
                                size="small"
                                variant="text"
                                @click="role._status = 'pending'"
                            >
                                <v-icon start>mdi-undo</v-icon>Restaurar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </div>

                <!-- ═══════════════════════════════════════ -->
                <!-- SECCIÓN: COMPETENCIAS -->
                <!-- ═══════════════════════════════════════ -->
                <v-divider class="my-6" />

                <div class="d-flex align-center justify-space-between mb-4">
                    <div class="d-flex align-center gap-2">
                        <v-icon color="teal" size="20">mdi-certificate</v-icon>
                        <h3 class="text-h6 font-weight-bold">
                            Propuestas de Catálogo
                            <v-chip
                                size="small"
                                class="ml-2"
                                :color="
                                    approvedCatalogCount > 0 ? 'teal' : 'grey'
                                "
                            >
                                {{ approvedCatalogCount }} /
                                {{ localCatalogProposals.length }}
                            </v-chip>
                        </h3>
                    </div>
                    <div class="d-flex gap-2">
                        <v-btn
                            size="small"
                            variant="tonal"
                            color="teal"
                            @click="approveAllCatalog"
                        >
                            <v-icon start>mdi-check-all</v-icon>Aprobar todos
                        </v-btn>
                        <v-btn
                            size="small"
                            variant="tonal"
                            color="error"
                            @click="rejectAllCatalog"
                        >
                            <v-icon start>mdi-close-circle</v-icon>Rechazar
                            todos
                        </v-btn>
                    </div>
                </div>

                <v-card
                    v-for="(comp, idx) in localCatalogProposals"
                    :key="`comp-${idx}`"
                    :variant="comp._status === 'rejected' ? 'text' : 'outlined'"
                    :class="{
                        'border-teal': comp._status === 'approved',
                        'opacity-50': comp._status === 'rejected',
                        'mb-3': true,
                    }"
                >
                    <v-card-item>
                        <template #prepend>
                            <v-chip
                                :color="getCatalogTypeColor(comp.type)"
                                size="small"
                                label
                                class="mr-2"
                            >
                                {{ comp.type?.toUpperCase() }}
                            </v-chip>
                        </template>

                        <v-card-title class="text-body-1">{{
                            comp.proposed_name
                        }}</v-card-title>
                        <v-card-subtitle>{{
                            comp.action_rationale
                        }}</v-card-subtitle>

                        <template #append>
                            <div class="d-flex align-center gap-2">
                                <v-chip
                                    v-if="comp._status === 'approved'"
                                    color="teal"
                                    size="small"
                                    variant="flat"
                                    prepend-icon="mdi-check-circle"
                                    >Aprobado</v-chip
                                >
                                <v-chip
                                    v-else-if="comp._status === 'rejected'"
                                    color="error"
                                    size="small"
                                    variant="flat"
                                    prepend-icon="mdi-close-circle"
                                    >Rechazado</v-chip
                                >

                                <v-btn
                                    v-if="comp._status !== 'rejected'"
                                    icon="mdi-close"
                                    density="compact"
                                    size="small"
                                    color="error"
                                    variant="text"
                                    @click="comp._status = 'rejected'"
                                />
                                <v-btn
                                    v-if="comp._status === 'rejected'"
                                    icon="mdi-undo"
                                    density="compact"
                                    size="small"
                                    variant="text"
                                    @click="comp._status = 'pending'"
                                />
                                <v-btn
                                    v-if="comp._status !== 'rejected'"
                                    :icon="
                                        comp._status === 'approved'
                                            ? 'mdi-check-circle'
                                            : 'mdi-check'
                                    "
                                    density="compact"
                                    size="small"
                                    :color="
                                        comp._status === 'approved'
                                            ? 'teal'
                                            : 'grey'
                                    "
                                    :variant="
                                        comp._status === 'approved'
                                            ? 'flat'
                                            : 'tonal'
                                    "
                                    @click="
                                        comp._status =
                                            comp._status === 'approved'
                                                ? 'pending'
                                                : 'approved'
                                    "
                                />
                            </div>
                        </template>
                    </v-card-item>
                </v-card>
            </v-card-text>

            <!-- Footer sticky con resumen y botón de confirmación -->
            <v-divider />
            <v-card-actions class="pa-4 bg-surface-variant">
                <span class="text-body-2 text-medium-emphasis">
                    <strong>{{ approvedRoleCount }}</strong> roles y
                    <strong>{{ approvedCatalogCount }}</strong> competencias
                    seleccionadas
                    <span
                        v-if="
                            approvedRoleCount === 0 &&
                            approvedCatalogCount === 0
                        "
                        class="text-error"
                    >
                        — selecciona al menos uno para confirmar
                    </span>
                </span>
                <v-spacer />
                <v-btn variant="text" @click="$emit('close')">Cancelar</v-btn>
                <v-btn
                    color="secondary"
                    variant="flat"
                    prepend-icon="mdi-check-all"
                    :loading="applying"
                    :disabled="
                        approvedRoleCount === 0 && approvedCatalogCount === 0
                    "
                    @click="confirmApply"
                >
                    Confirmar y aplicar ({{
                        approvedRoleCount + approvedCatalogCount
                    }})
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

// ─── Types ────────────────────────────────────────────────────────────────────

type ProposalStatus = 'pending' | 'approved' | 'rejected';

interface CompetencyMapping {
    competency_name: string;
    competency_id?: number | null;
    change_type: string;
    required_level: number;
    is_core: boolean;
    rationale?: string;
}

interface RoleProposal {
    type: string;
    proposed_name: string;
    proposed_description?: string;
    archetype?: string;
    fte_suggested?: number;
    target_role_id?: number | null;
    competency_mappings?: CompetencyMapping[];
    talent_composition?: {
        human_percentage: number;
        synthetic_percentage: number;
        logic_justification?: string;
    };
    _status: ProposalStatus;
}

interface CatalogProposal {
    type: string;
    proposed_name: string;
    competency_id?: number | null;
    action_rationale?: string;
    _status: ProposalStatus;
}

interface Proposals {
    role_proposals?: Omit<RoleProposal, '_status'>[];
    catalog_proposals?: Omit<CatalogProposal, '_status'>[];
    alignment_score?: number;
}

// ─── Props & Emits ────────────────────────────────────────────────────────────

interface Props {
    visible: boolean;
    loading?: boolean;
    proposals?: Proposals | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    close: [];
    applied: [];
}>();

// ─── Local reactive state ─────────────────────────────────────────────────────

const applying = ref(false);
const localRoleProposals = ref<RoleProposal[]>([]);
const localCatalogProposals = ref<CatalogProposal[]>([]);

// Reconstruir estado local cuando llegan nuevas propuestas
watch(
    () => props.proposals,
    (proposals) => {
        if (!proposals) return;
        localRoleProposals.value = (proposals.role_proposals ?? []).map(
            (r) => ({
                ...r,
                archetype: r.archetype ?? 'T',
                fte_suggested: r.fte_suggested ?? 1.0,
                competency_mappings: (r.competency_mappings ?? []).map((m) => ({
                    ...m,
                })),
                _status: 'pending' as ProposalStatus,
            }),
        );
        localCatalogProposals.value = (proposals.catalog_proposals ?? []).map(
            (c) => ({
                ...c,
                _status: 'pending' as ProposalStatus,
            }),
        );
    },
    { immediate: true },
);

// ─── Computed ─────────────────────────────────────────────────────────────────

const approvedRoleCount = computed(
    () =>
        localRoleProposals.value.filter((r) => r._status === 'approved').length,
);

const approvedCatalogCount = computed(
    () =>
        localCatalogProposals.value.filter((c) => c._status === 'approved')
            .length,
);

// ─── Bulk actions ─────────────────────────────────────────────────────────────

const approveAllRoles = () =>
    localRoleProposals.value.forEach((r) => (r._status = 'approved'));
const rejectAllRoles = () =>
    localRoleProposals.value.forEach((r) => (r._status = 'rejected'));
const approveAllCatalog = () =>
    localCatalogProposals.value.forEach((c) => (c._status = 'approved'));
const rejectAllCatalog = () =>
    localCatalogProposals.value.forEach((c) => (c._status = 'rejected'));

// ─── Apply confirmed proposals ────────────────────────────────────────────────

const confirmApply = async () => {
    const approvedRoles = localRoleProposals.value.filter(
        (r) => r._status === 'approved',
    );
    const approvedCatalog = localCatalogProposals.value.filter(
        (c) => c._status === 'approved',
    );

    // Obtener scenarioId desde la URL
    const pathParts = window.location.pathname.split('/');
    const scenarioId = pathParts[pathParts.indexOf('scenarios') + 1] ?? null;

    if (!scenarioId) {
        alert('No se pudo determinar el ID del escenario.');
        return;
    }

    applying.value = true;
    try {
        const response = await fetch(
            `/api/scenarios/${scenarioId}/step2/agent-proposals/apply`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '',
                    ),
                },
                body: JSON.stringify({
                    approved_role_proposals: approvedRoles.map(
                        ({ _status, ...r }) => r,
                    ),
                    approved_catalog_proposals: approvedCatalog.map(
                        ({ _status, ...c }) => c,
                    ),
                }),
            },
        );

        if (!response.ok) {
            const err = await response.json();
            throw new Error(err.message ?? 'Error al aplicar propuestas');
        }

        emit('applied');
        emit('close');
    } catch (err: unknown) {
        const message =
            err instanceof Error ? err.message : 'Error desconocido';
        alert(`Error: ${message}`);
    } finally {
        applying.value = false;
    }
};

// ─── Display helpers ──────────────────────────────────────────────────────────

const getTypeColor = (type: string): string => {
    switch (type?.toLowerCase()) {
        case 'new':
            return 'success';
        case 'evolve':
            return 'info';
        case 'replace':
            return 'warning';
        default:
            return 'grey';
    }
};

const getTypeIcon = (type: string): string => {
    switch (type?.toLowerCase()) {
        case 'new':
            return 'mdi-plus-circle';
        case 'evolve':
            return 'mdi-arrow-up-circle';
        case 'replace':
            return 'mdi-swap-horizontal';
        default:
            return 'mdi-circle';
    }
};

const getCatalogTypeColor = (type: string): string => {
    switch (type?.toLowerCase()) {
        case 'add':
            return 'teal';
        case 'modify':
            return 'blue';
        case 'replace':
            return 'orange';
        default:
            return 'grey';
    }
};

const getChangeTypeColor = (ct: string): string => {
    switch (ct?.toLowerCase()) {
        case 'maintenance':
            return 'success';
        case 'transformation':
            return 'blue';
        case 'enrichment':
            return 'purple';
        case 'extinction':
            return 'error';
        default:
            return 'grey';
    }
};

const changeTypeLabel = (ct: string): string => {
    switch (ct?.toLowerCase()) {
        case 'maintenance':
            return '✅ Mantención';
        case 'transformation':
            return '🔄 Transform.';
        case 'enrichment':
            return '📈 Enriq.';
        case 'extinction':
            return '📉 Extinción';
        default:
            return ct ?? '—';
    }
};

const archetypeLabel = (arch?: string): string => {
    switch (arch) {
        case 'E':
            return 'Estratégico';
        case 'T':
            return 'Táctico';
        case 'O':
            return 'Operacional';
        default:
            return 'Sin definir';
    }
};

// ─── Cube semaphore logic ─────────────────────────────────────────────────────

const cubeSignalColor = (
    archetype: string | undefined,
    level: number,
    isCore: boolean,
): string => {
    if (!archetype) return 'grey';
    const l = level ?? 0;
    if (archetype === 'O' && l > 3 && !isCore) return 'orange';
    if (archetype === 'T' && l > 4 && !isCore) return 'orange';
    if (archetype === 'E' && l < 3 && !isCore) return 'blue';
    return 'success';
};

const cubeSignalLabel = (
    archetype: string | undefined,
    level: number,
    isCore: boolean,
): string => {
    const color = cubeSignalColor(archetype, level, isCore);
    if (color === 'orange') return '⚠️ Posible sobrecarga para este arquetipo';
    if (color === 'blue')
        return 'ℹ️ Competencia de apoyo — nivel bajo para rol Estratégico';
    return '✅ Coherente con el arquetipo';
};
</script>

<style scoped>
.proposal-card {
    transition: border-color 0.2s ease;
}
.border-success {
    border-color: rgb(var(--v-theme-success)) !important;
    border-width: 2px !important;
}
.border-teal {
    border-color: rgb(var(--v-theme-teal)) !important;
    border-width: 2px !important;
}
.border-error {
    border-color: rgb(var(--v-theme-error)) !important;
}
</style>

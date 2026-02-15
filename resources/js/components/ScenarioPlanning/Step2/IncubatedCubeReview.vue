<template>
    <div class="incubated-cube-review">
        <!-- Dashboard Header: El Puente de Transición -->
        <div class="cube-review-info mb-8">
            <div
                class="flex items-start gap-4 rounded-xl border border-indigo-100 bg-indigo-50/50 p-4"
            >
                <div
                    class="flex-shrink-0 rounded-lg bg-indigo-600 p-3 shadow-lg shadow-indigo-200"
                >
                    <v-icon color="white" size="28">mdi-cube-scan</v-icon>
                </div>
                <div class="flex-grow">
                    <h4 class="text-lg leading-tight font-bold text-indigo-900">
                        Cubo de Roles e Ingeniería Organizacional
                    </h4>
                    <p class="mt-1 max-w-2xl text-sm text-indigo-700/80">
                        Esta fase permite validar la coherencia tridimensional
                        (Procesos x Arquetipos x Maestría). Los elementos en
                        esta sección están en modo
                        <span class="font-bold underline decoration-indigo-300"
                            >laboratorio</span
                        >
                        y no afectarán el catálogo hasta ser conciliados.
                    </p>
                </div>
                <div class="flex flex-shrink-0 gap-2 self-center">
                    <v-btn
                        variant="tonal"
                        color="indigo"
                        icon="mdi-help-circle-outline"
                        @click="showMatchHelp = !showMatchHelp"
                        class="rounded-lg"
                    ></v-btn>
                    <v-btn
                        variant="elevated"
                        color="indigo"
                        :loading="approving"
                        @click="approveSelection"
                        :disabled="selectedIds.length === 0"
                        class="text-none font-bold"
                        rounded="lg"
                    >
                        Aprobar para Ingeniería ({{ selectedIds.length }})
                    </v-btn>
                </div>
            </div>

            <!-- Help Guide: Tabla de Compatibilidad -->
            <v-expand-transition>
                <div
                    v-if="showMatchHelp"
                    class="mt-4 rounded-xl border-2 border-indigo-100 bg-white p-6 shadow-sm"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <v-icon color="indigo">mdi-book-open-variant</v-icon>
                        <h5
                            class="text-sm font-black tracking-widest text-indigo-900 uppercase"
                        >
                            Guía de Conciliación Organizacional
                        </h5>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div
                            class="rounded-lg border border-emerald-100 bg-emerald-50 p-3 text-center"
                        >
                            <div
                                class="mb-1 text-[10px] font-bold text-emerald-700 uppercase"
                            >
                                Nuevo (0%)
                            </div>
                            <div class="text-xs font-bold text-slate-800">
                                📈 Enriquecimiento
                            </div>
                            <div class="mt-1 text-[9px] text-slate-500">
                                <strong>Job Enlargement</strong>: Aumento
                                horizontal. Creación de capacidad/rol
                                inexistente.
                            </div>
                        </div>
                        <div
                            class="rounded-lg border border-amber-100 bg-amber-50 p-3 text-center"
                        >
                            <div
                                class="mb-1 text-[10px] font-bold text-amber-700 uppercase"
                            >
                                Parcial (40-85%)
                            </div>
                            <div class="text-xs font-bold text-slate-800">
                                🔄 Transformación
                            </div>
                            <div class="mt-1 text-[9px] text-slate-500">
                                <strong>Job Enrichment</strong>: Aumento
                                vertical. El rol evoluciona en profundidad
                                (Upskilling).
                            </div>
                        </div>
                        <div
                            class="rounded-lg border border-slate-100 bg-slate-50 p-3 text-center"
                        >
                            <div
                                class="mb-1 text-[10px] font-bold text-slate-500 uppercase"
                            >
                                Existente (>85%)
                            </div>
                            <div class="text-xs font-bold text-slate-800">
                                ✅ Mantención
                            </div>
                            <div class="mt-1 text-[9px] text-slate-500">
                                <strong>Job Stabilization</strong>: El rol
                                actual es maduro y suficiente para el diseño.
                            </div>
                        </div>
                        <div
                            class="rounded-lg border border-red-100 bg-red-50 p-3 text-center"
                        >
                            <div
                                class="mb-1 text-[10px] font-bold text-red-700 uppercase"
                            >
                                No Propuesto
                            </div>
                            <div class="text-xs font-bold text-slate-800">
                                📉 Extinción
                            </div>
                            <div class="mt-1 text-[9px] text-slate-500">
                                <strong>Job Substitution</strong>: Potencial
                                obsolescencia por cambio de modelo estratégico.
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-[11px] text-slate-400 italic">
                        Esta lógica asegura que las propuestas de la IA se
                        traduzcan en estados técnicos coherentes en la Matriz de
                        Ingeniería.
                    </p>
                </div>
            </v-expand-transition>
        </div>

        <!-- Diálogo de Confirmación Crítica -->
        <v-dialog v-model="confirmApproval" max-width="500" persistent>
            <v-card
                class="overflow-hidden rounded-2xl border-t-4 border-indigo-600"
            >
                <v-card-title class="bg-indigo-50/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <v-icon color="indigo-darken-2"
                            >mdi-shield-alert</v-icon
                        >
                        <span class="font-black text-indigo-900"
                            >Confirmar Conciliación</span
                        >
                    </div>
                </v-card-title>

                <v-card-text class="pa-6">
                    <p class="leading-relaxed text-slate-700">
                        Estás a punto de
                        <span class="font-bold text-indigo-700"
                            >promover {{ selectedIds.length }} elementos</span
                        >
                        del laboratorio a la fase de ingeniería.
                    </p>

                    <v-alert
                        type="warning"
                        variant="tonal"
                        icon="mdi-alert-octagon"
                        class="mt-4 border-l-4 border-amber-500"
                        density="comfortable"
                    >
                        <div class="text-sm font-bold">
                            Advertencia de Ingeniería
                        </div>
                        <div class="mt-1 text-xs opacity-90">
                            Este paso crea registros maestros en la base de
                            datos de competencias y roles. Una vez promovidos,
                            no podrán revertirse a "incubación" desde esta
                            vista.
                        </div>
                    </v-alert>

                    <div
                        class="max-height-[150px] mt-6 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50 p-3"
                    >
                        <div
                            class="mb-2 text-[10px] font-bold text-slate-400 uppercase"
                        >
                            Entidades a promover:
                        </div>
                        <div
                            v-for="s in selectedIds"
                            :key="s"
                            class="mb-1 flex items-center gap-2 text-xs text-slate-600"
                        >
                            <v-icon size="10" color="indigo">mdi-check</v-icon>
                            {{ getItemLabel(s) }}
                        </div>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="pa-4 bg-slate-50/80">
                    <v-spacer />
                    <v-btn
                        variant="text"
                        color="slate-darken-1"
                        @click="confirmApproval = false"
                        class="text-none"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        variant="elevated"
                        color="indigo-darken-1"
                        @click="executeApproval"
                        :loading="approving"
                        class="text-none font-bold"
                    >
                        Sí, Proceder a Ingeniería
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Loading / Empty States con mejor diseño -->
        <transition name="fade" mode="out-in">
            <div
                v-if="loading"
                key="loading"
                class="flex flex-col items-center justify-center p-20"
            >
                <v-progress-circular
                    indeterminate
                    color="indigo"
                    size="64"
                    width="6"
                ></v-progress-circular>
                <span class="mt-4 animate-pulse font-medium text-indigo-400"
                    >Analizando geometría del cubo...</span
                >
            </div>

            <div
                v-else-if="!hasData"
                key="empty"
                class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-16 text-center"
            >
                <div
                    class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100"
                >
                    <v-icon size="40" color="slate-lighten-1"
                        >mdi-cube-off-outline</v-icon
                    >
                </div>
                <h3 class="text-xl font-bold text-slate-800">
                    No hay propuestas incubadas
                </h3>
                <p class="mx-auto mt-2 max-w-sm text-slate-500">
                    Utiliza el motor de IA para generar una propuesta de roles y
                    competencias para este escenario.
                </p>
            </div>

            <div v-else key="content" class="space-y-10">
                <!-- Iteración por Capacidad (Eje Z) -->
                <div
                    v-for="cap in capabilities"
                    :key="cap.id"
                    class="process-block group rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:border-indigo-200 hover:shadow-md"
                >
                    <!-- Capacidad Header -->
                    <div
                        class="flex items-center justify-between rounded-t-2xl border-b border-slate-100 bg-slate-50/30 px-8 py-5"
                    >
                        <div class="flex items-center gap-4">
                            <v-checkbox
                                v-model="groupSelections[cap.id]"
                                @change="toggleGroup(cap.id)"
                                hide-details
                                density="compact"
                                color="indigo"
                            ></v-checkbox>
                            <div>
                                <span
                                    class="text-[10px] font-bold tracking-[0.2em] text-indigo-500 uppercase"
                                    >Capacidad / Dominio</span
                                >
                                <h4 class="text-xl font-black text-slate-800">
                                    {{ cap.name }}
                                </h4>
                            </div>
                        </div>
                        <v-chip
                            size="small"
                            variant="flat"
                            color="slate-100"
                            class="border border-slate-200 font-bold text-slate-600"
                        >
                            {{ cap.category || 'Core Business' }}
                        </v-chip>
                    </div>

                    <!-- Grid de Roles (Eje X) -->
                    <div
                        class="grid grid-cols-1 gap-8 p-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="role in getRolesForCapability(cap)"
                            :key="role.id"
                            class="role-card-premium group/card relative rounded-2xl border border-slate-100 bg-white p-6 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl"
                        >
                            <div class="absolute top-4 right-4 z-10">
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

                            <!-- Arquetipo Badge + Matching Pill -->
                            <div class="mb-4 flex flex-wrap gap-2">
                                <span
                                    class="rounded-md px-2.5 py-1 text-[10px] font-black tracking-wider uppercase shadow-sm"
                                    :class="getArchetypeClasses(role)"
                                >
                                    {{ getArchetypeLabel(role) }}
                                </span>

                                <!-- Similarity Pill Logic: Color coded by severity of change -->
                                <template
                                    v-if="
                                        role.similarity_warnings &&
                                        role.similarity_warnings.length > 0
                                    "
                                >
                                    <!-- High Match (>85%) -> Existente (Mantención) -->
                                    <template
                                        v-if="
                                            role.similarity_warnings[0].score >
                                            0.85
                                        "
                                    >
                                        <v-tooltip location="top">
                                            <template
                                                v-slot:activator="{ props }"
                                            >
                                                <span
                                                    v-bind="props"
                                                    class="cursor-help rounded-md border border-slate-200 bg-slate-100 px-2.5 py-1 text-[10px] font-black tracking-wider text-slate-700 uppercase shadow-sm"
                                                >
                                                    En Catálogo ({{
                                                        Math.round(
                                                            role
                                                                .similarity_warnings[0]
                                                                .score * 100,
                                                        )
                                                    }}%)
                                                </span>
                                            </template>
                                            <div class="pa-2 max-w-[250px]">
                                                <div
                                                    class="mb-1 border-b border-white/20 pb-1 font-bold text-slate-200"
                                                >
                                                    Match Alto Detectado
                                                </div>
                                                <div
                                                    class="space-y-1 text-[10px]"
                                                >
                                                    <div
                                                        v-for="w in role.similarity_warnings"
                                                        :key="w.id"
                                                        class="flex justify-between gap-4"
                                                    >
                                                        <span
                                                            >•
                                                            {{ w.name }}</span
                                                        >
                                                        <span class="font-black"
                                                            >{{
                                                                Math.round(
                                                                    w.score *
                                                                        100,
                                                                )
                                                            }}%</span
                                                        >
                                                    </div>
                                                </div>
                                                <div
                                                    class="mt-2 text-[9px] italic opacity-80"
                                                >
                                                    Sugerencia: Mantención. Este
                                                    rol ya está cubierto por el
                                                    catálogo actual.
                                                </div>
                                            </div>
                                        </v-tooltip>
                                    </template>
                                    <!-- Partial Match (40-85%) -> Parcial (Transformación) -->
                                    <template v-else>
                                        <v-tooltip location="top">
                                            <template
                                                v-slot:activator="{ props }"
                                            >
                                                <span
                                                    v-bind="props"
                                                    class="cursor-help rounded-md border border-amber-200 bg-amber-100 px-2.5 py-1 text-[10px] font-black tracking-wider text-amber-700 uppercase shadow-sm"
                                                >
                                                    Parcial ({{
                                                        Math.round(
                                                            role
                                                                .similarity_warnings[0]
                                                                .score * 100,
                                                        )
                                                    }}%)
                                                </span>
                                            </template>
                                            <div class="pa-2 max-w-[250px]">
                                                <div
                                                    class="mb-1 border-b border-white/20 pb-1 font-bold text-amber-200"
                                                >
                                                    Similitud Parcial
                                                </div>
                                                <div
                                                    class="space-y-1 text-[10px]"
                                                >
                                                    <div
                                                        v-for="w in role.similarity_warnings"
                                                        :key="w.id"
                                                        class="flex justify-between gap-4"
                                                    >
                                                        <span
                                                            >•
                                                            {{ w.name }}</span
                                                        >
                                                        <span class="font-black"
                                                            >{{
                                                                Math.round(
                                                                    w.score *
                                                                        100,
                                                                )
                                                            }}%</span
                                                        >
                                                    </div>
                                                </div>
                                                <div
                                                    class="mt-2 text-[9px] italic opacity-80"
                                                >
                                                    Sugerencia: Transformación /
                                                    Upskilling. El rol
                                                    evoluciona respecto al
                                                    catálogo.
                                                </div>
                                            </div>
                                        </v-tooltip>
                                    </template>
                                </template>
                                <!-- No Match -> Nuevo (Enriquecimiento) -->
                                <template v-else>
                                    <span
                                        class="rounded-md border border-emerald-200 bg-emerald-100 px-2.5 py-1 text-[10px] font-black tracking-wider text-emerald-700 uppercase shadow-sm"
                                    >
                                        Nuevo Rol
                                    </span>
                                </template>
                            </div>

                            <h5
                                class="text-lg font-bold text-slate-900 transition-colors group-hover/card:text-indigo-700"
                            >
                                {{ role.role_name }}
                            </h5>
                            <p
                                class="mt-2 line-clamp-2 h-10 text-sm leading-relaxed text-slate-500"
                            >
                                {{ role.role_description }}
                            </p>

                            <!-- Competencias (Eje Y) -->
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                        >Aptitud Requerida</span
                                    >
                                    <v-icon size="14" color="slate-300"
                                        >mdi-chevron-right</v-icon
                                    >
                                </div>

                                <div
                                    v-for="(
                                        comp, cIdx
                                    ) in getCompetenciesForRole(role)"
                                    :key="cIdx"
                                    class="comp-item flex items-center justify-between rounded-lg border border-transparent bg-slate-50/50 p-2.5 transition-all hover:border-slate-200 hover:bg-white"
                                >
                                    <span
                                        class="truncate pr-2 text-sm font-medium text-slate-700"
                                        >{{ comp.name }}</span
                                    >
                                    <div class="flex gap-1">
                                        <div
                                            v-for="n in 5"
                                            :key="n"
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="
                                                n <= comp.level
                                                    ? 'bg-indigo-500 shadow-sm shadow-indigo-200'
                                                    : 'bg-slate-200'
                                            "
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orphan Section -->
                <div v-if="orphanCompetencies.length > 0" class="mt-12">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="h-px flex-grow bg-slate-200"></div>
                        <span
                            class="text-xs font-black tracking-[0.3em] text-slate-400 uppercase"
                            >Competencias sin anclaje</span
                        >
                        <div class="h-px flex-grow bg-slate-200"></div>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6"
                    >
                        <div
                            v-for="comp in orphanCompetencies"
                            :key="comp.id"
                            class="flex items-center gap-2 rounded-xl border border-slate-100 bg-white p-3 transition-all hover:border-indigo-100"
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
                            <span
                                class="truncate text-xs font-bold text-slate-700"
                                >{{ comp.name }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </transition>
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

// Tipos base
interface Capability {
    id: number;
    name: string;
    category: string;
    llm_id?: string;
}

interface Role {
    id: number;
    role_name: string;
    role_description: string;
    human_leverage: number;
    key_competencies: any;
    similarity_warnings?: Array<{ id: number; name: string; score: number }>;
}

interface Competency {
    id: number;
    name: string;
    capability_id?: number;
}

// Estado
const loading = ref(false);
const approving = ref(false);
const confirmApproval = ref(false);
const showMatchHelp = ref(false);
const capabilities = ref<Capability[]>([]);
const roles = ref<Role[]>([]);
const competencies = ref<Competency[]>([]);
const selectedIds = ref<string[]>([]);
const groupSelections = ref<Record<number, boolean>>({});

const hasData = computed(
    () => capabilities.value.length > 0 || roles.value.length > 0,
);

const getItemLabel = (key: string) => {
    const [type, id] = key.split(':');
    const numericId = Number(id);
    if (type === 'role')
        return roles.value.find((r) => r.id === numericId)?.role_name || 'Rol';
    if (type === 'capability')
        return (
            capabilities.value.find((c) => c.id === numericId)?.name ||
            'Capacidad'
        );
    return `ID: ${id}`;
};

const orphanCompetencies = computed(() => {
    // Por ahora omitimos lógica compleja de huérfanos para simplificar la vista
    return [];
});

const fetchData = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/incubated-items`,
        );
        const remoteData = res.data.data || res.data;
        capabilities.value = remoteData.capabilities || [];
        roles.value = remoteData.roles || [];
        competencies.value = remoteData.competencies || [];
    } catch (e: any) {
        console.error('Fetch error:', e);
        showError('No se pudieron cargar los datos del laboratorio.');
    } finally {
        loading.value = false;
    }
};

const getArchetypeLabel = (role: Role) => {
    if (role.human_leverage > 70) return 'Estratégico (E)';
    if (role.human_leverage > 40) return 'Táctico (T)';
    return 'Operacional (O)';
};

const getArchetypeClasses = (role: Role) => {
    if (role.human_leverage > 70)
        return 'bg-purple-50 text-purple-700 border border-purple-100';
    if (role.human_leverage > 40)
        return 'bg-blue-50 text-blue-700 border border-blue-100';
    return 'bg-emerald-50 text-emerald-700 border border-emerald-100';
};

const getRolesForCapability = (cap: Capability) => {
    if (!roles.value.length) return [];

    // Si la capacidad tiene llm_id, buscamos roles cuyas competencias empiecen con ese ID
    if (cap.llm_id) {
        return roles.value.filter((role) => {
            const comps = role.key_competencies;
            if (Array.isArray(comps)) {
                return comps.some((c) => String(c).startsWith(cap.llm_id!));
            }
            return false;
        });
    }

    // Fallback: si es la primera capacidad, mostramos todo
    if (capabilities.value.length > 0 && cap.id === capabilities.value[0].id) {
        return roles.value;
    }
    return [];
};

const getCompetenciesForRole = (role: Role) => {
    let raw = role.key_competencies;
    if (typeof raw === 'string') {
        try {
            raw = JSON.parse(raw);
        } catch {
            raw = [];
        }
    }

    if (!Array.isArray(raw)) return [];

    return raw.map((c) => {
        if (typeof c === 'string') return { name: c, level: 3 };
        return {
            name: c.name || c.key || 'Competencia',
            level: c.level || c.score || 3,
        };
    });
};

const isSelected = (id: number, type: string) =>
    selectedIds.value.includes(`${type}:${id}`);

const toggleItem = (id: number, type: string) => {
    const key = `${type}:${id}`;
    const idx = selectedIds.value.indexOf(key);
    if (idx > -1) selectedIds.value.splice(idx, 1);
    else selectedIds.value.push(key);
};

const toggleGroup = (capId: number) => {
    const isChecked = groupSelections.value[capId];
    const rolesInGroup = getRolesForCapability(
        capabilities.value.find((c) => c.id === capId)!,
    );

    rolesInGroup.forEach((r) => {
        const key = `role:${r.id}`;
        const hasKey = selectedIds.value.includes(key);
        if (isChecked && !hasKey) selectedIds.value.push(key);
        if (!isChecked && hasKey)
            selectedIds.value.splice(selectedIds.value.indexOf(key), 1);
    });

    // También incluir la capacidad per se si queremos aprobarla
    const capKey = `capability:${capId}`;
    if (isChecked && !selectedIds.value.includes(capKey))
        selectedIds.value.push(capKey);
    else if (!isChecked && selectedIds.value.includes(capKey))
        selectedIds.value.splice(selectedIds.value.indexOf(capKey), 1);
};

const approveSelection = () => {
    confirmApproval.value = true;
};

const executeApproval = async () => {
    approving.value = true;
    confirmApproval.value = false;
    try {
        const payload = {
            items: selectedIds.value.map((s) => {
                const [type, id] = s.split(':');
                return { type, id: Number(id) };
            }),
        };
        await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/incubated-items/approve`,
            payload,
        );
        showSuccess('Estructura organizacional conciliada exitosamente.');
        await fetchData();
        selectedIds.value = [];
        groupSelections.value = {};
    } catch {
        showError('Fallo en la conciliación de elementos.');
    } finally {
        approving.value = false;
    }
};

onMounted(fetchData);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

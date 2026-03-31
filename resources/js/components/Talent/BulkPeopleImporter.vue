<script setup lang="ts">
import { post } from '@/apiHelper';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowLeft,
    PhArrowRight,
    PhArrowsClockwise,
    // PhUser,
    PhBuildings,
    PhCheckCircle,
    PhSignature,
    PhSuitcase,
    PhUploadSimple,
    PhWarningCircle,
    PhX,
} from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits(['close', 'completed', 'update:isOpen']);

const dialogModel = computed({
    get: () => props.isOpen,
    set: (val) => emit('update:isOpen', val),
});

const step = ref(1);
const file = ref<File | null>(null);
const rawData = ref<any[]>([]);
const analysis = ref<any>(null);
const loading = ref(false);
const changeSetId = ref<number | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const steps = [
    { title: 'bulk_import.steps.upload', icon: PhUploadSimple },
    { title: 'bulk_import.steps.alignment', icon: PhBuildings },
    { title: 'bulk_import.steps.resolution', icon: PhWarningCircle },
    { title: 'bulk_import.steps.approval', icon: PhSignature },
];

const handleFileUpload = (e: any) => {
    const f = e.target.files[0];
    if (f) {
        file.value = f;
        parseCSV(f);
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
};

const parseCSV = async (f: File) => {
    try {
        const text = await f.text();
        // Dividir por líneas manejando diferentes tipos de saltos de línea
        const lines = text.split(/\r?\n/).filter((r) => r.trim());

        if (lines.length === 0) return;

        // Función robusta para parsear una línea de CSV (maneja campos entre comillas)
        const parseCSVLine = (line: string) => {
            const result = [];
            let current = '';
            let inQuotes = false;
            for (let i = 0; i < line.length; i++) {
                const char = line[i];
                if (char === '"') {
                    inQuotes = !inQuotes;
                } else if (char === ',' && !inQuotes) {
                    result.push(current.trim());
                    current = '';
                } else {
                    current += char;
                }
            }
            result.push(current.trim());
            // Limpia comillas residuales en los extremos de cada campo
            return result.map((v) => v.replace(/^"|"$/g, '').trim());
        };

        const headers = parseCSVLine(lines[0]).map((h) =>
            h.toLowerCase().replaceAll(' ', '_'),
        );

        rawData.value = lines.slice(1).map((line) => {
            const values = parseCSVLine(line);
            const obj: any = {};
            headers.forEach((h, i) => {
                obj[h] = values[i] || '';
            });
            return obj;
        });

        step.value = 2;
    } catch (error) {
        console.error('Error parsing CSV:', error);
    }
};

const runAnalysis = async () => {
    loading.value = true;
    try {
        const res = await post('/api/talent/bulk-import/analyze', {
            rows: rawData.value,
        });
        analysis.value = res.analysis;
        step.value = 3;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const stageImport = async () => {
    loading.value = true;
    try {
        const res = await post('/api/talent/bulk-import/stage', {
            rows: rawData.value,
            mapping: {
                departments: analysis.value.detected_departments,
                roles: analysis.value.detected_roles,
                movements: analysis.value.movements,
            },
        });
        changeSetId.value = res.change_set_id;
        step.value = 4;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const commitImport = async () => {
    loading.value = true;
    try {
        await post(`/api/talent/bulk-import/${changeSetId.value}/approve`, {
            signature: 'digital_sign_hash_' + Date.now(),
        });
        emit('completed');
        emit('close');
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const close = () => {
    step.value = 1;
    file.value = null;
    rawData.value = [];
    analysis.value = null;
    dialogModel.value = false;
    emit('close');
};
</script>

<template>
    <v-dialog v-model="dialogModel" persistent max-width="1000px" scrollable>
        <StCardGlass class="st-modal-glass flex flex-col overflow-hidden!">
            <!-- Header -->
            <div
                class="st-modal-header-gradient flex items-center justify-between px-10 py-8"
            >
                <div>
                    <h2
                        class="flex items-center gap-3 text-2xl font-black text-white"
                    >
                        <PhUploadSimple :size="28" />
                        Stratos Node Aligner
                    </h2>
                    <p class="mt-1 text-sm text-white/60">
                        Sincronización masiva de talento con Nodos
                        Gravitacionales
                    </p>
                </div>
                <StButtonGlass
                    variant="ghost"
                    :icon="PhX"
                    circle
                    @click="close"
                />
            </div>

            <!-- Wizard Stepper -->
            <div
                class="no-scrollbar flex items-center justify-center gap-2 overflow-x-auto border-b border-white/5 bg-white/5 px-4 py-6 sm:gap-6"
            >
                <div
                    v-for="(s, i) in steps"
                    :key="i"
                    class="flex shrink-0 items-center"
                >
                    <div class="flex items-center gap-2">
                        <div
                            :class="[
                                'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-all',
                                step > i + 1
                                    ? 'bg-emerald-500 text-white'
                                    : step === i + 1
                                      ? 'bg-indigo-500 text-white shadow-[0_0_15px_rgba(99,102,241,0.5)]'
                                      : 'bg-white/10 text-white/30',
                            ]"
                        >
                            <component
                                v-if="step > i + 1"
                                :is="PhCheckCircle"
                                :size="14"
                            />
                            <span v-else>{{ i + 1 }}</span>
                        </div>
                        <span
                            :class="[
                                'text-[10px] font-black tracking-wider whitespace-nowrap uppercase sm:text-xs',
                                step === i + 1 ? 'text-white' : 'text-white/20',
                            ]"
                        >
                            {{ t(s.title) }}
                        </span>
                    </div>
                    <div
                        v-if="i < steps.length - 1"
                        class="mx-2 h-px w-4 bg-white/10 sm:mx-4 sm:w-10"
                    />
                </div>
            </div>

            <!-- Body -->
            <div class="st-modal-body min-h-[450px] p-12">
                <!-- STEP 1: UPLOAD -->
                <div v-if="step === 1" class="px-10 py-16">
                    <div class="flex flex-col items-center justify-center">
                        <div
                            class="group flex w-full max-w-lg cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-white/10 bg-white/5 p-12 transition-all hover:border-indigo-500/30 hover:bg-white/10"
                            @click="triggerFileInput"
                        >
                            <div
                                class="mb-6 flex h-24 w-24 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-400 transition-transform group-hover:scale-110"
                            >
                                <PhUploadSimple :size="48" />
                            </div>
                            <h3 class="mb-2 text-xl font-bold text-white">
                                Sube tu nómina de personal
                            </h3>
                            <p class="mb-8 text-center text-white/40">
                                Formatos soportados: CSV, XLS, XLSX. El sistema
                                intentará alinear los cargos y departamentos
                                automáticamente.
                            </p>

                            <input
                                ref="fileInput"
                                type="file"
                                id="fileImport"
                                class="hidden"
                                accept=".csv"
                                @change="handleFileUpload"
                            />
                            <StButtonGlass
                                variant="primary"
                                size="lg"
                                :icon="PhUploadSimple"
                                @click.stop="triggerFileInput"
                            >
                                Seleccionar Archivo
                            </StButtonGlass>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: ALIGNMENT PREVIEW -->
                <div v-if="step === 2" class="space-y-6 px-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            Vista Previa y Mapeo
                        </h3>
                        <StBadgeGlass variant="primary"
                            >{{ rawData.length }} registros
                            detectados</StBadgeGlass
                        >
                    </div>
                    <div
                        class="overflow-x-auto rounded-2xl border border-white/5 bg-black/20"
                    >
                        <table class="w-full border-collapse text-left text-sm">
                            <thead>
                                <tr class="bg-white/5">
                                    <th
                                        v-for="h in rawData.length > 0
                                            ? Object.keys(rawData[0])
                                            : []"
                                        :key="h"
                                        class="p-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        {{ h }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, idx) in rawData.slice(0, 5)"
                                    :key="idx"
                                    class="border-t border-white/5"
                                >
                                    <td
                                        v-for="(val, vIdx) in Object.values(
                                            row,
                                        )"
                                        :key="vIdx"
                                        class="p-4 text-white/70"
                                    >
                                        {{ val }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-if="rawData.length > 5"
                        class="text-center text-xs text-white/30"
                    >
                        ... y {{ rawData.length - 5 }} filas más ...
                    </div>

                    <div class="flex justify-center pt-8">
                        <StButtonGlass
                            variant="primary"
                            size="lg"
                            :icon="PhArrowRight"
                            @click="runAnalysis"
                            :loading="loading"
                        >
                            Analizar Estructura Organizacional
                        </StButtonGlass>
                    </div>
                </div>

                <!-- STEP 3: RESOLUTION -->
                <div v-if="step === 3 && analysis" class="space-y-8 px-10">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- DEPARTMENTS -->
                        <div class="space-y-4">
                            <div
                                class="mb-2 flex items-center gap-2 text-white/50"
                            >
                                <PhBuildings :size="18" />
                                <span
                                    class="text-xs font-black tracking-widest uppercase"
                                    >Alineación de Departamentos</span
                                >
                            </div>
                            <div
                                v-for="dept in analysis.detected_departments"
                                :key="dept.raw_name"
                                class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 p-4"
                            >
                                <div class="flex flex-col">
                                    <span class="font-bold text-white">{{
                                        dept.raw_name
                                    }}</span>
                                    <span
                                        class="text-[10px] tracking-tighter text-white/40 uppercase"
                                        v-if="dept.status === 'existing'"
                                        >Match con Nodo Existente</span
                                    >
                                    <span
                                        class="text-[10px] tracking-tighter text-indigo-400 uppercase"
                                        v-else
                                        >Creará Nuevo Nodo</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <PhArrowRight
                                        :size="14"
                                        class="text-white/20"
                                    />
                                    <StBadgeGlass
                                        :variant="
                                            dept.status === 'existing'
                                                ? 'success'
                                                : 'warning'
                                        "
                                    >
                                        {{ dept.suggested_name }}
                                    </StBadgeGlass>
                                </div>
                            </div>
                        </div>

                        <!-- ROLES -->
                        <div class="space-y-4">
                            <div
                                class="mb-2 flex items-center gap-2 text-white/50"
                            >
                                <PhSuitcase :size="18" />
                                <span
                                    class="text-xs font-black tracking-widest uppercase"
                                    >Alineación de Cargos / Roles</span
                                >
                            </div>
                            <div
                                v-for="role in analysis.detected_roles"
                                :key="role.raw_name"
                                class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 p-4"
                            >
                                <div class="flex flex-col">
                                    <span class="font-bold text-white">{{
                                        role.raw_name
                                    }}</span>
                                    <span
                                        class="text-[10px] tracking-tighter text-white/40 uppercase"
                                        v-if="role.status === 'existing'"
                                        >Match Encontrado</span
                                    >
                                    <span
                                        class="text-[10px] tracking-tighter text-amber-400 uppercase"
                                        v-else
                                        >Sugerencia Estratégica</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <PhArrowRight
                                        :size="14"
                                        class="text-white/20"
                                    />
                                    <StBadgeGlass
                                        :variant="
                                            role.status === 'existing'
                                                ? 'success'
                                                : 'primary'
                                        "
                                    >
                                        {{ role.suggested_name }}
                                    </StBadgeGlass>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TALENT DYNAMICS (Hires, Transfers, Exits) -->
                    <div class="border-t border-white/5 pt-8">
                        <div class="mb-6 flex items-center gap-2 text-white/50">
                            <PhArrowsClockwise :size="18" />
                            <span
                                class="text-xs font-black tracking-widest uppercase"
                                >Dinámica de Talento (Lifecycle)</span
                            >
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <!-- NEW HIRES -->
                            <div
                                class="rounded-2xl border border-emerald-500/10 bg-emerald-500/5 p-4"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black text-emerald-400 uppercase"
                                        >Nuevos Ingresos</span
                                    >
                                    <StBadgeGlass variant="success">{{
                                        analysis.movements.hires.length
                                    }}</StBadgeGlass>
                                </div>
                                <div
                                    class="max-h-40 space-y-2 overflow-y-auto pr-2"
                                >
                                    <div
                                        v-for="h in analysis.movements.hires"
                                        :key="h.email"
                                        class="flex items-center gap-2 text-xs text-white/60"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        />
                                        {{ h.name }}
                                    </div>
                                    <div
                                        v-if="
                                            analysis.movements.hires.length ===
                                            0
                                        "
                                        class="text-xs text-white/20 italic"
                                    >
                                        Sin nuevos ingresos
                                    </div>
                                </div>
                            </div>

                            <!-- TRANSFERS -->
                            <div
                                class="rounded-2xl border border-indigo-500/10 bg-indigo-500/5 p-4"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black text-indigo-400 uppercase"
                                        >Traslados / Ascensos</span
                                    >
                                    <StBadgeGlass variant="primary">{{
                                        analysis.movements.transfers.length
                                    }}</StBadgeGlass>
                                </div>
                                <div
                                    class="max-h-40 space-y-2 overflow-y-auto pr-2"
                                >
                                    <div
                                        v-for="t in analysis.movements
                                            .transfers"
                                        :key="t.email"
                                        class="flex flex-col gap-0.5 text-xs text-white/60"
                                    >
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-1.5 w-1.5 rounded-full bg-indigo-500"
                                            />
                                            {{ t.name }}
                                        </div>
                                        <div
                                            class="ml-3 text-[9px] text-white/30"
                                        >
                                            {{ t.changes.join(' | ') }}
                                        </div>
                                    </div>
                                    <div
                                        v-if="
                                            analysis.movements.transfers
                                                .length === 0
                                        "
                                        class="text-xs text-white/20 italic"
                                    >
                                        Sin movimientos internos
                                    </div>
                                </div>
                            </div>

                            <!-- EXITS -->
                            <div
                                class="rounded-2xl border border-rose-500/10 bg-rose-500/5 p-4"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black text-rose-400 uppercase"
                                        >Egresos Detectados</span
                                    >
                                    <StBadgeGlass variant="error">{{
                                        analysis.movements.exits.length
                                    }}</StBadgeGlass>
                                </div>
                                <div
                                    class="max-h-40 space-y-2 overflow-y-auto pr-2"
                                >
                                    <div
                                        v-for="e in analysis.movements.exits"
                                        :key="e.email"
                                        class="flex items-center gap-2 text-xs text-white/60"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                        />
                                        {{ e.name }}
                                    </div>
                                    <div
                                        v-if="
                                            analysis.movements.exits.length ===
                                            0
                                        "
                                        class="text-xs text-white/20 italic"
                                    >
                                        Sin egresos detectados
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center gap-4 pt-8">
                        <StButtonGlass variant="secondary" @click="step = 2"
                            >Revisar Mapeo</StButtonGlass
                        >
                        <StButtonGlass
                            variant="primary"
                            size="lg"
                            :icon="PhCheckCircle"
                            @click="stageImport"
                            :loading="loading"
                        >
                            Confirmar y Preparar Firma
                        </StButtonGlass>
                    </div>
                </div>

                <!-- STEP 4: SIGNATURE -->
                <div
                    v-if="step === 4"
                    class="flex flex-col items-center justify-center px-10 py-10 text-center"
                >
                    <div
                        class="relative w-full max-w-2xl overflow-hidden rounded-3xl border border-indigo-500/20 bg-indigo-500/5 p-12"
                    >
                        <!-- BG Decoration -->
                        <div
                            class="absolute -top-12 -right-12 h-48 w-48 rounded-full bg-indigo-500/10 blur-3xl"
                        />

                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400"
                        >
                            <PhSignature :size="32" />
                        </div>

                        <h3 class="mb-2 text-2xl font-black text-white">
                            Aprobación de Versión 0
                        </h3>
                        <p class="mb-6 text-sm text-white/60">
                            Esta acción establecerá el **Baseline
                            Organizacional**. Todos los cambios futuros serán
                            trackeados desde este punto.
                        </p>

                        <div
                            class="mb-6 space-y-4 rounded-2xl border border-white/5 bg-black/40 p-6 text-left"
                        >
                            <div
                                class="mb-1 flex items-center justify-between border-b border-white/5 pb-3"
                            >
                                <span
                                    class="text-[10px] font-black text-white/30 uppercase"
                                    >Documento de Carga</span
                                >
                                <span class="text-xs font-bold text-indigo-300"
                                    >#CHANGE_{{ changeSetId }}</span
                                >
                            </div>
                            <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                                <div class="flex justify-between text-xs">
                                    <span class="text-white/40"
                                        >Colaboradores:</span
                                    >
                                    <span class="font-bold text-white">{{
                                        rawData.length
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-white/40"
                                        >Responsable:</span
                                    >
                                    <span class="font-bold text-white">{{
                                        $page.props.auth.user.name
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-white/40"
                                        >Nuevos Deptos:</span
                                    >
                                    <span class="font-bold text-emerald-400">{{
                                        analysis?.detected_departments.filter(
                                            (d: any) => d.status === 'new',
                                        ).length
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-white/40"
                                        >Nuevos Roles:</span
                                    >
                                    <span class="font-bold text-amber-400">{{
                                        analysis?.detected_roles.filter(
                                            (r: any) => r.status === 'new',
                                        ).length
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <StButtonGlass
                            variant="primary"
                            size="lg"
                            block
                            :icon="PhSignature"
                            @click="commitImport"
                            :loading="loading"
                        >
                            Firmar y Sincronizar Stratos
                        </StButtonGlass>

                        <p
                            class="mt-4 text-[9px] font-bold tracking-[0.2em] text-white/20 uppercase"
                        >
                            Registro de Auditoría Activado
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer for generic navigation -->
            <div
                class="st-modal-footer flex items-center justify-between border-t border-white/5 bg-black/20 p-8"
                v-if="step > 1 && step < 4"
            >
                <StButtonGlass
                    variant="ghost"
                    :icon="PhArrowLeft"
                    @click="step--"
                    >Anterior</StButtonGlass
                >
                <div
                    class="text-[10px] font-black tracking-[0.3em] text-white/20 uppercase"
                >
                    Stratos Talent OS v4.5
                </div>
                <div />
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
.st-modal-glass {
    background: rgba(15, 23, 42, 0.8) !important;
    backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
.st-modal-header-gradient {
    background: linear-gradient(
        135deg,
        rgba(99, 102, 241, 0.2) 0%,
        rgba(168, 85, 247, 0.2) 100%
    );
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.st-modal-body {
    overflow-y: auto;
}
</style>

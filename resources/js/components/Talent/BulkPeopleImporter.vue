<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { post } from '@/apiHelper';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import { 
    PhUploadSimple, 
    PhCheckCircle, 
    PhWarningCircle, 
    PhSignature, 
    // PhUser, 
    PhBuildings, 
    PhSuitcase,
    PhArrowRight,
    PhArrowLeft,
    PhArrowsClockwise,
    PhX
} from '@phosphor-icons/vue';

const { t } = useI18n();

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits(['close', 'completed', 'update:isOpen']);

const dialogModel = computed({
    get: () => props.isOpen,
    set: (val) => emit('update:isOpen', val)
});

const step = ref(1);
const file = ref<File | null>(null);
const rawData = ref<any[]>([]);
const analysis = ref<any>(null);
const loading = ref(false);
const changeSetId = ref<number | null>(null);

const steps = [
    { title: 'Carga', icon: PhUploadSimple },
    { title: 'Alineación', icon: PhBuildings },
    { title: 'Resolución', icon: PhWarningCircle },
    { title: 'Aprobación', icon: PhSignature },
];

const handleFileUpload = (e: any) => {
    const f = e.target.files[0];
    if (f) {
        file.value = f;
        parseCSV(f);
    }
};

const parseCSV = async (f: File) => {
    try {
        const text = await f.text();
        const rows = text.split('\n').filter(r => r.trim());
        const headers = rows[0].split(',').map(h => h.trim());
        
        rawData.value = rows.slice(1).map(row => {
            const values = row.split(',');
            const obj: any = {};
            headers.forEach((h, i) => {
                obj[h.toLowerCase().replaceAll(' ', '_')] = values[i]?.trim();
            });
            return obj;
        });
        
        // Mapeo automático básico
        step.value = 2;
    } catch (error) {
        console.error('Error parsing CSV:', error);
    }
};

const runAnalysis = async () => {
    loading.value = true;
    try {
        const res = await post('/talent/bulk-import/analyze', { rows: rawData.value });
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
        const res = await post('/talent/bulk-import/stage', { 
            rows: rawData.value,
            mapping: {
                departments: analysis.value.detected_departments,
                roles: analysis.value.detected_roles,
                movements: analysis.value.movements
            }
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
        await post(`/talent/bulk-import/${changeSetId.value}/approve`, {
            signature: 'digital_sign_hash_' + Date.now()
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
            <div class="st-modal-header-gradient p-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-white flex items-center gap-3">
                        <PhUploadSimple :size="28" />
                        Stratos Node Aligner
                    </h2>
                    <p class="text-white/60 text-sm mt-1">Sincronización masiva de talento con Nodos Gravitacionales</p>
                </div>
                <StButtonGlass variant="ghost" :icon="PhX" circle @click="close" />
            </div>

            <!-- Wizard Stepper -->
            <div class="flex items-center justify-center gap-8 bg-white/5 p-4 border-b border-white/5">
                <div v-for="(s, i) in steps" :key="i" class="flex items-center gap-3">
                    <div 
                        :class="[
                            'h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                            step > i + 1 ? 'bg-emerald-500 text-white' : 
                            step === i + 1 ? 'bg-indigo-500 text-white shadow-[0_0_15px_rgba(99,102,241,0.5)]' : 
                            'bg-white/10 text-white/30'
                        ]"
                    >
                        <component v-if="step > i + 1" :is="PhCheckCircle" :size="16" />
                        <span v-else>{{ i + 1 }}</span>
                    </div>
                    <span :class="['text-xs font-black uppercase tracking-widest', step === i + 1 ? 'text-white' : 'text-white/20']">
                        {{ t(s.title) || s.title }}
                    </span>
                    <div v-if="i < steps.length - 1" class="h-px w-8 bg-white/10 ml-2" />
                </div>
            </div>

            <!-- Body -->
            <div class="st-modal-body p-8 min-h-[400px]">
                
                <!-- STEP 1: UPLOAD -->
                <div v-if="step === 1" class="flex flex-col items-center justify-center py-12">
                    <div class="w-full max-w-lg p-12 border-2 border-dashed border-white/10 rounded-3xl bg-white/5 flex flex-col items-center justify-center transition-all hover:border-indigo-500/30 hover:bg-white/10 group">
                        <div class="h-24 w-24 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 mb-6 group-hover:scale-110 transition-transform">
                            <PhUploadSimple :size="48" />
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Sube tu nómina de personal</h3>
                        <p class="text-white/40 text-center mb-8">Formatos soportados: CSV, XLS, XLSX. El sistema intentará alinear los cargos y departamentos automáticamente.</p>
                        
                        <input type="file" id="fileImport" class="hidden" accept=".csv" @change="handleFileUpload">
                        <label for="fileImport">
                            <StButtonGlass variant="primary" size="lg" :icon="PhUploadSimple">
                                Seleccionar Archivo
                            </StButtonGlass>
                        </label>
                    </div>
                </div>

                <!-- STEP 2: ALIGNMENT PREVIEW -->
                <div v-if="step === 2" class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Vista Previa y Mapeo</h3>
                        <StBadgeGlass variant="primary">{{ rawData.length }} registros detectados</StBadgeGlass>
                    </div>
                    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-black/20">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-white/5">
                                    <th v-for="h in (rawData.length > 0 ? Object.keys(rawData[0]) : [])" :key="h" class="p-4 font-black uppercase tracking-widest text-white/30 text-[10px]">
                                        {{ h }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, idx) in rawData.slice(0, 5)" :key="idx" class="border-t border-white/5">
                                    <td v-for="(val, vIdx) in Object.values(row)" :key="vIdx" class="p-4 text-white/70">
                                        {{ val }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="rawData.length > 5" class="text-center text-white/30 text-xs">... y {{ rawData.length - 5 }} filas más ...</div>
                    
                    <div class="flex justify-center pt-8">
                        <StButtonGlass variant="primary" size="lg" :icon="PhArrowRight" @click="runAnalysis" :loading="loading">
                            Analizar Estructura Organizational
                        </StButtonGlass>
                    </div>
                </div>

                <!-- STEP 3: RESOLUTION -->
                <div v-if="step === 3 && analysis" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- DEPARTMENTS -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-white/50 mb-2">
                                <PhBuildings :size="18" />
                                <span class="text-xs font-black uppercase tracking-widest">Alineación de Departamentos</span>
                            </div>
                            <div v-for="dept in analysis.detected_departments" :key="dept.raw_name" class="p-4 rounded-2xl border border-white/5 bg-white/5 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-white font-bold">{{ dept.raw_name }}</span>
                                    <span class="text-[10px] text-white/40 uppercase tracking-tighter" v-if="dept.status === 'existing'">Match con Nodo Existente</span>
                                    <span class="text-[10px] text-indigo-400 uppercase tracking-tighter" v-else>Creará Nuevo Nodo</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <PhArrowRight :size="14" class="text-white/20" />
                                    <StBadgeGlass :variant="dept.status === 'existing' ? 'success' : 'warning'">
                                        {{ dept.suggested_name }}
                                    </StBadgeGlass>
                                </div>
                            </div>
                        </div>

                        <!-- ROLES -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-white/50 mb-2">
                                <PhSuitcase :size="18" />
                                <span class="text-xs font-black uppercase tracking-widest">Alineación de Cargos / Roles</span>
                            </div>
                            <div v-for="role in analysis.detected_roles" :key="role.raw_name" class="p-4 rounded-2xl border border-white/5 bg-white/5 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-white font-bold">{{ role.raw_name }}</span>
                                    <span class="text-[10px] text-white/40 uppercase tracking-tighter" v-if="role.status === 'existing'">Match Encontrado</span>
                                    <span class="text-[10px] text-amber-400 uppercase tracking-tighter" v-else>Sugerencia Estratégica</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <PhArrowRight :size="14" class="text-white/20" />
                                    <StBadgeGlass :variant="role.status === 'existing' ? 'success' : 'primary'">
                                        {{ role.suggested_name }}
                                    </StBadgeGlass>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TALENT DYNAMICS (Hires, Transfers, Exits) -->
                    <div class="pt-8 border-t border-white/5">
                        <div class="flex items-center gap-2 text-white/50 mb-6">
                            <PhArrowsClockwise :size="18" />
                            <span class="text-xs font-black uppercase tracking-widest">Dinámica de Talento (Lifecycle)</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- NEW HIRES -->
                            <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black uppercase text-emerald-400">Nuevos Ingresos</span>
                                    <StBadgeGlass variant="success">{{ analysis.movements.hires.length }}</StBadgeGlass>
                                </div>
                                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                                    <div v-for="h in analysis.movements.hires" :key="h.email" class="text-xs text-white/60 flex items-center gap-2">
                                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                        {{ h.name }}
                                    </div>
                                    <div v-if="analysis.movements.hires.length === 0" class="text-xs text-white/20 italic">Sin nuevos ingresos</div>
                                </div>
                            </div>

                            <!-- TRANSFERS -->
                            <div class="p-4 rounded-2xl bg-indigo-500/5 border border-indigo-500/10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black uppercase text-indigo-400">Traslados / Ascensos</span>
                                    <StBadgeGlass variant="primary">{{ analysis.movements.transfers.length }}</StBadgeGlass>
                                </div>
                                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                                    <div v-for="t in analysis.movements.transfers" :key="t.email" class="text-xs text-white/60 flex flex-col gap-0.5">
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
                                            {{ t.name }}
                                        </div>
                                        <div class="text-[9px] text-white/30 ml-3">{{ t.changes.join(' | ') }}</div>
                                    </div>
                                    <div v-if="analysis.movements.transfers.length === 0" class="text-xs text-white/20 italic">Sin movimientos internos</div>
                                </div>
                            </div>

                            <!-- EXITS -->
                            <div class="p-4 rounded-2xl bg-rose-500/5 border border-rose-500/10">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black uppercase text-rose-400">Egresos Detectados</span>
                                    <StBadgeGlass variant="error">{{ analysis.movements.exits.length }}</StBadgeGlass>
                                </div>
                                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                                    <div v-for="e in analysis.movements.exits" :key="e.email" class="text-xs text-white/60 flex items-center gap-2">
                                        <div class="h-1.5 w-1.5 rounded-full bg-rose-500" />
                                        {{ e.name }}
                                    </div>
                                    <div v-if="analysis.movements.exits.length === 0" class="text-xs text-white/20 italic">Sin egresos detectados</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center pt-8 gap-4">
                        <StButtonGlass variant="secondary" @click="step = 2">Revisar Mapeo</StButtonGlass>
                        <StButtonGlass variant="primary" size="lg" :icon="PhCheckCircle" @click="stageImport" :loading="loading">
                            Confirmar y Preparar Firma
                        </StButtonGlass>
                    </div>
                </div>

                <!-- STEP 4: SIGNATURE -->
                <div v-if="step === 4" class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-full max-w-lg p-12 rounded-3xl bg-indigo-500/5 border border-indigo-500/20 relative overflow-hidden">
                        <!-- BG Decoration -->
                        <div class="absolute -top-12 -right-12 h-48 w-48 rounded-full bg-indigo-500/10 blur-3xl" />
                        
                        <div class="h-20 w-20 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 mx-auto mb-6">
                            <PhSignature :size="40" />
                        </div>
                        
                        <h3 class="text-2xl font-black text-white mb-2">Aprobación de Versión 0</h3>
                        <p class="text-white/60 mb-8">Esta acción establecerá el **Baseline Organizacional**. Todos los cambios futuros serán trackeados desde este punto.</p>
                        
                        <div class="bg-black/40 rounded-2xl p-6 border border-white/5 text-left mb-8">
                            <div class="flex items-center justify-between mb-4 border-b border-white/5 pb-4">
                                <span class="text-xs font-black uppercase text-white/30">Documento de Carga</span>
                                <span class="text-xs font-bold text-indigo-300">#CHANGE_{{ changeSetId }}</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-white/40">Total Colaboradores:</span>
                                    <span class="text-white font-bold">{{ rawData.length }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-white/40">Nuevos Departamentos:</span>
                                    <span class="text-emerald-400 font-bold">{{ analysis?.detected_departments.filter((d: { status: string; }) => d.status === 'new').length }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-white/40">Responsable:</span>
                                    <span class="text-white font-bold">{{ $page.props.auth.user.name }}</span>
                                </div>
                            </div>
                        </div>

                        <StButtonGlass variant="primary" size="lg" block :icon="PhSignature" @click="commitImport" :loading="loading">
                            Firmar y Sincronizar Stratos
                        </StButtonGlass>
                        
                        <p class="text-[10px] text-white/20 mt-4 uppercase tracking-[0.2em] font-bold">Registro de Auditoría Activado</p>
                    </div>
                </div>

            </div>

            <!-- Footer for generic navigation -->
            <div class="st-modal-footer p-6 border-t border-white/5 flex justify-between items-center bg-black/20" v-if="step > 1 && step < 4">
                <StButtonGlass variant="ghost" :icon="PhArrowLeft" @click="step--">Anterior</StButtonGlass>
                <div class="text-[10px] font-black uppercase tracking-[0.3em] text-white/20">Stratos Talent OS v4.5</div>
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
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(168, 85, 247, 0.2) 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.st-modal-body {
    overflow-y: auto;
}
</style>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { 
    PhSealCheck, 
    PhFileText, 
    PhSignature, 
    PhCheckCircle, 
    PhXCircle,
    PhBriefcase,
    PhTarget,
    PhListChecks,
    PhInfo
} from '@phosphor-icons/vue';
import axios from 'axios';
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';

const props = defineProps<{
    token: string;
}>();

const loading = ref(true);
const error = ref<string | null>(null);
const approval = ref<any>(null);
const role = ref<any>(null);
const approver = ref<any>(null);
const submitting = ref(false);
const success = ref(false);

const form = ref({
    description: '',
    purpose: '',
    expected_results: '',
});

onMounted(async () => {
    try {
        const response = await axios.get(`/api/approvals/${props.token}`);
        if (response.data.success) {
            approval.value = response.data.approval;
            role.value = response.data.approvable;
            approver.value = response.data.approver;
            
            // Prefill form
            form.value.description = role.value.description || '';
            form.value.purpose = role.value.purpose || '';
            form.value.expected_results = role.value.expected_results || '';
        } else {
            error.value = 'No se pudo cargar la solicitud de aprobación.';
        }
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Error al conectar con el servidor.';
    } finally {
        loading.value = false;
    }
});

const handleApprove = async () => {
    submitting.value = true;
    try {
        const response = await axios.post(`/api/approvals/${props.token}/approve`, form.value);
        if (response.data.status === 'success') {
            success.value = true;
        } else {
            alert(response.data.message || 'Error al aprobar.');
        }
    } catch (err: any) {
        alert(err.response?.data?.message || 'Error en el proceso de aprobación.');
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <div class="approval-page min-h-screen bg-slate-950 text-white py-12 px-4">
        <Head title="Aprobación de Rol Directivo" />
        
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="d-inline-flex pa-4 rounded-circle bg-indigo-500/10 border border-indigo-500/20 mb-6 glow-indigo">
                    <PhSealCheck :size="48" weight="duotone" class="text-indigo-400" />
                </div>
                <h1 class="text-h3 font-weight-black mb-2 font-premium tracking-tight">
                    Firma de Validación de Rol
                </h1>
                <p class="text-h6 text-slate-400 font-weight-medium">
                    Stratos Talent Engineering & Intelligence
                </p>
            </div>

            <div v-if="loading" class="d-flex flex-column align-center py-12">
                <v-progress-circular indeterminate color="indigo-accent-2" size="64" width="6" />
                <p class="mt-6 text-slate-400 animate-pulse">Cargando protocolo de validación...</p>
            </div>

            <div v-else-if="error" class="text-center py-12">
                <div class="pa-8 bg-red-500/10 border border-red-500/20 rounded-xl max-w-md mx-auto">
                    <PhXCircle :size="64" class="text-red-400 mb-4 mx-auto" />
                    <h3 class="text-h5 font-weight-bold mb-2">Enlace no válido</h3>
                    <p class="text-slate-400 mb-6">{{ error }}</p>
                    <StButtonGlass variant="secondary" @click="router.visit('/')">
                        Volver al Inicio
                    </StButtonGlass>
                </div>
            </div>

            <div v-else-if="success" class="text-center py-12">
                <div class="pa-10 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl max-w-xl mx-auto glass-glow-emerald">
                    <PhCheckCircle :size="80" weight="duotone" class="text-emerald-400 mb-6 mx-auto" />
                    <h2 class="text-h4 font-weight-bold mb-4">Firmado Exitosamente</h2>
                    <p class="text-h6 text-slate-300 mb-2">El rol <span class="text-white font-weight-bold">{{ role.name }}</span> ha sido validado.</p>
                    <p class="text-body-1 text-slate-400 mb-8">
                        La configuración ha sido sellada digitalmente y materializada en el catálogo activo de la organización.
                    </p>
                    <div class="pa-4 bg-black/40 rounded-lg border border-emerald-500/30 mb-8 font-mono text-xs text-emerald-200 break-all">
                        SEAL_ID: {{ approval.digital_signature || 'DIGITAL_SIGNATURE_GENERATE' }}
                    </div>
                    <StButtonGlass variant="primary" @click="router.visit('/')">
                        Ir al Tablero Principal
                    </StButtonGlass>
                </div>
            </div>

            <div v-else class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <!-- Info Banner -->
                <StCardGlass class="pa-6 border-indigo-500/30 overflow-hidden relative">
                    <div class="absolute inset-0 bg-linear-to-r from-indigo-500/5 to-transparent"></div>
                    <v-row align="center">
                        <v-col cols="12" md="8">
                            <div class="d-flex align-center mb-2">
                                <StBadgeGlass variant="primary" size="sm" class="mr-3">SOLICITUD PENDIENTE</StBadgeGlass>
                                <span class="text-caption text-slate-400 uppercase tracking-widest font-weight-bold">Token de Seguridad: {{ props.token.substring(0,8) }}...</span>
                            </div>
                            <h2 class="text-h4 font-weight-bold text-white mb-2">{{ role.name }}</h2>
                            <p class="text-body-1 text-slate-300">
                                Estimado/a <span class="text-white font-weight-bold">{{ approver?.full_name || 'Responsable' }}</span>, se requiere su revisión y firma para oficializar este nuevo diseño de rol.
                            </p>
                        </v-col>
                        <v-col cols="12" md="4" class="text-md-right">
                            <v-avatar size="64" class="border-2 border-indigo-500/30 mb-2">
                                <v-img v-if="approver?.avatar_url" :src="approver.avatar_url" />
                                <span v-else class="text-h5">{{ approver?.full_name?.[0] || 'R' }}</span>
                            </v-avatar>
                            <div class="text-subtitle-2 font-weight-bold text-white">{{ approver?.full_name }}</div>
                            <div class="text-caption text-slate-400">{{ approver?.job_title || 'Responsable de Aprobación' }}</div>
                        </v-col>
                    </v-row>
                </StCardGlass>

                <!-- Editable Fields -->
                <div class="grid grid-cols-1 gap-6">
                    <section>
                        <div class="d-flex align-center mb-4">
                            <PhFileText :size="24" class="text-indigo-400 mr-2" />
                            <h3 class="text-h5 font-weight-bold">Definición de Misión</h3>
                        </div>
                        <StCardGlass class="pa-0 overflow-hidden">
                            <v-textarea
                                v-model="form.description"
                                label="Misión de Negocio / Descripción"
                                variant="solo"
                                flat
                                hide-details
                                auto-grow
                                class="glass-input-premium"
                                placeholder="Defina la misión estratégica del rol..."
                            />
                        </StCardGlass>
                    </section>

                    <section>
                        <div class="d-flex align-center mb-4">
                            <PhTarget :size="24" class="text-emerald-400 mr-2" />
                            <h3 class="text-h5 font-weight-bold">Propósito Estratégico</h3>
                        </div>
                        <StCardGlass class="pa-0 overflow-hidden">
                            <v-textarea
                                v-model="form.purpose"
                                label="Propósito"
                                variant="solo"
                                flat
                                hide-details
                                auto-grow
                                class="glass-input-premium"
                                placeholder="¿Por qué existe este rol?"
                            />
                        </StCardGlass>
                    </section>

                    <section>
                        <div class="d-flex align-center mb-4">
                            <PhListChecks :size="24" class="text-amber-400 mr-2" />
                            <h3 class="text-h5 font-weight-bold">Resultados Esperados</h3>
                        </div>
                        <StCardGlass class="pa-0 overflow-hidden">
                            <v-textarea
                                v-model="form.expected_results"
                                label="Entregables y Metas"
                                variant="solo"
                                flat
                                hide-details
                                auto-grow
                                class="glass-input-premium"
                                placeholder="¿Qué resultados debe producir?"
                            />
                        </StCardGlass>
                    </section>
                </div>

                <!-- Competencies Preview -->
                <section>
                    <div class="d-flex align-center mb-4">
                        <PhBriefcase :size="24" class="text-sky-400 mr-2" />
                        <h3 class="text-h5 font-weight-bold">Capacidad & Competencias Propuestas</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <StCardGlass 
                            v-for="skill in role.skills" 
                            :key="skill.id"
                            class="pa-5 hover-glow-indigo transition-all duration-300"
                        >
                            <div class="d-flex justify-space-between align-start mb-2">
                                <h4 class="text-subtitle-1 font-weight-bold text-white">{{ skill.name }}</h4>
                                <StBadgeGlass variant="secondary" size="sm">Nivel {{ skill.pivot?.required_level || 3 }}</StBadgeGlass>
                            </div>
                            <p class="text-caption text-slate-400 line-clamp-2">{{ skill.description }}</p>
                            <div v-if="skill.pivot?.rationale" class="mt-3 pa-3 bg-white/5 rounded-lg border-l-2 border-indigo-500/40">
                                <p class="text-xs text-slate-300 italic">"{{ skill.pivot.rationale }}"</p>
                            </div>
                        </StCardGlass>
                    </div>
                </section>

                <!-- Final Action -->
                <div class="pt-8 text-center">
                    <div class="mb-6 d-flex justify-center">
                        <div class="pa-4 bg-amber-500/10 border border-amber-500/20 rounded-xl d-flex align-center max-w-xl text-left">
                            <div class="mr-4 rounded-lg bg-white/10 d-flex align-center justify-center shrink-0">
                                <PhInfo :size="32" class="text-amber-400" />
                            </div>
                            <p class="text-sm text-slate-300">
                                Al presionar el botón de abajo, usted certifica la validez técnica de este diseño y autoriza su incorporación inmediata a los sistemas operativos de talento.
                            </p>
                        </div>
                    </div>
                    
                    <StButtonGlass
                        variant="primary"
                        size="lg"
                        :icon="PhSignature"
                        :loading="submitting"
                        @click="handleApprove"
                        class="px-12 py-6 text-h6 shadow-indigo-lg"
                    >
                        APROBAR Y FIRMAR DIGITALMENTE
                    </StButtonGlass>
                    
                    <p class="mt-4 text-caption text-slate-500 text-uppercase tracking-tighter font-weight-bold">
                        Powered by Stratos AI Governance & Digital Seal Technology
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.font-premium {
    font-family: 'Outfit', 'Inter', sans-serif;
    letter-spacing: -0.02em;
}

.glow-indigo {
    box-shadow: 0 0 30px rgba(99, 102, 241, 0.2);
}

.glass-glow-emerald {
    box-shadow: 0 0 40px rgba(16, 185, 129, 0.1);
}

.hover-glow-indigo:hover {
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
    border-color: rgba(99, 102, 241, 0.3);
}

.shadow-indigo-lg {
    box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
}

:deep(.glass-input-premium) {
    --v-field-padding-start: 1.5rem;
    --v-field-padding-end: 1.5rem;
    --v-field-padding-top: 1.5rem;
    --v-field-padding-bottom: 1.5rem;
}

:deep(.glass-input-premium .v-field) {
    background: transparent !important;
    font-size: 1.125rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9) !important;
}

:deep(.glass-input-premium .v-label) {
    color: rgba(255, 255, 255, 0.4) !important;
    font-weight: 500;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animations */
.animate-in {
    animation: animate-in 0.7s ease-out;
}

@keyframes animate-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

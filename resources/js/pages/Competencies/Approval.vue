<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { 
    PhFileText, 
    PhSignature, 
    PhCheckCircle, 
    PhXCircle,
    PhLightning,
    PhListChecks,
    PhInfo,
    PhBrain
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
const competency = ref<any>(null);
const approver = ref<any>(null);
const submitting = ref(false);
const success = ref(false);

const form = ref({
    name: '',
    description: '',
});

onMounted(async () => {
    try {
        const response = await axios.get(`/api/approvals/${props.token}`);
        if (response.data.success) {
            approval.value = response.data.approval;
            competency.value = response.data.approvable;
            approver.value = response.data.approver;
            
            // Prefill form
            form.value.name = competency.value.name || '';
            form.value.description = competency.value.description || '';
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
        <Head title="Aprobación de Competencia Organizacional" />
        
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="d-inline-flex pa-4 rounded-circle bg-indigo-500/10 border border-indigo-500/20 mb-6 glow-indigo">
                    <PhBrain :size="48" weight="duotone" class="text-indigo-400" />
                </div>
                <h1 class="text-h3 font-weight-black mb-2 font-premium tracking-tight">
                    Validación de Competencia
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
                    <p class="text-h6 text-slate-300 mb-2">La competencia <span class="text-white font-weight-bold">{{ competency.name }}</span> ha sido validada.</p>
                    <p class="text-body-1 text-slate-400 mb-8">
                        La configuración ha sido sellada digitalmente y publicada en el catálogo activo.
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
                    <div class="absolute inset-0 bg-linear-to-r from-cyan-500/5 to-transparent"></div>
                    <v-row align="center">
                        <v-col cols="12" md="8">
                            <div class="d-flex align-center mb-2">
                                <StBadgeGlass variant="primary" size="sm" class="mr-3">SOLICITUD PENDIENTE</StBadgeGlass>
                                <span class="text-caption text-slate-400 uppercase tracking-widest font-weight-bold">Token: {{ props.token.substring(0,8) }}...</span>
                            </div>
                            <h2 class="text-h4 font-weight-bold text-white mb-2">{{ competency.name }}</h2>
                            <p class="text-body-1 text-slate-300">
                                Estimado/a <span class="text-white font-weight-bold">{{ approver?.full_name || 'Responsable' }}</span>, se requiere su revisión y firma para oficializar esta competencia en el catálogo organizacional.
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
                            <h3 class="text-h5 font-weight-bold">Nombre de la Competencia</h3>
                        </div>
                        <StCardGlass class="pa-0 overflow-hidden">
                            <v-text-field
                                v-model="form.name"
                                label="Nombre Oficial"
                                variant="solo"
                                flat
                                hide-details
                                class="glass-input-premium"
                            />
                        </StCardGlass>
                    </section>

                    <section>
                        <div class="d-flex align-center mb-4">
                            <PhListChecks :size="24" class="text-emerald-400 mr-2" />
                            <h3 class="text-h5 font-weight-bold">Descripción & Alcance</h3>
                        </div>
                        <StCardGlass class="pa-0 overflow-hidden">
                            <v-textarea
                                v-model="form.description"
                                label="Definición Técnica"
                                variant="solo"
                                flat
                                hide-details
                                auto-grow
                                class="glass-input-premium"
                                placeholder="Defina el alcance de esta competencia..."
                            />
                        </StCardGlass>
                    </section>
                </div>

                <!-- Skills Preview -->
                <section v-if="competency.skills?.length">
                    <div class="d-flex align-center mb-4">
                        <PhLightning :size="24" class="text-sky-400 mr-2" />
                        <h3 class="text-h5 font-weight-bold">Habilidades Asociadas</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <StCardGlass 
                            v-for="skill in competency.skills" 
                            :key="skill.id"
                            class="pa-5 hover-glow-indigo transition-all duration-300"
                        >
                            <div class="d-flex justify-space-between align-start mb-2">
                                <h4 class="text-subtitle-1 font-weight-bold text-white">{{ skill.name }}</h4>
                                <StBadgeGlass :variant="skill.category === 'technical' ? 'primary' : 'success'" size="sm">
                                    {{ skill.category }}
                                </StBadgeGlass>
                            </div>
                            <p class="text-caption text-slate-400 line-clamp-2">{{ skill.description }}</p>
                        </StCardGlass>
                    </div>
                </section>

                <!-- Final Action -->
                <div class="pt-8 text-center">
                    <div class="mb-6 d-flex justify-center">
                        <div class="pa-4 bg-amber-500/10 border border-amber-500/20 rounded-xl d-flex align-center max-w-xl text-left">
                            <PhInfo :size="32" class="text-amber-400 mr-4 shrink-0" />
                            <p class="text-sm text-slate-300">
                                Al presionar el botón de abajo, usted certifica la validez técnica de esta competencia y autoriza su publicación en el catálogo organizacional.
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
                        Stratos AI Digital Seal Technology
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

:deep(.glass-input-premium .v-field) {
    background: transparent !important;
    font-size: 1.125rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9) !important;
}

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

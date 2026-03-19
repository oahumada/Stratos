<script setup lang="ts">
import { ref, computed } from 'vue';
import { 
    PhSealCheck, 
    PhFingerprint, 
    PhCalendar, 
    PhUser, 
    PhInfo, 
    PhCertificate,
    PhX
} from '@phosphor-icons/vue';
import StCardGlass from './StCardGlass.vue';
import StBadgeGlass from './StBadgeGlass.vue';

const props = defineProps<{
    item: any; // Role or Competency
    type: 'role' | 'competency';
}>();

const showCertificate = ref(false);

const isSigned = computed(() => !!props.item.digital_signature);

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-CL', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const openCertificate = () => {
    if (isSigned.value) {
        showCertificate.value = true;
    }
};
</script>

<template>
    <div class="digital-seal-container d-inline-block">
        <!-- Main Seal Badge (Marketing Trigger) -->
        <button 
            v-if="isSigned"
            @click="openCertificate"
            class="seal-trigger group relative flex items-center gap-2 px-3 py-1.5 rounded-full transition-all duration-500 overflow-hidden"
            :class="[
                'bg-emerald-500/10 border border-emerald-500/30 hover:bg-emerald-500/20 hover:border-emerald-500/50',
                'animate-in fade-in zoom-in duration-1000'
            ]"
        >
            <div class="absolute inset-0 bg-linear-to-r from-emerald-500/0 via-emerald-500/5 to-emerald-500/0 group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
            
            <PhSealCheck 
                :size="20" 
                weight="fill" 
                class="text-emerald-400 group-hover:scale-110 transition-transform duration-300" 
            />
            
            <span class="text-xs font-bold tracking-wider text-emerald-400/90 uppercase">
                ISO 9001 VALIDATED
            </span>
            
            <!-- Tooltip pulse -->
            <span class="absolute -right-1 -top-1 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
        </button>

        <div v-else class="text-xs text-slate-500 italic d-flex align-center gap-2">
            <PhInfo :size="14" /> Pendiente de Validación Oficial
        </div>

        <!-- Premium Certificate Modal -->
        <v-dialog 
            v-model="showCertificate" 
            max-width="600" 
            persistent
            transition="dialog-bottom-transition"
        >
            <StCardGlass class="certificate-modal relative overflow-hidden backdrop-blur-2xl">
                <!-- Ornament Backgrounds -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>

                <div class="pa-10 relative">
                    <!-- Close button -->
                    <button 
                        @click="showCertificate = false" 
                        class="absolute top-6 right-6 text-slate-400 hover:text-white transition-colors"
                    >
                        <PhX :size="24" />
                    </button>

                    <!-- Certificate Header -->
                    <div class="text-center mb-10">
                        <div class="d-inline-flex pa-4 rounded-circle bg-emerald-500/10 border border-emerald-500/20 mb-6 relative">
                            <PhCertificate :size="56" weight="duotone" class="text-emerald-400" />
                            <div class="absolute -right-2 bottom-0 bg-indigo-600 rounded-full pa-1 border-2 border-slate-900">
                                <PhFingerprint :size="16" class="text-white" />
                            </div>
                        </div>
                        <h2 class="text-h4 font-weight-black text-white mb-1 font-premium tracking-tight uppercase">
                            Certificado de Validez Técnica
                        </h2>
                        <div class="h-1 w-24 bg-linear-to-r from-emerald-500 to-indigo-500 mx-auto rounded-full mb-2"></div>
                        <p class="text-caption text-slate-400 uppercase tracking-widest font-weight-bold">
                            Stratos Global Talent Intelligence Protocol
                        </p>
                    </div>

                    <!-- Certificate Body -->
                    <div class="space-y-6">
                        <div class="pa-6 rounded-2xl bg-white/5 border border-white/10 text-center">
                            <p class="text-slate-400 text-sm mb-1">Se certifica que el diseño de {{ type === 'role' ? 'Rol' : 'Competencia' }}:</p>
                            <h3 class="text-h5 font-weight-bold text-white mb-2">{{ item.name }}</h3>
                            <div class="d-flex justify-center flex-wrap gap-2">
                                <StBadgeGlass variant="success" size="sm">CUMPLIMIENTO ISO 9001:2015</StBadgeGlass>
                                <StBadgeGlass variant="primary" size="sm">ID: #{{ item.id }}</StBadgeGlass>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="pa-4 rounded-xl bg-black/20 border border-white/5">
                                <div class="d-flex align-center gap-3 mb-2">
                                    <PhUser :size="18" class="text-indigo-400" />
                                    <span class="text-xs text-slate-400 font-weight-bold uppercase">Aprobado Por</span>
                                </div>
                                <div class="text-sm text-white font-weight-bold">{{ item.approver_name || 'Responsable de Organización' }}</div>
                            </div>
                            <div class="pa-4 rounded-xl bg-black/20 border border-white/5">
                                <div class="d-flex align-center gap-3 mb-2">
                                    <PhCalendar :size="18" class="text-emerald-400" />
                                    <span class="text-xs text-slate-400 font-weight-bold uppercase">Sello de Fecha</span>
                                </div>
                                <div class="text-sm text-white font-weight-bold">{{ formatDate(item.signed_at) }}</div>
                            </div>
                        </div>

                        <!-- Signature Hash Section -->
                        <div class="pa-5 rounded-2xl border-2 border-dashed border-emerald-500/20 bg-emerald-500/5 overflow-hidden">
                            <div class="d-flex align-center gap-3 mb-3">
                                <PhFingerprint :size="20" class="text-emerald-400" />
                                <span class="text-xs text-emerald-400 font-weight-black uppercase tracking-tighter">
                                    Sello Digital de Integridad (SHA-256)
                                </span>
                            </div>
                            <div class="font-mono text-xs text-emerald-100/60 break-all leading-relaxed">
                                {{ item.digital_signature }}
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-10 pt-8 border-t border-white/10 d-flex justify-space-between align-center">
                        <div class="text-left">
                            <div class="text-[10px] text-slate-500 uppercase tracking-widest font-weight-black mb-1">Tecnología de Resguardo</div>
                            <div class="text-xs font-weight-bold text-slate-300">Stratos Trust & Governance Engine</div>
                        </div>
                        <PhCertificate :size="40" class="text-white/20" />
                    </div>
                </div>
            </StCardGlass>
        </v-dialog>
    </div>
</template>

<style scoped>
.font-premium {
    font-family: 'Outfit', 'Inter', sans-serif;
}

.certificate-modal {
    border: 1px solid rgba(var(--v-theme-emerald), 0.3) !important;
}

.seal-trigger:hover {
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
    transform: translateY(-2px);
}

.digital-signature-text {
    line-height: 1.4;
    letter-spacing: 0.05em;
}
</style>

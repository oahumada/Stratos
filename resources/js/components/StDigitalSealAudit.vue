<script setup lang="ts">
import {
    PhCalendar,
    PhCertificate,
    PhFingerprint,
    PhInfo,
    PhSealCheck,
    PhUser,
    PhX,
} from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import StBadgeGlass from './StBadgeGlass.vue';
import StCardGlass from './StCardGlass.vue';

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
        minute: '2-digit',
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
            class="seal-trigger group relative flex items-center gap-2 overflow-hidden rounded-full px-3 py-1.5 transition-all duration-500"
            :class="[
                'border border-emerald-500/30 bg-emerald-500/10 hover:border-emerald-500/50 hover:bg-emerald-500/20',
                'animate-in duration-1000 fade-in zoom-in',
            ]"
        >
            <div
                class="absolute inset-0 bg-linear-to-r from-emerald-500/0 via-emerald-500/5 to-emerald-500/0 transition-transform duration-1000 ease-in-out group-hover:translate-x-full"
            ></div>

            <PhSealCheck
                :size="20"
                weight="fill"
                class="text-emerald-400 transition-transform duration-300 group-hover:scale-110"
            />

            <span
                class="text-xs font-bold tracking-wider text-emerald-400/90 uppercase"
            >
                ISO 9001 VALIDATED
            </span>

            <!-- Tooltip pulse -->
            <span class="absolute -top-1 -right-1 flex h-2 w-2">
                <span
                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
                ></span>
                <span
                    class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"
                ></span>
            </span>
        </button>

        <div
            v-else
            class="d-flex align-center gap-2 text-xs text-slate-500 italic"
        >
            <PhInfo :size="14" /> Pendiente de Validación Oficial
        </div>

        <!-- Premium Certificate Modal -->
        <v-dialog
            v-model="showCertificate"
            max-width="600"
            persistent
            transition="dialog-bottom-transition"
        >
            <StCardGlass
                class="certificate-modal relative overflow-hidden backdrop-blur-2xl"
            >
                <!-- Ornament Backgrounds -->
                <div
                    class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"
                ></div>

                <div class="pa-10 relative">
                    <!-- Close button -->
                    <button
                        @click="showCertificate = false"
                        class="absolute top-6 right-6 text-slate-400 transition-colors hover:text-white"
                    >
                        <PhX :size="24" />
                    </button>

                    <!-- Certificate Header -->
                    <div class="mb-10 text-center">
                        <div
                            class="d-inline-flex pa-4 rounded-circle relative mb-6 border border-emerald-500/20 bg-emerald-500/10"
                        >
                            <PhCertificate
                                :size="56"
                                weight="duotone"
                                class="text-emerald-400"
                            />
                            <div
                                class="pa-1 absolute -right-2 bottom-0 rounded-full border-2 border-slate-900 bg-indigo-600"
                            >
                                <PhFingerprint :size="16" class="text-white" />
                            </div>
                        </div>
                        <h2
                            class="text-h4 font-weight-black font-premium mb-1 tracking-tight text-white uppercase"
                        >
                            Certificado de Validez Técnica
                        </h2>
                        <div
                            class="mx-auto mb-2 h-1 w-24 rounded-full bg-linear-to-r from-emerald-500 to-indigo-500"
                        ></div>
                        <p
                            class="text-caption font-weight-bold tracking-widest text-slate-400 uppercase"
                        >
                            Stratos Global Talent Intelligence Protocol
                        </p>
                    </div>

                    <!-- Certificate Body -->
                    <div class="space-y-6">
                        <div
                            class="pa-6 rounded-2xl border border-white/10 bg-white/5 text-center"
                        >
                            <p class="mb-1 text-sm text-slate-400">
                                Se certifica que el diseño de
                                {{ type === 'role' ? 'Rol' : 'Competencia' }}:
                            </p>
                            <h3
                                class="text-h5 font-weight-bold mb-2 text-white"
                            >
                                {{ item.name }}
                            </h3>
                            <div class="d-flex flex-wrap justify-center gap-2">
                                <StBadgeGlass variant="success" size="sm"
                                    >CUMPLIMIENTO ISO 9001:2015</StBadgeGlass
                                >
                                <StBadgeGlass variant="primary" size="sm"
                                    >ID: #{{ item.id }}</StBadgeGlass
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                class="pa-4 rounded-xl border border-white/5 bg-black/20"
                            >
                                <div class="d-flex align-center mb-2 gap-3">
                                    <PhUser
                                        :size="18"
                                        class="text-indigo-400"
                                    />
                                    <span
                                        class="font-weight-bold text-xs text-slate-400 uppercase"
                                        >Aprobado Por</span
                                    >
                                </div>
                                <div
                                    class="font-weight-bold text-sm text-white"
                                >
                                    {{
                                        item.approver_name ||
                                        'Responsable de Organización'
                                    }}
                                </div>
                            </div>
                            <div
                                class="pa-4 rounded-xl border border-white/5 bg-black/20"
                            >
                                <div class="d-flex align-center mb-2 gap-3">
                                    <PhCalendar
                                        :size="18"
                                        class="text-emerald-400"
                                    />
                                    <span
                                        class="font-weight-bold text-xs text-slate-400 uppercase"
                                        >Sello de Fecha</span
                                    >
                                </div>
                                <div
                                    class="font-weight-bold text-sm text-white"
                                >
                                    {{ formatDate(item.signed_at) }}
                                </div>
                            </div>
                        </div>

                        <!-- Signature Hash Section -->
                        <div
                            class="pa-5 overflow-hidden rounded-2xl border-2 border-dashed border-emerald-500/20 bg-emerald-500/5"
                        >
                            <div class="d-flex align-center mb-3 gap-3">
                                <PhFingerprint
                                    :size="20"
                                    class="text-emerald-400"
                                />
                                <span
                                    class="font-weight-black text-xs tracking-tighter text-emerald-400 uppercase"
                                >
                                    Sello Digital de Integridad (SHA-256)
                                </span>
                            </div>
                            <div
                                class="font-mono text-xs leading-relaxed break-all text-emerald-100/60"
                            >
                                {{ item.digital_signature }}
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="d-flex justify-space-between align-center mt-10 border-t border-white/10 pt-8"
                    >
                        <div class="text-left">
                            <div
                                class="font-weight-black mb-1 text-[10px] tracking-widest text-slate-500 uppercase"
                            >
                                Tecnología de Resguardo
                            </div>
                            <div
                                class="font-weight-bold text-xs text-slate-300"
                            >
                                Stratos Trust & Governance Engine
                            </div>
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

<script setup lang="ts">
import { PhCertificate, PhSealCheck, PhWarning } from '@phosphor-icons/vue';
import { computed } from 'vue';

const props = defineProps<{
    isVerified: boolean;
    signedAt?: string;
    version?: string;
}>();

const statusColor = computed(() =>
    props.isVerified ? 'text-emerald-400' : 'text-amber-400',
);
const statusBg = computed(() =>
    props.isVerified ? 'bg-emerald-500/10' : 'bg-amber-500/10',
);
const statusBorder = computed(() =>
    props.isVerified ? 'border-emerald-500/20' : 'border-amber-500/20',
);
</script>

<template>
    <div
        class="group flex items-center gap-3 rounded-lg border px-3 py-1.5 backdrop-blur-md transition-all duration-300"
        :class="[statusBg, statusBorder]"
    >
        <div class="relative flex items-center justify-center">
            <component
                :is="isVerified ? PhSealCheck : PhWarning"
                :size="18"
                weight="duotone"
                :class="[statusColor, isVerified ? 'animate-pulse' : '']"
            />
            <div
                v-if="isVerified"
                class="absolute inset-0 scale-150 rounded-full bg-emerald-400/20 opacity-0 blur-md transition-opacity group-hover:opacity-100"
            ></div>
        </div>

        <div class="flex flex-col border-l border-white/5 pl-3">
            <span
                class="flex items-center gap-1 text-[9px] font-black tracking-[0.15em] uppercase"
                :class="statusColor"
            >
                {{ isVerified ? 'Sello de Autenticidad' : 'Firma No Válida' }}
                <PhCertificate v-if="isVerified" :size="10" />
            </span>
            <span
                v-if="isVerified && signedAt"
                class="mt-0.5 text-[8px] font-bold tracking-tighter text-white/30 uppercase"
            >
                {{ version }} • CERBERO VERIFIED •
                {{ new Date(signedAt).toLocaleDateString() }}
            </span>
            <span
                v-else-if="!isVerified"
                class="mt-0.5 text-[8px] font-bold tracking-tighter text-amber-500/50 uppercase"
            >
                Integridad No Garantizada
            </span>
        </div>

        <v-tooltip
            activator="parent"
            location="top"
            transition="slide-y-transition"
        >
            <div
                class="st-glass-card max-w-xs rounded-xl border border-white/10 bg-slate-900/90 p-3 shadow-2xl backdrop-blur-xl"
            >
                <div class="mb-2 flex items-center gap-2">
                    <PhCertificate
                        :size="16"
                        weight="fill"
                        class="text-indigo-400"
                    />
                    <span
                        class="text-[10px] font-black tracking-widest text-white uppercase"
                        >Protocolo de Verificación</span
                    >
                </div>
                <p
                    class="text-[10px] leading-relaxed font-medium text-white/50"
                >
                    {{
                        isVerified
                            ? 'Este activo de ingeniería de talento ha sido sellado criptográficamente. Cualquier alteración manual en la base de datos invalidará este sello de origen original.'
                            : 'Atención: Este activo ha sido modificado manualmente fuera del motor Cerbero o el hash de integridad ha expirado.'
                    }}
                </p>
                <div
                    v-if="isVerified"
                    class="mt-2 border-t border-white/5 pt-2 font-mono text-[8px] break-all text-indigo-300/50"
                >
                    SENTINEL-SIG-HASH: Verified
                </div>
            </div>
        </v-tooltip>
    </div>
</template>

<style scoped>
.st-glass-card {
    box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.5);
}
</style>

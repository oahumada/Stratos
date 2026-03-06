<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    PhBrain,
    PhChatTeardropDots,
    PhCheckCircle,
    PhWarning,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import StBadgeGlass from '../../components/StBadgeGlass.vue';
import StCardGlass from '../../components/StCardGlass.vue';

const pulses = ref<any[]>([]);
const loading = ref(true);

const fetchPulses = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(
            '/api/people-experience/employee-pulses',
        );
        pulses.value = data.data;
    } catch (e) {
        console.error('Error fetching pulses:', e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchPulses();
});

const highRiskPulses = computed(() => {
    return pulses.value.filter((p) => p.ai_turnover_risk === 'high');
});

const averageEnps = computed(() => {
    if (pulses.value.length === 0) return 0;
    const total = pulses.value.reduce((acc, p) => acc + (p.e_nps || 0), 0);
    return (total / pulses.value.length).toFixed(1);
});

const getRiskVariant = (risk: string) => {
    switch (risk) {
        case 'high':
            return 'error';
        case 'medium':
            return 'warning';
        default:
            return 'success';
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 p-8 text-white">
        <Head title="Comando Px - Alerta Temprana" />

        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12 flex items-center justify-between">
                <div>
                    <h1
                        class="flex items-center gap-4 text-4xl font-black tracking-tight text-white"
                    >
                        <PhBrain
                            :size="40"
                            class="text-indigo-400"
                            weight="duotone"
                        />
                        Comando
                        <span
                            class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent"
                            >People Experience</span
                        >
                    </h1>
                    <p class="mt-2 font-medium text-white/40">
                        Panel de Control de Salud Organizacional y Alerta
                        Temprana de IA.
                    </p>
                </div>

                <div class="flex gap-4">
                    <div
                        class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-center backdrop-blur-xl"
                    >
                        <div
                            class="mb-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            eNPS Promedio
                        </div>
                        <div class="text-2xl font-black text-emerald-400">
                            {{ averageEnps }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats & Critical Alerts -->
            <div class="mb-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Critical Alerts Card -->
                <StCardGlass
                    class="border-rose-500/20 bg-rose-500/5 p-8 lg:col-span-1"
                >
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/20 text-rose-400"
                        >
                            <PhWarning :size="24" weight="fill" />
                        </div>
                        <h2 class="text-xl font-bold">Riesgo Crítico</h2>
                    </div>

                    <div v-if="highRiskPulses.length > 0" class="space-y-4">
                        <div
                            v-for="pulse in highRiskPulses"
                            :key="pulse.id"
                            class="group rounded-2xl border border-white/10 bg-white/5 p-4 transition-all hover:border-rose-500/30"
                        >
                            <div class="mb-2 flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500/20 text-xs font-bold text-indigo-300"
                                >
                                    {{
                                        (
                                            pulse.person?.first_name || '?'
                                        ).charAt(0)
                                    }}
                                </div>
                                <span
                                    class="font-bold text-white transition-colors group-hover:text-rose-300"
                                    >{{ pulse.person?.first_name }}
                                    {{ pulse.person?.last_name }}</span
                                >
                            </div>
                            <p
                                class="line-clamp-2 text-xs text-white/40 italic"
                            >
                                "{{ pulse.ai_turnover_reason }}"
                            </p>
                        </div>
                    </div>
                    <div v-else class="py-8 text-center">
                        <PhCheckCircle
                            :size="48"
                            class="mx-auto mb-4 text-emerald-500/20"
                        />
                        <p class="text-sm text-white/30">
                            No se detectan riesgos críticos por el momento.
                        </p>
                    </div>
                </StCardGlass>

                <!-- Activity Feed -->
                <StCardGlass class="p-8 lg:col-span-2">
                    <div class="mb-8 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-400"
                            >
                                <PhChatTeardropDots
                                    :size="24"
                                    weight="duotone"
                                />
                            </div>
                            <h2 class="text-xl font-bold">
                                Feedback en Tiempo Real
                            </h2>
                        </div>
                        <button
                            class="text-xs font-black tracking-widest text-white/30 uppercase transition-colors hover:text-white"
                            @click="fetchPulses"
                        >
                            Actualizar
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="border-b border-white/5 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    <th class="px-2 pb-4">Colaborador</th>
                                    <th class="px-2 pb-4">Pulso (eNPS)</th>
                                    <th class="px-2 pb-4">Estrés</th>
                                    <th class="px-2 pb-4">IA Predictor</th>
                                    <th class="px-2 pb-4 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="pulse in pulses"
                                    :key="pulse.id"
                                    class="group border-b border-white/5 transition-all hover:bg-white/[0.02]"
                                >
                                    <td class="px-2 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/5 text-xs font-bold"
                                            >
                                                {{
                                                    (
                                                        pulse.person
                                                            ?.first_name || '?'
                                                    ).charAt(0)
                                                }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold">
                                                    {{
                                                        pulse.person?.first_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[10px] text-white/30"
                                                >
                                                    {{
                                                        pulse.person?.role_name
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-2 w-12 overflow-hidden rounded-full bg-white/5"
                                            >
                                                <div
                                                    class="h-full bg-emerald-500"
                                                    :style="{
                                                        width:
                                                            pulse.e_nps * 10 +
                                                            '%',
                                                    }"
                                                ></div>
                                            </div>
                                            <span class="font-bold">{{
                                                pulse.e_nps
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-4">
                                        <StBadgeGlass
                                            :variant="
                                                pulse.stress_level > 3
                                                    ? 'error'
                                                    : 'glass'
                                            "
                                            size="sm"
                                        >
                                            {{ pulse.stress_level }}/5
                                        </StBadgeGlass>
                                    </td>
                                    <td class="px-2 py-4">
                                        <StBadgeGlass
                                            :variant="
                                                getRiskVariant(
                                                    pulse.ai_turnover_risk,
                                                )
                                            "
                                            size="sm"
                                        >
                                            {{
                                                pulse.ai_turnover_risk ||
                                                'Analizando...'
                                            }}
                                        </StBadgeGlass>
                                    </td>
                                    <td
                                        class="px-2 py-4 text-right text-xs text-white/30"
                                    >
                                        {{
                                            new Date(
                                                pulse.created_at,
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>

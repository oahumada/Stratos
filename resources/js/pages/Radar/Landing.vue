<template>
    <div class="radar-landing mx-auto max-w-5xl px-4">
        <GroupHeader
            :title="t('landings.radar.title')"
            :slogan="t('landings.radar.slogan')"
            :icon="PhTarget"
            gradient-variant="rose"
        />

        <!-- Agent Strategic Inquiry Section -->
        <div class="mb-10">
            <div
                class="relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl"
            >
                <div
                    class="relative z-10 grid grid-cols-1 gap-8 md:grid-cols-3"
                >
                    <div class="md:col-span-2">
                        <div class="mb-2 flex items-center gap-2">
                            <v-icon
                                icon="mdi-robot-outline"
                                color="indigo-400"
                            />
                            <span
                                class="text-xs font-black tracking-widest text-indigo-400 uppercase"
                            >
                                Stratos Neural Strategy
                            </span>
                        </div>
                        <h2 class="mb-3 text-2xl font-black text-white">
                            {{ t('landings.radar.agent_question.title') }}
                        </h2>
                        <p class="mb-6 text-sm text-white/60">
                            {{ t('landings.radar.agent_question.description') }}
                        </p>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input
                                v-model="agentQuestion"
                                type="text"
                                :placeholder="
                                    t(
                                        'landings.radar.agent_question.placeholder',
                                    )
                                "
                                class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/20 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/50 focus:outline-none"
                                @keyup.enter="launchAgentSimulation"
                            />
                            <StButtonGlass
                                variant="primary"
                                class="px-8!"
                                :loading="isSimulating"
                                @click="launchAgentSimulation"
                                :icon="PhRocketLaunch"
                            >
                                {{ t('landings.radar.agent_question.action') }}
                            </StButtonGlass>
                        </div>
                    </div>

                    <div class="hidden items-center justify-center md:flex">
                        <div
                            class="flex h-32 w-32 animate-pulse items-center justify-center rounded-full bg-indigo-500/10 ring-1 ring-white/10"
                        >
                            <PhBrain
                                :size="64"
                                weight="duotone"
                                class="text-indigo-400"
                            />
                        </div>
                    </div>
                </div>

                <!-- Abstract Background Elements -->
                <div
                    class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-purple-500/10 blur-[80px]"
                ></div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="mod in modules" :key="mod.title">
                <ModuleCard
                    :title="mod.title"
                    :description="mod.description"
                    :icon="mod.icon"
                    :href="mod.href"
                    :iconColor="mod.iconColor"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import GroupHeader from '@/components/Landing/GroupHeader.vue';
import ModuleCard from '@/components/Landing/ModuleCard.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import { router } from '@inertiajs/vue3';
import {
    PhBrain,
    PhChartLineUp,
    PhClipboardText,
    PhRocketLaunch,
    PhShieldCheck,
    PhTarget,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const agentQuestion = ref('');
const isSimulating = ref(false);

const launchAgentSimulation = () => {
    if (!agentQuestion.value.trim()) return;

    isSimulating.value = true;

    // Simulate thinking before redirecting (logic remains for future Materialization)
    setTimeout(() => {
        router.visit('/scenario-planning', {
            data: {
                ai_prompt: agentQuestion.value,
                mode: 'agentic_init',
            },
        });
    }, 1500);
};

const impactSummary = ref<any>(null);

const fetchImpactSummary = async () => {
    try {
        const response = await axios.get('/api/investor/impact-summary');
        impactSummary.value = response.data;
    } catch (e) {
        console.error('Error fetching impact summary:', e);
    }
};

onMounted(() => {
    fetchImpactSummary();
});

const modules = computed(() => [
    {
        title: t('landings.radar.modules.strategic_scenario_list.title'),
        description: t(
            'landings.radar.modules.strategic_scenario_list.description',
        ),
        icon: PhClipboardText,
        href: '/scenario-planning',
        iconColor: 'text-indigo-300',
    },
    {
        title: 'Impact Analytics (HCVA)',
        description: impactSummary.value
            ? `HCVA Actual: $${Math.round(impactSummary.value.hcva_average).toLocaleString()} USD`
            : 'Calculando eficiencia del capital humano en tiempo real...',
        icon: PhChartLineUp,
        href: '/dashboard/investor',
        iconColor: 'text-emerald-300',
    },
    {
        title: t('landings.radar.modules.executive_dashboard.title'),
        description: t(
            'landings.radar.modules.executive_dashboard.description',
        ),
        icon: PhShieldCheck,
        href: '/dashboard/analytics',
        iconColor: 'text-amber-300',
    },
    {
        title: t('landings.radar.modules.gap_analysis_engine.title'),
        description: t(
            'landings.radar.modules.gap_analysis_engine.description',
        ),
        icon: PhChartLineUp,
        href: '/gap-analysis',
        iconColor: 'text-fuchsia-300',
    },
    {
        title: 'Riesgo de Reemplazo',
        description: impactSummary.value
            ? `Costo potencial: $${Math.round(impactSummary.value.total_replacement_risk_usd).toLocaleString()} USD`
            : 'Escaneando vulnerabilidades de rotación...',
        icon: PhTarget,
        href: '/dashboard/investor',
        iconColor: 'text-rose-300',
    },
]);
</script>

<style scoped>
.radar-landing {
    padding: 32px 0;
}
</style>

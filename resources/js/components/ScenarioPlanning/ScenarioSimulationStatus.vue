<template>
    <div
        class="simulation-status-panel flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#0f172a]/80 pt-0 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] backdrop-blur-xl"
        v-if="visible"
    >
        <!-- Header -->
        <div
            class="flex items-center border-b border-white/5 bg-gradient-to-r from-indigo-500/10 to-transparent p-4"
        >
            <div
                class="mr-3 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-orange-500/30 bg-orange-500/20 shadow-[0_0_15px_rgba(249,115,22,0.2)]"
            >
                <v-icon
                    icon="mdi-molecule"
                    color="orange-400"
                    size="20"
                ></v-icon>
            </div>
            <div class="flex-grow">
                <div
                    class="mb-0.5 text-[9px] font-black tracking-widest text-indigo-400/80 uppercase"
                >
                    {{ $t('scenario_iq') }}
                </div>
                <div class="text-sm font-bold text-white">
                    {{ $t('active_simulation') }}
                </div>
            </div>
            <StBadgeGlass
                variant="success"
                size="sm"
                class="animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.3)]"
            >
                {{ $t('ai_analyzing') }}
            </StBadgeGlass>
        </div>

        <div class="space-y-4 p-4">
            <!-- Metrics Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="rounded-xl border border-white/5 bg-white/5 p-3 text-center transition-colors hover:bg-white/10"
                >
                    <div
                        class="mb-1 text-[10px] font-bold tracking-wider text-white/50 uppercase"
                    >
                        {{ $t('success_prob') }}
                    </div>
                    <div class="text-xl font-black text-cyan-400">
                        {{ metrics.success_probability }}%
                    </div>
                </div>
                <div
                    class="rounded-xl border border-white/5 bg-white/5 p-3 text-center transition-colors hover:bg-white/10"
                >
                    <div
                        class="mb-1 text-[10px] font-bold tracking-wider text-white/50 uppercase"
                    >
                        {{ $t('est_synergy') }}
                    </div>
                    <div class="text-xl font-black text-emerald-400">
                        {{ metrics.synergy_score }}/10
                    </div>
                </div>
                <div
                    class="rounded-xl border border-white/5 bg-white/5 p-3 text-center transition-colors hover:bg-white/10"
                >
                    <div
                        class="mb-1 text-[10px] font-bold tracking-wider text-white/50 uppercase"
                    >
                        {{ $t('cultural_friction') }}
                    </div>
                    <div class="text-xl font-black text-orange-400">
                        {{ metrics.cultural_friction }}%
                    </div>
                </div>
                <div
                    class="rounded-xl border border-white/5 bg-white/5 p-3 text-center transition-colors hover:bg-white/10"
                >
                    <div
                        class="mb-1 text-[10px] font-bold tracking-wider text-white/50 uppercase"
                    >
                        {{ $t('time_to_peak') }}
                    </div>
                    <div class="text-xl font-black text-purple-400">
                        {{ metrics.time_to_peak }}m
                    </div>
                </div>
            </div>

            <!-- Organic Simulator Insight -->
            <div>
                <div
                    class="mb-2 text-[10px] font-bold tracking-widest text-white/50 uppercase"
                >
                    Organic Simulator Insight:
                </div>
                <div
                    class="border-l-2 border-indigo-500/50 pl-3 text-xs leading-relaxed text-white/80 italic"
                >
                    "The proposed structure presents a high coupling at the
                    {{ metrics.key_node }} node. I recommend strengthening the
                    capabilities of {{ metrics.recommendation }} to mitigate
                    execution risks."
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2 border-t border-white/5 pt-2">
                <StButtonGlass
                    variant="ghost"
                    class="w-full justify-center"
                    icon="mdi-refresh"
                    @click="$emit('re-run')"
                >
                    {{ $t('recalculate') }}
                </StButtonGlass>

                <StButtonGlass
                    variant="primary"
                    class="w-full justify-center !border-pink-500/50 !bg-pink-600/20 text-pink-100 shadow-[0_0_15px_rgba(236,72,153,0.2)] hover:!bg-pink-600/30"
                    icon="mdi-shield-check"
                    :loading="mitigating"
                    @click="getMitigationPlan"
                >
                    {{ $t('generate_remediation') }}
                </StButtonGlass>
            </div>

            <!-- Remediation Plan -->
            <div
                v-if="mitigationPlan"
                class="mt-2 animate-in duration-500 fade-in slide-in-from-top-2"
            >
                <div class="border-t border-white/5 pt-4">
                    <div
                        class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest text-pink-400 uppercase"
                    >
                        <v-icon
                            icon="mdi-shield-star"
                            size="14"
                            color="pink-400"
                        />
                        {{ $t('sentinel_plan') }}
                    </div>
                    <div class="space-y-2">
                        <div
                            v-for="(action, i) in mitigationPlan.actions"
                            :key="i"
                            class="flex items-start gap-2 rounded-lg border border-pink-500/10 bg-pink-500/5 p-2.5 text-xs leading-relaxed text-white/80"
                        >
                            <span class="mt-0.5 text-pink-400">•</span>
                            <span>{{ action }}</span>
                        </div>
                    </div>
                    <div
                        class="mt-3 flex items-start gap-2 rounded-xl border border-cyan-500/20 bg-cyan-500/10 p-3 text-xs text-cyan-100/90"
                    >
                        <v-icon
                            icon="mdi-school"
                            size="16"
                            class="mt-0.5 shrink-0"
                            color="cyan-400"
                        ></v-icon>
                        <div>
                            <strong class="font-bold text-cyan-300">{{
                                $t('training_target')
                            }}</strong>
                            {{ mitigationPlan.training }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
    visible: { type: Boolean, default: true },
    scenarioId: { type: [Number, String], required: true },
    metrics: {
        type: Object,
        default: () => ({
            success_probability: 78,
            synergy_score: 8.4,
            cultural_friction: 12,
            time_to_peak: 6,
            key_node: 'Data Architecture',
            recommendation: 'MLOps & Governance',
        }),
    },
});

defineEmits(['re-run']);

const mitigating = ref(false);
const mitigationPlan = ref<{
    actions: string[];
    training: string;
    security_insight: string;
} | null>(null);

const getMitigationPlan = async () => {
    mitigating.value = true;
    try {
        const response = await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/mitigate`,
            {
                metrics: props.metrics,
            },
        );
        mitigationPlan.value = response.data.plan;
    } catch (error) {
        console.error('Error computing mitigation:', error);
    } finally {
        mitigating.value = false;
    }
};
</script>

<style scoped>
.simulation-status-panel {
    position: absolute;
    bottom: 24px;
    right: 24px;
    width: 360px;
    z-index: 100;
}
</style>

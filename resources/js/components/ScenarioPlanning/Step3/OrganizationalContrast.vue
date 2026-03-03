<template>
    <div class="organizational-contrast space-y-8 pb-12">
        <!-- Header: The Reality Check -->
        <div
            class="contrast-header animate-in duration-700 fade-in slide-in-from-top-4"
        >
            <div class="flex items-start gap-5">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                >
                    <v-icon
                        icon="mdi-scale-balance"
                        color="indigo-300"
                        size="24"
                    />
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-white">
                        Reality <span class="text-indigo-400">Contrast</span>
                    </h2>
                    <p class="mt-1 text-sm font-medium text-white/40">
                        Comparing strategic architecture (To-Be) with
                        organizational inventory (As-Is).
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Layout -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <!-- Strategic Gaps Matrix: Full Width -->
            <div class="lg:col-span-12">
                <StCardGlass
                    variant="glass"
                    border-accent="indigo"
                    class="overflow-hidden p-0!"
                    :no-hover="true"
                >
                    <div
                        class="flex items-center gap-3 border-b border-white/5 bg-white/5 px-6 py-4"
                    >
                        <v-icon color="indigo-400" size="20">mdi-matrix</v-icon>
                        <h4
                            class="text-xs font-black tracking-[0.2em] text-white/60 uppercase"
                        >
                            Skill Scarcity Mapping
                        </h4>
                    </div>
                    <div class="p-0">
                        <SkillGapsMatrix :scenario-id="props.scenarioId" />
                    </div>
                </StCardGlass>
            </div>

            <!-- Bandwidth Evolution: Left Column -->
            <div class="lg:col-span-12 xl:col-span-7">
                <StCardGlass
                    variant="glass"
                    class="h-full overflow-hidden p-0!"
                    :no-hover="true"
                >
                    <div
                        class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4"
                    >
                        <v-icon color="indigo-400" size="20"
                            >mdi-chart-areaspline</v-icon
                        >
                        <h4
                            class="text-xs font-black tracking-[0.2em] text-white/60 uppercase"
                        >
                            FTE Evolution Vector
                        </h4>
                    </div>
                    <div class="p-0">
                        <RoleForecastsTable :scenario-id="props.scenarioId" />
                    </div>
                </StCardGlass>
            </div>

            <!-- Talent Alignment: Right Column -->
            <div class="lg:col-span-12 xl:col-span-5">
                <StCardGlass
                    variant="glass"
                    class="h-full overflow-hidden p-0!"
                    :no-hover="true"
                >
                    <div
                        class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4"
                    >
                        <v-icon color="indigo-400" size="20"
                            >mdi-account-search</v-icon
                        >
                        <h4
                            class="text-xs font-black tracking-[0.2em] text-white/60 uppercase"
                        >
                            Internal DNA Match
                        </h4>
                    </div>
                    <div class="p-6">
                        <MatchingResults :scenario-id="props.scenarioId" />
                    </div>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import MatchingResults from '../Step2/MatchingResults.vue';
import RoleForecastsTable from '../Step2/RoleForecastsTable.vue';
import SkillGapsMatrix from '../Step2/SkillGapsMatrix.vue';

const props = defineProps<{
    scenarioId: number;
}>();
</script>

<style scoped>
.organizational-contrast {
    width: 100%;
}
</style>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { computed } from 'vue';

const props = defineProps<{
    scenario: any;
}>();

const metadata = computed(() => {
    // If scenario has source_generation, use its prompt_payload
    const gen = props.scenario?.source_generation;
    if (gen && gen.prompt_payload) {
        return typeof gen.prompt_payload === 'string'
            ? JSON.parse(gen.prompt_payload)
            : gen.prompt_payload;
    }
    // Fallback to scenario itself if it has these fields
    return {
        company_name: props.scenario?.company_name || 'N/A',
        industry: props.scenario?.industry || 'N/A',
        company_size: props.scenario?.company_size || 'N/A',
        strategic_goal:
            props.scenario?.strategic_goal ||
            props.scenario?.description ||
            'N/A',
        time_horizon: props.scenario?.time_horizon || 'N/A',
        current_challenges: props.scenario?.current_challenges || 'N/A',
        current_capabilities: props.scenario?.current_capabilities || 'N/A',
        current_gaps: props.scenario?.current_gaps || 'N/A',
        budget_level: props.scenario?.budget_level || 'N/A',
        talent_availability: props.scenario?.talent_availability || 'N/A',
        milestones: props.scenario?.milestones || 'N/A',
    };
});

const assumptionGroups = [
    {
        title: 'Identity & Context',
        icon: 'mdi-office-building-outline',
        fields: [
            { label: 'Company', key: 'company_name' },
            { label: 'Industry', key: 'industry' },
            { label: 'Size', key: 'company_size' },
        ],
    },
    {
        title: 'Strategic Intent',
        icon: 'mdi-target',
        fields: [
            { label: 'Goal', key: 'strategic_goal' },
            { label: 'Horizon', key: 'time_horizon' },
            { label: 'Budget', key: 'budget_level' },
        ],
    },
    {
        title: 'Current State',
        icon: 'mdi-gauge',
        fields: [
            { label: 'Challenges', key: 'current_challenges' },
            { label: 'Existing Capabilities', key: 'current_capabilities' },
            { label: 'Identified Gaps', key: 'current_gaps' },
        ],
    },
];
</script>

<template>
    <StCardGlass
        variant="glass"
        border-accent="indigo"
        class="scenario-assumptions-card overflow-hidden bg-white/2! p-0!"
    >
        <div
            class="flex items-center justify-between border-b border-white/5 bg-indigo-500/5 px-8 py-5"
        >
            <div class="flex items-center gap-3">
                <v-icon color="indigo-300" size="20"
                    >mdi-clipboard-text-search-outline</v-icon
                >
                <h2
                    class="text-sm font-black tracking-widest text-white uppercase"
                >
                    Strategic Assumptions & Foundation
                </h2>
            </div>
            <StBadgeGlass
                variant="glass"
                size="sm"
                class="text-[9px] tracking-[0.2em] uppercase"
                >RO - Prototype Only</StBadgeGlass
            >
        </div>

        <div class="grid grid-cols-1 gap-px bg-white/5 md:grid-cols-3">
            <div
                v-for="group in assumptionGroups"
                :key="group.title"
                class="bg-[#020617]/40 p-6 transition-colors hover:bg-white/5"
            >
                <div class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-indigo-500/20 bg-indigo-500/10"
                    >
                        <v-icon
                            :icon="group.icon"
                            size="16"
                            color="indigo-300"
                        />
                    </div>
                    <h4
                        class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                    >
                        {{ group.title }}
                    </h4>
                </div>

                <div class="space-y-6">
                    <div
                        v-for="field in group.fields"
                        :key="field.key"
                        class="assumption-item"
                    >
                        <span
                            class="mb-1.5 block text-[9px] font-black tracking-widest text-white/20 uppercase"
                            >{{ field.label }}</span
                        >
                        <div
                            class="text-[11px] leading-relaxed font-medium text-white/70"
                        >
                            {{
                                metadata[field.key] ||
                                'Not specified during generation'
                            }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Milestones Footer -->
        <div
            v-if="metadata.milestones"
            class="border-t border-white/5 bg-black/20 p-6 px-8"
        >
            <span
                class="mb-2 block text-[9px] font-black tracking-widest text-white/20 uppercase"
                >Strategic Milestones</span
            >
            <div class="text-[11px] font-medium text-indigo-300/80 italic">
                {{ metadata.milestones }}
            </div>
        </div>
    </StCardGlass>
</template>

<style scoped>
.scenario-assumptions-card {
    transition: all 0.3s ease;
}
.assumption-item {
    position: relative;
    padding-left: 0;
}
</style>

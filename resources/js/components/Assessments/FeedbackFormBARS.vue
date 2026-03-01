<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { ref, watch } from 'vue';

const props = defineProps<{
    skill: any;
    levels: any[];
    question?: string;
    modelValue: number | null;
    evidence?: string;
    confidence?: number;
}>();

const emit = defineEmits([
    'update:modelValue',
    'update:evidence',
    'update:confidence',
]);

const selectedLevel = ref(props.modelValue);
const evidenceUrl = ref(props.evidence || '');
const confidenceLevel = ref(props.confidence || 80);

watch(selectedLevel, (val) => emit('update:modelValue', val));
watch(evidenceUrl, (val) => emit('update:evidence', val));
watch(confidenceLevel, (val) => emit('update:confidence', val));

const getLevelColor = (level: number) => {
    if (level === 1) return '#ef4444'; // Red 500
    if (level === 2) return '#f59e0b'; // Amber 500
    if (level === 3) return '#6b7280'; // Gray 500
    if (level === 4) return '#10b981'; // Emerald 500
    if (level === 5) return '#6366f1'; // Indigo 500
    return '#6b7280';
};
</script>

<template>
    <StCardGlass class="mb-6 border-white/10 bg-white/5" :no-hover="true">
        <div class="d-flex align-center px-8 pt-8 pb-4">
            <div class="flex items-center gap-3">
                <v-icon color="indigo-400">mdi-star-circle-outline</v-icon>
                <h3 class="text-h6 font-weight-black tracking-tight text-white">
                    {{ skill.name }}
                </h3>
            </div>
            <v-spacer></v-spacer>
            <StBadgeGlass
                v-if="selectedLevel"
                variant="glass"
                size="md"
                class="px-4 py-1"
                :style="{
                    background: getLevelColor(selectedLevel) + '33',
                    color: getLevelColor(selectedLevel),
                    borderColor: getLevelColor(selectedLevel) + '44',
                }"
            >
                LEVEL {{ selectedLevel }}
            </StBadgeGlass>
        </div>

        <div class="px-8 pt-2 pb-6">
            <div
                class="text-body-1 mb-8 max-w-3xl leading-relaxed text-white/50"
            >
                {{
                    question ||
                    'Evaluate the level of competence observed in recent behaviors and professional impact.'
                }}
            </div>

            <!-- BARS Selector -->
            <v-slide-group
                show-arrows
                center-active
                class="pa-4 overflow-hidden rounded-2xl border border-white/5 bg-white/5"
            >
                <v-slide-group-item
                    v-for="level in levels"
                    :key="level.id"
                    v-slot="{ isSelected, toggle }"
                >
                    <div
                        class="ma-3 pa-4 flex min-h-[160px] w-[150px] cursor-pointer flex-col items-center justify-center rounded-2xl border text-center transition-all duration-300"
                        :class="[
                            isSelected
                                ? 'z-10 scale-105 border-indigo-500 bg-indigo-600 shadow-xl shadow-indigo-900/20'
                                : 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/10',
                        ]"
                        @click="
                            () => {
                                selectedLevel = level.level;
                                toggle();
                            }
                        "
                    >
                        <div
                            class="text-h4 font-weight-black mb-2"
                            :class="
                                isSelected ? 'text-white' : 'text-indigo-400'
                            "
                        >
                            {{ level.level }}
                        </div>
                        <div
                            class="text-caption font-weight-black mb-2 line-clamp-2 leading-tight tracking-tighter uppercase"
                            :class="isSelected ? 'text-white' : 'text-white/70'"
                        >
                            {{ level.name }}
                        </div>
                        <div
                            class="line-clamp-3 text-[11px] leading-snug"
                            :class="
                                isSelected ? 'text-white/80' : 'text-white/40'
                            "
                        >
                            {{ level.description }}
                        </div>
                    </div>
                </v-slide-group-item>
            </v-slide-group>

            <!-- Evidence Requirement (Conditional) -->
            <v-expand-transition>
                <div
                    v-if="
                        selectedLevel &&
                        (selectedLevel === 1 || selectedLevel === 5)
                    "
                    class="mt-8"
                >
                    <div
                        class="pa-4 mb-4 flex items-center gap-3 rounded-xl border border-indigo-500/20 bg-indigo-500/10"
                    >
                        <v-icon color="indigo-400" size="small"
                            >mdi-information-outline</v-icon
                        >
                        <div class="text-body-2 font-medium text-indigo-100/70">
                            Extreme validation: Level
                            {{ selectedLevel }} requires mandatory evidence or
                            documented rationale.
                        </div>
                    </div>
                    <v-text-field
                        v-model="evidenceUrl"
                        label="Source URL / Evidence Rationale"
                        prepend-inner-icon="mdi-link"
                        variant="outlined"
                        class="glass-input-premium"
                        persistent-placeholder
                        :rules="[
                            (v) =>
                                !!v ||
                                'Evidence is mandatory for level ' +
                                    selectedLevel,
                        ]"
                    ></v-text-field>
                </div>
            </v-expand-transition>

            <!-- Confidence Slider -->
            <div
                class="mt-8 flex flex-col gap-6 border-t border-white/5 pt-6 sm:flex-row sm:items-center"
            >
                <div
                    class="text-caption font-weight-black tracking-widest text-white/40 uppercase"
                >
                    Confidence Index:
                </div>
                <div class="flex flex-grow items-center gap-6">
                    <v-slider
                        v-model="confidenceLevel"
                        min="0"
                        max="100"
                        step="10"
                        thumb-label="always"
                        hide-details
                        color="indigo-500"
                        track-color="white/10"
                        class="max-w-md"
                    ></v-slider>
                    <div
                        class="text-h6 font-weight-black w-12 text-center text-white"
                    >
                        {{ confidenceLevel }}%
                    </div>
                </div>
            </div>
        </div>
    </StCardGlass>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-clamp: 2;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    line-clamp: 3;
    overflow: hidden;
}

.glass-input-premium :deep(.v-field) {
    background: rgba(255, 255, 255, 0.03) !important;
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    transition: all 0.3s ease;
}

.glass-input-premium :deep(.v-field--focused) {
    border-color: rgba(99, 102, 241, 0.4) !important;
    background: rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
}
</style>

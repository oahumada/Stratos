<script setup lang="ts">
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    skill: any; // { id, name, description }
    levels: any[]; // [{ level, name, description, key_behaviors }]
    question?: string;
    modelValue: number | null; // Selected level
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

const selectedLevelDetails = computed(() => {
    return props.levels.find((l) => l.level === selectedLevel.value);
});

const getLevelColor = (level: number) => {
    if (level === 1) return 'grey';
    if (level === 2) return 'blue-grey';
    if (level === 3) return 'blue';
    if (level === 4) return 'teal';
    if (level === 5) return 'green';
    return 'grey';
};
</script>

<template>
    <v-card variant="outlined" class="mb-4">
        <v-card-title
            class="text-subtitle-1 font-weight-bold d-flex align-center"
        >
            <v-icon start color="primary">mdi-star-circle-outline</v-icon>
            {{ skill.name }}
            <v-spacer></v-spacer>
            <v-chip
                v-if="selectedLevel"
                :color="getLevelColor(selectedLevel)"
                size="small"
            >
                Nivel {{ selectedLevel }}
            </v-chip>
        </v-card-title>

        <v-card-text>
            <div class="text-body-2 text-grey-darken-1 mb-4">
                {{
                    question ||
                    'Evalúe el nivel de competencia observado en comportamientos recientes.'
                }}
            </div>

            <!-- BARS Selector -->
            <v-slide-group
                show-arrows
                center-active
                class="pa-2 bg-grey-lighten-5 rounded"
            >
                <v-slide-group-item
                    v-for="level in levels"
                    :key="level.id"
                    v-slot="{ isSelected, toggle }"
                >
                    <v-card
                        :color="
                            isSelected ? getLevelColor(level.level) : 'white'
                        "
                        :variant="isSelected ? 'flat' : 'outlined'"
                        class="ma-2 pa-2 d-flex flex-column align-center transition-swing cursor-pointer text-center"
                        height="140"
                        width="140"
                        @click="
                            () => {
                                selectedLevel = level.level;
                                toggle();
                            }
                        "
                    >
                        <div class="text-h6 font-weight-bold mb-1">
                            {{ level.level }}
                        </div>
                        <div
                            class="text-caption font-weight-bold mb-1 line-clamp-1"
                        >
                            {{ level.name }}
                        </div>
                        <div
                            class="text-caption text-grey-darken-1 line-clamp-3"
                            :class="{ 'text-white': isSelected }"
                        >
                            {{ level.description }}
                        </div>
                    </v-card>
                </v-slide-group-item>
            </v-slide-group>

            <!-- Evidence Requirement (Conditional) -->
            <v-expand-transition>
                <div
                    v-if="
                        selectedLevel &&
                        (selectedLevel === 1 || selectedLevel === 5)
                    "
                    class="mt-4"
                >
                    <v-alert
                        type="info"
                        variant="tonal"
                        density="compact"
                        class="mb-2"
                        icon="mdi-information-outline"
                    >
                        Para niveles extremos (1 o 5), se requiere evidencia de
                        soporte (link a documento, jira, etc).
                    </v-alert>
                    <v-text-field
                        v-model="evidenceUrl"
                        label="Link de Evidencia / Justificación"
                        prepend-inner-icon="mdi-link"
                        variant="outlined"
                        density="compact"
                        :rules="[
                            (v) =>
                                !!v ||
                                'Evidencia requerida para nivel ' +
                                    selectedLevel,
                        ]"
                    ></v-text-field>
                </div>
            </v-expand-transition>

            <!-- Confidence Slider -->
            <div class="d-flex align-center mt-4 gap-4">
                <div class="text-caption text-grey">Nivel de Confianza:</div>
                <v-slider
                    v-model="confidenceLevel"
                    min="0"
                    max="100"
                    step="10"
                    thumb-label
                    density="compact"
                    hide-details
                    color="primary"
                    style="max-width: 200px"
                ></v-slider>
                <div class="text-body-2 font-weight-bold">
                    {{ confidenceLevel }}%
                </div>
            </div>
        </v-card-text>
    </v-card>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.cursor-pointer {
    cursor: pointer;
}
.transition-swing {
    transition: 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}
</style>

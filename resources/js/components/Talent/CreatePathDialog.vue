<template>
    <v-dialog v-model="dialog" max-width="600">
        <template #activator="{ props: activatorProps }">
            <v-btn
                v-if="!hasActivator"
                v-bind="activatorProps"
                color="primary"
                prepend-icon="mdi-plus"
                variant="flat"
            >
                Nuevo Plan
            </v-btn>
            <!-- Hidden button to trigger from parent if needed -->
            <button
                v-show="false"
                id="open-create-path-dialog"
                v-bind="activatorProps"
            ></button>
        </template>

        <v-card>
            <v-card-title class="pa-4 text-h6">
                Generar Plan de Desarrollo
            </v-card-title>
            <v-card-text>
                <div class="text-body-2 text-grey-darken-1 mb-4">
                    Seleccione una skill con brecha para generar automáticamente
                    recomendaciones de formación, mentoría y proyectos (Modelo
                    70-20-10).
                </div>

                <v-form ref="form">
                    <v-select
                        v-model="selectedSkillId"
                        :items="skillOptions"
                        item-title="title"
                        item-value="id"
                        label="Seleccionar Skill"
                        placeholder="Elija una skill para mejorar"
                        variant="outlined"
                        :rules="[(v) => !!v || 'Debe seleccionar una skill']"
                    >
                        <template #item="{ props, item }">
                            <v-list-item
                                v-bind="props"
                                :subtitle="item.raw.subtitle"
                            >
                                <template #append>
                                    <v-chip
                                        size="x-small"
                                        :color="item.raw.color"
                                        variant="flat"
                                    >
                                        Gap: {{ item.raw.gap }}
                                    </v-chip>
                                </template>
                            </v-list-item>
                        </template>
                    </v-select>
                </v-form>

                <v-alert
                    v-if="selectedSkill"
                    type="info"
                    variant="tonal"
                    density="compact"
                    class="mt-2"
                >
                    <div class="font-weight-bold">Análisis de Brecha</div>
                    <div>Nivel Actual: {{ selectedSkill.current_level }}</div>
                    <div>
                        Nivel Requerido: {{ selectedSkill.required_level }}
                    </div>
                    <div class="mt-1">
                        Se generará un plan de
                        {{ selectedSkill.gap > 1 ? '6 meses' : '3 meses' }} de
                        duración estimada.
                    </div>
                </v-alert>
            </v-card-text>
            <v-card-actions class="pa-4">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="dialog = false">Cancelar</v-btn>
                <v-btn
                    color="primary"
                    variant="flat"
                    :loading="generating"
                    @click="generate"
                >
                    Generar Plan
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps({
    personId: {
        type: Number,
        required: true,
    },
    skills: {
        type: Array,
        default: () => [],
    },
    hasActivator: {
        type: Boolean,
        default: false, // If true, caller provides activator logic or calls open()
    },
});

const emit = defineEmits(['generated']);

const dialog = ref(false);
const generating = ref(false);
const selectedSkillId = ref(null);

const skillOptions = computed(() => {
    return props.skills
        .filter((s) => {
            const pivot = s.pivot || {};
            // Filter only where there is a gap (required > current)
            return (pivot.required_level || 0) > (pivot.current_level || 0);
        })
        .map((s) => {
            const current = s.pivot?.current_level || 0;
            const required = s.pivot?.required_level || 0;
            const gap = required - current;
            return {
                id: s.id,
                title: s.name,
                subtitle: `Nivel ${current} ➔ ${required}`,
                current_level: current,
                required_level: required,
                gap: gap,
                color: gap > 1 ? 'error' : 'warning',
            };
        });
});

const selectedSkill = computed(() => {
    return skillOptions.value.find((s) => s.id === selectedSkillId.value);
});

const open = () => {
    dialog.value = true;
};

const generate = async () => {
    if (!selectedSkillId.value) return;

    generating.value = true;
    try {
        const payload = {
            people_id: props.personId,
            skill_id: selectedSkillId.value,
            current_level: selectedSkill.value.current_level,
            target_level: selectedSkill.value.required_level,
        };

        await axios.post('/api/development-paths/generate', payload);

        emit('generated');
        dialog.value = false;
        selectedSkillId.value = null;
    } catch (e) {
        console.error('Error creating path', e);
        // Could emit error or show toast
    } finally {
        generating.value = false;
    }
};

defineExpose({ open });
</script>

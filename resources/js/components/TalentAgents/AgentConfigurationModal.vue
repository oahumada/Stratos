<template>
    <v-dialog
        :model-value="visible"
        max-width="700px"
        persistent
        @update:model-value="$emit('close')"
    >
        <v-card v-if="agent" class="overflow-hidden rounded-xl">
            <v-toolbar :class="getAgentHeaderClass(agent)" flat>
                <v-icon start class="ml-4">{{
                    getAgentIcon(agent.name)
                }}</v-icon>
                <v-toolbar-title class="font-weight-bold"
                    >Configurar {{ agent.name }}</v-toolbar-title
                >
                <v-spacer></v-spacer>
                <v-btn icon @click="$emit('close')">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pa-6">
                <v-form ref="form" v-model="isValid">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="editedAgent.name"
                                label="Nombre del Agente"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                class="mb-4"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="editedAgent.role_description"
                                label="Título/Rol"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                class="mb-4"
                                required
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-textarea
                                v-model="editedAgent.persona"
                                label="Persona (Comportamiento e Instrucciones Base)"
                                variant="outlined"
                                density="comfortable"
                                rows="4"
                                hide-details="auto"
                                class="mb-4"
                                required
                            ></v-textarea>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-select
                                v-model="editedAgent.provider"
                                :items="[
                                    'openai',
                                    'anthropic',
                                    'google',
                                    'local',
                                ]"
                                label="Proveedor"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                class="mb-4"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="editedAgent.model"
                                label="Modelo"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                class="mb-4"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <div class="text-subtitle-2 mb-2">Expertise</div>
                            <v-combobox
                                v-model="editedAgent.expertise_areas"
                                chips
                                multiple
                                label="Áreas de Especialidad"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                closable-chips
                            ></v-combobox>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions class="pa-4 bg-grey-lighten-5">
                <v-btn variant="text" @click="$emit('close')" class="text-none"
                    >Cancelar</v-btn
                >
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    variant="flat"
                    class="text-none rounded-lg px-6"
                    :loading="loading"
                    :disabled="!isValid"
                    @click="handleSave"
                >
                    Guardar Cambios
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    visible: boolean;
    agent: any;
    loading: boolean;
}>();

const emit = defineEmits(['close', 'save']);

const isValid = ref(true);
const editedAgent = ref<any>({});

watch(
    () => props.agent,
    (newVal) => {
        if (newVal) {
            editedAgent.value = JSON.parse(JSON.stringify(newVal));
        }
    },
    { immediate: true },
);

const handleSave = () => {
    emit('save', editedAgent.value);
};

const getAgentHeaderClass = (agent: any) => {
    if (agent.name.includes('Estratega'))
        return 'bg-blue-lighten-4 text-blue-darken-4';
    if (agent.name.includes('Diseñador'))
        return 'bg-blue-grey-lighten-4 text-blue-grey-darken-4';
    if (agent.name.includes('Curador'))
        return 'bg-indigo-lighten-4 text-indigo-darken-4';
    return 'bg-grey-lighten-3';
};

const getAgentIcon = (name: string) => {
    if (name.includes('Estratega')) return 'mdi-strategy';
    if (name.includes('Diseñador')) return 'mdi-vector-arrange-below';
    if (name.includes('Curador')) return 'mdi-book-open-page-variant-outline';
    return 'mdi-robot';
};
</script>

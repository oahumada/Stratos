<template>
    <v-dialog :model-value="visible" max-width="900" persistent>
        <v-card>
            <v-toolbar color="secondary" dark>
                <v-icon start class="ml-4">mdi-robot</v-icon>
                <v-toolbar-title
                    >Propuestas de Diseño de Talento (AI)</v-toolbar-title
                >
                <v-spacer></v-spacer>
                <v-btn icon @click="$emit('close')">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pa-6">
                <div v-if="loading" class="py-10 text-center">
                    <v-progress-circular
                        indeterminate
                        color="secondary"
                        size="64"
                    ></v-progress-circular>
                    <p class="text-h6 mt-4">Los agentes están colaborando...</p>
                </div>

                <div v-else-if="proposals">
                    <v-alert type="info" variant="tonal" class="mb-6">
                        El <strong>Diseñador de Roles</strong> y el
                        <strong>Curador de Competencias</strong> han analizado
                        tu estrategia y proponen los siguientes cambios al
                        catálogo.
                    </v-alert>

                    <!-- Roles Proposals -->
                    <h3 class="text-h6 align-center mb-4 flex">
                        <v-icon color="primary" class="mr-2"
                            >mdi-account-tie</v-icon
                        >
                        Propuestas de Roles
                    </h3>

                    <v-row>
                        <v-col
                            v-for="(role, idx) in proposals.role_proposals"
                            :key="idx"
                            cols="12"
                            md="6"
                        >
                            <v-card variant="outlined" class="h-100">
                                <v-card-item>
                                    <template #prepend>
                                        <v-chip
                                            :color="getChangeColor(role.type)"
                                            size="small"
                                            label
                                            class="mr-2"
                                        >
                                            {{ role.type.toUpperCase() }}
                                        </v-chip>
                                    </template>
                                    <v-card-title>{{
                                        role.proposed_name
                                    }}</v-card-title>
                                </v-card-item>
                                <v-card-text>
                                    <p class="text-body-2">
                                        {{ role.proposed_description }}
                                    </p>
                                    <div
                                        v-if="role.added_competencies"
                                        class="text-caption mt-2"
                                    >
                                        <strong>Competencias Sugeridas:</strong>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            <v-chip
                                                v-for="c in role.added_competencies"
                                                :key="c"
                                                size="x-small"
                                                variant="flat"
                                            >
                                                {{ c }}
                                            </v-chip>
                                        </div>
                                    </div>
                                    <div
                                        v-if="role.talent_composition"
                                        class="text-caption mt-2"
                                    >
                                        <strong>Leverage Humano:</strong>
                                        {{
                                            role.talent_composition
                                                .human_percentage
                                        }}%
                                    </div>
                                </v-card-text>
                                <v-divider></v-divider>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="success"
                                        prepend-icon="mdi-check"
                                        variant="text"
                                        >Aprobar</v-btn
                                    >
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>

                    <!-- Competencies Proposals -->
                    <h3 class="text-h6 align-center mt-8 mb-4 flex">
                        <v-icon color="teal" class="mr-2"
                            >mdi-certificate</v-icon
                        >
                        Propuestas de Catálogo de Competencias
                    </h3>

                    <v-row>
                        <v-col
                            v-for="(comp, idx) in proposals.catalog_proposals"
                            :key="idx"
                            cols="12"
                        >
                            <v-card
                                variant="outlined"
                                class="bg-grey-lighten-4"
                            >
                                <v-row no-gutters>
                                    <v-col
                                        cols="auto"
                                        class="pa-4 align-center flex"
                                    >
                                        <v-chip
                                            :color="getChangeColor(comp.type)"
                                            size="small"
                                            label
                                        >
                                            {{ comp.type.toUpperCase() }}
                                        </v-chip>
                                    </v-col>
                                    <v-col class="pa-4">
                                        <div
                                            class="text-subtitle-1 font-weight-bold"
                                        >
                                            {{ comp.proposed_name }}
                                        </div>
                                        <div class="text-body-2">
                                            {{ comp.action_rationale }}
                                        </div>
                                    </v-col>
                                    <v-col
                                        cols="auto"
                                        class="pa-4 align-center flex"
                                    >
                                        <v-btn
                                            color="success"
                                            icon="mdi-check"
                                            variant="text"
                                        ></v-btn>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>

                <div v-else class="py-10 text-center">
                    <v-icon size="64" color="grey">mdi-robot-off</v-icon>
                    <p class="mt-4">No hay propuestas disponibles.</p>
                </div>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions class="pa-4 bg-grey-lighten-3">
                <v-btn variant="text" @click="$emit('close')">Cerrar</v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    variant="flat"
                    prepend-icon="mdi-check-all"
                    disabled
                >
                    Aprobar Todo (Beta)
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
interface Props {
    visible: boolean;
    loading?: boolean;
    proposals?: any;
}

const props = defineProps<Props>();
defineEmits(['close', 'approve-role', 'approve-comp']);

const getChangeColor = (type: string) => {
    switch (type.toLowerCase()) {
        case 'new':
            return 'success';
        case 'evolution':
        case 'evolve':
            return 'info';
        case 'replace':
            return 'warning';
        case 'obsolete':
            return 'error';
        default:
            return 'grey';
    }
};
</script>

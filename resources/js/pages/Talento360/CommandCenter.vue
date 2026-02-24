<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface Policy {
    id: number;
    name: string;
    target_type: string;
    target_name: string;
    frequency_months: number;
    owner_name: string;
    is_active: boolean;
    last_run_at: string | null;
}

const policies = ref<Policy[]>([]);
const loading = ref(true);

const fetchPolicies = async () => {
    loading.value = true;
    try {
        // En un entorno real, esto vendría de un endpoint de API
        // Por ahora, simulamos datos basados en el nuevo modelo
        policies.value = [
            {
                id: 1,
                name: 'Evaluación Senior Backend 360',
                target_type: 'role',
                target_name: 'Senior Backend Developer',
                frequency_months: 6,
                owner_name: 'HR Manager',
                is_active: true,
                last_run_at: '2024-01-15',
            },
            {
                id: 2,
                name: 'Assessment de Onboarding (90 días)',
                target_type: 'global',
                target_name: 'Toda la Organización',
                frequency_months: 0,
                owner_name: 'Onboarding Specialist',
                is_active: true,
                last_run_at: '2024-02-20',
            },
        ];
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchPolicies);

const getTargetIcon = (type: string) => {
    switch (type) {
        case 'department':
            return 'mdi-office-building';
        case 'role':
            return 'mdi-account-tie';
        case 'person':
            return 'mdi-account';
        default:
            return 'mdi-earth';
    }
};
</script>

<template>
    <div class="command-center pa-6">
        <Head title="Unidad de Comando 360" />

        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center justify-space-between mb-6">
                    <div>
                        <h1 class="text-h4 font-weight-bold primary--text">
                            Unidad de Comando Cerbero
                        </h1>
                        <p class="text-subtitle-1 text-secondary">
                            Gestión centralizada de políticas y periodicidad de
                            evaluaciones
                        </p>
                    </div>
                    <v-btn color="primary" prepend-icon="mdi-plus" rounded="lg">
                        Nueva Política
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="8">
                <v-card border flat class="overflow-hidden rounded-xl">
                    <v-toolbar flat color="transparent" class="px-4">
                        <v-toolbar-title class="font-weight-bold"
                            >Políticas Activas</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <v-btn icon="mdi-filter-variant" variant="text"></v-btn>
                    </v-toolbar>

                    <v-table>
                        <thead>
                            <tr class="bg-grey-lighten-4">
                                <th class="font-weight-bold text-left">
                                    Nombre de la Política
                                </th>
                                <th class="font-weight-bold text-left">
                                    Objetivo (Target)
                                </th>
                                <th class="font-weight-bold text-left">
                                    Frecuencia
                                </th>
                                <th class="font-weight-bold text-left">
                                    Responsable
                                </th>
                                <th class="font-weight-bold text-left">
                                    Estado
                                </th>
                                <th class="font-weight-bold text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="policy in policies"
                                :key="policy.id"
                                class="policy-row"
                            >
                                <td>
                                    <div class="font-weight-bold">
                                        {{ policy.name }}
                                    </div>
                                    <div class="text-caption text-secondary">
                                        Última ejecución:
                                        {{ policy.last_run_at || 'Nunca' }}
                                    </div>
                                </td>
                                <td>
                                    <v-chip
                                        size="small"
                                        variant="tonal"
                                        color="primary"
                                    >
                                        <v-icon
                                            start
                                            :icon="
                                                getTargetIcon(
                                                    policy.target_type,
                                                )
                                            "
                                            size="14"
                                        ></v-icon>
                                        {{ policy.target_name }}
                                    </v-chip>
                                </td>
                                <td>
                                    <span v-if="policy.frequency_months > 0"
                                        >Cada
                                        {{
                                            policy.frequency_months
                                        }}
                                        meses</span
                                    >
                                    <span v-else class="text-italic"
                                        >Por Evento</span
                                    >
                                </td>
                                <td>{{ policy.owner_name }}</td>
                                <td>
                                    <v-switch
                                        v-model="policy.is_active"
                                        color="success"
                                        hide-details
                                        density="compact"
                                    ></v-switch>
                                </td>
                                <td class="text-right">
                                    <v-btn
                                        icon="mdi-pencil-outline"
                                        variant="text"
                                        size="small"
                                    ></v-btn>
                                    <v-btn
                                        icon="mdi-history"
                                        variant="text"
                                        size="small"
                                        color="secondary"
                                    ></v-btn>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </v-col>

            <v-col cols="12" md="4">
                <v-card
                    border
                    flat
                    class="pa-4 bg-blue-darken-4 mb-6 rounded-xl text-white"
                >
                    <div class="d-flex align-center mb-4">
                        <v-icon color="white" class="mr-2"
                            >mdi-shield-check</v-icon
                        >
                        <div class="text-h6">Criterio de Gobernanza</div>
                    </div>
                    <p class="text-body-2 mb-4 opacity-80">
                        Stratos asegura que cada evaluación tenga un responsable
                        asignado y cumpla con los estándares de privacidad de la
                        organización.
                    </p>
                    <v-list bg-color="transparent" class="pa-0">
                        <v-list-item class="px-0">
                            <template v-slot:prepend>
                                <v-icon color="green-lighten-2"
                                    >mdi-check-circle</v-icon
                                >
                            </template>
                            <v-list-item-title class="text-body-2"
                                >Anonimidad Garantizada</v-list-item-title
                            >
                        </v-list-item>
                        <v-list-item class="px-0">
                            <template v-slot:prepend>
                                <v-icon color="green-lighten-2"
                                    >mdi-check-circle</v-icon
                                >
                            </template>
                            <v-list-item-title class="text-body-2"
                                >Cruce Masivo (N>3)</v-list-item-title
                            >
                        </v-list-item>
                    </v-list>
                </v-card>

                <v-card border flat class="pa-4 rounded-xl">
                    <div class="text-h6 font-weight-bold mb-4">
                        Automatización AI
                    </div>
                    <div class="text-caption grey--text mb-2">
                        Próximos disparos automáticos
                    </div>
                    <v-timeline side="end" density="compact">
                        <v-timeline-item dot-color="info" size="x-small">
                            <div class="text-caption font-weight-bold">
                                Mañana
                            </div>
                            <div class="text-body-2">
                                Onboarding 90d - 12 personas
                            </div>
                        </v-timeline-item>
                        <v-timeline-item dot-color="success" size="x-small">
                            <div class="text-caption font-weight-bold">
                                1 Mar
                            </div>
                            <div class="text-body-2">
                                Ciclo Semestral - Dept. Ventas
                            </div>
                        </v-timeline-item>
                    </v-timeline>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<style scoped>
.policy-row:hover {
    background-color: rgba(var(--v-theme-primary), 0.02);
}
</style>

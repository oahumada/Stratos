<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-2">
                    <v-icon class="mr-2">mdi-chart-bar</v-icon>
                    Stratos Logos — Analytics & Business Intelligence
                </h1>
                <p class="text-subtitle-1 text-medium-emphasis">
                    λόγος — Datos que hablan, decisiones que transforman
                </p>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6" lg="3">
                <v-card variant="tonal" color="primary">
                    <v-card-text class="text-center">
                        <v-icon size="40" class="mb-2"
                            >mdi-account-group</v-icon
                        >
                        <div class="text-h4">
                            {{ summary.core?.total_people ?? '—' }}
                        </div>
                        <div class="text-caption">Personas activas</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-card variant="tonal" color="success">
                    <v-card-text class="text-center">
                        <v-icon size="40" class="mb-2">mdi-school</v-icon>
                        <div class="text-h4">
                            {{ summary.praxis?.completion_rate ?? '—' }}%
                        </div>
                        <div class="text-caption">Tasa de completado LMS</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-card variant="tonal" color="warning">
                    <v-card-text class="text-center">
                        <v-icon size="40" class="mb-2">mdi-forum</v-icon>
                        <div class="text-h4">
                            {{ summary.agora?.avg_health_score ?? '—' }}
                        </div>
                        <div class="text-caption">Health Score Comunidades</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-card variant="tonal" color="info">
                    <v-card-text class="text-center">
                        <v-icon size="40" class="mb-2">mdi-brain</v-icon>
                        <div class="text-h4">
                            {{ summary.stratos_iq?.current_iq ?? '—' }}
                        </div>
                        <div class="text-caption">
                            Stratos IQ
                            <v-chip
                                v-if="summary.stratos_iq?.iq_trend"
                                :color="
                                    summary.stratos_iq.iq_trend > 0
                                        ? 'success'
                                        : 'error'
                                "
                                size="x-small"
                                class="ml-1"
                            >
                                {{ summary.stratos_iq.iq_trend > 0 ? '+' : ''
                                }}{{ summary.stratos_iq.iq_trend }}
                            </v-chip>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row class="mt-4">
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-school-outline</v-icon>
                        Praxis — Formación
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-book-open</v-icon></template
                                >
                                <v-list-item-title
                                    >Cursos publicados</v-list-item-title
                                >
                                <template #append>{{
                                    summary.praxis?.published_courses ?? 0
                                }}</template>
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon
                                        >mdi-account-check</v-icon
                                    ></template
                                >
                                <v-list-item-title
                                    >Enrollments totales</v-list-item-title
                                >
                                <template #append>{{
                                    summary.praxis?.total_enrollments ?? 0
                                }}</template>
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-check-circle</v-icon></template
                                >
                                <v-list-item-title
                                    >Completados</v-list-item-title
                                >
                                <template #append>{{
                                    summary.praxis?.completed_enrollments ?? 0
                                }}</template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-telescope</v-icon>
                        Horizon — Planificación
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <template #prepend
                                    ><v-icon
                                        >mdi-file-document</v-icon
                                    ></template
                                >
                                <v-list-item-title
                                    >Escenarios totales</v-list-item-title
                                >
                                <template #append>{{
                                    summary.horizon?.total_scenarios ?? 0
                                }}</template>
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-play-circle</v-icon></template
                                >
                                <v-list-item-title
                                    >Escenarios activos</v-list-item-title
                                >
                                <template #append>{{
                                    summary.horizon?.active_scenarios ?? 0
                                }}</template>
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon
                                        >mdi-check-decagram</v-icon
                                    ></template
                                >
                                <v-list-item-title
                                    >Escenarios aprobados</v-list-item-title
                                >
                                <template #append>{{
                                    summary.horizon?.approved_scenarios ?? 0
                                }}</template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row class="mt-4">
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-account-group-outline</v-icon>
                        Ágora — Comunidades
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-forum</v-icon></template
                                >
                                <v-list-item-title
                                    >Comunidades activas</v-list-item-title
                                >
                                <template #append>{{
                                    summary.agora?.active_communities ?? 0
                                }}</template>
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon
                                        >mdi-account-multiple</v-icon
                                    ></template
                                >
                                <v-list-item-title
                                    >Miembros totales</v-list-item-title
                                >
                                <template #append>{{
                                    summary.agora?.total_members ?? 0
                                }}</template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-brain</v-icon>
                        Stratos IQ — Inteligencia Organizacional
                    </v-card-title>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-trending-up</v-icon></template
                                >
                                <v-list-item-title
                                    >Proficiency gap promedio</v-list-item-title
                                >
                                <template #append>{{
                                    summary.stratos_iq
                                        ?.average_proficiency_gap ?? '—'
                                }}</template>
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-speedometer</v-icon></template
                                >
                                <v-list-item-title
                                    >Learning velocity</v-list-item-title
                                >
                                <template #append
                                    >{{
                                        summary.stratos_iq?.learning_velocity ??
                                        '—'
                                    }}%</template
                                >
                            </v-list-item>
                            <v-list-item>
                                <template #prepend
                                    ><v-icon>mdi-calendar</v-icon></template
                                >
                                <v-list-item-title
                                    >Último snapshot</v-list-item-title
                                >
                                <template #append>{{
                                    summary.stratos_iq?.snapshot_date ?? '—'
                                }}</template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

const summary = ref<Record<string, any>>({});

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/logos/executive-summary');
        summary.value = data.data;
    } catch {
        // Silent fail — dashboard shows '—' placeholders
    }
});
</script>

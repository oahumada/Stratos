<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-2">
                    <v-icon class="mr-2"
                        >mdi-chart-timeline-variant-shimmer</v-icon
                    >
                    Executive Dashboard
                </h1>
                <p class="text-subtitle-1 text-medium-emphasis">
                    Vista ejecutiva consolidada — Stratos Logos
                </p>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>Tendencia Stratos IQ</v-card-title>
                    <v-card-text>
                        <v-table v-if="trends.length" density="compact">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-right">Stratos IQ</th>
                                    <th class="text-right">Gap promedio</th>
                                    <th class="text-right">
                                        Learning Velocity
                                    </th>
                                    <th class="text-right">Personas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="t in trends" :key="t.date">
                                    <td>{{ t.date }}</td>
                                    <td class="text-right">
                                        {{ t.stratos_iq }}
                                    </td>
                                    <td class="text-right">
                                        {{ t.average_gap }}
                                    </td>
                                    <td class="text-right">
                                        {{ t.learning_velocity }}%
                                    </td>
                                    <td class="text-right">
                                        {{ t.total_people }}
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                        <v-alert v-else type="info" variant="tonal">
                            No hay datos de tendencia disponibles aún.
                        </v-alert>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

const trends = ref<any[]>([]);

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/logos/trends', {
            params: { months: 12 },
        });
        trends.value = data.data;
    } catch {
        // Silent fail
    }
});
</script>

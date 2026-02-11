<template>
    <v-card color="primary" variant="tonal">
        <v-card-text class="text-center">
            <div class="text-overline">Índice de Sintetización</div>
            <div class="text-h2 font-weight-bold">{{ rounded }}%</div>
            <div class="text-caption mt-2">Potencial de apalancamiento IA</div>
            <v-progress-circular
                :model-value="rounded"
                :size="80"
                :width="8"
                color="primary"
                class="mt-4"
            >
                {{ rounded }}%
            </v-progress-circular>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Role } from '../../../../types/talent';

const props = defineProps<{ suggestedRoles: Role[] }>();

const average = computed(() => {
    if (!props.suggestedRoles || props.suggestedRoles.length === 0) return 0;
    const total = props.suggestedRoles.reduce((acc, r) => {
        return acc + (r.talent_composition?.synthetic_percentage || 0);
    }, 0);
    return total / props.suggestedRoles.length;
});

const rounded = computed(() => Math.round(average.value));
</script>

<style scoped>
.text-h2 {
    font-size: 1.5rem;
}
</style>

<template>
    <v-card variant="outlined" class="mb-4">
        <v-card-text>
            <div class="d-flex justify-space-between align-center mb-2">
                <div class="text-h6">{{ role.name }}</div>
                <v-chip
                    :color="strategyColor"
                    :class="['text-uppercase font-weight-bold', strategyClass]"
                    size="small"
                    label
                >
                    {{ role.talent_composition.strategy_suggestion }}
                </v-chip>
            </div>

            <div class="text-caption text-medium-emphasis mb-4">
                FTE: {{ role.estimated_fte }} | Agente:
                {{ role.suggested_agent_type || 'N/A' }}
            </div>

            <!-- Barra de Composición -->
            <div class="mb-2">
                <div class="d-flex justify-space-between text-caption mb-1">
                    <span>
                        <v-icon size="14" color="primary">mdi-account</v-icon>
                        Humano ({{ role.talent_composition.human_percentage }}%)
                    </span>
                    <span>
                        IA ({{ role.talent_composition.synthetic_percentage }}%)
                        <v-icon size="14" color="secondary">mdi-robot</v-icon>
                    </span>
                </div>
                <v-progress-linear
                    height="12"
                    rounded
                    :model-value="role.talent_composition.human_percentage"
                    color="primary"
                    background-color="secondary"
                    background-opacity="1"
                />
            </div>

            <v-alert
                v-if="role.talent_composition.logic_justification"
                density="compact"
                type="info"
                variant="tonal"
                class="text-caption mt-3"
            >
                {{ role.talent_composition.logic_justification }}
            </v-alert>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Role } from '../../../../types/talent';

const props = defineProps<{ role: Role }>();

const strategyColor = computed(() => {
    const s = props.role.talent_composition.strategy_suggestion || '';
    const colors: Record<string, string> = {
        Buy: 'success',
        Build: 'info',
        Borrow: 'warning',
        Synthetic: 'secondary',
        Hybrid: 'primary',
    };
    return colors[s] || 'grey';
});

const strategyClass = computed(() => `strategy-${strategyColor.value}`);
</script>

<style scoped>
.text-caption {
    font-size: 0.8rem;
    color: rgba(0, 0, 0, 0.7);
}
</style>

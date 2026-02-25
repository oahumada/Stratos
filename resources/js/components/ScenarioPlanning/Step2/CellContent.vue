<template>
    <div
        v-if="mapping"
        class="cell-content flex h-full flex-col items-center justify-center gap-1 px-2 py-2"
    >
        <!-- Source badge: agent vs manual -->
        <div class="source-badge">
            <v-tooltip :text="sourceTooltip" location="top">
                <template #activator="{ props: tooltipProps }">
                    <span
                        v-bind="tooltipProps"
                        class="source-icon"
                        :class="sourceClass"
                    >
                        {{ sourceIcon }}
                    </span>
                </template>
            </v-tooltip>
        </div>

        <!-- State Badge -->
        <div
            class="rounded px-2 py-1 text-xs font-semibold text-white"
            :class="stateColor"
        >
            {{ stateIcon }} {{ stateText }}
        </div>

        <!-- Level Info -->
        <div class="text-center text-xs text-gray-600">
            <div
                v-if="mapping.change_type === 'transformation'"
                class="font-mono"
            >
                Lvl {{ mapping.current_level || '?' }} →
                {{ mapping.required_level }}
            </div>
            <div v-else class="font-mono">Lvl {{ mapping.required_level }}</div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-1 flex gap-1">
            <v-btn
                icon="mdi-pencil"
                size="x-small"
                variant="text"
                density="compact"
                @click.stop="$emit('edit')"
            />
            <v-btn
                icon="mdi-delete"
                size="x-small"
                variant="text"
                color="red"
                density="compact"
                @click.stop="$emit('remove')"
            />
        </div>
    </div>

    <div
        v-else
        class="cell-empty flex h-full cursor-pointer items-center justify-center rounded border-2 border-dashed border-gray-300 transition-colors hover:border-blue-300 hover:bg-blue-50"
        @click.stop="$emit('edit')"
    >
        <div class="text-center">
            <v-icon
                icon="mdi-plus-circle"
                class="mb-1 text-gray-400 hover:text-blue-500"
            />
            <div class="text-xs text-gray-500">Click para asignar</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    mapping?: {
        change_type:
            | 'maintenance'
            | 'transformation'
            | 'enrichment'
            | 'extinction';
        required_level: number;
        current_level?: number;
        source?: 'agent' | 'manual' | 'auto';
    };
}

interface Emits {
    (e: 'edit'): void;
    (e: 'remove'): void;
}

defineEmits<Emits>();
const props = defineProps<Props>();

// ─── Source (agent / manual / auto) ──────────────────────────────────────────

const sourceIcon = computed(() => {
    if (!props.mapping) return '';
    switch (props.mapping.source) {
        case 'agent':
            return '🤖';
        case 'auto':
            return '⚙️';
        default:
            return '👤';
    }
});

const sourceClass = computed(() => {
    if (!props.mapping) return '';
    return props.mapping.source === 'agent' ? 'source-agent' : 'source-manual';
});

const sourceTooltip = computed(() => {
    if (!props.mapping) return '';
    switch (props.mapping.source) {
        case 'agent':
            return 'Propuesto por el agente IA';
        case 'auto':
            return 'Derivado automáticamente';
        default:
            return 'Asignado manualmente';
    }
});

// ─── Change type display ──────────────────────────────────────────────────────

const stateIcon = computed(() => {
    if (!props.mapping) return '';
    const icons: Record<string, string> = {
        maintenance: '✅',
        transformation: '🔄',
        enrichment: '📈',
        extinction: '📉',
    };
    return icons[props.mapping.change_type] || '';
});

const stateText = computed(() => {
    if (!props.mapping) return '';
    const texts: Record<string, string> = {
        maintenance: 'Mant.',
        transformation: 'Transf.',
        enrichment: 'Enriq.',
        extinction: 'Extinc.',
    };
    return texts[props.mapping.change_type] || '';
});

const stateColor = computed(() => {
    if (!props.mapping) return 'bg-gray-300';
    const colors: Record<string, string> = {
        maintenance: 'bg-green-500',
        transformation: 'bg-blue-500',
        enrichment: 'bg-green-600',
        extinction: 'bg-red-500',
    };
    return colors[props.mapping.change_type] || 'bg-gray-500';
});
</script>

<style scoped>
.cell-content {
    width: 100%;
    height: 100%;
}

.cell-empty {
    width: 100%;
    opacity: 0;
    transition: opacity 0.2s;
}

.cell-empty:hover {
    opacity: 1;
}

/* Source badge */
.source-badge {
    position: absolute;
    top: 3px;
    right: 4px;
    font-size: 11px;
    line-height: 1;
}

.source-icon {
    display: inline-block;
    cursor: default;
    filter: drop-shadow(0 0 1px rgba(0, 0, 0, 0.15));
}

.source-agent {
    /* subtle glow para distinguir propuestas de agente */
    filter: drop-shadow(0 0 2px rgba(99, 102, 241, 0.6));
}
</style>

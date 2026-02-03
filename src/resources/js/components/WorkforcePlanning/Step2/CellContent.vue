<template>
  <div
    v-if="mapping"
    class="cell-content flex flex-col items-center justify-center h-full gap-1 px-2 py-2"
  >
    <!-- State Badge -->
    <div
      class="px-2 py-1 rounded text-xs font-semibold text-white"
      :class="stateColor"
    >
      {{ stateIcon }} {{ stateText }}
    </div>

    <!-- Level Info -->
    <div class="text-xs text-center text-gray-600">
      <div v-if="mapping.change_type === 'transformation'" class="font-mono">
        Lvl {{ mapping.current_level || '?' }} → {{ mapping.required_level }}
      </div>
      <div v-else class="font-mono">
        Lvl {{ mapping.required_level }}
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-1 mt-1">
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

  <div v-else class="cell-empty h-full flex items-center justify-center">
    <v-btn
      icon="mdi-plus-circle"
      size="x-small"
      variant="text"
      color="gray"
      @click.stop="$emit('edit')"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  mapping?: {
    change_type: 'maintenance' | 'transformation' | 'enrichment' | 'extinction';
    required_level: number;
    current_level?: number;
  };
}

interface Emits {
  (e: 'edit'): void;
  (e: 'remove'): void;
}

defineProps<Props>();
defineEmits<Emits>();

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

const props = defineProps<Props>();
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
</style>

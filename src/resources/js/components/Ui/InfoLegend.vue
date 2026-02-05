<template>
  <v-dialog :model-value="modelValue" max-width="{width}" @update:model-value="(val) => $emit('update:modelValue', val)">
    <v-card class="rounded-lg shadow-sm" :style="`border-left:4px solid ${stripeColor}`">
      <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b">
        <div class="flex items-center gap-3">
          <v-icon :class="iconClass">{{ icon }}</v-icon>
          <div class="text-lg font-semibold">{{ title }}</div>
        </div>
        <v-btn icon variant="text" aria-label="Cerrar" @click="$emit('update:modelValue', false)">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>

      <v-card-text class="p-4 bg-white" style="font-size:16px; line-height:1.4;">
        <div v-for="(it, idx) in items" :key="idx" class="mb-3">
          <p class="mb-1"><strong>{{ it.title }}</strong>: {{ it.text }}</p>
        </div>
      </v-card-text>

      <div class="px-4 pb-3 flex justify-end">
        <v-btn variant="text" @click="$emit('update:modelValue', false)">Cerrar</v-btn>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  title: { type: String, default: 'Información' },
  items: { type: Array as () => Array<{ title: string; text: string }>, default: () => [] },
  icon: { type: String, default: 'mdi-information-variant-circle' },
  width: { type: [String, Number], default: 480 },
  stripeColor: { type: String, default: 'rgb(var(--v-theme-info))' },
});

const emits = defineEmits(['update:modelValue']);

const iconClass = computed(() => 'text-2xl text-info-darken-1');
</script>

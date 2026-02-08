<template>
  <v-dialog v-model="visible" max-width="600">
    <v-card>
      <v-card-title>{{ title }}</v-card-title>
      <v-card-text>
        <div v-if="messages && messages.length">
          <ul>
            <li v-for="(m, i) in messages" :key="i">{{ m }}</li>
          </ul>
        </div>
        <div v-else>
          <p>No hay detalles adicionales.</p>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text color="primary" @click="close">Cerrar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { PropType } from 'vue';

const props = defineProps({
  modelValue: { type: Boolean as PropType<boolean>, default: false },
  title: { type: String as PropType<string>, default: 'Errores' },
  messages: { type: Array as PropType<string[]>, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const visible = computed({
  get: () => props.modelValue,
  set: (v: boolean) => emit('update:modelValue', v),
});

function close() {
  visible.value = false;
}
</script>

<style scoped>
ul { margin: 0; padding-left: 1rem; }
</style>
